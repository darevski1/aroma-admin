@extends('layouts.master')

@section('title', 'Pages')

@php
    use App\Support\PageTemplateConfig;
@endphp

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">Pages</h3>
            <p class="mb-4">
                Manage all public pages. Edit content, control visibility, and link pages to the site navigation.
            </p>
        </div>
    </div>

    {{-- Success / Error messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('success') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('error') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Pages Table --}}
    <div class="row mb-4 mt-3">
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">All Pages</h6>
                    <a href="{{ route('pages.create') }}" class="btn btn-sm bg-gradient-dark mb-0">
                        <i class="material-symbols-rounded me-1" style="font-size: 16px;">add_page</i>
                        Add Page
                    </a>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Title</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Slug</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Route</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Template</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">In Navigation</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pages as $page)
                                    <tr>
                                        <td class="align-middle">
                                            <span class="text-xs font-weight-bold">{{ $page->id }}</span>
                                        </td>
                                        <td class="align-middle ps-2">
                                            <span class="text-xs font-weight-bold">{{ $page->title }}</span>
                                        </td>
                                        <td class="align-middle">
                                            <code class="text-xs">{{ $page->slug }}</code>
                                        </td>
                                        <td class="align-middle">
                                            <code class="text-xs">{{ $page->route_name ?: '—' }}</code>
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge bg-gradient-secondary">{{ \App\Support\PageTemplateConfig::getLayouts()[$page->layout] ?? $page->layout }}</span>
                                        </td>
                                        <td class="align-middle">
                                            @if ($page->navigations_count > 0)
                                                <span class="badge bg-gradient-success">
                                                    {{ $page->navigations_count }} nav item(s)
                                                </span>
                                            @else
                                                <span class="text-muted text-xs">Not linked</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            @if ($page->published)
                                                <span class="badge bg-gradient-success">Published</span>
                                            @else
                                                <span class="badge bg-gradient-secondary">Draft</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            {{-- Toggle Publish --}}
                                            <form action="{{ route('pages.toggle', $page->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm {{ $page->published ? 'btn-outline-success' : 'btn-outline-secondary' }} me-1" title="{{ $page->published ? 'Unpublish' : 'Publish' }}">
                                                    <i class="material-symbols-rounded" style="font-size: 16px;">{{ $page->published ? 'visibility' : 'visibility_off' }}</i>
                                                </button>
                                            </form>

                                            {{-- Every page opens in the settings/editor hub. --}}
                                            <a href="{{ route('pages.edit-content', $page->id) }}" class="btn btn-sm btn-outline-info me-1" title="Edit">
                                                <i class="material-symbols-rounded" style="font-size: 16px;">edit</i>
                                            </a>

                                            {{-- Settings (for template pages – access general/SEO/media tabs) --}}
                                            <a href="{{ route('pages.edit', $page->id) }}" class="btn btn-sm btn-outline-secondary me-1" title="Page Settings">
                                                <i class="material-symbols-rounded" style="font-size: 16px;">settings</i>
                                            </a>

                                            {{-- View (if published) --}}
                                            @if ($page->published)
                                                <a href="{{ url('pages/' . $page->slug) }}" target="_blank" class="btn btn-sm btn-outline-dark me-1" title="View on site">
                                                    <i class="material-symbols-rounded" style="font-size: 16px;">open_in_new</i>
                                                </a>
                                            @endif

                                            {{-- Delete --}}
                                            <form action="{{ route('pages.destroy', $page->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this page? This will unlink it from navigation.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="material-symbols-rounded" style="font-size: 16px;">delete</i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">No pages found. Create your first page to get started.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
