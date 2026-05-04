@extends('layouts.app')
<livewire:navbar />
@section('content')

<div>

  <!-- ── HERO ─────────────────────────────────────────── -->
  <section class="snap-section bg-gray-100 relative overflow-hidden  ">

    <!-- Decorative blobs (pointer-events:none so they don't block clicks) -->
    <div class="blob absolute w-72 h-72 bg-blue-200/60 rounded-full blur-3xl top-20 left-10 pointer-events-none"></div>
    <div
      class="blob-delay absolute w-80 h-80 bg-blue-100/50 rounded-full blur-3xl bottom-10 left-1/3 pointer-events-none">
    </div>

    <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center gap-12 pt-20">

      <!-- Copy -->
      <div class="md:w-1/2 space-y-6 z-10">
        <h1 class="text-7xl md:text-7xl font-normal">CENOBOTS</h1>
        <p class="text-xl md:text-2xl font-light text-gray-700 leading-relaxed">
          AI-автономни роботи за чистење. Паметно, ефикасно и еколошко решение за секој комерцијален простор.
        </p>
        <a href="#l3"
          class="px-6 py-3 mt-5 rounded-full bg-[#5c59f5] text-white font-md transition-colors duration-300 hover:bg-black">
          Дознајте повеќе
        </a>
      </div>

      <!-- Robot image -->
      <div class="md:w-1/2 flex justify-center z-10">
        <img id="hero-image" src="{{ asset('./../assets/images/cenobot.png') }}" alt="Cenobot робот"
          class="hero-image max-h-[60vh] object-contain transition-transform duration-200 will-change-transform">
      </div>
    </div>
  </section>
  <section class="bg-[#ff6d63] sticky-section_navbar br" id="navbar_sticky">
    <div class="max-w-7xl mx-auto">
      <div class="flex flex-wrap gap-4">
        <a href="#l3"
          class="px-5 py-2 border border-black rounded-full text-md hover:bg-black hover:text-white transition">
          Cenobot L3
        </a>
        <a href="#l50"
          class="px-5 py-2 border border-black rounded-full text-md hover:bg-black hover:text-white transition">
          Cenobot L50
        </a>
        <a href="#l4"
          class="px-5 py-2 border border-black rounded-full text-md hover:bg-black hover:text-white transition">
          Cenobot L4
        </a>

        <a href="#sp50"
          class="px-5 py-2 border border-black rounded-full text-md hover:bg-black hover:text-white transition">
          Cenobot SP50
        </a>

        <a href="#tehnologija"
          class="px-5 py-2 border border-black rounded-full text-md hover:bg-black hover:text-white transition">
          Технологија
        </a>

        <a href="#resenija"
          class="px-5 py-2 border border-black rounded-full text-md hover:bg-black hover:text-white transition">
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
          Области на <span class="colorBlue">Примена</span>
        </h2>
        <p class="text-xl md:text-2xl font-light text-gray-700 leading-relaxed">
          Од лобија до ходници — нашите роботи се прилагодуваат на секоја комерцијална средина.
        </p>
      </div>

      <div class=" grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        <!-- Each card shares the same structure -->
        <div class="area-card relative rounded-xl overflow-hidden shadow-md cursor-pointer">
          <img src="{{asset(" ./../assets/images/retail.jpg")}}" alt="Трговски Центри" class="w-full h-64 object-cover">
          <div class="absolute inset-0 bg-gradient-to-t from-black/65 to-transparent"></div>
          <h3 class="absolute bottom-4 left-4 text-white font-semibold">Трговски Центри</h3>
        </div>

        <div class="area-card relative rounded-xl overflow-hidden shadow-md cursor-pointer">
          <img src="{{asset(" ./../assets/images/hotel.jpg")}}" alt="Хотели" class="w-full h-64 object-cover">
          <div class="absolute inset-0 bg-gradient-to-t from-black/65 to-transparent"></div>
          <h3 class="absolute bottom-4 left-4 text-white font-semibold">Хотели</h3>
        </div>

        <div class="area-card relative rounded-xl overflow-hidden shadow-md cursor-pointer">
          <img src="{{asset(" ./../assets/images/hospital.jpg")}}" alt="Болници" class="w-full h-64 object-cover">
          <div class="absolute inset-0 bg-gradient-to-t from-black/65 to-transparent"></div>
          <h3 class="absolute bottom-4 left-4 text-white font-semibold">Болници</h3>
        </div>

        <div class="area-card relative rounded-xl overflow-hidden shadow-md cursor-pointer">
          <img src="{{asset(" ./../assets/images/education.jpg")}}" alt="Образование" class="w-full h-64 object-cover">
          <div class="absolute inset-0 bg-gradient-to-t from-black/65 to-transparent"></div>
          <h3 class="absolute bottom-4 left-4 text-white font-semibold">Образование</h3>
        </div>
      </div>
    </div>
  </section>

  <!-- ── APP SECTION ───────────────────────────────────── -->
  <section class="snap-section h100vh bg-zinc-100" id="tehnologija">
    <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center gap-12">

      <!-- Copy -->
      <div class="md:w-1/2 space-y-6">
        <h2 class="text-3xl md:text-5xl font-normal leading-snug">
          Паметна контрола на чистењето во ваши раце
        </h2>
        <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950">
          Апликацијата за мобилни уреди на CenoBots L50 носи ново ниво на контрола и прегледност
          во процесот на чистење. Со модерен интерфејс, корисниците во секој момент имаат увид
          во работата на роботот — од тековната позиција до детални информации за перформансите.
        </p>
        <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950">
          Преку апликацијата лесно се креираат рути за чистење, добиваат известувања за завршени
          задачи и се следи статусот на уредот. Управувањето станува едноставно, паметно и целосно
          под ваша контрола — каде и да сте.
        </p>
      </div>

      <!-- App screenshot -->
      <div class="md:w-1/2 flex justify-center">
        <img src="{{asset(" ./../assets/images/cenobots-app-l50.webp")}}" alt="CenoBots L50 апликација"
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
          CenoBots L3
        </h2>

        <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950">
          Компактен автономен робот за професионално чистење со AI навигација и висока ефикасност.
          Идеален за канцеларии, маркети, хотели и комерцијални простори.
        </p>

        <div class="swiper mySwiper">
          <div class="swiper-wrapper">
            <div class="swiper-slide">
              <img src="{{asset(" ./../assets/images/cenobots/L3-images/01.png")}}" alt="CenoBots L50 апликација"
                class="w-full max-w-xl  hover:scale-105 transition-transform duration-300">
            </div>
            <div class="swiper-slide">
              <img src="{{asset(" ./../assets/images/cenobots/L3-images/02.png")}}" alt="CenoBots L50 апликација"
                class="w-full max-w-md  hover:scale-105 transition-transform duration-300">
            </div>
            <div class="swiper-slide">
              <img src="{{asset(" ./../assets/images/cenobots/L3-images/03.png")}}" alt="CenoBots L50 апликација"
                class="w-full max-w-md  hover:scale-105 transition-transform duration-300">
            </div>
            <div class="swiper-slide">
              <img src="{{asset(" ./../assets/images/cenobots/L3-images/04.png")}}" alt="CenoBots L50 апликација"
                class="w-full max-w-md  hover:scale-105 transition-transform duration-300">
            </div>
            <div class="swiper-slide">
              <img src="{{asset(" ./../assets/images/cenobots/L3-images/05.png")}}" alt="CenoBots L50 апликација"
                class="w-full max-w-md  hover:scale-105 transition-transform duration-300">
            </div>
            <div class="swiper-slide">
              <img src="{{asset(" ./../assets/images/cenobots/L3-images/06.png")}}" alt="CenoBots L50 апликација"
                class="w-full max-w-md  hover:scale-105 transition-transform duration-300">
            </div>


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
              Автономно професионално чистење со паметна роботика и целосна контрола
            </p>
          </div>

          <div class="space-y-3 text-zinc-950">

            <div class="flex text-lg md:text-xl font-thin items-center gap-2">
              <span>🧠</span> AI + SLAM навигација
            </div>

            <div class="flex text-lg md:text-xl font-thin items-center gap-2">
              <span>📡</span> 3D LiDAR детекција на пречки
            </div>

            <div class="flex  text-lg md:text-xl font-thin items-center gap-2">
              <span>💧</span> 25L чиста / 25L валкана вода
            </div>

            <div class="flex text-lg md:text-xl font-thin items-center gap-2">
              <span>⚡</span> до 2,000 m²/час покриеност
            </div>

            <div class="flex text-lg md:text-xl font-thin items-center gap-2">
              <span>🔄</span> автоматско докирање и полнење
            </div>

            <div class="flex text-lg md:text-xl font-thin items-center gap-2">
              <span>📱</span> далечинска контрола и распоредување
            </div>

          </div>

          <div class="pt-4">
            <p class="text-lg md:text-xl font-thin text-zinc-700">
              100% автономно чистење без човечка интервенција
            </p>
          </div>

          <div class="flex gap-4 pt-2">
            <a href="#l3"
              class="px-6 py-3 mt-5 rounded-full bg-[#5c59f5] text-white font-md transition-colors duration-300 hover:bg-black">
              Дознајте повеќе
            </a>
            <a href="#l3"
              class="px-6 py-3 mt-5 rounded-full bg-white text-[#5c59f5] font-medium border border-[#5c59f5] transition-all duration-300 hover:bg-black hover:text-white hover:border-black">
              Брошура
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
          CenoBots l50
        </h2>

        <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950">
          CenoBots L3 е индустриска роботска машина дизајнирана за автоматизација
          на производствени процеси. Овозможува висока прецизност, брзина и
          сигурност во работењето, што ја прави идеална за модерни фабрики.
        </p>

        <img src="{{asset(" ./../assets/images/cenobots/L3-images/01.png")}}" alt="CenoBots L50 апликација"
          class="w-full max-w-md  hover:scale-135 transition-transform duration-300">
      </div>

      <!-- RIGHT SIDE (EMPTY) -->
      <div class="hidden md:block">
        <div>
          <div class="space-y-6">

            <div>
              <p class="text-lg md:text-xl font-normal text-zinc-950 mt-3">
                Индустриски автономен робот за чистење на големи површини со висока продуктивност и напредна роботика
              </p>
            </div>

            <div class="space-y-3 text-zinc-950">

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>🧠</span> AI + SLAM навигација со висока прецизност
              </div>

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>📡</span> 3D LiDAR + мулти-сензорска детекција
              </div>

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>💧</span> голем капацитет на резервоари (чиста / валкана вода)
              </div>

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>⚡</span> висока покриеност за индустриски простори
              </div>

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>🔄</span> автоматско докирање, полнење и празнење
              </div>

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>📱</span> централизирана контрола и управување преку апликација
              </div>

            </div>

            <div class="pt-4">
              <p class="text-lg md:text-xl font-thin text-zinc-700">
                Максимална ефикасност за големи комерцијални и индустриски објекти
              </p>
            </div>

            <div class="flex gap-4 pt-2">
              <a href="#l50"
                class="px-6 py-3 mt-5 rounded-full bg-[#5c59f5] text-white font-md transition-colors duration-300 hover:bg-black">
                Дознајте повеќе
              </a>
              <a href="#l50"
                class="px-6 py-3 mt-5 rounded-full bg-white text-[#5c59f5] font-medium border border-[#5c59f5] transition-all duration-300 hover:bg-black hover:text-white hover:border-black">
                Брошура
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
          CenoBots l4
        </h2>

        <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950">
          CenoBots L3 е индустриска роботска машина дизајнирана за автоматизација
          на производствени процеси. Овозможува висока прецизност, брзина и
          сигурност во работењето, што ја прави идеална за модерни фабрики.
        </p>

        <img src="{{asset(" ./../assets/images/cenobots/L3-images/01.png")}}" alt="CenoBots L50 апликација"
          class="w-full max-w-md  hover:scale-15 transition-transform duration-300">
      </div>

      <!-- RIGHT SIDE (EMPTY) -->
      <div class="hidden md:block">
        <!-- Можеш подоцна тука да додадеш нешто -->
        <div>
          <div class="space-y-6">

            <div>
              <p class="text-lg md:text-xl font-normal text-zinc-950 mt-3">
                Компактен и интелигентен робот за автономно чистење во тесни и динамични простори
              </p>
            </div>

            <div class="space-y-3 text-zinc-950">

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>🧠</span> AI + SLAM навигација со Nvidia процесор
              </div>

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>📡</span> 3D LiDAR + depth камери за прецизна детекција
              </div>

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>💧</span> ~34–38L резервоари за чиста и валкана вода
              </div>

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>⚡</span> до ~2,000 m²/час продуктивност
              </div>

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>📐</span> дизајниран за тесни простори и коридори
              </div>

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>🔄</span> автоматско полнење, мапирање и 24/7 работа
              </div>

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>📱</span> контрола преку мобилна апликација и touchscreen
              </div>

            </div>

            <div class="pt-4">
              <p class="text-lg md:text-xl font-thin text-zinc-700">
                Оптимизиран за максимална маневрибилност и ефикасност во сложени простори
              </p>
            </div>

            <div class="flex gap-4 pt-2">
              <a href="#l4"
                class="px-6 py-3 mt-5 rounded-full bg-[#5c59f5] text-white font-md transition-colors duration-300 hover:bg-black">
                Дознајте повеќе
              </a>
              <a href="#l4"
                class="px-6 py-3 mt-5 rounded-full bg-white text-[#5c59f5] font-medium border border-[#5c59f5] transition-all duration-300 hover:bg-black hover:text-white hover:border-black">
                Брошура
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
          CenoBots SP50
        </h2>

        <h2 class="text-3xl md:text-3xl font-thin">
          Автономен робот за индустриско чистење со висока прецизност
        </h2>

        <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950">
          Паметно, брзо и целосно автоматизирано чистење на големи површини. SP50 користи AI навигација и LiDAR
          технологија за прецизно детектирање и отстранување на нечистотии – без потреба од човечка интервенција.
        </p>
        <div class="swiper mySwiper">
          <div class="swiper-wrapper">
            <div class="swiper-slide">
              <img src="{{asset(" ./../assets/images/cenobots/SP50/1.png")}}" alt="CenoBots L50 апликација"
                class="w-full max-w-xl  hover:scale-105 transition-transform duration-300">
            </div>
            <div class="swiper-slide">
              <img src="{{asset(" ./../assets/images/cenobots/SP50/1.png")}}" alt="CenoBots L50 апликација"
                class="w-full max-w-md  hover:scale-105 transition-transform duration-300">
            </div>
            <div class="swiper-slide">
              <img src="{{asset(" ./../assets/images/cenobots/SP50/2.png")}}" alt="CenoBots L50 апликација"
                class="w-full max-w-md  hover:scale-105 transition-transform duration-300">
            </div>
            <div class="swiper-slide">
              <img src="{{asset(" ./../assets/images/cenobots/SP50/3.png")}}" alt="CenoBots L50 апликација"
                class="w-full max-w-md  hover:scale-105 transition-transform duration-300">
            </div>
            <div class="swiper-slide">
              <img src="{{asset(" ./../assets/images/cenobots/SP50/4.png")}}" alt="CenoBots L50 апликација"
                class="w-full max-w-md  hover:scale-105 transition-transform duration-300">
            </div>
            <div class="swiper-slide">
              <img src="{{asset(" ./../assets/images/cenobots/SP50/5.png")}}" alt="CenoBots L50 апликација"
                class="w-full max-w-md  hover:scale-105 transition-transform duration-300">
            </div>


          </div>
          <div class="swiper-button-next"></div>
          <div class="swiper-button-prev"></div>
          <div class="swiper-pagination"></div>
        </div>

      </div>

      <!-- RIGHT SIDE (EMPTY) -->
      <div class="hidden md:block">
        <div>
          <div class="space-y-6">

            <div>
              <p class="text-lg md:text-xl font-normal text-zinc-950 mt-3">
                Автономен робот за суво чистење и вакуумирање со интелигентно spot-cleaning детектирање
              </p>
            </div>

            <div class="space-y-3 text-zinc-950">

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>🧠</span> AI детекција на над 30 типови нечистотии (99% прецизност)
              </div>

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>📡</span> 3D LiDAR + мулти-сензорска навигација
              </div>

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>🧹</span> вакуумирање + метeње (dual cleaning систем)
              </div>

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>⚡</span> до 20,000 m²/час spot-cleaning продуктивност
              </div>

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>🔄</span> интелигентно повторно чистење (CRC технологија)
              </div>

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>🔋</span> до ~16 часа автономна работа
              </div>

              <div class="flex text-lg md:text-xl font-thin items-center gap-2">
                <span>📱</span> апликација за контрола, мониторинг и планирање
              </div>

            </div>

            <div class="pt-4">
              <p class="text-lg md:text-xl font-thin text-zinc-700">
                Идеален за големи и динамични простори со висок проток на луѓе
              </p>
            </div>

            <div class="flex gap-4 pt-2">
              <a href="#sp50"
                class="px-6 py-3 mt-5 rounded-full bg-[#5c59f5] text-white font-md transition-colors duration-300 hover:bg-black">
                Дознајте повеќе
              </a>
              <a href="#sp50"
                class="px-6 py-3 mt-5 rounded-full bg-white text-[#5c59f5] font-medium border border-[#5c59f5] transition-all duration-300 hover:bg-black hover:text-white hover:border-black">
                Брошура
              </a>
            </div>

          </div>
        </div>
      </div>

    </div>
  </section>



</div><!-- /snap-wrapper -->






@endsection

<script>
  const sections = document.querySelectorAll('.section-hidden');

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('section-show');
    }
  });
}, {
  threshold: 0.2
});

sections.forEach(section => {
  observer.observe(section);
});
</script>