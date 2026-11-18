<?php

namespace App\Services\HeroSlider;

use App\Models\HeroSlider;
use Illuminate\Support\Collection;

/**
 * Turns hero sliders into HTML, both for the <x-hero-slider> Blade component
 * and for [YYYY-N] shortcodes written in page content.
 *
 * Registered as a scoped singleton so the slider CSS/JS tags are emitted only
 * once per response, however many sliders the page holds.
 */
class HeroSliderRenderer
{
    /** Matches a shortcode such as [2026-1]. */
    public const PATTERN = '/\[(\d{4}-\d{1,9})\]/';

    /** Text inside these elements is never treated as a shortcode. */
    private const RAW_TAGS = ['script', 'style', 'textarea', 'title', 'code', 'pre', 'noscript', 'template'];

    /** Text wrappers removed when the shortcode is their only content. */
    private const UNWRAP_TAGS = 'p|span|strong|em|b|i|h[1-6]';

    private bool $assetsEmitted = false;

    private int $instance = 0;

    /** Start a fresh page: assets not yet emitted. */
    public function reset(): void
    {
        $this->assetsEmitted = false;
        $this->instance = 0;
    }

    public static function normalizeCode(string $code): string
    {
        return trim($code, " \t\n\r\0\x0B[]");
    }

    /** HTML for a slider by its shortcode (with or without brackets); '' when hidden. */
    public function renderCode(string $code): string
    {
        $slider = HeroSlider::where('shortcode', self::normalizeCode($code))
            ->with('activeSlides')
            ->first();

        return $slider ? $this->render($slider) : '';
    }

    /** HTML for one slider; '' when the slider is disabled or has no enabled slides. */
    public function render(HeroSlider $slider, bool $withAssets = true): string
    {
        if (! $slider->is_active) {
            return '';
        }

        $slides = $slider->relationLoaded('activeSlides')
            ? $slider->activeSlides
            : $slider->activeSlides()->get();
        $slides = $slides->filter(fn ($s) => $s->desktop_image)->values();

        if ($slides->isEmpty()) {
            return '';
        }

        $html = view('hero-slider.slider', [
            'slider' => $slider,
            'slides' => $slides,
            'domId' => 'hs-' . $slider->shortcode . '-' . (++$this->instance),
        ])->render();

        if ($withAssets && ! $this->assetsEmitted) {
            $this->assetsEmitted = true;
            $html = $this->styleTag() . $html . $this->scriptTag();
        }

        return $html;
    }

