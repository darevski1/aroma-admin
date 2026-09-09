@extends('layouts.master')

@section('title', 'Add New Homepage Section')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">Add New Section</h3>
            <p class="mb-4">
                Create a new homepage section and add its content. After saving, it appears in the
                Homepage Sections list where you can publish/unpublish and drag &amp; drop to reorder it.
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

    <div class="row mt-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('homepage-sections.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Section Name / Title --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Section Name / Title <span class="text-danger">*</span></label>
                            <div class="input-group input-group-outline">
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                            </div>
                            <small class="text-muted">Shown in the Homepage Sections management list.</small>
                        </div>

                        {{-- Slug --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Slug (auto-generated from name if empty)</label>
                            <div class="input-group input-group-outline">
                                <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="e.g. summer-promo">
                            </div>
                            <small class="text-muted">Lowercase letters, numbers and dashes. Slugs of built-in sections (hero, services, cta, ...) are reserved.</small>
                        </div>

                        <hr>

                        {{-- Section Content --}}
                        <h6 class="text-sm font-weight-bolder mb-3">Section Content</h6>
                        <p class="text-xs text-muted mb-3">
                            All content fields are optional — only filled fields are rendered on the homepage,
                            using the same components as the existing sections (eyebrow label, heading, text, image and buttons).
                        </p>

                        {{-- Eyebrow --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Eyebrow Label (small text above heading)</label>
                            <div class="input-group input-group-outline">
                                <input type="text" name="eyebrow" class="form-control" value="{{ old('eyebrow') }}" placeholder="e.g. explore">
                            </div>
                        </div>

                        {{-- Heading --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Heading</label>
                            <div class="input-group input-group-outline">
                                <input type="text" name="heading" class="form-control" value="{{ old('heading') }}">
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Body Text</label>
                            <div class="input-group input-group-outline">
                                <textarea name="body" class="form-control" rows="4">{{ old('body') }}</textarea>
                            </div>
                        </div>

                        {{-- Image --}}
                        <div class="mb-3">
                            <x-media.media-picker name="image_file" collection="homepage-sections" label="Image (optional)" :multiple="false" :accept="'image/jpeg,image/png,image/webp,image/avif,image/gif,image/svg+xml'" />
                        </div>

                        {{-- Buttons --}}
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-xs font-weight-bolder mb-2">Primary Button</h6>
                                <div class="input-group input-group-outline mb-2">
                                    <input type="text" name="button1_label" class="form-control" value="{{ old('button1_label') }}" placeholder="Button label">
                                </div>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="text" name="button1_url" class="form-control" value="{{ old('button1_url') }}" placeholder="/kontakt or https://...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-xs font-weight-bolder mb-2">Secondary Button</h6>
                                <div class="input-group input-group-outline mb-2">
                                    <input type="text" name="button2_label" class="form-control" value="{{ old('button2_label') }}" placeholder="Button label">
                                </div>
                                <div class="input-group input-group-outline mb-3">
                                    <input type="text" name="button2_url" class="form-control" value="{{ old('button2_url') }}" placeholder="/kontakt or https://...">
                                </div>
                            </div>
                        </div>

                        {{-- Background --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Background Style</label>
                            <div class="input-group input-group-outline">
                                <select name="background" class="form-control">
                                    <option value="light" {{ old('background', 'light') == 'light' ? 'selected' : '' }}>Light Gray</option>
                                    <option value="white" {{ old('background') == 'white' ? 'selected' : '' }}>White</option>
                                    <option value="dark" {{ old('background') == 'dark' ? 'selected' : '' }}>Dark</option>
                                    <option value="accent" {{ old('background') == 'accent' ? 'selected' : '' }}>Accent (Coral)</option>
                                </select>
                            </div>
                        </div>

                        <hr>

                        {{-- Published --}}
                        <div class="form-check form-switch my-3">
                            <input class="form-check-input" type="checkbox" name="published" value="1" {{ old('published', true) ? 'checked' : '' }} id="publishedCheck">
                            <label class="form-check-label ms-2" for="publishedCheck">Publish immediately (visible on homepage)</label>
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn bg-gradient-dark">
                                <i class="material-symbols-rounded me-1" style="font-size: 16px;">save</i>
                                Save Section
                            </button>
                            <a href="{{ route('homepage-sections.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar Tips --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-sm font-weight-bolder mb-3">How it works</h6>
                    <ul class="text-xs text-muted ps-3" style="list-style: disc;">
                        <li class="mb-2"><strong>Name:</strong> The display name in the management list.</li>
                        <li class="mb-2"><strong>Content:</strong> The section is rendered on the homepage with the same building blocks used by existing sections — eyebrow label, heading, body text, an optional image and up to two buttons.</li>
                        <li class="mb-2"><strong>Order:</strong> The new section is added at the end of the list. Use drag &amp; drop on the list page to reposition it.</li>
                        <li class="mb-2"><strong>Publish:</strong> Only published sections appear on the public homepage. You can toggle this anytime from the list.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
