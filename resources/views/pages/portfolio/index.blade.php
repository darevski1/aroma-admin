@extends('layouts.app')
<livewire:navbar />
@section('content')

<!-- PORTFOLIO SECTION -->
<section class="bg-gray-50 py-20">
  <div class="max-w-7xl mx-auto px-6 lg:px-8">

    <!-- Section Header -->
    <div class="text-center mb-16">
      <h2 class="block text-3xl  text-foreground sm:text-4xl lg:text-7xl lg:leading-tight">
        Портфолио
      </h2>

      <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950">
        Дел од проектите и решенијата кои успешно ги реализиравме
        за компании, институции и приватни клиенти.
      </p>
    </div>

    <!-- Portfolio Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

      <!-- Project 1 -->
      <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300">
        <img src="/images/portfolio/network.jpg" alt="Мрежна инфраструктура" class="w-full h-60 object-cover">

        <div class="p-6">
          <span class="inline-block bg-blue-100 text-blue-700 text-sm font-medium px-3 py-1 rounded-full mb-4">
            Мрежни Решенија
          </span>

          <h3 class="text-2xl font-semibold text-gray-900 mb-3">
            Инсталација на корпоративна мрежа
          </h3>

          <p class="text-gray-600 leading-relaxed mb-5">
            Поставување и конфигурација на стабилна LAN/WiFi
            инфраструктура за деловен објект со повеќе корисници.
          </p>

          <a href="#" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-800 transition">
            Погледни проект
          </a>
        </div>
      </div>

      <!-- Project 2 -->
      <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300">
        <img src="/images/portfolio/cctv.jpg" alt="Видео надзор" class="w-full h-60 object-cover">

        <div class="p-6">
          <span class="inline-block bg-red-100 text-red-700 text-sm font-medium px-3 py-1 rounded-full mb-4">
            Видео Надзор
          </span>

          <h3 class="text-2xl font-semibold text-gray-900 mb-3">
            CCTV систем за деловен простор
          </h3>

          <p class="text-gray-600 leading-relaxed mb-5">
            Монтажа на IP камери, NVR систем и далечински
            пристап за целосна безбедност и контрола.
          </p>

          <a href="#" class="inline-flex items-center text-red-600 font-semibold hover:text-red-800 transition">
            Погледни проект
          </a>
        </div>
      </div>

      <!-- Project 3 -->
      <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300">
        <img src="/images/portfolio/computers.jpg" alt="Компјутерски системи" class="w-full h-60 object-cover">

        <div class="p-6">
          <span class="inline-block bg-green-100 text-green-700 text-sm font-medium px-3 py-1 rounded-full mb-4">
            ИТ Поддршка
          </span>

          <h3 class="text-2xl font-semibold text-gray-900 mb-3">
            Одржување на ИТ инфраструктура
          </h3>

          <p class="text-gray-600 leading-relaxed mb-5">
            Редовно одржување и оптимизација на компјутерски
            системи и сервери за компанија со повеќе канцеларии.
          </p>

          <a href="#" class="inline-flex items-center text-green-600 font-semibold hover:text-green-800 transition">
            Погледни проект
          </a>
        </div>
      </div>

      <!-- Project 4 -->
      <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300">
        <img src="/images/portfolio/printer.jpg" alt="Принтери" class="w-full h-60 object-cover">

        <div class="p-6">
          <span class="inline-block bg-yellow-100 text-yellow-700 text-sm font-medium px-3 py-1 rounded-full mb-4">
            Принтери
          </span>

          <h3 class="text-2xl font-semibold text-gray-900 mb-3">
            Сервисирање на печатарски систем
          </h3>

          <p class="text-gray-600 leading-relaxed mb-5">
            Комплетен сервис и конфигурација на мрежни принтери
            за канцелариска и индустриска употреба.
          </p>

          <a href="#" class="inline-flex items-center text-yellow-600 font-semibold hover:text-yellow-800 transition">
            Погледни проект
          </a>
        </div>
      </div>

      <!-- Project 5 -->
      <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300">
        <img src="/images/portfolio/server.jpg" alt="Сервери" class="w-full h-60 object-cover">

        <div class="p-6">
          <span class="inline-block bg-indigo-100 text-indigo-700 text-sm font-medium px-3 py-1 rounded-full mb-4">
            Серверски Решенија
          </span>

          <h3 class="text-2xl font-semibold text-gray-900 mb-3">
            Инсталација на сервер систем
          </h3>

          <p class="text-gray-600 leading-relaxed mb-5">
            Конфигурација и обезбедување на серверска
            инфраструктура со backup и remote management.
          </p>

          <a href="#" class="inline-flex items-center text-indigo-600 font-semibold hover:text-indigo-800 transition">
            Погледни проект
          </a>
        </div>
      </div>

      <!-- Project 6 -->
      <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition duration-300">
        <img src="/images/portfolio/access-control.jpg" alt="Контрола на пристап" class="w-full h-60 object-cover">

        <div class="p-6">
          <span class="inline-block bg-purple-100 text-purple-700 text-sm font-medium px-3 py-1 rounded-full mb-4">
            Контрола на Пристап
          </span>

          <h3 class="text-2xl font-semibold text-gray-900 mb-3">
            Smart Access систем
          </h3>

          <p class="text-gray-600 leading-relaxed mb-5">
            Инсталација на систем за контрола на пристап со
            картички, biometric уреди и логирање на корисници.
          </p>

          <a href="#" class="inline-flex items-center text-purple-600 font-semibold hover:text-purple-800 transition">
            Погледни проект
          </a>
        </div>
      </div>

    </div>

  </div>
</section>
@endsection