@extends('layouts.app')
<livewire:navbar />
@section('content')





<section class="pt-32 pb-20 intro-header bg">
  <!-- Hero -->
  <div class="max-w-[85rem] mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Grid -->
    <div class="grid md:grid-cols-2 gap-4 md:gap-8 xl:gap-20 md:items-center">
      <div>
        <h1 class="block text-3xl  text-foreground sm:text-4xl lg:text-7xl lg:leading-tight"> Комплетни ИТ
          решенија за вашиот бизнис</h1>
        <p class="mt-3 text-lg text-foreground">Нудиме целосна ИТ поддршка за компании – од инфраструктура и одржување
          до
          софтверски решенија и безбедност. .</p>

        <!-- Buttons -->
        <div class="mt-7 grid gap-3 w-full sm:inline-flex mt-10">
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

      <div class="relative ms-4">
        <img class="w-full rounded-md"
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
        <h2 class="text-6xl md:text-6xl font-normal mb-4">
          Наши Услуги
        </h2>
        <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950">
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
        Нудиме продажба и изнајмување на индустриски роботи <b>(Cenobots) </b> за автоматизација на производствени
        процеси.
        Зголемете ја продуктивноста, намалете ги трошоците и оптимизирајте ја вашата работа со модерни роботски
        решенија.
      </p>

    </div>

    <div class="mt-10 relative max-w-5xl mx-auto">
      <div class="w-full h-96 sm:h-[480px] bg-center bg-cover bg-no-repeat rounded-xl"
        style="background-image: url('/assets/images/printer-hero.jpg');">
      </div>

      <div class="absolute inset-0 size-full">
        <div class="flex flex-col justify-center items-center size-full">
          <a href="#learn" class="flex items-center px-6 py-3 rounded-full text-lg int-btn transition gap-2">
            <img src="{{asset(" ./../assets/images/logo_button.svg")}}" alt="icon" class="w-9 h-9 spin" />
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



<section class="p-4  bgpacebot bg">
  <div class="wl32">
    <div class="mls flex items-center justify-center min-h-screen p-4">
      <div class="relative z-10 max-w-5xl w-full text-center text-zinc-950">
        <h2 class="text-3xl md:text-3xl font-thin mb-6 leading-tight mt-10">
          explore
        </h2>
        <img src="{{ asset('./assets/images/logo/garmin.svg') }}" alt="Garmin" class="mx-auto mb-6 w-100 h-auto" />


        <p class="font-normal md:text-3xl mb-9 text-zinc-950 mt-10">
          Паметни часовници и GPS уреди дизајнирани за авантура, спорт и секојдневие.
        </p>

        <div class="flex justify-center mt-9">
          <a href="https://exploregarmin.mk/" target="_blank"
            class="flex items-center px-9 py-5 rounded-full text-lg bg-gray-200  text-black font-medium hover:bg-gray-200 transition gap-2">
            <img src="{{ asset('./assets/images/explore_garmin.png') }}" alt="explore garmin" />
          </a>
        </div>

      </div>
    </div>
    <div class="mls">


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