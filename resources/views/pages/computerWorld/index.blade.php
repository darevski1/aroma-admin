@extends('layouts.app')
<livewire:navbar />
@section('content')
<section class="bg-white py-24">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">

        <!-- HERO -->
        <div class="grid lg:grid-cols-2 gap-16 items-center">

            <!-- LEFT -->
            <div>

                <span class="inline-block px-5 py-2 bg-gray-100 text-gray-600 rounded-full text-sm font-medium">
                    Server Infrastructure
                </span>

                <h1 class="text-3xl  text-foreground sm:text-4xl lg:text-7xl lg:leading-tigh">
                    Сервери,
                    Виртуелизација
                    и <span class="main-red">ИТ Инфраструктура</span>
                </h1>

                <p class="text-lg text-gray-600 leading-8 mt-8 max-w-2xl">
                    Обезбедуваме комплетни серверски решенија за компании – од
                    инсталација и конфигурација до одржување, мониторинг и
                    24/7 поддршка за критични системи.
                </p>

                <div class="flex flex-wrap gap-4 mt-10">

                    <a href="#"
                        class="bg-black text-white px-8 py-4 rounded-2xl font-semibold hover:bg-gray-800 transition">
                        Контакт
                    </a>

                    <a href="#"
                        class="border border-gray-300 text-gray-700 px-8 py-4 rounded-2xl font-semibold hover:border-black hover:text-black transition">
                        Услуги
                    </a>

                </div>

                <!-- STATS -->
                <div class="grid grid-cols-3 gap-6 mt-14">

                    <div>
                        <h3 class="text-3xl font-bold text-gray-900">24/7</h3>
                        <p class="text-gray-600 mt-2">Мониторинг</p>
                    </div>

                    <div>
                        <h3 class="text-3xl font-bold text-gray-900">99.9%</h3>
                        <p class="text-gray-600 mt-2">Uptime</p>
                    </div>

                    <div>
                        <h3 class="text-3xl font-bold text-gray-900">15+</h3>
                        <p class="text-gray-600 mt-2">Години Искуство</p>
                    </div>

                </div>

            </div>

            <!-- RIGHT -->
            <div class="relative">

                <!-- Background shape -->
                <div class="absolute inset-0 bg-gray-100 rounded-[40px] rotate-3"></div>

                <!-- Image -->
                <div class="relative rounded-[40px] overflow-hidden shadow-2xl">
                    <img src="https://images.unsplash.com/photo-1558494949-ef010cbdcc31?q=80&w=1400&auto=format&fit=crop"
                        class="w-full h-[650px] object-cover" alt="Server Room">
                </div>

                <!-- Floating card -->
                <div class="absolute -bottom-10 left-8 bg-white border border-gray-100 shadow-xl rounded-3xl p-8">

                    <div class="flex items-center gap-5">

                        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                            </svg>
                        </div>

                        <div>
                            <h3 class="text-xl font-bold text-gray-900">
                                Active Monitoring
                            </h3>
                            <p class="text-gray-600">
                                Секогаш под контрола
                            </p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- SERVICES -->
        <div class="mt-40">

            <div class="text-center max-w-3xl mx-auto">

                <span class="text-sm uppercase tracking-[4px] text-gray-500 font-semibold">
                    Server Services
                </span>

                <h2 class="text-5xl font-bold text-gray-900 mt-6">
                    Комплетни Серверски Решенија
                </h2>

                <p class="text-lg text-gray-600 mt-6 leading-8">
                    Дизајн, имплементација и одржување на сигурна ИТ инфраструктура
                    за мали, средни и големи компании.
                </p>

            </div>

            <!-- CARDS -->
            <div class="grid md:grid-cols-3 gap-8 mt-20">

                <!-- CARD -->
                <div class="bg-gray-50 border border-gray-100 rounded-[32px] p-10 hover:shadow-xl transition">

                    <h3 class="text-2xl font-bold text-gray-900">
                        Windows / Linux Server
                    </h3>

                    <p class="text-gray-600 mt-5 leading-7">
                        Инсталација, конфигурација и одржување на Windows Server и Linux
                        системи за стабилна работа на вашата компанија.
                    </p>

                </div>

                <!-- CARD -->
                <div class="bg-gray-50 border border-gray-100 rounded-[32px] p-10 hover:shadow-xl transition">

                    <h3 class="text-2xl font-bold text-gray-900">
                        Virtualization
                    </h3>

                    <p class="text-gray-600 mt-5 leading-7">
                        VMware и Hyper-V решенија за оптимизација на ресурси,
                        backup и висока достапност.
                    </p>

                </div>

                <!-- CARD -->
                <div class="bg-gray-50 border border-gray-100 rounded-[32px] p-10 hover:shadow-xl transition">

                    <h3 class="text-2xl font-bold text-gray-900">
                        Backup & Security
                    </h3>

                    <p class="text-gray-600 mt-5 leading-7">
                        Напредни backup системи, disaster recovery и заштита
                        на вашите критични податоци.
                    </p>

                </div>

            </div>

        </div>

    </div>
</section>
@endsection