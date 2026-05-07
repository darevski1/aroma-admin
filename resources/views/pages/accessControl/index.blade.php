@extends('layouts.app')
<livewire:navbar />
@section('content')
<section class="bg-white py-20">
    <div class="container mx-auto px-6">

        <!-- Heading -->
        <div class="text-center max-w-3xl mx-auto mb-16">


            <span
                class="inline-flex items-center px-5 py-2  rounded-full text-lg bg-gray-200  text-red-600  font-medium hover:bg-gray-200 transition gap-2">
                CCTV Решенија
            </span>
            <h1 class="text-3xl  text-foreground sm:text-4xl lg:text-7xl lg:leading-tigh">
                Паметен видео надзор за
                вашиот дом и бизнис
            </h1>

            <p class="text-gray-600 mt-6 text-lg leading-relaxed">
                Професионална инсталација на CCTV системи,
                IP камери и видео надзор со 24/7 мониторинг,
                далечински пристап и максимална безбедност.
            </p>
        </div>

        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <!-- Card -->
            <div
                class="bg-gray-50 rounded-[12px] overflow-hidden  border border-gray-200 hover:shadow-2xl transition duration-300">
                <img src="{{asset(" ./../assets/images/printer.jpg")}}" alt="IP Cameras"
                    class="w-full h-64 object-cover">

                <div class="p-8">
                    <h3 class="text-2xl font-thin text-gray-900 mb-4">
                        IP Камери
                    </h3>

                    <p class="text-gray-600 leading-relaxed">
                        Висококвалитетни IP камери со Full HD и 4K резолуција,
                        ноќен режим и паметна детекција на движење.
                    </p>
                </div>
            </div>

            <!-- Card -->
            <div
                class="bg-gray-50 rounded-[12px] overflow-hidden  border border-gray-200 hover:shadow-2xl transition duration-300">
                <img src="{{asset(" ./../assets/images/printer.jpg")}}" alt="Monitoring"
                    class="w-full h-64 object-cover">

                <div class="p-8">
                    <h3 class="text-2xl font-thin text-gray-900 mb-4">
                        24/7 Мониторинг
                    </h3>

                    <p class="text-gray-600 leading-relaxed">
                        Следете го вашиот објект во секое време преку мобилен,
                        таблет или компјутер од било каде.
                    </p>
                </div>
            </div>

            <!-- Card -->
            <div
                class="bg-gray-50 rounded-[12px] overflow-hidden  border border-gray-200 hover:shadow-2xl transition duration-300">
                <img src="{{asset(" ./../assets/images/printer.jpg")}}" alt="Installation"
                    class="w-full h-64 object-cover">

                <div class="p-8">
                    <h3 class="text-2xl font-thin text-gray-900 mb-4">
                        Инсталација и Поддршка
                    </h3>

                    <p class="text-gray-600 leading-relaxed">
                        Комплетна инсталација, конфигурација и техничка поддршка
                        за сигурен и стабилен CCTV систем.
                    </p>
                </div>
            </div>

        </div>

        <!-- Bottom CTA -->
        <div class="mt-20 bg-black rounded-3xl p-10 md:p-14 text-center">
            <h2 class="text-3xl md:text-4xl font-thin text-white mb-6">
                Обезбедете го вашиот простор денес
            </h2>

            <p class="text-gray-300 text-lg max-w-2xl mx-auto mb-8">
                Нудиме современи CCTV решенија за компании,
                магацини, канцеларии, продавници и домови.
            </p>

            <a href="#contact"
                class="inline-flex items-center justify-center px-8 py-4 rounded-2xl bg-red-600 text-white font-semibold hover:bg-red-700 transition">
                Побарај Понуда
            </a>
        </div>

    </div>
</section>

<section class="relative w-full py-32 overflow-hidden">

    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="{{asset(" ./../assets/images/cctv-hero.jpg")}}" alt="AI CCTV Background"
            class="w-full h-full object-cover">
    </div>

    <!-- Dark Overlay -->
    <div class="absolute inset-0 bg-black/70"></div>

    <!-- Content -->
    <div class="relative z-10 container mx-auto px-6">

        <div class="max-w-3xl">

            <span
                class="inline-block px-4 py-2 rounded-full bg-red-600/20 text-red-400 text-sm font-semibold uppercase tracking-widest mb-6">
                AI Security Solutions
            </span>

            <h2 class="text-5xl md:text-6xl font-bold text-white leading-tight">
                Паметен AI Видео Надзор
                за максимална безбедност
            </h2>

            <p class="text-gray-300 text-lg md:text-xl leading-relaxed mt-8 max-w-2xl">
                Интелигентни CCTV системи со AI детекција,
                препознавање лица, следење на движење и
                паметни известувања во реално време.
            </p>

            <!-- Buttons -->
            <div class="flex flex-wrap gap-5 mt-10">

                <a href="#contact"
                    class="px-8 py-4 bg-red-600 hover:bg-red-700 text-white rounded-2xl font-semibold transition duration-300">
                    Побарај Понуда
                </a>


            </div>

        </div>

    </div>

</section>
@endsection