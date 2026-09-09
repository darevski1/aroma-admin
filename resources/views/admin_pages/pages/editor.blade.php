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

      <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-preview" title="Preview in new tab">
        <i class="material-symbols-rounded" style="font-size:16px;">open_in_new</i>
        Preview
      </button>

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

    // ── Preview button ─────────────────────────────────────────────
    btnPreview.addEventListener('click', function() {
      window.open('{{ $previewUrl }}', '_blank');
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
