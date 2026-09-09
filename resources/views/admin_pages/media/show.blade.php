@extends('layouts.master')

@section('title', 'Media Detail')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('media.index') }}" class="text-dark">Media Library</a></li>
                    <li class="breadcrumb-item active text-dark" aria-current="page">Detail</li>
                </ol>
            </nav>
            <h3 class="mb-0 h4 font-weight-bolder">Media Detail</h3>
            <p class="mb-4">View and manage media file details.</p>
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

    <div class="row">
        {{-- Left: Preview + Metadata --}}
        <div class="col-lg-8">
            {{-- Preview --}}
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6 class="mb-0">Preview</h6>
                </div>
                <div class="card-body text-center">
                    @if(Str::startsWith($media->mime_type, 'image/'))
                        <img src="{{ $media->getUrl() }}" alt="{{ $media->name }}" class="img-fluid rounded" style="max-height:400px;object-fit:contain;width:100%;">
                    @else
                        <div class="d-flex align-items-center justify-content-center rounded" style="height:250px;background:#f3f4f6;">
                            <i class="material-symbols-rounded text-muted" style="font-size:80px;">{{ $media->mime_type === 'application/pdf' ? 'picture_as_pdf' : (Str::startsWith($media->mime_type, 'video/') ? 'videocam' : (Str::startsWith($media->mime_type, 'audio/') ? 'audiotrack' : 'insert_drive_file')) }}</i>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Metadata --}}
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6 class="mb-0">Metadata</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-xs font-weight-bolder" style="width:180px;">Filename</td>
                                    <td class="text-xs">{{ $media->file_name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-xs font-weight-bolder">Name</td>
                                    <td class="text-xs">{{ $media->name }}</td>
                                </tr>
                                <tr>
                                    <td class="text-xs font-weight-bolder">MIME Type</td>
                                    <td class="text-xs">{{ $media->mime_type }}</td>
                                </tr>
                                <tr>
                                    <td class="text-xs font-weight-bolder">Size</td>
                                    <td class="text-xs">
                                        @if($media->size > 1048576)
                                            {{ round($media->size / 1048576, 2) }} MB
                                        @elseif($media->size > 1024)
                                            {{ round($media->size / 1024, 1) }} KB
                                        @else
                                            {{ $media->size }} B
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-xs font-weight-bolder">Dimensions</td>
                                    <td class="text-xs">
                                        @if($media->custom_properties && isset($media->custom_properties['width']) && isset($media->custom_properties['height']))
                                            {{ $media->custom_properties['width'] }} &times; {{ $media->custom_properties['height'] }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-xs font-weight-bolder">Collection</td>
                                    <td class="text-xs">
                                        @if($media->collection_name)
                                            <span class="badge bg-gradient-info text-xxs">{{ $media->collection_name }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-xs font-weight-bolder">Disk</td>
                                    <td class="text-xs">{{ $media->disk ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-xs font-weight-bolder">Associated Model</td>
                                    <td class="text-xs">
                                        @if($media->model_type)
                                            {{ $media->model_type }} #{{ $media->model_id }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-xs font-weight-bolder">Created At</td>
                                    <td class="text-xs">{{ $media->created_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-xs font-weight-bolder">Updated At</td>
                                    <td class="text-xs">{{ $media->updated_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- URLs --}}
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6 class="mb-0">URLs</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-xs font-weight-bolder" style="width:180px;">Original</td>
                                    <td class="text-xs">
                                        <code class="text-xs">{{ $media->getUrl() }}</code>
                                    </td>
                                </tr>
                                @foreach(['thumb', 'small', 'medium', 'large', 'social', 'avatar'] as $conv)
                                    @if($media->model_type && method_exists($media, 'getUrl'))
                                        <tr>
                                            <td class="text-xs font-weight-bolder">{{ ucfirst($conv) }}</td>
                                            <td class="text-xs">
                                                <code class="text-xs">{{ $media->getUrl($conv) }}</code>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Edit + Actions --}}
        <div class="col-lg-4">
            {{-- Edit Form --}}
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6 class="mb-0">Edit Metadata</h6>
                </div>
                <div class="card-body">
                    <form id="editMetaForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label text-xs font-weight-bolder">Alt Text</label>
                            <input type="text" class="form-control form-control-sm" name="alt_text" value="{{ $media->custom_properties['alt_text'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-xs font-weight-bolder">Title</label>
                            <input type="text" class="form-control form-control-sm" name="title" value="{{ $media->custom_properties['title'] ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-xs font-weight-bolder">Caption</label>
                            <textarea class="form-control form-control-sm" name="caption" rows="3">{{ $media->custom_properties['caption'] ?? '' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-xs font-weight-bolder">Copyright</label>
                            <input type="text" class="form-control form-control-sm" name="copyright" value="{{ $media->custom_properties['copyright'] ?? '' }}">
                        </div>
                        <button type="button" class="btn btn-sm bg-gradient-info w-100" id="saveMetaBtn">
                            <i class="material-symbols-rounded me-1" style="font-size:16px;">save</i> Save Changes
                        </button>
                    </form>
                </div>
            </div>

            {{-- Actions --}}
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6 class="mb-0">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        {{-- Replace --}}
                        <button type="button" class="btn btn-sm btn-outline-info" id="replaceBtn">
                            <i class="material-symbols-rounded me-1" style="font-size:16px;">swap_horiz</i> Replace File
                        </button>
                        <input type="file" id="replaceInput" class="d-none">

                        {{-- Download --}}
                        <a href="{{ route('media.download', $media->id) }}" class="btn btn-sm bg-gradient-dark text-center">
                            <i class="material-symbols-rounded me-1" style="font-size:16px;">download</i> Download
                        </a>

                        {{-- Copy URL --}}
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="copyUrlBtn" data-url="{{ $media->getUrl() }}">
                            <i class="material-symbols-rounded me-1" style="font-size:16px;">link</i> Copy URL
                        </button>

                        {{-- Delete --}}
                        <button type="button" class="btn btn-sm btn-outline-danger" id="deleteBtn">
                            <i class="material-symbols-rounded me-1" style="font-size:16px;">delete</i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '{{ csrf_token() }}';
    const mediaId = {{ $media->id }};

    // ─── Save Metadata ───────────────────────────────────────────
    document.getElementById('saveMetaBtn').addEventListener('click', function() {
        var form = document.getElementById('editMetaForm');
        var data = new FormData(form);
        data.append('_token', csrfToken);

        fetch('/admin/media/' + mediaId, {
            method: 'POST',
            body: data
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                showToast('Metadata updated successfully.', 'success');
            } else {
                showToast(data.message || 'Update failed.', 'danger');
            }
        })
        .catch(function() {
            showToast('An error occurred while saving.', 'danger');
        });
    });

    // ─── Replace File ────────────────────────────────────────────
    var replaceBtn = document.getElementById('replaceBtn');
    var replaceInput = document.getElementById('replaceInput');

    replaceBtn.addEventListener('click', function() { replaceInput.click(); });

    replaceInput.addEventListener('change', function() {
        if (!replaceInput.files.length) return;
        if (!confirm('Replace the current file? The old file will be removed.')) return;

        var data = new FormData();
        data.append('_token', csrfToken);
        data.append('_method', 'POST');
        data.append('file', replaceInput.files[0]);

        replaceBtn.disabled = true;
        replaceBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Replacing...';

        fetch('/admin/media/' + mediaId + '/replace', {
            method: 'POST',
            body: data
        })
        .then(function(res) { return res.json(); })
        .then(function(resp) {
            if (resp.success) {
                showToast('File replaced successfully.', 'success');
                setTimeout(function() { location.reload(); }, 1000);
            } else {
                showToast(resp.message || 'Replace failed.', 'danger');
            }
        })
        .catch(function() {
            showToast('An error occurred.', 'danger');
        })
        .finally(function() {
            replaceBtn.disabled = false;
            replaceBtn.innerHTML = '<i class="material-symbols-rounded me-1" style="font-size:16px;">swap_horiz</i> Replace File';
        });
    });

    // ─── Copy URL ────────────────────────────────────────────────
    document.getElementById('copyUrlBtn').addEventListener('click', function() {
        var url = this.dataset.url;
        navigator.clipboard.writeText(url).then(function() {
            showToast('URL copied to clipboard.', 'success');
        }).catch(function() {
            showToast('Failed to copy URL.', 'danger');
        });
    });

    // ─── Delete ──────────────────────────────────────────────────
    document.getElementById('deleteBtn').addEventListener('click', function() {
        if (!confirm('Are you sure you want to delete this file? This action cannot be undone.')) return;

        var formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('_method', 'DELETE');

        fetch('/admin/media/' + mediaId, {
            method: 'POST',
            body: formData
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                showToast('File deleted.', 'success');
                setTimeout(function() {
                    window.location.href = '{{ route("media.index") }}';
                }, 1000);
            } else {
                showToast(data.message || 'Delete failed.', 'danger');
            }
        })
        .catch(function() {
            showToast('An error occurred.', 'danger');
        });
    });

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
});
</script>
@endpush
