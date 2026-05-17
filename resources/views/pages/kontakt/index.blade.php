@extends('layouts.app')
<livewire:navbar />
@section('content')
<!-- CONTACT PAGE -->
<section class="bg-white py-20">
  <div class="max-w-7xl mx-auto px-6 lg:px-8">

    <!-- Header -->
    <div class="text-center mb-16">
      <h1 class="block text-2xl  text-foreground sm:text-3xl lg:text-5xl lg:leading-tight">
        Контакт
      </h1>
      <p class="text-lg md:text-xl font-thin mb-8 text-zinc-950">
        Слободно контактирајте не за прашања, понуди или техничка поддршка.
      </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

      <!-- Contact Form -->
      <div class="bg-gray-50 p-8 rounded-2xl shadow-sm">
        <h2 class="text-2xl font-semibold text-gray-900 mb-6">
          Испрати порака
        </h2>

        <form class="space-y-5">

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Име и Презиме
            </label>
            <input type="text"
              class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Email
            </label>
            <input type="email"
              class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Порака
            </label>
            <textarea rows="5"
              class="w-full border border-gray-300 rounded-xl p-3 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
          </div>

          <button type="submit" class="flex items-center px-6 py-3 rounded-full text-lg int-btn transition gap-2">
            Испрати
          </button>

        </form>
      </div>

      <!-- Contact Info -->
      <div class="space-y-8">

        <div class="bg-gray-50 p-8 rounded-2xl shadow-sm">
          <h2 class="text-2xl font-semibold text-gray-900 mb-6">
            Информации
          </h2>

          <div class="space-y-4 text-gray-700">

            <p>
              📍 Адреса: Бул: 11 Октомври 86 - 3/8, 1000 Скопје, Македонија
            </p>

            <p>
              📞 Телефон: +389 71 354 822
            </p>

            <p>
              ✉️ Email: info@itplus.mk
            </p>

            <p>
              🕒 Работно време: Пон - Пет, 09:00 - 17:00
            </p>

          </div>
        </div>

      </div>

    </div>

    <!-- MAP SECTION -->
    <div class="mt-16">
      <h2 class="text-2xl font-semibold text-gray-900 mb-6 text-center">
        Локација
      </h2>

      <div class="w-full h-106 rounded-2xl overflow-hidden shadow-lg">
        <div id="map" class="w-full h-106 rounded-2xl overflow-hidden"></div>
      </div>
    </div>

  </div>
</section>
@endsection