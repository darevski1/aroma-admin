@extends('layouts.app')
<livewire:navbar />

@section('title', 'ITPlus | Почетна')
@section('meta_description', 'Комплетни ИТ решенија за вашиот бизнис: инфраструктура, одржување, софтверски решенија, безбедност, принтери и техничка поддршка.')


@section('content')





<section class="intro-header home-hero bg">
  <!-- Hero -->
  <div class="home-hero__container max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Grid -->
    <div class="home-hero__grid grid md:grid-cols-2">
      <div class="home-hero__content">
        <h1 class="block text-3xl  text-foreground sm:text-4xl lg:text-7xl lg:leading-tight"> Комплетни ИТ
          решенија за вашиот бизнис</h1>
        <p class="mt-3 text-lg text-foreground">Нудиме целосна ИТ поддршка за компании – од инфраструктура и одржување
          до
          софтверски решенија и безбедност. .</p>

        <!-- Buttons -->
        <div class="home-hero__actions grid gap-3 w-full sm:inline-flex">
          <a href="#learn" class="flex items-center px-6 py-3 rounded-full text-lg int-btn transition gap-2">
            <img src="{{asset(" ./../assets/images/logo_button.svg")}}" alt="icon" class="w-9 h-9 spin" />
            Дознај повеќе
          </a>
          <a href="#contact" class="flex items-center px-6 py-3 rounded-full text-lg int-btn-black-outline gap-2">

            <!-- Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>

            Контактирај не
          </a>


        </div>
        <!-- End Buttons -->


      </div>
      <!-- End Col -->

      <div class="home-hero__media relative">
        <img class="home-hero__image rounded-md"
          src="https://images.unsplash.com/photo-1665686377065-08ba896d16fd?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=700&h=800&q=80"
          alt="Hero Image">
        <div
          class="absolute inset-0 -z-1 bg-linear-to-tr from-surface-1 via-transparent to-travia-transparent size-full rounded-md mt-4 -mb-4 me-4 -ms-4 lg:mt-6 lg:-mb-6 lg:me-6 lg:-ms-6">
        </div>

        <!-- SVG-->
        <div class="absolute bottom-0 start-0">
          <svg class="w-2/3 ms-auto h-auto text-white dark:text-neutral-900" width="630" height="451"
            viewBox="0 0 630 451" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="531" y="352" width="99" height="99" fill="currentColor" />
            <rect x="140" y="352" width="106" height="99" fill="currentColor" />
            <rect x="482" y="402" width="64" height="49" fill="currentColor" />
            <rect x="433" y="402" width="63" height="49" fill="currentColor" />
            <rect x="384" y="352" width="49" height="50" fill="currentColor" />
            <rect x="531" y="328" width="50" height="50" fill="currentColor" />
            <rect x="99" y="303" width="49" height="58" fill="currentColor" />
            <rect x="99" y="352" width="49" height="50" fill="currentColor" />
            <rect x="99" y="392" width="49" height="59" fill="currentColor" />
            <rect x="44" y="402" width="66" height="49" fill="currentColor" />
            <rect x="234" y="402" width="62" height="49" fill="currentColor" />
            <rect x="334" y="303" width="50" height="49" fill="currentColor" />
            <rect x="581" width="49" height="49" fill="currentColor" />
            <rect x="581" width="49" height="64" fill="currentColor" />
            <rect x="482" y="123" width="49" height="49" fill="currentColor" />
            <rect x="507" y="124" width="49" height="24" fill="currentColor" />
            <rect x="531" y="49" width="99" height="99" fill="currentColor" />
          </svg>
        </div>
        <!-- End SVG-->
      </div>
      <!-- End Col -->
    </div>
    <!-- End Grid -->
  </div>
  <!-- End Hero -->
</section>
<!-- You need something to scroll to -->