    /**
     * Replace every shortcode found in the text of an HTML document with its
     * slider. Text inside <script>, <textarea>, <code> etc. and attribute
     * values are left alone, as are codes that belong to no slider.
     */
    public function replaceShortcodes(string $html): string
    {
        if (! str_contains($html, '[') || ! preg_match_all(self::PATTERN, $html, $m)) {
            return $html;
        }

        /** @var Collection<string, HeroSlider> $sliders */
        $sliders = HeroSlider::whereIn('shortcode', array_unique($m[1]))
            ->with('activeSlides')
            ->get()
            ->keyBy('shortcode');

        if ($sliders->isEmpty()) {
            return $html;
        }

        // 1. Swap shortcodes in text nodes for placeholders.
        $parts = preg_split('/(<!--.*?-->|<[^>]*>)/s', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        $raw = null;
        $found = false;
        foreach ($parts as $i => $part) {
            if ($i % 2 === 1) { // a tag or comment
                if ($raw === null && preg_match('/^<(' . implode('|', self::RAW_TAGS) . ')\b/i', $part, $t)) {
                    $raw = strtolower($t[1]);
                } elseif ($raw !== null && preg_match('/^<\/' . $raw . '\s*>/i', $part)) {
                    $raw = null;
                }
                continue;
            }
            if ($raw !== null || ! str_contains($part, '[')) {
                continue;
            }
            $parts[$i] = preg_replace_callback(self::PATTERN, function ($match) use ($sliders, &$found) {
                if (! $sliders->has($match[1])) {
                    return $match[0];
                }
                $found = true;

                return '<!--hero-slider:' . $match[1] . '-->';
            }, $part);
        }

        if (! $found) {
            return $html;
        }
        $html = implode('', $parts);

        // 2. A shortcode alone in a <p>/<span>/<h2>… replaces that element too,
        //    so the slider is not nested in a paragraph.
        $wrapped = '~<(' . self::UNWRAP_TAGS . ')\b[^>]*>\s*(<!--hero-slider:[\d-]+-->)\s*</\1\s*>~i';
        do {
            $html = preg_replace($wrapped, '$2', $html, -1, $count);
        } while ($count > 0);

        // 3. Render each placeholder.
        $rendered = false;
        $html = preg_replace_callback('/<!--hero-slider:([\d-]+)-->/', function ($match) use ($sliders, &$rendered) {
            $out = $this->render($sliders[$match[1]], false);
            $rendered = $rendered || $out !== '';

            return $out;
        }, $html);

        if ($rendered && ! $this->assetsEmitted) {
            $this->assetsEmitted = true;
            $html = $this->insertBefore($html, '</head>', $this->styleTag(), prepend: true);
            $html = $this->insertBefore($html, '</body>', $this->scriptTag(), prepend: false);
        }

        return $html;
    }

    /**
     * Insert the sliders assigned to a page (Admin → Hero Sliders → "Show on
     * page") at the start or end of the page's <main> element.
     */
    public function placeAssigned(string $html, \App\Models\Page $page): string
    {
        $sliders = HeroSlider::where('page_id', $page->id)->where('is_active', true)->with('activeSlides')->orderBy('id')->get();
        if ($sliders->isEmpty()) {
            return $html;
        }

        $rendered = false;
        foreach (['top', 'bottom'] as $placement) {
            $out = '';
            foreach ($sliders->where('placement', $placement) as $slider) {
                $out .= $this->render($slider, false);
            }
            if ($out === '') {
                continue;
            }
            $rendered = true;

            if ($placement === 'top') {
                $html = preg_match('/<main\b[^>]*>/i', $html, $m, PREG_OFFSET_CAPTURE)
                    ? substr_replace($html, $out, $m[0][1] + strlen($m[0][0]), 0)
                    : (preg_match('/<body\b[^>]*>/i', $html, $m, PREG_OFFSET_CAPTURE)
                        ? substr_replace($html, $out, $m[0][1] + strlen($m[0][0]), 0)
                        : $out . $html);
            } else {
                $pos = strripos($html, '</main>');
                $pos = $pos === false ? strripos($html, '</body>') : $pos;
                $html = $pos === false ? $html . $out : substr_replace($html, $out, $pos, 0);
            }
        }

        if ($rendered && ! $this->assetsEmitted) {
            $this->assetsEmitted = true;
            $html = $this->insertBefore($html, '</head>', $this->styleTag(), prepend: true);
            $html = $this->insertBefore($html, '</body>', $this->scriptTag(), prepend: false);
        }

        return $html;
    }

    private function insertBefore(string $html, string $needle, string $tag, bool $prepend): string
    {
        $pos = stripos($html, $needle);
        if ($pos === false) {
            return $prepend ? $tag . $html : $html . $tag;
        }

        return substr_replace($html, $tag, $pos, 0);
    }

    private function styleTag(): string
    {
        return '<link rel="stylesheet" href="' . e($this->asset('hero-slider.css')) . '">';
    }

    private function scriptTag(): string
    {
        return '<script src="' . e($this->asset('hero-slider.js')) . '" defer></script>';
    }

    private function asset(string $file): string
    {
        $path = public_path('vendor/hero-slider/' . $file);

        return asset('vendor/hero-slider/' . $file) . (is_file($path) ? '?v=' . filemtime($path) : '');
    }
}
