{{--
    Shared CenoBots footer.

    Extracted verbatim from pages/cennobots/index.blade.php. The footer model
    list now links to each model's own page instead of the landing page
    anchor; everything else is unchanged.

    @var string $base   Same meaning as in _header.blade.php.
    @var string $active Same meaning as in _header.blade.php.
--}}
@php
    $footerNav = [
        ['label' => 'Почетна', 'href' => $base . '#top', 'key' => 'home'],
        ['label' => 'CenoBots', 'href' => $base . '#models', 'key' => 'cennobots'],
        ['label' => 'Модели', 'href' => route('cennobots.models'), 'key' => 'models'],
        ['label' => 'Решенија', 'href' => $base . '#solutions', 'key' => 'solutions'],
        ['label' => 'Услуги', 'href' => $base . '#service', 'key' => 'service'],
        ['label' => 'Проекти', 'href' => $base . '#projects', 'key' => 'projects'],
        ['label' => 'За нас', 'href' => $base . '#contact', 'key' => 'about'],
        ['label' => 'Контакт', 'href' => $base . '#contact', 'key' => 'contact'],
    ];

    $footerModels = config('cenobots.models');
@endphp

<footer class="site-footer">
    <div class="container site-footer__grid">
        <div class="site-footer__brand">
            <a href="/" class="site-footer__logo" aria-label="IT Plus Smart">
                <img src="{{ asset('assets/images/logo-itplus.png') }}" alt="IT Plus - Be Smart" height="48">
            </a>
            <img class="site-footer__distributor"
                src="{{ asset('assets/pdf/cenobots/' . rawurlencode('Authorized Distributor CenoBots IT Plus.jpg')) }}"
                alt="Authorized Distributor CenoBots IT Plus" loading="lazy"
                style="display:block; margin-top:12px; max-width:220px; height:auto;">
            <p class="brand__name">Ceno<span>Bots</span> x IT Plus Smart</p>
            <p>Овластен CenoBots дистрибутер и локален сервисен партнер во Северна Македонија. Продажба, изнајмување,
                обука и сервис на автономни роботи за комерцијално и индустриско чистење.</p>
        </div>

        <div>
            <p class="eyebrow">Навигација</p>
            <ul class="footer-list">
                @foreach ($footerNav as $item)
                    <li><a href="{{ $item['href'] }}"{!! $item['key'] === $active ? ' class="is-active"' : '' !!}>{{ $item['label'] }}</a></li>
                @endforeach
            </ul>
        </div>

        <div>
            <p class="eyebrow">Модели</p>
            <ul class="footer-list">
                @foreach ($footerModels as $m)
                    <li><a href="{{ route('cennobots.model', $m['id']) }}">{{ $m['full'] }}</a></li>
                @endforeach
            </ul>

            <p class="eyebrow footer-list-gap">Контакт</p>
            <ul class="footer-list">
                <li><a href="tel:+389020000000">+389 (0) 2 000 000</a></li>
                <li><a href="mailto:info@itplussmart.mk">info@itplussmart.mk</a></li>
                <li>Скопје, Северна Македонија</li>
            </ul>
        </div>
    </div>

    <div class="site-footer__bottom">
        <div class="container site-footer__bottom-inner">
            <p>&copy; <span id="year">2026</span> IT Plus Smart. Сите права задржани.</p>
            <p>CenoBots е заштитена трговска марка на својот производител.</p>
        </div>
    </div>
</footer>