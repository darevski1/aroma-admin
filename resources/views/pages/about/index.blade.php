@extends('layouts.app')
<livewire:navbar />
@section('content')

<!-- Hero -->
<section class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-20">
  <div class="max-w-6xl mx-auto px-6 text-center">
    <h1 class="text-4xl md:text-5xl font-bold mb-4">За Нас</h1>
    <p class="text-lg md:text-xl max-w-2xl mx-auto">
      Професионални ИТ решенија за модерни бизниси. Ние ја градиме вашата дигитална иднина.
    </p>
  </div>
</section>

<!-- About -->
<section class="py-16">
  <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-2 gap-10 items-center">

    <div>
      <h2 class="text-3xl font-bold mb-4">Кои сме ние?</h2>
      <p class="mb-4 text-gray-600">
        Ние сме ИТ компанија специјализирана за развој на веб апликации, мрежни решенија и ИТ одржување.
        Со долгогодишно искуство и тим од експерти, нудиме сигурни и скалабилни решенија.
      </p>
      <p class="text-gray-600">
        Работиме со технологии како Laravel, Tailwind CSS, MikroTik, Cisco и cloud инфраструктури
        за да им помогнеме на нашите клиенти да растат и да бидат конкурентни.
      </p>
    </div>

    <div>
      <img src="https://images.unsplash.com/photo-1552664730-d307ca884978" class="rounded-2xl shadow-lg" alt="Team">
    </div>

  </div>
</section>

<!-- Services -->
<section class="bg-white py-16">
  <div class="max-w-6xl mx-auto px-6">
    <h2 class="text-3xl font-bold text-center mb-12">Наши Услуги</h2>

    <div class="grid md:grid-cols-3 gap-8">

      <div class="p-6 bg-gray-50 rounded-2xl shadow hover:shadow-lg transition">
        <h3 class="text-xl font-semibold mb-2">Веб Развој</h3>
        <p class="text-gray-600">
          Креирање модерни веб страни и апликации со најнови технологии.
        </p>
      </div>

      <div class="p-6 bg-gray-50 rounded-2xl shadow hover:shadow-lg transition">
        <h3 class="text-xl font-semibold mb-2">Мрежни Решенија</h3>
        <p class="text-gray-600">
          Дизајн и конфигурација на мрежи (MikroTik, Cisco, Ubiquiti).
        </p>
      </div>

      <div class="p-6 bg-gray-50 rounded-2xl shadow hover:shadow-lg transition">
        <h3 class="text-xl font-semibold mb-2">ИТ Одржување</h3>
        <p class="text-gray-600">
          Поддршка, мониторинг и безбедност за вашата ИТ инфраструктура.
        </p>
      </div>

    </div>
  </div>
</section>

<!-- Stats -->
<section class="py-16 bg-gray-100">
  <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-4 gap-8 text-center">
    <div>
      <h3 class="text-3xl font-bold text-blue-600">100+</h3>
      <p class="text-gray-600">Проекти</p>
    </div>
    <div>
      <h3 class="text-3xl font-bold text-blue-600">50+</h3>
      <p class="text-gray-600">Клиенти</p>
    </div>
    <div>
      <h3 class="text-3xl font-bold text-blue-600">10+</h3>
      <p class="text-gray-600">Години искуство</p>
    </div>
    <div>
      <h3 class="text-3xl font-bold text-blue-600">24/7</h3>
      <p class="text-gray-600">Поддршка</p>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="py-20 bg-indigo-700 text-white text-center">
  <h2 class="text-3xl font-bold mb-4">Подготвени за соработка?</h2>
  <p class="mb-6">Контактирајте не и започнете го вашиот следен проект со нас.</p>
  <a href="#contact" class="bg-white text-indigo-700 px-6 py-3 rounded-full font-semibold hover:bg-gray-200 transition">
    Контакт
  </a>
</section>


@endsection