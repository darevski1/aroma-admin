@extends('layouts.cenobots')

@section('title', $model['full'] . ' — ' . $model['type'] . ' | IT Plus Smart')

@section('description', Str::limit($model['description'], 180))

@section('content')

@php
// The nav points back at the CenoBots landing page for its in-page sections.
$base = route('cennobots');

// Headline characteristics, per the reference page.
$highlights = $model['highlights'] ?? [];
@endphp

<a class="skip-link" href="#main">Skip to content</a>

@include('pages.cennobots.partials._header', ['base' => $base, 'active' => 'models'])

<main id="main">
    {{-- ======================= 1. MODEL HERO ======================= --}}
    <section class="mp-hero">
        <div class="container mp-hero__inner">
            <div class="reveal">
                <p class="eyebrow">{{ $model['type'] }}</p>
                <h1 class="mp-hero__title">{{ $model['full'] }}</h1>
                <p class="mp-hero__lede">{{ $model['description'] }}</p>

                <div class="mp-hero__actions">
                    <a href="{{ route('kontakt') }}" class="btn btn--brand">Побарај бесплатен демо</a>
                    <a href="#mp-specs" class="btn btn--outline-light">Технички податоци</a>
                </div>
            </div>

            <div class="mp-hero__media">
                <img src="{{ asset($model['stage']) }}" alt="{{ $model['full'] }} — {{ $model['type'] }}"
                    width="{{ $model['stage_size'][0] }}" height="{{ $model['stage_size'][1] }}" fetchpriority="high">
            </div>
        </div>
    </section>

    {{-- =============== 2. FEATURES + TECHNICAL DATA ================
         The two blocks stay in document order, one below the other, each on
         its own full-width row. --}}
    <section class="mp-section">
        <div class="container mp-split mp-split--stack">
            <div class="reveal">
                <p class="eyebrow">Карактеристики</p>
                <h2 class="mp-split__title">Што го одликува моделот</h2>

                <ul class="mp-highlights">
                    @foreach ($highlights as $highlight)
                    <li class="mp-highlights__item">
                        <span class="mp-highlights__dot" aria-hidden="true"></span>
                        <span class="mp-highlights__text">{{ $highlight }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="reveal" id="mp-specs">
                <p class="eyebrow">Спецификации</p>
                <h2 class="mp-split__title">Технички податоци</h2>

                {{-- Authoritative values come from config/cenobots.php — never invented. --}}
                <dl class="mp-specs">
                    @foreach ($model['specs'] as [$specLabel, $specValue])
                    <div class="mp-specs__row">
                        <dt class="mp-specs__label">{{ $specLabel }}</dt>
                        <dd class="mp-specs__value">{{ $specValue }}</dd>
                    </div>
                    @endforeach
                </dl>
            </div>
        </div>
    </section>

    {{-- ======================= 3. APPLICATIONS ======================= --}}
    <section class="mp-section mp-section--surface">
        <div class="container">
            <div class="reveal">
                <p class="eyebrow">Примена</p>
                <h2 class="mp-split__title">Соодветно за</h2>
                <p class="mp-split__intro">Типови објекти каде овој модел најчесто се применува.</p>
            </div>

            <div class="mp-usecases">
                @foreach ($model['use_cases'] as $useCase)
                <p class="mp-usecase reveal">{{ $useCase }}</p>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ======================= 4. OTHER MODELS ======================= --}}
    <section class="mp-section">
        <div class="container">
            <div class="reveal">
                <p class="eyebrow">Останати модели</p>
                <h2 class="mp-split__title">Споредете со други модели</h2>
            </div>

            <div class="mp-compare">
                @foreach ($others as $other)
                <a class="mp-compare__card reveal" href="{{ route('cennobots.model', $other['id']) }}">
                    <div class="mp-compare__shot">
                        <img src="{{ asset($other['card']) }}" alt="{{ $other['full'] }}"
                            width="{{ $other['card_size'][0] }}" height="{{ $other['card_size'][1] }}" loading="lazy">
                    </div>
                    <p class="mp-compare__name">{{ $other['name'] }}</p>
                    <p class="mp-compare__text">{{ $other['summary'] }}</p>
                </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ======================= 5. CTA BAND ======================= --}}
    <section class="mp-cta">
        <div class="container mp-cta__inner">
            <div class="mp-cta__copy reveal">
                <p class="eyebrow">Бесплатно демо</p>
                <h2 class="mp-cta__title">Не купувајте робот пред да го тестирате.</h2>
                <p class="mp-cta__text">Организираме демонстрација во вашиот об историја и ви предлагаме најсоодветен
                    модел.</p>
            </div>

            <div class="mp-cta__actions reveal">
                <a href="{{ route('kontakt') }}" class="btn btn--brand">Побарај бесплатен демо</a>
                <a href="{{ route('cennobots.models') }}" class="btn btn--outline-light">Погледни ги моделите</a>
            </div>
        </div>
    </section>

    {{-- ======================= 6. CATALOG DOWNLOAD ======================= --}}
    <section class="mp-section mp-section--surface mp-catalog">
        <div class="container">
            <div class="mp-catalog__panel reveal">
                <div class="mp-catalog__media">
                    <img src="{{ asset($model['stage']) }}" alt="{{ $model['full'] }} — {{ $model['type'] }}"
                        loading="lazy">

                    <span class="mp-catalog__chip">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" aria-hidden="true">
                            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                            <polyline points="14 2 14 8 20 8" />
                            <path d="M16 13H8" />
                            <path d="M16 17H8" />
                            <path d="M10 9H8" />
                        </svg>
                        {{ config('cenobots.brochure.label') }}
                    </span>
                </div>

                <div class="mp-catalog__body">
                    <p class="eyebrow">Каталог</p>
                    <h2 class="mp-catalog__title">Преземи го каталогот за {{ $model['full'] }}</h2>
                    <p class="mp-catalog__text">Целосно досие за моделот – подготвен за печатење и споделување
                        со вашиот тим. Технички податоци, перформанси и можности за користење на едно место.</p>

                    <ul class="mp-highlights">
                        <li class="mp-highlights__item">
                            <span class="mp-highlights__dot" aria-hidden="true"></span>
                            <span class="mp-highlights__text">Целосни технички податоци и перформанси</span>
                        </li>
                        <li class="mp-highlights__item">
                            <span class="mp-highlights__dot" aria-hidden="true"></span>
                            <span class="mp-highlights__text">Сите CenoBots модели во еден документ</span>
                        </li>
                        <li class="mp-highlights__item">
                            <span class="mp-highlights__dot" aria-hidden="true"></span>
                            <span class="mp-highlights__text">Примена, опции и сервисна поддршка</span>
                        </li>
                    </ul>

                    <div class="mp-catalog__actions">
                        <a href="{{ asset(config('cenobots.brochure.path')) }}" class="btn btn--brand group"
                            download>
                            Download catalog
                            <svg class="btn__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <polyline points="7 10 12 15 17 10" />
                                <line x1="12" x2="12" y1="15" y2="3" />
                            </svg>
                        </a>

                        <a href="{{ asset($model['brochure']['path']) }}" class="btn btn--outline" download>
                            {{ $model['brochure']['label'] }}
                        </a>
                    </div>

                    <p class="mp-catalog__note">{{ $model['brochure']['meta'] }}</p>
                </div>
            </div>
        </div>
    </section>
</main>

@include('pages.cennobots.partials._footer', ['base' => $base, 'active' => 'models'])

@endsection