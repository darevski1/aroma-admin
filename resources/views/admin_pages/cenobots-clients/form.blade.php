@extends('layouts.master')

@php $isEdit = $client->exists; @endphp

@section('title', $isEdit ? 'Edit Project' : 'Add Project')

@section('content')
<div class="container-fluid py-2">
    <div class="ms-3 mb-3">
        <h3 class="mb-0 h4 font-weight-bolder">{{ $isEdit ? 'Edit Project' : 'Add New Project' }}</h3>
        <p class="mb-0 text-sm">Shown as a slide in the "Проекти" slider on /services/cenobots.</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $isEdit ? route('cenobots-clients.update', $client) : route('cenobots-clients.store') }}">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Project / client name *</label>
                        <input type="text" name="name" class="form-control border px-2" required maxlength="255" value="{{ old('name', $client->name) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category (small label above the name)</label>
                        <input type="text" name="category" class="form-control border px-2" maxlength="255" placeholder="e.g. Шопинг центар" value="{{ old('category', $client->category) }}">
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" maxlength="500" class="form-control border px-2">{{ old('description', $client->description) }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Footer line</label>
                        <input type="text" name="foot_text" class="form-control border px-2" maxlength="255" value="{{ old('foot_text', $client->foot_text) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Website URL (optional)</label>
                        <input type="url" name="website_url" class="form-control border px-2" placeholder="https://example.com" value="{{ old('website_url', $client->website_url) }}">
                    </div>

                    {{-- Client logo --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label d-block">Client logo (from Media Library)</label>
                        @php $logoUrl = old('logo_media_id', $client->logo_media_id) ? \Spatie\MediaLibrary\MediaCollections\Models\Media::find(old('logo_media_id', $client->logo_media_id))?->getUrl() : null; @endphp
                        <input type="hidden" name="logo_media_id" id="logo_media_id" value="{{ old('logo_media_id', $client->logo_media_id) }}">
                        <div class="d-flex align-items-center gap-3">
                            <div id="logo-preview" class="border rounded d-flex align-items-center justify-content-center bg-light" style="width:160px;height:90px;overflow:hidden;">
                                @if ($logoUrl)
                                    <img src="{{ $logoUrl }}" alt="" style="max-width:100%;max-height:100%;object-fit:contain;">
                                @else
                                    <span class="text-xs text-muted">No logo</span>
                                @endif
                            </div>
                            <div class="d-flex flex-column gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary mb-0" data-pick="logo">Choose / Upload logo</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary mb-0" data-clear="logo">Remove</button>
                            </div>
                        </div>
                    </div>

                    {{-- Project photo --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label d-block">Project photo (from Media Library)</label>
                        @php $photoUrl = old('photo_media_id', $client->photo_media_id) ? \Spatie\MediaLibrary\MediaCollections\Models\Media::find(old('photo_media_id', $client->photo_media_id))?->getUrl() : null; @endphp
                        <input type="hidden" name="photo_media_id" id="photo_media_id" value="{{ old('photo_media_id', $client->photo_media_id) }}">
                        <div class="d-flex align-items-center gap-3">
                            <div id="photo-preview" class="border rounded d-flex align-items-center justify-content-center bg-light" style="width:160px;height:90px;overflow:hidden;">
                                @if ($photoUrl)
                                    <img src="{{ $photoUrl }}" alt="" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <span class="text-xs text-muted text-center px-2">Default CenoBots photo</span>
                                @endif
                            </div>
                            <div class="d-flex flex-column gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary mb-0" data-pick="photo">Choose / Upload photo</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary mb-0" data-clear="photo">Remove</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Logo alt text</label>
                        <input type="text" name="logo_alt" class="form-control border px-2" maxlength="255" value="{{ old('logo_alt', $client->logo_alt) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Sort order</label>
                        <input type="number" name="sort_order" min="0" class="form-control border px-2" value="{{ old('sort_order', $client->sort_order) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select border px-2">
                            <option value="1" @selected(old('is_active', $client->is_active ? '1' : '0') == '1')>Active</option>
                            <option value="0" @selected(old('is_active', $client->is_active ? '1' : '0') == '0')>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex gap-2">
                <button class="btn bg-gradient-dark mb-0">{{ $isEdit ? 'Save changes' : 'Add project' }}</button>
                <a href="{{ route('cenobots-clients.index') }}" class="btn btn-outline-secondary mb-0">Cancel</a>
            </div>
        </div>
    </form>
</div>

{{-- Media Library picker (uses the existing /admin/media-picker endpoints) --}}
<div class="modal fade" id="mediaPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="picker-title">Select image from Media Library</h6>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex gap-2 mb-3">
                    <input type="search" id="picker-search" class="form-control border px-2" placeholder="Search media...">
                    <label class="btn btn-sm btn-outline-primary mb-0 d-flex align-items-center text-nowrap">
                        Upload new <input type="file" id="picker-upload" accept="image/*" hidden>
                    </label>
                </div>
                <div id="picker-grid" class="row g-2"></div>
                <p id="picker-empty" class="text-center text-muted d-none">No images found.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var csrf = '{{ csrf_token() }}';
    var modalEl = document.getElementById('mediaPickerModal');
    var grid = document.getElementById('picker-grid');
    var empty = document.getElementById('picker-empty');
    var timer;
    var modal;
    var target = 'logo';

    // Per field: where the id goes, where the preview shows, how the image fits.
    var fields = {
        logo:  {input: 'logo_media_id',  preview: 'logo-preview',  fit: 'contain', label: 'logo',  none: 'No logo'},
        photo: {input: 'photo_media_id', preview: 'photo-preview', fit: 'cover',   label: 'photo', none: 'Default CenoBots photo'}
    };

    function getModal() {
        if (!modal) modal = new bootstrap.Modal(modalEl);
        return modal;
    }

    function showPreview(kind, url) {
        var f = fields[kind];
        var box = document.getElementById(f.preview);
        box.innerHTML = '';
        if (!url) {
            var span = document.createElement('span');
            span.className = 'text-xs text-muted text-center px-2';
            span.textContent = f.none;
            box.appendChild(span);
            return;
        }
        var img = document.createElement('img');
        img.src = url;
        img.style.cssText = f.fit === 'cover' ? 'width:100%;height:100%;object-fit:cover;' : 'max-width:100%;max-height:100%;object-fit:contain;';
        box.appendChild(img);
    }

    function choose(item) {
        document.getElementById(fields[target].input).value = item.id;
        showPreview(target, item.url);
        if (target === 'logo') {
            var alt = document.querySelector('[name=logo_alt]');
            if (!alt.value) alt.value = item.name;
        }
        getModal().hide();
    }

    function render(items) {
        grid.innerHTML = '';
        empty.classList.toggle('d-none', items.length > 0);
        items.forEach(function (item) {
            var col = document.createElement('div');
            col.className = 'col-4 col-md-3';
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-outline-secondary w-100 p-1 mb-0';
            btn.title = item.name;
            btn.style.height = '90px';
            var img = document.createElement('img');
            img.src = item.thumb_url || item.url;
            img.alt = item.name;
            img.style.cssText = 'max-width:100%;max-height:100%;object-fit:contain;';
            btn.appendChild(img);
            btn.addEventListener('click', function () { choose(item); });
            col.appendChild(btn);
            grid.appendChild(col);
        });
    }

    function load() {
        var q = encodeURIComponent(document.getElementById('picker-search').value);
        fetch('{{ route('media.picker') }}?mime_type=image/&per_page=60&search=' + q, {headers: {'Accept': 'application/json'}})
            .then(function (r) { return r.json(); })
            .then(function (d) { render(d.data || []); });
    }

    document.querySelectorAll('[data-pick]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            target = btn.getAttribute('data-pick');
            document.getElementById('picker-title').textContent = 'Select ' + fields[target].label + ' from Media Library';
            getModal().show();
            load();
        });
    });

    document.querySelectorAll('[data-clear]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var kind = btn.getAttribute('data-clear');
            document.getElementById(fields[kind].input).value = '';
            showPreview(kind, null);
        });
    });

    document.getElementById('picker-search').addEventListener('input', function () { clearTimeout(timer); timer = setTimeout(load, 300); });

    document.getElementById('picker-upload').addEventListener('change', function () {
        if (!this.files.length) return;
        var fd = new FormData();
        fd.append('file', this.files[0]);
        fd.append('collection', 'cenobots-projects');
        fetch('{{ route('media.picker.upload') }}', {method: 'POST', headers: {'X-CSRF-TOKEN': csrf, 'Accept': 'application/json'}, body: fd})
            .then(function (r) { if (!r.ok) throw new Error(); return r.json(); })
            .then(choose)
            .catch(function () { alert('Upload failed (images up to 10 MB).'); });
        this.value = '';
    });
});
</script>
@endsection
