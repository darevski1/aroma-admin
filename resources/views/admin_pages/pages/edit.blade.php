@extends('layouts.master')

@section('title', 'Edit Page: ' . $page->title)

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">Edit Page: {{ $page->title }}</h3>
            <p class="mb-4">
                Update page content, settings, and visibility. Changes are saved without altering the page's unique design or functionality.
            </p>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('success') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-4" id="pageEditTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link" href="{{ route('pages.edit-content', $page->id) }}">
                <i class="material-symbols-rounded me-1" style="font-size:16px;">edit</i>Visual Editor
            </a>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-general" data-bs-toggle="tab" data-bs-target="#panel-general"
                type="button" role="tab" aria-controls="panel-general" aria-selected="true">
                <i class="material-symbols-rounded me-1" style="font-size:16px;">settings</i>
                General
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-media" data-bs-toggle="tab" data-bs-target="#panel-media"
                type="button" role="tab" aria-controls="panel-media" aria-selected="false">
                <i class="material-symbols-rounded me-1" style="font-size:16px;">perm_media</i>
                Media
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-seo" data-bs-toggle="tab" data-bs-target="#panel-seo"
                type="button" role="tab" aria-controls="panel-seo" aria-selected="false">
                <i class="material-symbols-rounded me-1" style="font-size:16px;">search</i>
                SEO
            </button>
        </li>
        @if (trim((string) $page->content) !== '')
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-editor" data-bs-toggle="tab" data-bs-target="#panel-editor"
                type="button" role="tab" aria-controls="panel-editor" aria-selected="false">
                <i class="material-symbols-rounded me-1" style="font-size:16px;">edit_note</i>
                Full Page Editor
            </button>
        </li>
        @endif
    </ul>

    <div class="tab-content" id="pageEditTabContent">

        {{-- ═══════════════ GENERAL TAB ═══════════════ --}}
        <div class="tab-pane fade show active" id="panel-general" role="tabpanel" aria-labelledby="tab-general">
            <form action="{{ route('pages.update', $page->id) }}" method="POST" enctype="multipart/form-data" id="generalForm">
                @csrf
                @method('PATCH')
                <input type="hidden" name="canonical_url" value="{{ $page->setting('canonical_url') }}">
                <input type="hidden" name="og_title" value="{{ $page->setting('og_title') }}">
                <input type="hidden" name="og_description" value="{{ $page->setting('og_description') }}">
                <input type="hidden" name="og_image" value="{{ $page->setting('og_image') }}">
                <input type="hidden" name="position" value="{{ $page->position }}">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                {{-- Title --}}
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bolder mb-1 text-dark">Page Title</label>
                                    <div class="input-group input-group-outline">
                                        <input type="text" name="title" class="form-control" value="{{ old('title', $page->title) }}" required>
                                    </div>
                                </div>

                                {{-- Slug --}}
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bolder mb-1 text-dark">Slug</label>
                                    <div class="input-group input-group-outline">
                                        <input type="text" name="slug" class="form-control" value="{{ old('slug', $page->slug) }}">
                                    </div>
                                </div>

                                <input type="hidden" name="content" value="{{ $page->content }}">

                                {{-- Layout --}}
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bolder mb-1 text-dark">Layout Template</label>
                                    <div class="input-group input-group-outline">
                                        <select name="layout" class="form-control">
                                            @php
                                                $layouts = [
                                                    'pages.show' => 'Default (pages.show)',
                                                    'pages.full' => 'Full Width (pages.full)',
                                                    'pages.sidebar' => 'With Sidebar (pages.sidebar)',
                                                ];
                                            @endphp
                                            @foreach ($layouts as $value => $label)
                                                <option value="{{ $value }}" {{ old('layout', $page->layout) == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                            @if (!in_array($page->layout, array_keys($layouts)))
                                                <option value="{{ $page->layout }}" selected>{{ $page->layout }} (current)</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                {{-- Route Name --}}
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bolder mb-1 text-dark">Route Name (for custom Blade templates)</label>
                                    <div class="input-group input-group-outline">
                                        <input type="text" name="route_name" class="form-control" value="{{ old('route_name', $page->route_name) }}">
                                    </div>
                                    <small class="text-muted">Optional. The named route used to render this page (e.g. <code>za-nas</code>, <code>cennobots</code>).</small>
                                </div>

                                {{-- Published --}}
                                <div class="form-check form-switch my-3">
                                    <input class="form-check-input" type="checkbox" name="published" value="1" {{ old('published', $page->published) ? 'checked' : '' }} id="publishedCheck">
                                    <label class="form-check-label ms-2" for="publishedCheck">Published (visible on site)</label>
                                </div>

                                {{-- Info --}}
                                <div class="text-muted text-xs mt-2">
                                    Created: {{ $page->created_at?->format('M d, Y H:i') ?? 'N/A' }}
                                    &middot;
                                    Last updated: {{ $page->updated_at?->format('M d, Y H:i') ?? 'N/A' }}
                                </div>

                                <div class="mb-3 form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="robots_index" value="1" id="robotsIndexGeneral" {{ $page->setting('robots_index', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="robotsIndexGeneral">Allow search engine indexing</label>
                                </div>

                                {{-- Buttons --}}
                                <div class="d-flex gap-2 mt-4">
                                    <button type="submit" class="btn bg-gradient-dark">
                                        <i class="material-symbols-rounded me-1" style="font-size: 16px;">save</i>
                                        Save Changes
                                    </button>
                                    <a href="{{ route('pages.index') }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar --}}
                    <div class="col-lg-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="text-sm font-weight-bolder mb-3">
                                    <i class="material-symbols-rounded me-1" style="font-size: 16px;">link</i>
                                    Linked Navigation Items
                                </h6>
                                @if ($page->navigations->count() > 0)
                                    <ul class="list-unstyled text-xs">
                                        @foreach ($page->navigations as $nav)
                                            <li class="mb-1">
                                                <span class="badge {{ $nav->parent_id ? 'bg-gradient-secondary' : 'bg-gradient-info' }}">{{ $nav->parent_id ? 'Sub' : 'Main' }}</span>
                                                {{ $nav->title }}
                                                @if ($nav->parent)
                                                    <span class="text-muted">({{ $nav->parent->title }})</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                    <p class="text-muted text-xs mt-2">
                                        Manage in <a href="{{ route('navigation') }}">Navigation Builder</a>.
                                    </p>
                                @else
                                    <p class="text-muted text-xs">
                                        Not linked to navigation.
                                        <a href="{{ route('navigation') }}">Add in Navigation Builder</a>.
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-sm font-weight-bolder mb-3">Tips</h6>
                                <ul class="text-xs text-muted ps-3" style="list-style: disc;">
                                    <li class="mb-2"><strong>Content:</strong> Edit HTML content here, or use the Full Page Editor for visual inline editing.</li>
                                    <li class="mb-2"><strong>Route Name:</strong> Keep matching the existing route name for correct template rendering.</li>
                                    <li class="mb-2"><strong>Published:</strong> Unpublishing hides the page from navigation.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- ═══════════════ MEDIA TAB ═══════════════ --}}
        <div class="tab-pane fade" id="panel-media" role="tabpanel" aria-labelledby="tab-media">
            <form action="{{ route('pages.update', $page->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <input type="hidden" name="canonical_url" value="{{ $page->setting('canonical_url') }}">
                <input type="hidden" name="og_title" value="{{ $page->setting('og_title') }}">
                <input type="hidden" name="og_description" value="{{ $page->setting('og_description') }}">
                <input type="hidden" name="og_image" value="{{ $page->setting('og_image') }}">
                <input type="hidden" name="position" value="{{ $page->position }}">
                <input type="hidden" name="title" value="{{ $page->title }}">

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-sm font-weight-bolder mb-3">
                                    <i class="material-symbols-rounded me-1" style="font-size:16px;">image</i>
                                    Featured Image
                                </h6>

                                <x-media.media-picker name="featured_image_file" collection="featured_image" :model="$page" :current="$page->getFirstMedia('featured_image')" label="Featured Image" :multiple="false" :accept="'image/jpeg,image/png,image/webp,image/avif,image/gif,image/svg+xml'" />
                                @if ($page->featured_image && !$page->getFirstMedia('featured_image'))
                                    <div class="mb-3"><img src="{{ str_starts_with($page->featured_image, 'http') ? $page->featured_image : url($page->featured_image) }}" alt="Current featured image" style="max-width:240px;max-height:160px;object-fit:contain"></div>
                                @endif

                                <hr class="my-4">

                                <h6 class="text-sm font-weight-bolder mb-3">
                                    <i class="material-symbols-rounded me-1" style="font-size:16px;">attach_file</i>
                                    Attachments
                                </h6>

                                @if ($page->getMedia('attachments')->count() > 0)
                                    <div class="mb-3">
                                        @foreach ($page->getMedia('attachments') as $attachment)
                                            <div class="d-flex align-items-center justify-content-between p-2 border rounded mb-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="material-symbols-rounded me-2" style="font-size:20px;">description</i>
                                                    <div>
                                                        <p class="text-xs font-weight-bold mb-0">{{ $attachment->name }}</p>
                                                        <p class="text-xxs text-muted mb-0">{{ $attachment->mime_type }} · {{ round($attachment->size / 1024) }}KB</p>
                                                    </div>
                                                </div>
                                                <div>
                                                    <a href="{{ $attachment->getUrl() }}" target="_blank" class="btn btn-sm btn-outline-info me-1" title="Download">
                                                        <i class="material-symbols-rounded" style="font-size:14px;">download</i>
                                                    </a>
                                                    <label class="btn btn-sm btn-outline-danger mb-0" title="Remove">
                                                        <i class="material-symbols-rounded" style="font-size:14px;">delete</i>
                                                        <input type="checkbox" name="remove_attachment_media_id[]" value="{{ $attachment->id }}" class="d-none">
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted text-xs">No attachments uploaded yet.</p>
                                @endif

                                <div class="mb-3">
                                    <div class="input-group input-group-outline">
                                        <input type="file" name="attachment_files[]" class="form-control" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.zip">
                                    </div>
                                    <small class="text-muted">PDF, Word, Excel, Text, ZIP. Max 20MB per file.</small>
                                </div>

                                <div class="d-flex gap-2 mt-4">
                                    <button type="submit" class="btn bg-gradient-dark">
                                        <i class="material-symbols-rounded me-1" style="font-size: 16px;">save</i>
                                        Save Media
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- ═══════════════ SEO TAB ═══════════════ --}}
        <div class="tab-pane fade" id="panel-seo" role="tabpanel" aria-labelledby="tab-seo">
            <form action="{{ route('pages.update', $page->id) }}" method="POST">
                @csrf
                @method('PATCH')
                {{-- Include hidden title so validation passes --}}
                <input type="hidden" name="title" value="{{ $page->title }}">

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-sm font-weight-bolder mb-3">
                                    <i class="material-symbols-rounded me-1" style="font-size:16px;">search</i>
                                    SEO Settings
                                </h6>

                                {{-- Meta Title --}}
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bolder mb-1 text-dark">Meta Title</label>
                                    <div class="input-group input-group-outline">
                                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $page->meta_title) }}" maxlength="255">
                                    </div>
                                    <small class="text-muted">Recommended: 50-60 characters. Leave empty to use the page title.</small>
                                </div>

                                {{-- Meta Description --}}
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bolder mb-1 text-dark">Meta Description</label>
                                    <div class="input-group input-group-outline">
                                        <textarea name="meta_description" class="form-control" rows="4" maxlength="500">{{ old('meta_description', $page->meta_description) }}</textarea>
                                    </div>
                                    <small class="text-muted">Recommended: 150-160 characters. Describes the page for search engines.</small>
                                </div>

                                <div class="mb-3">
                                    <label class="text-xs font-weight-bolder mb-1 text-dark">Canonical URL</label>
                                    <div class="input-group input-group-outline"><input type="url" name="canonical_url" class="form-control" value="{{ old('canonical_url', $page->setting('canonical_url')) }}" placeholder="https://example.com/page"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bolder mb-1 text-dark">Open Graph title</label>
                                    <div class="input-group input-group-outline"><input type="text" name="og_title" class="form-control" value="{{ old('og_title', $page->setting('og_title')) }}" maxlength="255"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bolder mb-1 text-dark">Open Graph description</label>
                                    <textarea name="og_description" class="form-control" rows="3" maxlength="500">{{ old('og_description', $page->setting('og_description')) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bolder mb-1 text-dark">Open Graph image URL</label>
                                    <div class="input-group input-group-outline"><input type="text" name="og_image" class="form-control" value="{{ old('og_image', $page->setting('og_image')) }}"></div>
                                </div>
                                <div class="mb-3 form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="robots_index" value="1" id="robotsIndexSeo" {{ $page->setting('robots_index', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="robotsIndexSeo">Allow search engine indexing</label>
                                </div>

                                {{-- Slug (SEO-relevant) --}}
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bolder mb-1 text-dark">URL Slug</label>
                                    <div class="input-group input-group-outline">
                                        <input type="text" name="slug" class="form-control" value="{{ old('slug', $page->slug) }}">
                                    </div>
                                    <small class="text-muted">The page URL path. Use lowercase, hyphens only (e.g. <code>about-us</code>).</small>
                                </div>

                                {{-- Buttons --}}
                                <div class="d-flex gap-2 mt-4">
                                    <button type="submit" class="btn bg-gradient-dark">
                                        <i class="material-symbols-rounded me-1" style="font-size: 16px;">save</i>
                                        Save SEO Settings
                                    </button>
                                    <a href="{{ route('pages.index') }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SEO Preview --}}
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-sm font-weight-bolder mb-3">Search Engine Preview</h6>
                                <div class="border rounded p-3 bg-white" id="seo-preview">
                                    <div class="text-sm text-primary" id="preview-title" style="color:#1a0dab;cursor:pointer;">
                                        {{ $page->meta_title ?: $page->title }}
                                    </div>
                                    <div class="text-xs text-success mt-1" id="preview-url" style="color:#006621;">
                                        {{ url('pages/' . $page->slug) }}
                                    </div>
                                    <div class="text-xs text-muted mt-1" id="preview-desc" style="color:#545454;">
                                        {{ $page->meta_description ?: 'No meta description set. Search engines will auto-generate one from your page content.' }}
                                    </div>
                                </div>
                                <p class="text-muted text-xs mt-3">
                                    This is a preview of how your page may appear in Google search results.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- ═══════════════ FULL PAGE EDITOR TAB ═══════════════ --}}
        <div class="tab-pane fade" id="panel-editor" role="tabpanel" aria-labelledby="tab-editor">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="material-symbols-rounded text-muted mb-3" style="font-size:48px;">edit_note</i>
                    <h5 class="font-weight-bolder">Page Editor</h5>
                    <p class="text-muted text-sm mb-4">
                        Choose how you want to edit this page's content.
                    </p>

                    @if (\App\Support\PageTemplateConfig::isTemplatePage($page->layout))
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="{{ route('pages.edit-content', $page->id) }}" class="btn bg-gradient-primary btn-lg text-white">
                                <i class="material-symbols-rounded me-2" style="font-size:20px;">edit</i>
                                Visual Content Editor
                            </a>
                            <a href="{{ route('pages.editor', $page->id) }}" class="btn btn-outline-secondary btn-lg">
                                <i class="material-symbols-rounded me-2" style="font-size:20px;">open_in_new</i>
                                Full Page Editor (iframe)
                            </a>
                            <a href="{{ $previewUrl }}{{ str_contains($previewUrl, '?') ? '&' : '?' }}editor=1" target="_blank" class="btn btn-outline-dark btn-lg">
                                <i class="material-symbols-rounded me-2" style="font-size:20px;">visibility</i>
                                Preview
                            </a>
                        </div>
                        <div class="mt-4 text-muted text-xs">
                            <p class="mb-1">
                                <i class="material-symbols-rounded me-1" style="font-size:14px;">info</i>
                                <strong>Visual Content Editor</strong> – Edit text, headings and images with a WordPress-style editor and Media Library integration.
                            </p>
                            <p class="mb-0">
                                <i class="material-symbols-rounded me-1" style="font-size:14px;">info</i>
                                <strong>Full Page Editor</strong> – Visually edit the page directly on the live page via iframe.
                            </p>
                        </div>
                    @else
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="{{ route('pages.editor', $page->id) }}" class="btn bg-gradient-dark btn-lg">
                                <i class="material-symbols-rounded me-2" style="font-size:20px;">open_in_new</i>
                                Open Full Page Editor
                            </a>
                            <a href="{{ route('pages.edit', $page->id) }}#panel-general" class="btn btn-outline-primary btn-lg">General, SEO &amp; Media</a>
                            <a href="{{ $previewUrl }}" target="_blank" class="btn btn-outline-secondary btn-lg">
                                <i class="material-symbols-rounded me-2" style="font-size:20px;">visibility</i>
                                Preview Page
                            </a>
                        </div>
                        <div class="mt-4 text-muted text-xs">
                            <p class="mb-1">
                                <i class="material-symbols-rounded me-1" style="font-size:14px;">info</i>
                                The editor opens the actual page with inline editing enabled.
                            </p>
                            <p class="mb-0">
                                All changes are saved to the page content and displayed on the frontend.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>

<script>
(function() {
    // ── Live SEO Preview ────────────────────────────────────────
    const titleInput = document.querySelector('#panel-seo input[name="meta_title"]');
    const descInput = document.querySelector('#panel-seo textarea[name="meta_description"]');
    const slugInput = document.querySelector('#panel-seo input[name="slug"]');
    const previewTitle = document.getElementById('preview-title');
    const previewDesc = document.getElementById('preview-desc');
    const previewUrl = document.getElementById('preview-url');
    const mainTitle = '{{ addslashes($page->title) }}';
    const baseUrl = '{{ url("pages") }}';
    const mainSlug = '{{ addslashes($page->slug) }}';

    if (titleInput) {
        titleInput.addEventListener('input', function() {
            previewTitle.textContent = this.value || mainTitle;
        });
    }
    if (descInput) {
        descInput.addEventListener('input', function() {
            previewDesc.textContent = this.value || 'No meta description set. Search engines will auto-generate one from your page content.';
        });
    }
    if (slugInput) {
        slugInput.addEventListener('input', function() {
            previewUrl.textContent = baseUrl + '/' + (this.value || mainSlug);
        });
    }
})();
</script>
@endsection
