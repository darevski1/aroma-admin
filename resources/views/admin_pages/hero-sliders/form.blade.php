@extends('layouts.master')

@php $isEdit = $slider->exists; @endphp

@section('title', $isEdit ? 'Edit Hero Slider' : 'New Hero Slider')

@section('content')
<div class="container-fluid py-2">
    <div class="ms-3 mb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
            <h3 class="mb-0 h4 font-weight-bolder">{{ $isEdit ? $slider->name : 'New Hero Slider' }}</h3>
            <p class="mb-0 text-sm">
                <a href="{{ route('hero-sliders.index') }}">&larr; All sliders</a>
            </p>
        </div>
        @if ($isEdit)
            <div class="me-3 text-end">
                <div class="text-xs text-secondary mb-1">Shortcode — paste it where the slider should appear</div>
                @include('admin_pages.hero-sliders._shortcode', ['tag' => $slider->tag])
            </div>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('success') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger text-white">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    @if ($isEdit)
        {{-- ───────── Slides ───────── --}}
        <div class="card mb-4">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">Slides</h6>
                    <p class="text-xs text-secondary mb-0">Drag rows to change the order; it saves automatically. Disabled slides are hidden on the website.</p>
                </div>
                <a href="{{ route('hero-sliders.slides.create', $slider) }}" class="btn btn-sm bg-gradient-dark mb-0">
                    <i class="material-symbols-rounded me-1" style="font-size:16px;">add</i> Add Slide
                </a>
            </div>
            <div class="card-body px-0 pb-2">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th style="width:40px;"></th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Desktop</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Mobile</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Content</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="slides-body">
                            @forelse ($slider->slides as $slide)
                                <tr data-id="{{ $slide->id }}">
                                    <td class="align-middle">
                                        <i class="material-symbols-rounded text-muted drag-handle" style="font-size:18px;cursor:grab;">drag_indicator</i>
                                    </td>
                                    <td class="align-middle"><span class="text-xs font-weight-bold order-badge">{{ $loop->iteration }}</span></td>
                                    <td class="align-middle">
                                        @if ($slide->desktop_url)
                                            <img src="{{ $slide->desktop_url }}" alt="" style="height:44px;width:80px;object-fit:cover;border-radius:4px;">
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if ($slide->mobile_url)
                                            <img src="{{ $slide->mobile_url }}" alt="" style="height:44px;width:30px;object-fit:cover;border-radius:4px;">
                                        @else
                                            <span class="text-xs text-muted">Uses desktop</span>
                                        @endif
                                    </td>
                                    <td class="align-middle" style="max-width:320px;white-space:normal;">
                                        <span class="text-sm font-weight-bold d-block">{{ $slide->title ?: '(no title)' }}</span>
                                        @if ($slide->subtitle)<span class="text-xs text-secondary d-block">{{ $slide->subtitle }}</span>@endif
                                        @if ($slide->hasButton())<span class="text-xs d-block">Button: {{ $slide->button_text }} → {{ \Illuminate\Support\Str::limit($slide->button_url, 40) }}</span>@endif
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge status-badge {{ $slide->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">{{ $slide->is_active ? 'Enabled' : 'Disabled' }}</span>
                                    </td>
                                    <td class="align-middle text-center text-nowrap">
                                        <button type="button" class="btn btn-sm btn-outline-secondary toggle-btn mb-0" data-url="{{ route('hero-sliders.slides.toggle', [$slider, $slide]) }}">
                                            {{ $slide->is_active ? 'Disable' : 'Enable' }}
                                        </button>
                                        <a href="{{ route('hero-sliders.slides.edit', [$slider, $slide]) }}" class="btn btn-sm btn-outline-primary mb-0">Edit</a>
                                        <form action="{{ route('hero-sliders.slides.duplicate', [$slider, $slide]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-info mb-0">Duplicate</button>
                                        </form>
                                        <form action="{{ route('hero-sliders.slides.destroy', [$slider, $slide]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this slide?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger mb-0">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-4">No slides yet. A slider without enabled slides shows nothing on the website.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- ───────── Settings ───────── --}}
    <form method="POST" action="{{ $isEdit ? route('hero-sliders.update', $slider) : route('hero-sliders.store') }}">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="card mb-4">
            <div class="card-header pb-0"><h6 class="mb-0">Slider settings</h6></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name (internal) *</label>
                        <input type="text" name="name" class="form-control border px-2" required maxlength="255" placeholder="e.g. CenoBots models hero" value="{{ old('name', $slider->name) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label d-block">Status</label>
                        <div class="form-check form-switch ps-0 d-flex align-items-center gap-2 mt-2">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input ms-0" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $slider->is_active))>
                            <label class="form-check-label mb-0" for="is_active">Slider enabled (shown on the website)</label>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Show on page</label>
                        <select name="page_id" class="form-select border px-2">
                            <option value="">— None (use the shortcode instead) —</option>
                            @foreach (\App\Models\Page::orderBy('title')->get(['id', 'title', 'slug']) as $p)
                                <option value="{{ $p->id }}" @selected((string) old('page_id', $slider->page_id) === (string) $p->id)>{{ $p->title }} (/{{ $p->slug }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Position on the page</label>
                        <select name="placement" class="form-select border px-2">
                            @foreach (\App\Models\HeroSlider::PLACEMENTS as $value => $label)
                                <option value="{{ $value }}" @selected(old('placement', $slider->placement ?: 'top') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Transition</label>
                        <select name="transition" class="form-select border px-2">
                            @foreach (\App\Models\HeroSlider::TRANSITIONS as $value => $label)
                                <option value="{{ $value }}" @selected(old('transition', $slider->transition) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Transition speed (ms)</label>
                        <input type="number" name="transition_ms" min="100" max="3000" step="50" class="form-control border px-2" value="{{ old('transition_ms', $slider->transition_ms) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Time per slide (ms)</label>
                        <input type="number" name="interval_ms" min="1500" max="60000" step="500" class="form-control border px-2" value="{{ old('interval_ms', $slider->interval_ms) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label d-block">Autoplay</label>
                        <div class="form-check form-switch ps-0 d-flex align-items-center gap-2 mt-2">
                            <input type="hidden" name="autoplay" value="0">
                            <input class="form-check-input ms-0" type="checkbox" name="autoplay" value="1" id="autoplay" @checked(old('autoplay', $slider->autoplay))>
                            <label class="form-check-label mb-0" for="autoplay">Change slides automatically</label>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Height on desktop (% of screen)</label>
                        <input type="number" name="height_desktop" min="20" max="100" class="form-control border px-2" value="{{ old('height_desktop', $slider->height_desktop) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Height on mobile (% of screen)</label>
                        <input type="number" name="height_mobile" min="20" max="100" class="form-control border px-2" value="{{ old('height_mobile', $slider->height_mobile) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label d-block">Controls</label>
                        <div class="d-flex flex-wrap gap-4 mt-2">
                            @foreach (['show_arrows' => 'Previous / next arrows', 'show_dots' => 'Pagination dots', 'pause_on_hover' => 'Pause on hover', 'full_width' => 'Full screen width'] as $flag => $label)
                                <div class="form-check form-switch ps-0 d-flex align-items-center gap-2">
                                    <input type="hidden" name="{{ $flag }}" value="0">
                                    <input class="form-check-input ms-0" type="checkbox" name="{{ $flag }}" value="1" id="{{ $flag }}" @checked(old($flag, $slider->$flag))>
                                    <label class="form-check-label mb-0" for="{{ $flag }}">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex gap-2">
                <button class="btn bg-gradient-dark mb-0">{{ $isEdit ? 'Save settings' : 'Create slider' }}</button>
                <a href="{{ route('hero-sliders.index') }}" class="btn btn-outline-secondary mb-0">Cancel</a>
            </div>
        </div>
    </form>
</div>

@include('admin_pages.hero-sliders._scripts')
@if ($isEdit)
<script src="https://cdn.jsdelivr.net/sortablejs/1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var tbody = document.getElementById('slides-body');
    heroAdmin.bindToggles(tbody, 'Enabled', 'Disabled');

    if (window.Sortable && tbody.querySelector('tr[data-id]')) {
        Sortable.create(tbody, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function () {
                var order = [];
                tbody.querySelectorAll('tr[data-id]').forEach(function (row, i) {
                    row.querySelector('.order-badge').textContent = i + 1;
                    order.push(row.dataset.id);
                });
                fetch('{{ route('hero-sliders.slides.reorder', $slider) }}', {
                    method: 'POST',
                    headers: Object.assign({ 'Content-Type': 'application/json' }, heroAdmin.headers),
                    body: JSON.stringify({ order: order })
                }).then(function (r) { if (!r.ok) throw new Error(); })
                  .catch(function () { alert('Failed to save the slide order.'); });
            }
        });
    }
});
</script>
@endif
@endsection
