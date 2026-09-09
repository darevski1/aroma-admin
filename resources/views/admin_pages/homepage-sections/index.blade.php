@extends('layouts.master')

@section('title', 'Homepage Sections')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">Homepage Sections</h3>
            <p class="mb-4">
                Manage which sections appear on the homepage and in what order. Drag to reorder, toggle to publish/unpublish.
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

    {{-- Sections Table --}}
    <div class="row mb-4 mt-3">
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">All Homepage Sections</h6>
                    <div class="d-flex align-items-center gap-3">
                        <span class="text-xs text-muted">
                            Drag rows to reorder. Changes save automatically.
                        </span>
                        <a href="{{ route('homepage-sections.create') }}" class="btn btn-sm bg-gradient-dark mb-0">
                            <i class="material-symbols-rounded me-1" style="font-size:16px;">add</i>
                            Add New Section
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0" id="sections-table">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width:40px;"></th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Order</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Section Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Slug</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="sections-body">
                                @forelse ($sections as $section)
                                    <tr data-id="{{ $section->id }}" style="cursor:grab;">
                                        <td class="align-middle">
                                            <i class="material-symbols-rounded text-muted" style="font-size:18px;cursor:grab;">drag_indicator</i>
                                        </td>
                                        <td class="align-middle">
                                            <span class="text-xs font-weight-bold order-badge">{{ $section->sort_order }}</span>
                                        </td>
                                        <td class="align-middle ps-2">
                                            <span class="text-xs font-weight-bold">{{ $section->name }}</span>
                                            @if ($section->hasCustomContent())
                                                <span class="badge bg-gradient-info text-xxs ms-1">Custom</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <code class="text-xs">{{ $section->slug }}</code>
                                        </td>
                                        <td class="align-middle">
                                            @if ($section->is_published)
                                                <span class="badge bg-gradient-success">Published</span>
                                            @else
                                                <span class="badge bg-gradient-secondary">Unpublished</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            {{-- Toggle Publish --}}
                                            <button
                                                type="button"
                                                class="btn btn-sm {{ $section->is_published ? 'btn-outline-success' : 'btn-outline-secondary' }} toggle-publish-btn"
                                                data-id="{{ $section->id }}"
                                                title="{{ $section->is_published ? 'Unpublish' : 'Publish' }}"
                                            >
                                                <i class="material-symbols-rounded" style="font-size:16px;">
                                                    {{ $section->is_published ? 'visibility' : 'visibility_off' }}
                                                </i>
                                                {{ $section->is_published ? 'Unpublish' : 'Publish' }}
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No homepage sections found.
                                        </td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/sortablejs/1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tbody = document.getElementById('sections-body');
    const csrfToken = '{{ csrf_token() }}';

    // ── Toggle Publish ──────────────────────────────────────
    tbody.addEventListener('click', function(e) {
        const btn = e.target.closest('.toggle-publish-btn');
        if (!btn) return;

        const id = btn.dataset.id;
        const row = btn.closest('tr');

        btn.disabled = true;

        fetch(`/homepage-sections/${id}/toggle-publish`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) throw new Error('Failed');

            const badge = row.querySelector('.badge');
            const icon = btn.querySelector('i');

            if (data.is_published) {
                badge.className = 'badge bg-gradient-success';
                badge.textContent = 'Published';
                btn.className = 'btn btn-sm btn-outline-success toggle-publish-btn';
                icon.textContent = 'visibility';
                btn.childNodes[btn.childNodes.length - 1].textContent = ' Unpublish';
            } else {
                badge.className = 'badge bg-gradient-secondary';
                badge.textContent = 'Unpublished';
                btn.className = 'btn btn-sm btn-outline-secondary toggle-publish-btn';
                icon.textContent = 'visibility_off';
                btn.childNodes[btn.childNodes.length - 1].textContent = ' Publish';
            }

            showToast(data.message, 'success');
        })
        .catch(() => {
            showToast('Failed to update section.', 'error');
        })
        .finally(() => {
            btn.disabled = false;
        });
    });

    // ── Drag & Drop Reorder ─────────────────────────────────
    if (typeof Sortable !== 'undefined' && tbody) {
        Sortable.create(tbody, {
            handle: 'i.material-symbols-rounded',
            animation: 150,
            ghostClass: 'table-active',
            onEnd: function() {
                const rows = tbody.querySelectorAll('tr[data-id]');
                const order = Array.from(rows).map((row, index) => {
                    const id = row.dataset.id;
                    // Update the visual order badge
                    row.querySelector('.order-badge').textContent = index + 1;
                    return id;
                });

                fetch('/homepage-sections/reorder', {
                    method: 'POST',
                    headers: {
                        'Content-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ order: order }),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast('Section order updated.', 'success');
                    }
                })
                .catch(() => {
                    showToast('Failed to reorder sections.', 'error');
                });
            }
        });
    }

    // ── Toast Helper ────────────────────────────────────────
    function showToast(message, type) {
        const existing = document.getElementById('section-toast');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.id = 'section-toast';
        toast.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;padding:12px 24px;border-radius:8px;color:#fff;font-size:14px;font-weight:500;box-shadow:0 4px 12px rgba(0,0,0,0.15);transition:opacity 0.3s;';
        toast.style.background = type === 'success' ? '#28a745' : '#dc3545';
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 2500);
    }
});
</script>
@endpush
