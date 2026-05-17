@extends('layouts.app')
<livewire:navbar />
@section('content')
<!-- SERVICES SECTION -->
<section class="bg-white py-20">
  <div class="max-w-7xl mx-auto px-6 lg:px-8">

    <!-- Section Title -->
    <div class="text-center mb-16">
      <h2 class="block text-3xl  text-foreground sm:text-4xl lg:text-7xl lg:leading-tight">
        Наши Услуги
      </h2>

      <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950">
        Нудиме професионални ИТ решенија за компании и физички лица,
        со брза поддршка, сигурност и современа технологија.
      </p>
    </div>

    <!-- Services Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

      <!-- Service 1 -->
      <div class="bg-gray-50 rounded-2xl p-8 shadow-sm hover:shadow-xl transition duration-300 border border-gray-100">
        <div class="mb-6">
          <div class="w-16 h-16 rounded-xl bg-blue-100 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M8 9h8M8 13h6m-9 8h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg>
          </div>
        </div>

        <h3 class="text-2xl font-semibold text-gray-900 mb-4">
          Мрежни инсталации и одржување на мрежи
        </h3>

        <p class="text-gray-600 leading-relaxed mb-6">
          Комплетни решенија за LAN/WiFi мрежи, конфигурација,
          поставување на рутери, switch уреди и професионално
          одржување на мрежна инфраструктура.
        </p>

        <a href="#" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-800 transition">
          Дознај повеќе
        </a>
      </div>

      <!-- Service 2 -->
      <div class="bg-gray-50 rounded-2xl p-8 shadow-sm hover:shadow-xl transition duration-300 border border-gray-100">
        <div class="mb-6">
          <div class="w-16 h-16 rounded-xl bg-green-100 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L15 12l-5.25-5" />
            </svg>
          </div>
        </div>

        <h3 class="text-2xl font-semibold text-gray-900 mb-4">
          Одржување на компјутерски системи
        </h3>

        <p class="text-gray-600 leading-relaxed mb-6">
          Редовно сервисирање, оптимизација и техничка поддршка
          за компјутери, сервери и деловни ИТ системи.
        </p>

        <a href="#" class="inline-flex items-center text-green-600 font-semibold hover:text-green-800 transition">
          Дознај повеќе
        </a>
      </div>

      <!-- Service 3 -->
      <div class="bg-gray-50 rounded-2xl p-8 shadow-sm hover:shadow-xl transition duration-300 border border-gray-100">
        <div class="mb-6">
          <div class="w-16 h-16 rounded-xl bg-red-100 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h8a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z" />
            </svg>
          </div>
        </div>

        <h3 class="text-2xl font-semibold text-gray-900 mb-4">
          Видео надзор и контрола на пристап
        </h3>

        <p class="text-gray-600 leading-relaxed mb-6">
          Инсталација на CCTV системи, IP камери, системи за
          контрола на пристап и безбедносни решенија за домови
          и компании.
        </p>

        <a href="#" class="inline-flex items-center text-red-600 font-semibold hover:text-red-800 transition">
          Дознај повеќе
        </a>
      </div>

      <!-- Service 4 -->
      <div class="bg-gray-50 rounded-2xl p-8 shadow-sm hover:shadow-xl transition duration-300 border border-gray-100">
        <div class="mb-6">
          <div class="w-16 h-16 rounded-xl bg-yellow-100 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-yellow-600" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h4" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 7h14v10H5z" />
            </svg>
          </div>
        </div>

        <h3 class="text-2xl font-semibold text-gray-900 mb-4">
          Сервис и продажба на принтери
        </h3>

        <p class="text-gray-600 leading-relaxed mb-6">
          Продажба, сервисирање и одржување на ласерски и inkjet
          принтери, како и набавка на тонери и резервни делови.
        </p>

        <a href="#" class="inline-flex items-center text-yellow-600 font-semibold hover:text-yellow-800 transition">
          Дознај повеќе
        </a>
      </div>

    </div>
  </div>
</section>
@endsection