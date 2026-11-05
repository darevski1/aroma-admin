@extends('layouts.master')
@section('title', 'Full Page Editor: ' . $page->title)
@section('content')
<div class="page-editor-wrapper" style="margin: -0.5rem -0.5rem 0 -0.5rem; display: flex; flex-direction: column; height: calc(100vh - 80px);">
  {{-- Editor Toolbar --}}
  <div id="editor-toolbar" style="background: #fff; border-bottom: 1px solid #e5e7eb; padding: 8px 16px; display: flex; align-items: center; gap: 8px; flex-shrink: 0; z-index: 100;">
    <a href="{{ route('pages.edit', $page->id) }}" class="btn btn-sm btn-outline-secondary" title="Back to Edit Page">
      <i class="material-symbols-rounded" style="font-size:16px;">arrow_back</i>
      Back
    </a>

    <div class="vr mx-1"></div>

    <span class="text-sm text-muted" id="editor-status">
      <i class="material-symbols-rounded" style="font-size:14px;">edit</i>
      Click any text on the page to edit it
    </span>

    <div class="ms-auto d-flex align-items-center gap-2">
      <span id="editor-dirty-badge" class="badge bg-warning text-dark" style="display:none;">Unsaved changes</span>

      <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-preview" title="See the page as visitors will (your unsaved edits included)">
        <i class="material-symbols-rounded" style="font-size:16px;" id="btn-preview-icon">visibility</i>
        <span id="btn-preview-label">Preview</span>
      </button>

      <a href="{{ $previewUrl }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary" title="Open the saved page in a new tab">
        <i class="material-symbols-rounded" style="font-size:16px;">open_in_new</i>
      </a>

      <button type="button" class="btn btn-sm bg-gradient-success text-white" id="btn-save" title="Save changes">
        <i class="material-symbols-rounded" style="font-size:16px;">save</i>
        <span id="btn-save-label">Save Changes</span>
      </button>

      <a href="{{ route('pages.edit', $page->id) }}" class="btn btn-sm btn-outline-secondary" id="btn-cancel" title="Cancel editing">
        <i class="material-symbols-rounded" style="font-size:16px;">close</i>
        Cancel
      </a>
    </div>
  </div>

  {{-- Editor Iframe --}}
  <iframe
    id="editor-iframe"
    src="{{ $editorUrl }}"
    style="flex: 1; border: none; width: 100%; background: #fff;"
    sandbox="allow-scripts allow-same-origin allow-forms allow-popups"
  ></iframe>
</div>

{{-- Replace Image modal (opened by clicking an image inside the page) --}}
<div class="modal fade" id="editorImageModal" tabindex="-1" aria-labelledby="editorImageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header pb-0 border-bottom-0">
        <h5 class="modal-title font-weight-bolder" id="editorImageModalLabel">Replace Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-2">
        <div class="row">
          <div class="col-lg-8">
            <div class="d-flex gap-2 align-items-center mb-3">
              <div class="input-group input-group-outline flex-grow-1">
                <input type="search" id="eimSearch" class="form-control" placeholder="Search the Media Library…">
              </div>
              <label class="btn btn-sm bg-gradient-dark text-nowrap mb-0">
                <i class="material-symbols-rounded me-1" style="font-size:16px;">upload</i> Upload new
                <input type="file" id="eimUpload" accept="image/jpeg,image/png,image/webp,image/avif,image/gif,image/svg+xml" hidden>
              </label>
            </div>
            <div id="eimStatus" class="text-xs text-muted mb-2"></div>
            <div id="eimGrid" class="row g-2"></div>
            <div class="text-center mt-3">
              <button type="button" id="eimMore" class="btn btn-sm btn-outline-secondary d-none">Load more</button>
            </div>
          </div>
          <div class="col-lg-4">
            <h6 class="text-sm font-weight-bolder">Selected image</h6>
            <p id="eimNone" class="text-xs text-muted">Choose an image from the library or upload a new one.</p>
            <div id="eimDetails" class="d-none">
              <img id="eimPreview" src="" alt="" class="border rounded mb-3" style="max-width:100%;max-height:160px;object-fit:contain;">
              <div class="mb-2"><label class="text-xs font-weight-bolder mb-1 text-dark">Name</label>
                <div class="input-group input-group-outline"><input type="text" id="eimName" class="form-control" maxlength="255"></div></div>
              <div class="mb-2"><label class="text-xs font-weight-bolder mb-1 text-dark">Alt Text</label>
                <div class="input-group input-group-outline"><input type="text" id="eimAlt" class="form-control" maxlength="500"></div></div>
              <div class="mb-2"><label class="text-xs font-weight-bolder mb-1 text-dark">Title</label>
                <div class="input-group input-group-outline"><input type="text" id="eimTitle" class="form-control" maxlength="255"></div></div>
              <div class="mb-2"><label class="text-xs font-weight-bolder mb-1 text-dark">Caption / Description</label>
                <div class="input-group input-group-outline"><textarea id="eimCaption" class="form-control" rows="2" maxlength="1000"></textarea></div></div>
              <div class="mb-2"><label class="text-xs font-weight-bolder mb-1 text-dark">Copyright</label>
                <div class="input-group input-group-outline"><input type="text" id="eimCopyright" class="form-control" maxlength="255"></div></div>
            </div>
            <div id="eimError" class="text-danger text-xs mt-2 d-none"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm mb-0" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="eimApply" class="btn btn-sm bg-gradient-dark mb-0" disabled>Replace Image</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
