<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title ?? 'My App' }}</title>

    @include('partials.header')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

</head>
<body class="min-h-screen">

    {{-- Page Content --}}
    <main class="mx-auto w-full ">
        @yield('content')
    </main>

    {{-- Footer scripts --}}
    @include('partials.scripts')

    {{-- Footer --}}
    @include('partials.footer')
    @livewireScripts
</body>
</html>
