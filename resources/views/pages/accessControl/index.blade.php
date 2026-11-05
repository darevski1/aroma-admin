@extends('layouts.app')


@include('partials.page-seo', ['seoPage' => $page ?? null, 'seoTitle' => 'ITPlus | Видео надзор и контрола на пристап', 'seoDescription' => 'Безбедносни решенија од ITPlus: CCTV видео надзор, контрола на пристап, алармни системи, дојава на пожар, видео интерфони и интегрирани системи за мониторинг.'])
@section('content')
@php
    $s = $page->settings ?? [];
    $contactUrl = route('kontakt');
    $phone = '+389 71 354 822';

    $services = [
        [
            'id' => 'cctv',
            'title' => 'CCTV и видео надзор',
            'text' => 'IP камери со Full HD и 4K резолуција, ноќен режим и паметна детекција на движење, со снимање на NVR/DVR и далечински пристап.',
            'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
            'items' => ['IP и аналогни камери (Full HD / 4K)', 'NVR/DVR снимање и складирање', 'Ноќно гледање и детекција на движење', 'Преглед од мобилен, таблет и компјутер'],
        ],
        [
            'id' => 'pristap',
            'title' => 'Контрола на пристап',
            'text' => 'Контролирајте кој, кога и каде влегува: картички, PIN, отпечаток или лице, со целосна евиденција на влезови и излези.',
            'icon' => 'M12 11c0-1.1.9-2 2-2s2 .9 2 2v1m-4-1a2 2 0 10-4 0v1m-2 0h12a1 1 0 011 1v6a1 1 0 01-1 1H6a1 1 0 01-1-1v-6a1 1 0 011-1zm6-5V5a3 3 0 00-6 0',
            'items' => ['Читачи на картички, PIN и биометрија', 'Електромагнетни брави и турникети', 'Права на пристап по лице и време', 'Извештаи за влезови и евиденција на присуство'],
        ],
        [
            'id' => 'alarm',
            'title' => 'Алармни системи и детекција на упад',
            'text' => 'Сензори за движење, магнетни контакти и сирени што веднаш ве известуваат за секој неовластен обид за влез.',
            'icon' => 'M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2a2 2 0 01-.6 1.4L4 17h5m6 0a3 3 0 11-6 0',
            'items' => ['PIR сензори и магнетни контакти', 'Сирени и известување на телефон', 'Зони, кодови и далечинско вклучување', 'Приклучување на центар за мониторинг'],
        ],
        [
            'id' => 'pozar',
            'title' => 'Дојава и алармирање на пожар',
            'text' => 'Адресибилни и конвенционални системи за рано откривање на чад и пламен, во согласност со прописите за заштита од пожар.',
            'icon' => 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657zM9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z',
            'items' => ['Оптички и термички детектори', 'Алармни централи и рачни јавувачи', 'Звучна и светлосна сигнализација', 'Проект, монтажа и редовно сервисирање'],
        ],
        [
            'id' => 'interfon',
            'title' => 'Видео интерфон и контрола на влез',
            'text' => 'Видете и разговарајте со посетителот пред да отворите: за домови, згради, канцеларии и влезови на објекти.',
            'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
            'items' => ['Видео и аудио интерфони', 'Далечинско отворање на врата и капии', 'Повеќе станови/простории на еден влез', 'Поврзување со мобилна апликација'],
        ],
        [
            'id' => 'integracija',
            'title' => 'Интегрирани безбедносни системи',
            'text' => 'Видео, пристап, аларм и пожар во едно решение со централен мониторинг, автоматски реакции и единствен преглед.',
            'icon' => 'M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z',
            'items' => ['Единствена платформа за сите системи', 'Автоматски сценарија (упад → снимка → известување)', '24/7 мониторинг и далечински пристап', 'Скалабилно: од дом до големи објекти'],
        ],
    ];

    $benefits = [
        ['title' => 'Заштита 24/7', 'text' => 'Постојан надзор и моментално известување за секој настан.'],
        ['title' => 'Далечински пристап', 'text' => 'Прегледајте го објектот од мобилен, таблет или компјутер.'],
        ['title' => 'Современа опрема', 'text' => 'Сертифицирани производи од проверени светски производители.'],
        ['title' => 'Професионална монтажа', 'text' => 'Проект, инсталација, конфигурација и обука на корисници.'],
        ['title' => 'Локален сервис', 'text' => 'Брза техничка поддршка и редовно одржување.'],
        ['title' => 'Прилагодено решение', 'text' => 'Систем димензиониран според вашиот објект и буџет.'],
    ];

    $steps = [
        ['title' => 'Увид и консултација', 'text' => 'Го разгледуваме објектот и ги дефинираме ризиците и потребите.'],
        ['title' => 'Проект и понуда', 'text' => 'Предлог на опрема, распоред на уреди и јасна понуда.'],
        ['title' => 'Инсталација', 'text' => 'Уредна монтажа, каблирање и конфигурација од наши техничари.'],
        ['title' => 'Обука и поддршка', 'text' => 'Ве обучуваме и го одржуваме системот и по пуштање во употреба.'],
    ];

    $sectors = [
        ['img' => 'retail.jpg', 'title' => 'Продавници и центри'],
        ['img' => 'hotel.jpg', 'title' => 'Хотели и угостителство'],
        ['img' => 'hospital.jpg', 'title' => 'Болници и ординации'],
        ['img' => 'education.jpg', 'title' => 'Образование'],
    ];

    $heroImg = $s['ai_background_image'] ?? asset('assets/images/cctv-hero.jpg');
