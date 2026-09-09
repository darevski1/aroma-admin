@extends('layouts.app')

@section('title', 'ITPlus | Портфолио')
@section('meta_description', 'Погледнете дел од реализираните ITPlus проекти за мрежна инфраструктура, видео надзор, сервери, принтери и контрола на пристап.')

@section('content')
@php
  $s = $page->settings ?? [];

  $defaultProjects = [
    [
      'title' => 'Инсталација на корпоративна мрежа',
      'category' => 'Мрежни Решенија',
      'filter' => 'network',
      'image' => '/images/portfolio/network.jpg',
      'alt' => 'Мрежна инфраструктура',
      'description' => 'Поставување и конфигурација на стабилна LAN/WiFi инфраструктура за деловен објект со повеќе корисници.',
      'accent' => '#2563eb',
      'featured' => true,
    ],
    [
      'title' => 'CCTV систем за деловен простор',
      'category' => 'Видео Надзор',
      'filter' => 'security',
      'image' => '/images/portfolio/cctv.jpg',
      'alt' => 'Видео надзор',
      'description' => 'Монтажа на IP камери, NVR систем и далечински пристап за целосна безбедност и контрола.',
      'accent' => '#e04534',
      'featured' => true,
    ],
    [
      'title' => 'Одржување на ИТ инфраструктура',
      'category' => 'ИТ Поддршка',
      'filter' => 'support',
      'image' => '/images/portfolio/computers.jpg',
      'alt' => 'Компјутерски системи',
      'description' => 'Редовно одржување и оптимизација на компјутерски системи и сервери за компанија со повеќе канцеларии.',
      'accent' => '#16a34a',
      'featured' => false,
    ],
    [
      'title' => 'Сервисирање на печатарски систем',
      'category' => 'Принтери',
      'filter' => 'printers',
      'image' => '/images/portfolio/printer.jpg',
      'alt' => 'Принтери',
      'description' => 'Комплетен сервис и конфигурација на мрежни принтери за канцелариска и индустриска употреба.',
      'accent' => '#ca8a04',
      'featured' => false,
    ],
    [
      'title' => 'Инсталација на сервер систем',
      'category' => 'Серверски Решенија',
      'filter' => 'servers',
      'image' => '/images/portfolio/server.jpg',
      'alt' => 'Сервери',
      'description' => 'Конфигурација и обезбедување на серверска инфраструктура со backup и remote management.',
      'accent' => '#4f46e5',
      'featured' => true,
    ],
    [
      'title' => 'Smart Access систем',
      'category' => 'Контрола на Пристап',
      'filter' => 'security',
      'image' => '/images/portfolio/access-control.jpg',
      'alt' => 'Контрола на пристап',
      'description' => 'Инсталација на систем за контрола на пристап со картички, biometric уреди и логирање на корисници.',
      'accent' => '#7c3aed',
      'featured' => false,
    ],
  ];

  $projects = $s['projects'] ?? $defaultProjects;

  $defaultFilters = [
    'all' => 'Сите',
    'network' => 'Мрежи',
    'security' => 'Безбедност',
    'support' => 'ИТ поддршка',
    'printers' => 'Принтери',
    'servers' => 'Сервери',
  ];

  $filters = $s['filters'] ?? $defaultFilters;
@endphp

