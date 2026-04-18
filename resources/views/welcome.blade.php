@extends('layouts.app')
<livewire:navbar />
@section('content')


<section class="pt-32 pb-20 intro-header bg">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
      <div class="space-y-10">
        <div class="space-y-6">
          <div class="inline-block">

          </div>
          <h1 class="text-7xl md:text-7xl font-normal">
            <span class="tech-text">
              Комплетни ИТ решенија за вашиот бизнис
            </span>
          </h1>
          <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950">

            Нудиме целосна ИТ поддршка за компании – од инфраструктура и одржување до софтверски решенија и безбедност.
          </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-6">

          <a href="#learn" class="flex items-center px-6 py-3 rounded-full text-lg int-btn transition gap-2">
            <img src="{{asset(" ./../assets/images/logo_button.svg")}}" alt="icon" class="w-9 h-9 spin" />
            Learn More
          </a>
        </div>

      </div>
      <div class="relative">
        <div class="relative overflow-hidden rounded-3xl shadow-2xl">
          <img alt="TechTonic Solutions" class="w-full h-96 object-cover" src="/assets/hero-software-us1PMivG.jpg" />
          <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent">
          </div>
          <div class="absolute bottom-6 left-6">
            <div class="bg-white/90 backdrop-blur-sm px-4 py-2 rounded-lg">
              <div class="flex items-center space-x-2">
                <svg class="lucide lucide-wrench h-5 w-5 text-primary" fill="none" height="24" stroke="currentColor"
                  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewbox="0 0 24 24" width="24"
                  xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z">
                  </path>
                </svg>
                <span class="font-medium text-foreground">
                  Expert Solutions
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- You need something to scroll to -->


<section class="bg-[#ff6d63] sticky-section br">
  <div class="max-w-7xl mx-auto">
    <span class="block text-lg md:text-xl lg:text-2xl font-medium text-center p-6 text-white">
      Illuminating Brilliance in Digital Experiences, Content and Creativity
    </span>
  </div>
</section>

<div class="container mx-auto max-h-[300px] flex items-center overflow-hidden">
  <div class="swiper w-full h-full">
    <div class="swiper-wrapper h-full flex items-center">

      <div class="swiper-slide flex items-center justify-center">
        <div class="logo-box">
          <img src="{{ asset('./../assets/images/logo/HP_Logo_0.svg') }}" alt="icon" class="w-22 h-22" />
        </div>
      </div>

      <div class="swiper-slide flex items-center justify-center">
        <div class="logo-box">
          <img src="{{ asset('./../assets/images/logo/Dell_Logo_Alternative_0.svg') }}" alt="icon" class="w-22 h-22" />
        </div>
      </div>

      <div class="swiper-slide flex items-center justify-center">
        <div class="logo-box">
          <img src="{{ asset('./../assets/images/logo/Lenovo_idDXuX8rvi_0.svg') }}" alt="icon" class="w-22 h-22" />
        </div>
      </div>

      <div class="swiper-slide flex items-center justify-center">
        <div class="logo-box">
          <img src="{{ asset('./../assets/images/logo/Ricoh_Australia_Logo_0.svg') }}" alt="icon" class="w-22 h-22" />
        </div>
      </div>

      <div class="swiper-slide flex items-center justify-center">
        <div class="logo-box">
          <img src="{{ asset('./../assets/images/logo/Canon_ide_p-tcLS_0.svg') }}" alt="icon" class="w-22 h-22" />
        </div>
      </div>

      <div class="swiper-slide flex items-center justify-center">
        <div class="logo-box">
          <img src="{{ asset('./../assets/images/logo/MikroTik_idAi6_gbXf_1.svg') }}" alt="icon" class="w-22 h-22" />
        </div>
      </div>

      <div class="swiper-slide flex items-center justify-center">
        <div class="logo-box">
          <img src="{{ asset('./../assets/images/logo/Fortinet_idZhbQEBqi_0.svg') }}" alt="icon" class="w-22 h-22" />
        </div>
      </div>

    </div>
  </div>
</div>

