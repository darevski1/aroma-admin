<?php

namespace App\Services\SiteImages;

use App\Models\SiteImageOverride;
use App\Models\SiteImageSlot;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Finds every image a rendered public page shows (<img src>, inline background-image,
 * <style> url()) and re-points it at an admin-chosen Media Library file. Templates are
 * never touched: the original src is the stable key, replacements are applied to the
 * final HTML, so removing an override restores the original image exactly.
 */
class SiteImages
{
    private const IMG_EXT = '~\.(?:jpe?g|png|webp|gif|svg|avif)$~i';
    private const TAG = '~<([a-z][a-z0-9-]*)\b((?:[^>"\']|"[^"]*"|\'[^\']*\')*)>~i';
    private const SCRIPT = '~<script\b.*?</script>~is';

    // ── Keys ────────────────────────────────────────────────────

    /** Stable key for an image URL; null when it is not a plain image URL (data: URIs, gradients...). */
    public static function normalize(?string $src): ?string
    {
        $src = trim(html_entity_decode((string) $src, ENT_QUOTES));
        if ($src === '' || str_starts_with($src, 'data:') || str_starts_with($src, 'blob:')) {
            return null;
        }
        $external = false;
        if (preg_match('~^(?:https?:)?//([^/]+)(/.*)?$~i', $src, $m)) {
            $own = array_filter([parse_url((string) config('app.url'), PHP_URL_HOST), request()->getHost()]);
            if (in_array(strtolower($m[1]), array_map('strtolower', $own), true)) {
                $src = $m[2] ?? '/';
            } else {
                $external = true;
            }
        }
        $src = preg_replace('~[?#].*$~', '', $src);
        $src = rawurldecode($src);
        if ($src === '' || !preg_match(self::IMG_EXT, $src)) {
            return null;
        }

        if ($external) {
            return $src;
        }
        // "./../assets/x.png" and "assets/x.png" are the same file as "/assets/x.png" for our purposes.
        $parts = [];
        foreach (explode('/', $src) as $seg) {
            if ($seg === '' || $seg === '.') {
                continue;
            }
            if ($seg === '..') {
                array_pop($parts);
                continue;
            }
            $parts[] = $seg;
        }

        return '/' . implode('/', $parts);
    }

    public static function pathOf(\Illuminate\Http\Request $request): string
    {
        $p = trim($request->path(), '/');

        return $p === '' ? '/' : '/' . $p;
    }

    // ── Scan ────────────────────────────────────────────────────

