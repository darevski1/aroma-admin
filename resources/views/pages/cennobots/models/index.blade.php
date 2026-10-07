@extends('layouts.cenobots')

@section('title', 'CenoBots модели — L3, L4, L50, S5, SP50 | IT Plus Smart')
@section('description', 'Целосна палета CenoBots автономни роботи за чистење: scrubber dryer моделите L3, L4 и L50, индустрискиот sweeper S5 и vacuum sweeper-от SP50.')

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
    {{-- ======================= HERO ======================= --}}
    <section class="hero hero--product">
        <img class="hero__media" src="{{ asset('assets/images/cenobots/cenno/hero-robot.jpg') }}"
            alt="CenoBots автономен робот за чистење во комерцијален објект" width="1920" height="1088"
            fetchpriority="high">
        <div class="hero__veil"></div>

        <div class="container hero__inner">
            <div class="hero__content reveal">
                <p class="eyebrow">Модели</p>
                <h1 class="hero__title">Кој робот ви треба?</h1>
                <p class="hero__lede">Од суво метење до автономно машинско перење – изберете го решението според типот на
                    површината, големината на објектот и начинот на работа.</p>

                <div class="hero__actions">
                    <a href="{{ route('kontakt') }}" class="btn btn--brand">Побарај бесплатен демо</a>
                    <a href="{{ route('kontakt') }}" class="btn btn--outline-light">Побарај понуда</a>
                </div>

                <p class="hero__note">Пет модели, една технологија. Изберете го моделот според вашиот објект.
                </p>
            </div>
        </div>
    </section>

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