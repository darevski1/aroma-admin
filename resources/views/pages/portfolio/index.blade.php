@extends('layouts.app')
<livewire:navbar />

@section('title', 'ITPlus | Портфолио')
@section('meta_description', 'Погледнете дел од реализираните ITPlus проекти за мрежна инфраструктура, видео надзор, сервери, принтери и контрола на пристап.')

@section('content')
@php
  $projects = [
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

  $filters = [
    'all' => 'Сите',
    'network' => 'Мрежи',
    'security' => 'Безбедност',
    'support' => 'ИТ поддршка',
    'printers' => 'Принтери',
    'servers' => 'Сервери',
  ];
@endphp

<section class="portfolio-page">
  <section class="portfolio-hero">
    <div class="portfolio-hero__bg" aria-hidden="true"></div>
    <div class="portfolio-shell portfolio-hero__grid">
      <div class="portfolio-reveal">
        <span class="portfolio-eyebrow">Портфолио</span>
        <h1>Проекти што ја прават технологијата сигурна, брза и видлива.</h1>
        <p>
          Дел од проектите и решенијата кои успешно ги реализиравме за компании,
          институции и приватни клиенти.
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
        <span class="portfolio-eyebrow">Featured work</span>
        <h2 id="featured-projects-title">Избрани реализации</h2>
        <p>Поголеми инфраструктурни и безбедносни проекти со јасен фокус на стабилност, контрола и долгорочна вредност.</p>
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
          <span class="portfolio-eyebrow">Project library</span>
          <h2 id="portfolio-projects-title">Сите проекти</h2>
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
        <strong data-count="200">0</strong>
        <span>Реализирани проекти</span>
      </div>
      <div class="portfolio-stat portfolio-reveal">
        <strong data-count="100">0</strong>
        <span>Активни клиенти</span>
      </div>
      <div class="portfolio-stat portfolio-reveal">
        <strong data-count="10">0</strong>
        <span>Години искуство</span>
      </div>
      <div class="portfolio-stat portfolio-reveal">
        <strong data-count="24">0</strong>
        <span>Поддршка и сервис</span>
      </div>
    </div>
  </section>

  <section class="portfolio-testimonials" aria-labelledby="portfolio-testimonials-title">
    <div class="portfolio-shell">
      <div class="portfolio-section-head portfolio-reveal">
        <span class="portfolio-eyebrow">Client voice</span>
        <h2 id="portfolio-testimonials-title">Што добиваат клиентите</h2>
      </div>

      <div class="swiper portfolioTestimonials portfolio-reveal">
        <div class="swiper-wrapper">
          <div class="swiper-slide">
            <blockquote class="portfolio-testimonial">
              <p>„Стабилна мрежа, брза имплементација и јасна комуникација во секоја фаза од проектот.“</p>
              <footer>Корпоративен клиент <span>Мрежни решенија</span></footer>
            </blockquote>
          </div>
          <div class="swiper-slide">
            <blockquote class="portfolio-testimonial">
              <p>„Видео надзорот и контролата на пристап ни дадоа многу подобра прегледност и сигурност.“</p>
              <footer>Деловен простор <span>Безбедносни системи</span></footer>
            </blockquote>
          </div>
          <div class="swiper-slide">
            <blockquote class="portfolio-testimonial">
              <p>„Одржувањето е организирано, навремено и без непотребни прекини во секојдневната работа.“</p>
              <footer>Приватна компанија <span>ИТ поддршка</span></footer>
            </blockquote>
          </div>
        </div>
        <div class="portfolio-testimonials__pagination swiper-pagination"></div>
      </div>
    </div>
  </section>

  <section class="portfolio-cta">
    <div class="portfolio-shell">
      <div class="portfolio-cta__box portfolio-reveal">
        <span class="portfolio-eyebrow">Next project</span>
        <h2>Подготвени сте за подобра ИТ инфраструктура?</h2>
        <p>Кажете ни што ви треба, а ние ќе предложиме јасно, стабилно и скалабилно решение.</p>
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
