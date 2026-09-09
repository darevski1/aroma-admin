@extends('layouts.app')


@section('title', 'ITPlus | CenoBots')
@section('meta_description', 'AI автономни роботи за чистење CenoBots: модерни роботски решенија за компании, хотели, трговски центри и јавни објекти.')
@section('content')
@php
    $s = $page->settings ?? [];
@endphp

<div class="cenobots-page">
  <!-- ── HERO ─────────────────────────────────────────── -->
  <section class="cenobots-hero snap-section bg-gray-100 relative overflow-hidden" id="hero-section">

    <!-- Decorative blobs (pointer-events:none so they don't block clicks) -->
    <div class="blob absolute w-72 h-72 bg-blue-200/60 rounded-full blur-3xl top-20 left-10 pointer-events-none"></div>
    <div
      class="blob-delay absolute w-80 h-80 bg-blue-100/50 rounded-full blur-3xl bottom-10 left-1/3 pointer-events-none">
    </div>

    <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center gap-12">

      <!-- Copy -->
      <div class="md:w-1/2 space-y-6 z-10">
        <h1 class="text-7xl md:text-7xl font-normal">{{ $s['hero_heading'] ?? 'CENOBOTS' }}</h1>
        <p class="text-xl md:text-2xl font-light text-gray-700 leading-relaxed">
          {{ $s['hero_description'] ?? 'AI-автономни роботи за чистење. Паметно, ефикасно и еколошко решение за секој комерцијален простор.' }}
        </p>
        <a href="#l3"
          class="cenobots-glass-btn px-6 py-3 mt-5 rounded-full text-white font-md">
          {{ $s['hero_cta'] ?? 'Дознајте повеќе' }}
        </a>
      </div>

      <!-- Robot image -->
      <div class="md:w-1/2 flex justify-center z-10">
        <img id="hero-image" src="{{ $s['hero_image'] ?? asset('./../assets/images/cenobot.png') }}" alt="{{ $s['hero_image_alt'] ?? 'Cenobot робот' }}"
          class="hero-image max-h-[60vh] object-contain transition-transform duration-200 will-change-transform">
      </div>
    </div>
  </section>

  <section class="cenobots-solutions" id="resenija">
    <div class="max-w-7xl mx-auto px-6">
      <div class="cenobots-solutions__card">
        <div>
          <span class="cenobots-eyebrow">{{ $s['solutions_eyebrow'] ?? 'Решенија' }}</span>
          <h2>{{ $s['solutions_heading'] ?? 'Индустриски машини за професионално чистење' }}</h2>
        </div>
        <div class="cenobots-solutions__content">
          <p>
            {{ $s['solutions_description'] ?? 'Cenobots се индустриски машини за чистење наменети за ефикасно, безбедно и автоматизирано одржување на големи комерцијални и индустриски простори. Тие помагаат во намалување на трошоците, зголемување на продуктивноста и обезбедување постојано висок квалитет на чистење.' }}
          </p>
          <div class="cenobots-solutions__actions">
            <a href="#l3" class="cenobots-btn cenobots-btn--primary">{{ $s['solutions_btn_1'] ?? 'Погледни модели' }}</a>
            <a href="{{ asset('storage/assets/docs/cenobots-brochure.pdf') }}" target="_blank" download="cenobots-brochure.pdf"
              class="cenobots-btn cenobots-btn--ghost">{{ $s['solutions_btn_2'] ?? 'Преземи брошура' }}</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="bg-[#ff6d63] sticky-section_navbar br" id="navbar_sticky">
    <div class="max-w-7xl mx-auto">
      <div class="flex flex-wrap gap-4">
        <a href="#l3"
          class="px-5 py-2 border text-white border-white rounded-full text-md hover:bg-black hover:text-white transition">
          Cenobot L3
        </a>
        <a href="#l50"
          class="px-5 py-2 border text-white border-white rounded-full text-md hover:bg-black hover:text-white transition">
          Cenobot L50
        </a>
        <a href="#l4"
          class="px-5 py-2 border text-white border-white rounded-full text-md hover:bg-black hover:text-white transition">
          Cenobot L4
        </a>

        <a href="#sp50"
          class="px-5 py-2 border text-white border-white rounded-full text-md hover:bg-black hover:text-white transition">
          Cenobot SP50
        </a>

        <a href="#tehnologija"
          class="px-5 py-2 border text-white border-white rounded-full text-md hover:bg-black hover:text-white transition">
          Технологија
        </a>

        <a href="#resenija"
          class="px-5 py-2 border text-white border-white rounded-full text-md hover:bg-black hover:text-white transition">
          Решенија
        </a>

      </div>
    </div>
  </section>
  <!-- ── AREAS OF USE ──────────────────────────────────── -->
  <section class="snap-section bg-gr">
    <div class="max-w-7xl mx-auto px-6 py-16 w-full">

      <div class="text-center mb-12">
        <h2 class="text-6xl md:text-6xl font-normal mb-4">
          {{ $s['areas_heading_1'] ?? 'Области на' }} <span class="colorBlue">{{ $s['areas_heading_2'] ?? 'Примена' }}</span>
        </h2>
        <p class="text-xl md:text-2xl font-light text-gray-700 leading-relaxed">
          {{ $s['areas_description'] ?? 'Од лобија до ходници — нашите роботи се прилагодуваат на секоја комерцијална средина.' }}
        </p>
      </div>

      @php
        $defaultAreas = [
          ['image' => './../assets/images/retail.jpg', 'alt' => 'Трговски Центри', 'title' => 'Трговски Центри'],
          ['image' => './../assets/images/hotel.jpg', 'alt' => 'Хотели', 'title' => 'Хотели'],
          ['image' => './../assets/images/hospital.jpg', 'alt' => 'Болници', 'title' => 'Болници'],
          ['image' => './../assets/images/education.jpg', 'alt' => 'Образование', 'title' => 'Образование'],
        ];
        $areas = $s['areas'] ?? $defaultAreas;
      @endphp
      <div class=" grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($areas as $area)
        <div class="area-card relative rounded-xl overflow-hidden shadow-md cursor-pointer">
          <img src="{{asset( $area['image'] ?? './../assets/images/retail.jpg')}}" alt="{{ $area['alt'] ?? $area['title'] ?? '' }}" class="w-full h-64 object-cover">
          <div class="absolute inset-0 bg-gradient-to-t from-black/65 to-transparent"></div>
          <h3 class="absolute bottom-4 left-4 text-white font-semibold">{{ $area['title'] ?? '' }}</h3>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- ── APP SECTION ───────────────────────────────────── -->
  <section class="snap-section h100vh bg-zinc-100" id="tehnologija">
    <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center gap-12">

      <!-- Copy -->
      <div class="md:w-1/2 space-y-6">
        <h2 class="text-6xl md:text-6xl font-normal leading-snug tracking-tighter ">
          {{ $s['tech_heading'] ?? 'Паметна контрола на чистењето во ваши раце' }}
        </h2>
        <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950">
          {{ $s['tech_paragraph_1'] ?? 'Апликацијата за мобилни уреди на CenoBots L50 носи ново ниво на контрола и прегледност во процесот на чистење. Со модерен интерфејс, корисниците во секој момент имаат увид во работата на роботот — од тековната позиција до детални информации за перформансите.' }}
        </p>
        <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950">
          {{ $s['tech_paragraph_2'] ?? 'Преку апликацијата лесно се креираат рути за чистење, добиваат известувања за завршени задачи и се следи статусот на уредот. Управувањето станува едноставно, паметно и целосно под ваша контрола — каде и да сте.' }}
        </p>
      </div>

      <!-- App screenshot -->
      <div class="md:w-1/2 flex justify-center">
        <img src="{{ $s['tech_image'] ?? asset('./../assets/images/cenobots-app-l50.webp') }}" alt="{{ $s['tech_image_alt'] ?? 'CenoBots L50 апликација' }}"
          class="w-full max-w-md object-cover rounded-2xl shadow-xl hover:scale-105 transition-transform duration-300">
      </div>
    </div>
  </section>

  <!-- ── FULL-COLOUR SNAP SECTIONS ─────────────────────── -->
  <section class="snap-section py-20  section-scroll-top animate-item" id="l3">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

      <!-- LEFT SIDE -->
      <div class="space-y-6">
        <h2 class="text-7xl md:text-7xl font-normal">
          {{ $s['l3_heading'] ?? 'CenoBots L3' }}
        </h2>

        <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950">
          {{ $s['l3_description'] ?? 'Компактен автономен робот за професионално чистење со AI навигација и висока ефикасност. Идеален за канцеларии, маркети, хотели и комерцијални простори.' }}
        </p>

        @php
          $defaultL3Images = [
            './../assets/images/cenobots/L3-images/01.png',
            './../assets/images/cenobots/L3-images/02.png',
            './../assets/images/cenobots/L3-images/03.png',
            './../assets/images/cenobots/L3-images/04.png',
            './../assets/images/cenobots/L3-images/05.png',
            './../assets/images/cenobots/L3-images/06.png',
          ];
          $l3Images = $s['l3_images'] ?? $defaultL3Images;
        @endphp
        <div class="swiper mySwiper">
          <div class="swiper-wrapper">
            @foreach($l3Images as $img)
            <div class="swiper-slide">
              <img src="{{asset($img)}}" alt="{{ $s['l3_heading'] ?? 'CenoBots L3' }}"
                class="w-full max-w-xl hover:scale-105 transition-transform duration-300">
            </div>
            @endforeach
          </div>
          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>
          <div class="swiper-pagination"></div>
        </div>
      </div>

      <!-- RIGHT SIDE -->
      <div>
        <div class="space-y-6">

          <div>
            <p class="text-lg md:text-xl font-normal text-zinc-950 mt-3">
              {{ $s['l3_feature_heading'] ?? 'Автономно професионално чистење со паметна роботика и целосна контрола' }}
            </p>
          </div>

          <div class="space-y-3 text-zinc-950">
            @php
              $defaultL3Features = [
                ['icon' => '🧠', 'text' => 'AI + SLAM навигација'],
                ['icon' => '📡', 'text' => '3D LiDAR детекција на пречки'],
                ['icon' => '💧', 'text' => '25L чиста / 25L валкана вода'],
                ['icon' => '⚡', 'text' => 'до 2,000 m²/час покриеност'],
                ['icon' => '🔄', 'text' => 'автоматско докирање и полнење'],
                ['icon' => '📱', 'text' => 'далечинска контрола и распоредување'],
              ];
              $l3Features = $s['l3_features'] ?? $defaultL3Features;
            @endphp
            @foreach($l3Features as $f)
            <div class="flex text-lg md:text-xl font-thin items-center gap-2">
              <span>{{ $f['icon'] ?? '' }}</span> {{ $f['text'] ?? '' }}
            </div>
            @endforeach
          </div>

          <div class="pt-4">
            <p class="text-lg md:text-xl font-thin text-zinc-700">
              {{ $s['l3_footer_text'] ?? '100% автономно чистење без човечка интервенција' }}
            </p>
          </div>

          <div class="flex gap-4 pt-2">
            <a href="#l3"
              class="cenobots-glass-btn px-6 py-3 mt-5 rounded-full text-white font-md">
              {{ $s['l3_btn_1'] ?? 'Дознајте повеќе' }}
            </a>
            <a href="{{ asset('storage/assets/docs/cenobots-brochure.pdf') }}" target="_blank"
              download="cenobots-brochure.pdf"
              class="px-6 py-3 mt-5 rounded-full bg-white text-[#5c59f5] font-medium border border-[#5c59f5] transition-all duration-300 hover:bg-black hover:text-white hover:border-black">
              {{ $s['l3_btn_2'] ?? 'Брошура' }}
            </a>
          </div>

        </div>
      </div>

    </div>
  </section>
  <section class="snap-section br h100vh  py-20  section-scroll-top" id="l50">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

      <!-- LEFT SIDE -->
      <div class="space-y-6">
        <h2 class="text-7xl md:text-7xl font-normal">
          {{ $s['l50_heading'] ?? 'CenoBots l50' }}
        </h2>

        <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950">
          {{ $s['l50_description'] ?? 'CenoBots L3 е индустриска роботска машина дизајнирана за автоматизација на производствени процеси. Овозможува висока прецизност, брзина и сигурност во работењето, што ја прави идеална за модерни фабрики.' }}
        </p>

        <img src="{{asset($s['l50_image'] ?? './../assets/images/cenobots/L3-images/01.png')}}" alt="{{ $s['l50_heading'] ?? 'CenoBots L50' }}"
          class="w-full max-w-md hover:scale-135 transition-transform duration-300">
      </div>

      <!-- RIGHT SIDE -->
      <div class="hidden md:block">
        <div>
          <div class="space-y-6">

            <div>
              <p class="text-lg md:text-xl font-normal text-zinc-950 mt-3">
                {{ $s['l50_feature_heading'] ?? 'Индустриски автономен робот за чистење на големи површини со висока продуктивност и напредна роботика' }}
              </p>
            </div>

            <div class="space-y-3 text-zinc-950">
              @php
                $defaultL50Features = [
                  ['icon' => '🧠', 'text' => 'AI + SLAM навигација со висока прецизност'],
                  ['icon' => '📡', 'text' => '3D LiDAR + мулти-сензорска детекција'],
                  ['icon' => '💧', 'text' => 'голем капацитет на резервоари (чиста / валкана вода)'],
                  ['icon' => '⚡', 'text' => 'висока покриеност за индустриски простори'],
                  ['icon' => '🔄', 'text' => 'автоматско докирање, полнење и празнење'],
                  ['icon' => '📱', 'text' => 'централизирана контрола и управување преку апликација'],
                ];
                $l50Features = $s['l50_features'] ?? $defaultL50Features;
              @endphp
              @foreach($l50Features as $f)
              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>{{ $f['icon'] ?? '' }}</span> {{ $f['text'] ?? '' }}
              </div>
              @endforeach
            </div>

            <div class="pt-4">
              <p class="text-lg md:text-xl font-thin text-zinc-700">
                {{ $s['l50_footer_text'] ?? 'Максимална ефикасност за големи комерцијални и индустриски објекти' }}
              </p>
            </div>

            <div class="flex gap-4 pt-2">
              <a href="#l50"
                class="cenobots-glass-btn px-6 py-3 mt-5 rounded-full text-white font-md">
                {{ $s['l50_btn_1'] ?? 'Дознајте повеќе' }}
              </a>
              <a href="{{ asset('storage/assets/docs/cenobots-brochure.pdf') }}" target="_blank"
                download="cenobots-brochure.pdf"
                class="px-6 py-3 mt-5 rounded-full bg-white text-[#5c59f5] font-medium border border-[#5c59f5] transition-all duration-300 hover:bg-black hover:text-white hover:border-black">
                {{ $s['l50_btn_2'] ?? 'Брошура' }}
              </a>
            </div>

          </div>
        </div>
      </div>

    </div>
  </section>
  <section class="snap-section br h100vh  py-20 section-scroll-top" id="l4">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

      <!-- LEFT SIDE -->
      <div class="space-y-6">
        <h2 class="text-7xl md:text-7xl font-normal">
          {{ $s['l4_heading'] ?? 'CenoBots l4' }}
        </h2>

        <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950">
          {{ $s['l4_description'] ?? 'CenoBots L3 е индустриска роботска машина дизајнирана за автоматизација на производствени процеси. Овозможува висока прецизност, брзина и сигурност во работењето, што ја прави идеална за модерни фабрики.' }}
        </p>

        <img src="{{asset($s['l4_image'] ?? './../assets/images/cenobots/L3-images/01.png')}}" alt="{{ $s['l4_heading'] ?? 'CenoBots L4' }}"
          class="w-full max-w-md hover:scale-15 transition-transform duration-300">
      </div>

      <!-- RIGHT SIDE -->
      <div class="hidden md:block">
        <div>
          <div class="space-y-6">

            <div>
              <p class="text-lg md:text-xl font-normal text-zinc-950 mt-3">
                {{ $s['l4_feature_heading'] ?? 'Компактен и интелигентен робот за автономно чистење во тесни и динамични простори' }}
              </p>
            </div>

            <div class="space-y-3 text-zinc-950">
              @php
                $defaultL4Features = [
                  ['icon' => '🧠', 'text' => 'AI + SLAM навигација со Nvidia процесор'],
                  ['icon' => '📡', 'text' => '3D LiDAR + depth камери за прецизна детекција'],
                  ['icon' => '💧', 'text' => '~34–38L резервоари за чиста и валкана вода'],
                  ['icon' => '⚡', 'text' => 'до ~2,000 m²/час продуктивност'],
                  ['icon' => '📐', 'text' => 'дизајниран за тесни простори и коридори'],
                  ['icon' => '🔄', 'text' => 'автоматско полнење, мапирање и 24/7 работа'],
                  ['icon' => '📱', 'text' => 'контрола преку мобилна апликација и touchscreen'],
                ];
                $l4Features = $s['l4_features'] ?? $defaultL4Features;
              @endphp
              @foreach($l4Features as $f)
              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>{{ $f['icon'] ?? '' }}</span> {{ $f['text'] ?? '' }}
              </div>
              @endforeach
            </div>

            <div class="pt-4">
              <p class="text-lg md:text-xl font-thin text-zinc-700">
                {{ $s['l4_footer_text'] ?? 'Оптимизиран за максимална маневрибилност и ефикасност во сложени простори' }}
              </p>
            </div>

            <div class="flex gap-4 pt-2">
              <a href="#l4"
                class="cenobots-glass-btn px-6 py-3 mt-5 rounded-full text-white font-md">
                {{ $s['l4_btn_1'] ?? 'Дознајте повеќе' }}
              </a>
              <a href="{{ asset('storage/assets/docs/cenobots-brochure.pdf') }}" target="_blank"
                download="cenobots-brochure.pdf"
                class="px-6 py-3 mt-5 rounded-full bg-white text-[#5c59f5] font-medium border border-[#5c59f5] transition-all duration-300 hover:bg-black hover:text-white hover:border-black">
                {{ $s['l4_btn_2'] ?? 'Брошура' }}
              </a>
            </div>

          </div>
        </div>
      </div>

    </div>
  </section>
  <section class="snap-section br xl  py-20 section-scroll-top" id="sp50">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
      <!-- LEFT SIDE -->
      <div class="space-y-6" br>
        <h2 class="text-7xl md:text-7xl font-normal">
          {{ $s['sp50_heading'] ?? 'CenoBots SP50' }}
        </h2>

        <h2 class="text-3xl md:text-3xl font-thin">
          {{ $s['sp50_subheading'] ?? 'Автономен робот за индустриско чистење со висока прецизност' }}
        </h2>

        <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950">
          {{ $s['sp50_description'] ?? 'Паметно, брзо и целосно автоматизирано чистење на големи површини. SP50 користи AI навигација и LiDAR технологија за прецизно детектирање и отстранување на нечистотии – без потреба од човечка интервенција.' }}
        </p>
        @php
          $defaultSP50Images = [
            './../assets/images/cenobots/SP50/1.png',
            './../assets/images/cenobots/SP50/1.png',
            './../assets/images/cenobots/SP50/2.png',
            './../assets/images/cenobots/SP50/3.png',
            './../assets/images/cenobots/SP50/4.png',
            './../assets/images/cenobots/SP50/5.png',
          ];
          $sp50Images = $s['sp50_images'] ?? $defaultSP50Images;
        @endphp
        <div class="swiper mySwiper">
          <div class="swiper-wrapper">
            @foreach($sp50Images as $img)
            <div class="swiper-slide">
              <img src="{{asset($img)}}" alt="{{ $s['sp50_heading'] ?? 'CenoBots SP50' }}"
                class="w-full max-w-xl hover:scale-105 transition-transform duration-300">
            </div>
            @endforeach
          </div>
          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>
          <div class="swiper-pagination"></div>
        </div>

      </div>

      <!-- RIGHT SIDE -->
      <div class="hidden md:block">
        <div>
          <div class="space-y-6">

            <div>
              <p class="text-lg md:text-xl font-normal text-zinc-950 mt-3">
                {{ $s['sp50_feature_heading'] ?? 'Автономен робот за суво чистење и вакуумирање со интелигентно spot-cleaning детектирање' }}
              </p>
            </div>

            <div class="space-y-3 text-zinc-950">
              @php
                $defaultSP50Features = [
                  ['icon' => '🧠', 'text' => 'AI детекција на над 30 типови нечистотии (99% прецизност)'],
                  ['icon' => '📡', 'text' => '3D LiDAR + мулти-сензорска навигација'],
                  ['icon' => '🧹', 'text' => 'вакуумирање + метeње (dual cleaning систем)'],
                  ['icon' => '⚡', 'text' => 'до 20,000 m²/час spot-cleaning продуктивност'],
                  ['icon' => '🔄', 'text' => 'интелигентно повторно чистење (CRC технологија)'],
                  ['icon' => '🔋', 'text' => 'до ~16 часа автономна работа'],
                  ['icon' => '📱', 'text' => 'апликација за контрола, мониторинг и планирање'],
                ];
                $sp50Features = $s['sp50_features'] ?? $defaultSP50Features;
              @endphp
              @foreach($sp50Features as $f)
              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>{{ $f['icon'] ?? '' }}</span> {{ $f['text'] ?? '' }}
              </div>
              @endforeach
            </div>

            <div class="pt-4">
              <p class="text-lg md:text-xl font-thin text-zinc-700">
                {{ $s['sp50_footer_text'] ?? 'Идеален за големи и динамични простори со висок проток на луѓе' }}
              </p>
            </div>

            <div class="flex gap-4 pt-2">
              <a href="#sp50"
                class="cenobots-glass-btn px-6 py-3 mt-5 rounded-full text-white font-md">
                {{ $s['sp50_btn_1'] ?? 'Дознајте повеќе' }}
              </a>
              <a href="{{ asset('storage/assets/docs/cenobots-brochure.pdf') }}" target="_blank"
                download="cenobots-brochure.pdf"
                class="px-6 py-3 mt-5 rounded-full bg-white text-[#5c59f5] font-medium border border-[#5c59f5] transition-all duration-300 hover:bg-black hover:text-white hover:border-black">
                {{ $s['sp50_btn_2'] ?? 'Брошура' }}
              </a>
            </div>

          </div>
        </div>
      </div>

    </div>
  </section>



</div><!-- /snap-wrapper -->


<!-- CTA SECTION -->
<section class="py-20 bg-zinc-900">
  <div class="max-w-7xl mx-auto px-6 lg:px-8">

    <div class="bg-zinc-800 rounded-3xl p-10 md:p-16 text-center shadow-2xl">

      <!-- Title -->
      <h2 class="text-3xl md:text-5xl font-bold text-white leading-tight">
        {{ $s['cta_heading'] ?? 'Потребна ви е професионална ИТ поддршка?' }}
      </h2>

      <!-- Description -->
      <p class="mt-6 text-lg md:text-xl text-zinc-300 max-w-3xl mx-auto leading-relaxed">
        {{ $s['cta_description'] ?? 'Контактирајте не за понуда, консултација или техничка поддршка. Нудиме решенија за мрежи, видео надзор, сервисирање и комплетна ИТ инфраструктура.' }}
      </p>

      <!-- Buttons -->
      <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">

        <!-- Contact -->
        <a href="/kontakt"
          class="inline-flex items-center justify-center px-8 py-4 rounded-full bg-white text-zinc-900 font-semibold hover:bg-zinc-200 transition duration-300">

          Побарај Понуда

        </a>

        <!-- Phone -->
        <a href="tel:+38970000000"
          class="inline-flex items-center justify-center px-8 py-4 rounded-full border border-zinc-600 text-white font-semibold hover:bg-zinc-700 transition duration-300">

          +389 70 000 000

        </a>

      </div>

    </div>

  </div>
</section>

@endsection
