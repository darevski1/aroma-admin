<!DOCTYPE html>
<html lang="en">

<head>
    @php
        $siteName = 'ITPlus';
        $defaultTitle = $siteName . ' | Комплетни ИТ решенија';
        $resolvedTitle = trim($__env->yieldContent('title', $title ?? $defaultTitle));
        $pageTitle = str_contains($resolvedTitle, $siteName) ? $resolvedTitle : $siteName . ' | ' . $resolvedTitle;
        $pageDescription = trim($__env->yieldContent(
            'meta_description',
            $metaDescription ?? 'ITPlus нуди професионални ИТ решенија, мрежна инфраструктура, видео надзор, сервис, принтери и техничка поддршка.'
        ));
    @endphp
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $pageTitle }}</title>

    @include('partials.header')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

</head>

<body class="min-h-screen">

    {{-- Navigation --}}
    <livewire:navbar />

    {{-- Page Content --}}
    <main class="mx-auto w-full ">
        @yield('content')
    </main>

    {{-- Footer scripts --}}
    @include('partials.scripts')

    {{-- Footer --}}
    @include('partials.footer')
    @livewireScripts

    {{-- ── Full Page Editor mode ────────────────────────────────── --}}
    @if(request('editor'))
        <script type="module" src="{{ Vite::asset('resources/js/page-editor.js') }}"></script>
    @endif
</body>

</html>
