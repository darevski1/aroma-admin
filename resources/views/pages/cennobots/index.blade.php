@extends('layouts.cenobots')

@if (!empty($page?->meta_title) || !empty($page?->meta_description) || $page?->setting('canonical_url') || $page?->setting('og_image') || $page?->setting('robots_index') === false)
@include('partials.page-seo', [
    'seoPage' => $page,
    'seoTitle' => 'CenoBots — Автономни роботи за чистење | IT Plus Smart',
    'seoDescription' => 'Професионални автономни роботи за комерцијално и индустриско чистење во Северна Македонија – продажба, изнајмување, обука и локален сервис преку IT Plus Smart.',
])
@endif

@section('content')

@php
// Images copied verbatim from cenno/assets/img. The cenno prefix is kept in
// the public path so the served bytes match the source folder exactly.
$cennoImg = 'assets/images/';
$arrowIcon = '<svg class="btn__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
    aria-hidden="true">
    <path d="M5 12h14" />
    <path d="m12 5 7 7-7 7" />
</svg>';
@endphp

<a class="skip-link" href="#main">Skip to content</a>

{{-- ======================= HEADER + MOBILE NAV ======================= --}}
@include('pages.cennobots.partials._header', ['base' => '', 'active' => 'home'])

<main id="main">
    {{-- ======================= HERO ======================= --}}
    <section class="hero" id="top">
        <img class="hero__media"
            src="{{ asset($cennoImg . '/cenobots/cenno/1d0e5e38-0382-4ada-a946-7bf3a22b0c35.png') }}"
            alt="CenoBots автономен робот за чистење во комерцијален објект" width="1920" height="1088"
            fetchpriority="high">
        <div class="hero__veil"></div>

        <div class="container hero__inner">
            <div class="hero__content reveal">
                <p class="eyebrow">CenoBots x IT Plus Smart</p>
                <h1 class="hero__title">Автономно чистење. Попаметно. Поефикасно.</h1>
                <p class="hero__lede">Професионални автономни роботи за комерцијално и индустриско чистење – со локална
                    поддршка, сервис и обука.</p>

                <div class="hero__actions">
                    <a href="#contact" class="btn btn--brand">Побарај бесплатен демо</a>
                    <a href="#models" class="btn btn--outline-light">Погледни ги моделите</a>
                </div>

                <p class="hero__note">Овластен CenoBots дистрибутер и локален сервисен партнер во Северна Македонија.
                </p>
            </div>
        </div>
    </section>
    {{-- ======================= FEATURE STRIP ======================= --}}
    <section class="strip" aria-label="Клучни предности">
        <div class="container strip__grid">
            <div class="strip__item reveal">
                <p class="strip__num">01</p>
                <h3>Овластен дистрибутер</h3>
                <p>CenoBots во Северна Македонија</p>
            </div>
            <div class="strip__item reveal">
                <p class="strip__num">02</p>
                <h3>Локален сервис</h3>
                <p>Поддршка, обука и одржување</p>
            </div>
            <div class="strip__item reveal">
                <p class="strip__num">03</p>
                <h3>Бесплатно демо</h3>
                <p>Тестирајте го роботот во вашиот објект</p>
            </div>
            <div class="strip__item reveal">
                <p class="strip__num">04</p>
                <h3>Продажба и изнајмување</h3>
                <p>Решение според потребите на објектот</p>
            </div>
        </div>
    </section>
    {{-- section video --}}
    <section class="video-section" aria-label="Видео">
        <video class="video-section__video" autoplay muted loop playsinline>
            <source src="{{ asset('/assets/video/Cenobots S5 Official Video.mp4') }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </section>


    {{-- ======================= MODELS ======================= --}}
    <section class="section" id="models">
        <div class="container">
            <div class="section__head reveal">
                <p class="eyebrow">Модели</p>
                <h2>Кој робот ви треба?</h2>
                <p>Од суво метење до автономно машинско перење – изберете го решението според типот на површината,
                    големината на објектот и начинот на работа.</p>
            </div>

            <div class="section__body grid grid--3">
                <a class="card card--link group reveal" href="{{ route('cennobots.model', 'l3') }}">
                    <div class="card__meta">
                        <span class="card__name">L3</span>
                        <span class="card__type">Автономен scrubber dryer</span>
                    </div>
                    <div class="card__shot">
                        <img src="{{ asset($cennoImg . '/cenobots/L3-images/01.png') }}" alt="CenoBots L3" width="1024"
                            height="1024" loading="lazy">
                    </div>
                    <p class="card__body">Компактен робот за помали и средни комерцијални простори.</p>
                    <span class="card__link">Детали за L3 {!! $arrowIcon !!}</span>
                </a>

                <a class="card card--link group reveal" href="{{ route('cennobots.model', 'l4') }}">
                    <div class="card__meta">
                        <span class="card__name">L4</span>
                        <span class="card__type">Автономен scrubber dryer</span>
                    </div>
                    <div class="card__shot">
                        <img src="{{ asset($cennoImg . '/cenobots/L4/1.png') }}" alt="CenoBots L4" width="1024"
                            height="1024" loading="lazy">
                    </div>
                    <p class="card__body">Робот за комерцијални и јавни објекти со поголема површина.</p>
                    <span class="card__link">Детали за L4 {!! $arrowIcon !!}</span>
                </a>

                <a class="card card--link group reveal" href="{{ route('cennobots.model', 'l50') }}">
                    <div class="card__meta">
                        <span class="card__name">L50</span>
                        <span class="card__type">Автономен scrubber dryer</span>
                    </div>
                    <div class="card__shot">
                        <img src="{{ asset($cennoImg . '/cenobots/L50/1.png') }}" alt="CenoBots L50" width="1024"
                            height="1024" loading="lazy">
                    </div>
                    <p class="card__body">Високопродуктивно решение за големи површини.</p>
                    <span class="card__link">Детали за L50 {!! $arrowIcon !!}</span>
                </a>

                <a class="card card--link group reveal" href="{{ route('cennobots.model', 's5') }}">
                    <div class="card__meta">
                        <span class="card__name">S5</span>
                        <span class="card__type">Автономен индустриски sweeper</span>
                    </div>
                    <div class="card__shot">
                        <img src="{{ asset($cennoImg . 'cenobots/S5-images/S5-1.png') }}" alt="CenoBots S5" width="1024"
                            height="1024" loading="lazy">
                    </div>
                    <p class="card__body">Суво метење и собирање отпад во индустриски услови.</p>
                    <span class="card__link">Детали за S5 {!! $arrowIcon !!}</span>
                </a>

                <a class="card card--link group reveal" href="{{ route('cennobots.model', 'sp50') }}">
                    <div class="card__meta">
                        <span class="card__name">SP50</span>
                        <span class="card__type">Автономен vacuum sweeper</span>
                    </div>
                    <div class="card__shot">
                        <img src="{{ asset($cennoImg . '/cenobots/SP50/1.png') }}" alt="CenoBots SP50" width="1024"
                            height="1024" loading="lazy">
                    </div>
                    <p class="card__body">Суво чистење со силна филтрација.</p>
                    <span class="card__link">Детали за SP50 {!! $arrowIcon !!}</span>
                </a>
            </div>
        </div>
    </section>

    {{-- ======================= TECHNOLOGY ======================= --}}
    <section class="section section--ink" id="technology">
        <div class="container">
            <div class="section__head reveal">
                <p class="eyebrow">Технологија</p>
                <h2>Зошто CenoBots?</h2>
            </div>

            <div class="section__body divider-grid divider-grid--3">
                <div class="reveal">
                    <h3>Автономна работа</h3>
                    <p>Роботот самостојно ги извршува зададените задачи.</p>
                </div>
                <div class="reveal">
                    <h3>AI + 3D LiDAR навигација</h3>
                    <p>Интелигентно препознавање и адаптација на околината.</p>
                </div>
                <div class="reveal">
                    <h3>Мобилна апликација</h3>
                    <p>Поставување задачи и следење на извештаи.</p>
                </div>
                <div class="reveal">
                    <h3>OTA software updates</h3>
                    <p>Бесплатни ажурирања на софтверот.</p>
                </div>
                <div class="reveal">
                    <h3>Интелигентни режими</h3>
                    <p>Quick Demo, Follow-Me, DeepClean, TeamClean и Voice Command.</p>
                </div>
                <div class="reveal">
                    <h3>Локална поддршка</h3>
                    <p>Продажба, обука, сервис и потрошен материјал преку IT Plus Smart.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================= SOLUTIONS ======================= --}}
    <section class="section section--surface" id="solutions">
        <div class="container">
            <div class="section__head reveal">
                <p class="eyebrow">Решенија</p>
                <h2>Едно решение за различни објекти</h2>
                <p>Автономното чистење се применува во објекти со различна намена, површина и режим на работа.</p>
            </div>

            <div class="section__body grid grid--4">
                <div class="card card--hover reveal">
                    <h3 class="card__title">Шопинг центри и retail</h3>
                    <p>Големи јавни површини со висока фреквенција.</p>
                </div>
                <div class="card card--hover reveal">
                    <h3 class="card__title">Магацини и логистички центри</h3>
                    <p>Прашина, цврст отпад и долги коридори.</p>
                </div>
                <div class="card card--hover reveal">
                    <h3 class="card__title">Фабрики и производни погони</h3>
                    <p>Интензивна работа и индустриски подови.</p>
                </div>
                <div class="card card--hover reveal">
                    <h3 class="card__title">Болници и здравствени установи</h3>
                    <p>Стандарди за хигиена и тивка работа.</p>
                </div>
                <div class="card card--hover reveal">
                    <h3 class="card__title">Хотели</h3>
                    <p>Лоби, коридори и заеднички простори.</p>
                </div>
                <div class="card card--hover reveal">
                    <h3 class="card__title">Канцелариски и деловни објекти</h3>
                    <p>Чистење без прекин на работниот ден.</p>
                </div>
                <div class="card card--hover reveal">
                    <h3 class="card__title">Аеродроми и транспортни центри</h3>
                    <p>Работа 24/7 во динамична средина.</p>
                </div>
                <div class="card card--hover reveal">
                    <h3 class="card__title">Училишта и јавни институции</h3>
                    <p>Големи површини со ограничен персонал.</p>
                </div>
                <div class="card card--hover reveal">
                    <h3 class="card__title">Паркинг гаражи</h3>
                    <p>Суво метење и контрола на прашина.</p>
                </div>
                <div class="card card--hover reveal">
                    <h3 class="card__title">Спортски центри и други големи објекти</h3>
                    <p>Отворени површини и трибуни.</p>
                </div>
            </div>

            <div class="section__cta reveal">
                <a href="#solutions" class="btn btn--outline">Сите решенија</a>
            </div>
        </div>
    </section>

    {{-- ======================= FREE DEMO ======================= --}}
    <section class="section section--ink section--media" id="demo">
        <img class="section__media-img" src="{{ asset($cennoImg . 'warehouse-sweeper.jpg') }}"
            alt="Автономен робот во логистички центар" width="1600" height="1008" loading="lazy">
        <div class="section__media-veil"></div>

        <div class="container">
            <div class="section__head reveal">
                <p class="eyebrow">Бесплатно демо</p>
                <h2>Не купувајте робот пред да го тестирате.</h2>
                <p>Организираме бесплатна демонстрација во вашиот објект. Ќе ја разгледаме површината, начинот на работа
                    и условите за чистење, ќе го предложиме најсоодветниот модел и ќе ви покажеме како роботот работи во
                    реална средина.</p>
            </div>

            <ol class="section__body divider-grid divider-grid--5 divider-grid--compact">
                <li class="reveal"><span class="strip__num">01</span>
                    <p class="step__label">Анализа на објектот</p>
                </li>
                <li class="reveal"><span class="strip__num">02</span>
                    <p class="step__label">Избор на модел</p>
                </li>
                <li class="reveal"><span class="strip__num">03</span>
                    <p class="step__label">Бесплатно демо</p>
                </li>
                <li class="reveal"><span class="strip__num">04</span>
                    <p class="step__label">Предлог-решение</p>
                </li>
                <li class="reveal"><span class="strip__num">05</span>
                    <p class="step__label">Понуда</p>
                </li>
            </ol>

            <div class="section__cta reveal">
                <a href="#contact" class="btn btn--brand">Закажи бесплатно демо</a>
            </div>
        </div>
    </section>

    {{-- ======================= USAGE / PURCHASE vs RENT =======================
    Sales-focused redesign of the "Флексибилен начин на користење" block only.
    Two equally weighted option panels (Купување / Изнајмување), each with a
    CenoBots robot cutout, benefits, a one-line verdict and its own
    "Побај понуда" CTA. Everything is namespaced .um-* so no other section,
    product card, the slider, typography or global spacing is affected. --}}
    @php
    $checkIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M20 6 9 17l-5-5" />
    </svg>';
    $optionImg = 'assets/images/cenobots/opt/';
    @endphp
    <section class="section" id="usage">
        <div class="container">
            <div class="section__head um-head reveal">
                <p class="eyebrow">Начин на користење</p>
                <h2>Изберете како ќе го користите роботот</h2>
                <p>Два начина на набавка со една иста локална поддршка. Изберете го моделот што најдобро одговара на
                    вашиот објект, буџет и оперативен план.</p>
            </div>

            <div class="um-grid">
                {{-- ── Option 1 · Купување ────────────────────────────── --}}
                <article class="um-card um-card--buy reveal">
                    <div class="um-card__media">
                        <span class="um-card__index">01</span>
                        <span class="um-card__flag">Сопственост</span>
                        <img class="um-card__shot" src="{{ asset($optionImg . 'l3-01-1400.webp') }}"
                            alt="CenoBots L3 автономен робот за чистење" width="1400" height="788" loading="lazy">
                    </div>

                    <div class="um-card__body">
                        <h3 class="um-card__title">Купување</h3>
                        <p class="um-card__tagline">Ваш робот. Ваша инвестиција.</p>
                        <p class="um-card__text">Долгорочно решение за компании кои сакаат сопствена опрема и целосна
                            контрола врз роботизираното чистење.</p>

                        <ul class="um-list">
                            <li>{!! $checkIcon !!}<span>Роботот е сопственост на вашата компанија</span></li>
                            <li>{!! $checkIcon !!}<span>Инвестиција што останува во вашиот објект</span></li>
                            <li>{!! $checkIcon !!}<span>Целосна контрола над распоредот и задачите</span></li>
                        </ul>

                        <p class="um-card__verdict">Сопственост и долгорочна инвестиција</p>

                        <a href="#contact" class="btn btn--brand um-card__cta">Побај понуда {!! $arrowIcon !!}</a>
                    </div>
                </article>

                {{-- ── Option 2 · Изнајмување ─────────────────────────── --}}
                <article class="um-card um-card--rent reveal">
                    <div class="um-card__media">
                        <span class="um-card__index">02</span>
                        <span class="um-card__flag">Флексибилност</span>
                        <img class="um-card__shot" src="{{ asset($optionImg . 'sp50-01-1400.webp') }}"
                            alt="CenoBots SP50 автономен робот за чистење" width="1400" height="787" loading="lazy">
                    </div>

                    <div class="um-card__body">
                        <h3 class="um-card__title">Изнајмување</h3>
                        <p class="um-card__tagline">Флексибилно решение без голема почетна инвестиција.</p>
                        <p class="um-card__text">Флексибилен пристап за компании кои сакаат да ја користат роботската
                            технологија без директна набавка на опремата.</p>

                        <ul class="um-list">
                            <li>{!! $checkIcon !!}<span>Без директна набавка на опремата</span></li>
                            <li>{!! $checkIcon !!}<span>Мал почетен трошок наместо капитална инвестиција</span></li>
                            <li>{!! $checkIcon !!}<span>Пристап до роботизација според потребите на објектот</span></li>
                        </ul>

                        <p class="um-card__verdict">Флексибилност и помал почетен трошок</p>

                        <a href="#contact" class="btn btn--brand um-card__cta">Побај понуда {!! $arrowIcon !!}</a>
                    </div>
                </article>
            </div>

            <div class="um-note reveal">
                <p class="um-note__text">И двата модела се со локален сервис, обука и поддршка од IT Plus Smart.</p>
                <a class="um-note__link" href="#contact">Побарајте предлог за вашиот објект {!! $arrowIcon !!}</a>
            </div>
        </div>
    </section>

    {{-- ======================= PROJECTS ======================= --}}
    <section class="section section--surface" id="projects">
        <div class="container">
            <div class="section__head reveal">
                <p class="eyebrow">Проекти</p>
                <h2>CenoBots во реални објекти</h2>
                <p>Практични примени на автономното чистење во комерцијални и деловни простори.</p>
            </div>

            @php
                // Projects come from Admin → CenoBots Projects. Until the first one is
                // saved there, the original three projects are shown as plain cards.
                $fallbackPhoto = asset(config('cenobots.projects.fallback_image'));
            @endphp

            @if (!empty($projects) && $projects->count())
            <div class="cb-projects reveal" data-cb-projects data-interval="6000" aria-roledescription="carousel" aria-label="Проекти">
                <div class="cb-projects__frame">
                    <button type="button" class="cb-projects__arrow cb-projects__arrow--prev" data-cb-prev aria-label="Претходен проект">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6" /></svg>
                    </button>

                    <div class="cb-projects__viewport" data-cb-viewport tabindex="0" aria-live="off" aria-label="Листај ги проектите со стрелките на тастатурата">
                        <ul class="cb-projects__track" data-cb-track>
                            @foreach ($projects as $project)
                                @php
                                    $photoUrl = $project->photo ? $project->photo->getUrl() : $fallbackPhoto;
                                    $logoUrl = $project->logo?->getUrl();
                                @endphp
                                <li class="cb-projects__slide" role="group" aria-roledescription="slide" aria-label="{{ $loop->iteration }} од {{ $loop->count }}">
                                    <article class="cb-project">
                                        <div class="cb-project__media">
                                            <img src="{{ $photoUrl }}" alt="{{ $project->photo ? ($project->photo->getCustomProperty('alt_text') ?: $project->name) : $project->name . ' – CenoBots' }}" loading="lazy" draggable="false">
                                            @if ($logoUrl)
                                                <span class="cb-project__logo">
                                                    <img src="{{ $logoUrl }}" alt="{{ $project->logo_alt ?: $project->name }}" loading="lazy" draggable="false">
                                                </span>
                                            @endif
                                        </div>
                                        <div class="cb-project__body">
                                            @if ($project->category)<p class="eyebrow">{{ $project->category }}</p>@endif
                                            <h3 class="card__heading card__heading--sm">{{ $project->name }}</h3>
                                            @if ($project->description)<p class="card__body">{{ $project->description }}</p>@endif
                                            @if ($project->foot_text)<p class="card__foot">{{ $project->foot_text }}</p>@endif
                                            @if ($project->website_url)
                                                <a class="cb-project__link" href="{{ $project->website_url }}" target="_blank" rel="noopener noreferrer">Посети веб-страница {!! $arrowIcon !!}</a>
                                            @endif
                                        </div>
                                    </article>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <button type="button" class="cb-projects__arrow cb-projects__arrow--next" data-cb-next aria-label="Следен проект">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6" /></svg>
                    </button>
                </div>

                <div class="cb-projects__controls">
                    <div class="cb-projects__dots" data-cb-dots role="group" aria-label="Позиција на слајдерот"></div>
                    <button type="button" class="cb-projects__toggle" data-cb-toggle aria-pressed="false" aria-label="Паузирај автоматско листање">
                        <svg class="cb-projects__icon-pause" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><rect x="6" y="5" width="4" height="14" rx="1" /><rect x="14" y="5" width="4" height="14" rx="1" /></svg>
                        <svg class="cb-projects__icon-play" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.5v13a1 1 0 0 0 1.5.86l10.5-6.5a1 1 0 0 0 0-1.72L9.5 4.64A1 1 0 0 0 8 5.5Z" /></svg>
                    </button>
                </div>
            </div>
            @else
            <div class="section__body grid grid--3">
                @foreach (config('cenobots.projects.items') as [$category, $name, $description])
                    <article class="card card--pad reveal">
                        <p class="eyebrow">{{ $category }}</p>
                        <h3 class="card__heading card__heading--sm">{{ $name }}</h3>
                        <p class="card__body">{{ $description }}</p>
                        <p class="card__foot">CenoBots автономно чистење</p>
                    </article>
                @endforeach
            </div>
            @endif

        </div>
    </section>

    {{-- ======================= SERVICE & SUPPORT ======================= --}}
    <section class="section" id="service">
        <div class="container">
            <div class="split">
                <div class="reveal">
                    <div class="section__head">
                        <p class="eyebrow">Сервис и поддршка</p>
                        <h2>Не продаваме само робот. Градиме решение.</h2>
                        <p>IT Plus Smart обезбедува локална поддршка во целиот процес – од анализа на објектот и избор
                            на модел, преку инсталација, мапирање и обука на оператори, до сервис, резервни делови и
                            потрошен материјал.</p>
                    </div>
                    <div class="split__actions">
                        <a href="#service" class="btn btn--ink">Наши услуги</a>
                        <a href="#contact" class="btn btn--outline">Контактирај нè</a>
                    </div>
                </div>

                <img class="split__media reveal" src="{{ asset($cennoImg . 'product-l3-BOvsIQaE.webp') }}"
                    alt="Техничар на IT Plus Smart сервисира CenoBots робот" width="1600" height="1008" loading="lazy">
            </div>
        </div>
    </section>

    {{-- ======================= CTA BAND ======================= --}}
    <section class="cta-band" id="contact">
        <div class="container cta-band__inner">
            <div class="reveal">
                <h2>Разговарајте со нашиот тим</h2>
                <p>+389 (0) 2 000 000 &nbsp;·&nbsp; info@itplussmart.mk &nbsp;·&nbsp; Скопје, Северна Македонија</p>
            </div>
            <a href="#contact" class="btn btn--brand reveal">Побарај бесплатен демо</a>
        </div>
    </section>
</main>

{{-- ======================= FOOTER ======================= --}}
@include('pages.cennobots.partials._footer', ['base' => '', 'active' => 'home'])

@endsection