<section class="bg-[#ff6d63] sticky-section br">
  <div class="max-w-7xl mx-auto">
    <span class="block text-lg md:text-xl lg:text-2xl font-medium text-center p-6 text-white">
      Illuminating Brilliance in Digital Experiences, Content and Creativity
    </span>
  </div>
</section>

@php
  $brandLogos = [
    ['src' => './../assets/images/logo/HP_Logo_0.svg', 'alt' => 'HP'],
    ['src' => './../assets/images/logo/Dell_Logo_Alternative_0.svg', 'alt' => 'Dell'],
    ['src' => './../assets/images/logo/Lenovo_idDXuX8rvi_0.svg', 'alt' => 'Lenovo'],
    ['src' => './../assets/images/logo/Ricoh_Australia_Logo_0.svg', 'alt' => 'Ricoh'],
    ['src' => './../assets/images/logo/Canon_ide_p-tcLS_0.svg', 'alt' => 'Canon'],
    ['src' => './../assets/images/logo/MikroTik_idAi6_gbXf_1.svg', 'alt' => 'MikroTik'],
    ['src' => './../assets/images/logo/Fortinet_idZhbQEBqi_0.svg', 'alt' => 'Fortinet'],
  ];
@endphp

<section class="brand-marquee" aria-label="Компании и брендови со кои работиме">
  <div class="brand-marquee__track">
    @for ($i = 0; $i < 2; $i++)
      <div class="brand-marquee__group" aria-hidden="{{ $i === 1 ? 'true' : 'false' }}">
        @foreach ($brandLogos as $logo)
          <div class="brand-marquee__item">
            <div class="logo-box">
              <img src="{{ asset($logo['src']) }}" alt="{{ $logo['alt'] }}" loading="lazy" />
            </div>
          </div>
        @endforeach
      </div>
    @endfor
  </div>
</section>

<section class="home-services mlss" id="learn">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="home-services__layout">
      <div class="home-services__intro">
        <span class="home-services__eyebrow">Наши Услуги</span>
        <h2>Професионални ИТ решенија за стабилен бизнис</h2>
        <p>
          Нудиме широк спектар на професионални ИТ услуги прилагодени на вашите потреби.
          Од консалтинг и инсталации до одржување и поддршка откријте што можеме да направиме за вас.
        </p>

        <div class="home-services__actions">
          <a href="/uslugi" class="home-services__primary">Сите услуги</a>
          <a href="/kontakt" class="home-services__secondary">Побарај понуда</a>
        </div>

        <div class="home-services__metrics" aria-label="ITPlus service highlights">
          <div>
            <strong>24/7</strong>
            <span>поддршка</span>
          </div>
          <div>
            <strong>360°</strong>
            <span>ИТ покриеност</span>
          </div>
        </div>
      </div>

      <div class="home-services__showcase">
        <div class="home-services__accent-card">
          <span>Managed IT</span>
          <p>Една точка за мрежи, опрема, безбедност, инфраструктура и поддршка.</p>
        </div>

        <div class="home-services__grid">
          <article class="home-service-card">
            <div class="home-service-card__icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-5h6v5M5 21h14M7 12l5-8 5 8" />
              </svg>
            </div>
            <h3>ИТ поддршка</h3>
            <p>Редовен сервис и поддршка за компјутери, сервери и ИТ опрема.</p>
            <a href="/uslugi/odrzuvanje-na-kompjuterski-sistemi">Повеќе</a>
          </article>

          <article class="home-service-card">
            <div class="home-service-card__icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M7 12h10M9 17h6" />
              </svg>
            </div>
            <h3>Мрежни решенија</h3>
            <p>LAN/Wi-Fi инсталации и одржување за стабилна инфраструктура.</p>
            <a href="/uslugi/mrezni-instalacii-i-odrzuvanje-na-mrezi">Повеќе</a>
          </article>

          <article class="home-service-card">
            <div class="home-service-card__icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 10l4.55-2.28A1 1 0 0121 8.62v6.76a1 1 0 01-1.45.9L15 14M4 7h9a2 2 0 012 2v6a2 2 0 01-2 2H4z" />
              </svg>
            </div>
            <h3>Безбедност</h3>
            <p>CCTV системи и контрола на пристап за деловни простори.</p>
            <a href="/uslugi/video-nadzor-i-kontrola-na-pristap">Повеќе</a>
          </article>

          <article class="home-service-card">
            <div class="home-service-card__icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 19V5a2 2 0 012-2h8a2 2 0 012 2v14M9 7h6M9 11h6M9 15h6" />
              </svg>
            </div>
            <h3>Сервери и инфраструктура</h3>
            <p>Планирање и поддршка за сигурни деловни системи.</p>
            <a href="/uslugi">Повеќе</a>
          </article>

          <article class="home-service-card">
            <div class="home-service-card__icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01.88-7.9A5.5 5.5 0 0118.4 10.1 3.5 3.5 0 1118 17H8" />
              </svg>
            </div>
            <h3>Cloud / Backup решенија</h3>
            <p>Решенија за достапност, заштита и континуитет на податоци.</p>
            <a href="/uslugi">Повеќе</a>
          </article>

          <article class="home-service-card">
            <div class="home-service-card__icon">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 8V4h10v4M6 17H5a2 2 0 01-2-2v-3a3 3 0 013-3h12a3 3 0 013 3v3a2 2 0 01-2 2h-1M7 14h10v6H7z" />
              </svg>
            </div>
            <h3>Принтери и сервис</h3>
            <p>Продажба, сервис и изнајмување на принтери и уреди.</p>
            <a href="/uslugi/servis-i-prodazba-na-printeri">Повеќе</a>
          </article>
        </div>
      </div>
    </div>
  </div>
