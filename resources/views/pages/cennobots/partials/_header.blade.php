{{--
    Shared CenoBots header + mobile navigation.

    Extracted verbatim from pages/cennobots/index.blade.php so every CenoBots
    page renders byte-identical markup. The only behaviour change is the
    "Модели" item, which now points at the models overview page.

    The desktop bar and the mobile sheet keep their original item sets and
    order exactly as they were (desktop has no "CenoBots" item).

    @var string $base   URL prefix for the in-page anchors. Empty string on the
                        CenoBots landing page (so they stay "#top", "#solutions"
                        …), and route('cennobots') on the model pages, where those
                        sections live on the landing page.
    @var string $active Which nav item is highlighted: 'home' or 'models'.
--}}
@php
    // Desktop bar — unchanged item set and order.
    $desktopNav = [
        ['label' => 'Почетна', 'href' => $base . '#top', 'key' => 'home'],
        ['label' => 'За нас', 'href' => $base . '#contact', 'key' => 'about'],
        ['label' => 'Модели', 'href' => route('cennobots.models'), 'key' => 'models'],
        ['label' => 'Решенија', 'href' => $base . '#solutions', 'key' => 'solutions'],
        ['label' => 'Услуги', 'href' => $base . '#service', 'key' => 'service'],
        ['label' => 'Проекти', 'href' => $base . '#projects', 'key' => 'projects'],
        ['label' => 'Контакт', 'href' => $base . '#contact', 'key' => 'contact'],
    ];

    // Mobile sheet — unchanged item set and order.
    $mobileNav = [
        ['label' => 'Почетна', 'href' => $base . '#top', 'key' => 'home'],
        ['label' => 'CenoBots', 'href' => $base . '#models', 'key' => 'cennobots'],
        ['label' => 'Модели', 'href' => route('cennobots.models'), 'key' => 'models'],
        ['label' => 'Решенија', 'href' => $base . '#solutions', 'key' => 'solutions'],
        ['label' => 'Услуги', 'href' => $base . '#service', 'key' => 'service'],
        ['label' => 'Проекти', 'href' => $base . '#projects', 'key' => 'projects'],
        ['label' => 'За нас', 'href' => $base . '#contact', 'key' => 'about'],
        ['label' => 'Контакт', 'href' => $base . '#contact', 'key' => 'contact'],
    ];
@endphp

<header class="site-header" id="siteHeader">
    <div class="container site-header__inner">

        <img src="{{ asset('assets/images/logo/logocenotbot.png') }}"
            alt="CenoBots автономен робот за чистење во комерцијален објект" width="1920" height="1088"
            fetchpriority="high" style="width: 200px; height: auto;">
        <nav class="nav" aria-label="Главна навигација">
            @foreach ($desktopNav as $item)
                <a href="{{ $item['href'] }}"{!! $item['key'] === $active ? ' class="is-active"' : '' !!}>{{ $item['label'] }}</a>
            @endforeach
        </nav>

        <div class="site-header__actions">
            <a href="{{ $base . '#contact' }}" class="btn btn--brand btn--sm">Побарај бесплатен демо</a>
            <button class="nav-toggle" id="navToggle" aria-label="Отвори мени" aria-expanded="false"
                aria-controls="mobileNav">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    aria-hidden="true">
                    <path d="M4 5h16" />
                    <path d="M4 12h16" />
                    <path d="M4 19h16" />
                </svg>
            </button>
        </div>
    </div>
</header>

<div class="mobile-nav" id="mobileNav" aria-label="Мобилна навигација">
    @foreach ($mobileNav as $item)
        <a href="{{ $item['href'] }}"{!! $item['key'] === $active ? ' class="is-active"' : '' !!}>{{ $item['label'] }}</a>
    @endforeach
    <a href="{{ $base . '#contact' }}" class="btn btn--brand">Побарај бесплатен демо</a>
</div>