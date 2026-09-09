@extends('layouts.master')

@section('title', 'Create Post')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">Create New Post</h3>
            <p class="mb-4">
                Write and publish a new blog post.
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

    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row mt-3">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        {{-- Title --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Post Title</label>
                            <div class="input-group input-group-outline">
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                            </div>
                        </div>

                        {{-- Slug --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Slug (auto-generated from title if empty)</label>
                            <div class="input-group input-group-outline">
                                <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="e.g. my-new-post" id="slugInput">
                            </div>
                        </div>

                        {{-- Excerpt --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Excerpt</label>
                            <textarea name="excerpt" class="form-control" rows="3" placeholder="Short summary of the post...">{{ old('excerpt') }}</textarea>
                        </div>

                        {{-- Content --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Content</label>
                            <textarea name="content" class="form-control" rows="12" placeholder="Post content...">{{ old('content') }}</textarea>
                        </div>

                        {{-- Layout --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Layout Template</label>
                            <select name="layout" class="form-control">
                                <option value="posts.default" {{ old('layout') == 'posts.default' ? 'selected' : '' }}>Default (posts.default)</option>
                                <option value="posts.full" {{ old('layout') == 'posts.full' ? 'selected' : '' }}>Full Width (posts.full)</option>
                                <option value="posts.sidebar" {{ old('layout') == 'posts.sidebar' ? 'selected' : '' }}>With Sidebar (posts.sidebar)</option>
                            </select>
                        </div>

                        {{-- Published --}}
                        <div class="form-check form-switch my-3">
                            <input class="form-check-input" type="checkbox" name="published" value="1" {{ old('published') ? 'checked' : '' }} id="publishedCheck">
                            <label class="form-check-label ms-2" for="publishedCheck">Published (visible on site)</label>
                        </div>
                    </div>
                </div>

                {{-- Media Card --}}
                <div class="card">
                    <div class="card-body">
                        <h6 class="text-sm font-weight-bolder mb-3">
                            <i class="material-symbols-rounded me-1" style="font-size: 16px;">photo_library</i>
                            Media
                        </h6>

                        {{-- Featured Image --}}
                        <x-media.media-picker name="featured_image_file" collection="featured_image" label="Featured Image" :multiple="false" :accept="'image/*'" />

                        {{-- Attachments --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Attachments</label>
                            <input type="file" name="attachment_files[]" class="form-control" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.zip">
                            <small class="text-muted">PDF, DOC, XLS, TXT, ZIP. Max 20 MB each.</small>
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
                            <i class="material-symbols-rounded me-1" style="font-size: 16px;">search</i>
                            SEO
                        </h6>

                        {{-- Meta Title --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Meta Title</label>
                            <div class="input-group input-group-outline">
                                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title') }}" maxlength="255">
                            </div>
                            <small class="text-muted">Recommended: 50-60 characters.</small>
                        </div>

                        {{-- Meta Description --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="3" maxlength="500">{{ old('meta_description') }}</textarea>
                            <small class="text-muted">Recommended: 150-160 characters.</small>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="text-sm font-weight-bolder mb-3">Tips</h6>
                        <ul class="text-xs text-muted ps-3" style="list-style: disc;">
                            <li class="mb-2"><strong>Title:</strong> The display name for this post.</li>
                            <li class="mb-2"><strong>Slug:</strong> Used in the URL. Auto-generated from title if left empty.</li>
                            <li class="mb-2"><strong>Excerpt:</strong> A short summary displayed in post listings.</li>
                            <li class="mb-2"><strong>Layout:</strong> Select a Blade template for rendering the post page.</li>
                            <li class="mb-2"><strong>Featured Image:</strong> Main image shown at the top of the post and in social shares.</li>
                            <li class="mb-2"><strong>Published:</strong> Only published posts are visible on the site.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
(function() {
    const titleInput = document.querySelector('input[name="title"]');
    const slugInput = document.getElementById('slugInput');
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
})();
</script>
@endsection
