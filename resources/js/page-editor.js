/**
 * Page Editor – Inline Content Editing via iframe
 *
 * Injected into the public page iframe when ?editor=1 is present.
 * Makes text elements contenteditable and saves modified HTML
 * back to the pages.content field via AJAX.
 */
(function () {
  'use strict';

  // ── State ──────────────────────────────────────────────────────
  let isDirty = false;
  let originalHTML = '';
  const EDITABLE = 'h1, h2, h3, h4, h5, h6, p, span, a, li, td, th, strong, em, blockquote, figcaption, .text, .title, .subtitle, .description';
  const EDITOR_URL = new URLSearchParams(window.location.search).get('editor') === '1';
  let pendingImage = null;

  // ── Highlight styles ───────────────────────────────────────────
  if (!EDITOR_URL) return;

  const style = document.createElement('style');
  style.textContent = `
    [data-editor-editable="true"] {
      outline: 2px dashed rgba(59,130,246,0.5) !important;
      outline-offset: 2px;
      cursor: text !important;
      transition: outline-color 0.15s;
    }
    [data-editor-editable="true"]:hover {
      outline-color: rgba(59,130,246,0.9) !important;
    }
    [data-editor-editable="true"]:focus {
      outline: 2px solid rgba(59,130,246,1) !important;
      outline-offset: 2px;
      background: rgba(59,130,246,0.04) !important;
    }
    body[data-editor-active="true"] {
      cursor: default;
    }
    body[data-editor-active="true"] a { pointer-events: none; }
    body[data-editor-active="true"] button { pointer-events: none; }
    body[data-editor-active="true"] form { pointer-events: none; }
  `;
  document.head.appendChild(style);

  // ── Make elements editable ─────────────────────────────────────
  function enableEditing() {
    document.body.setAttribute('data-editor-active', 'true');
    const els = document.querySelectorAll(EDITABLE);
    els.forEach(function (el) {
      // Skip elements inside the editor toolbar itself
      if (el.closest('#page-editor-toolbar')) return;
      if (el.closest('header, nav, footer, script, style, svg, button')) return;
      if (!el.closest('main')) return;
      // Skip very short text nodes (single chars, whitespace-only)
      if (el.textContent.trim().length < 2) return;
      // Skip elements that are likely icons or SVGs
      if (el.querySelector('svg') && el.textContent.trim().length < 5) return;
      el.setAttribute('contenteditable', 'true');
      el.setAttribute('data-editor-editable', 'true');
    });
    originalHTML = getPageContent();
    trackChanges();
  }

  function addImageToolbar() {
    const toolbar = document.createElement('label');
    toolbar.id = 'page-editor-image-upload';
    const labelText = document.createTextNode('Insert image');
    toolbar.appendChild(labelText);
    toolbar.style.cssText = 'position:fixed;right:16px;bottom:16px;z-index:9999;padding:10px 14px;border-radius:8px;background:#1f2937;color:#fff;font:600 13px sans-serif;cursor:pointer;box-shadow:0 4px 18px #0003';
    const input = document.createElement('input');
    input.type = 'file'; input.accept = 'image/*'; input.hidden = true;
    input.addEventListener('change', function() {
      const file = input.files && input.files[0];
      if (!file) return;
      pendingImage = {file:file, target:document.activeElement};
      notifyParent({type:'editor:media-upload', file:file});
      toolbar.textContent = 'Uploading image…';
    });
    toolbar.appendChild(input);
    document.body.appendChild(toolbar);
  }

  // ── Track changes ──────────────────────────────────────────────
  function trackChanges() {
    document.addEventListener('input', function (e) {
      if (e.target.getAttribute('data-editor-editable') === 'true') {
        markDirty();
      }
    });
  }

  function markDirty() {
    if (!isDirty) {
      isDirty = true;
      notifyParent({ type: 'editor:dirty', dirty: true });
    }
  }

  function markClean() {
    isDirty = false;
    notifyParent({ type: 'editor:dirty', dirty: false });
  }

  // ── Post message to parent (admin panel) ───────────────────────
  function notifyParent(msg) {
    if (window.parent && window.parent !== window) {
      window.parent.postMessage(msg, '*');
    }
  }

  // ── Get modified page HTML ─────────────────────────────────────
  function getContentRoot() {
    // Capture only the page's own content region so navigation,
    // footer and scripts are not persisted into pages.content.
    return document.querySelector('main') || document.body;
  }

  function getPageContent() {
    const root = getContentRoot();
    // Get the root HTML with editor attributes removed
    const clone = root.cloneNode(true);
    // Remove editor-specific attributes
    clone.querySelectorAll('[data-editor-editable]').forEach(function (el) {
      el.removeAttribute('data-editor-editable');
      el.removeAttribute('contenteditable');
    });
    clone.removeAttribute('data-editor-active');
    // Remove editor toolbar if present
    const toolbar = clone.querySelector('#page-editor-toolbar');
    if (toolbar) toolbar.remove();
    return clone.innerHTML;
  }

  // ── Save content via AJAX ──────────────────────────────────────
  function saveContent(pageId, csrfToken) {
    const content = getPageContent();

    return fetch('/pages/' + pageId + '/save-content', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json',
      },
      body: JSON.stringify({ content: content }),
    })
      .then(function (r) {
        return r.json().catch(function () {
          // Non-JSON response (e.g. HTML error page) – build a useful error.
          throw new Error('Server error (HTTP ' + r.status + ')');
        }).then(function (data) {
          if (!r.ok) {
            // Laravel validation/HTTP errors: { message, errors? }
            let msg = data.message || ('Save failed (HTTP ' + r.status + ')');
            if (data.errors) {
              const first = Object.values(data.errors)[0];
              if (first && first[0]) msg = first[0];
            }
            const err = new Error(msg);
            err.handled = true;
            throw err;
          }
          return data;
        });
      })
      .then(function (data) {
        if (data.success) {
          markClean();
          originalHTML = getContentRoot().innerHTML;
        }
        return data;
      });
  }

  // ── Listen for commands from parent ────────────────────────────
  window.addEventListener('message', function (e) {
    const data = e.data;
    if (!data || !data.type) return;

    switch (data.type) {
      case 'editor:media-uploaded':
        if (data.error) {
          window.alert(data.error);
        } else if (data.url && pendingImage) {
          const active = pendingImage.target;
          const image = document.createElement('img');
          image.src = data.url;
          image.alt = '';
          image.style.maxWidth = '100%';
          if (active && active.getAttribute && active.getAttribute('data-editor-editable') === 'true') active.appendChild(image);
          else getContentRoot().appendChild(image);
          markDirty();
        }
        pendingImage = null;
        const uploadLabel = document.getElementById('page-editor-image-upload');
        if (uploadLabel) uploadLabel.firstChild.textContent = 'Insert image';
        break;

      case 'editor:save':
        saveContent(data.pageId, data.csrfToken)
          .then(function (result) {
            notifyParent({ type: 'editor:saved', success: result.success, message: result.message });
          })
          .catch(function (err) {
            const msg = err && err.handled ? err.message : 'Failed to save the page. Please try again.';
            notifyParent({ type: 'editor:saved', success: false, message: msg });
          });
        break;

      case 'editor:getContent':
        notifyParent({ type: 'editor:content', content: getPageContent() });
        break;

      case 'editor:reload':
        window.location.reload();
        break;
    }
  });

  // ── Unsaved changes warning ────────────────────────────────────
  window.addEventListener('beforeunload', function (e) {
    if (isDirty) {
      e.preventDefault();
      e.returnValue = '';
    }
  });

  // ── Notify parent that editor is ready ─────────────────────────
  function init() {
    enableEditing();
    addImageToolbar();
    notifyParent({ type: 'editor:ready' });
  }

  // Wait for DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