</section>
<section class="relative min-h-screen flex items-center bg-gray-100 overflow-hidden">

  <!-- 🔵 Background blobs -->
  <div class="absolute w-72 h-72 bg-blue-200/50 rounded-full blur-3xl top-20 left-10 animate-pulse"></div>
  <div class="absolute w-96 h-96 bg-blue-100/40 rounded-full blur-3xl bottom-10 right-10 animate-pulse"></div>

  <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-12 items-center relative z-10">

    <!-- ✏️ LEFT CONTENT -->
    <div class="space-y-6 opacity-0 translate-y-10 animate-fadeIn">

      <h1 class="block font-medium text-foreground-inverse text-4xl sm:text-5xl md:text-6xl lg:text-7xl">
        Ceno<span class="text-[#5c59f5]">BOTS</span> Индустриски решенија за чистење
      </h1>

      <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950 mb-6">
        Нудиме продажба и изнајмување на индустриски роботи <b>(Cenobots) </b> за автоматизација на производствени
        процеси.
        Зголемете ја продуктивноста, намалете ги трошоците и оптимизирајте ја вашата работа со модерни роботски
        решенија.
      </p>

      <div class="flex gap-4 pt-4">
        <a href="/services/cenobots" class="px-6 py-3 rounded-full bg-[#5c59f5] text-white hover:bg-black transition">
          Дознај повеќе
        </a>

        <a href="#tehnologija"
          class="px-6 py-3 rounded-full border border-[#5c59f5] text-[#5c59f5] hover:bg-black hover:text-white transition">
          Технологија
        </a>
      </div>

    </div>

    <!-- 🤖 RIGHT IMAGE -->
    <div class="flex justify-center relative">

      <!-- glow behind robot -->
      <div class="absolute w-[400px] h-[400px] bg-blue-300/30 blur-3xl rounded-full"></div>

      <img id="heroRobot" src="{{ asset('./../assets/images/cenobot.png') }}" alt="CenoBot Robot"
        class="relative z-10 max-h-[60vh] object-contain transition-transform duration-300 ease-out animate-float" />

    </div>

  </div>
</section>

