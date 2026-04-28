@extends('layouts.app')
<livewire:navbar />
@section('content')

  <div  >

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
<section class="snap-section py-20 cennobotl1" id="l3">
  <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
    
    <!-- LEFT SIDE -->
    <div class="space-y-6">
      <h2 class="text-7xl md:text-7xl font-normal text-white">
        CenoBots L3
      </h2>

      <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950">
        Компактен автономен робот за чистење со AI навигација и висока ефикасност.
        Идеален за комерцијални простори како канцеларии, маркети и хотели.
      </p>

     <img 
        src="{{asset(" ./../assets/images/cenobots/L3-images/01.png")}}" 
        alt="CenoBots L3" 
        class="w-full   object-contain"
      >
    </div>

    <!-- RIGHT SIDE -->
    <div>
      
      <!-- Tabs -->
      <div class="flex gap-4 mb-6">
        <button onclick="openTab('specs')" class="tab-btn bg-white text-black px-4 py-2 rounded-xl">Спецификации</button>
        <button onclick="openTab('battery')" class="tab-btn bg-gray-700 px-4 py-2 rounded-xl">Батерија</button>
        <button onclick="openTab('performance')" class="tab-btn bg-gray-700 px-4 py-2 rounded-xl">Перформанси</button>
      </div>

      <!-- Tab Content -->
      <div id="specs" class="tab-content">
        <ul class="space-y-2 text-gray-300">
          <li>Ширина на чистење: 400 mm</li>
          <li>Притисок на четка: 18 kg</li>
          <li>Резервоар (чиста вода): 25 L</li>
          <li>Резервоар (отпадна вода): 25 L</li>
          <li>Димензии: 765 × 572 × 1000 mm</li>
        </ul>
      </div>

      <div id="battery" class="tab-content hidden">
        <ul class="space-y-2 text-gray-300">
          <li>Време на работа: до 4 часа</li>
          <li>Време на полнење: ~1.5 часа</li>
          <li>Автоматско враќање на полнач</li>
        </ul>
      </div>

      <div id="performance" class="tab-content hidden">
        <ul class="space-y-2 text-gray-300">
          <li>Продуктивност: до 2000 m²/h</li>
          <li>Покриеност: до 3000 m² по циклус</li>
          <li>Навигација: 3D LiDAR + AI</li>
          <li>Автоматско избегнување пречки</li>
        </ul>
      </div>
    </div>

  </div>
</section>

 <section class="snap-section   py-20  " id="l50">
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

      <img 
        src="{{asset(" ./../assets/images/cenobots/L50/1.png")}}" 
        alt="CenoBots L50" 
        class="w-full   object-contain"
      >
    </div>

    <!-- RIGHT SIDE (EMPTY) -->
    <div class="hidden md:block">
      <!-- Можеш подоцна тука да додадеш нешто -->
    </div>

  </div>
</section>
 <section class="snap-section   py-20 " id="l4">
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

      <img 
        src="{{asset(" ./../assets/images/cenobots/L4/1.png")}}" 
        alt="CenoBots L3" 
        class="w-full   object-contain"
      >
    </div>

    <!-- RIGHT SIDE (EMPTY) -->
    <div class="hidden md:block">
      <!-- Можеш подоцна тука да додадеш нешто -->
    </div>

  </div>
</section>
 <section class="snap-section   py-20 " id="sp50">
  <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
    
    <!-- LEFT SIDE -->
    <div class="space-y-6">
     <h2 class="text-7xl md:text-7xl font-normal">
        CenoBots SP50
      </h2>
      
     <h2 class="text-5xl md:text-5xl font-thin">
       Автономен робот за индустриско чистење со висока прецизност
      </h2>

      <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950">
       Паметно, брзо и целосно автоматизирано чистење на големи површини. SP50 користи AI навигација и LiDAR технологија за прецизно детектирање и отстранување на нечистотии – без потреба од човечка интервенција.
      </p>

      <img 
        src="{{asset(" ./../assets/images/cenobots/SP50/1.png")}}" 
        alt="CenoBots L3" 
        class="w-full   object-contain"
      >
    </div>

    <!-- RIGHT SIDE (EMPTY) -->
    <div class="hidden md:block">
      <!-- Можеш подоцна тука да додадеш нешто -->
    </div>

  </div>
</section>
     
 

  </div><!-- /snap-wrapper -->



@endsection


<script>
  function openTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.getElementById(tabId).classList.remove('hidden');

    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.classList.remove('bg-white','text-black');
      btn.classList.add('bg-gray-700');
    });

    event.target.classList.add('bg-white','text-black');
    event.target.classList.remove('bg-gray-700');
  }
</script>