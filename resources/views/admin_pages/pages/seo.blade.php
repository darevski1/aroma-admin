@extends('layouts.master')

@section('title', 'Edit SEO: ' . $page->title)

@php
    $robotsIndex = old('robots_index', $page->setting('robots_index', true) ? 'index' : 'noindex');
    $robotsFollow = old('robots_follow', $page->setting('robots_follow', true) ? 'follow' : 'nofollow');
@endphp

@section('content')
<div class="container-fluid py-2">
    <div class="ms-3 mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h3 class="mb-0 h4 font-weight-bolder">Edit SEO</h3>
            <p class="mb-0 text-sm">
                {{ $page->title }} &middot; <a href="{{ $publicUrl }}" target="_blank" rel="noopener"><code>{{ parse_url($publicUrl, PHP_URL_PATH) ?: '/' }}</code></a>
            </p>
        </div>
        <a href="{{ route('pages.index') }}" class="btn btn-sm btn-outline-secondary me-3 mb-0">&larr; All pages</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success text-white"><span class="text-sm">{{ session('success') }}</span></div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger text-white"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('pages.seo.update', $page) }}">
        @csrf
        @method('PATCH')

        <div class="card mb-4">
            <div class="card-header pb-0"><h6 class="mb-0">Search engines</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="meta_title">SEO Title</label>
                    <input id="meta_title" name="meta_title" type="text" maxlength="255" class="form-control border px-2" value="{{ old('meta_title', $page->meta_title) }}">
                    <small class="text-muted">Recommended 50–60 characters. Empty = the template default title.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="meta_description">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" rows="3" maxlength="500" class="form-control border px-2">{{ old('meta_description', $page->meta_description) }}</textarea>
                    <small class="text-muted">Recommended up to 160 characters.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="meta_keywords">Meta Keywords</label>
                    <input id="meta_keywords" name="meta_keywords" type="text" maxlength="500" class="form-control border px-2" value="{{ old('meta_keywords', $page->setting('meta_keywords')) }}" placeholder="keyword one, keyword two">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="canonical_url">Canonical URL</label>
                    <input id="canonical_url" name="canonical_url" type="url" maxlength="2048" class="form-control border px-2" value="{{ old('canonical_url', $page->setting('canonical_url')) }}" placeholder="https://example.com/page">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="robots_index">Robots index</label>
                        <select id="robots_index" name="robots_index" class="form-select border px-2">
                            <option value="index" @selected($robotsIndex === 'index')>Index</option>
                            <option value="noindex" @selected($robotsIndex === 'noindex')>No Index</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="robots_follow">Robots follow</label>
                        <select id="robots_follow" name="robots_follow" class="form-select border px-2">
                            <option value="follow" @selected($robotsFollow === 'follow')>Follow</option>
                            <option value="nofollow" @selected($robotsFollow === 'nofollow')>No Follow</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header pb-0"><h6 class="mb-0">Open Graph (social sharing)</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="og_title">Open Graph Title</label>
                    <input id="og_title" name="og_title" type="text" maxlength="255" class="form-control border px-2" value="{{ old('og_title', $page->setting('og_title')) }}">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="og_description">Open Graph Description</label>
                    <textarea id="og_description" name="og_description" rows="2" maxlength="500" class="form-control border px-2">{{ old('og_description', $page->setting('og_description')) }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="og_image">Open Graph Image (URL or /path)</label>
                    <input id="og_image" name="og_image" type="text" maxlength="500" class="form-control border px-2" value="{{ old('og_image', $page->setting('og_image')) }}" placeholder="https://example.com/image.jpg">
                </div>
            </div>
            <div class="card-footer d-flex gap-2">
                <button class="btn bg-gradient-dark mb-0">Save SEO</button>
                <a href="{{ route('pages.index') }}" class="btn btn-outline-secondary mb-0">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