    /** @return array<int,array{kind:string,key:string,alt:?string,context:?string}> unique per key */
    public function scan(string $html): array
    {
        [$work] = $this->stripScripts($html);
        $found = [];

        preg_match_all(self::TAG, $work, $tags, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        foreach ($tags as $t) {
            $name = strtolower($t[1][0]);
            $attrs = $t[2][0];
            $offset = $t[0][1];
            if ($name === 'img' && preg_match('~\ssrc\s*=\s*(["\'])(.*?)\1~is', $attrs, $m)) {
                if ($key = self::normalize($m[2])) {
                    $alt = preg_match('~\salt\s*=\s*(["\'])(.*?)\1~is', $attrs, $a) ? html_entity_decode($a[2]) : null;
                    $found[$key] ??= ['kind' => 'img', 'key' => $key, 'alt' => $alt, 'offset' => $offset];
                }
            }
            if (preg_match('~\sstyle\s*=\s*(["\'])(.*?)\1~is', $attrs, $m) && stripos($m[2], 'url(') !== false) {
                foreach ($this->cssUrls($m[2]) as $u) {
                    if ($key = self::normalize($u)) {
                        $found[$key] ??= ['kind' => 'bg', 'key' => $key, 'alt' => null, 'offset' => $offset];
                    }
                }
            }
        }
        if (preg_match_all('~<style\b[^>]*>(.*?)</style>~is', $work, $styles, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            foreach ($styles as $s) {
                foreach ($this->cssUrls($s[1][0]) as $u) {
                    if ($key = self::normalize($u)) {
                        $found[$key] ??= ['kind' => 'css', 'key' => $key, 'alt' => null, 'offset' => $s[0][1]];
                    }
                }
            }
        }

        foreach ((array) config('site_images.css_backgrounds', []) as $bg) {
            $pos = $this->cssClassOffset($work, $bg['class']);
            if ($pos !== null && ($key = self::normalize($bg['src']))) {
                $found[$key] ??= ['kind' => 'css', 'key' => $key, 'alt' => $bg['label'] ?? null, 'offset' => $pos];
            }
        }

        foreach ($found as &$f) {
            $f['context'] = $this->headingBefore($work, $f['offset']);
            unset($f['offset']);
        }

        return array_values($found);
    }

    /** Offset of the first element using the CSS class, or null. */
    private function cssClassOffset(string $html, string $class): ?int
    {
        $re = '~\sclass\s*=\s*(["\'])(?:(?!\1).)*?(?<![\w-])' . preg_quote($class, '~') . '(?![\w-])~is';

        return preg_match($re, $html, $m, PREG_OFFSET_CAPTURE) ? $m[0][1] : null;
    }

    private function headingBefore(string $work, int $offset): ?string
    {
        $start = max(0, $offset - 8000);
        $chunk = substr($work, $start, $offset - $start);
        if (preg_match_all('~<h([1-4])\b[^>]*>(.*?)</h\1>~is', $chunk, $m) && ($last = end($m[2]))) {
            $text = trim(preg_replace('/\s+/u', ' ', strip_tags($last)));

            return $text !== '' ? mb_substr($text, 0, 120) : null;
        }

        return null;
    }

    private function cssUrls(string $css): array
    {
        preg_match_all('~url\(\s*(["\']?)(.*?)\1\s*\)~is', $css, $m);

        return $m[2];
    }

    private function stripScripts(string $html): array
    {
        $scripts = [];
        $work = preg_replace_callback(self::SCRIPT, function ($m) use (&$scripts) {
            $scripts[] = $m[0];

            return '<!--SI' . (count($scripts) - 1) . '-->';
        }, $html);

        return [$work ?? $html, $scripts];
    }

    // ── Apply ───────────────────────────────────────────────────

    /** @param array<string,array> $overrides key => prepared override (see overridesFor) */
    public function apply(string $html, array $overrides): string
    {
        if (!$overrides) {
            return $html;
        }
        [$work, $scripts] = $this->stripScripts($html);
        $mobileCss = [];
        $counter = 0;

        $work = preg_replace_callback(self::TAG, function ($m) use ($overrides, &$mobileCss, &$counter) {
            $tag = $m[0];
            $name = strtolower($m[1]);
            $attrs = $m[2];

            if ($name === 'img' && preg_match('~\ssrc\s*=\s*(["\'])(.*?)\1~is', $attrs, $s)) {
                $key = self::normalize($s[2]);
                if ($key && isset($overrides[$key])) {
                    return $this->applyImg($attrs, $overrides[$key]);
                }
            }
            if (preg_match('~\sstyle\s*=\s*(["\'])(.*?)\1~is', $attrs, $s) && stripos($s[2], 'url(') !== false) {
                $changed = false;
                $mobile = null;
                $ovr = null;
                $style = $this->replaceCssUrls($s[2], $overrides, $changed, $mobile, $ovr);
                if ($changed) {
                    $q = $s[1];
                    $newAttrs = str_replace($s[0], ' style=' . $q . $this->appendBgSettings($style, $ovr) . $q, $attrs);
                    if ($mobile) {
                        $id = 'si' . (++$counter);
                        $mobileCss[] = '@media (max-width: 767px){[data-si="' . $id . '"]{background-image:url("' . $mobile . '") !important}}';
                        $newAttrs .= ' data-si="' . $id . '"';
                    }

                    return '<' . $m[1] . $newAttrs . '>';
                }
            }

            return $tag;
        }, $work);

        $work = preg_replace_callback('~(<style\b[^>]*>)(.*?)(</style>)~is', function ($m) use ($overrides) {
            $changed = false;
            $mobile = null;
            $ovr = null;

            return $m[1] . $this->replaceCssUrls($m[2], $overrides, $changed, $mobile, $ovr) . $m[3];
        }, $work);

        // Backgrounds defined in compiled CSS: override the class from a <style> block.
        foreach ((array) config('site_images.css_backgrounds', []) as $bg) {
            $key = self::normalize($bg['src']);
            $o = $key ? ($overrides[$key] ?? null) : null;
            if (!$o || $this->cssClassOffset($work, $bg['class']) === null) {
                continue;
            }
            $sel = '.' . $bg['class'];
            if ($o['hidden']) {
                $mobileCss[] = $sel . '{background-image:none !important}';
                continue;
            }
            if (!$o['src']) {
                continue;
            }
            $mobileCss[] = $sel . "{background-image:url('" . $o['src'] . "') !important"
                . ($o['object_position'] ? ';background-position:' . $o['object_position'] . ' !important' : '')
                . ($o['object_fit'] ? ';background-size:' . $o['object_fit'] . ' !important' : '') . '}';
            if ($o['mobile_src']) {
                $mobileCss[] = '@media (max-width: 767px){' . $sel . "{background-image:url('" . $o['mobile_src'] . "') !important}}";
            }
        }

        if ($mobileCss) {
            $style = '<style>' . implode('', $mobileCss) . '</style>';
            $work = stripos($work, '</body>') !== false ? preg_replace('~</body>~i', $style . '</body>', $work, 1) : $work . $style;
        }

        return preg_replace_callback('~<!--SI(\d+)-->~', fn ($m) => $scripts[(int) $m[1]], $work);
    }

    private function replaceCssUrls(string $css, array $overrides, bool &$changed, ?string &$mobile, ?array &$last): string
    {
        return preg_replace_callback('~url\(\s*(["\']?)(.*?)\1\s*\)~is', function ($m) use ($overrides, &$changed, &$mobile, &$last) {
            $key = self::normalize($m[2]);
            if (!$key || !isset($overrides[$key])) {
                return $m[0];
            }
            $o = $overrides[$key];
            if (!$o['hidden'] && !$o['src']) {
                return $m[0];
            }
            $changed = true;
            $last = $o;
            if ($o['hidden']) {
                return 'none';
            }
            $mobile = $o['mobile_src'] ?? null;

            return "url('" . $o['src'] . "')";
        }, $css) ?? $css;
    }

    private function appendBgSettings(string $style, ?array $o): string
    {
        if (!$o || $o['hidden']) {
            return $style;
        }
        $style = rtrim($style, "; \t\n") . ';';
        if ($o['object_position']) {
            $style .= 'background-position:' . $o['object_position'] . ';';
        }
        if ($o['object_fit']) {
            $style .= 'background-size:' . $o['object_fit'] . ';';
        }

        return $style;
    }

    private function applyImg(string $attrs, array $o): string
    {
        $new = $attrs;
        $set = function (string $name, ?string $value) use (&$new) {
            $new = preg_replace('~\s' . preg_quote($name, '~') . '\s*=\s*(["\']).*?\1~is', '', $new);
            if ($value !== null) {
                $new .= ' ' . $name . '="' . e($value) . '"';
            }
        };
        $style = fn (string $from) => preg_match('~\sstyle\s*=\s*(["\'])(.*?)\1~is', $from, $s) ? rtrim($s[2], "; \t\n") : '';

        if ($o['hidden']) {
            $set('style', ltrim($style($new) . ';display:none', ';'));

            return '<img' . $new . ' hidden>';
        }

        if ($o['src']) {
            $sizes = preg_match('~\ssizes\s*=\s*(["\'])(.*?)\1~is', $attrs, $z) ? $z[2] : '100vw';
            foreach (['srcset', 'sizes', 'data-src', 'data-srcset'] as $a) {
                $set($a, null);
            }
            $set('src', $o['src']);
            if (!empty($o['srcset']) && empty($o['mobile_src'])) {
                $set('srcset', $o['srcset']);
                $set('sizes', $sizes);
            }
        }
        if ($o['alt'] !== null) {
            $set('alt', $o['alt']);
        }
        if ($o['title'] !== null && $o['title'] !== '') {
            $set('title', $o['title']);
        }
        if ($o['object_position'] || $o['object_fit']) {
            $css = $style($new);
            $css = ($css !== '' ? $css . ';' : '')
                . ($o['object_fit'] ? 'object-fit:' . $o['object_fit'] . ';' : '')
                . ($o['object_position'] ? 'object-position:' . $o['object_position'] . ';' : '');
            $set('style', $css);
        }

        $img = '<img' . $new . '>';
        if (!empty($o['mobile_src'])) {
            $img = '<picture style="display:contents"><source media="(max-width: 767px)" srcset="' . e($o['mobile_src']) . '">' . $img . '</picture>';
        }

        return $img;
    }

    // ── Overrides lookup ────────────────────────────────────────

    public static function version(): int
    {
        return (int) Cache::get('site_images.version', 1);
    }

    public static function bump(): void
    {
        Cache::forever('site_images.version', self::version() + 1);
    }

    /** Overrides that apply on $path: global ones first, page-specific ones win. */
    public function overridesFor(string $path): array
    {
        return Cache::remember('site_images.ovr.' . self::version() . '.' . md5($path), 3600, function () use ($path) {
            $rows = SiteImageOverride::whereIn('scope_path', [SiteImageOverride::GLOBAL, $path])->get()
                ->sortBy(fn ($r) => $r->isGlobal() ? 0 : 1);
            $ids = $rows->pluck('media_id')->merge($rows->pluck('mobile_media_id'))->filter()->unique();
            $media = $ids->isEmpty() ? collect() : Media::whereIn('id', $ids)->get()->keyBy('id');

            $out = [];
            foreach ($rows as $row) {
                $out[$row->src_key] = $this->prepare($row, $media[$row->media_id] ?? null, $media[$row->mobile_media_id] ?? null);
            }

            return $out;
        });
    }

    public function prepare(SiteImageOverride $row, ?Media $media, ?Media $mobile): array
    {
        return [
            'src' => $media ? MediaOptimizer::publicUrl($media) : null,
            'srcset' => $media ? MediaOptimizer::srcset($media) : null,
            'mobile_src' => $mobile ? MediaOptimizer::publicUrl($mobile) : null,
            'alt' => $row->alt,
            'title' => $row->title,
            'object_position' => $row->object_position,
            'object_fit' => $row->object_fit,
            'hidden' => (bool) $row->hidden,
        ];
    }

    // ── Registry ────────────────────────────────────────────────

    /** Remember which images a page shows (cheap: only writes when the set of images changed). */
    public function record(string $path, string $html, bool $prune = false): array
    {
        $found = $this->scan($html);
        $sig = md5(json_encode(array_column($found, 'key')));
        $cacheKey = 'site_images.sig.' . md5($path);
        if (!$prune && Cache::get($cacheKey) === $sig) {
            return $found;
        }

        $title = preg_match('~<title>(.*?)</title>~is', $html, $t) ? mb_substr(trim(html_entity_decode($t[1])), 0, 255) : null;
        foreach ($found as $f) {
            SiteImageSlot::updateOrCreate(
                ['path' => $path, 'src_key' => $f['key']],
                ['kind' => $f['kind'], 'alt' => $f['alt'] !== null ? mb_substr($f['alt'], 0, 500) : null, 'context' => $f['context'], 'page_title' => $title, 'last_seen_at' => now()]
            );
        }
        if ($prune) {
            SiteImageSlot::where('path', $path)->whereNotIn('src_key', array_column($found, 'key') ?: [''])->delete();
        }
        Cache::forever($cacheKey, $sig);

        return $found;
    }
}
