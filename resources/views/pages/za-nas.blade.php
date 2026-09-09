@extends('layouts.app')


@section('title', 'ITPlus | За нас')
@section('meta_description', 'Информации за ITPlus, нашето искуство, пристап и професионални ИТ решенија.')
@section('content')
@php
    $s = $page->settings ?? [];
@endphp

<section class="relative bg-white py-24 overflow-hidden">

  <!-- background glow -->
  <div class="absolute w-96 h-96 bg-blue-100/40 blur-3xl rounded-full top-10 left-10"></div>
  <div class="absolute w-96 h-96 bg-indigo-100/30 blur-3xl rounded-full bottom-10 right-10"></div>

  <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-16 items-center relative z-10">

    <!-- LEFT TEXT -->
    <div class="space-y-6">

      <h2 class="text-5xl md:text-6xl font-normal leading-tight">
        {{ $s['text_heading'] ?? 'За Нас' }}
      </h2>

      <p class="text-lg md:text-xl text-gray-700 font-light leading-relaxed">
        {{ $s['text_paragraph_1'] ?? 'Ние сме технолошка компанија фокусирана на развој на автономни роботски системи кои ја трансформираат индустријата за чистење и одржување.' }}
      </p>

      <p class="text-lg md:text-xl text-gray-700 font-light leading-relaxed">
        {{ $s['text_paragraph_2'] ?? 'Со користење на вештачка интелигенција, LiDAR навигација и напредна роботика, создаваме решенија кои ја зголемуваат ефикасноста, ја намалуваат човечката интервенција и придонесуваат кон одржлива иднина.' }}
      </p>

      <!-- stats -->
      <div class="grid grid-cols-2 gap-6 pt-6">

        <div>
          <h3 class="text-3xl font-semibold text-[#5c59f5]">{{ $s['stat_1_value'] ?? '100%' }}</h3>
          <p class="text-gray-600">{{ $s['stat_1_label'] ?? 'Автономија' }}</p>
        </div>

        <div>
          <h3 class="text-3xl font-semibold text-[#5c59f5]">{{ $s['stat_2_value'] ?? 'AI' }}</h3>
          <p class="text-gray-600">{{ $s['stat_2_label'] ?? 'Паметна навигација' }}</p>
        </div>

        <div>
          <h3 class="text-3xl font-semibold text-[#5c59f5]">{{ $s['stat_3_value'] ?? '24/7' }}</h3>
          <p class="text-gray-600">{{ $s['stat_3_label'] ?? 'Континуирана работа' }}</p>
        </div>

        <div>
          <h3 class="text-3xl font-semibold text-[#5c59f5]">{{ $s['stat_4_value'] ?? 'Eco' }}</h3>
          <p class="text-gray-600">{{ $s['stat_4_label'] ?? 'Еко решенија' }}</p>
        </div>

      </div>

    </div>

    <!-- RIGHT IMAGE -->
    <div class="flex justify-center relative">

      <div class="absolute w-[350px] h-[350px] bg-blue-200/30 blur-3xl rounded-full"></div>

      <img src="{{ $s['image_robot'] ?? asset('./../assets/images/about-robot.png') }}" alt="CenoBots Team / Robot"
        class="relative z-10 max-h-[60vh] object-contain rounded-2xl shadow-xl hover:scale-105 transition-transform duration-500" />

    </div>

  </div>
</section>

@endsection
