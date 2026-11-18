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
                    <h6 class="mb-0">{{ $showRemoved ? 'Removed Pages' : 'All Pages' }}</h6>
                    <div class="ms-auto me-3">
                        @if ($showRemoved)
                            <a href="{{ route('pages.index') }}" class="btn btn-sm btn-outline-secondary mb-0">Back to active pages</a>
                        @elseif ($removedCount > 0)
                            <a href="{{ route('pages.index', ['removed' => 1]) }}" class="btn btn-sm btn-outline-secondary mb-0">Removed pages ({{ $removedCount }})</a>
                        @endif
                    </div>
                    <a href="{{ route('pages.create') }}" class="btn btn-sm bg-gradient-dark mb-0">
                        <i class="material-symbols-rounded me-1" style="font-size: 16px;">add_page</i>
                        Add Page
                    </a>
                </div>
                <div class="card-body px-0 pb-2">
                    <style>
                        .pages-table { table-layout: fixed; width: 100%; }
                        .pages-table th, .pages-table td { padding: .5rem .75rem; vertical-align: middle; }
                        .pages-table .col-url { width: 32%; }
                        .pages-table .col-status { width: 110px; }
                        .pages-table .col-actions { width: 230px; }
                        .pages-table .cell-trunc { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
                        @media (max-width: 767.98px) {
                            .pages-table .col-url { display: none; }
                            .pages-table .col-status { width: 90px; }
                            .pages-table .col-actions { width: 150px; }
                            .pages-table .fi-actions { flex-wrap: wrap; }
                        }
                        /* Filament-style soft icon buttons */
                        .fi-actions { display: inline-flex; gap: .3rem; align-items: center; justify-content: center; }
                        .fi-actions form { margin: 0; display: inline-flex; }
                        .fi-act { --c: 107 114 128; --bg: 243 244 246; display: inline-flex; align-items: center; justify-content: center;
                            width: 2rem; height: 2rem; padding: 0; border: 0; border-radius: .5rem; cursor: pointer;
                            color: rgb(var(--c)); background: rgb(var(--bg)); box-shadow: inset 0 0 0 1px rgb(var(--c) / .25);
                            transition: background-color .15s, color .15s, box-shadow .15s; }
                        .fi-act:hover { background: rgb(var(--c)); color: #fff; box-shadow: none; }
                        .fi-act .material-symbols-rounded { font-size: 18px; }
                        .fi-act.success { --c: 22 163 74;  --bg: 240 253 244; }
                        .fi-act.warning { --c: 217 119 6;  --bg: 255 251 235; }
                        .fi-act.info    { --c: 2 132 199;  --bg: 240 249 255; }
                        .fi-act.primary { --c: 124 58 237; --bg: 245 243 255; }
                        .fi-act.danger  { --c: 220 38 38;  --bg: 254 242 242; }
                        .fi-act.gray    { --c: 75 85 99;   --bg: 243 244 246; }
                    </style>
                    <table class="table align-items-center mb-0 pages-table">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Page Title</th>
                                <th class="col-url text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">URL</th>
                                <th class="col-status text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                <th class="col-actions text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pages as $page)
                                @php $urlPath = parse_url($page->public_url, PHP_URL_PATH) ?: '/'; @endphp
                                <tr>
                                    <td>
                                        <span class="cell-trunc text-sm font-weight-bold" title="{{ $page->title }}">{{ $page->title }}</span>
                                    </td>
                                    <td class="col-url">
                                        <a href="{{ $page->public_url }}" target="_blank" class="cell-trunc text-xs" title="{{ $page->public_url }}"><code>{{ $urlPath }}</code></a>
                                    </td>
                                    <td class="col-status">
                                        @if ($page->published)
                                            <span class="badge bg-gradient-success">Published</span>
                                        @else
                                            <span class="badge bg-gradient-secondary">Draft</span>
                                        @endif
                                    </td>
                                    <td class="col-actions text-center">
                                        @if ($page->trashed())
                                            <form action="{{ route('pages.restore', $page->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="fi-act success" title="Restore"><i class="material-symbols-rounded">restore_from_trash</i></button>
                                            </form>
                                        @else
                                            <div class="fi-actions">
                                                <form action="{{ route('pages.toggle', $page->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="fi-act {{ $page->published ? 'success' : 'gray' }}" title="{{ $page->published ? 'Disable (unpublish)' : 'Enable (publish)' }}">
                                                        <i class="material-symbols-rounded">{{ $page->published ? 'toggle_on' : 'toggle_off' }}</i>
                                                    </button>
                                                </form>
                                                <a href="{{ route('pages.editor', $page->id) }}" class="fi-act warning" title="Visual Editor"><i class="material-symbols-rounded">draw</i></a>
                                                <a href="{{ route('pages.seo', $page->id) }}" class="fi-act primary" title="Edit SEO"><i class="material-symbols-rounded">travel_explore</i></a>
                                                <a href="{{ route('pages.edit', $page->id) }}" class="fi-act gray" title="Page settings"><i class="material-symbols-rounded">settings</i></a>
                                                <a href="{{ $page->public_url }}" target="_blank" class="fi-act gray" title="Preview frontend"><i class="material-symbols-rounded">open_in_new</i></a>
                                                <form action="{{ route('pages.destroy', $page->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this page? This will unlink it from navigation.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="fi-act danger" title="Delete"><i class="material-symbols-rounded">delete</i></button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No pages found. Create your first page to get started.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
