<!DOCTYPE html>
<html lang="mk">

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
        @php
            // Re-apply text edits saved from the visual editor (no-op when a page has none).
            $__inlineEdits = isset($page) && $page instanceof \App\Models\Page ? $page->setting('inline_edits') : null;
        @endphp
        @if (!empty($__inlineEdits))
            {!! app(\App\Services\InlineEdits::class)->apply($__env->yieldContent('content'), $__inlineEdits, request()->boolean('editor') && auth()->check() && auth()->user()->isAdmin()) !!}
        @else
            @yield('content')
        @endif
    </main>

    {{-- Footer scripts --}}
    @include('partials.scripts')

    {{-- Footer --}}
    @include('partials.footer')
    @livewireScripts

    {{-- ── Full Page Editor mode ────────────────────────────────── --}}
    @if(request('editor'))
        @php
            $__ieTemplate = isset($page) && $page instanceof \App\Models\Page
                && $page->layout && $page->layout !== 'welcome' && $page->layout !== 'pages.dynamic' && view()->exists($page->layout);
        @endphp
        <div id="ie-config" data-mode="{{ $__ieTemplate ? 'edits' : 'content' }}"
             data-page-id="{{ isset($page) && $page instanceof \App\Models\Page ? $page->id : '' }}"
             data-csrf="{{ auth()->check() && auth()->user()->isAdmin() ? csrf_token() : '' }}" hidden></div>
        <script type="module" src="{{ Vite::asset('resources/js/page-editor.js') }}"></script>
    @endif
</body>

</html>
