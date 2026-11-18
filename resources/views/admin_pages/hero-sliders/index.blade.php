@extends('layouts.master')

@section('title', 'Hero Sliders')

@section('content')
<div class="container-fluid py-2">
    <div class="ms-3">
        <h3 class="mb-0 h4 font-weight-bolder">Hero Sliders</h3>
        <p class="mb-4">
            Each slider gets its own shortcode, e.g. <code>[2026-1]</code>. Paste the shortcode into the content of any public page
            (visual editor, page content, posts, homepage sections) and the slider appears there. One slider can be used on as many pages as you like.
        </p>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('success') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">All Sliders</h6>
            <a href="{{ route('hero-sliders.create') }}" class="btn btn-sm bg-gradient-dark mb-0">
                <i class="material-symbols-rounded me-1" style="font-size:16px;">add</i> New Slider
            </a>
        </div>
        <div class="card-body px-0 pb-2">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Name</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Shortcode</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Slides</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="sliders-body">
                        @forelse ($sliders as $slider)
                            <tr data-id="{{ $slider->id }}">
                                <td class="align-middle ps-4">
                                    <a href="{{ route('hero-sliders.edit', $slider) }}" class="text-sm font-weight-bold text-dark">{{ $slider->name }}</a>
                                </td>
                                <td class="align-middle">
                                    @include('admin_pages.hero-sliders._shortcode', ['tag' => $slider->tag])
                                </td>
                                <td class="align-middle">
                                    <span class="text-xs">{{ $slider->active_slides_count }} active / {{ $slider->slides_count }} total</span>
                                </td>
                                <td class="align-middle">
                                    <span class="badge status-badge {{ $slider->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">{{ $slider->is_active ? 'Enabled' : 'Disabled' }}</span>
                                </td>
                                <td class="align-middle text-center text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-secondary toggle-btn mb-0" data-url="{{ route('hero-sliders.toggle', $slider) }}">
                                        {{ $slider->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                    <a href="{{ route('hero-sliders.edit', $slider) }}" class="btn btn-sm btn-outline-primary mb-0">Edit</a>
                                    <form action="{{ route('hero-sliders.duplicate', $slider) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-outline-info mb-0">Duplicate</button>
                                    </form>
                                    <form action="{{ route('hero-sliders.destroy', $slider) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this slider and all its slides? Pages using {{ $slider->tag }} will show nothing in its place.');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger mb-0">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">No sliders yet. Create one to get its shortcode.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('admin_pages.hero-sliders._scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    heroAdmin.bindToggles(document.getElementById('sliders-body'), 'Enabled', 'Disabled');
});
</script>
@endsection
