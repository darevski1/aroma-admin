<?php

namespace App\Services;

use Dom\Element;
use Dom\HTMLDocument;

/**
 * Inline (WYSIWYG) text edits for Blade-rendered pages.
 *
 * The visual editor never rewrites the template. Each edited text element is
 * stored as {k: original text, n: nth occurrence of that text, html: new inner
 * HTML} in the page's settings and re-applied to the rendered <main> content at
 * request time. The "qualifying element" rules below MUST stay identical to the
 * ones in resources/js/page-editor.js, because both sides derive the same keys.
 */
class InlineEdits
{
    /** Elements whose text can be edited in place. */
    public const TARGETS = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'li', 'td', 'th', 'blockquote', 'figcaption', 'div', 'a', 'span', 'strong', 'em', 'button', 'dt', 'dd', 'label', 'summary'];

    /** A target holding one of these is a layout wrapper, not a text element. */
    public const BLOCKS = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'li', 'td', 'th', 'blockquote', 'figcaption', 'div', 'section', 'article', 'ul', 'ol', 'table', 'tr', 'dl', 'dt', 'dd', 'form', 'header', 'footer', 'nav', 'main', 'aside', 'figure'];

    /** Content that cannot survive an innerHTML round trip. */
    public const OPAQUE = ['svg', 'img', 'picture', 'video', 'iframe', 'input', 'select', 'textarea', 'canvas', 'script', 'style'];

    /** Containers that are never editable. */
    public const SKIP_ANCESTORS = ['header', 'nav', 'footer', 'script', 'style', 'svg', 'form', 'select', 'textarea'];

    /** Tags (and attributes) kept when a user's edited HTML is stored. */
    private const ALLOWED_TAGS = ['br', 'strong', 'b', 'em', 'i', 'u', 'span', 'a'];
    private const ALLOWED_ATTRS = ['href', 'class', 'target', 'rel', 'title'];

    public static function normalize(string $text): string
    {
        return trim((string) preg_replace('/[\s\x{00A0}]+/u', ' ', $text));
    }

    /**
     * Apply a page's stored edits to rendered content. $mark adds data-ie-key
     * to edited elements so the editor can keep addressing the original text.
     */
    public function apply(string $html, ?array $edits, bool $mark = false): string
    {
        if (empty($edits) || trim($html) === '') {
            return $html;
        }

        $lookup = [];
        foreach ($edits as $edit) {
            if (isset($edit['k'], $edit['n'], $edit['html'])) {
                $lookup[$edit['k'] . "\0" . (int) $edit['n']] = $edit['html'];
            }
        }
        if (!$lookup) {
            return $html;
        }

        $doc = HTMLDocument::createFromString('<!DOCTYPE html><html><body><div id="ie-root">' . $html . '</div></body></html>', LIBXML_NOERROR);
        $root = $doc->getElementById('ie-root');
        if (!$root) {
            return $html;
        }

        $seen = [];
        $changed = false;
        $imgJobs = [];
        $imgSeen = [];
        foreach ($root->querySelectorAll('img') as $img) {
            if ($this->insideSkipped($img, $root)) {
                continue;
            }
            $src = preg_replace('#^https?://[^/]+#i', '', (string) $img->getAttribute('src'));
            $n = $imgSeen[$src] = ($imgSeen[$src] ?? -1) + 1;
            $key = 'img:' . $src . "\0" . $n;
            if (isset($lookup[$key])) {
                $imgJobs[] = [$img, $lookup[$key], $key];
            }
        }
        // Keys are read from the untouched document first, replaced after.
        $jobs = [];
        foreach ($this->qualifying($root) as $el) {
            $text = self::normalize($el->textContent);
            $n = $seen[$text] = ($seen[$text] ?? -1) + 1;
            if (isset($lookup[$text . "\0" . $n])) {
                $jobs[] = [$el, $lookup[$text . "\0" . $n], $text . "\0" . $n];
            }
        }
        foreach ($imgJobs as [$img, $json, $key]) {
            $d = json_decode($json, true);
            if (!is_array($d) || empty($d['src'])) {
                continue;
            }
            $img->setAttribute('src', $d['src']);
            foreach (['srcset', 'sizes', 'data-src', 'data-srcset'] as $a) {
                $img->removeAttribute($a);
            }
            if ($img->parentNode instanceof Element && strtolower($img->parentNode->localName) === 'picture') {
                foreach (iterator_to_array($img->parentNode->querySelectorAll('source')) as $source) {
                    $source->remove();
                }
            }
            $img->setAttribute('alt', (string) ($d['alt'] ?? ''));
            if (!empty($d['title'])) {
                $img->setAttribute('title', $d['title']);
            } else {
                $img->removeAttribute('title');
            }
            if ($mark) {
                $img->setAttribute('data-ie-key', base64_encode($key));
            }
            $changed = true;
        }
        foreach ($jobs as [$el, $new, $key]) {
            $el->innerHTML = $new;
            if ($mark) {
                $el->setAttribute('data-ie-key', base64_encode($key));
            }
            $changed = true;
        }

        return $changed ? $root->innerHTML : $html;
    }

    private function insideSkipped(Element $el, Element $root): bool
    {
        for ($p = $el->parentNode; $p instanceof Element && $p !== $root; $p = $p->parentNode) {
            if (in_array(strtolower($p->localName), self::SKIP_ANCESTORS, true)) {
                return true;
            }
        }

        return false;
    }

    /** Elements that can be edited, in document order. */
    private function qualifying(Element $root): array
    {
        $out = [];
        $this->walk($root, false, $out);

        return $out;
    }

    private function walk(Element $node, bool $insideEditable, array &$out): void
    {
        foreach ($node->children as $child) {
            $tag = strtolower($child->localName);
            if (in_array($tag, self::SKIP_ANCESTORS, true)) {
                continue;
            }
            $take = !$insideEditable && $this->isEditable($child, $tag);
            if ($take) {
                $out[] = $child;
                continue; // everything inside belongs to this element
            }
            $this->walk($child, $insideEditable, $out);
        }
    }

    private function isEditable(Element $el, string $tag): bool
    {
        if (!in_array($tag, self::TARGETS, true)) {
            return false;
        }
        if (mb_strlen(self::normalize($el->textContent)) < 2) {
            return false;
        }
        foreach ($el->querySelectorAll('*') as $d) {
            $t = strtolower($d->localName);
            if (in_array($t, self::BLOCKS, true) || in_array($t, self::OPAQUE, true)) {
                return false;
            }
        }

        return true;
    }

    /** Reduce user-supplied inner HTML to a safe set of inline markup. */
    public function sanitize(string $html): string
    {
        $doc = HTMLDocument::createFromString('<!DOCTYPE html><html><body><div id="ie-root">' . $html . '</div></body></html>', LIBXML_NOERROR);
        $root = $doc->getElementById('ie-root');
        if (!$root) {
            return '';
        }
        $this->clean($root);

        return $root->innerHTML;
    }

    private function clean(Element $node): void
    {
        foreach (iterator_to_array($node->children) as $child) {
            $tag = strtolower($child->localName);
            $this->clean($child);
            if (!in_array($tag, self::ALLOWED_TAGS, true)) {
                // Unwrap: keep the text, drop the element (scripts/styles lose their text too).
                if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed'], true)) {
                    $child->remove();
                    continue;
                }
                while ($child->firstChild) {
                    $child->parentNode->insertBefore($child->firstChild, $child);
                }
                $child->remove();
                continue;
            }
            foreach (iterator_to_array($child->attributes) as $attr) {
                $name = strtolower($attr->name);
                $val = trim((string) $attr->value);
                if (!in_array($name, self::ALLOWED_ATTRS, true)
                    || ($name === 'href' && preg_match('/^\s*(javascript|data|vbscript):/i', $val))) {
                    $child->removeAttribute($attr->name);
                }
            }
        }
    }

    /** Merge new edits into the stored list (same key+occurrence is replaced). */
    public function merge(array $stored, array $incoming): array
    {
        $map = [];
        foreach ($stored as $e) {
            $map[$e['k'] . "\0" . $e['n']] = $e;
        }
        foreach ($incoming as $e) {
            $rawK = (string) ($e['k'] ?? '');
            if (str_starts_with($rawK, 'img:')) {
                $d = json_decode((string) ($e['html'] ?? ''), true);
                $src = is_array($d) ? trim((string) ($d['src'] ?? '')) : '';
                if ($src === '' || preg_match('/^\s*(javascript|data|vbscript):/i', $src)) {
                    continue;
                }
                $map[$rawK . "\0" . (int) ($e['n'] ?? 0)] = [
                    'k' => $rawK,
                    'n' => (int) ($e['n'] ?? 0),
                    'html' => json_encode(['src' => $src, 'alt' => (string) ($d['alt'] ?? ''), 'title' => (string) ($d['title'] ?? '')], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ];
                continue;
            }
            $k = self::normalize($rawK);
            if ($k === '') {
                continue;
            }
            $map[$k . "\0" . (int) ($e['n'] ?? 0)] = [
                'k' => $k,
                'n' => (int) ($e['n'] ?? 0),
                'html' => $this->sanitize((string) ($e['html'] ?? '')),
            ];
        }

        return array_values($map);
    }
}