<section class="w-full py-16 br">
  <div class="max-w-7xl mx-auto px-4">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

      <!-- Left: Intro Text -->
      <div class="flex flex-col justify-center text-center md:text-left">
        <h2 class="text-5xl md:text-4xl font-normal text-zinc-950 mb-4">
          Наши Услуги
        </h2>
        <p class="text-lg md:text-lg text-zinc-950 mb-6 font-thin">
          Нудиме широк спектар на професионални ИТ услуги прилагодени на вашите потреби.
          Од консалтинг и инсталации до одржување и поддршка откријте што можеме да направиме за вас.
        </p>
      </div>

      <!-- Right: Accordion -->
      <div class="space-y-4">

        <!-- Item 1 -->
        <details class="border border-zinc-300 rounded-lg p-4 group">
          <summary class="cursor-pointer font-thin text-zinc-800">
            Мрежни инсталации и одржување на мрежи
          </summary>
          <p class="mt-2 text-gray-600">
            Планирање, инсталација и одржување на LAN/Wi-Fi мрежи за стабилна и сигурна инфраструктура.
          </p>
          <a href="/services/network" class="inline-block mt-2 text-blue-600 font-medium hover:underline">
            Прочитај повеќе →
          </a>
        </details>

        <!-- Item 2 -->
        <details class="border border-zinc-300 rounded-lg p-4 group">
          <summary class="cursor-pointer font-thin text-zinc-800">
            Одржување на компјутерски системи
          </summary>
          <p class="mt-2 text-gray-600">
            Редовен сервис и поддршка за компјутери, сервери и ИТ опрема.
          </p>
          <a href="/services/it-support" class="inline-block mt-2 text-blue-600 font-medium hover:underline">
            Прочитај повеќе →
          </a>
        </details>

        <!-- Item 3 -->
        <details class="border border-zinc-300 rounded-lg p-4 group">
          <summary class="cursor-pointer font-thin text-zinc-800">
            Видео надзор и контрола на пристап
          </summary>
          <p class="mt-2 text-gray-600">
            Инсталација на CCTV системи и решенија за безбедност.
          </p>
          <a href="/services/security" class="inline-block mt-2 text-blue-600 font-medium hover:underline">
            Прочитај повеќе →
          </a>
        </details>

        <!-- Item 4 -->
        <details class="border border-zinc-300 rounded-lg p-4 group">
          <summary class="cursor-pointer font-thin text-zinc-800">
            Сервис и продажба на принтери
          </summary>
          <p class="mt-2 text-gray-600">
            Продажба, сервис и изнајмување на принтери и мултифункционални уреди.
          </p>
          <a href="/services/printers" class="inline-block mt-2 text-blue-600 font-medium hover:underline">
            Прочитај повеќе →
          </a>
        </details>

        <!-- Item 5 -->
        <details class="border border-zinc-300 rounded-lg p-4 group">
          <summary class="cursor-pointer font-thin text-zinc-800">
            Развој на софтверски решенија
          </summary>
          <p class="mt-2 text-gray-600">
            Custom веб и софтверски решенија според вашите потреби.
          </p>
          <a href="/services/software" class="inline-block mt-2 text-blue-600 font-medium hover:underline">
            Прочитај повеќе →
          </a>
        </details>

      </div>
    </div>

  </div>
</section>
<section class="bg-gray-70 py-18 cenobot-section">
  <div class="max-w-7xl mx-auto px-6 lg:px-8">

    <div class="grid lg:grid-cols-2 gap-10 items-center">

      <!-- LEFT CONTENT -->
      <div>
        <img src="{{asset(" ./../assets/images/logo/logocenotbot.png")}}" alt="icon" class="w-160 height-auto" />
          <h2 class="text-5xl md:text-4xl font-normal text-zinc-950 mb-4">
          Cenobots  Индустриски решенија за чистење
        </h2>

        <p class="text-lg md:text-lg  font-thin text-zinc-950  mb-6">
          Нудиме продажба и изнајмување на индустриски роботи (Cenobots) за автоматизација на производствени процеси.
          Зголемете ја продуктивноста, намалете ги трошоците и оптимизирајте ја вашата работа со модерни роботски
          решенија.
        </p>

        <ul class="space-y-3 mb-6 text-zinc-800">
          <li class="flex items-center gap-2 font-thin"> <img src="{{asset(" ./../assets/images/icons/check.svg")}}" alt="icon"
              class="w-5 h-5" /> Продажба на индустриски
            роботи</li>
          <li class="flex items-center gap-2 font-thin"> <img src="{{asset(" ./../assets/images/icons/check.svg")}}" alt="icon"
              class="w-5 h-5" /> Изнајмување на краток и долг рок</li>
          <li class="flex items-center gap-2 font-thin"> <img src="{{asset(" ./../assets/images/icons/check.svg")}}" alt="icon"
              class="w-5 h-5" /> Инсталација и поддршка</li>
          <li class="flex items-center gap-2 font-thin"> <img src="{{asset(" ./../assets/images/icons/check.svg")}}" alt="icon"
              class="w-5 h-5" /> Прилагодени решенија</li>
        </ul>

        <div class="flex flex-wrap gap-4">
              <a href="/services/cenobots" class="inline-block mt-6 px-8 py-4 int-btn transition">
        Дознај повеќе
      </a>
        </div>
      </div>

      <!-- RIGHT IMAGE -->
      <div class="relative h-[70vh] w-full">

        <img src="{{ asset('assets/images/cenobots.jpg') }}" alt="Cenobots"
          class="absolute inset-0 w-full h-full object-cover rounded-2xl shadow-xl ">

      </div>

    </div>

  </div>
