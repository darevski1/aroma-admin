<?php

namespace App\View\Components;

use App\Services\HeroSlider\HeroSliderRenderer;
use Illuminate\View\Component;

/**
 * <x-hero-slider code="2026-1" /> — renders a hero slider by its shortcode.
 * Prints nothing when the slider is unknown, disabled or has no enabled slides.
 */
class HeroSlider extends Component
{
    public function __construct(public string $code)
    {
    }

    public function render(): \Closure
    {
        return fn () => app(HeroSliderRenderer::class)->renderCode($this->code);
    }
}