<section class="portfolio-page">
  <section class="portfolio-hero">
    <div class="portfolio-hero__bg" aria-hidden="true"></div>
    <div class="portfolio-shell portfolio-hero__grid">
      <div class="portfolio-reveal">
        <span class="portfolio-eyebrow">{{ $s['hero_eyebrow'] ?? 'Портфолио' }}</span>
        <h1>{{ $s['hero_heading'] ?? 'Проекти што ја прават технологијата сигурна, брза и видлива.' }}</h1>
        <p>
          {{ $s['hero_description'] ?? 'Дел од проектите и решенијата кои успешно ги реализиравме за компании, институции и приватни клиенти.' }}
        </p>
        <div class="portfolio-hero__actions">
          <a href="#portfolio-projects" class="portfolio-btn portfolio-btn--primary">Погледни проекти</a>
          <a href="{{ route('kontakt') }}" class="portfolio-btn portfolio-btn--ghost">Побарај понуда</a>
        </div>
      </div>

      <div class="portfolio-hero__panel portfolio-reveal">
        <div class="portfolio-hero__metric">
          <strong>360°</strong>
          <span>ИТ решенија од планирање до поддршка</span>
        </div>
        <div class="portfolio-hero__preview">
          @foreach(array_slice($projects, 0, 4) as $project)
            <img src="{{ $project['image'] }}" alt="{{ $project['alt'] }}" loading="eager">
          @endforeach
        </div>
      </div>
    </div>
  </section>

  <section class="portfolio-featured" aria-labelledby="featured-projects-title">
    <div class="portfolio-shell">
      <div class="portfolio-section-head portfolio-reveal">
        <span class="portfolio-eyebrow">{{ $s['featured_eyebrow'] ?? 'Featured work' }}</span>
        <h2 id="featured-projects-title">{{ $s['featured_heading'] ?? 'Избрани реализации' }}</h2>
        <p>{{ $s['featured_paragraph'] ?? 'Поголеми инфраструктурни и безбедносни проекти со јасен фокус на стабилност, контрола и долгорочна вредност.' }}</p>
      </div>

      <div class="portfolio-featured__grid">
        @foreach(collect($projects)->where('featured', true)->values() as $index => $project)
          <article class="portfolio-featured-card portfolio-reveal {{ $index === 0 ? 'portfolio-featured-card--large' : '' }}" style="--project-accent: {{ $project['accent'] }}">
            <a href="#" class="portfolio-featured-card__media" aria-label="Погледни проект: {{ $project['title'] }}">
              <img src="{{ $project['image'] }}" alt="{{ $project['alt'] }}" loading="lazy">
            </a>
            <div class="portfolio-featured-card__content">
              <span>{{ $project['category'] }}</span>
              <h3>{{ $project['title'] }}</h3>
              <p>{{ $project['description'] }}</p>
              <a href="#" class="portfolio-link">Погледни проект</a>
            </div>
          </article>
        @endforeach
      </div>
    </div>
  </section>

  <section id="portfolio-projects" class="portfolio-projects" aria-labelledby="portfolio-projects-title">
    <div class="portfolio-shell">
      <div class="portfolio-projects__top portfolio-reveal">
        <div>
          <span class="portfolio-eyebrow">{{ $s['projects_eyebrow'] ?? 'Project library' }}</span>
          <h2 id="portfolio-projects-title">{{ $s['projects_heading'] ?? 'Сите проекти' }}</h2>
        </div>

        <div class="portfolio-filters" role="list" aria-label="Филтрирај портфолио категории">
          @foreach($filters as $key => $label)
            <button class="portfolio-filter {{ $key === 'all' ? 'is-active' : '' }}" type="button" data-filter="{{ $key }}" role="listitem">
              {{ $label }}
            </button>
          @endforeach
        </div>
      </div>

      <div class="portfolio-grid">
        @foreach($projects as $project)
          <article class="portfolio-card portfolio-reveal" data-category="{{ $project['filter'] }}" style="--project-accent: {{ $project['accent'] }}">
            <a href="#" class="portfolio-card__media" aria-label="Погледни проект: {{ $project['title'] }}">
              <img src="{{ $project['image'] }}" alt="{{ $project['alt'] }}" loading="lazy">
            </a>
            <div class="portfolio-card__body">
              <span>{{ $project['category'] }}</span>
              <h3>{{ $project['title'] }}</h3>
              <p>{{ $project['description'] }}</p>
              <a href="#" class="portfolio-card__button">Погледни проект</a>
            </div>
          </article>
        @endforeach
      </div>
    </div>
  </section>

  <section class="portfolio-stats" aria-label="Portfolio statistics">
    <div class="portfolio-shell portfolio-stats__grid">
      <div class="portfolio-stat portfolio-reveal">
        <strong data-count="{{ $s['stat_1_value'] ?? 200 }}">0</strong>
        <span>{{ $s['stat_1_label'] ?? 'Реализирани проекти' }}</span>
      </div>
      <div class="portfolio-stat portfolio-reveal">
        <strong data-count="{{ $s['stat_2_value'] ?? 100 }}">0</strong>
        <span>{{ $s['stat_2_label'] ?? 'Активни клиенти' }}</span>
      </div>
      <div class="portfolio-stat portfolio-reveal">
        <strong data-count="{{ $s['stat_3_value'] ?? 10 }}">0</strong>
        <span>{{ $s['stat_3_label'] ?? 'Години искуство' }}</span>
      </div>
      <div class="portfolio-stat portfolio-reveal">
        <strong data-count="{{ $s['stat_4_value'] ?? 24 }}">0</strong>
        <span>{{ $s['stat_4_label'] ?? 'Поддршка и сервис' }}</span>
      </div>
    </div>
  </section>

  <section class="portfolio-testimonials" aria-labelledby="portfolio-testimonials-title">
    <div class="portfolio-shell">
      <div class="portfolio-section-head portfolio-reveal">
        <span class="portfolio-eyebrow">{{ $s['testimonials_eyebrow'] ?? 'Client voice' }}</span>
        <h2 id="portfolio-testimonials-title">{{ $s['testimonials_heading'] ?? 'Што добиваат клиентите' }}</h2>
      </div>

      @php
        $defaultTestimonials = [
          ['quote' => '„Стабилна мрежа, брза имплементација и јасна комуникација во секоја фаза од проектот."', 'author' => 'Корпоративен клиент', 'tag' => 'Мрежни решенија'],
          ['quote' => '„Видео надзорот и контролата на пристап ни дадоа многу подобра прегледност и сигурност."', 'author' => 'Деловен простор', 'tag' => 'Безбедносни системи'],
          ['quote' => '„Одржувањето е организирано, навремено и без непотребни прекини во секојдневната работа."', 'author' => 'Приватна компанија', 'tag' => 'ИТ поддршка'],
        ];
        $testimonials = $s['testimonials'] ?? $defaultTestimonials;
      @endphp
      <div class="swiper portfolioTestimonials portfolio-reveal">
        <div class="swiper-wrapper">
          @foreach($testimonials as $t)
          <div class="swiper-slide">
            <blockquote class="portfolio-testimonial">
              <p>{{ $t['quote'] ?? '' }}</p>
              <footer>{{ $t['author'] ?? '' }} <span>{{ $t['tag'] ?? '' }}</span></footer>
            </blockquote>
          </div>
          @endforeach
        </div>
        <div class="portfolio-testimonials__pagination swiper-pagination"></div>
      </div>
    </div>
  </section>

  <section class="portfolio-cta">
    <div class="portfolio-shell">
      <div class="portfolio-cta__box portfolio-reveal">
        <span class="portfolio-eyebrow">{{ $s['cta_eyebrow'] ?? 'Next project' }}</span>
        <h2>{{ $s['cta_heading'] ?? 'Подготвени сте за подобра ИТ инфраструктура?' }}</h2>
        <p>{{ $s['cta_paragraph'] ?? 'Кажете ни што ви треба, а ние ќе предложиме јасно, стабилно и скалабилно решение.' }}</p>
        <a href="{{ route('kontakt') }}" class="portfolio-btn portfolio-btn--primary">Контактирајте не</a>
      </div>
    </div>
  </section>