(function() {
  // Wait for DOM so the iframe/toolbar elements exist. The script is pushed
  // to the end of <body> via @stack('scripts'), but guard anyway.
  function boot() {
    const iframe = document.getElementById('editor-iframe');
    const btnSave = document.getElementById('btn-save');
    const btnSaveLabel = document.getElementById('btn-save-label');
    const btnPreview = document.getElementById('btn-preview');
    const btnCancel = document.getElementById('btn-cancel');
    const statusEl = document.getElementById('editor-status');
    const dirtyBadge = document.getElementById('editor-dirty-badge');
    const csrfToken = '{{ csrf_token() }}';
    const pageId = {{ $page->id }};
    let editorReady = false;
    let saving = false;

    if (!iframe || !btnSave) return;

    // ── Toast notifications (Material Dashboard style) ───────────
    function showToast(message, type) {
      const colors = {
        success: { bg: 'linear-gradient(195deg, #4CAF50, #2E7D32)', icon: 'check_circle' },
        error:   { bg: 'linear-gradient(195deg, #EF5350, #B71C1C)', icon: 'error' },
      };
      const c = colors[type] || colors.success;

      let container = document.getElementById('editor-toast-container');
      if (!container) {
        container = document.createElement('div');
        container.id = 'editor-toast-container';
        container.style.cssText = 'position:fixed;top:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px;max-width:380px;';
        document.body.appendChild(container);
      }

      const toast = document.createElement('div');
      toast.style.cssText =
        'display:flex;align-items:flex-start;gap:10px;padding:14px 18px;border-radius:12px;color:#fff;' +
        'font-size:14px;font-weight:500;line-height:1.4;box-shadow:0 8px 20px rgba(0,0,0,.25);' +
        'background:' + c.bg + ';opacity:0;transform:translateX(16px);transition:opacity .25s, transform .25s;';
      toast.innerHTML =
        '<span class="material-symbols-rounded" style="font-size:20px;flex-shrink:0;">' + c.icon + '</span>' +
        '<span style="flex:1;"></span>';

      toast.querySelector('span:last-child').textContent = message;
      container.appendChild(toast);

      requestAnimationFrame(function() {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
      });

      const hide = function() {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(16px)';
        setTimeout(function() { toast.remove(); }, 300);
      };
      setTimeout(hide, type === 'error' ? 6000 : 3500);
      toast.addEventListener('click', hide);
    }

    // ── Save button state helpers ─────────────────────────────────
    function setSaving(on) {
      saving = on;
      btnSave.disabled = on;
      btnSave.style.opacity = on ? '0.7' : '';
      btnSave.style.pointerEvents = on ? 'none' : '';
      btnSaveLabel.textContent = on ? 'Saving...' : 'Save Changes';
    }

    // ── Listen for messages from iframe ────────────────────────
    window.addEventListener('message', function(e) {
      const data = e.data;
      if (!data || !data.type) return;

      switch (data.type) {
        case 'editor:ready':
          editorReady = true;
          statusEl.innerHTML = '<i class="material-symbols-rounded" style="font-size:14px;color:green;">check_circle</i> Editor ready — click text to edit';
          break;

        case 'editor:dirty':
          if (data.dirty) {
            dirtyBadge.style.display = '';
            btnSave.classList.remove('disabled');
          } else {
            dirtyBadge.style.display = 'none';
          }
          break;

        case 'editor:saved':
          setSaving(false);
          if (data.success) {
            statusEl.innerHTML = '<i class="material-symbols-rounded" style="font-size:14px;color:green;">check_circle</i> Changes saved successfully';
            dirtyBadge.style.display = 'none';
            showToast(data.message || 'Page saved successfully.', 'success');
          } else {
            statusEl.innerHTML = '<i class="material-symbols-rounded" style="font-size:14px;color:red;">error</i> ' + (data.message || 'Save failed');
            showToast(data.message || 'Failed to save the page. Please try again.', 'error');
          }
          break;
      }
    });

    // ── Save button ────────────────────────────────────────────────
    btnSave.addEventListener('click', function() {
      if (!editorReady || saving) return;
      setSaving(true);
      statusEl.innerHTML = '<i class="material-symbols-rounded" style="font-size:14px;">sync</i> Saving...';
      iframe.contentWindow.postMessage({
        type: 'editor:save',
        pageId: pageId,
        csrfToken: csrfToken,
      }, '*');

      // Safety net: restore the button if the iframe never answers.
      setTimeout(function() {
        if (saving) {
          setSaving(false);
          statusEl.innerHTML = '<i class="material-symbols-rounded" style="font-size:14px;color:red;">error</i> Save timed out — please try again';
          showToast('Failed to save the page. Please try again.', 'error');
        }
      }, 20000);
    });

    window.addEventListener('message', function(event) {
      if (!event.data || event.data.type !== 'editor:media-upload') return;
      const formData = new FormData();
      formData.append('file', event.data.file);
      fetch('{{ route('pages.editor-media', $page->id) }}', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'},
        body: formData,
      }).then(response => response.json().then(data => ({response, data}))).then(({response, data}) => {
        if (!response.ok || !data.success) throw new Error(data.message || 'Image upload failed.');
        iframe.contentWindow.postMessage({type:'editor:media-uploaded', url:data.media.url}, window.location.origin);
      }).catch(error => {
        iframe.contentWindow.postMessage({type:'editor:media-uploaded', error:error.message}, window.location.origin);
      });
    });

    // ── Replace image: Media Library modal ─────────────────────────
    (function() {
      const modalEl = document.getElementById('editorImageModal');
      if (!modalEl) return;

      const $ = function(id) { return document.getElementById(id); };
      const grid = $('eimGrid'), moreBtn = $('eimMore'), statusEl2 = $('eimStatus'), errorEl = $('eimError');
      const applyBtn = $('eimApply');
      const pickerUrl = '{{ route('media.picker') }}';
      const uploadUrl = '{{ route('media.picker.upload') }}';
      const mediaBase = '{{ url('/admin/media') }}';
      let modal = null, items = {}, selected = null, page = 1, lastPage = 1, loadToken = 0, searchTimer = null;
      let current = { alt: '', title: '' };

      function esc(v) { const d = document.createElement('div'); d.textContent = v == null ? '' : String(v); return d.innerHTML; }

      function showError(msg) { errorEl.textContent = msg || ''; errorEl.classList.toggle('d-none', !msg); }

      function tileHtml(item) {
        return '<div class="col-4 col-md-3" data-eim-id="' + item.id + '">' +
          '<div class="border rounded p-1 h-100" style="cursor:pointer;">' +
            '<div style="aspect-ratio:1;overflow:hidden;background:#f0f0f0;" class="rounded">' +
              '<img src="' + esc(item.thumb_url || item.url) + '" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover;" onerror="if(this.src!==this.dataset.full){this.src=this.dataset.full;}" data-full="' + esc(item.url) + '">' +
            '</div>' +
            '<div class="text-xxs text-truncate mt-1">' + esc(item.name) + '</div>' +
          '</div></div>';
      }

      function loadList(reset) {
        const token = ++loadToken;
        if (reset) { page = 1; grid.innerHTML = ''; items = {}; }
        statusEl2.textContent = 'Loading…';
        const params = new URLSearchParams({ mime_type: 'image/', per_page: 24, page: page, search: $('eimSearch').value.trim() });
        fetch(pickerUrl + '?' + params, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
          .then(function(r) { if (!r.ok) throw new Error(); return r.json(); })
          .then(function(data) {
            if (token !== loadToken) return;
            lastPage = data.last_page || 1;
            const html = (data.data || []).map(function(item) { items[item.id] = item; return tileHtml(item); }).join('');
            grid.insertAdjacentHTML('beforeend', html);
            moreBtn.classList.toggle('d-none', page >= lastPage);
            statusEl2.textContent = data.total ? '' : 'No images found in the Media Library.';
            highlight();
          })
          .catch(function() { if (token === loadToken) statusEl2.textContent = 'Could not load the Media Library.'; });
      }

      function highlight() {
        grid.querySelectorAll('[data-eim-id]').forEach(function(col) {
          const on = selected && String(selected.id) === col.getAttribute('data-eim-id');
          col.firstElementChild.style.borderColor = on ? '#344767' : '';
          col.firstElementChild.style.boxShadow = on ? '0 0 0 2px #344767' : '';
        });
      }

      function select(item) {
        selected = item;
        const props = item.custom_properties || {};
        $('eimPreview').src = item.url;
        $('eimName').value = item.name || '';
        $('eimAlt').value = props.alt_text || current.alt || '';
        $('eimTitle').value = props.title || current.title || '';
        $('eimCaption').value = props.caption || '';
        $('eimCopyright').value = props.copyright || '';
        $('eimNone').classList.add('d-none');
        $('eimDetails').classList.remove('d-none');
        applyBtn.disabled = false;
        showError('');
        highlight();
      }

      function open(data) {
        current = { alt: data.alt || '', title: data.title || '' };
        selected = null;
        $('eimDetails').classList.add('d-none');
        $('eimNone').classList.remove('d-none');
        applyBtn.disabled = true;
        showError('');
        $('eimSearch').value = '';
        modal = modal || bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
        loadList(true);
      }

      grid.addEventListener('click', function(e) {
        const col = e.target.closest('[data-eim-id]');
        if (col && items[col.getAttribute('data-eim-id')]) select(items[col.getAttribute('data-eim-id')]);
      });
      moreBtn.addEventListener('click', function() { page++; loadList(false); });
      $('eimSearch').addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() { loadList(true); }, 300);
      });

      // Upload straight into the Media Library, then select it right away.
      $('eimUpload').addEventListener('change', function() {
        const file = this.files && this.files[0];
        const input = this;
        if (!file) return;
        const body = new FormData();
        body.append('file', file);
        body.append('collection', 'editor-library');
        showError('');
        statusEl2.textContent = 'Uploading…';
        fetch(uploadUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: body })
          .then(function(r) { return r.json().then(function(d) { if (!r.ok || !d.id) throw new Error((d.errors && Object.values(d.errors)[0][0]) || d.message || 'Upload failed.'); return d; }); })
          .then(function(item) {
            item.custom_properties = item.custom_properties || {};
            items[item.id] = item;
            grid.insertAdjacentHTML('afterbegin', tileHtml(item));
            statusEl2.textContent = '';
            select(item);
          })
          .catch(function(err) { statusEl2.textContent = ''; showError(err.message || 'Upload failed.'); })
          .finally(function() { input.value = ''; });
      });

      // Save the metadata to the Media Library, then swap the image on the page.
      applyBtn.addEventListener('click', function() {
        if (!selected) return;
        applyBtn.disabled = true;
        showError('');
        const props = {
          alt_text: $('eimAlt').value, title: $('eimTitle').value,
          caption: $('eimCaption').value, copyright: $('eimCopyright').value,
        };
        fetch(mediaBase + '/' + selected.id, {
          method: 'PATCH',
          headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
          body: JSON.stringify({ name: $('eimName').value || selected.name, custom_properties: props }),
        })
          .then(function(r) { return r.json().then(function(d) { if (!r.ok || !d.success) throw new Error((d.errors && Object.values(d.errors)[0][0]) || d.message || 'Could not save the image details.'); }); })
          .then(function() {
            const url = new URL(selected.url, window.location.origin);
            iframe.contentWindow.postMessage({
              type: 'editor:image-replace',
              url: url.origin === window.location.origin ? url.pathname + url.search : url.href,
              alt: props.alt_text,
              title: props.title,
            }, window.location.origin);
            modal.hide();
          })
          .catch(function(err) { showError(err.message); })
          .finally(function() { applyBtn.disabled = !selected; });
      });

      window.addEventListener('message', function(e) {
        if (e.source !== iframe.contentWindow || !e.data || e.data.type !== 'editor:image-select') return;
        open(e.data);
      });
    })();

    // ── Preview button ─────────────────────────────────────────────
    // Toggles an in-place preview: same page, editing chrome hidden, unsaved edits kept.
    let previewing = false;
    btnPreview.addEventListener('click', function() {
      if (!editorReady) return;
      previewing = !previewing;
      iframe.contentWindow.postMessage({type: 'editor:preview', on: previewing}, '*');
      document.getElementById('btn-preview-label').textContent = previewing ? 'Back to editing' : 'Preview';
      document.getElementById('btn-preview-icon').textContent = previewing ? 'edit' : 'visibility';
      btnPreview.classList.toggle('btn-dark', previewing);
      btnPreview.classList.toggle('btn-outline-secondary', !previewing);
      statusEl.innerHTML = previewing
        ? '<i class="material-symbols-rounded" style="font-size:14px;">visibility</i> Preview — this is how visitors will see the page'
        : '<i class="material-symbols-rounded" style="font-size:14px;color:green;">check_circle</i> Editor ready — click text to edit';
    });

    // ── Cancel with unsaved changes warning ───────────────────────
    btnCancel.addEventListener('click', function(e) {
      if (dirtyBadge.style.display !== 'none') {
        if (!confirm('You have unsaved changes. Are you sure you want to leave?')) {
          e.preventDefault();
          return false;
        }
      }
    });

    // ── Warn on page unload if dirty ───────────────────────────────
    window.addEventListener('beforeunload', function(e) {
      if (dirtyBadge.style.display !== 'none') {
        e.preventDefault();
        e.returnValue = '';
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
</script>
@endpush
