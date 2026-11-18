{{-- Public markup of one hero slider. Rendered by App\Services\HeroSlider\HeroSliderRenderer;
     styles/behaviour live in public/vendor/hero-slider/. --}}
@php $count = $slides->count(); @endphp
<section class="hs hs--{{ $slider->transition }} {{ $slider->full_width ? 'hs--full' : '' }}" id="{{ $domId }}" data-hero-slider
    data-autoplay="{{ $slider->autoplay && $count > 1 ? '1' : '0' }}"
    data-interval="{{ $slider->interval_ms }}"
    data-pause-hover="{{ $slider->pause_on_hover ? '1' : '0' }}"
    style="--hs-h: {{ $slider->height_desktop }}vh; --hs-hm: {{ $slider->height_mobile }}vh; --hs-speed: {{ $slider->transition_ms }}ms;"
    aria-roledescription="carousel" aria-label="{{ $slider->name }}">
    <div class="hs__viewport">
        <div class="hs__track">
            @foreach ($slides as $slide)
                @php
                    $first = $loop->first;
                    $link = $slide->button_url;
                    $target = $slide->button_new_tab ? ' target="_blank" rel="noopener"' : '';
                @endphp
                <div class="hs__slide hs__slide--{{ $slide->text_alignment }} {{ $first ? 'is-active' : '' }}"
                    role="group" aria-roledescription="slide" aria-label="{{ $loop->iteration }} / {{ $count }}"
                    @unless ($first) aria-hidden="true" inert @endunless
                    style="--hs-overlay: {{ $slide->overlay_opacity / 100 }};">
                    <picture class="hs__media">
                        @if ($slide->mobile_url)
                            <source media="(max-width: 767px)" srcset="{{ $slide->mobile_url }}"
                                @if ($slide->mobile_width) width="{{ $slide->mobile_width }}" height="{{ $slide->mobile_height }}" @endif>
                        @endif
                        <img src="{{ $slide->desktop_url }}" alt="{{ $slide->image_alt ?: $slide->title }}"
                            @if ($slide->desktop_width) width="{{ $slide->desktop_width }}" height="{{ $slide->desktop_height }}" @endif
                            style="object-position: {{ $slide->image_position }};"
                            decoding="async"
                            @if ($first) fetchpriority="high" @else loading="lazy" @endif>
                    </picture>
                    <div class="hs__veil" aria-hidden="true"></div>

                    @if ($slide->link_whole_slide && $link)
                        <a class="hs__cover-link" href="{{ $link }}"{!! $target !!} aria-label="{{ $slide->title ?: $slide->button_text ?: $slide->image_alt ?: 'Open' }}"></a>
                    @endif

                    @if ($slide->title || $slide->subtitle || $slide->description || $slide->hasButton())
                        <div class="hs__inner">
                            <div class="hs__content">
                                @if ($slide->subtitle)<p class="hs__subtitle">{{ $slide->subtitle }}</p>@endif
                                @if ($slide->title)<h2 class="hs__title">{{ $slide->title }}</h2>@endif
                                @if ($slide->description)<p class="hs__desc">{!! nl2br(e($slide->description)) !!}</p>@endif
                                @if ($slide->hasButton())
                                    <a class="hs__btn" href="{{ $link }}"{!! $target !!}>
                                        {{ $slide->button_text }}
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14" /><path d="m12 5 7 7-7 7" /></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    @if ($count > 1)
        @if ($slider->show_arrows)
            <button type="button" class="hs__arrow hs__arrow--prev" data-hs-prev aria-label="Previous slide">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6" /></svg>
            </button>
            <button type="button" class="hs__arrow hs__arrow--next" data-hs-next aria-label="Next slide">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
            </button>
        @endif
        @if ($slider->show_dots)
            <div class="hs__dots">
                @foreach ($slides as $slide)
                    <button type="button" class="hs__dot {{ $loop->first ? 'is-active' : '' }}" data-hs-dot="{{ $loop->index }}"
                        aria-label="Slide {{ $loop->iteration }}" @if ($loop->first) aria-current="true" @endif></button>
                @endforeach
            </div>
        @endif
    @endif
</section>
