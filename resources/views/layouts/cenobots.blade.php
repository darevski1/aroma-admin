<!DOCTYPE html>
<html lang="mk">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  {{-- Metadata mirrors cenno/index.html verbatim. The hero image is served from
  the copied source asset so the OG card is the exact design image.
  The title/description are overridable per page; the defaults below keep
  the CenoBots landing page output byte-identical to before. --}}
  <title>@yield('title', 'CenoBots — Автономни роботи за чистење | IT Plus Smart')</title>
  <meta name="author" content="IT Plus Smart">
  <meta name="description"
    content="@yield('description', 'Професионални автономни роботи за комерцијално и индустриско чистење во Северна Македонија – продажба, изнајмување, обука и локален сервис преку IT Plus Smart.')">

  <meta property="og:type" content="website">
  <meta property="og:site_name" content="CenoBots x IT Plus Smart">
  <meta property="og:title" content="@yield('title', 'CenoBots — Автономни роботи за чистење | IT Plus Smart')">
  <meta property="og:description"
    content="@yield('description', 'Професионални автономни роботи за комерцијално и индустриско чистење во Северна Македонија – продажба, изнајмување, обука и локален сервис преку IT Plus Smart.')">
  <meta property="og:image" content="@yield('og_image', asset('assets/images/cenobots/cenno/hero-robot.jpg'))">
  <link rel="canonical" href="@yield('canonical_url', url()->current())">
  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:locale" content="mk_MK">
  @hasSection('robots_content')<meta name="robots" content="@yield('robots_content')">@endif
  <meta name="twitter:card" content="summary_large_image">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Archivo:wght@500;600;700;800&family=Barlow:wght@400;500;600&display=swap">

  <link rel="icon" type="image/png" href="/assets/images/favicon/favicon-96x96.png" sizes="96x96" />
  <link rel="icon" type="image/svg+xml" href="/assets/images/favicon/favicon.svg" />
  <link rel="shortcut icon" href="/assets/images/favicon/favicon.ico" />

  {{-- Isolated CenoBots bundle only: no app.css, no Livewire, no global bundle --}}
  @vite(['resources/css/cenobots.css', 'resources/js/cenobots.js'])
</head>

<body>
  @php
    // Text/image edits saved from the visual editor (no-op when a page has none).
    $__inlineEdits = isset($page) && $page instanceof \App\Models\Page ? $page->setting('inline_edits') : null;
  @endphp
  @if (!empty($__inlineEdits))
    {!! app(\App\Services\InlineEdits::class)->apply($__env->yieldContent('content'), $__inlineEdits, request()->boolean('editor') && auth()->check() && auth()->user()->isAdmin()) !!}
  @else
    @yield('content')
  @endif

  {{-- ── Visual editor mode (admin iframe / ?editor=1) ── --}}
  @if (request('editor') && auth()->check() && auth()->user()->isAdmin())
    <div id="ie-config" data-mode="edits"
      data-page-id="{{ isset($page) && $page instanceof \App\Models\Page ? $page->id : '' }}"
      data-csrf="{{ csrf_token() }}" hidden></div>
    <script type="module" src="{{ Vite::asset('resources/js/page-editor.js') }}"></script>
  @endif
</body>

</html>