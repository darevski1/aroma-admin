@extends('layouts.app')
<livewire:navbar />
@section('content')

  <div id="snap-wrapper">

    <!-- ── HERO ─────────────────────────────────────────── -->
    <section class="snap-section bg-gray-100 relative overflow-hidden">

      <!-- Decorative blobs (pointer-events:none so they don't block clicks) -->
      <div class="blob absolute w-72 h-72 bg-blue-200/60 rounded-full blur-3xl top-20 left-10 pointer-events-none"></div>
      <div class="blob-delay absolute w-80 h-80 bg-blue-100/50 rounded-full blur-3xl bottom-10 left-1/3 pointer-events-none"></div>

      <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center gap-12 pt-20">

        <!-- Copy -->
        <div class="md:w-1/2 space-y-6 z-10">
          <h1 class="text-5xl md:text-7xl font-bold">CENOBOTS</h1>
          <p class="text-xl md:text-2xl font-light text-gray-700 leading-relaxed">
            AI-автономни роботи за чистење. Паметно, ефикасно и еколошко решение за секој комерцијален простор.
          </p>
          <a href="#navbar_sticky"
             class="inline-block px-8 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors">
            Дознај повеќе
          </a>
        </div>

        <!-- Robot image -->
        <div class="md:w-1/2 flex justify-center z-10">
          <img
            src="{{asset(" ./../assets/images/cenobot.png")}}"
            alt="Cenobot робот"
            class="hero-image max-h-[60vh] object-contain"
          >
        </div>
      </div>
    </section>
 <section class="bg-[#ff6d63] sticky-section_navbar br" id="navbar_sticky">
  <div class="max-w-7xl mx-auto">
     <div class="flex flex-wrap gap-4">
        <a href="#l3" class="px-5 py-2 border border-black rounded-full text-sm hover:bg-black hover:text-white transition">
          Cenobot L3
        </a>
        <a href="#l50" class="px-5 py-2 border border-black rounded-full text-sm hover:bg-black hover:text-white transition">
          Cenobot L50
        </a>
        <a href="#l4" class="px-5 py-2 border border-black rounded-full text-sm hover:bg-black hover:text-white transition">
          Cenobot L4
        </a>

        <a href="#sp50" class="px-5 py-2 border border-black rounded-full text-sm hover:bg-black hover:text-white transition">
          Cenobot SP50
        </a>

        <a href="#tehnologija" class="px-5 py-2 border border-black rounded-full text-sm hover:bg-black hover:text-white transition">
          Технологија
        </a>

        <a href="#resenija" class="px-5 py-2 border border-black rounded-full text-sm hover:bg-black hover:text-white transition">
          Решенија
        </a>

      </div>
  </div>
</section>
    <!-- ── AREAS OF USE ──────────────────────────────────── -->
    <section class="snap-section bg-white" >
      <div class="max-w-7xl mx-auto px-6 py-16 w-full">

        <div class="text-center mb-12">
          <h2 class="text-4xl md:text-5xl font-normal mb-4">
            Области на <span class="text-teal-500">Примена</span>
          </h2>
          <p class="text-lg md:text-xl font-light text-gray-600">
            Од лобија до ходници — нашите роботи се прилагодуваат на секоја комерцијална средина.
          </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
          <!-- Each card shares the same structure -->
          <div class="area-card relative rounded-xl overflow-hidden shadow-md cursor-pointer">
            <img src="{{asset(" ./../assets/images/retail.jpg")}}"    alt="Трговски Центри" class="w-full h-64 object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/65 to-transparent"></div>
            <h3 class="absolute bottom-4 left-4 text-white font-semibold">Трговски Центри</h3>
          </div>

          <div class="area-card relative rounded-xl overflow-hidden shadow-md cursor-pointer">
            <img src="{{asset(" ./../assets/images/hotel.jpg")}}"     alt="Хотели" class="w-full h-64 object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/65 to-transparent"></div>
            <h3 class="absolute bottom-4 left-4 text-white font-semibold">Хотели</h3>
          </div>

          <div class="area-card relative rounded-xl overflow-hidden shadow-md cursor-pointer">
            <img src="{{asset(" ./../assets/images/hospital.jpg")}}"  alt="Болници" class="w-full h-64 object-cover">
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
    <section class="snap-section bg-white">
      <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center gap-12">

        <!-- Copy -->
        <div class="md:w-1/2 space-y-6">
          <h2 class="text-3xl md:text-5xl font-normal leading-snug">
            Паметна контрола на чистењето во ваши раце
          </h2>
          <p class="text-gray-600 font-extralight leading-relaxed">
            Апликацијата за мобилни уреди на CenoBots L50 носи ново ниво на контрола и прегледност
            во процесот на чистење. Со модерен интерфејс, корисниците во секој момент имаат увид
            во работата на роботот — од тековната позиција до детални информации за перформансите.
          </p>
          <p class="text-gray-600 font-extralight leading-relaxed">
            Преку апликацијата лесно се креираат рути за чистење, добиваат известувања за завршени
            задачи и се следи статусот на уредот. Управувањето станува едноставно, паметно и целосно
            под ваша контрола — каде и да сте.
          </p>
        </div>

        <!-- App screenshot -->
        <div class="md:w-1/2 flex justify-center">
          <img
            src="{{asset(" ./../assets/images/cenobots-app-l50.webp")}}"
            alt="CenoBots L50 апликација"
            class="w-full max-w-md object-cover rounded-2xl shadow-xl hover:scale-105 transition-transform duration-300"
          >
        </div>
      </div>
    </section>

    <!-- ── FULL-COLOUR SNAP SECTIONS ─────────────────────── -->
    <section class="snap-section bg-blue-500 text-white text-4xl font-bold" id="l3">Section 1</section>
    <section class="snap-section bg-red-500  text-white text-4xl font-bold" id="l50">Section 2</section>
    <section class="snap-section bg-green-500 text-white text-4xl font-bold" id="l4">Section 3</section>
    <section class="snap-section bg-gray-900 text-white text-4xl font-bold" id="sp50">Section 4</section>

  </div><!-- /snap-wrapper -->



 <script>
      const snapWrapper = document.getElementById('snap-wrapper');
 

    snapWrapper.addEventListener('scroll', () => {
      navbar.classList.toggle('scrolled', snapWrapper.scrollTop > 60);
    }, { passive: true });

    // ── Hero image: subtle scale-down as user scrolls ──────────
    const heroImage = document.querySelector('.hero-image');

    snapWrapper.addEventListener('scroll', () => {
      const scale = Math.max(0.2, 1 - snapWrapper.scrollTop / 900);
      heroImage.style.transform = `scale(${scale})`;
    }, { passive: true });
  </script>

 @endsection