<!-- Hero -->
<div class="relative overflow-hidden">
  <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="absolute w-72 h-72 bg-blue-200/50 rounded-full blur-3xl top-20 left-10 animate-pulse"></div>
    <div class="absolute w-96 h-96 bg-blue-100/40 rounded-full blur-3xl bottom-10 right-10 animate-pulse"></div>
    <div class="max-w-2xl text-center mx-auto">
      <h1 class="block font-medium text-foreground-inverse text-3xl sm:text-4xl md:text-5xl lg:text-6xl"> Изнајмување,
        продажба и сервис на
        печатачи </h1>

      <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950 mt-8">
        Современи и сигурни решенија за печатење прилагодени на потребите на вашиот бизнис – од професионални печатачи и
        редовен сервис, до флексибилно изнајмување со целосна техничка поддршка и брза интервенција.
      </p>

    </div>

    <div class="mt-10 relative max-w-5xl mx-auto">
      <div class="w-full h-96 sm:h-[480px] bg-center bg-cover bg-no-repeat rounded-xl"
        style="background-image: url('/assets/images/printer-hero.jpg');">
      </div>

      <div class="absolute inset-0 size-full">
        <div class="flex flex-col justify-center items-center size-full">
          <a href="#learn" class="flex items-center px-6 py-3 rounded-full text-lg int-btn transition gap-2">
            Дознај повеќе
          </a>
        </div>
      </div>

      <div
        class="absolute bottom-12 -start-20 -z-1 size-48 bg-linear-to-b blur-xl opacity-50 from-red-500 to-transparent p-px rounded-lg">
        <div class="bg-background size-48 rounded-lg"></div>
      </div>

      <div
        class="absolute -top-12 -end-20 -z-1 size-68 bg-linear-to-t from-primary opacity-80 blur-xxl to-cyan-400 p-px rounded-full">
        <div class="bg-background size-48 rounded-full"></div>
      </div>
    </div>
  </div>
</div>
<!-- End Hero -->

<section class="home-clients" aria-labelledby="home-clients-title">
  <div class="home-clients__inner">

    <!-- Header -->
    <div class="home-clients__header">

      <span class="home-clients__eyebrow">
        Доверба и соработка
      </span>


      <h2 id="home-clients-title">
        Наши Клиенти
      </h2>

      <p>
        Компании кои ни веруваат за професионални ИТ решенија, мрежна инфраструктура,
        видео надзор и печатарски услуги.
      </p>
    </div>

    @php
      $clientLogos = [
        ['./../assets/images/clients/erak-group-300x69.png', 'Erak Group'],
        ['./../assets/images/clients/logo_arka-300x300.jpg', 'Arka'],
        ['./../assets/images/clients/logo_delight.jpg', 'Delight'],
        ['./../assets/images/clients/logo_biomedika.jpg', 'Biomedika'],
        ['./../assets/images/clients/logo_digital.jpg', 'Digital'],
        ['./../assets/images/clients/logo_doma.jpg', 'Doma'],
        ['./../assets/images/clients/logo_eurofast.jpg', 'Eurofast'],
        ['./../assets/images/clients/logo_fresh_farm.jpg', 'Fresh Farm'],
        ['./../assets/images/clients/logo_majorel.jpg', 'Majorel'],
        ['./../assets/images/clients/logo_pivara.jpg', 'Pivara'],
        ['./../assets/images/clients/logo_premium.jpg', 'Premium'],
        ['./../assets/images/clients/logo_tehnoguma.jpg', 'Tehnoguma'],
        ['./../assets/images/clients/logo_total_comunikacii.jpg', 'Total Komunikacii'],
        ['./../assets/images/clients/logo_trend.jpg', 'Trend'],
        ['./../assets/images/clients/Vato-Logo.png', 'Vato'],
      ];
    @endphp

    <div class="client-marquee" aria-label="Логоа на наши клиенти">
      <div class="client-marquee__track">
        @for ($i = 0; $i < 2; $i++)
          <div class="client-marquee__group" aria-hidden="{{ $i === 1 ? 'true' : 'false' }}">
            @foreach ($clientLogos as [$src, $alt])
              <div class="client-marquee__item">
                <img src="{{ asset($src) }}" alt="{{ $alt }}" loading="lazy">
              </div>
            @endforeach
          </div>
        @endfor
      </div>
    </div>

  </div>