</section>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const filterButtons = document.querySelectorAll('.portfolio-filter');
    const cards = document.querySelectorAll('.portfolio-card');

    filterButtons.forEach((button) => {
      button.addEventListener('click', () => {
        const filter = button.dataset.filter;

        filterButtons.forEach((item) => item.classList.toggle('is-active', item === button));

        cards.forEach((card) => {
          const isVisible = filter === 'all' || card.dataset.category === filter;
          card.classList.toggle('is-hidden', !isVisible);
        });
      });
    });

    const revealItems = document.querySelectorAll('.portfolio-reveal');
    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.16 });

    revealItems.forEach((item) => revealObserver.observe(item));

    const counters = document.querySelectorAll('[data-count]');
    const counterObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;

        const target = Number(entry.target.dataset.count);
        const suffix = target === 24 ? '/7' : '+';
        const duration = 1200;
        const startTime = performance.now();

        const tick = (now) => {
          const progress = Math.min((now - startTime) / duration, 1);
          const value = Math.floor(progress * target);
          entry.target.textContent = `${value}${suffix}`;

          if (progress < 1) requestAnimationFrame(tick);
        };

        requestAnimationFrame(tick);
        counterObserver.unobserve(entry.target);
      });
    }, { threshold: 0.5 });

    counters.forEach((counter) => counterObserver.observe(counter));
  });
</script>
@endsection