</section>

<section class="py-16 px-4 md:px-8 ct">
  <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

    <!-- LEFT: Заглавија и Опис -->
    <div class="space-y-6">
      <h2 class="text-4xl md:text-4xl font-normal text-zinc-950">
        Изнајмување, продажба и сервис на печатачи
      </h2>
      <p class="text-lg md:text-lg   text-zinc-950">
        Одржете го вашето печатење непречено со нашите професионални решенија за печатачи.
        Нудиме комплетни услуги — од поправка и одржување до продажба и снабдување со тонер.
      </p>

      <!-- Услуги -->
      <div class="space-y-4">
        <div>
          <h3 class="font-normal md:text-lg   text-zinc-950"><img src="{{asset(" ./../assets/images/icons/check.svg")}}" alt="icon"
              class="w-5 h-5" />Сервис на печатачи</h3>
          <p class="md:text-lg  font-thin text-zinc-950 text-sm">Брза и професионална поправка на сите типови на печатачи.</p>
        </div>
        <div>
          <h3 class="font-normal md:text-lg   text-zinc-950"><img src="{{asset(" ./../assets/images/icons/check.svg")}}" alt="icon"
              class="w-5 h-5" />Продажба на печатачи</h3>
          <p class="md:text-lg  font-thin text-zinc-950 text-sm">Продажба на нови и реновирани печатачи за вашата канцеларија.
          </p>
        </div>
        <div>
          <h3 class="font-normal md:text-lg   text-zinc-950">Снабдување со тонер</h3>
          <p class="md:text-lg  font-thin text-zinc-950 text-sm">Квалитетен тонер за непречено работење на вашите печатачи.</p>
        </div>
      </div>

      <!-- CTA копче -->
      <a href="/services/printers" class="inline-block mt-6 px-8 py-4 int-btn transition">
        Дознај повеќе
      </a>

    </div>

    <!-- RIGHT: Слика -->
    <div class="flex justify-center lg:justify-end">
      <img src="{{ asset('assets/images/printer1.png') }}" alt="Копирни уреди"
        class="rounded-xl   w-full max-w-lg lg:max-w-xl object-cover">
    </div>

  </div>
</section>
<!-- Section 1: Hero Image with zoom in/out on scroll -->
<section
  class="relative pt-[100px] pb-[70px] px-4 md:px-8 min-h-[900px] flex items-center justify-center overflow-hidden"
  id="hero-section">

  <div class="absolute inset-0 overflow-hidden">
    <img src="{{ asset('./assets/images/fenix-serija-2048x582.webp') }}" alt="Garmin" id="hero-image"
      class="w-full h-full object-contain object-center transition-transform duration-200" />
  </div>
</section>


<section
  class="relative py-[70px] px-4 md:px-8 min-h-[200px] flex items-center justify-center overflow-hidden bg-white">
  <div class="relative z-10 max-w-5xl w-full text-center text-zinc-950">
    <h2 class="text-8xl md:text-8xl font-bold mb-6 leading-tight mt-10">
      Истражи со
    </h2>
    <img src="{{ asset('./assets/images/logo/garmin.svg') }}" alt="Garmin" class="mx-auto mb-6 w-202 h-auto" />   


    <p class="font-normal md:text-3xl mb-9 text-zinc-950 mt-10">
      Паметни часовници и GPS уреди дизајнирани за авантура, спорт и секојдневие.
    </p>

    <div class="flex justify-center mt-9">
      <a href="https://exploregarmin.mk/" target="_blank"
        class="flex items-center px-6 py-3 rounded-full text-lg bg-gray-100  text-black font-medium hover:bg-gray-200 transition gap-2">
        <img src="{{ asset('./assets/images/explore_garmin.png') }}" alt="icon" />
      </a>
    </div>

  </div>
</section>





@endsection