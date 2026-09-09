@extends('layouts.master')

@section('title', 'Edit: ' . $page->title)

@section('content')
@php
    use App\Support\PageTemplateConfig;
    $resolvedSettings = [];
    foreach ($sections as $section) {
        if (isset($section['type']) && $section['type'] === 'repeatable') {
            $repeat = $section['repeat'] ?? 1;
            for ($i = 0; $i < $repeat; $i++) {
                foreach ($section['fields'] as $field) {
                    $key = str_replace('{n}', $i + 1, $field['key']);
                    $resolvedSettings[$key] = PageTemplateConfig::resolveValue(
                        $page->settings ?? [],
                        $field,
                        $section['defaults'] ?? null,
                        $i + 1
                    );
                }
            }
        } else {
            foreach ($section['fields'] as $field) {
                $resolvedSettings[$field['key']] = PageTemplateConfig::resolveValue(
                    $page->settings ?? [],
                    $field,
                    null,
                    0
                );
            }
        }
    }
@endphp

<style>
    /* ── Visual Editor Layout ────────────────────────────── */
    .ve-header {
        position: sticky; top: 0; z-index: 1040;
        background: #fff; border-bottom: 1px solid #e5e7eb;
        padding: 10px 24px; display: flex; align-items: center; gap: 12px;
    }
    .ve-header .ve-title-input {
        font-size: 1.1rem; font-weight: 600; border: none; outline: none;
        background: transparent; padding: 4px 8px; border-radius: 6px;
        flex: 1; min-width: 200px; color: #1f2937;
    }
    .ve-header .ve-title-input:focus { background: #f3f4f6; }
    .ve-status { font-size: 0.8rem; color: #6b7280; display: flex; align-items: center; gap: 4px; }
    .ve-status .dot { width: 8px; height: 8px; border-radius: 50%; background: #d1d5db; }
    .ve-status .dot.saved { background: #22c55e; }
    .ve-status .dot.saving { background: #f59e0b; animation: pulse 1s infinite; }
    .ve-status .dot.error { background: #ef4444; }
    @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.4; } }

    /* ── Section Cards ───────────────────────────────────── */
    .ve-section { margin-bottom: 24px; }
    .ve-section-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
        overflow: hidden; transition: box-shadow .2s;
    }
    .ve-section-card:hover { box-shadow: 0 2px 12px rgba(0,0,0,.06); }
    .ve-section-header {
        padding: 14px 20px; display: flex; align-items: center; gap: 10px;
        border-bottom: 1px solid #f3f4f6; cursor: pointer; user-select: none;
    }
    .ve-section-header h6 { margin: 0; font-size: 0.85rem; font-weight: 700; color: #374151; }
    .ve-section-header .chevron {
        margin-left: auto; transition: transform .2s; color: #9ca3af;
    }
    .ve-section-header.collapsed .chevron { transform: rotate(-90deg); }
    .ve-section-body { padding: 20px; }
    .ve-section-body.collapsed { display: none; }

    /* ── Field Groups ────────────────────────────────────── */
    .ve-field { margin-bottom: 18px; }
    .ve-field:last-child { margin-bottom: 0; }
    .ve-field-label {
        display: block; font-size: 0.75rem; font-weight: 700; color: #6b7280;
        text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;
    }
    .ve-field-input {
        width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px;
        font-size: 0.9rem; color: #1f2937; background: #fff; outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
    .ve-field-input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }

    /* ── Image Fields ────────────────────────────────────── */
    .ve-image-field { display: flex; align-items: center; gap: 16px; }
    .ve-image-thumb {
        width: 96px; height: 96px; border-radius: 10px; overflow: hidden;
        background: #f3f4f6; border: 2px dashed #d1d5db; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        transition: border-color .2s;
    }
    .ve-image-thumb.has-image { border-style: solid; }
    .ve-image-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .ve-image-thumb .placeholder-icon { color: #9ca3af; font-size: 32px; }
    .ve-image-actions { display: flex; gap: 8px; flex-wrap: wrap; }

    /* ── Repeatable Items ────────────────────────────────── */
    .ve-repeatable-item {
        padding: 16px; border: 1px solid #f3f4f6; border-radius: 10px;
        margin-bottom: 16px; background: #fafafa;
    }
    .ve-repeatable-item:last-child { margin-bottom: 0; }
    .ve-repeatable-label {
        font-size: 0.7rem; font-weight: 700; color: #9ca3af;
        text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;
    }

    /* ── Quill Overrides ─────────────────────────────────── */
    .ql-toolbar.ql-snow { border-radius: 8px 8px 0 0; border-color: #d1d5db; }
    .ql-container.ql-snow { border-radius: 0 0 8px 8px; border-color: #d1d5db; min-height: 120px; font-size: 0.9rem; }
    .ql-editor { min-height: 120px; }
    .ql-editor.ql-blank::before { color: #9ca3af; font-style: normal; }

    /* ── Media Library Modal ─────────────────────────────── */
    .ml-modal-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(110px, 1fr)); gap: 10px; }
    .ml-modal-item {
        border: 2px solid transparent; border-radius: 8px; overflow: hidden;
        cursor: pointer; transition: all .15s; position: relative; background: #f3f4f6;
    }
    .ml-modal-item:hover { border-color: #93c5fd; }
    .ml-modal-item.selected { border-color: #6366f1; box-shadow: 0 0 0 2px rgba(99,102,241,.3); }
    .ml-modal-item img {
        width: 100%; aspect-ratio: 1; object-fit: cover; display: block;
    }
    .ml-modal-item .name {
        font-size: 0.65rem; padding: 4px 6px; white-space: nowrap;
        overflow: hidden; text-overflow: ellipsis; color: #374151;
    }
    .ml-modal-item .check-badge {
        position: absolute; top: 6px; right: 6px; width: 22px; height: 22px;
        border-radius: 50%; background: #6366f1; color: #fff; display: none;
        align-items: center; justify-content: center; font-size: 12px;
    }
    .ml-modal-item.selected .check-badge { display: flex; }
    .ml-pagination .page-link { font-size: 0.8rem; padding: 4px 10px; }
</style>

<div class="ve-header">
    <a href="{{ route('pages.index') }}" class="btn btn-sm btn-outline-secondary" title="Back to Pages">
        <i class="material-symbols-rounded" style="font-size:18px;">arrow_back</i>
    </a>

    <input type="text" class="ve-title-input" id="vePageTitle"
           value="{{ old('title', $page->title) }}" placeholder="Page title…">

    <div class="ve-status" id="veStatus">
        <span class="dot saved" id="veStatusDot"></span>
        <span id="veStatusText">Saved</span>
    </div>

    <div class="d-flex gap-2 ms-auto">
        @if ($previewUrl)
            <a href="{{ $previewUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Preview">
                <i class="material-symbols-rounded" style="font-size:16px;">open_in_new</i>
                Preview
            </a>
        @endif
        <button type="button" class="btn btn-sm bg-gradient-dark" id="veSaveBtn" title="Save changes">
            <i class="material-symbols-rounded" style="font-size:16px;">save</i>
            Save
        </button>
    </div>
</div>

<div class="container-fluid py-3" style="max-width:960px; margin:0 auto;">

    {{-- Success / Error flash --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('success') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- Section cards --}}
    @foreach ($sections as $sIdx => $section)
        <div class="ve-section" data-section-index="{{ $sIdx }}">
            <div class="ve-section-card">
                <div class="ve-section-header" onclick="this.nextElementSibling.classList.toggle('collapsed');this.classList.toggle('collapsed');">
                    <i class="material-symbols-rounded" style="font-size:18px;color:#6366f1;">
                        {{ ($section['type'] ?? '') === 'repeatable' ? 'format_list_numbered' : 'edit' }}
                    </i>
                    <h6>{{ $section['name'] ?? 'Section ' . ($sIdx + 1) }}</h6>
                    <i class="material-symbols-rounded chevron" style="font-size:18px;">expand_more</i>
                </div>

                <div class="ve-section-body">
                    @if (($section['type'] ?? '') === 'repeatable')
                        {{-- ── Repeatable Group ────────────────────── --}}
                        @for ($i = 0; $i < ($section['repeat'] ?? 1); $i++)
                            <div class="ve-repeatable-item">
                                <div class="ve-repeatable-label">Item {{ $i + 1 }}</div>
                                @foreach ($section['fields'] as $field)
                                    @php
                                        $key = str_replace('{n}', $i + 1, $field['key']);
                                        $value = $resolvedSettings[$key] ?? ($field['default'] ?? '');
                                    @endphp
                                    @include('admin_pages.pages._ve-field', [
                                        'field'     => $field,
                                        'fieldKey'  => $key,
                                        'fieldValue'=> $value,
                                    ])
                                @endforeach
                            </div>
                        @endfor
                    @else
                        {{-- ── Static Fields ──────────────────────── --}}
                        @foreach ($section['fields'] as $field)
                            @php
                                $key   = $field['key'];
                                $value = $resolvedSettings[$key] ?? ($field['default'] ?? '');
                            @endphp
                            @include('admin_pages.pages._ve-field', [
                                'field'     => $field,
                                'fieldKey'  => $key,
                                'fieldValue'=> $value,
                            ])
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    {{-- Bottom Save --}}
    <div class="d-flex gap-2 my-4">
        <button type="button" class="btn bg-gradient-dark" id="veSaveBtnBottom">
            <i class="material-symbols-rounded me-1" style="font-size:16px;">save</i>
            Save Changes
        </button>
        <a href="{{ route('pages.index') }}" class="btn btn-outline-secondary">Back to Pages</a>
    </div>
</div>

{{-- ═══════════════ MEDIA LIBRARY MODAL ═══════════════ --}}
<div class="modal fade" id="mediaLibraryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title font-weight-bolder">
                    <i class="material-symbols-rounded me-1" style="font-size:18px;">perm_media</i>
                    Media Library
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Search & Filters --}}
                <div class="d-flex gap-2 mb-3">
                    <div class="input-group input-group-outline flex-grow-1">
                        <input type="text" class="form-control form-control-sm" id="mlSearch" placeholder="Search images…">
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="mlSearchBtn">
                        <i class="material-symbols-rounded" style="font-size:16px;">search</i>
                    </button>
                </div>

                {{-- Grid --}}
                <div class="ml-modal-grid" id="mlGrid"></div>

                {{-- Empty state --}}
                <div id="mlEmpty" class="text-center py-5" style="display:none;">
                    <i class="material-symbols-rounded text-muted mb-2" style="font-size:40px;">image_not_supported</i>
                    <p class="text-muted text-sm mb-0">No images found.</p>
                </div>

                {{-- Loading --}}
                <div id="mlLoading" class="text-center py-5" style="display:none;">
                    <div class="spinner-border text-primary" role="status" style="width:1.5rem;height:1.5rem;">
                        <span class="visually-hidden">Loading…</span>
                    </div>
                </div>

                {{-- Pagination --}}
                <nav id="mlPaginationWrap" class="mt-3" style="display:none;">
                    <ul class="pagination pagination-sm justify-content-center mb-0 ml-pagination" id="mlPagination"></ul>
                </nav>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm bg-gradient-dark" id="mlSelectBtn" disabled>
                    <i class="material-symbols-rounded me-1" style="font-size:14px;">check</i>
                    Select Image
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
{{-- ── Quill.js (CDN) ────────────────────────────────────── --}}
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

<script>
(function() {
    'use strict';

    const CSRF   = '{{ csrf_token() }}';
    const PAGE_ID = {{ $page->id }};
    const SAVE_URL = '{{ route("pages.visual-save", $page->id) }}';
    const PICKER_URL = '{{ route("media.picker") }}';

    // ── Collect all field data ──────────────────────────────
    // Store Quill instances by field key
    const quillEditors = {};

    // Initialise Quill on every .ve-quill-editor
    document.querySelectorAll('.ve-quill-editor').forEach(function(el) {
        const key = el.dataset.fieldKey;
        const q = new Quill(el, {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ header: [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link', 'image'],
                    ['clean']
                ]
            },
            placeholder: 'Start writing…'
        });
        quillEditors[key] = q;
    });

    /**
     * Gather every field value into { key: value }.
     */
    function collectSettings() {
        const settings = {};

        // Text inputs
        document.querySelectorAll('.ve-text-input').forEach(function(el) {
            settings[el.dataset.fieldKey] = el.value;
        });

        // Quill rich-text editors
        Object.keys(quillEditors).forEach(function(key) {
            const html = quillEditors[key].root.innerHTML;
            // Store empty editor as empty string
            settings[key] = (html === '<p><br></p>') ? '' : html;
        });

        // Image hidden inputs
        document.querySelectorAll('.ve-image-url').forEach(function(el) {
            settings[el.dataset.fieldKey] = el.value;
        });

        return settings;
    }

    // ── Save ────────────────────────────────────────────────
    let saving = false;

    function savePage() {
        if (saving) return;
        saving = true;

        const title    = document.getElementById('vePageTitle').value.trim();
        const settings = collectSettings();
        const dot      = document.getElementById('veStatusDot');
        const text     = document.getElementById('veStatusText');

        dot.className = 'dot saving';
        text.textContent = 'Saving…';

        fetch(SAVE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ title: title, settings: settings }),
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            saving = false;
            if (data.success) {
                dot.className = 'dot saved';
                text.textContent = 'Saved';
                showToast('Changes saved successfully.', 'success');
            } else {
                dot.className = 'dot error';
                text.textContent = 'Error';
                showToast(data.message || 'Save failed.', 'error');
            }
        })
        .catch(function(err) {
            saving = false;
            dot.className = 'dot error';
            text.textContent = 'Error';
            showToast('Network error. Please try again.', 'error');
            console.error(err);
        });
    }

    document.getElementById('veSaveBtn').addEventListener('click', savePage);
    document.getElementById('veSaveBtnBottom').addEventListener('click', savePage);

    // Ctrl+S / Cmd+S shortcut
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            savePage();
        }
    });

    // Mark dirty on any change
    document.getElementById('vePageTitle').addEventListener('input', markDirty);
    document.querySelectorAll('.ve-text-input').forEach(function(el) {
        el.addEventListener('input', markDirty);
    });
    Object.values(quillEditors).forEach(function(q) {
        q.on('text-change', markDirty);
    });

    function markDirty() {
        const dot = document.getElementById('veStatusDot');
        const text = document.getElementById('veStatusText');
        if (!dot.classList.contains('saving')) {
            dot.className = 'dot';
            text.textContent = 'Unsaved changes';
        }
    }

    // ── Toast helper ────────────────────────────────────────
    function showToast(message, type) {
        var bg = type === 'success' ? '#22c55e' : '#ef4444';
        if (typeof Toastify === 'function') {
            Toastify({ text: message, backgroundColor: bg, duration: 3000, gravity: 'bottom', position: 'right' }).showToast();
        } else {
            // Fallback: simple alert
            console.log('Toast:', type, message);
        }
    }

    // ═══════════════════════════════════════════════════════
    // MEDIA LIBRARY PICKER
    // ═══════════════════════════════════════════════════════
    let mlTargetKey = null;         // field key we're replacing
    let mlSelectedUrl = null;       // selected image URL
    let mlCurrentPage = 1;
    let mlLastPage = 1;
    let mlSearchQuery = '';
    let mlModal = null;

    // Open modal for a specific image field
    window.openMediaLibrary = function(fieldKey) {
        mlTargetKey = fieldKey;
        mlSelectedUrl = null;
        mlCurrentPage = 1;
        mlSearchQuery = '';
        document.getElementById('mlSearch').value = '';
        document.getElementById('mlSelectBtn').disabled = true;

        if (!mlModal) {
            mlModal = new bootstrap.Modal(document.getElementById('mediaLibraryModal'));
        }
        mlModal.show();
        loadMediaPage(1);
    };

    // Clear image
    window.clearImage = function(fieldKey) {
        const wrapper = document.querySelector('.ve-image-field[data-field-key="' + fieldKey + '"]');
        if (!wrapper) return;
        const thumb = wrapper.querySelector('.ve-image-thumb');
        const input = wrapper.querySelector('.ve-image-url');
        const removeBtn = wrapper.querySelector('.ve-image-remove');

        thumb.classList.remove('has-image');
        thumb.innerHTML = '<i class="material-symbols-rounded placeholder-icon">image</i>';
        input.value = '';
        if (removeBtn) removeBtn.style.display = 'none';
        markDirty();
    };

    // Fetch media page
    function loadMediaPage(page) {
        const grid = document.getElementById('mlGrid');
        const empty = document.getElementById('mlEmpty');
        const loading = document.getElementById('mlLoading');
        const pagWrap = document.getElementById('mlPaginationWrap');

        grid.innerHTML = '';
        empty.style.display = 'none';
        loading.style.display = '';
        pagWrap.style.display = 'none';

        const params = new URLSearchParams({
            page: page,
            per_page: 24,
        });
        if (mlSearchQuery) params.set('search', mlSearchQuery);

        fetch(PICKER_URL + '?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            loading.style.display = 'none';
            const items = data.data || [];
            mlCurrentPage = data.current_page || 1;
            mlLastPage = data.last_page || 1;

            if (items.length === 0) {
                empty.style.display = '';
                return;
            }

            items.forEach(function(item) {
                // Only show images
                if (!item.mime_type || !item.mime_type.startsWith('image/')) return;

                var div = document.createElement('div');
                div.className = 'ml-modal-item';
                div.dataset.url = item.url;
                div.innerHTML =
                    '<img src="' + (item.thumb_url || item.url) + '" alt="' + (item.name || '') + '" loading="lazy">' +
                    '<div class="check-badge"><i class="material-symbols-rounded" style="font-size:14px;">check</i></div>' +
                    '<div class="name" title="' + (item.file_name || item.name) + '">' + (item.file_name || item.name) + '</div>';

                div.addEventListener('click', function() {
                    document.querySelectorAll('.ml-modal-item').forEach(function(el) { el.classList.remove('selected'); });
                    div.classList.add('selected');
                    mlSelectedUrl = item.url;
                    document.getElementById('mlSelectBtn').disabled = false;
                });

                grid.appendChild(div);
            });

            // Pagination
            if (mlLastPage > 1) {
                pagWrap.style.display = '';
                renderPagination();
            }
        })
        .catch(function(err) {
            loading.style.display = 'none';
            empty.style.display = '';
            console.error('Media picker error:', err);
        });
    }

    function renderPagination() {
        const ul = document.getElementById('mlPagination');
        ul.innerHTML = '';

        // Previous
        var liPrev = document.createElement('li');
        liPrev.className = 'page-item' + (mlCurrentPage <= 1 ? ' disabled' : '');
        liPrev.innerHTML = '<a class="page-link" href="#">&laquo;</a>';
        liPrev.querySelector('a').addEventListener('click', function(e) {
            e.preventDefault();
            if (mlCurrentPage > 1) loadMediaPage(mlCurrentPage - 1);
        });
        ul.appendChild(liPrev);

        // Pages
        var start = Math.max(1, mlCurrentPage - 2);
        var end = Math.min(mlLastPage, mlCurrentPage + 2);
        for (var p = start; p <= end; p++) {
            var li = document.createElement('li');
            li.className = 'page-item' + (p === mlCurrentPage ? ' active' : '');
            li.innerHTML = '<a class="page-link" href="#">' + p + '</a>';
            li.querySelector('a').addEventListener('click', (function(page) {
                return function(e) { e.preventDefault(); loadMediaPage(page); };
            })(p));
            ul.appendChild(li);
        }

        // Next
        var liNext = document.createElement('li');
        liNext.className = 'page-item' + (mlCurrentPage >= mlLastPage ? ' disabled' : '');
        liNext.innerHTML = '<a class="page-link" href="#">&raquo;</a>';
        liNext.querySelector('a').addEventListener('click', function(e) {
            e.preventDefault();
            if (mlCurrentPage < mlLastPage) loadMediaPage(mlCurrentPage + 1);
        });
        ul.appendChild(liNext);
    }

    // Search
    document.getElementById('mlSearchBtn').addEventListener('click', function() {
        mlSearchQuery = document.getElementById('mlSearch').value.trim();
        loadMediaPage(1);
    });
    document.getElementById('mlSearch').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            mlSearchQuery = this.value.trim();
            loadMediaPage(1);
        }
    });

    // Select button
    document.getElementById('mlSelectBtn').addEventListener('click', function() {
        if (!mlSelectedUrl || !mlTargetKey) return;

        // Update hidden input
        var input = document.querySelector('.ve-image-url[data-field-key="' + mlTargetKey + '"]');
        if (input) input.value = mlSelectedUrl;

        // Update thumbnail
        var wrapper = document.querySelector('.ve-image-field[data-field-key="' + mlTargetKey + '"]');
        if (wrapper) {
            var thumb = wrapper.querySelector('.ve-image-thumb');
            var removeBtn = wrapper.querySelector('.ve-image-remove');
            thumb.classList.add('has-image');
            thumb.innerHTML = '<img src="' + mlSelectedUrl + '" alt="">';
            if (removeBtn) removeBtn.style.display = '';
        }

        markDirty();
        mlModal.hide();
    });

})();
</script>
@endsection
