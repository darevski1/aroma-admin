@extends('layouts.master')

@section('title', 'Edit Post: ' . $post->title)

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">Edit Post: {{ $post->title }}</h3>
            <p class="mb-4">
                Update post content, media, and settings.
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
    <ul class="nav nav-tabs mb-4" id="postEditTabs" role="tablist">
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
                <i class="material-symbols-rounded me-1" style="font-size:16px;">photo_library</i>
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
    </ul>

    <div class="tab-content" id="postEditTabContent">

        {{-- ═══════════════ GENERAL TAB ═══════════════ --}}
        <div class="tab-pane fade show active" id="panel-general" role="tabpanel" aria-labelledby="tab-general">
            <form action="{{ route('posts.update', $post->id) }}" method="POST" id="generalForm" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                {{-- Title --}}
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bolder mb-1 text-dark">Post Title</label>
                                    <div class="input-group input-group-outline">
                                        <input type="text" name="title" class="form-control" value="{{ old('title', $post->title) }}" required>
                                    </div>
                                </div>

                                {{-- Slug --}}
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bolder mb-1 text-dark">Slug</label>
                                    <div class="input-group input-group-outline">
                                        <input type="text" name="slug" class="form-control" value="{{ old('slug', $post->slug) }}" id="slugInput">
                                    </div>
                                </div>

                                {{-- Excerpt --}}
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bolder mb-1 text-dark">Excerpt</label>
                                    <textarea name="excerpt" class="form-control" rows="3">{{ old('excerpt', $post->excerpt) }}</textarea>
                                </div>

                                {{-- Content --}}
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bolder mb-1 text-dark">Content</label>
                                    <textarea name="content" class="form-control" rows="12">{{ old('content', $post->content) }}</textarea>
                                </div>

                                {{-- Layout --}}
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bolder mb-1 text-dark">Layout Template</label>
                                    <select name="layout" class="form-control">
                                        @php
                                            $layouts = [
                                                'posts.default' => 'Default (posts.default)',
                                                'posts.full' => 'Full Width (posts.full)',
                                                'posts.sidebar' => 'With Sidebar (posts.sidebar)',
                                            ];
                                        @endphp
                                        @foreach ($layouts as $value => $label)
                                            <option value="{{ $value }}" {{ old('layout', $post->layout) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                        @if ($post->layout && !in_array($post->layout, array_keys($layouts)))
                                            <option value="{{ $post->layout }}" selected>{{ $post->layout }} (current)</option>
                                        @endif
                                    </select>
                                </div>

                                {{-- Published --}}
                                <div class="form-check form-switch my-3">
                                    <input class="form-check-input" type="checkbox" name="published" value="1" {{ old('published', $post->published) ? 'checked' : '' }} id="publishedCheck">
                                    <label class="form-check-label ms-2" for="publishedCheck">Published (visible on site)</label>
                                </div>

                                {{-- Info --}}
                                <div class="text-muted text-xs mt-2">
                                    Created: {{ $post->created_at?->format('M d, Y H:i') ?? 'N/A' }}
                                    &middot;
                                    Last updated: {{ $post->updated_at?->format('M d, Y H:i') ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn bg-gradient-dark">
                                <i class="material-symbols-rounded me-1" style="font-size: 16px;">save</i>
                                Save
                            </button>
                            <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </div>

                    {{-- Sidebar --}}
                    <div class="col-lg-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="text-sm font-weight-bolder mb-3">
                                    <i class="material-symbols-rounded me-1" style="font-size: 16px;">info</i>
                                    Post Info
                                </h6>
                                <ul class="list-unstyled text-xs text-muted mb-0">
                                    <li class="mb-1"><strong>ID:</strong> {{ $post->id }}</li>
                                    <li class="mb-1"><strong>Slug:</strong> <code>{{ $post->slug }}</code></li>
                                    <li class="mb-1"><strong>Status:</strong> {{ $post->published ? 'Published' : 'Draft' }}</li>
                                </ul>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-body">
                                <h6 class="text-sm font-weight-bolder mb-3">
                                    <i class="material-symbols-rounded me-1" style="font-size: 16px;">link</i>
                                    Quick Links
                                </h6>
                                <ul class="list-unstyled text-xs mb-0">
                                    <li class="mb-2">
                                        <a href="{{ route('posts.index') }}">
                                            <i class="material-symbols-rounded me-1" style="font-size: 14px;">arrow_back</i>
                                            Back to All Posts
                                        </a>
                                    </li>
                                    <li class="mb-2">
                                        <a href="{{ route('posts.create') }}">
                                            <i class="material-symbols-rounded me-1" style="font-size: 14px;">add</i>
                                            Create New Post
                                        </a>
                                    </li>
                                    @if ($post->published)
                                        <li class="mb-2">
                                            <a href="{{ route('pages.show', $post->slug) }}" target="_blank">
                                                <i class="material-symbols-rounded me-1" style="font-size: 14px;">open_in_new</i>
                                                View on Site
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-sm font-weight-bolder mb-3">Tips</h6>
                                <ul class="text-xs text-muted ps-3" style="list-style: disc;">
                                    <li class="mb-2"><strong>Content:</strong> Edit the post body directly in the textarea.</li>
                                    <li class="mb-2"><strong>Published:</strong> Unpublishing hides the post from the site.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- ═══════════════ MEDIA TAB ═══════════════ --}}
        <div class="tab-pane fade" id="panel-media" role="tabpanel" aria-labelledby="tab-media">
            <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <input type="hidden" name="title" value="{{ $post->title }}">

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-sm font-weight-bolder mb-3">
                                    <i class="material-symbols-rounded me-1" style="font-size:16px;">photo_library</i>
                                    Media
                                </h6>

                                {{-- Featured Image --}}
                                <x-media.media-picker name="featured_image_file" collection="featured_image" :model="$post" :current="$post->getFirstMedia('featured_image')" label="Featured Image" />

                                {{-- Attachments --}}
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bolder mb-1 text-dark">Attachments</label>

                                    @php $attachments = $post->getMedia('attachments'); @endphp
                                    @if ($attachments->count() > 0)
                                        <div class="mb-2">
                                            @foreach ($attachments as $attachment)
                                                <div class="d-flex align-items-center gap-2 p-2 border rounded bg-light mb-1">
                                                    <i class="material-symbols-rounded text-muted" style="font-size:18px;">description</i>
                                                    <a href="{{ $attachment->getUrl() }}" target="_blank" class="text-xs text-dark text-truncate" style="max-width:200px;">
                                                        {{ $attachment->file_name }}
                                                    </a>
                                                    <span class="text-xs text-muted ms-auto">{{ number_format(($attachment->size ?? 0) / 1024, 1) }} KB</span>
                                                    <div class="form-check form-check-inline mb-0 ms-2">
                                                        <input class="form-check-input" type="checkbox" name="remove_attachment_id[]" value="{{ $attachment->id }}" id="removeAttachment{{ $attachment->id }}">
                                                        <label class="form-check-label text-danger text-xs" for="removeAttachment{{ $attachment->id }}">Remove</label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted text-xs mb-2">No attachments uploaded yet.</p>
                                    @endif

                                    <input type="file" name="attachment_files[]" class="form-control" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.zip">
                                    <small class="text-muted">PDF, DOC, XLS, TXT, ZIP. Max 20 MB each.</small>
                                </div>

                                {{-- Buttons --}}
                                <div class="d-flex gap-2 mt-4">
                                    <button type="submit" class="btn bg-gradient-dark">
                                        <i class="material-symbols-rounded me-1" style="font-size: 16px;">save</i>
                                        Save
                                    </button>
                                    <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar --}}
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="text-sm font-weight-bolder mb-3">Media Tips</h6>
                                <ul class="text-xs text-muted ps-3" style="list-style: disc;">
                                    <li class="mb-2"><strong>Featured Image:</strong> Main image for the post header and social sharing.</li>
                                    <li class="mb-2"><strong>Attachments:</strong> Downloadable files linked at the bottom of the post.</li>
                                    <li class="mb-2"><strong>Image formats:</strong> JPEG, PNG, WebP, AVIF, GIF, SVG. Max 10 MB.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- ═══════════════ SEO TAB ═══════════════ --}}
        <div class="tab-pane fade" id="panel-seo" role="tabpanel" aria-labelledby="tab-seo">
            <form action="{{ route('posts.update', $post->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="title" value="{{ $post->title }}">

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
                                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $post->meta_title) }}" maxlength="255">
                                    </div>
                                    <small class="text-muted">Recommended: 50-60 characters. Leave empty to use the post title.</small>
                                </div>

                                {{-- Meta Description --}}
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bolder mb-1 text-dark">Meta Description</label>
                                    <textarea name="meta_description" class="form-control" rows="4" maxlength="500">{{ old('meta_description', $post->meta_description) }}</textarea>
                                    <small class="text-muted">Recommended: 150-160 characters. Describes the post for search engines.</small>
                                </div>

                                {{-- Slug (SEO-relevant) --}}
                                <div class="mb-3">
                                    <label class="text-xs font-weight-bolder mb-1 text-dark">URL Slug</label>
                                    <div class="input-group input-group-outline">
                                        <input type="text" name="slug" class="form-control" value="{{ old('slug', $post->slug) }}">
                                    </div>
                                    <small class="text-muted">The post URL path. Use lowercase, hyphens only (e.g. <code>my-post-title</code>).</small>
                                </div>

                                {{-- Buttons --}}
                                <div class="d-flex gap-2 mt-4">
                                    <button type="submit" class="btn bg-gradient-dark">
                                        <i class="material-symbols-rounded me-1" style="font-size: 16px;">save</i>
                                        Save SEO Settings
                                    </button>
                                    <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
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
                                        {{ $post->meta_title ?: $post->title }}
                                    </div>
                                    <div class="text-xs text-success mt-1" id="preview-url" style="color:#006621;">
                                        {{ url('p/' . $post->slug) }}
                                    </div>
                                    <div class="text-xs text-muted mt-1" id="preview-desc" style="color:#545454;">
                                        {{ $post->meta_description ?: 'No meta description set. Search engines will auto-generate one from your post content.' }}
                                    </div>
                                </div>
                                <p class="text-muted text-xs mt-3">
                                    This is a preview of how your post may appear in Google search results.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
