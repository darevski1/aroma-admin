@extends('layouts.cenobots')

@include('partials.page-seo', ['seoPage' => \App\Models\Page::where('layout', 'pages.cennobots.models.index')->first(), 'seoTitle' => 'CenoBots модели — L3, L4, L50, S5, SP50 | IT Plus Smart', 'seoDescription' => 'Целосна палета CenoBots автономни роботи за чистење: scrubber dryer моделите L3, L4 и L50, индустрискиот sweeper S5 и vacuum sweeper-от SP50.'])

@section('content')

@php
    // The nav points back at the CenoBots landing page for its in-page sections.
    $base = route('cennobots');

    $arrowIcon = '<svg class="btn__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
    aria-hidden="true">
    <path d="M5 12h14" />
    <path d="m12 5 7 7-7 7" />
</svg>';
@endphp

<a class="skip-link" href="#main">Skip to content</a>

@include('pages.cennobots.partials._header', ['base' => $base, 'active' => 'models'])

<main id="main">
    {{-- ======================= HERO SLIDER (local to /modeli) ======================= --}}
    @php
        $mhSlides = [
            ['src' => 'assets/images/cenobots/opt/hero-scene-1600.webp', 'alt' => 'CenoBots L50 во трговски центар', 'pos' => '50% 60%'],
            ['src' => 'assets/images/cenobots/cenno/hero-robot.jpg', 'alt' => 'CenoBots робот за чистење пред бутици', 'pos' => '50% 55%'],
            ['src' => 'assets/images/cenobots/opt/sp50-08-1400.webp', 'alt' => 'CenoBots SP50 во болнички хол', 'pos' => '50% 55%'],
            ['src' => 'assets/images/cenobots/opt/l50-08-1200.webp', 'alt' => 'CenoBots L50 автономен scrubber dryer', 'pos' => '50% 55%'],
        ];
    @endphp
    <style>
        .mh-hero { position: relative; isolation: isolate; overflow: hidden; background: #05080f; color: #fff; }
        .mh-hero__slide { position: absolute; inset: 0; opacity: 0; transition: opacity 1.2s ease; z-index: 0; }
        .mh-hero__slide.is-active { opacity: 1; z-index: 1; }
        .mh-hero__slide img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .mh-hero__veil { position: absolute; inset: 0; z-index: 2; pointer-events: none;
            background: linear-gradient(90deg, rgba(5,10,20,.78) 0%, rgba(5,10,20,.5) 55%, rgba(5,10,20,.3) 100%),
                        linear-gradient(0deg, rgba(5,10,20,.55) 0%, transparent 45%); }
        .mh-hero__inner { position: relative; z-index: 3; min-height: 82vh; display: flex; flex-direction: column; justify-content: center;
            padding-top: 9rem; padding-bottom: 6rem; }
        .mh-hero__content { max-width: 40rem; }
        .mh-hero__title { margin-top: 1rem; color: #fff; font-size: clamp(2rem, 5.5vw, 3.75rem); line-height: 1.08; }
        .mh-hero__lede { margin-top: 1.25rem; max-width: 34rem; font-size: clamp(1rem, 2vw, 1.25rem); line-height: 1.6; color: rgba(255,255,255,.88); }
        .mh-hero__actions { margin-top: 2rem; }
        .mh-hero__arrow { position: absolute; top: 50%; z-index: 4; transform: translateY(-50%); width: 3rem; height: 3rem;
            border-radius: 50%; border: 1px solid rgba(255,255,255,.35); background: rgba(5,10,20,.35); color: #fff; cursor: pointer;
            display: grid; place-items: center; backdrop-filter: blur(4px); transition: background .2s, border-color .2s; }
        .mh-hero__arrow:hover, .mh-hero__arrow:focus-visible { background: rgba(255,255,255,.2); border-color: #fff; }
        .mh-hero__arrow--prev { left: 1rem; } .mh-hero__arrow--next { right: 1rem; }
        .mh-hero__dots { position: absolute; z-index: 4; left: 50%; bottom: 1.75rem; transform: translateX(-50%); display: flex; gap: .6rem; }
        .mh-hero__dot { width: 2.25rem; height: .3rem; padding: 0; border: 0; border-radius: 2px; background: rgba(255,255,255,.35); cursor: pointer; transition: background .2s; }
        .mh-hero__dot.is-active { background: #fff; }
        @media (max-width: 767px) {
            .mh-hero__inner { min-height: 78vh; padding-top: 7.5rem; padding-bottom: 5rem; }
            .mh-hero__arrow { display: none; }
            .mh-hero__veil { background: linear-gradient(0deg, rgba(5,10,20,.8) 0%, rgba(5,10,20,.5) 100%); }
        }
        @media (prefers-reduced-motion: reduce) { .mh-hero__slide { transition: none; } }
    </style>

    <section class="mh-hero" id="mh-hero" aria-roledescription="carousel" aria-label="CenoBots модели">
        @foreach ($mhSlides as $slide)
            <div class="mh-hero__slide {{ $loop->first ? 'is-active' : '' }}" aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
                <img src="{{ asset($slide['src']) }}" alt="{{ $slide['alt'] }}" style="object-position: {{ $slide['pos'] }}"
                    @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif>
            </div>
        @endforeach
        <div class="mh-hero__veil"></div>

        <div class="container mh-hero__inner">
            <div class="mh-hero__content">
                <p class="eyebrow">CenoBots</p>
                <h1 class="mh-hero__title">Запознајте ги Cenobots моделите</h1>
                <p class="mh-hero__lede">Откријте интелигентни роботски решенија за иднината.</p>
                <div class="mh-hero__actions">
                    <a href="#paleta" class="btn btn--brand">Истражи ги моделите {!! $arrowIcon !!}</a>
                </div>
            </div>
        </div>

        <button type="button" class="mh-hero__arrow mh-hero__arrow--prev" aria-label="Претходна слика">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6" /></svg>
        </button>
        <button type="button" class="mh-hero__arrow mh-hero__arrow--next" aria-label="Следна слика">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
        </button>
        <div class="mh-hero__dots">
            @foreach ($mhSlides as $slide)
                <button type="button" class="mh-hero__dot {{ $loop->first ? 'is-active' : '' }}" aria-label="Слика {{ $loop->iteration }}"></button>
            @endforeach
        </div>
    </section>
    <script>
        (function () {
            var root = document.getElementById('mh-hero');
            if (!root) return;
            var slides = root.querySelectorAll('.mh-hero__slide');
            var dots = root.querySelectorAll('.mh-hero__dot');
            var i = 0, timer = null, DELAY = 5500;
            function show(n) {
                i = (n + slides.length) % slides.length;
                slides.forEach(function (s, k) { s.classList.toggle('is-active', k === i); s.setAttribute('aria-hidden', k === i ? 'false' : 'true'); });
                dots.forEach(function (d, k) { d.classList.toggle('is-active', k === i); });
            }
            function stop() { if (timer) { clearInterval(timer); timer = null; } }
            function start() {
                stop();
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                timer = setInterval(function () { show(i + 1); }, DELAY);
            }
            root.querySelector('.mh-hero__arrow--prev').addEventListener('click', function () { show(i - 1); start(); });
            root.querySelector('.mh-hero__arrow--next').addEventListener('click', function () { show(i + 1); start(); });
            dots.forEach(function (d, k) { d.addEventListener('click', function () { show(k); start(); }); });
            root.addEventListener('mouseenter', stop);
            root.addEventListener('mouseleave', start);
            document.addEventListener('visibilitychange', function () { if (document.hidden) { stop(); } else { start(); } });
            start();
        })();
    </script>

    {{-- ======================= PALETTE ======================= --}}
    <section class="section" id="paleta">
        <div class="container">
            <div class="section__head reveal">
                <p class="eyebrow">Палета</p>
                <h2>Пет модели за различни објекти</h2>
                <p>Секој модел е предвиден за различна површина, интензитет на работа и тип на нечистотија.</p>
            </div>

            <div class="section__body grid grid--3">
                @foreach ($models as $model)
                    <a class="card card--link group reveal" href="{{ route('cennobots.model', $model['id']) }}">
                        <div class="card__meta">
                            <span class="strip__num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="card__type">{{ $model['type'] }}</span>
                        </div>

                        <div class="card__shot">
                            <img src="{{ asset($model['card']) }}" alt="{{ $model['full'] }}"
                                width="{{ $model['card_size'][0] }}" height="{{ $model['card_size'][1] }}" loading="lazy">
                        </div>

                        <h3 class="card__heading card__heading--sm">{{ $model['full'] }}</h3>
                        <p class="card__body">{{ $model['description'] }}</p>

                        <ul class="chips chips--card">
                            @foreach ($model['use_cases'] as $useCase)
                                <li class="chip">{{ $useCase }}</li>
                            @endforeach
                        </ul>

                        <span class="card__link">Детали за {{ $model['name'] }} {!! $arrowIcon !!}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ======================= FREE DEMO ======================= --}}
    <section class="cta-band" id="kontakt">
        <div class="container cta-band__inner">
            <div class="reveal">
                <p class="eyebrow">Бесплатно демо</p>
                <h2>Не купувајте робот пред да го тестирате.</h2>
                <p>Организираме демонстрација во вашиот објект и ви предлагаме најсоодветен модел.</p>
            </div>

            <div class="cta-band__actions reveal">
                <a href="{{ route('kontakt') }}" class="btn btn--brand">Побарај бесплатен демо</a>
                <a href="{{ route('kontakt') }}" class="btn btn--outline-light">Побарај понуда</a>
            </div>
        </div>
    </section>
</main>

@include('pages.cennobots.partials._footer', ['base' => $base, 'active' => 'models'])

@endsection