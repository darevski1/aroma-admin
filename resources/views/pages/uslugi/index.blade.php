@extends('layouts.app')


@include('partials.page-seo', ['seoPage' => $page ?? null, 'seoTitle' => 'ITPlus | Услуги', 'seoDescription' => 'Професионални ИТ услуги од ITPlus: мрежни инсталации, компјутерско одржување, видео надзор, контрола на пристап и принтери.'])

@section('content')
@php
    $s = $page->settings ?? [];
    $services = [
        [
            'title' => $s['service_1_title'] ?? 'Мрежни инсталации и одржување на мрежи',
            'description' => $s['service_1_description'] ?? 'Комплетни решенија за LAN/Wi-Fi мрежи, конфигурација на рутери, switch уреди и стабилна мрежна инфраструктура.',
            'href' => '/uslugi/mrezni-instalacii-i-odrzuvanje-na-mrezi',
            'label' => 'Инфраструктура',
            'icon' => 'network',
        ],
        [
            'title' => $s['service_2_title'] ?? 'Одржување на компјутерски системи',
            'description' => $s['service_2_description'] ?? 'Редовно сервисирање, оптимизација и техничка поддршка за компјутери, сервери и деловни ИТ системи.',
            'href' => '/uslugi/odrzuvanje-na-kompjuterski-sistemi',
            'label' => 'ИТ поддршка',
            'icon' => 'support',
        ],
        [
            'title' => $s['service_3_title'] ?? 'Видео надзор и контрола на пристап',
            'description' => $s['service_3_description'] ?? 'Инсталација на CCTV системи, IP камери и безбедносни решенија за професионална заштита на домови и компании.',
            'href' => '/uslugi/video-nadzor-i-kontrola-na-pristap',
            'label' => 'Безбедност',
            'icon' => 'security',
        ],
        [
            'title' => $s['service_4_title'] ?? 'Сервис и продажба на принтери',
            'description' => $s['service_4_description'] ?? 'Продажба, сервисирање и одржување на ласерски и inkjet принтери, тонери и резервни делови.',
            'href' => '/uslugi/servis-i-prodazba-na-printeri',
            'label' => 'Печатење',
            'icon' => 'printer',
        ],
    ];
@endphp

<main class="services-page">
  <section class="services-hero" aria-labelledby="services-hero-title">
    <div class="services-shell services-hero__grid">
      <div class="services-hero__content">
        <span class="services-eyebrow">{{ $s['hero_eyebrow'] ?? 'Професионални ИТ услуги' }}</span>
        <h1 id="services-hero-title">{{ $s['hero_heading'] ?? 'Стабилна технологија за сигурен бизнис.' }}</h1>
        <p>
          {{ $s['hero_description'] ?? 'Нудиме професионални ИТ решенија за компании и физички лица, со брза поддршка, сигурност и современа технологија прилагодена на вашата работа.' }}
        </p>
        <div class="services-hero__actions">
          <a href="#services-grid" class="services-btn services-btn--primary">Погледни услуги</a>
          <a href="/kontakt" class="services-btn services-btn--ghost">Побарај понуда</a>
        </div>
      </div>

      <div class="services-hero__panel" aria-label="ITPlus service highlights">
        <div class="services-hero__panel-top">
          <span>ITPlus Care</span>
          <strong>360°</strong>
          <p>Поддршка за инфраструктура, безбедност, уреди и секојдневно работење.</p>
        </div>
        <div class="services-hero__mini-grid">
          <div>
            <strong>24/7</strong>
            <span>поддршка</span>
          </div>
          <div>
            <strong>4</strong>
            <span>клучни услуги</span>
          </div>
          <div>
            <strong>Pro</strong>
            <span>пристап</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="services-overview" id="services-grid" aria-labelledby="services-grid-title">
    <div class="services-shell">
      <div class="services-section-head">
        <span class="services-eyebrow">{{ $s['overview_eyebrow'] ?? 'Што покриваме' }}</span>
        <h2 id="services-grid-title">{{ $s['overview_heading'] ?? 'Услуги организирани околу реалните потреби на вашиот бизнис.' }}</h2>
        <p>
          {{ $s['overview_paragraph'] ?? 'Од мрежа и опрема до безбедност и печатење, секоја услуга е поставена за стабилност, брза реакција и долгорочна вредност.' }}
        </p>
      </div>

      <div class="services-grid">
        @foreach ($services as $service)
          <article class="services-card">
            <div class="services-card__top">
              <div class="services-card__icon" aria-hidden="true">
                @if ($service['icon'] === 'network')
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M4 7h16M7 12h10M9 17h6" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                @elseif ($service['icon'] === 'support')
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M5 12a7 7 0 0114 0v3a3 3 0 01-3 3h-2" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M6 15H5a2 2 0 01-2-2v-1a2 2 0 012-2h1m12 5h1a2 2 0 002-2v-1a2 2 0 00-2-2h-1" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                @elseif ($service['icon'] === 'security')
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 3l7 3v5c0 4.4-2.8 8.4-7 10-4.2-1.6-7-5.6-7-10V6l7-3z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M9.5 12l1.8 1.8 3.5-4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                @else
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M7 8V4h10v4M6 17H5a2 2 0 01-2-2v-3a3 3 0 013-3h12a3 3 0 013 3v3a2 2 0 01-2 2h-1" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M7 14h10v6H7z" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                @endif
              </div>
              <span>{{ $service['label'] }}</span>
            </div>
            <h3>{{ $service['title'] }}</h3>
            <p>{{ $service['description'] }}</p>
            <a href="{{ $service['href'] }}" class="services-card__link">
              Дознај повеќе
              <span aria-hidden="true">→</span>
            </a>
          </article>
        @endforeach
      </div>
    </div>
  </section>

  <section class="services-process" aria-labelledby="services-process-title">
    <div class="services-shell services-process__grid">
      <div>
        <span class="services-eyebrow">{{ $s['process_eyebrow'] ?? 'Како работиме' }}</span>
        <h2 id="services-process-title">{{ $s['process_heading'] ?? 'Јасен процес, брза реализација и поддршка после имплементација.' }}</h2>
      </div>
      <div class="services-process__steps">
        <div><strong>01</strong><span>{{ $s['process_step_1'] ?? 'Анализа на потреби и постоечка инфраструктура.' }}</span></div>
        <div><strong>02</strong><span>{{ $s['process_step_2'] ?? 'Предлог решение со јасен обем, рокови и приоритети.' }}</span></div>
        <div><strong>03</strong><span>{{ $s['process_step_3'] ?? 'Имплементација, тестирање и континуирана техничка поддршка.' }}</span></div>
      </div>
    </div>
  </section>

  <section class="services-cta" aria-label="Контакт за ИТ услуги">
    <div class="services-shell">
      <div class="services-cta__box">
        <div>
          <span class="services-eyebrow">Подготвени за подобра ИТ основа?</span>
          <h2>Да направиме систем што работи тивко, стабилно и сигурно.</h2>
        </div>
        <a href="/kontakt" class="services-btn services-btn--primary">Контактирај не</a>
      </div>
    </div>
  </section>
</main>
@endsection
