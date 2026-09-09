@extends('layouts.app')


@section('title', 'ITPlus | Мрежни инсталации')
@section('meta_description', 'Мрежни инсталации и одржување на мрежи од ITPlus: LAN, WiFi, стабилна инфраструктура и професионална техничка поддршка.')
@section('content')
@php
    $s = $page->settings ?? [];
@endphp
<section class="bg-white py-20">
    <div class="container mx-auto px-6">

        <!-- Heading -->
        <div class="text-center max-w-3xl mx-auto mb-16">


            <span
                class="inline-flex items-center px-5 py-2  rounded-full text-lg bg-gray-200  text-red-600  font-medium hover:bg-gray-200 transition gap-2">
                {{ $s['eyebrow'] ?? 'CCTV Решенија' }}
            </span>
            <h1 class="text-3xl  text-foreground sm:text-4xl lg:text-7xl lg:leading-tigh">
                {!! nl2br(e($s['heading'] ?? "Паметен видео надзор за\nвашиот дом и бизнис")) !!}
            </h1>

            <p class="text-gray-600 mt-6 text-lg leading-relaxed">
                {!! nl2br(e($s['description'] ?? "Професионална инсталација на CCTV системи,\nIP камери и видео надзор со 24/7 мониторинг,\nдалечински пристап и максимална безбедност.")) !!}
            </p>
        </div>

        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <!-- Card 1 -->
            <div
                class="bg-gray-50 rounded-[12px] overflow-hidden  border border-gray-200 hover:shadow-2xl transition duration-300">
                <img src="{{ $s['card_1_image'] ?? asset(" ./../assets/images/printer.jpg") }}" alt="IP Cameras"
                    class="w-full h-64 object-cover">

                <div class="p-8">
                    <h3 class="text-2xl font-thin text-gray-900 mb-4">
                        {{ $s['card_1_title'] ?? 'IP Камери' }}
                    </h3>

                    <p class="text-gray-600 leading-relaxed">
                        {!! nl2br(e($s['card_1_description'] ?? 'Висококвалитетни IP камери со Full HD и 4K резолуција,\nноќен режим и паметна детекција на движење.')) !!}
                    </p>
                </div>
            </div>

            <!-- Card 2 -->
            <div
                class="bg-gray-50 rounded-[12px] overflow-hidden  border border-gray-200 hover:shadow-2xl transition duration-300">
                <img src="{{ $s['card_2_image'] ?? asset(" ./../assets/images/printer.jpg") }}" alt="Monitoring"
                    class="w-full h-64 object-cover">

                <div class="p-8">
                    <h3 class="text-2xl font-thin text-gray-900 mb-4">
                        {{ $s['card_2_title'] ?? '24/7 Мониторинг' }}
                    </h3>

                    <p class="text-gray-600 leading-relaxed">
                        {!! nl2br(e($s['card_2_description'] ?? 'Следете го вашиот објект во секое време преку мобилен,\nтаблет или компјутер од било каде.')) !!}
                    </p>
                </div>
            </div>

            <!-- Card 3 -->
            <div
                class="bg-gray-50 rounded-[12px] overflow-hidden  border border-gray-200 hover:shadow-2xl transition duration-300">
                <img src="{{ $s['card_3_image'] ?? asset(" ./../assets/images/printer.jpg") }}" alt="Installation"
                    class="w-full h-64 object-cover">

                <div class="p-8">
                    <h3 class="text-2xl font-thin text-gray-900 mb-4">
                        {{ $s['card_3_title'] ?? 'Инсталација и Поддршка' }}
                    </h3>

                    <p class="text-gray-600 leading-relaxed">
                        {!! nl2br(e($s['card_3_description'] ?? 'Комплетна инсталација, конфигурација и техничка поддршка\nза сигурен и стабилен CCTV систем.')) !!}
                    </p>
                </div>
            </div>

        </div>

        <!-- Bottom CTA -->
        <div class="mt-20 bg-black rounded-3xl p-10 md:p-14 text-center">
            <h2 class="text-3xl md:text-4xl font-thin text-white mb-6">
                {{ $s['cta_heading'] ?? 'Обезбедете го вашиот простор денес' }}
            </h2>

            <p class="text-gray-300 text-lg max-w-2xl mx-auto mb-8">
                {!! nl2br(e($s['cta_description'] ?? "Нудиме современи CCTV решенија за компании,\nмагацини, канцеларии, продавници и домови.")) !!}
            </p>

            <a href="#contact"
                class="inline-flex items-center justify-center px-8 py-4 rounded-2xl bg-red-600 text-white font-semibold hover:bg-red-700 transition">
                {{ $s['cta_button_text'] ?? 'Побарај Понуда' }}
            </a>
        </div>

    </div>
</section>

<section class="relative w-full py-32 overflow-hidden">

    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="{{ $s['ai_background_image'] ?? asset(" ./../assets/images/cctv-hero.jpg") }}" alt="AI CCTV Background"
            class="w-full h-full object-cover">
    </div>

    <!-- Dark Overlay -->
    <div class="absolute inset-0 bg-black/70"></div>

    <!-- Content -->
    <div class="relative z-10 container mx-auto px-6">

        <div class="max-w-3xl">

            <span
                class="inline-block px-4 py-2 rounded-full bg-red-600/20 text-red-400 text-sm font-semibold uppercase tracking-widest mb-6">
                {{ $s['ai_badge'] ?? 'AI Security Solutions' }}
            </span>

            <h2 class="text-5xl md:text-6xl font-bold text-white leading-tight">
                {!! nl2br(e($s['ai_heading'] ?? "Паметен AI Видео Надзор\nза максимална безбедност")) !!}
            </h2>

            <p class="text-gray-300 text-lg md:text-xl leading-relaxed mt-8 max-w-2xl">
                {!! nl2br(e($s['ai_description'] ?? "Интелигентни CCTV системи со AI детекција,\nпрепознавање лица, следење на движење и\nпаметни известувања во реално време.")) !!}
            </p>

            <!-- Buttons -->
            <div class="flex flex-wrap gap-5 mt-10">

                <a href="#contact"
                    class="px-8 py-4 bg-red-600 hover:bg-red-700 text-white rounded-2xl font-semibold transition duration-300">
                    {{ $s['ai_button_text'] ?? 'Побарај Понуда' }}
                </a>


            </div>

        </div>

    </div>

</section>
@endsection
