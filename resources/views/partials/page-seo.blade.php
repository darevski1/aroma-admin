{{--
  Applies the SEO fields saved in the page editor (SEO title, meta description,
  canonical URL, Open Graph image, robots) and falls back to the template's own
  defaults when a field is empty.

  Expects: $seoPage (Page|null), $seoTitle, $seoDescription
--}}
@php
    $seoPage = $seoPage ?? null;
    $seoTitleValue = trim((string) ($seoPage?->meta_title ?? '')) ?: $seoTitle;
    $seoDescValue = trim((string) ($seoPage?->meta_description ?? '')) ?: $seoDescription;
    $seoCanonical = trim((string) ($seoPage?->setting('canonical_url') ?? ''));
    $seoOgImage = trim((string) ($seoPage?->setting('og_image') ?? ''));
    $seoNoIndex = $seoPage && $seoPage->setting('robots_index', true) === false;
    $seoNoFollow = $seoPage && $seoPage->setting('robots_follow', true) === false;
    $seoOgTitle = trim((string) ($seoPage?->setting('og_title') ?? ''));
    $seoOgDesc = trim((string) ($seoPage?->setting('og_description') ?? ''));
    $seoKeywords = trim((string) ($seoPage?->setting('meta_keywords') ?? ''));
@endphp
@section('title', $seoTitleValue)
@section('meta_description', $seoDescValue)
@section('description', $seoDescValue)
@if ($seoCanonical !== '')
    @section('canonical_url', $seoCanonical)
@endif
@if ($seoOgImage !== '')
    @section('og_image', $seoOgImage)
@endif
@if ($seoNoIndex || ($seoPage && !$seoPage->published))
    @section('robots_content', 'noindex,nofollow')
@elseif ($seoNoFollow)
    @section('robots_content', 'index,nofollow')
@endif
@if ($seoOgTitle !== '')
    @section('og_title', $seoOgTitle)
@endif
@if ($seoOgDesc !== '')
    @section('og_description', $seoOgDesc)
@endif
@if ($seoKeywords !== '')
    @section('meta_keywords', $seoKeywords)
@endif