</section>


<section class="p-4 bgpacebot bg">
  <div class="wl32">

    <div class="mls flex items-center justify-center min-h-screen p-4">

      yuka banner za promocija druga

    </div>

    <div class="mls">
      tuka moze promocija nekoj za renta na printer

    </div>

</section>
<section class="garmin-showcase bgpacebot bg" aria-labelledby="garmin-showcase-title">
  <div class="garmin-showcase__inner">
    <div class="garmin-showcase__header">
      <span class="garmin-showcase__eyebrow">explore</span>

      <img src="{{ asset('./assets/images/logo/garmin.svg') }}" alt="Garmin" class="garmin-showcase__logo" />

      <h2 id="garmin-showcase-title" class="garmin-showcase__title">
        Паметни часовници и GPS уреди дизајнирани за авантура, спорт и секојдневие.
      </h2>

      <a href="https://exploregarmin.mk/" target="_blank" rel="noopener"
        class="garmin-showcase__brand-link" aria-label="Отвори Explore Garmin">
        <img src="{{ asset('./assets/images/explore_garmin.png') }}" alt="explore garmin" />
      </a>
    </div>

    <div class="garmin-showcase__slider-wrap">
      <div class="garmin-showcase__nav">
        <button class="garmin-showcase__arrow garmin-showcase__arrow--prev" type="button" aria-label="Претходен Garmin производ">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M15 18l-6-6 6-6" />
          </svg>
        </button>
        <button class="garmin-showcase__arrow garmin-showcase__arrow--next" type="button" aria-label="Следен Garmin производ">
          <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M9 6l6 6-6 6" />
          </svg>
        </button>
      </div>

      <div class="swiper garminSwiper">
        <div class="swiper-wrapper">
          <div class="swiper-slide garmin-showcase__slide">
            <article class="garmin-card">
              <a href="https://exploregarmin.mk/shop/swoof/series-fenix/" target="_blank" rel="noopener" class="garmin-card__image-link">
                <img src="{{ asset('./assets/images/garmin/fenix.webp') }}" alt="Garmin Fenix серија" class="garmin-card__image" loading="lazy" />
              </a>
              <div class="garmin-card__body">
                <p class="garmin-card__label">Adventure GPS</p>
                <h3>Garmin Fenix</h3>
                <p>Премиум часовник за авантура и тренинг.</p>
                <a href="https://exploregarmin.mk/shop/swoof/series-fenix/" target="_blank" rel="noopener" class="garmin-card__cta">Погледни серија</a>
              </div>
            </article>
          </div>

          <div class="swiper-slide garmin-showcase__slide">
            <article class="garmin-card">
              <a href="https://exploregarmin.mk/shop/swoof/series-forerunner/#products-archive" target="_blank" rel="noopener" class="garmin-card__image-link">
                <img src="{{ asset('./assets/images/garmin/forerunner.webp') }}" alt="Garmin Forerunner серија" class="garmin-card__image" loading="lazy" />
              </a>
              <div class="garmin-card__body">
                <p class="garmin-card__label">Running Performance</p>
                <h3>Garmin Forerunner</h3>
                <p>Создаден за трчање, подготовка и прогрес.</p>
                <a href="https://exploregarmin.mk/shop/swoof/series-forerunner/#products-archive" target="_blank" rel="noopener" class="garmin-card__cta">Погледни серија</a>
              </div>
            </article>
          </div>

          <div class="swiper-slide garmin-showcase__slide">
            <article class="garmin-card">
              <a href="https://exploregarmin.mk/shop/swoof/series-venu/#products-archive" target="_blank" rel="noopener" class="garmin-card__image-link">
                <img src="{{ asset('./assets/images/garmin/venu.avif') }}" alt="Garmin Venu серија" class="garmin-card__image" loading="lazy" />
              </a>
              <div class="garmin-card__body">
                <p class="garmin-card__label">Wellness Smartwatch</p>
                <h3>Garmin Venu</h3>
                <p>AMOLED стил, здравје и фитнес во едно.</p>
                <a href="https://exploregarmin.mk/shop/swoof/series-venu/#products-archive" target="_blank" rel="noopener" class="garmin-card__cta">Погледни серија</a>
              </div>
            </article>
          </div>

          <div class="swiper-slide garmin-showcase__slide">
            <article class="garmin-card">
              <a href="https://exploregarmin.mk/shop/swoof/series-fenix/" target="_blank" rel="noopener" class="garmin-card__image-link">
                <img src="{{ asset('./assets/images/garmin/fenix2.webp') }}" alt="Garmin Fenix промо модел" class="garmin-card__image" loading="lazy" />
              </a>
              <div class="garmin-card__body">
                <p class="garmin-card__label">Premium Outdoor</p>
                <h3>Fenix Collection</h3>
                <p>Издржлива колекција за секој предизвик.</p>
                <a href="https://exploregarmin.mk/shop/swoof/series-fenix/" target="_blank" rel="noopener" class="garmin-card__cta">Истражи модели</a>
              </div>
            </article>
          </div>

          <div class="swiper-slide garmin-showcase__slide">
            <article class="garmin-card">
              <a href="https://exploregarmin.mk/shop/swoof/series-vivoactive/#products-archive" target="_blank" rel="noopener" class="garmin-card__image-link">
                <img src="{{ asset('./assets/images/garmin/vivoactive.webp') }}" alt="Garmin Vivoactive серија" class="garmin-card__image" loading="lazy" />
              </a>
              <div class="garmin-card__body">
                <p class="garmin-card__label">Everyday Fitness</p>
                <h3>Garmin Vivoactive</h3>
                <p>Фитнес, здравје и паметни функции секој ден.</p>
                <a href="https://exploregarmin.mk/shop/swoof/series-vivoactive/#products-archive" target="_blank" rel="noopener" class="garmin-card__cta">Погледни серија</a>
              </div>
            </article>
          </div>
        </div>

        <div class="garmin-showcase__pagination swiper-pagination"></div>
      </div>
    </div>
  </div>
  <!-- CTA SECTION -->
  <section class="py-16 bg-zinc-100">
    <div class="max-w-5xl mx-auto px-6">

      <div class="bg-white rounded-3xl shadow-lg p-10 md:p-14 text-center">

        <h2 class="text-2xl md:text-4xl font-thin text-zinc-900">
          Побарајте понуда
        </h2>

        <p class="mt-4 text-zinc-600 text-base md:text-lg max-w-2xl mx-auto">
          Контактирајте не за повеќе информации околу нашите услуги и добијте понуда според вашите потреби.
        </p>

        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">

          <a href="/kontakt"
            class="px-8 py-4 rounded-full bg-zinc-900 text-white font-medium hover:bg-zinc-800 transition">
            Контакт
          </a>

          <a href="tel:+38970000000"
            class="px-8 py-4 rounded-full border border-zinc-300 text-zinc-800 font-medium hover:bg-zinc-100 transition">
            +389 70 000 000
          </a>

        </div>

      </div>

    </div>
  </section>


  @endsection

  <script>
    const robot = document.getElementById('heroRobot');

window.addEventListener('scroll', () => {
  const scrollY = window.scrollY;

  let scale = 1 + scrollY * 0.0003;
  scale = Math.min(Math.max(scale, 1), 1.2);

  robot.style.transform = `scale(${scale})`;
});
  </script>
