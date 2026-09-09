@extends('layouts.master')

@section('title', 'Create Page')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">Create New Page</h3>
            <p class="mb-4">
                Create a new page. You can link it to navigation after creation.
            </p>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Add Existing Manual Blade Page --}}
    <div class="row mb-1">
        <div class="col-lg-8">
            <div class="card border shadow-none">
                <div class="card-body d-flex justify-content-between align-items-center py-3">
                    <div class="me-3">
                        <h6 class="mb-1 font-weight-bolder text-sm">Add Existing Manual Page</h6>
                        <p class="text-xs text-muted mb-0">
                            Select a manually created Blade page from <code>resources/views/pages</code> and add it to the pages management system – without creating a duplicate copy of the file. You can also rename its Blade file.
                        </p>
                    </div>
                    <button type="button" class="btn btn-sm bg-gradient-dark text-nowrap mb-0" data-bs-toggle="modal" data-bs-target="#addExistingPageModal">
                        <i class="material-symbols-rounded me-1" style="font-size: 16px;">add_page</i>
                        Add
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('pages.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Title --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Page Title</label>
                            <div class="input-group input-group-outline">
                                <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                            </div>
                        </div>

                        {{-- Slug --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Slug (auto-generated from title if empty)</label>
                            <div class="input-group input-group-outline">
                                <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="e.g. about-us">
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Content (HTML)</label>
                            <div class="input-group input-group-outline">
                                <textarea name="content" class="form-control" rows="8" placeholder="Page content in HTML...">{{ old('content') }}</textarea>
                            </div>
                        </div>

                        {{-- Layout --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Layout Template</label>
                            <div class="input-group input-group-outline">
                                <select name="layout" class="form-control">
                                    <option value="pages.show" {{ old('layout') == 'pages.show' ? 'selected' : '' }}>Default (pages.show)</option>
                                    <option value="pages.full" {{ old('layout') == 'pages.full' ? 'selected' : '' }}>Full Width (pages.full)</option>
                                    <option value="pages.sidebar" {{ old('layout') == 'pages.sidebar' ? 'selected' : '' }}>With Sidebar (pages.sidebar)</option>
                                </select>
                            </div>
                        </div>

                        {{-- Route Name --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Route Name (for custom Blade templates)</label>
                            <div class="input-group input-group-outline">
                                <input type="text" name="route_name" class="form-control" value="{{ old('route_name') }}" placeholder="e.g. za-nas">
                            </div>
                            <small class="text-muted">Optional. Use the existing route name if this page has a custom Blade template.</small>
                        </div>

                        {{-- Meta Title --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Meta Title (SEO)</label>
                            <div class="input-group input-group-outline">
                                <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title') }}" maxlength="255">
                            </div>
                        </div>

                        {{-- Meta Description --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Meta Description (SEO)</label>
                            <div class="input-group input-group-outline">
                                <textarea name="meta_description" class="form-control" rows="3" maxlength="500">{{ old('meta_description') }}</textarea>
                            </div>
                        </div>

                        {{-- Featured Image --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Featured Image</label>
                            <x-media.media-picker name="featured_image_file" collection="featured_image" label="Featured Image" :multiple="false" :accept="'image/jpeg,image/png,image/webp,image/avif,image/gif,image/svg+xml'" />
                        </div>

                        {{-- Attachments --}}
                        <div class="mb-3">
                            <label class="text-xs font-weight-bolder mb-1 text-dark">Attachments (Documents)</label>
                            <div class="input-group input-group-outline">
                                <input type="file" name="attachment_files[]" class="form-control" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.zip">
                            </div>
                            <small class="text-muted">PDF, Word, Excel, Text, ZIP. Max 20MB per file.</small>
                        </div>

                        {{-- Published --}}
                        <div class="form-check form-switch my-3">
                            <input class="form-check-input" type="checkbox" name="published" value="1" {{ old('published') ? 'checked' : '' }} id="publishedCheck">
                            <label class="form-check-label ms-2" for="publishedCheck">Published (visible on site)</label>
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn bg-gradient-dark">
                                <i class="material-symbols-rounded me-1" style="font-size: 16px;">save</i>
                                Create Page
                            </button>
                            <a href="{{ route('pages.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar Tips --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-sm font-weight-bolder mb-3">Tips</h6>
                    <ul class="text-xs text-muted ps-3" style="list-style: disc;">
                        <li class="mb-2"><strong>Title:</strong> The display name for this page.</li>
                        <li class="mb-2"><strong>Slug:</strong> Used in the URL. Auto-generated from title if left empty.</li>
                        <li class="mb-2"><strong>Layout:</strong> Select a Blade template for rendering. Use "Default" for standard pages.</li>
                        <li class="mb-2"><strong>Route Name:</strong> If this page has a dedicated Blade template (like the existing service pages), enter the route name here so the system knows how to link to it.</li>
                        <li class="mb-2"><strong>Published:</strong> Only published pages can appear on the public site.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Modal: Add Existing Manual Blade Page ─── --}}
    <div class="modal fade" id="addExistingPageModal" tabindex="-1" aria-labelledby="addExistingPageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header pb-0 border-bottom-0">
                    <h5 class="modal-title font-weight-bolder" id="addExistingPageModalLabel">
                        <i class="material-symbols-rounded me-1 align-middle" style="font-size: 20px;">add_page</i>
                        Add Existing Manual Page
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="text-xs text-muted">
                        Manually created Blade pages detected in <code>resources/views/pages</code>. Select one to add it to the pages management system (the file itself is not copied or duplicated).
                    </p>

                    <div id="manualPagesList" class="vstack gap-2">
                        <div class="text-center text-muted py-4 text-xs">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading pages…
                        </div>
                    </div>

                    {{-- Selected page details --}}
                    <div id="selectedPageDetails" class="d-none mt-4">
                        <hr>
                        <h6 class="font-weight-bolder text-sm mb-3">
                            Selected: <span id="aepSelectedName" class="text-primary"></span>
                            <code class="text-xxs ms-1" id="aepSelectedView"></code>
                        </h6>

                        <form id="addExistingPageForm" action="{{ route('pages.add-existing') }}" method="POST">
                            @csrf
                            <input type="hidden" name="view" id="aepView">

                            {{-- Rename --}}
                            <div class="mb-3">
                                <label class="text-xs font-weight-bolder mb-1 text-dark">Rename Blade file / folder (optional)</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" name="new_name" id="aepNewName" class="form-control" autocomplete="off">
                                </div>
                                <small class="text-muted d-block mt-1" id="aepRenameHint"></small>
                            </div>

                            {{-- Title --}}
                            <div class="mb-3">
                                <label class="text-xs font-weight-bolder mb-1 text-dark">Page Title</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" name="title" id="aepTitle" class="form-control" required>
                                </div>
                            </div>

                            {{-- Slug --}}
                            <div class="mb-3">
                                <label class="text-xs font-weight-bolder mb-1 text-dark">Slug (auto-generated from title if empty)</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" name="slug" id="aepSlug" class="form-control">
                                </div>
                            </div>

                            {{-- Route Name --}}
                            <div class="mb-3">
                                <label class="text-xs font-weight-bolder mb-1 text-dark">Route Name (optional, for custom routes)</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" name="route_name" id="aepRouteName" class="form-control">
                                </div>
                            </div>

                            {{-- Published --}}
                            <div class="form-check form-switch my-3">
                                <input class="form-check-input" type="checkbox" name="published" value="1" checked id="aepPublished">
                                <label class="form-check-label ms-2" for="aepPublished">Published (visible on site)</label>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm mb-0" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="addExistingPageForm" class="btn btn-sm bg-gradient-dark mb-0" id="aepSubmitBtn" disabled>
                        <i class="material-symbols-rounded me-1" style="font-size: 16px;">save</i>
                        Add Page
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('addExistingPageModal');
        if (!modalEl) return;

        var listEl = document.getElementById('manualPagesList');
        var detailsEl = document.getElementById('selectedPageDetails');
        var submitBtn = document.getElementById('aepSubmitBtn');
        var renameInput = document.getElementById('aepNewName');
        var renameHint = document.getElementById('aepRenameHint');
        var pagesCache = [];
        var current = null;

        function esc(value) {
            var div = document.createElement('div');
            div.textContent = value == null ? '' : String(value);
            return div.innerHTML;
        }

        function titleCase(name) {
            return name.replace(/[-_]+/g, ' ').replace(/\b\w/g, function (c) { return c.toUpperCase(); });
        }

        function slugify(name) {
            return name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        }

        function updateRenameHint() {
            if (!current) return;
            var value = renameInput.value.trim();
            if (value && value !== current.name) {
                var target = current.kind === 'folder' ? value + '/index.blade.php' : value + '.blade.php';
                renameHint.innerHTML = 'Will rename <code>resources/views/pages/' + esc(current.path) +
                    '</code> &rarr; <code>' + esc(target) + '</code>';
            } else {
                renameHint.textContent = 'Leave unchanged to keep the current file name.';
            }
        }

        function selectPage(page) {
            current = page;
            detailsEl.classList.remove('d-none');
            submitBtn.disabled = false;

            document.getElementById('aepView').value = page.view;
            document.getElementById('aepSelectedName').textContent = page.name;
            document.getElementById('aepSelectedView').textContent = page.view;
            renameInput.value = page.name;
            document.getElementById('aepTitle').value = page.page_title || titleCase(page.name);
            document.getElementById('aepSlug').value = slugify(page.name);
            document.getElementById('aepRouteName').value = '';
            updateRenameHint();
        }

        function renderList(pages) {
            pagesCache = pages;

            if (!pages.length) {
                listEl.innerHTML = '<div class="alert alert-warning text-xs mb-0">No manually created Blade pages were found in resources/views/pages.</div>';
                return;
            }

            var html = '';
            pages.forEach(function (page, index) {
                var badge = page.added
                    ? '<span class="badge bg-gradient-success ms-2">Already added</span>'
                    : '<span class="badge bg-gradient-secondary ms-2">Not added</span>';

                html += '<label class="border rounded-3 p-2 mb-0 d-flex align-items-center ' + (page.added ? 'opacity-50' : '') + '" style="cursor: ' + (page.added ? 'not-allowed' : 'pointer') + ';">' +
                    '<input type="radio" name="aepRadio" value="' + index + '" class="form-check-input mt-0 me-2"' + (page.added ? ' disabled' : '') + '>' +
                    '<span class="flex-grow-1">' +
                        '<span class="text-xs font-weight-bolder d-block">' + esc(page.name) + badge + '</span>' +
                        '<span class="text-xxs text-muted d-block">resources/views/pages/' + esc(page.path) + ' &middot; <code>' + esc(page.view) + '</code></span>' +
                    '</span>' +
                '</label>';
            });

            listEl.innerHTML = html;

            listEl.querySelectorAll('input[name="aepRadio"]').forEach(function (radio) {
                radio.addEventListener('change', function () {
                    var page = pagesCache[parseInt(this.value, 10)];
                    if (page && !page.added) selectPage(page);
                });
            });
        }

        function loadPages() {
            listEl.innerHTML = '<div class="text-center text-muted py-4 text-xs"><span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading pages…</div>';
            detailsEl.classList.add('d-none');
            submitBtn.disabled = true;
            current = null;

            fetch('{{ route('pages.manual-blades') }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (response) { return response.json(); })
                .then(function (data) { renderList(data.pages || []); })
                .catch(function () {
                    listEl.innerHTML = '<div class="alert alert-danger text-xs mb-0">Could not load the list of Blade pages.</div>';
                });
        }

        modalEl.addEventListener('show.bs.modal', loadPages);
        renameInput.addEventListener('input', updateRenameHint);
    });
</script>
@endpush
