@extends('layouts.app')
<livewire:navbar />
@section('content')
<section class="bg-white py-24 overflow-hidden">
  <div class="max-w-7xl mx-auto px-6 lg:px-12">

    <!-- Hero Section -->
    <div class="grid lg:grid-cols-2 gap-16 items-center">

      <!-- Left Content -->
      <div>

        <span class="text-gray-500 uppercase tracking-[4px] text-sm font-semibold">
          За Нас
        </span>

        <h1 class="block text-3xl  text-foreground sm:text-4xl lg:text-7xl lg:leading-tight">
          Креираме Модерни
          <span class="text-gray-600">ИКТ Решенија</span>
        </h1>

        <p class="text-gray-600 text-lg leading-8 mt-8">
          Нашата приказна започнува во 2018 година со тим од искусни и
          амбициозни инженери, посветени на развој на современи софтверски
          и ИКТ решенија за компании ширум светот.
        </p>

        <div class="flex flex-wrap gap-4 mt-10">
          <a href="#learn" class="flex items-center px-6 py-3 rounded-full text-lg int-btn transition gap-2">
            Наши Услуги
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

      </div>

      <!-- Right Cards -->
      <div class="relative">

        <div class="grid grid-cols-2 gap-6">

          <div class="bg-gray-50 border border-gray-100 rounded-3xl p-8 shadow-sm hover:shadow-lg transition">
            <h3 class="text-4xl font-bold text-gray-900">15+</h3>
            <p class="text-gray-600 mt-3">
              Години Искуство
            </p>
          </div>

          <div class="bg-gray-50 border border-gray-100 rounded-3xl p-8 shadow-sm hover:shadow-lg transition">
            <h3 class="text-4xl font-bold text-gray-900">24/7</h3>
            <p class="text-gray-600 mt-3">
              Техничка Поддршка
            </p>
          </div>

          <div class="bg-gray-50 border border-gray-100 rounded-3xl p-8 shadow-sm hover:shadow-lg transition">
            <h3 class="text-4xl font-bold text-gray-900">100+</h3>
            <p class="text-gray-600 mt-3">
              Реализирани Проекти
            </p>
          </div>

          <div class="bg-gray-50 border border-gray-100 rounded-3xl p-8 shadow-sm hover:shadow-lg transition">
            <h3 class="text-3xl font-bold text-gray-900">
              Worldwide
            </h3>
            <p class="text-gray-600 mt-3">
              Клиенти Низ Светот
            </p>
          </div>

        </div>

      </div>

    </div>

    <!-- About Content -->
    <div class="grid lg:grid-cols-2 gap-16 mt-32 items-start">

      <div>

        <span class="text-gray-500 uppercase tracking-[4px] text-sm font-thin">
          Нашата Приказна
        </span>

        <h2 class="text-5xl font-thin text-gray-900 mt-6 leading-tight">
          Технологијата е Нашата
          Инспирација
        </h2>

      </div>

      <div>

        <p class="text-gray-600 text-lg leading-8">
          Денес сме брзорастечка софтверска и ИКТ компанија која
          нуди иновативни решенија, професионални услуги и долгорочна
          поддршка за своите клиенти.
        </p>

        <p class="text-gray-600 text-lg leading-8 mt-6">
          Нашиот тим постојано инвестира во нови технологии и знаење,
          со цел да обезбедиме сигурни, модерни и ефикасни решенија
          кои го подобруваат работењето и дигиталната трансформација
          на компаниите.
        </p>

      </div>

    </div>

  </div>
</section>
@endsection