(function() {
    // ── Slug auto-generation from title ────────────────────────────
    var titleInput = document.querySelector('#panel-general input[name="title"]');
    var slugInput = document.getElementById('slugInput');
    if (titleInput && slugInput) {
        titleInput.addEventListener('input', function() {
            if (!slugInput.dataset.manual) {
                slugInput.value = this.value
                    .toLowerCase()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim();
            }
        });
        slugInput.addEventListener('input', function() {
            this.dataset.manual = this.value.length > 0 ? '1' : '';
        });
    }

    // ── Live SEO Preview ────────────────────────────────────────
    var seoTitleInput = document.querySelector('#panel-seo input[name="meta_title"]');
    var seoDescInput = document.querySelector('#panel-seo textarea[name="meta_description"]');
    var seoSlugInput = document.querySelector('#panel-seo input[name="slug"]');
    var previewTitle = document.getElementById('preview-title');
    var previewDesc = document.getElementById('preview-desc');
    var previewUrl = document.getElementById('preview-url');
    var mainTitle = @json($post->title);
    var baseUrl = '{{ url("p") }}';

    if (seoTitleInput) {
        seoTitleInput.addEventListener('input', function() {
            previewTitle.textContent = this.value || mainTitle;
        });
    }
    if (seoDescInput) {
        seoDescInput.addEventListener('input', function() {
            previewDesc.textContent = this.value || 'No meta description set. Search engines will auto-generate one from your post content.';
        });
    }
    if (seoSlugInput) {
        seoSlugInput.addEventListener('input', function() {
            previewUrl.textContent = baseUrl + '/' + (this.value || @json($post->slug));
        });
    }
})();
</script>
@endsection
