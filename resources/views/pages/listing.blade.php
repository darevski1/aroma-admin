@extends('layouts.app')

@section('title', 'ITPlus | Страници')
@section('meta_description', 'Преглед на сите страници на ITPlus – услуги, портфолио, контакт и повеќе.')

@section('content')
<section class="bg-white py-24 overflow-hidden">
  <div class="max-w-7xl mx-auto px-6 lg:px-12">

    <!-- Header -->
    <div class="text-center mb-16">
      <span class="text-gray-500 uppercase tracking-[4px] text-sm font-semibold">
        Сите Страници
      </span>
      <h1 class="block text-3xl text-foreground sm:text-4xl lg:text-5xl lg:leading-tight mt-4">
        Нашите <span class="text-gray-600">страници</span>
      </h1>
      <p class="text-gray-600 text-lg leading-8 mt-6 max-w-2xl mx-auto">
        Преглед на сите достапни страници на нашата веб-страна.
        Изберете ја страницата што ви треба.
      </p>
    </div>

    <!-- Pages Grid -->
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8 mt-12">
      @forelse ($pages as $page)
        <a href="{{ url('pages/' . $page->slug) }}"
           class="group block bg-gray-50 border border-gray-100 rounded-3xl p-8 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1">

          {{-- Page icon based on layout --}}
          <div class="w-14 h-14 rounded-2xl bg-gray-900 text-white flex items-center justify-center mb-6 group-hover:bg-gray-800 transition">
            @php
                $icons = [
                    'pages.about.index'          => '<path d="M12 12c2.5 0 4.5-2 4.5-4.5S14.5 3 12 3 7.5 5 7.5 7.5 9.5 12 12 12zm0 2c-3 0-9 1.5-9 4.5V21h18v-2.5c0-3-6-4.5-9-4.5z" stroke="currentColor" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
                    'pages.uslugi.index'         => '<path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="8" cy="6" r="1.5" fill="currentColor"/><circle cx="14" cy="12" r="1.5" fill="currentColor"/><circle cx="10" cy="18" r="1.5" fill="currentColor"/>',
                    'pages.cennobots.index'      => '<path d="M9 3v2m6-2v2M9 19v2m6-2v2M3 9h2m-2 6h2m14-6h2m-2 6h2M7.5 7.5l1.4 1.4m6.2 6.2l1.4 1.4M7.5 16.5l1.4-1.4m6.2-6.2l1.4-1.4" stroke="currentColor" fill="none" stroke-width="1.5" stroke-linecap="round"/><rect x="6" y="6" width="12" height="12" rx="2" stroke="currentColor" fill="none" stroke-width="1.5"/>',
                    'pages.computer_network.index' => '<path d="M12 22c5.5 0 10-4.5 10-10S17.5 2 12 2 2 6.5 2 12s4.5 10 10 10z" stroke="currentColor" fill="none" stroke-width="1.5"/><path d="M2 12h20M12 2c3 3 4.5 6.5 4.5 10s-1.5 7-4.5 10c-3-3-4.5-6.5-4.5-10S9 5 12 2z" stroke="currentColor" fill="none" stroke-width="1.5"/>',
                    'pages.computerWorld.index'   => '<path d="M4 6h16a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2zM8 20h8" stroke="currentColor" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 18v2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>',
                    'pages.accessControl.index'   => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="currentColor" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 12l2 2 4-4" stroke="currentColor" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
                    'pages.printerWorld.index'    => '<path d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2" stroke="currentColor" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><rect x="6" y="14" width="12" height="8" rx="1" stroke="currentColor" fill="none" stroke-width="1.5"/>',
                    'pages.portfolio.index'       => '<path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2zM22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z" stroke="currentColor" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
                    'pages.kontakt.index'         => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" fill="none" stroke-width="1.5"/><path d="M22 6l-10 7L2 6" stroke="currentColor" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>',
                ];
                $icon = $icons[$page->layout] ?? '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="currentColor" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8" stroke="currentColor" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>';
            @endphp
            <svg viewBox="0 0 24 24" class="w-7 h-7" fill="none">
              {!! $icon !!}
            </svg>
          </div>

          <h2 class="text-xl font-semibold text-gray-900 mb-3 group-hover:text-gray-700 transition">
            {{ $page->title }}
          </h2>

          <p class="text-gray-500 text-sm leading-relaxed mb-6">
            {{ $page->meta_description ?: 'Погледнете ја страницата за повеќе информации.' }}
          </p>

          <div class="flex items-center text-sm font-medium text-gray-900 group-hover:gap-3 gap-2 transition-all">
            <span>Отвори</span>
            <span aria-hidden="true" class="transition-transform group-hover:translate-x-1">→</span>
          </div>
        </a>
      @empty
        <div class="col-span-full text-center py-16">
          <p class="text-gray-400 text-lg">Нема објавени страници.</p>
        </div>
      @endforelse
    </div>

  </div>
</section>
@endsection
