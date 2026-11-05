/**
 * Page Editor – WordPress-style inline visual editing.
 *
 * Loaded into the real public page (inside the admin iframe) when ?editor=1.
 * Text is edited in place on the live layout: no forms, no separate inputs.
 *
 * Persistence:
 *  - Template (Blade) pages:   only the edited elements are sent as
 *    {k: original text, n: nth occurrence, html} and re-applied on render.
 *  - Stored-HTML pages (Home): the whole <main> HTML is sent, as before.
 *
 * The "editable element" rules mirror app/Services/InlineEdits.php exactly.
 */
(function () {
  'use strict';

  if (new URLSearchParams(window.location.search).get('editor') !== '1') return;

  const TARGETS = 'h1,h2,h3,h4,h5,h6,p,li,td,th,blockquote,figcaption,div,a,span,strong,em,button,dt,dd,label,summary';
  const BLOCKS = 'h1,h2,h3,h4,h5,h6,p,li,td,th,blockquote,figcaption,div,section,article,ul,ol,table,tr,dl,dt,dd,form,header,footer,nav,main,aside,figure';
  const OPAQUE = 'svg,img,picture,video,iframe,input,select,textarea,canvas,script,style';
  const SKIP = 'header,nav,footer,script,style,svg,form,select,textarea';

  let isDirty = false;
  let previewing = false;
  let pendingImage = null;
  let activeImage = null;
  let editables = [];
  const dirtyEls = new Set();

  const norm = function (s) { return (s || '').replace(/[\s ]+/g, ' ').trim(); };

  // ── Styles (only exist inside the editor iframe) ───────────────
  const style = document.createElement('style');
  style.textContent = `
    body[data-editor-active="true"] [data-editor-editable="true"] { outline: 2px dashed rgba(59,130,246,.5); outline-offset: 2px; cursor: text; transition: outline-color .15s; }
    body[data-editor-active="true"] [data-editor-editable="true"]:hover { outline-color: rgba(59,130,246,.9); }
    body[data-editor-active="true"] [data-editor-editable="true"]:focus { outline: 2px solid #3b82f6; background-color: rgba(59,130,246,.04); }
    body[data-editor-active="true"] [data-editor-editable="true"][data-editor-dirty="true"] { outline-color: rgba(245,158,11,.8); }
    body[data-editor-active="true"] main img { cursor: pointer; outline: 2px dashed rgba(16,185,129,.6); outline-offset: 2px; }
    body[data-editor-active="true"] main img:hover { outline-color: rgba(16,185,129,1); }
    #ie-format { position: absolute; z-index: 2147483000; display: none; gap: 2px; padding: 4px; border-radius: 8px; background: #111827; box-shadow: 0 8px 24px rgba(0,0,0,.35); }
    #ie-format button { all: unset; box-sizing: border-box; min-width: 30px; height: 30px; display: grid; place-items: center; border-radius: 6px; color: #fff; font: 600 14px/1 system-ui, sans-serif; cursor: pointer; }
    #ie-format button:hover { background: rgba(255,255,255,.18); }
    #ie-format::after { content: ""; position: absolute; left: 50%; bottom: -5px; width: 10px; height: 10px; margin-left: -5px; background: #111827; transform: rotate(45deg); }
  `;
  document.head.appendChild(style);

  // ── Which elements are editable (mirror of InlineEdits.php) ────
  function root() { return document.querySelector('main') || document.body; }

  function isEditable(el) {
    if (!el.matches(TARGETS)) return false;
    if (norm(el.textContent).length < 2) return false;
    return !el.querySelector(BLOCKS + ',' + OPAQUE);
  }

  function collect(node, out) {
    Array.prototype.forEach.call(node.children, function (child) {
      if (child.matches(SKIP) || child.id === 'ie-format' || child.id === 'ie-config' || child.id === 'page-editor-image-upload') return;
      if (isEditable(child)) { out.push(child); return; }
      collect(child, out);
    });
    return out;
  }

  // Each element is keyed by its ORIGINAL text (kept in data-ie-key once an
  // earlier edit changed it) plus its occurrence index among equal keys.
  function assignKeys(els) {
    const seen = {};
    els.forEach(function (el) {
      let k = norm(el.textContent);
      const stored = el.getAttribute('data-ie-key');
      if (stored) { try { k = atob(stored).split('\0')[0]; } catch (e) { /* keep current text */ } }
      el._ieKey = k;
      el._ieN = seen[k] = (seen[k] === undefined ? 0 : seen[k] + 1);
      el._ieOrig = el.innerHTML;
    });
  }

  function mode() {
    const cfg = document.getElementById('ie-config');
    return cfg ? cfg.getAttribute('data-mode') : 'content';
  }

  // ── Enable editing ─────────────────────────────────────────────
  function enableEditing() {
    document.body.setAttribute('data-editor-active', 'true');
    document.body.setAttribute('data-editor-image', 'true');

    editables = collect(root(), []);
    assignKeys(editables);
    assignImageKeys();
    editables.forEach(function (el) {
      el.setAttribute('contenteditable', 'true');
      el.setAttribute('data-editor-editable', 'true');
      el.setAttribute('spellcheck', 'false');
    });

    // Block navigation / form submits while editing, but keep the caret usable.
    document.addEventListener('click', function (e) {
      if (e.target.closest && e.target.closest('#ie-format, #ie-standalone')) return;
      const link = e.target.closest && e.target.closest('a, button, summary, label');
      if (link && !link.closest('#ie-format')) e.preventDefault();
    }, true);
    document.addEventListener('submit', function (e) { e.preventDefault(); }, true);

    document.addEventListener('input', function (e) {
      const el = e.target && e.target.closest ? e.target.closest('[data-editor-editable="true"]') : null;
      if (!el) return;
      el.setAttribute('data-editor-dirty', 'true');
      dirtyEls.add(el);
      markDirty();
    });

    // Enter inserts a line break (never splits into new block elements); Space on
    // buttons/summary must type a space instead of "clicking".
    document.addEventListener('keydown', function (e) {
      const el = e.target && e.target.closest ? e.target.closest('[data-editor-editable="true"]') : null;
      if (!el || previewing) return;
      if (e.key === 'Enter') {
        e.preventDefault();
        document.execCommand('insertLineBreak');
      } else if (e.key === ' ' && el.matches('button, summary')) {
        e.preventDefault();
        document.execCommand('insertText', false, ' ');
      } else if (e.key === 'Escape') {
        el.blur();
      }
    });

    document.addEventListener('paste', function (e) {
      const el = e.target && e.target.closest ? e.target.closest('[data-editor-editable="true"]') : null;
      if (!el) return;
      e.preventDefault();
      const text = (e.clipboardData || window.clipboardData).getData('text/plain');
      document.execCommand('insertText', false, text);
    });

    // Dropping files/HTML into text would break the layout.
    document.addEventListener('drop', function (e) {
      if (e.target.closest && e.target.closest('[data-editor-editable="true"]')) e.preventDefault();
    });

    document.addEventListener('click', onImageClick, true);
    setupFormatBar();
  }

  // Images are keyed by their ORIGINAL src attribute + occurrence (see InlineEdits.php).
  let images = [];
  function assignImageKeys() {
    const seen = {};
    images = Array.prototype.filter.call(root().querySelectorAll('img'), function (img) {
      return !img.closest(SKIP) && !img.closest('#page-editor-image-upload');
    });
    images.forEach(function (img) {
      let k = (img.getAttribute('src') || '').replace(/^https?:\/\/[^\/]+/i, '');
      const stored = img.getAttribute('data-ie-key');
      if (stored) { try { k = atob(stored).split('\0')[0].replace(/^img:/, ''); } catch (e) { /* keep src */ } }
      img._ieKey = k;
      img._ieN = seen[k] = (seen[k] === undefined ? 0 : seen[k] + 1);
      img._ieOrig = img.getAttribute('src') + '|' + (img.getAttribute('alt') || '') + '|' + (img.getAttribute('title') || '');
    });
  }
  function imgState(img) { return img.getAttribute('src') + '|' + (img.getAttribute('alt') || '') + '|' + (img.getAttribute('title') || ''); }

  // ── Floating format bar (bold / italic / link) ─────────────────
  function setupFormatBar() {
    const bar = document.createElement('div');
    bar.id = 'ie-format';
    bar.innerHTML =
      '<button type="button" data-cmd="bold" title="Bold"><b>B</b></button>' +
      '<button type="button" data-cmd="italic" title="Italic"><i style="font-family:serif">I</i></button>' +
      '<button type="button" data-cmd="link" title="Link">🔗</button>' +
      '<button type="button" data-cmd="unlink" title="Remove link">⊘</button>';
    document.body.appendChild(bar);

    bar.addEventListener('mousedown', function (e) { e.preventDefault(); }); // keep the selection
    bar.addEventListener('click', function (e) {
      const btn = e.target.closest('button');
      if (!btn) return;
      const cmd = btn.getAttribute('data-cmd');
      if (cmd === 'link') {
        const url = window.prompt('Link URL (https://…, /page, mailto:…)');
        if (url && !/^\s*(javascript|data|vbscript):/i.test(url)) document.execCommand('createLink', false, url.trim());
      } else {
        document.execCommand(cmd);
      }
      const el = activeEditable();
      if (el) { el.setAttribute('data-editor-dirty', 'true'); dirtyEls.add(el); markDirty(); }
    });

    document.addEventListener('selectionchange', function () {
      if (previewing) return hideBar();
      const sel = window.getSelection();
      const el = activeEditable();
      if (!sel || sel.isCollapsed || !el) return hideBar();
      const r = sel.getRangeAt(0).getBoundingClientRect();
      if (!r.width && !r.height) return hideBar();
      bar.style.display = 'flex';
      const w = bar.offsetWidth;
      const left = Math.max(8, Math.min(window.scrollX + r.left + r.width / 2 - w / 2, window.scrollX + document.documentElement.clientWidth - w - 8));
      bar.style.left = left + 'px';
      bar.style.top = (window.scrollY + r.top - bar.offsetHeight - 10) + 'px';
    });

    function hideBar() { bar.style.display = 'none'; }
  }

  function activeEditable() {
    const sel = window.getSelection();
    const node = sel && sel.anchorNode ? (sel.anchorNode.nodeType === 1 ? sel.anchorNode : sel.anchorNode.parentElement) : null;
    return node && node.closest ? node.closest('[data-editor-editable="true"]') : null;
  }

  // ── Preview (see the page as visitors do) ──────────────────────
  function setPreview(on) {
    previewing = !!on;
    document.body.setAttribute('data-editor-active', previewing ? 'false' : 'true');
    editables.forEach(function (el) {
      if (previewing) el.removeAttribute('contenteditable'); else el.setAttribute('contenteditable', 'true');
    });
    if (previewing && document.activeElement && document.activeElement.blur) document.activeElement.blur();
    const bar = document.getElementById('ie-format');
    if (bar) bar.style.display = 'none';
  }

  // ── Replace existing images via the Media Library (stored-HTML pages) ──
  function addImageToolbar() {
    if (mode() !== 'content') return;
    const toolbar = document.createElement('label');
    toolbar.id = 'page-editor-image-upload';
    toolbar.appendChild(document.createTextNode('Insert image'));
    toolbar.style.cssText = 'position:fixed;right:16px;bottom:16px;z-index:9999;padding:10px 14px;border-radius:8px;background:#1f2937;color:#fff;font:600 13px sans-serif;cursor:pointer;box-shadow:0 4px 18px #0003';
    const input = document.createElement('input');
    input.type = 'file'; input.accept = 'image/*'; input.hidden = true;
    input.addEventListener('change', function () {
      const file = input.files && input.files[0];
      if (!file) return;
      pendingImage = { file: file, target: document.activeElement };
      notifyParent({ type: 'editor:media-upload', file: file });
      toolbar.firstChild.textContent = 'Uploading image…';
    });
    toolbar.appendChild(input);
    document.body.appendChild(toolbar);
  }

  let standalone = false; // opened outside the admin iframe: no Media Library modal available
  function onImageClick(e) {
    if (previewing || standalone) return;
    const img = e.target && e.target.closest ? e.target.closest('img') : null;
    if (!img || !root().contains(img) || img.closest('#page-editor-image-upload')) return;
    e.preventDefault();
    e.stopPropagation();
    activeImage = img;
    notifyParent({
      type: 'editor:image-select',
      src: img.currentSrc || img.src,
      alt: img.getAttribute('alt') || '',
      title: img.getAttribute('title') || '',
    });
  }

  function replaceImage(data) {
    const img = activeImage;
    activeImage = null;
    if (!img || !data.url) return;
    img.setAttribute('src', data.url);
    ['srcset', 'sizes', 'data-src', 'data-srcset'].forEach(function (a) { img.removeAttribute(a); });
    if (img.parentElement && img.parentElement.tagName === 'PICTURE') {
      img.parentElement.querySelectorAll('source').forEach(function (s) { s.remove(); });
    }
    img.setAttribute('alt', data.alt || '');
    if (data.title) img.setAttribute('title', data.title); else img.removeAttribute('title');
    img.setAttribute('data-editor-dirty', 'true');
    markDirty();
  }

  // ── Dirty state / parent messaging ─────────────────────────────
  function markDirty() {
    if (!isDirty) { isDirty = true; notifyParent({ type: 'editor:dirty', dirty: true }); }
  }
  function markClean() {
    isDirty = false;
    dirtyEls.clear();
    editables.forEach(function (el) { el.removeAttribute('data-editor-dirty'); });
    notifyParent({ type: 'editor:dirty', dirty: false });
  }
  function notifyParent(msg) {
    if (window.parent && window.parent !== window) window.parent.postMessage(msg, '*');
  }

  // ── Serialisation ──────────────────────────────────────────────
  function cleanInner(el) {
    const clone = el.cloneNode(true);
    clone.querySelectorAll('[data-editor-editable],[contenteditable]').forEach(function (n) {
      n.removeAttribute('data-editor-editable'); n.removeAttribute('contenteditable');
    });
    return clone.innerHTML;
  }

  function getEdits() {
    const out = [];
    dirtyEls.forEach(function (el) {
      const html = cleanInner(el);
      if (html === el._ieOrig) return;
      out.push({ k: el._ieKey, n: el._ieN, html: html });
    });
    images.forEach(function (img) {
      if (imgState(img) === img._ieOrig) return;
      out.push({
        k: 'img:' + img._ieKey, n: img._ieN,
        html: JSON.stringify({ src: img.getAttribute('src'), alt: img.getAttribute('alt') || '', title: img.getAttribute('title') || '' }),
      });
    });
    return out;
  }

  function getPageContent() {
    const clone = root().cloneNode(true);
    clone.querySelectorAll('[data-editor-editable],[contenteditable],[data-editor-dirty]').forEach(function (el) {
      el.removeAttribute('data-editor-editable');
      el.removeAttribute('contenteditable');
      el.removeAttribute('data-editor-dirty');
      el.removeAttribute('spellcheck');
    });
    clone.removeAttribute('data-editor-active');
    ['#page-editor-toolbar', '#page-editor-image-upload', '#ie-format', '#ie-config'].forEach(function (s) {
      const n = clone.querySelector(s); if (n) n.remove();
    });
    return clone.innerHTML;
  }

  // ── Save ───────────────────────────────────────────────────────
  function saveContent(pageId, csrfToken) {
    const body = mode() === 'content' ? { content: getPageContent() } : { edits: getEdits() };

    if (mode() !== 'content' && !body.edits.length) {
      // (nothing changed)
      markClean();
      return Promise.resolve({ success: true, message: 'No changes to save.' });
    }

    return fetch('/pages/' + pageId + '/save-content', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
      body: JSON.stringify(body),
    })
      .then(function (r) {
        return r.json().catch(function () { throw new Error('Server error (HTTP ' + r.status + ')'); }).then(function (data) {
          if (!r.ok) {
            let msg = data.message || ('Save failed (HTTP ' + r.status + ')');
            if (data.errors) { const first = Object.values(data.errors)[0]; if (first && first[0]) msg = first[0]; }
            const err = new Error(msg); err.handled = true; throw err;
          }
          return data;
        });
      })
      .then(function (data) {
        if (data.success) {
          // Saved text becomes the new baseline for the next save in this session.
          editables.forEach(function (el) { el._ieOrig = cleanInner(el); });
          images.forEach(function (img) { img._ieOrig = imgState(img); img.removeAttribute('data-editor-dirty'); });
          markClean();
        }
        return data;
      });
  }

  // ── Commands from the admin panel ──────────────────────────────
  window.addEventListener('message', function (e) {
    if (e.source !== window.parent) return;
    const data = e.data;
    if (!data || !data.type) return;

    switch (data.type) {
      case 'editor:media-uploaded':
        if (data.error) {
          window.alert(data.error);
        } else if (data.url && pendingImage) {
          const active = pendingImage.target;
          const image = document.createElement('img');
          image.src = data.url; image.alt = ''; image.style.maxWidth = '100%';
          if (active && active.getAttribute && active.getAttribute('data-editor-editable') === 'true') active.appendChild(image);
          else root().appendChild(image);
          markDirty();
        }
        pendingImage = null;
        const label = document.getElementById('page-editor-image-upload');
        if (label) label.firstChild.textContent = 'Insert image';
        break;

      case 'editor:image-replace':
        if (e.origin === window.location.origin) replaceImage(data);
        break;

      case 'editor:preview':
        setPreview(data.on);
        break;

      case 'editor:save':
        saveContent(data.pageId, data.csrfToken)
          .then(function (result) { notifyParent({ type: 'editor:saved', success: result.success, message: result.message }); })
          .catch(function (err) {
            notifyParent({ type: 'editor:saved', success: false, message: err && err.handled ? err.message : 'Failed to save the page. Please try again.' });
          });
        break;

      case 'editor:getContent':
        notifyParent({ type: 'editor:content', content: getPageContent() });
        break;

      case 'editor:reload':
        isDirty = false;
        window.location.reload();
        break;
    }
  });

  window.addEventListener('beforeunload', function (e) {
    if (isDirty) { e.preventDefault(); e.returnValue = ''; }
  });

  // ── Standalone mode: the editor URL opened directly (not inside the admin iframe) ──
  // Shows Save / Preview on the page itself.
  function addStandaloneBar() {
    const cfg = document.getElementById('ie-config');
    const pageId = cfg && cfg.getAttribute('data-page-id');
    const csrf = cfg && cfg.getAttribute('data-csrf');
    if (window.parent !== window || !pageId || !csrf) return;

    const bar = document.createElement('div');
    bar.id = 'ie-standalone';
    bar.style.cssText = 'position:fixed;left:50%;bottom:20px;transform:translateX(-50%);z-index:2147483001;display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:12px;background:#111827;color:#fff;font:500 14px system-ui,sans-serif;box-shadow:0 10px 30px rgba(0,0,0,.35)';
    const btn = 'border:0;border-radius:8px;padding:8px 14px;font:600 13px system-ui,sans-serif;cursor:pointer;';
    bar.innerHTML =
      '<span id="ie-st" style="padding:0 6px;opacity:.85">Visual Editor</span>' +
      '<a href="/pages/' + pageId + '/editor" style="' + btn + 'background:#374151;color:#fff;text-decoration:none" title="Open with the Media Library to replace images">Images…</a>' +
      '<button type="button" id="ie-prev" style="' + btn + 'background:#374151;color:#fff">Preview</button>' +
      '<button type="button" id="ie-save" style="' + btn + 'background:#16a34a;color:#fff">Save</button>';
    document.body.appendChild(bar);

    const st = bar.querySelector('#ie-st'), save = bar.querySelector('#ie-save'), prev = bar.querySelector('#ie-prev');
    function say(t, color) { st.textContent = t; st.style.color = color || ''; }

    prev.addEventListener('click', function () {
      setPreview(!previewing);
      prev.textContent = previewing ? 'Back to editing' : 'Preview';
    });
    save.addEventListener('click', function () {
      if (save.disabled) return;
      save.disabled = true; say('Saving…');
      saveContent(pageId, csrf)
        .then(function (r) { say(r.success ? 'Saved ✓' : 'Save failed', r.success ? '#86efac' : '#fca5a5'); })
        .catch(function (e) { say(e && e.handled ? e.message : 'Save failed', '#fca5a5'); })
        .finally(function () { save.disabled = false; setTimeout(function () { say(isDirty ? 'Unsaved changes' : 'Visual Editor', ''); }, 2500); });
    });
    standalone = true;
    document.body.removeAttribute('data-editor-image');
    document.addEventListener('input', function () { say('Unsaved changes', '#fcd34d'); });
  }

  function init() {
    enableEditing();
    addImageToolbar();
    addStandaloneBar();
    notifyParent({ type: 'editor:ready' });
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
