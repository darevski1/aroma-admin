@extends('layouts.app')
<livewire:navbar />
@section('content')


  <section class="bg-blue-600 text-white py-20">
    <div class="max-w-7xl mx-auto px-6 text-center">
      <h1 class="text-4xl md:text-5xl font-bold mb-4">За Нашата Компанија</h1>
      <p class="text-lg md:text-xl max-w-2xl mx-auto">
        Креираме иновативни дигитални решенија кои им помагаат на бизнисите да растат и да успеат во брзо менувачкиот свет.
      </p>
    </div>
  </section>

  <!-- About Section -->
  <section class="py-16">
    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-10 items-center">
      <div>
        <h2 class="text-3xl font-bold mb-4">Кои сме ние</h2>
        <p class="text-gray-600 mb-4">
          Ние сме тим од посветени програмери, дизајнери и ИТ специјалисти кои нудат квалитетни софтверски решенија. 
          Нашето искуство опфаќа веб развој, cloud инфраструктура и enterprise апликации.
        </p>
        <p class="text-gray-600">
          Со долгогодишно искуство им помагаме на стартапи и компании да ги претворат своите идеи во моќни дигитални производи.
        </p>
      </div>
      <div>
        <img src="https://via.placeholder.com/500x350" alt="Тим" class="rounded-2xl shadow-lg">
      </div>
    </div>
  </section>

  <!-- Mission & Vision -->
  <section class="bg-gray-100 py-16">
    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-10">
      <div class="bg-white p-8 rounded-2xl shadow">
        <h3 class="text-2xl font-semibold mb-3">Нашата мисија</h3>
        <p class="text-gray-600">
          Да им помогнеме на бизнисите преку технологија со развој на скалабилни, безбедни и иновативни ИТ решенија.
        </p>
      </div>
      <div class="bg-white p-8 rounded-2xl shadow">
        <h3 class="text-2xl font-semibold mb-3">Нашата визија</h3>
        <p class="text-gray-600">
          Да станеме лидер во дигиталната трансформација и да им помогнеме на компаниите да успеат во дигиталната ера.
        </p>
      </div>
    </div>
  </section>

  <!-- Stats -->
  <section class="py-16">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
      <div>
        <h4 class="text-3xl font-bold text-blue-600">10+</h4>
        <p class="text-gray-600">Години искуство</p>
      </div>
      <div>
        <h4 class="text-3xl font-bold text-blue-600">200+</h4>
        <p class="text-gray-600">Реализирани проекти</p>
      </div>
      <div>
        <h4 class="text-3xl font-bold text-blue-600">50+</h4>
        <p class="text-gray-600">Членови на тимот</p>
      </div>
      <div>
        <h4 class="text-3xl font-bold text-blue-600">100%</h4>
        <p class="text-gray-600">Задоволни клиенти</p>
      </div>
    </div>
  </section>

 

  <!-- CTA -->
  <section class="py-16 text-center">
    <div class="max-w-3xl mx-auto px-6">
      <h2 class="text-3xl font-bold mb-4">Да соработуваме</h2>
      <p class="text-gray-600 mb-6">
        Подготвени сте за нов проект? Контактирајте нè уште денес.
      </p>
      <a href="#" class="bg-blue-600 text-white px-6 py-3 rounded-full shadow hover:bg-blue-700 transition">
        Контакт
      </a>
    </div>
  </section>

@endsection