@endphp

<div class="sec">
    {{-- ============ HERO ============ --}}
    <section class="sec-hero">
        <img class="sec-hero__bg" src="{{ $heroImg }}" alt="" fetchpriority="high">
        <div class="sec-hero__veil"></div>
        <div class="sec-wrap sec-hero__inner">
            <span class="sec-eyebrow sec-eyebrow--dark">{{ $s['eyebrow'] ?? 'Безбедносни решенија' }}</span>
            <h1 class="sec-hero__title">{!! nl2br(e($s['heading'] ?? "Видео надзор и контрола\nна пристап за секој објект")) !!}</h1>
            <p class="sec-hero__lede">{!! nl2br(e($s['description'] ?? 'Проектираме, монтираме и одржуваме комплетни безбедносни системи: од камери и контрола на пристап до аларм, дојава на пожар и интегриран мониторинг.')) !!}</p>
            <div class="sec-hero__actions">
                <a href="{{ $contactUrl }}" class="sec-btn sec-btn--primary">{{ $s['cta_button_text'] ?? 'Побарај понуда' }}</a>
                <a href="#uslugi" class="sec-btn sec-btn--ghost">Погледни ги услугите</a>
            </div>
            <ul class="sec-hero__stats">
                <li><strong>6</strong><span>безбедносни системи</span></li>
                <li><strong>24/7</strong><span>надзор и поддршка</span></li>
                <li><strong>1</strong><span>партнер за сè</span></li>
            </ul>
        </div>
    </section>

    {{-- ============ SERVICES ============ --}}
    <section class="sec-section" id="uslugi">
        <div class="sec-wrap">
            <div class="sec-head">
                <span class="sec-eyebrow">Услуги</span>
                <h2>Комплетна безбедност на едно место</h2>
                <p>Шест решенија што можете да ги комбинирате во еден систем прилагоден на вашиот објект.</p>
            </div>

            <div class="sec-grid">
                @foreach ($services as $service)
                    <article class="sec-card" id="{{ $service['id'] }}">
                        <div class="sec-card__icon">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $service['icon'] }}" /></svg>
                        </div>
                        <h3>{{ $service['title'] }}</h3>
                        <p>{{ $service['text'] }}</p>
                        <ul>
                            @foreach ($service['items'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                        <a href="{{ $contactUrl }}" class="sec-card__link">Побарај понуда <span aria-hidden="true">→</span></a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ BENEFITS ============ --}}
    <section class="sec-section sec-section--dark">
        <div class="sec-wrap">
            <div class="sec-head sec-head--light">
                <span class="sec-eyebrow sec-eyebrow--dark">Предности</span>
                <h2>Зошто ITPlus за вашата безбедност</h2>
            </div>
            <div class="sec-benefits">
                @foreach ($benefits as $b)
                    <div class="sec-benefit">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 13l4 4L19 7" /></svg>
                        <div>
                            <h3>{{ $b['title'] }}</h3>
                            <p>{{ $b['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ SECTORS ============ --}}
    <section class="sec-section">
        <div class="sec-wrap">
            <div class="sec-head">
                <span class="sec-eyebrow">Примена</span>
                <h2>Решенија за различни објекти</h2>
                <p>Од домови и канцеларии до хотели, болници и образовни институции.</p>
            </div>
            <div class="sec-sectors">
                @foreach ($sectors as $sector)
                    <figure class="sec-sector">
                        <img src="{{ asset('assets/images/' . $sector['img']) }}" alt="{{ $sector['title'] }}" loading="lazy">
                        <figcaption>{{ $sector['title'] }}</figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ PROCESS ============ --}}
    <section class="sec-section sec-section--soft">
        <div class="sec-wrap">
            <div class="sec-head">
                <span class="sec-eyebrow">Како работиме</span>
                <h2>Од увид до работен систем</h2>
            </div>
            <ol class="sec-steps">
                @foreach ($steps as $step)
                    <li class="sec-step">
                        <h3>{{ $step['title'] }}</h3>
                        <p>{{ $step['text'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="sec-cta" id="kontakt">
        <img class="sec-cta__bg" src="{{ $heroImg }}" alt="" loading="lazy">
        <div class="sec-cta__veil"></div>
        <div class="sec-wrap sec-cta__inner">
            <h2>{{ $s['cta_heading'] ?? 'Обезбедете го вашиот простор денес' }}</h2>
            <p>Побарајте бесплатна консултација и понуда за системот што ви одговара.</p>
            <div class="sec-hero__actions" style="justify-content:center">
                <a href="{{ $contactUrl }}" class="sec-btn sec-btn--primary">Побарај понуда</a>
                <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="sec-btn sec-btn--ghost">Повикај {{ $phone }}</a>
            </div>
        </div>
    </section>
</div>

<style>
    .sec { --sec-accent: #e04534; --sec-accent-strong: #ce3b2a; --sec-ink: #111827; --sec-muted: #5b6573; --sec-line: #e5e8ee; --sec-soft: #f4f6f9; --sec-navy: #0a101c; color: var(--sec-ink); }
    .sec a:focus-visible { outline: 3px solid var(--sec-accent); outline-offset: 3px; }
    .sec-wrap { width: 100%; max-width: 76rem; margin: 0 auto; padding: 0 1.5rem; }
    .sec-eyebrow { display: inline-flex; align-items: center; gap: .5rem; padding: .45rem 1rem; border-radius: 9999px; background: var(--sec-soft); color: var(--sec-accent-strong); font-weight: 600; font-size: .9rem; }
    .sec-eyebrow::before { content: ""; width: .5rem; height: .5rem; border-radius: 9999px; background: var(--sec-accent); }
    .sec-eyebrow--dark { background: rgba(255,255,255,.12); color: #fff; }

    .sec-btn { display: inline-flex; align-items: center; justify-content: center; padding: .95rem 1.8rem; border-radius: 1rem; font-weight: 600; text-decoration: none; transition: background .2s, color .2s, transform .2s; }
    .sec-btn--primary { background: var(--sec-accent-strong); color: #fff; box-shadow: 0 10px 28px -12px rgba(224,69,52,.9); }
    .sec-btn--primary:hover { background: #b93223; transform: translateY(-1px); }
    .sec-btn--ghost { border: 1px solid rgba(255,255,255,.45); color: #fff; }
    .sec-btn--ghost:hover { background: #fff; color: var(--sec-ink); }

    /* hero */
    .sec-hero { position: relative; isolation: isolate; overflow: hidden; background: var(--sec-navy); color: #fff; }
    .sec-hero__bg, .sec-cta__bg { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: -2; }
    .sec-hero__veil { position: absolute; inset: 0; z-index: -1; background: linear-gradient(90deg, rgba(5,10,20,.88) 0%, rgba(5,10,20,.62) 60%, rgba(5,10,20,.35) 100%); }
    .sec-hero__inner { padding-top: clamp(6rem, 12vw, 9rem); padding-bottom: clamp(4rem, 8vw, 6rem); }
    .sec-hero__title { margin-top: 1.25rem; max-width: 46rem; font-size: clamp(2.1rem, 5.5vw, 4.25rem); line-height: 1.08; font-weight: 600; color: #fff; }
    .sec-hero__lede { margin-top: 1.4rem; max-width: 38rem; font-size: clamp(1rem, 2vw, 1.2rem); line-height: 1.7; color: rgba(255,255,255,.85); }
    .sec-hero__actions { display: flex; flex-wrap: wrap; gap: 1rem; margin-top: 2rem; }
    .sec-hero__stats { display: flex; flex-wrap: wrap; gap: 1.5rem 3rem; margin-top: clamp(2.5rem, 6vw, 4rem); padding-top: 1.75rem; border-top: 1px solid rgba(255,255,255,.2); list-style: none; padding-left: 0; }
    .sec-hero__stats strong { display: block; font-size: 2rem; font-weight: 600; line-height: 1.1; }
    .sec-hero__stats span { font-size: .95rem; color: rgba(255,255,255,.7); }

    /* sections */
    .sec-section { padding: clamp(3.5rem, 8vw, 6.5rem) 0; background: #fff; }
    .sec-section--soft { background: var(--sec-soft); }
    .sec-section--dark { background: var(--sec-navy); color: #fff; }
    .sec-head { max-width: 42rem; margin: 0 auto clamp(2.2rem, 5vw, 3.5rem); text-align: center; }
    .sec-head h2 { margin-top: 1rem; font-size: clamp(1.8rem, 4vw, 2.9rem); line-height: 1.15; font-weight: 600; }
    .sec-head p { margin-top: 1rem; font-size: 1.08rem; line-height: 1.7; color: var(--sec-muted); }
    .sec-head--light h2 { color: #fff; }

    /* service cards */
    .sec-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
    .sec-card { display: flex; flex-direction: column; padding: 2rem; border: 1px solid var(--sec-line); border-radius: 1.25rem; background: #fff; transition: box-shadow .25s, transform .25s, border-color .25s; scroll-margin-top: 6rem; }
    .sec-card:hover { box-shadow: 0 24px 50px -28px rgba(10,16,28,.45); transform: translateY(-3px); border-color: #d6dbe4; }
    .sec-card__icon { display: grid; place-items: center; width: 3.4rem; height: 3.4rem; border-radius: 1rem; background: #fdecea; color: var(--sec-accent-strong); }
    .sec-card h3 { margin-top: 1.25rem; font-size: 1.3rem; font-weight: 600; line-height: 1.3; }
    .sec-card p { margin-top: .7rem; line-height: 1.65; color: var(--sec-muted); }
    .sec-card ul { margin: 1.25rem 0 0; padding: 1.25rem 0 0; border-top: 1px solid var(--sec-line); list-style: none; display: grid; gap: .6rem; flex: 1; align-content: start; }
    .sec-card li { position: relative; padding-left: 1.5rem; font-size: .95rem; line-height: 1.5; }
    .sec-card li::before { content: ""; position: absolute; left: .15rem; top: .4rem; width: .6rem; height: .35rem; border-left: 2px solid var(--sec-accent-strong); border-bottom: 2px solid var(--sec-accent-strong); transform: rotate(-45deg); }
    .sec-card__link { margin-top: 1.5rem; font-weight: 600; color: var(--sec-accent-strong); text-decoration: none; }
    .sec-card__link:hover { text-decoration: underline; }

    /* benefits */
    .sec-benefits { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; }
    .sec-benefit { display: flex; gap: 1rem; padding: 1.5rem; border: 1px solid rgba(255,255,255,.12); border-radius: 1.1rem; background: rgba(255,255,255,.04); }
    .sec-benefit svg { flex: none; margin-top: .2rem; color: #ff6b5a; }
    .sec-benefit h3 { font-size: 1.1rem; font-weight: 600; }
    .sec-benefit p { margin-top: .4rem; line-height: 1.6; font-size: .95rem; color: rgba(255,255,255,.7); }

    /* sectors */
    .sec-sectors { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; }
    .sec-sector { position: relative; margin: 0; overflow: hidden; border-radius: 1.25rem; aspect-ratio: 3 / 4; background: var(--sec-navy); }
    .sec-sector img { width: 100%; height: 100%; object-fit: cover; transition: transform .6s ease; }
    .sec-sector:hover img { transform: scale(1.05); }
    .sec-sector::after { content: ""; position: absolute; inset: 0; background: linear-gradient(0deg, rgba(5,10,20,.8) 0%, transparent 55%); }
    .sec-sector figcaption { position: absolute; z-index: 1; left: 1.25rem; right: 1.25rem; bottom: 1.1rem; color: #fff; font-weight: 600; font-size: 1.1rem; }

    /* steps */
    .sec-steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; list-style: none; padding: 0; margin: 0; counter-reset: step; }
    .sec-step { padding: 1.6rem 1.5rem; border: 1px solid var(--sec-line); border-radius: 1.25rem; background: #fff; }
    .sec-step::before { counter-increment: step; content: counter(step); display: grid; place-items: center; width: 2.4rem; height: 2.4rem; border-radius: 9999px; background: #000; color: #fff; font-weight: 600; }
    .sec-step h3 { margin-top: 1.1rem; font-size: 1.2rem; font-weight: 600; }
    .sec-step p { margin-top: .5rem; line-height: 1.65; color: var(--sec-muted); font-size: .97rem; }

    /* cta */
    .sec-cta { position: relative; isolation: isolate; overflow: hidden; background: var(--sec-navy); color: #fff; text-align: center; padding: clamp(4rem, 9vw, 7rem) 0; }
    .sec-cta__veil { position: absolute; inset: 0; z-index: -1; background: rgba(5,10,20,.78); }
    .sec-cta h2 { font-size: clamp(1.9rem, 4.5vw, 3.2rem); line-height: 1.15; font-weight: 600; }
    .sec-cta p { margin: 1rem auto 0; max-width: 34rem; font-size: 1.1rem; line-height: 1.7; color: rgba(255,255,255,.82); }

    @media (max-width: 1023px) {
        .sec-grid, .sec-benefits { grid-template-columns: repeat(2, 1fr); }
        .sec-sectors, .sec-steps { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 639px) {
        .sec-grid, .sec-benefits, .sec-sectors, .sec-steps { grid-template-columns: 1fr; }
        .sec-sector { aspect-ratio: 4 / 3; }
        .sec-hero__veil { background: linear-gradient(0deg, rgba(5,10,20,.88) 0%, rgba(5,10,20,.6) 100%); }
        .sec-btn { width: 100%; }
        .sec-card { padding: 1.5rem; }
    }
    @media (prefers-reduced-motion: reduce) { .sec-card, .sec-sector img, .sec-btn { transition: none; } }
</style>
@endsection
