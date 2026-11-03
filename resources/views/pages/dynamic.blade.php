@extends('layouts.app')
@if(!$page->content || !(str_contains($page->content, '<html') || str_contains($page->content, '<body')))


@endif
@section('title', request()->boolean('editor') ? ($page->meta_title ?: $page->title . ' | ITPlus') : (trim($page->setting('og_title', '')) ?: ($page->meta_title ?: $page->title . ' | ITPlus')))
@if(request()->boolean('editor') && $page->meta_description)
@section('meta_description', $page->meta_description)
@elseif($page->setting('og_description'))
@section('meta_description', $page->setting('og_description'))
@elseif($page->meta_description)
@section('meta_description', $page->meta_description)
@endif
@section('canonical_url', $page->setting('canonical_url') ?: url('pages/' . $page->slug))
@php
    $pageOgImage = $page->setting('og_image') ?: ($page->featured_image ? (str_starts_with($page->featured_image, 'http') ? $page->featured_image : url($page->featured_image)) : '');
@endphp
@if($pageOgImage)
@section('og_image', $pageOgImage)
@endif
@section('robots_content', $page->published && $page->setting('robots_index', true) ? 'index,follow' : 'noindex,nofollow')
@section('content')
@if($page->content && (str_contains($page->content, '<html') || str_contains($page->content, '<body')))
    {{-- Full HTML from Full Page Editor: strip outer html/body to avoid nesting --}}
    @php
        $html = $page->content;
        // Remove <!DOCTYPE ...> if present
        $html = preg_replace('/<!DOCTYPE[^>]*>/i', '', $html);
        // Extract <body> content if present
        if (preg_match('/<body[^>]*>(.*)<\/body>/is', $html, $m)) {
            $html = $m[1];
        }
        // Remove <html> and </html> wrappers if still present
        $html = preg_replace('/<\/?html[^>]*>/i', '', $html);
        // Remove <head>...</head> blocks
        $html = preg_replace('/<head[\s\S]*?<\/head>/i', '', $html);
        // Remove <meta ...> tags
        $html = preg_replace('/<meta[^>]*>/i', '', $html);
        // Remove <title>...</title> tags
        $html = preg_replace('/<title[^>]*>.*?<\/title>/is', '', $html);
        // Remove inline <script> tags (scripts are loaded by the layout)
        $html = preg_replace('/<script[^>]*>[\s\S]*?<\/script>/i', '', $html);
        // Remove inline <style> tags (CSS is loaded by the layout)
        $html = preg_replace('/<style[^>]*>[\s\S]*?<\/style>/i', '', $html);
        // Remove @vite/@livewire references if present as text
        $html = str_replace(['@vite', '@livewireStyles', '@livewireScripts'], '', $html);
    @endphp
    <main class="mx-auto w-full">
        {!! $html !!}
    </main>
@elseif($page->content)
    <section class="py-16">
      <div class="max-w-screen-xl mx-auto px-4">
        <h1 class="text-3xl font-bold mb-6">{{ $page->title }}</h1>
        <div class="prose prose-lg max-w-none">
          {!! $page->content !!}
        </div>
      </div>
    </section>
@else
    <section class="py-16">
      <div class="max-w-screen-xl mx-auto px-4">
        <h1 class="text-3xl font-bold mb-6">{{ $page->title }}</h1>
        <p class="text-gray-500">
          This page is managed via its dedicated template.
          @if($page->route_name && \Illuminate\Support\Facades\Route::has($page->route_name))
            <a href="{{ route($page->route_name) }}" class="text-blue-600 underline">View full page</a>
          @endif
        </p>
      </div>
    </section>
@endif
@endsection
