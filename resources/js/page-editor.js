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
    .ie-bg-btn { position: absolute; white-space: nowrap; width: max-content; line-height: 1; z-index: 2147482000; display: none; align-items: center; gap: 6px; padding: 6px 10px; border: 0; border-radius: 999px; background: #059669; color: #fff; font: 600 12px/1 system-ui, sans-serif; cursor: pointer; box-shadow: 0 4px 14px rgba(0,0,0,.3); }
    .ie-bg-btn:hover { background: #047857; }
    body[data-editor-active="true"] .ie-bg-btn { display: inline-flex; }
    [data-ie-reveal] { opacity: 1 !important; visibility: visible !important; z-index: 6 !important; }
    body[data-editor-active="true"] main img[data-ie-broken] { min-width: 160px; min-height: 90px; background: #e5e7eb; }
    .ie-sl-btn { position: absolute; white-space: nowrap; width: max-content; line-height: 1; z-index: 2147482500; display: none; align-items: center; gap: 6px; padding: 8px 12px; border: 0; border-radius: 999px; background: #2563eb; color: #fff; font: 600 12px/1 system-ui, sans-serif; cursor: pointer; box-shadow: 0 4px 14px rgba(0,0,0,.35); }
    .ie-sl-btn:hover { background: #1d4ed8; }
    body[data-editor-active="true"] .ie-sl-btn { display: inline-flex; }
    .ie-sl-pop { position: absolute; z-index: 2147483001; display: none; width: 300px; max-height: 60vh; overflow: auto; padding: 8px; border-radius: 12px; background: #111827; color: #fff; font: 500 12px/1.4 system-ui, sans-serif; box-shadow: 0 10px 30px rgba(0,0,0,.45); }
    .ie-sl-pop .ie-sl-h { padding: 4px 6px 8px; font-weight: 700; }
    .ie-sl-pop .ie-sl-row { display: flex; align-items: center; gap: 10px; width: 100%; padding: 6px; border: 0; border-radius: 8px; background: transparent; color: #fff; text-align: left; cursor: pointer; font: inherit; }
    .ie-sl-pop .ie-sl-row:hover { background: #1f2937; }
    .ie-sl-pop .ie-sl-t { flex: none; width: 64px; height: 42px; border-radius: 6px; background: #374151 center/cover no-repeat; }
    .ie-sl-pop .ie-sl-d { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ie-sl-pop .ie-sl-d small { display: block; opacity: .6; overflow: hidden; text-overflow: ellipsis; }
    .ie-sl-pop .ie-sl-go { flex: none; padding: 4px 8px; border-radius: 999px; background: #2563eb; font-weight: 700; }
    #ie-imgs-btn { position: fixed; left: 16px; bottom: 16px; z-index: 2147483002; display: none; align-items: center; gap: 6px; padding: 9px 14px; border: 0; border-radius: 999px; background: #059669; color: #fff; font: 600 13px/1 system-ui, sans-serif; cursor: pointer; box-shadow: 0 6px 18px rgba(0,0,0,.35); }
    body[data-editor-active="true"] #ie-imgs-btn { display: inline-flex; }
    #ie-imgs { position: fixed; left: 16px; bottom: 60px; z-index: 2147483002; display: none; width: 300px; max-height: 60vh; overflow: auto; padding: 8px; border-radius: 12px; background: #111827; color: #fff; font: 500 12px/1.4 system-ui, sans-serif; box-shadow: 0 10px 30px rgba(0,0,0,.4); }
    #ie-imgs .ie-i { display: flex; align-items: center; gap: 10px; width: 100%; padding: 6px; border: 0; border-radius: 8px; background: transparent; color: #fff; text-align: left; cursor: pointer; font: inherit; }
    #ie-imgs .ie-i:hover { background: #1f2937; }
    #ie-imgs .ie-i.is-on { background: #374151; }
    #ie-imgs .ie-t { flex: none; width: 64px; height: 42px; border-radius: 6px; object-fit: cover; background: #374151 center/cover no-repeat; }
    #ie-imgs .ie-n { flex: none; width: 18px; opacity: .6; font-weight: 700; }
    #ie-imgs .ie-d { flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    #ie-imgs .ie-d small { display: block; opacity: .6; overflow: hidden; text-overflow: ellipsis; }
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
    setupBackgroundImages();
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
    document.addEventListener('click', onSelectClick, true);
    trackTextSelection();
    setupFormatBar();
    setupImageList();
    setupSliders();
  }

  // ── Sliders: every slider gets a "Slider images" button that lists its slides ──
  // and opens the Media Library straight away to add / replace a slide's picture.
  const SLIDER_HINT = /slider|carousel|swiper|slideshow|slides/i;
  function setupSliders() {
    const found = [];
    root().querySelectorAll('*').forEach(function (el) {
      if (el.closest(SKIP) || el.closest('.ie-sl-pop')) return;
      const hint = el.getAttribute('aria-roledescription') === 'carousel' || SLIDER_HINT.test((el.id || '') + ' ' + (typeof el.className === 'string' ? el.className : ''));
      if (!hint) return;
      if (found.some(function (f) { return f.el.contains(el); })) return; // keep the outermost
      const imgs = images.filter(function (i) { return el.contains(i); });
      if (imgs.length >= 2) found.push({ el: el, imgs: imgs });
    });
    if (!found.length) return;

    found.forEach(function (f, n) {
      const btn = document.createElement('button');
      btn.type = 'button'; btn.className = 'ie-sl-btn';
      btn.innerHTML = '&#128444; Slider images (' + f.imgs.length + ')';
      const pop = document.createElement('div');
      pop.className = 'ie-sl-pop';
      document.body.appendChild(btn); document.body.appendChild(pop);

      function render() {
        pop.innerHTML = '';
        const h = document.createElement('div'); h.className = 'ie-sl-h'; h.textContent = 'Slider ' + (found.length > 1 ? (n + 1) + ' ' : '') + '— pick a slide to add / change its picture';
        pop.appendChild(h);
        f.imgs.forEach(function (img, i) {
          const row = document.createElement('button'); row.type = 'button'; row.className = 'ie-sl-row';
          const t = document.createElement('span'); t.className = 'ie-sl-t';
          t.style.backgroundImage = "url('" + (img.currentSrc || img.src || '').replace(/'/g, '%27') + "')";
          const d = document.createElement('span'); d.className = 'ie-sl-d';
          d.textContent = 'Slide ' + (i + 1) + (img.getAttribute('alt') ? ' — ' + img.getAttribute('alt') : '');
          const sm = document.createElement('small'); sm.textContent = (img.getAttribute('src') || '').split('/').pop(); d.appendChild(sm);
          const go = document.createElement('span'); go.className = 'ie-sl-go'; go.textContent = 'Add image';
          row.appendChild(t); row.appendChild(d); row.appendChild(go);
          row.addEventListener('mousedown', function (e) { e.preventDefault(); });
          row.addEventListener('click', function (e) {
            e.preventDefault(); e.stopPropagation();
            if (previewing || standalone) { if (standalone) window.alert('Open the editor from Admin → Pages to use the Media Library.'); return; }
            pop.style.display = 'none';
            activeImage = img;
            notifyParent({ type: 'editor:image-select', src: img.currentSrc || img.src, alt: img.getAttribute('alt') || '', title: img.getAttribute('title') || '' });
          });
          pop.appendChild(row);
        });
      }
      btn.addEventListener('click', function (e) {
        e.preventDefault(); e.stopPropagation();
        if (previewing) return;
        if (pop.style.display === 'block') { pop.style.display = 'none'; return; }
        render(); pop.style.display = 'block'; place();
      });
      function place() {
        const r = f.el.getBoundingClientRect();
        const top = Math.max(r.top, 0) + window.scrollY, left = r.left + window.scrollX;
        btn.style.top = (top + Math.min(r.height - 50, 110)) + 'px';
        btn.style.left = (left + 10) + 'px';
        pop.style.top = (parseFloat(btn.style.top) + 38) + 'px';
        pop.style.left = btn.style.left;
      }
      f.place = place; place();
      window.addEventListener('resize', place);
      window.addEventListener('load', place);
      if (window.ResizeObserver) new ResizeObserver(place).observe(document.body);
      setInterval(place, 1500);
    });
  }

  // ── "Images on this page" list ─────────────────────────────────
  // Slides stacked on top of each other (opacity 0 / covered) and broken images
  // can't be clicked on the page. This list reaches every <img> in the page so
  // any of them can be selected and replaced from the Media Library.
  let revealed = [];
  function clearReveal() {
    revealed.forEach(function (n) { n.removeAttribute('data-ie-reveal'); });
    revealed = [];
  }
  function revealImage(img) {
    clearReveal();
    for (let n = img; n && n !== root().parentElement && n !== document.body; n = n.parentElement) {
      const cs = getComputedStyle(n);
      if (parseFloat(cs.opacity) === 0 || cs.visibility === 'hidden') { n.setAttribute('data-ie-reveal', '1'); revealed.push(n); }
    }
  }
  function setupImageList() {
    if (!images.length) return;
    images.forEach(function (img) {
      const mark = function () { if (img.complete && !img.naturalWidth) img.setAttribute('data-ie-broken', '1'); else img.removeAttribute('data-ie-broken'); };
      mark(); img.addEventListener('error', mark); img.addEventListener('load', mark);
    });
    const btn = document.createElement('button');
    btn.type = 'button'; btn.id = 'ie-imgs-btn';
    btn.innerHTML = '&#128247; Images on this page (' + images.length + ')';
    const list = document.createElement('div');
    list.id = 'ie-imgs';
    document.body.appendChild(list);
    document.body.appendChild(btn);

    function render() {
      list.innerHTML = '';
      images.forEach(function (img, i) {
        const row = document.createElement('button');
        row.type = 'button'; row.className = 'ie-i' + (selected === img ? ' is-on' : '');
        const src = img.getAttribute('src') || '';
        const thumb = document.createElement('span');
        thumb.className = 'ie-t';
        thumb.style.backgroundImage = "url('" + (img.currentSrc || img.src || '').replace(/'/g, '%27') + "')";
        const num = document.createElement('span'); num.className = 'ie-n'; num.textContent = i + 1;
        const d = document.createElement('span'); d.className = 'ie-d';
        d.textContent = img.getAttribute('alt') || '(no alt text)';
        const sm = document.createElement('small'); sm.textContent = src.split('/').pop();
        d.appendChild(sm);
        row.appendChild(num); row.appendChild(thumb); row.appendChild(d);
        row.addEventListener('mousedown', function (e) { e.preventDefault(); });
        row.addEventListener('click', function () {
          if (previewing) return;
          revealImage(img);
          selectElement(img);
          const r = img.getBoundingClientRect();
          if (r.top < 0 || r.bottom > window.innerHeight) window.scrollTo({ top: Math.max(0, r.top + window.scrollY - 120), behavior: 'smooth' });
          render();
        });
        list.appendChild(row);
      });
    }
    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      if (list.style.display === 'block') { list.style.display = 'none'; return; }
      render(); list.style.display = 'block';
    });
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

  // ── CSS background images (inline style="background-image:url(...)") ──
  // Keyed like <img>: ORIGINAL url + occurrence, stored as "bg:<url>" (see InlineEdits.php).
  let bgs = [];
  const BG_URL = /url\(\s*(['"]?)(.*?)\1\s*\)/i;
  const IMG_EXT = /\.(jpe?g|png|webp|gif|svg|avif)(\?[^'")]*)?$/i;
  function bgUrl(el) { const m = (el.getAttribute('style') || '').match(BG_URL); return m ? m[2] : null; }
  function setBgUrl(el, url) {
    el.setAttribute('style', (el.getAttribute('style') || '').replace(BG_URL, function () { return "url('" + url + "')"; }));
  }
  function setupBackgroundImages() {
    const seen = {};
    bgs = Array.prototype.filter.call(root().querySelectorAll('[style*="url("]'), function (el) {
      const u = bgUrl(el);
      if (!u || !IMG_EXT.test(u) || el.closest(SKIP)) return false;
      const os = el.getAttribute('data-ie-os'); // set by "Background image" on a section: not a template background
      if (os !== null) { try { if (!BG_URL.test(atob(os))) return false; } catch (e) { /* keep */ } }
      return true;
    });
    bgs.forEach(function (el) {
      let k = bgUrl(el).replace(/^https?:\/\/[^\/]+/i, '');
      const stored = el.getAttribute('data-ie-key');
      if (stored) { try { const d = atob(stored).split('\0')[0]; if (d.indexOf('bg:') === 0) k = d.slice(3); } catch (e) { /* keep url */ } }
      el._ieKey = k;
      el._ieN = seen[k] = (seen[k] === undefined ? 0 : seen[k] + 1);
      el._ieOrig = bgUrl(el);
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'ie-bg-btn';
      btn.innerHTML = '&#128247; Change background image';
      btn.addEventListener('click', function (ev) {
        ev.preventDefault(); ev.stopPropagation();
        if (previewing) return;
        activeImage = el;
        notifyParent({ type: 'editor:image-select', src: new URL(bgUrl(el), window.location.href).href, alt: '', title: '', background: true });
      });
      document.body.appendChild(btn);
      el._ieBtn = btn;
    });
    placeBgButtons();
    window.addEventListener('resize', placeBgButtons);
    window.addEventListener('load', placeBgButtons);
    if (window.ResizeObserver) new ResizeObserver(placeBgButtons).observe(document.body);
    setInterval(placeBgButtons, 1500);
  }
  function placeBgButtons() {
    bgs.forEach(function (el) {
      const r = el.getBoundingClientRect();
      el._ieBtn.style.top = (r.top + window.scrollY + 10) + 'px';
      el._ieBtn.style.left = (r.left + window.scrollX + 10) + 'px';
    });
  }

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
    if (previewing) deselect();
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
    // A click selects the image (resize handles + "Upload / replace image…" in the
    // style panel); the Media Library modal opens from that button.
    e.preventDefault();
  }

  function replaceImage(data) {
    const img = activeImage;
    activeImage = null;
    if (!img || !data.url) return;
    if (!img._ieBtn && img.tagName !== 'IMG') { // section background image (added by the editor)
      setData(img, { bgimg: data.url });
      return;
    }
    if (img._ieBtn) { // background image element
      setBgUrl(img, data.url);
      img.setAttribute('data-editor-dirty', 'true');
      markDirty();
      return;
    }
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

  // ── Style editing: text colour, background colour, image size ───
  // Stored per element as "sty:<child-index path from the content root>|<tag>" =
  // {color, bg, ng, w, h, lock} (see InlineEdits.php). The template is never
  // rewritten; the values are re-applied as inline style on every render.
  const styled = new Map(); // element -> {orig: template inline style, data: {...}, touched: bool}
  const MANAGED = ['color', 'background-color', 'width', 'max-width', 'height', 'aspect-ratio', 'object-fit'];
  const HEX_RE = /^#(?:[0-9a-f]{3}|[0-9a-f]{6})$/i;
  let selected = null;
  let panel = null;
  let handles = null;
  let savedRange = null;
  let savedEl = null;

  function toHex(css) {
    const m = (css || '').match(/rgba?\(\s*(\d+)[,\s]+(\d+)[,\s]+(\d+)(?:[,\s/]+([\d.]+))?/);
    if (!m || (m[4] !== undefined && parseFloat(m[4]) === 0)) return null;
    return '#' + [1, 2, 3].map(function (i) { return ('0' + (+m[i]).toString(16)).slice(-2); }).join('');
  }
  function longHex(h) { return h.length === 4 ? '#' + h[1] + h[1] + h[2] + h[2] + h[3] + h[3] : h; }

  function styleRootEl() {
    const cfg = document.getElementById('ie-config');
    return cfg && cfg.getAttribute('data-style-root') === 'body' ? document.body : (document.querySelector('body > main') || root());
  }
  function stylePath(el) {
    const base = styleRootEl();
    const idx = [];
    let n = el;
    while (n && n !== base) {
      const p = n.parentElement;
      if (!p) return null;
      idx.unshift(Array.prototype.indexOf.call(p.children, n));
      n = p;
    }
    return n === base && idx.length ? idx.join('.') + '|' + el.tagName.toLowerCase() : null;
  }

  // Styles saved earlier arrive already applied by the server (+ data-ie-os = the template's own style).
  function rec(el) {
    let r = styled.get(el);
    if (r) return r;
    const os = el.getAttribute('data-ie-os');
    let orig = el.getAttribute('style') || '';
    const data = {};
    if (os !== null) {
      try { orig = atob(os); } catch (e) { /* keep current */ }
      if (el.style.color) data.color = toHex(el.style.color);
      if (el.style.backgroundColor) { data.bg = toHex(el.style.backgroundColor); if (el.style.backgroundImage === 'none') data.ng = true; }
      if (/px$/.test(el.style.width)) {
        data.w = Math.round(parseFloat(el.style.width));
        data.lock = !el.style.aspectRatio;
        if (!data.lock) { const a = el.style.aspectRatio.split('/'); data.h = Math.round(data.w * parseFloat(a[1]) / parseFloat(a[0])); }
      }
    }
    if (os !== null && !BG_URL.test(orig)) {
      const m = (el.getAttribute('style') || '').match(BG_URL);
      if (m && IMG_EXT.test(m[2])) data.bgimg = m[2];
    }
    Object.keys(data).forEach(function (k) { if (data[k] === null || data[k] === undefined) delete data[k]; });
    r = { orig: orig, data: data, touched: false, ngApplied: !!data.ng, bgimgApplied: !!data.bgimg };
    styled.set(el, r);
    return r;
  }

  function applyData(el, r) {
    const tmp = document.createElement('div');
    tmp.style.cssText = r.orig;
    MANAGED.forEach(function (p) {
      el.style.removeProperty(p);
      const v = tmp.style.getPropertyValue(p);
      if (v) el.style.setProperty(p, v, tmp.style.getPropertyPriority(p));
    });
    if (r.ngApplied && !r.data.ng && !r.data.bgimg) { // gradient hidden by an earlier colour: bring it back
      el.style.removeProperty('background-image');
      const v = tmp.style.getPropertyValue('background-image');
      if (v) el.style.setProperty('background-image', v);
      r.ngApplied = false;
    }
    const d = r.data;
    const BGP = ['background-image', 'background-size', 'background-position', 'background-repeat'];
    if (d.bgimg) {
      el.style.setProperty('background-image', "url('" + d.bgimg + "')");
      el.style.setProperty('background-size', 'cover');
      el.style.setProperty('background-position', 'center');
      el.style.setProperty('background-repeat', 'no-repeat');
      r.bgimgApplied = true;
    } else if (r.bgimgApplied) {
      BGP.forEach(function (p) {
        el.style.removeProperty(p);
        const v = tmp.style.getPropertyValue(p);
        if (v) el.style.setProperty(p, v);
      });
      r.bgimgApplied = false;
    }
    if (d.color) el.style.setProperty('color', d.color);
    if (d.bg) {
      el.style.setProperty('background-color', d.bg);
      if (d.ng && !d.bgimg) { el.style.setProperty('background-image', 'none'); r.ngApplied = true; }
    }
    if (d.w) {
      el.style.setProperty('width', d.w + 'px');
      el.style.setProperty('max-width', '100%'); // never wider than its container
      el.style.setProperty('height', 'auto');
      if (d.lock === false && d.h) {
        el.style.setProperty('aspect-ratio', d.w + ' / ' + d.h);
        el.style.setProperty('object-fit', 'fill');
      }
    }
    if (!el.getAttribute('style')) el.removeAttribute('style');
  }

  function setData(el, patch) {
    const r = rec(el);
    Object.keys(patch).forEach(function (k) {
      if (patch[k] === null || patch[k] === undefined || patch[k] === '') delete r.data[k]; else r.data[k] = patch[k];
    });
    if (patch.bg && r.data.ng === undefined) {
      const bi = getComputedStyle(el).backgroundImage;
      if (bi.indexOf('gradient') !== -1 && bi.indexOf('url(') === -1) r.data.ng = true;
    }
    if (!r.data.bg) delete r.data.ng;
    r.touched = true;
    applyData(el, r);
    markDirty();
    refreshPanel();
  }

  function el$(id) { return document.getElementById(id); }

  function buildPanel() {
    const css = document.createElement('style');
    css.textContent = `
      body[data-editor-active="true"] [data-ie-sel] { outline: 2px solid #f59e0b !important; outline-offset: 2px; }
      #ie-style { position: fixed; right: 16px; top: 16px; width: 252px; z-index: 2147483002; display: none; padding: 12px; border-radius: 12px; background: #111827; color: #fff; font: 500 12px/1.4 system-ui, sans-serif; box-shadow: 0 10px 30px rgba(0,0,0,.4); }
      #ie-style * { box-sizing: border-box; }
      #ie-style .ie-row { display: flex; align-items: center; gap: 6px; margin-top: 6px; }
      #ie-style .ie-lbl { margin-top: 10px; font-weight: 700; opacity: .85; }
      #ie-style input[type=color] { width: 34px; height: 28px; padding: 0; border: 0; background: none; cursor: pointer; }
      #ie-style input[type=text], #ie-style input[type=number] { flex: 1; min-width: 0; height: 28px; padding: 0 8px; border: 1px solid #374151; border-radius: 6px; background: #1f2937; color: #fff; font: 12px system-ui, sans-serif; }
      #ie-style button { border: 0; border-radius: 6px; padding: 5px 9px; background: #374151; color: #fff; font: 600 12px system-ui, sans-serif; cursor: pointer; }
      #ie-style button:hover { background: #4b5563; }
      #ie-style .ie-hint { margin-top: 4px; font-size: 11px; opacity: .6; }
      #ie-handles .ie-h { position: absolute; width: 14px; height: 14px; margin: -7px 0 0 -7px; border: 2px solid #fff; border-radius: 3px; background: #f59e0b; z-index: 2147483001; touch-action: none; }
    `;
    document.head.appendChild(css);

    panel = document.createElement('div');
    panel.id = 'ie-style';
    panel.innerHTML =
      '<div class="ie-row" style="margin-top:0"><strong id="ie-s-label" style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></strong>' +
      '<button type="button" id="ie-s-up" title="Select the containing block (section)">&#9650; Parent</button><button type="button" id="ie-s-close" title="Deselect">&#10005;</button></div>' +
      '<div id="ie-s-warn" class="ie-hint" style="display:none;color:#fca5a5">This element cannot be addressed for saving.</div>' +
      '<div id="ie-s-text"><div class="ie-lbl">Text color</div><div class="ie-row"><input type="color" id="ie-s-color"><input type="text" id="ie-s-color-hex" maxlength="7" placeholder="#RRGGBB"><button type="button" id="ie-s-color-reset" title="Back to the original color">Reset</button></div>' +
      '<div class="ie-hint">Select part of a text to color only that part.</div>' +
      '<div class="ie-lbl">Background color</div><div class="ie-row"><input type="color" id="ie-s-bg"><input type="text" id="ie-s-bg-hex" maxlength="7" placeholder="#RRGGBB"><button type="button" id="ie-s-bg-reset" title="Back to the original background">Reset</button></div>' +
      '<div class="ie-lbl">Background image</div><div class="ie-row"><button type="button" id="ie-s-bgimg" style="flex:1">Add / change image&hellip;</button><button type="button" id="ie-s-bgimg-reset" title="Remove the added background image">Remove</button></div>' +
      '<div class="ie-hint">Sets a cover image behind this block (e.g. a hero section). Use &#9650; Parent to pick the whole section.</div></div>' +
      '<div id="ie-s-img" style="display:none"><div class="ie-lbl">Image size <span id="ie-s-dim" style="font-weight:500;opacity:.7"></span></div>' +
      '<div class="ie-row"><input type="number" id="ie-s-w" min="24" max="5000" placeholder="Width"><span>&times;</span><input type="number" id="ie-s-h" min="24" max="5000" placeholder="Height"><span>px</span></div>' +
      '<div class="ie-row"><label style="display:flex;gap:6px;align-items:center;flex:1"><input type="checkbox" id="ie-s-lock" checked> Keep aspect ratio</label><button type="button" id="ie-s-size-reset">Reset size</button></div>' +
      '<div class="ie-hint">Drag a corner handle on the image. The file itself is never cropped or changed.</div>' +
      '<div class="ie-row"><button type="button" id="ie-s-replace" style="flex:1">Upload / replace image&hellip;</button></div></div>';
    document.body.appendChild(panel);

    handles = document.createElement('div');
    handles.id = 'ie-handles';
    handles.style.cssText = 'position:absolute;left:0;top:0;width:0;height:0;display:none';
    ['nw', 'ne', 'sw', 'se'].forEach(function (c) {
      const h = document.createElement('div');
      h.className = 'ie-h'; h.setAttribute('data-c', c);
      h.style.cursor = (c === 'nw' || c === 'se') ? 'nwse-resize' : 'nesw-resize';
      h.addEventListener('pointerdown', onHandleDown);
      handles.appendChild(h);
    });
    document.body.appendChild(handles);

    panel.addEventListener('mousedown', function (e) { if (!e.target.closest('input')) e.preventDefault(); }); // keep text selection
    el$('ie-s-close').addEventListener('click', deselect);
    el$('ie-s-up').addEventListener('click', function () {
      const p = selected && selected.parentElement;
      if (p && p !== root() && root().contains(p)) selectElement(p);
    });

    function colorWiring(pickId, hexId, resetId, prop) {
      const pick = el$(pickId), hex = el$(hexId);
      function apply(v) {
        if (!selected) return;
        if (prop === 'color') applyTextColor(v); else setData(selected, { bg: v });
      }
      pick.addEventListener('input', function () { hex.value = pick.value; apply(pick.value); });
      hex.addEventListener('change', function () {
        let v = hex.value.trim();
        if (v && v[0] !== '#') v = '#' + v;
        if (!HEX_RE.test(v)) { window.alert('Enter a valid HEX color, e.g. #1a2b3c'); refreshPanel(); return; }
        v = longHex(v).toLowerCase(); hex.value = v; pick.value = v; apply(v);
      });
      el$(resetId).addEventListener('click', function () {
        if (!selected) return;
        if (prop === 'color') setData(selected, { color: null }); else setData(selected, { bg: null });
      });
    }
    colorWiring('ie-s-color', 'ie-s-color-hex', 'ie-s-color-reset', 'color');
    colorWiring('ie-s-bg', 'ie-s-bg-hex', 'ie-s-bg-reset', 'bg');

    el$('ie-s-bgimg').addEventListener('click', function () {
      if (!selected || selected.tagName === 'IMG') return;
      if (standalone) { window.alert('Open the editor from Admin → Pages to add images with the Media Library.'); return; }
      activeImage = selected;
      const cur = (styled.get(selected) || { data: {} }).data.bgimg || '';
      notifyParent({ type: 'editor:image-select', src: cur, alt: '', title: '', sectionBg: true });
    });
    el$('ie-s-bgimg-reset').addEventListener('click', function () {
      if (selected && selected.tagName !== 'IMG') setData(selected, { bgimg: null });
    });
    el$('ie-s-w').addEventListener('change', function () { sizeFromInputs('w'); });
    el$('ie-s-h').addEventListener('change', function () { sizeFromInputs('h'); });
    el$('ie-s-lock').addEventListener('change', function () {
      if (!selected || selected.tagName !== 'IMG') return;
      const rc = selected.getBoundingClientRect();
      const lock = el$('ie-s-lock').checked;
      setData(selected, { w: Math.round(rc.width), lock: lock, h: lock ? null : Math.round(rc.height) });
    });
    el$('ie-s-size-reset').addEventListener('click', function () {
      if (selected && selected.tagName === 'IMG') setData(selected, { w: null, h: null, lock: null });
    });
    el$('ie-s-replace').addEventListener('click', function () {
      if (!selected || selected.tagName !== 'IMG' || standalone) {
        if (standalone) window.alert('Open the editor from Admin → Pages to replace images with the Media Library.');
        return;
      }
      activeImage = selected;
      notifyParent({ type: 'editor:image-select', src: selected.currentSrc || selected.src, alt: selected.getAttribute('alt') || '', title: selected.getAttribute('title') || '' });
    });

    window.addEventListener('resize', placeHandles);
    window.addEventListener('scroll', placeHandles, { passive: true });
    setInterval(placeHandles, 400);
  }

  function ratioOf(img) {
    return img.naturalWidth && img.naturalHeight ? img.naturalHeight / img.naturalWidth : (img.getBoundingClientRect().height / (img.getBoundingClientRect().width || 1));
  }
  function sizeFromInputs(which) {
    if (!selected || selected.tagName !== 'IMG') return;
    const wIn = el$('ie-s-w'), hIn = el$('ie-s-h');
    const lock = el$('ie-s-lock').checked;
    let w = parseInt(wIn.value, 10), h = parseInt(hIn.value, 10);
    if (which === 'h' && lock && h) w = Math.round(h / ratioOf(selected));
    if (!w || w < 24 || w > 5000) { refreshPanel(); return; }
    if (!lock && (!h || h < 24 || h > 5000)) { h = Math.round(selected.getBoundingClientRect().height); }
    setData(selected, { w: w, lock: lock, h: lock ? null : h });
  }

  function onHandleDown(e) {
    if (!selected || selected.tagName !== 'IMG') return;
    e.preventDefault(); e.stopPropagation();
    const img = selected, corner = e.target.getAttribute('data-c');
    const rc = img.getBoundingClientRect();
    const start = { x: e.clientX, y: e.clientY, w: rc.width, h: rc.height };
    const lock = el$('ie-s-lock').checked;
    const ratio = lock ? ratioOf(img) : start.h / start.w;
    e.target.setPointerCapture(e.pointerId);
    function move(ev) {
      const dx = (corner.indexOf('w') !== -1 ? -1 : 1) * (ev.clientX - start.x);
      const dy = (corner.indexOf('n') !== -1 ? -1 : 1) * (ev.clientY - start.y);
      const w = Math.min(5000, Math.max(24, Math.round(start.w + dx)));
      const h = lock ? Math.round(w * ratio) : Math.min(5000, Math.max(24, Math.round(start.h + dy)));
      setData(img, { w: w, lock: lock, h: lock ? null : h });
    }
    function up(ev) {
      e.target.releasePointerCapture(ev.pointerId);
      e.target.removeEventListener('pointermove', move);
      e.target.removeEventListener('pointerup', up);
    }
    e.target.addEventListener('pointermove', move);
    e.target.addEventListener('pointerup', up);
  }

  function placeHandles() {
    if (!handles) return;
    if (!selected || selected.tagName !== 'IMG' || previewing) { handles.style.display = 'none'; return; }
    const r = selected.getBoundingClientRect();
    handles.style.display = 'block';
    const x = r.left + window.scrollX, y = r.top + window.scrollY;
    const pos = { nw: [x, y], ne: [x + r.width, y], sw: [x, y + r.height], se: [x + r.width, y + r.height] };
    Array.prototype.forEach.call(handles.children, function (h) {
      const p = pos[h.getAttribute('data-c')];
      h.style.left = p[0] + 'px'; h.style.top = p[1] + 'px';
    });
    const dim = el$('ie-s-dim');
    if (dim) dim.textContent = Math.round(r.width) + ' × ' + Math.round(r.height) + ' px';
  }

  function refreshPanel() {
    if (!panel || !selected) return;
    const isImg = selected.tagName === 'IMG';
    el$('ie-s-label').textContent = '<' + selected.tagName.toLowerCase() + '>' + (selected.classList[0] ? ' .' + selected.classList[0] : '');
    el$('ie-s-img').style.display = isImg ? 'block' : 'none';
    el$('ie-s-text').style.display = isImg ? 'none' : 'block';
    el$('ie-s-warn').style.display = mode() === 'content' || stylePath(selected) ? 'none' : 'block';
    const r = styled.get(selected);
    const d = r ? r.data : {};
    const cs = getComputedStyle(selected);
    el$('ie-s-color').value = longHex(d.color || toHex(cs.color) || '#000000');
    el$('ie-s-color-hex').value = d.color || '';
    el$('ie-s-bg').value = longHex(d.bg || toHex(cs.backgroundColor) || '#ffffff');
    el$('ie-s-bg-hex').value = d.bg || '';
    if (isImg) {
      const rc = selected.getBoundingClientRect();
      if (document.activeElement !== el$('ie-s-w')) el$('ie-s-w').value = Math.round(rc.width);
      if (document.activeElement !== el$('ie-s-h')) el$('ie-s-h').value = Math.round(rc.height);
      el$('ie-s-lock').checked = d.lock !== false;
    }
    placeHandles();
  }

  function selectElement(el) {
    if (!panel) buildPanel();
    if (selected && selected !== el) selected.removeAttribute('data-ie-sel');
    if (revealed.length && !(revealed.some(function (n) { return n === el || n.contains(el); }))) clearReveal();
    selected = el;
    el.setAttribute('data-ie-sel', '1');
    panel.style.display = 'block';
    refreshPanel();
  }
  function deselect() {
    clearReveal();
    if (selected) selected.removeAttribute('data-ie-sel');
    selected = null;
    if (panel) panel.style.display = 'none';
    if (handles) handles.style.display = 'none';
  }

  // Colour for the selected words, or for the whole element when nothing is highlighted.
  function applyTextColor(hex) {
    if (savedRange && savedEl && savedEl === selected) {
      savedEl.focus();
      const sel = window.getSelection();
      sel.removeAllRanges(); sel.addRange(savedRange);
      document.execCommand('styleWithCSS', false, true);
      document.execCommand('foreColor', false, hex);
      savedEl.setAttribute('data-editor-dirty', 'true'); dirtyEls.add(savedEl); markDirty();
      return;
    }
    setData(selected, { color: hex });
  }

  function onSelectClick(e) {
    if (previewing) return;
    const t = e.target;
    if (!t || !t.closest) return;
    if (t.closest('#ie-style, #ie-format, #ie-standalone, #ie-handles, .ie-bg-btn, #page-editor-image-upload, #ie-imgs, #ie-imgs-btn')) return;
    const rootEl = root();
    if (!rootEl.contains(t) || t === rootEl || t.closest(SKIP)) { deselect(); return; }
    const ed = t.closest('[data-editor-editable="true"]');
    const target = t.closest('img') || ed
      || t.closest('section,article,aside,li,figure,ul,ol,table,div,a,button,p,span,h1,h2,h3,h4,h5,h6') || t;
    if (target === rootEl) { deselect(); return; }
    if (savedEl && savedEl !== target) { savedRange = null; savedEl = null; }
    selectElement(target);
  }

  function trackTextSelection() {
    document.addEventListener('selectionchange', function () {
      const sel = window.getSelection();
      const el = activeEditable();
      if (sel && !sel.isCollapsed && el) { savedRange = sel.getRangeAt(0).cloneRange(); savedEl = el; }
    });
    document.addEventListener('mousedown', function (e) {
      if (e.target.closest && !e.target.closest('#ie-style')) { savedRange = null; savedEl = null; }
    }, true);
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
    bgs.forEach(function (el) {
      const u = bgUrl(el);
      if (u === el._ieOrig) return;
      out.push({ k: 'bg:' + el._ieKey, n: el._ieN, html: JSON.stringify({ src: u, alt: '', title: '' }) });
    });
    styled.forEach(function (r, el) {
      const p = r.touched ? stylePath(el) : null;
      if (p) out.push({ k: 'sty:' + p, n: 0, html: JSON.stringify(r.data) });
    });
    return out;
  }

  function getPageContent() {
    const clone = root().cloneNode(true);
    clone.querySelectorAll('[data-editor-editable],[contenteditable],[data-editor-dirty],[data-ie-sel],[data-ie-reveal],[data-ie-broken]').forEach(function (el) {
      el.removeAttribute('data-ie-sel');
      el.removeAttribute('data-ie-reveal');
      el.removeAttribute('data-ie-broken');
      el.removeAttribute('data-editor-editable');
      el.removeAttribute('contenteditable');
      el.removeAttribute('data-editor-dirty');
      el.removeAttribute('spellcheck');
    });
    clone.removeAttribute('data-editor-active');
    ['#page-editor-toolbar', '#page-editor-image-upload', '#ie-format', '#ie-config', '#ie-imgs', '#ie-imgs-btn'].forEach(function (s) {
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
          bgs.forEach(function (el) { el._ieOrig = bgUrl(el); el.removeAttribute('data-editor-dirty'); });
          styled.forEach(function (r) { r.touched = false; });
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
