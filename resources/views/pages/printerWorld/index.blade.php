@extends('layouts.app')
<livewire:navbar />
@section('content')
<section class="bg-white py-24 overflow-hidden">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">

        <!-- HERO -->
        <div class="grid lg:grid-cols-2 gap-16 items-center">

            <!-- LEFT -->
            <div>

                <span
                    class="inline-flex items-center px-5 py-2  rounded-full text-lg bg-gray-200  text-black font-medium hover:bg-gray-200 transition gap-2">
                    Уреди за печатење
                </span>

                <h1 class="block text-3xl  text-foreground sm:text-4xl lg:text-7xl lg:leading-tight">
                    Продажба,
                    Изнајмување
                    <span class="main-red">
                        Принтери
                    </span>
                </h1>

                <p class="text-gray-600 text-lg leading-8 mt-8">
                    Нудиме професионални решенија за продажба, изнајмување
                    и сервисирање на printer уреди за компании, институции
                    и деловни корисници. Брза поддршка, квалитетна опрема
                    и сигурна техничка услуга на едно место.
                </p>

                <!-- BUTTONS -->
                <div class="flex flex-wrap gap-4 mt-10">



                    <a href="#contact"
                        class="flex items-center px-6 py-3 rounded-full text-lg int-btn-black-outline gap-2">

                        <!-- Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>

                        Контакт
                    </a>
                </div>

                <!-- SMALL INFO -->
                <div class="flex flex-wrap gap-10 mt-14">

                    <div>
                        <h3 class="text-3xl font-thin main-red">
                            15+
                        </h3>

                        <p class="text-gray-600 mt-2">
                            Години Искуство
                        </p>
                    </div>

                    <div>
                        <h3 class="text-3xl font-thin main-red">
                            24/7
                        </h3>

                        <p class="text-gray-600 mt-2">
                            Техничка Поддршка
                        </p>
                    </div>

                    <div>
                        <h3 class="text-3xl font-thin main-red">
                            100+
                        </h3>

                        <p class="text-gray-600 mt-2">
                            Задоволни Клиенти
                        </p>
                    </div>

                </div>

            </div>

            <!-- RIGHT IMAGE -->
            <div class="relative">

                <!-- BACKGROUND SHAPE -->
                <div class="absolute inset-0 bg-gray-100 rounded-[40px] rotate-3"></div>

                <!-- IMAGE -->
                <div class="relative rounded-[40px] overflow-hidden shadow-2xl">

                    <img src="{{asset(" ./../assets/images/printer.jpg")}}" alt="Printer Service"
                        class="w-full h-[650px] object-cover">
                </div>

                <!-- FLOATING CARD -->
                <div class="absolute -bottom-8 left-8 bg-white rounded-3xl shadow-2xl border border-gray-100 p-8">

                    <div class="flex items-center gap-5">

                        <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center">

                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-black" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2a4 4 0 014-4h4m0 0l-3-3m3 3l-3 3" />

                            </svg>

                        </div>

                        <div>

                            <h3 class="text-2xl font-thin text-gray-900">
                                Брз и Сигурен Сервис
                            </h3>

                            <p class="text-gray-600 mt-1">
                                Поддршка за сите типови уреди
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- SERVICES -->


    </div>
</section>

<div class="container mx-auto py-10">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Block 1 -->
        <div class="bg-white rounded-2xl p-8 flex items-center justify-center h-[220px]">

            <img src="{{ asset('./../assets/images/logo/Canon_ide_p-tcLS_0.svg') }}" alt="Canon Logo"
                class="max-h-15 object-contain" />
        </div>

        <!-- Block 2 -->
        <div class="bg-white rounded-2xl p-8 flex items-center justify-center h-[220px]">
            <img src="{{ asset('./../assets/images/logo/Ricoh_Australia_Logo_0.svg') }}" alt="Ricoh Logo"
                class="max-h-20 object-contain" />
        </div>

        <!-- Block 3 -->
        <div class="bg-white rounded-2xl p-8 flex items-center justify-center h-[220px]">
            <img src="{{ asset('./../assets/images/logo/HP_Logo_0.svg') }}" alt="Hp Logo"
                class="max-h-24 object-contain" />>
        </div>

    </div>
</div>


<section class="bg-gray-50 py-24 overflow-hidden ">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">

        <div class="mt-4">

            <div class="text-center max-w-3xl mx-auto">

                <span
                    class="text-sm uppercase tracking-[4px]inline-flex items-center px-5 py-2  rounded-full text-lg bg-gray-200  text-black font-medium hover:bg-gray-200 transition gap-2">
                    Наши Услуги
                </span>

                <h2 class="text-5xl font-thin text-gray-900 mt-6">
                    Комплетни Решенија за Вашиот Бизнис
                </h2>

                <p class="text-lg text-gray-600 leading-8 mt-6">
                    Обезбедуваме професионални услуги за продажба,
                    изнајмување и одржување на системи
                    според потребите на вашиот бизнис.
                </p>

            </div>

            <!-- CARDS -->
            <div class="grid md:grid-cols-3 gap-8 mt-20">

                <!-- CARD -->
                <div
                    class="bg-gray-50 rounded-[12px] p-10 border border-gray-200 hover:shadow-2xl transition duration-300">

                    <div class="w-16 h-16 rounded-2xl bg-white shadow-md flex items-center justify-center">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-black" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2h-2" />

                        </svg>

                    </div>

                    <h3 class="text-3xl font-thin text-gray-900 mt-8">
                        Продажба
                    </h3>

                    <p class="text-gray-600 leading-8 mt-5">
                        Продажба на нови и професионални уреди за печатење
                        од реномирани брендови за сите деловни потреби.
                    </p>

                </div>

                <!-- CARD -->
                <div
                    class="bg-gray-50 rounded-[12px] p-10 border border-gray-200 hover:shadow-2xl transition duration-300">

                    <div class="w-16 h-16 rounded-2xl bg-white shadow-md flex items-center justify-center">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-black" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10" />

                        </svg>

                    </div>

                    <h3 class="text-3xl font-thin text-gray-900 mt-8">
                        Изнајмување
                    </h3>

                    <p class="text-gray-600 leading-8 mt-5">
                        Флексибилни решенија за изнајмување уреди за печатење со месечна поддршка и одржување.
                    </p>

                </div>

                <!-- CARD -->
                <div
                    class="bg-gray-50 rounded-[12px] p-10 border border-gray-200 hover:shadow-2xl transition duration-300">

                    <div class="w-16 h-16 rounded-2xl bg-white shadow-md flex items-center justify-center">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-black" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />

                        </svg>

                    </div>

                    <h3 class="text-3xl font-thin text-gray-900 mt-8">
                        Сервис
                    </h3>

                    <p class="text-gray-600 leading-8 mt-5">
                        Брз и професионален сервис за сите уреди,
                        дијагностика и техничка поддршка.
                    </p>

                </div>

            </div>

        </div>

    </div>
</section>
@endsection