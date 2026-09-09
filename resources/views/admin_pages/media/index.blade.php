@extends('layouts.master')

@section('title', 'Media Library')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">Media Library</h3>
            <p class="mb-4">Manage all uploaded media files.</p>
        </div>
    </div>

    {{-- Flash Messages --}}
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

    {{-- Toolbar --}}
    <div class="row mb-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body py-2">
                    <form id="mediaFilterForm" method="GET" action="{{ route('media.index') }}">
                        <div class="row align-items-center g-2">
                            {{-- Search --}}
                            <div class="col-lg-3 col-md-4">
                                <div class="input-group input-group-outline">
                                    <label class="form-label"></label>
                                    <input type="text" name="search" class="form-control" placeholder="Search files..." value="{{ request('search') }}">
                                </div>
                            </div>
                            {{-- Collection Filter --}}
                            <div class="col-lg-2 col-md-3">
                                <select name="collection" class="form-select" id="collectionFilter">
                                    <option value="">All Collections</option>
                                    @foreach($collections as $col)
                                        <option value="{{ $col }}" {{ request('collection') == $col ? 'selected' : '' }}>{{ ucfirst($col) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- MIME Type Filter --}}
                            <div class="col-lg-2 col-md-3">
                                <select name="mime_type" class="form-select" id="mimeFilter">
                                    <option value="">All Types</option>
                                    <option value="image" {{ request('mime_type') == 'image' ? 'selected' : '' }}>Images</option>
                                    <option value="video" {{ request('mime_type') == 'video' ? 'selected' : '' }}>Videos</option>
                                    <option value="audio" {{ request('mime_type') == 'audio' ? 'selected' : '' }}>Audio</option>
                                    <option value="application/pdf" {{ request('mime_type') == 'application/pdf' ? 'selected' : '' }}>PDF</option>
                                    <option value="text" {{ request('mime_type') == 'text' ? 'selected' : '' }}>Text</option>
                                </select>
                            </div>
                            {{-- Sort --}}
                            <div class="col-lg-2 col-md-3">
                                <select name="sort" class="form-select" id="sortFilter">
                                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date</option>
                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                                    <option value="size" {{ request('sort') == 'size' ? 'selected' : '' }}>Size</option>
                                </select>
                            </div>
                            {{-- Actions --}}
                            <div class="col-lg-3 col-md-5 text-end">
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary mb-0">
                                        <i class="material-symbols-rounded" style="font-size:16px;">filter_list</i> Filter
                                    </button>
                                    <button type="button" class="btn btn-sm bg-gradient-dark mb-0" data-bs-toggle="modal" data-bs-target="#uploadModal">
                                        <i class="material-symbols-rounded" style="font-size:16px;">cloud_upload</i> Upload
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger mb-0 d-none" id="bulkDeleteBtn">
                                        <i class="material-symbols-rounded" style="font-size:16px;">delete</i> Delete Selected (<span id="selectedCount">0</span>)
                                    </button>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-secondary active" id="gridViewBtn" title="Grid View">
                                            <i class="material-symbols-rounded" style="font-size:16px;">grid_view</i>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="listViewBtn" title="List View">
                                            <i class="material-symbols-rounded" style="font-size:16px;">view_list</i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Media Grid --}}
    <div id="mediaGrid">
        <div class="row" id="mediaContainer">
            @forelse($media as $item)
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-4 media-item" data-id="{{ $item->id }}">
                    <div class="card media-card h-100">
                        <div class="card-body p-2">
                            <div class="form-check position-absolute" style="top:8px;left:8px;z-index:2;">
                                <input class="form-check-input media-select" type="checkbox" value="{{ $item->id }}" data-id="{{ $item->id }}">
                            </div>
                            <div class="position-absolute d-flex gap-1" style="top:6px;right:6px;z-index:2;">
                                @if(Str::startsWith($item->mime_type, 'image/'))
                                <button type="button" class="btn btn-sm btn-white px-1 py-0 media-preview-btn" data-id="{{ $item->id }}" data-url="{{ $item->model_type ? $item->getUrl() : $item->getUrl() }}" data-name="{{ $item->name }}" title="Preview" onclick="event.stopPropagation();">
                                    <i class="material-symbols-rounded" style="font-size:16px;">visibility</i>
                                </button>
                                @endif
                                <button type="button" class="btn btn-sm btn-white px-1 py-0 media-delete-btn" data-id="{{ $item->id }}" data-name="{{ $item->name }}" title="Delete" onclick="event.stopPropagation();">
                                    <i class="material-symbols-rounded text-danger" style="font-size:16px;">delete</i>
                                </button>
                            </div>
                            <div class="media-thumb text-center mb-2" style="height:120px;overflow:hidden;background:#f3f4f6;border-radius:8px;">
                                @if(Str::startsWith($item->mime_type, 'image/'))
                                    <img src="{{ $item->model_type ? $item->getUrl('thumb') : $item->getUrl() }}" alt="{{ $item->name }}" style="width:100%;height:120px;object-fit:cover;border-radius:8px;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <i class="material-symbols-rounded text-muted" style="font-size:48px;">{{ $item->mime_type === 'application/pdf' ? 'picture_as_pdf' : (Str::startsWith($item->mime_type, 'video/') ? 'videocam' : (Str::startsWith($item->mime_type, 'audio/') ? 'audiotrack' : 'insert_drive_file')) }}</i>
                                    </div>
                                @endif
                            </div>
                            <div class="d-flex align-items-center gap-1 mb-1">
                                <p class="text-xs text-dark mb-0 text-truncate flex-grow-1 media-name-display" data-id="{{ $item->id }}" title="{{ $item->name }}">{{ $item->name }}</p>
                                <button type="button" class="btn btn-link p-0 flex-shrink-0 media-edit-name-btn" data-id="{{ $item->id }}" data-name="{{ $item->name }}" title="Edit name" style="line-height:1;" onclick="event.stopPropagation();">
                                    <i class="material-symbols-rounded text-muted" style="font-size:14px;">edit</i>
                                </button>
                            </div>
                            <div class="d-none media-name-edit-form" data-id="{{ $item->id }}">
                                <div class="input-group input-group-outline input-group-sm">
                                    <label class="form-label"></label>
                                    <input type="text" class="form-control form-control-sm media-name-input" data-id="{{ $item->id }}" value="{{ $item->name }}">
                                </div>
                                <div class="d-flex gap-1 mt-1">
                                    <button type="button" class="btn btn-sm bg-gradient-info py-0 px-2 media-save-name-btn" data-id="{{ $item->id }}" style="font-size:11px;">Save</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 media-cancel-name-btn" data-id="{{ $item->id }}" style="font-size:11px;">Cancel</button>
                                </div>
                            </div>
                            <p class="text-xs text-muted mb-0">{{ $item->mime_type }} &middot; {{ round($item->size / 1024, 1) }} KB</p>
                            @if($item->collection_name)
                                <span class="badge bg-gradient-info text-xxs">{{ $item->collection_name }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="material-symbols-rounded text-muted" style="font-size:64px;">folder_off</i>
                    <p class="text-muted mt-2">No media files found.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- List View --}}
    <div id="mediaList" class="d-none">
        <div class="card">
            <div class="card-body px-0 pb-2">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAllList">
                                    </div>
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Preview</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Filename</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Type</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Size</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Collection</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Uploaded</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center" style="width:80px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($media as $item)
                                <tr class="media-list-row" data-id="{{ $item->id }}" style="cursor:pointer;">
                                    <td class="align-middle">
                                        <div class="form-check">
                                            <input class="form-check-input media-select" type="checkbox" value="{{ $item->id }}" data-id="{{ $item->id }}">
                                        </div>
                                    </td>
                                    <td class="align-middle ps-2">
                                        @if(Str::startsWith($item->mime_type, 'image/'))
                                            <div class="d-flex align-items-center gap-1">
                                                <img src="{{ $item->model_type ? $item->getUrl('thumb') : $item->getUrl() }}" alt="{{ $item->name }}" class="rounded" style="width:40px;height:40px;object-fit:cover;">
                                                <button type="button" class="btn btn-link p-0 media-preview-btn" data-id="{{ $item->id }}" data-url="{{ $item->model_type ? $item->getUrl() : $item->getUrl() }}" data-name="{{ $item->name }}" title="Preview" onclick="event.stopPropagation();">
                                                    <i class="material-symbols-rounded text-info" style="font-size:16px;">visibility</i>
                                                </button>
                                            </div>
                                        @else
                                            <div class="d-flex align-items-center justify-content-center rounded" style="width:40px;height:40px;background:#f3f4f6;">
                                                <i class="material-symbols-rounded text-muted" style="font-size:24px;">insert_drive_file</i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="text-xs font-weight-bold media-name-display" data-id="{{ $item->id }}">{{ $item->name }}</span>
                                            <button type="button" class="btn btn-link p-0 flex-shrink-0 media-edit-name-btn" data-id="{{ $item->id }}" data-name="{{ $item->name }}" title="Edit name" onclick="event.stopPropagation();" style="line-height:1;">
                                                <i class="material-symbols-rounded text-muted" style="font-size:14px;">edit</i>
                                            </button>
                                        </div>
                                        <div class="d-none media-name-edit-form" data-id="{{ $item->id }}">
                                            <div class="input-group input-group-outline input-group-sm" style="max-width:220px;">
                                                <label class="form-label"></label>
                                                <input type="text" class="form-control form-control-sm media-name-input" data-id="{{ $item->id }}" value="{{ $item->name }}">
                                            </div>
                                            <div class="d-flex gap-1 mt-1">
                                                <button type="button" class="btn btn-sm bg-gradient-info py-0 px-2 media-save-name-btn" data-id="{{ $item->id }}" style="font-size:11px;">Save</button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 media-cancel-name-btn" data-id="{{ $item->id }}" style="font-size:11px;">Cancel</button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-xs font-weight-bold">{{ $item->file_name }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-xs">{{ $item->mime_type }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-xs">{{ round($item->size / 1024, 1) }} KB</span>
                                    </td>
                                    <td class="align-middle">
                                        @if($item->collection_name)
                                            <span class="badge bg-gradient-info text-xxs">{{ $item->collection_name }}</span>
                                        @else
                                            <span class="text-xs text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-xs text-muted">{{ $item->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger px-2 py-0 media-delete-btn" data-id="{{ $item->id }}" data-name="{{ $item->name }}" title="Delete" onclick="event.stopPropagation();">
                                            <i class="material-symbols-rounded" style="font-size:14px;">delete</i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">No media files found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    @if($media->hasPages())
        <div class="row mt-3">
            <div class="col-lg-12 d-flex justify-content-center">
                {{ $media->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

{{-- Upload Modal --}}
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bolder" id="uploadModalLabel">Upload Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-xs font-weight-bolder">Collection</label>
                        <select name="collection" class="form-select">
                            <option value="default">Default</option>
                            @foreach($collections as $col)
                                <option value="{{ $col }}">{{ ucfirst($col) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="dropZone" class="border border-2 border-dashed rounded-3 p-5 text-center" style="border-color:#dee2e6;cursor:pointer;transition:all .2s;">
                        <i class="material-symbols-rounded text-muted" style="font-size:48px;">cloud_upload</i>
                        <p class="text-sm text-muted mt-2 mb-1">Drag & drop files here or click to browse</p>
                        <p class="text-xs text-muted">Supports: JPG, JPEG, PNG, WebP images (max 10 MB each, multiple files allowed)</p>
                        <input type="file" name="file[]" id="fileInput" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="d-none" multiple>
                    </div>
                    <div id="uploadProgress" class="mt-3 d-none">
                        <div class="progress-wrapper">
                            <div class="progress-info">
                                <div class="progress-percentage">
                                    <span class="text-xs font-weight-bold" id="uploadPercent">0%</span>
                                </div>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-gradient-info w-0" id="uploadBar" role="progressbar"></div>
                            </div>
                        </div>
                    </div>
                    <div id="uploadFileList" class="mt-3"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm bg-gradient-dark" id="uploadSubmitBtn">
                    <i class="material-symbols-rounded me-1" style="font-size:16px;">cloud_upload</i> Upload
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bolder" id="detailModalLabel">Media Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-info" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="detailModalFooter"></div>
        </div>
    </div>
</div>

{{-- Preview Lightbox Modal --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true" style="z-index:1060;">
    <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width:90vw;">
        <div class="modal-content bg-transparent border-0" style="background:rgba(0,0,0,0.85) !important;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title text-white font-weight-bolder" id="previewModalLabel"></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex align-items-center justify-content-center p-3" style="min-height:60vh;">
                <img id="previewImage" src="" alt="" class="img-fluid" style="max-height:78vh;max-width:100%;object-fit:contain;border-radius:8px;">
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<style>
    /* ── Media selection checkbox: visible, clean, aligned ─────
       Material Dashboard strips the border (border:none) and the app's
       Tailwind layer paints the checked state using currentColor,
       so we restore a visible border and drive currentColor. */
    .media-select {
        appearance: none;
        -webkit-appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #d2d6da !important;
        border-radius: 6px !important;
        background-color: #fff;
        color: #17c1e8;
        cursor: pointer;
        position: relative;
        margin: 0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.18);
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .media-select:hover {
        border-color: #17c1e8 !important;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.25);
    }
    .media-select:checked::after {
        content: '';
        position: absolute;
        left: 5px;
        top: 1px;
        width: 5px;
        height: 10px;
        border: solid #fff;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }
    .media-select:focus-visible {
        outline: 2px solid #17c1e8;
        outline-offset: 2px;
    }
    /* Align the grid-card checkbox flush with the card corner */
    .media-card .form-check.position-absolute {
        padding-left: 0;
        min-height: 0;
        margin: 0;
        line-height: 0;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    // ─── Grid / List View Toggle ─────────────────────────────────
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    const mediaGrid = document.getElementById('mediaGrid');
    const mediaList = document.getElementById('mediaList');

    gridBtn.addEventListener('click', function() {
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
        mediaGrid.classList.remove('d-none');
        mediaList.classList.add('d-none');
    });

    listBtn.addEventListener('click', function() {
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
        mediaList.classList.remove('d-none');
        mediaGrid.classList.add('d-none');
    });

    // ─── Multi-Select & Bulk Delete ──────────────────────────────
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCount = document.getElementById('selectedCount');
    const selectAllList = document.getElementById('selectAllList');

    function updateSelection() {
        const checked = document.querySelectorAll('.media-select:checked');
        const count = checked.length;
        selectedCount.textContent = count;
        if (count > 0) {
            bulkDeleteBtn.classList.remove('d-none');
        } else {
            bulkDeleteBtn.classList.add('d-none');
        }
    }

    document.querySelectorAll('.media-select').forEach(function(cb) {
        cb.addEventListener('change', updateSelection);
    });

    if (selectAllList) {
        selectAllList.addEventListener('change', function() {
            document.querySelectorAll('.media-select').forEach(function(cb) {
                cb.checked = selectAllList.checked;
            });
            updateSelection();
        });
    }

    bulkDeleteBtn.addEventListener('click', function() {
        const checked = document.querySelectorAll('.media-select:checked');
        if (checked.length === 0) return;
        if (!confirm('Are you sure you want to delete ' + checked.length + ' file(s)? This action cannot be undone.')) return;

        const ids = Array.from(checked).map(function(cb) { return cb.value; });
        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('_method', 'POST');
        ids.forEach(function(id) { formData.append('ids[]', id); });

        fetch('{{ route("media.bulk-delete") }}', {
            method: 'POST',
            body: formData
        })
        .then(parseJsonSafe)
        .then(function(data) {
            if (data.success) {
                ids.forEach(function(id) {
                    const el = document.querySelector('.media-item[data-id="' + id + '"]');
                    if (el) el.remove();
                    const row = document.querySelector('.media-list-row[data-id="' + id + '"]');
                    if (row) row.remove();
                });
                // Reset selection state
                if (selectAllList) selectAllList.checked = false;
                updateSelection();
                showToast(data.message || ('Deleted ' + ids.length + ' file(s).'), 'success');
            } else {
                showToast(data.message || 'Delete failed.', 'danger');
            }
        })
        .catch(function(err) {
            showToast((err && err.message) ? err.message : 'An error occurred.', 'danger');
        });
    });

    // ─── Upload Modal ────────────────────────────────────────────
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const uploadSubmitBtn = document.getElementById('uploadSubmitBtn');
    const uploadForm = document.getElementById('uploadForm');
    const uploadProgress = document.getElementById('uploadProgress');
    const uploadBar = document.getElementById('uploadBar');
    const uploadPercent = document.getElementById('uploadPercent');
    const uploadFileList = document.getElementById('uploadFileList');
    let selectedFiles = [];

    dropZone.addEventListener('click', function() { fileInput.click(); });

    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.style.borderColor = '#667eea';
        dropZone.style.background = '#f0f0ff';
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dropZone.style.borderColor = '#dee2e6';
        dropZone.style.background = 'transparent';
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.style.borderColor = '#dee2e6';
        dropZone.style.background = 'transparent';
        selectedFiles = Array.from(e.dataTransfer.files);
        showFileList(selectedFiles);
    });

    fileInput.addEventListener('change', function() {
        selectedFiles = Array.from(fileInput.files);
        showFileList(selectedFiles);
    });

    function showFileList(files) {
        uploadFileList.innerHTML = '';
        for (var i = 0; i < files.length; i++) {
            var div = document.createElement('div');
            div.className = 'd-flex align-items-center gap-2 mb-2';
            div.innerHTML = '<i class="material-symbols-rounded text-info" style="font-size:18px;">insert_drive_file</i>' +
                '<span class="text-xs text-dark">' + escHtml(files[i].name) + '</span>' +
                '<span class="text-xs text-muted ms-auto">' + formatSize(files[i].size) + '</span>';
            uploadFileList.appendChild(div);
        }
    }

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    uploadSubmitBtn.addEventListener('click', function() {
        if (!selectedFiles.length) {
            showToast('Please select a file to upload.', 'warning');
            return;
        }

        // Client-side MIME type validation (JPG, JPEG, PNG, WebP only)
        var allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        for (var i = 0; i < selectedFiles.length; i++) {
            if (allowedTypes.indexOf(selectedFiles[i].type) === -1) {
                showToast('File "' + selectedFiles[i].name + '" is not a supported image type. Please use JPG, JPEG, PNG, or WebP.', 'danger');
                return;
            }
        }

        uploadProgress.classList.remove('d-none');
        uploadSubmitBtn.disabled = true;
        uploadSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Uploading...';

        var collection = uploadForm.querySelector('[name="collection"]').value;
        var totalFiles = selectedFiles.length;
        var uploadedCount = 0;
        var failedCount = 0;

        function uploadNext() {
            if (uploadedCount + failedCount >= totalFiles) {
                // All done
                uploadSubmitBtn.disabled = false;
                uploadSubmitBtn.innerHTML = '<i class="material-symbols-rounded me-1" style="font-size:16px;">cloud_upload</i> Upload';

                if (failedCount === 0) {
                    showToast(totalFiles + ' file(s) uploaded successfully!', 'success');
                    var modal = bootstrap.Modal.getInstance(document.getElementById('uploadModal'));
                    modal.hide();
                    // Reset state
                    selectedFiles = [];
                    uploadFileList.innerHTML = '';
                    uploadBar.style.width = '0%';
                    uploadPercent.textContent = '0%';
                    uploadProgress.classList.add('d-none');
                    // Refresh the page to show new uploads
                    location.reload();
                } else {
                    showToast(uploadedCount + ' uploaded, ' + failedCount + ' failed.', failedCount === totalFiles ? 'danger' : 'warning');
                    uploadBar.style.width = '0%';
                    uploadPercent.textContent = '0%';
                    uploadProgress.classList.add('d-none');
                }
                return;
            }

            var file = selectedFiles[uploadedCount + failedCount];
            var formData = new FormData();
            formData.append('file', file);
            formData.append('collection', collection);
            formData.append('_token', csrfToken);

            var pct = Math.round(((uploadedCount + failedCount) / totalFiles) * 100);
            uploadBar.style.width = pct + '%';
            uploadPercent.textContent = pct + '% (' + (uploadedCount + failedCount + 1) + '/' + totalFiles + ')';

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route("media.store") }}', true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');
            // Do NOT set Content-Type — browser must set multipart boundary automatically

            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable) {
                    var filePct = Math.round((e.loaded / e.total) * 100);
                    var overallPct = Math.round(((uploadedCount + (e.loaded / e.total)) / totalFiles) * 100);
                    uploadBar.style.width = overallPct + '%';
                    uploadPercent.textContent = overallPct + '% — ' + file.name;
                }
            });

            xhr.onload = function() {
                try {
                    var resp = JSON.parse(xhr.responseText);
                    if (xhr.status >= 200 && xhr.status < 300 && resp.success) {
                        uploadedCount++;
                    } else {
                        failedCount++;
                        var msg = resp.message || resp.errors?.file?.[0] || 'Upload failed.';
                        showToast(file.name + ': ' + msg, 'danger');
                    }
                } catch (e) {
                    failedCount++;
                    showToast(file.name + ': Unexpected server response.', 'danger');
                }
                uploadNext();
            };

            xhr.onerror = function() {
                failedCount++;
                showToast(file.name + ': Network error.', 'danger');
                uploadNext();
            };

            xhr.send(formData);
        }

        uploadNext();
    });

    // ─── Detail Modal (AJAX) ────────────────────────────────────
    function openDetail(id) {
        var modal = new bootstrap.Modal(document.getElementById('detailModal'));
        document.getElementById('detailModalBody').innerHTML = '<div class="text-center py-4"><div class="spinner-border text-info" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        document.getElementById('detailModalFooter').innerHTML = '';
        modal.show();

        fetch('/admin/media/' + id, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                var item = data;
                var preview = '';
                if (item.mime_type && item.mime_type.startsWith('image/')) {
                    preview = '<img src="' + item.url + '" alt="' + escHtml(item.name) + '" class="img-fluid rounded" style="max-height:350px;object-fit:contain;width:100%;">';
                } else {
                    var icon = 'insert_drive_file';
                    if (item.mime_type === 'application/pdf') icon = 'picture_as_pdf';
                    else if (item.mime_type && item.mime_type.startsWith('video/')) icon = 'videocam';
                    else if (item.mime_type && item.mime_type.startsWith('audio/')) icon = 'audiotrack';
                    preview = '<div class="d-flex align-items-center justify-content-center rounded" style="height:200px;background:#f3f4f6;"><i class="material-symbols-rounded text-muted" style="font-size:64px;">' + icon + '</i></div>';
                }

                var dims = '';
                if (item.custom_properties && item.custom_properties.width && item.custom_properties.height) {
                    dims = item.custom_properties.width + ' × ' + item.custom_properties.height;
                }

                var html = preview;
                html += '<div class="table-responsive mt-3"><table class="table align-items-center mb-0">';
                html += '<tbody>';
                html += metaRow('Filename', item.file_name);
                html += metaRow('Name', item.name);
                html += metaRow('MIME Type', item.mime_type);
                html += metaRow('Size', formatSize(item.size));
                if (dims) html += metaRow('Dimensions', dims);
                html += metaRow('Collection', item.collection_name || '—');
                html += metaRow('Disk', item.disk || '—');
                if (item.model_type) html += metaRow('Model', item.model_type + ' #' + item.model_id);
                html += metaRow('Created', item.created_at);
                html += metaRow('Updated', item.updated_at);
                html += '</tbody></table></div>';

                html += '<hr class="dark horizontal my-3">';
                html += '<h6 class="text-xs font-weight-bolder text-uppercase mb-2">Edit Metadata</h6>';
                html += '<form id="updateMetaForm">';
                html += '<div class="mb-2"><label class="form-label text-xs">Alt Text</label><input type="text" class="form-control form-control-sm" name="alt_text" value="' + escAttr(item.custom_properties?.alt_text || '') + '"></div>';
                html += '<div class="mb-2"><label class="form-label text-xs">Title</label><input type="text" class="form-control form-control-sm" name="title" value="' + escAttr(item.custom_properties?.title || '') + '"></div>';
                html += '<div class="mb-2"><label class="form-label text-xs">Caption</label><textarea class="form-control form-control-sm" name="caption" rows="2">' + escHtml(item.custom_properties?.caption || '') + '</textarea></div>';
                html += '</form>';

                document.getElementById('detailModalBody').innerHTML = html;

                var footer = '';
                footer += '<button type="button" class="btn btn-sm bg-gradient-dark" onclick="downloadMedia(' + item.id + ')"><i class="material-symbols-rounded me-1" style="font-size:16px;">download</i> Download</button>';
                footer += '<button type="button" class="btn btn-sm btn-outline-info" onclick="copyMediaUrl(\'' + escAttr(item.url) + '\')"><i class="material-symbols-rounded me-1" style="font-size:16px;">link</i> Copy URL</button>';
                footer += '<button type="button" class="btn btn-sm bg-gradient-info" onclick="saveMeta(' + item.id + ')"><i class="material-symbols-rounded me-1" style="font-size:16px;">save</i> Save</button>';
                footer += '<button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteMedia(' + item.id + ')"><i class="material-symbols-rounded me-1" style="font-size:16px;">delete</i> Delete</button>';
                footer += '<button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>';
                document.getElementById('detailModalFooter').innerHTML = footer;
            })
            .catch(function() {
                document.getElementById('detailModalBody').innerHTML = '<div class="alert alert-danger text-sm">Failed to load media details.</div>';
            });
    }

    function metaRow(label, value) {
        return '<tr><td class="text-xs font-weight-bolder" style="width:130px;">' + label + '</td><td class="text-xs">' + (value || '—') + '</td></tr>';
    }

    function escHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function escAttr(str) {
        return String(str).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    // Parse a fetch response as JSON, surfacing real errors for non-JSON (e.g. HTML error pages)
    function parseJsonSafe(res) {
        return res.text().then(function(text) {
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('Server error (HTTP ' + res.status + ').');
            }
        });
    }

    // ─── Click card / row to open detail ─────────────────────────
    document.querySelectorAll('.media-card').forEach(function(card) {
        card.style.cursor = 'pointer';
        card.addEventListener('click', function(e) {
            if (e.target.closest('.form-check')) return;
            if (e.target.closest('.media-preview-btn')) return;
            if (e.target.closest('.media-edit-name-btn')) return;
            if (e.target.closest('.media-name-edit-form')) return;
            if (e.target.closest('.media-delete-btn')) return;
            var item = card.closest('.media-item');
            if (item) openDetail(item.dataset.id);
        });
    });

    document.querySelectorAll('.media-list-row').forEach(function(row) {
        row.addEventListener('click', function(e) {
            if (e.target.closest('.form-check')) return;
            if (e.target.closest('.media-preview-btn')) return;
            if (e.target.closest('.media-edit-name-btn')) return;
            if (e.target.closest('.media-name-edit-form')) return;
            if (e.target.closest('.media-delete-btn')) return;
            openDetail(row.dataset.id);
        });
    });

    // ─── Delete from Grid / List ───────────────────────────────
    document.querySelectorAll('.media-delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var id = btn.dataset.id;
            var name = btn.dataset.name || 'this file';
            if (!confirm('Delete "' + name + '"? This action cannot be undone.')) return;

            btn.disabled = true;
            var originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';

            var formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('_method', 'DELETE');

            fetch('/admin/media/' + id, {
                method: 'POST',
                body: formData
            })
            .then(parseJsonSafe)
            .then(function(data) {
                if (data.success) {
                    var el = document.querySelector('.media-item[data-id="' + id + '"]');
                    if (el) el.remove();
                    var row = document.querySelector('.media-list-row[data-id="' + id + '"]');
                    if (row) row.remove();
                    showToast(data.message || 'File deleted.', 'success');
                } else {
                    showToast(data.message || 'Delete failed.', 'danger');
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            })
            .catch(function(err) {
                showToast((err && err.message) ? err.message : 'An error occurred.', 'danger');
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        });
    });

    // ─── Image Preview Lightbox ──────────────────────────────────
    function openPreview(url, name) {
        document.getElementById('previewModalLabel').textContent = name || 'Image Preview';
        document.getElementById('previewImage').src = url;
        document.getElementById('previewImage').alt = name || 'Preview';
        var previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
        previewModal.show();
    }

    document.querySelectorAll('.media-preview-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openPreview(btn.dataset.url, btn.dataset.name);
        });
    });

    // Clicking an image thumbnail previews that image
    document.querySelectorAll('.media-thumb img, #mediaList .media-list-row img').forEach(function(img) {
        img.style.cursor = 'zoom-in';
        img.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var container = img.closest('.media-item') || img.closest('.media-list-row');
            var nameEl = container ? container.querySelector('.media-name-display') : null;
            openPreview(img.src, nameEl ? nameEl.textContent : '');
        });
    });

    // ─── Inline Name Editing ─────────────────────────────────────
    document.querySelectorAll('.media-edit-name-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var id = btn.dataset.id;
            startEditName(id);
        });
    });

    document.querySelectorAll('.media-save-name-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var id = btn.dataset.id;
            saveName(id);
        });
    });

    document.querySelectorAll('.media-cancel-name-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var id = btn.dataset.id;
            cancelEditName(id);
        });
    });

    // Allow Enter to save, Escape to cancel in name inputs
    document.querySelectorAll('.media-name-input').forEach(function(input) {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveName(input.dataset.id);
            } else if (e.key === 'Escape') {
                e.preventDefault();
                cancelEditName(input.dataset.id);
            }
        });
    });

    function startEditName(id) {
        // Hide display, show edit form for all matching elements (grid + list)
        document.querySelectorAll('.media-name-display[data-id="' + id + '"]').forEach(function(el) {
            el.classList.add('d-none');
        });
        document.querySelectorAll('.media-edit-name-btn[data-id="' + id + '"]').forEach(function(btn) {
            btn.classList.add('d-none');
        });
        document.querySelectorAll('.media-name-edit-form[data-id="' + id + '"]').forEach(function(el) {
            el.classList.remove('d-none');
        });
        document.querySelectorAll('.media-name-input[data-id="' + id + '"]').forEach(function(input) {
            input.focus();
            input.select();
        });
    }

    function cancelEditName(id) {
        // Restore original name value from the button's data attribute
        var originalName = '';
        var editBtn = document.querySelector('.media-edit-name-btn[data-id="' + id + '"]');
        if (editBtn) originalName = editBtn.dataset.name;

        document.querySelectorAll('.media-name-input[data-id="' + id + '"]').forEach(function(input) {
            input.value = originalName;
        });
        document.querySelectorAll('.media-name-display[data-id="' + id + '"]').forEach(function(el) {
            el.classList.remove('d-none');
        });
        document.querySelectorAll('.media-edit-name-btn[data-id="' + id + '"]').forEach(function(btn) {
            btn.classList.remove('d-none');
        });
        document.querySelectorAll('.media-name-edit-form[data-id="' + id + '"]').forEach(function(el) {
            el.classList.add('d-none');
        });
    }

    function saveName(id) {
        var input = document.querySelector('.media-name-input[data-id="' + id + '"]');
        if (!input) return;
        var newName = input.value.trim();
        if (!newName) {
            showToast('Name cannot be empty.', 'warning');
            return;
        }

        var saveBtn = document.querySelector('.media-save-name-btn[data-id="' + id + '"]');
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';
        }

        var formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('_method', 'PATCH');
        formData.append('name', newName);

        fetch('/admin/media/' + id, {
            method: 'POST',
            body: formData
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                // Update all display elements with the new name
                document.querySelectorAll('.media-name-display[data-id="' + id + '"]').forEach(function(el) {
                    el.textContent = newName;
                    el.title = newName;
                    el.classList.remove('d-none');
                });
                // Update the edit button's data-name for future edits
                document.querySelectorAll('.media-edit-name-btn[data-id="' + id + '"]').forEach(function(btn) {
                    btn.dataset.name = newName;
                    btn.classList.remove('d-none');
                });
                // Hide edit forms
                document.querySelectorAll('.media-name-edit-form[data-id="' + id + '"]').forEach(function(el) {
                    el.classList.add('d-none');
                });
                showToast('Name updated successfully.', 'success');
            } else {
                showToast(data.message || 'Update failed.', 'danger');
                cancelEditName(id);
            }
        })
        .catch(function() {
            showToast('An error occurred while saving.', 'danger');
            cancelEditName(id);
        })
        .finally(function() {
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = 'Save';
            }
        });
    }

    // ─── Global helpers ──────────────────────────────────────────
    window.downloadMedia = function(id) {
        window.location.href = '/admin/media/' + id + '/download';
    };

    window.copyMediaUrl = function(url) {
        navigator.clipboard.writeText(url).then(function() {
            showToast('URL copied to clipboard.', 'success');
        });
    };

    window.saveMeta = function(id) {
        var form = document.getElementById('updateMetaForm');
        var data = new FormData(form);
        data.append('_token', csrfToken);
        data.append('_method', 'PUT');

        fetch('/admin/media/' + id, {
            method: 'POST',
            body: data
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                showToast('Metadata updated.', 'success');
            } else {
                showToast(data.message || 'Update failed.', 'danger');
            }
        })
        .catch(function() {
            showToast('An error occurred.', 'danger');
        });
    };

    window.deleteMedia = function(id) {
        if (!confirm('Are you sure you want to delete this file? This action cannot be undone.')) return;
        var formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('_method', 'DELETE');

        fetch('/admin/media/' + id, {
            method: 'POST',
            body: formData
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                var el = document.querySelector('.media-item[data-id="' + id + '"]');
                if (el) el.remove();
                var row = document.querySelector('.media-list-row[data-id="' + id + '"]');
                if (row) row.remove();
                var modal = bootstrap.Modal.getInstance(document.getElementById('detailModal'));
                modal.hide();
                showToast('File deleted.', 'success');
            } else {
                showToast(data.message || 'Delete failed.', 'danger');
            }
        })
        .catch(function() {
            showToast('An error occurred.', 'danger');
        });
    };

    // ─── Toast helper ────────────────────────────────────────────
    function showToast(message, type) {
        var toast = document.createElement('div');
        toast.className = 'alert alert-' + type + ' alert-dismissible text-white position-fixed';
        toast.setAttribute('role', 'alert');
        toast.style.cssText = 'top:20px;right:20px;z-index:9999;min-width:280px;';
        toast.innerHTML = '<span class="text-sm">' + message + '</span>' +
            '<button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        document.body.appendChild(toast);
        setTimeout(function() {
            toast.remove();
        }, 4000);
    }

    // ─── Auto-submit filter changes ──────────────────────────────
    document.getElementById('collectionFilter').addEventListener('change', function() {
        document.getElementById('mediaFilterForm').submit();
    });
    document.getElementById('mimeFilter').addEventListener('change', function() {
        document.getElementById('mediaFilterForm').submit();
    });
    document.getElementById('sortFilter').addEventListener('change', function() {
        document.getElementById('mediaFilterForm').submit();
    });
});
</script>
@endpush
