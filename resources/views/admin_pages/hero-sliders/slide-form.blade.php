@extends('layouts.master')

@php $isEdit = $slide->exists; @endphp

@section('title', $isEdit ? 'Edit Slide' : 'Add Slide')

@section('content')
<div class="container-fluid py-2">
    <div class="ms-3 mb-3">
        <h3 class="mb-0 h4 font-weight-bolder">{{ $isEdit ? 'Edit Slide' : 'Add Slide' }}</h3>
        <p class="mb-0 text-sm">
            <a href="{{ route('hero-sliders.edit', $slider) }}">&larr; {{ $slider->name }}</a>
            <span class="text-secondary ms-2">{{ $slider->tag }}</span>
        </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger text-white">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" enctype="multipart/form-data"
        action="{{ $isEdit ? route('hero-sliders.slides.update', [$slider, $slide]) : route('hero-sliders.slides.store', $slider) }}">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="card mb-4">
            <div class="card-header pb-0"><h6 class="mb-0">Images</h6></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-7 mb-3">
                        <label class="form-label d-block">Desktop image {{ $isEdit ? '' : '*' }}</label>
                        <div class="border rounded bg-light mb-2 d-flex align-items-center justify-content-center" style="aspect-ratio:16/7;overflow:hidden;max-width:520px;">
                            <img id="desktop-preview" src="{{ $slide->desktop_url }}" alt="" style="width:100%;height:100%;object-fit:cover;object-position:{{ old('image_position', $slide->image_position) }};{{ $slide->desktop_url ? '' : 'display:none;' }}">
                            <span class="text-xs text-muted preview-empty" @if ($slide->desktop_url) style="display:none" @endif>No image</span>
                        </div>
                        <input type="file" name="desktop_image" accept="image/jpeg,image/png,image/webp,image/avif" class="form-control border px-2" data-preview="desktop-preview" @unless ($isEdit) required @endunless>
                        <small class="text-xs text-secondary">JPG, PNG, WebP or AVIF, max 8 MB, at least 800×300 px. Recommended 1920×900 px. Large images are resized and converted to WebP automatically.</small>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label d-block">Mobile image (optional)</label>
                        <div class="border rounded bg-light mb-2 d-flex align-items-center justify-content-center" style="aspect-ratio:3/4;overflow:hidden;max-width:200px;">
                            <img id="mobile-preview" src="{{ $slide->mobile_url }}" alt="" style="width:100%;height:100%;object-fit:cover;object-position:{{ old('image_position', $slide->image_position) }};{{ $slide->mobile_url ? '' : 'display:none;' }}">
                            <span class="text-xs text-muted preview-empty text-center px-2" @if ($slide->mobile_url) style="display:none" @endif>Uses the desktop image</span>
                        </div>
                        <input type="file" name="mobile_image" accept="image/jpeg,image/png,image/webp,image/avif" class="form-control border px-2" data-preview="mobile-preview">
                        <small class="text-xs text-secondary">Shown on screens up to 767 px wide. Max 5 MB, at least 320×320 px. Recommended 900×1200 px.</small>
                        @if ($slide->mobile_image)
                            <div class="form-check mt-2 ps-0 d-flex align-items-center gap-2">
                                <input class="form-check-input ms-0" type="checkbox" name="remove_mobile_image" value="1" id="remove_mobile_image">
                                <label class="form-check-label mb-0" for="remove_mobile_image">Remove mobile image</label>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-5 mb-3">
                        <label class="form-label">Image alt text</label>
                        <input type="text" name="image_alt" class="form-control border px-2" maxlength="255" placeholder="Describe the image for screen readers / SEO" value="{{ old('image_alt', $slide->image_alt) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Image focus point</label>
                        <select name="image_position" id="image_position" class="form-select border px-2">
                            @foreach (\App\Models\HeroSlide::IMAGE_POSITIONS as $value => $label)
                                <option value="{{ $value }}" @selected(old('image_position', $slide->image_position) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <small class="text-xs text-secondary">Which part of the image stays visible when it is cropped.</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Dark overlay: <span id="overlay-value">{{ old('overlay_opacity', $slide->overlay_opacity) }}</span>%</label>
                        <input type="range" name="overlay_opacity" min="0" max="90" step="5" class="form-range" value="{{ old('overlay_opacity', $slide->overlay_opacity) }}"
                            oninput="document.getElementById('overlay-value').textContent = this.value">
                        <small class="text-xs text-secondary">Darkens the image so the text stays readable.</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header pb-0"><h6 class="mb-0">Text &amp; button</h6></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Subtitle (small line above the title)</label>
                        <input type="text" name="subtitle" class="form-control border px-2" maxlength="255" value="{{ old('subtitle', $slide->subtitle) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control border px-2" maxlength="255" value="{{ old('title', $slide->title) }}">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" maxlength="2000" class="form-control border px-2">{{ old('description', $slide->description) }}</textarea>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Text alignment</label>
                        <select name="text_alignment" class="form-select border px-2">
                            @foreach (\App\Models\HeroSlide::TEXT_ALIGNMENTS as $value => $label)
                                <option value="{{ $value }}" @selected(old('text_alignment', $slide->text_alignment) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Button text</label>
                        <input type="text" name="button_text" class="form-control border px-2" maxlength="100" placeholder="e.g. Learn more" value="{{ old('button_text', $slide->button_text) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Button URL</label>
                        <input type="text" name="button_url" class="form-control border px-2" maxlength="2048" placeholder="/kontakt, #section or https://…" value="{{ old('button_url', $slide->button_url) }}">
                    </div>
                    <div class="col-12 mb-1 d-flex flex-wrap gap-4">
                        <div class="form-check form-switch ps-0 d-flex align-items-center gap-2">
                            <input type="hidden" name="button_new_tab" value="0">
                            <input class="form-check-input ms-0" type="checkbox" name="button_new_tab" value="1" id="button_new_tab" @checked(old('button_new_tab', $slide->button_new_tab))>
                            <label class="form-check-label mb-0" for="button_new_tab">Open link in a new tab</label>
                        </div>
                        <div class="form-check form-switch ps-0 d-flex align-items-center gap-2">
                            <input type="hidden" name="link_whole_slide" value="0">
                            <input class="form-check-input ms-0" type="checkbox" name="link_whole_slide" value="1" id="link_whole_slide" @checked(old('link_whole_slide', $slide->link_whole_slide))>
                            <label class="form-check-label mb-0" for="link_whole_slide">Make the whole slide clickable</label>
                        </div>
                        <div class="form-check form-switch ps-0 d-flex align-items-center gap-2">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input ms-0" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $slide->is_active))>
                            <label class="form-check-label mb-0" for="is_active">Slide enabled</label>
                        </div>
                    </div>
                    <div class="col-12"><small class="text-xs text-secondary">Leave the button text empty to hide the button. A clickable slide still needs a URL.</small></div>
                </div>
            </div>
            <div class="card-footer d-flex gap-2">
                <button class="btn bg-gradient-dark mb-0">{{ $isEdit ? 'Save slide' : 'Add slide' }}</button>
                <a href="{{ route('hero-sliders.edit', $slider) }}" class="btn btn-outline-secondary mb-0">Cancel</a>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[type=file][data-preview]').forEach(function (input) {
        input.addEventListener('change', function () {
            var img = document.getElementById(input.dataset.preview);
            var file = input.files && input.files[0];
            if (!img || !file) return;
            img.src = URL.createObjectURL(file);
            img.style.display = '';
            var empty = img.parentNode.querySelector('.preview-empty');
            if (empty) empty.style.display = 'none';
        });
    });
    var pos = document.getElementById('image_position');
    pos.addEventListener('change', function () {
        ['desktop-preview', 'mobile-preview'].forEach(function (id) {
            document.getElementById(id).style.objectPosition = pos.value;
        });
    });
});
</script>
@endsection
