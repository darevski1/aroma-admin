@php
    // Admin, auth and editor URLs must never be indexed, whatever the page settings say.
    $__seoPrivatePaths = [
        'home', 'profile*', 'users*', 'navigation*', 'posts*', 'admin*', 'health',
        'login', 'register', 'password*', 'verify*',
        'pages/create', 'pages/manual-blades', 'pages/all/json',
        'pages/*/edit', 'pages/*/edit-content', 'pages/*/editor',
    ];
    $__seoRobots = request()->is($__seoPrivatePaths)
        ? 'noindex,nofollow'
        : trim($__env->yieldContent('robots_content'));
    // Every page gets a self-referencing canonical unless the page sets its own.
    $__seoCanonical = trim($__env->yieldContent('canonical_url')) ?: url()->current();
@endphp
<meta name="description" content="{{ $pageDescription ?? 'ITPlus нуди професионални ИТ решенија, мрежна инфраструктура, видео надзор, сервис, принтери и техничка поддршка.' }}">
<meta property="og:title" content="{{ $pageTitle ?? 'ITPlus | Комплетни ИТ решенија' }}">
<meta property="og:description" content="{{ $pageDescription ?? 'ITPlus нуди професионални ИТ решенија, мрежна инфраструктура, видео надзор, сервис, принтери и техничка поддршка.' }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="ITPlus">
<meta property="og:url" content="{{ url()->current() }}">
<link rel="canonical" href="{{ $__seoCanonical }}">
@if($__seoRobots !== '')
<meta name="robots" content="{{ $__seoRobots }}">
@endif
@if(trim($__env->yieldContent('og_image')) !== '')
<meta property="og:image" content="{{ trim($__env->yieldContent('og_image')) }}">
<meta name="twitter:image" content="{{ trim($__env->yieldContent('og_image')) }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle ?? 'ITPlus | Комплетни ИТ решенија' }}">
<meta name="twitter:description" content="{{ $pageDescription ?? 'ITPlus нуди професионални ИТ решенија, мрежна инфраструктура, видео надзор, сервис, принтери и техничка поддршка.' }}">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<link rel="icon" type="image/png" href="/assets/images/favicon/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="/assets/images/favicon/favicon.svg" />
<link rel="shortcut icon" href="/assets/images/favicon/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="/assets/images/favicon/apple-touch-icon.png" />
<link rel="manifest" href="/assets/images/favicon/site.webmanifest" />
