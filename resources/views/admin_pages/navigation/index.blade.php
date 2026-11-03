@extends('layouts.master')
@section('title', 'Navigation Builder')
@section('content')
@php
  $activePages = $pages->where('published', true)->values()->map(fn ($page) => ['id' => $page->id, 'title' => $page->title]);
@endphp
<style>
  .nav-tree, .nav-children { list-style: none; margin: 0; padding: 0; }
  .nav-children { margin-left: 1.75rem; min-height: 10px; padding-top: 2px; border-left: 2px dashed #d9dde3; padding-left: .5rem; }
  .nav-node { margin-bottom: .5rem; }
  .nav-node__bar { display: flex; align-items: center; gap: .5rem; padding: .5rem .75rem; background: #fff; border: 1px solid #d9dde3; border-radius: .5rem; }
  .nav-node.is-inactive > .nav-node__bar { background: #f4f5f7; opacity: .75; }
  .nav-node__title { flex: 1 1 auto; font-weight: 600; font-size: .85rem; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .nav-handle { cursor: grab; color: #8a94a3; font-size: 20px; user-select: none; }
  .nav-node__panel { padding: .75rem; margin: 0 .5rem; background: #f8f9fb; border: 1px solid #d9dde3; border-top: 0; border-radius: 0 0 .5rem .5rem; }
  .nav-node__panel label { font-size: .72rem; font-weight: 700; margin-bottom: .15rem; }
  .nav-ghost > .nav-node__bar { background: #e8eefc; border-style: dashed; }
  .nav-dirty { color: #b45309; font-weight: 600; font-size: .8rem; }
</style>

<div class="container-fluid py-2">
  <div class="row">
    <div class="ms-3">
      <h3 class="mb-0 h4 font-weight-bolder">Navigation Builder</h3>
      <p class="mb-4">
        Add menu items, then drag them to reorder or nest them. Edit an item by opening its panel. Press <strong>Save Menu</strong> to publish your changes to the site.
      </p>
    </div>
  </div>

  <div id="nav-alerts"></div>

  <div class="row mt-2 mb-4">
    {{-- Add item --}}
    <div class="col-lg-4 mb-4">
      <div class="card h-100">
        <div class="card-header pb-0"><h6 class="mb-0">Add Menu Item</h6></div>
        <div class="card-body">
          <form id="nav-add-form" novalidate>
            <div class="mb-3">
              <label class="form-label text-xs font-weight-bolder">Title <span class="text-muted">(optional when a page is selected)</span></label>
              <input type="text" name="title" class="form-control border px-2">
            </div>
            <div class="mb-3">
              <label class="form-label text-xs font-weight-bolder">Linked page</label>
              <select name="page_id" class="form-control border px-2">
                <option value="">— Custom URL —</option>
                @foreach ($activePages as $page)
                  <option value="{{ $page['id'] }}">{{ $page['title'] }}</option>
                @endforeach
              </select>
              <div class="form-text text-xs">Only active pages are listed.</div>
            </div>
            <div class="mb-3">
              <label class="form-label text-xs font-weight-bolder">Custom URL</label>
              <input type="text" name="url" class="form-control border px-2" placeholder="https://… or /path">
            </div>
            <div class="mb-3">
              <label class="form-label text-xs font-weight-bolder">Slug <span class="text-muted">(auto if empty)</span></label>
              <input type="text" name="slug" class="form-control border px-2">
            </div>
            <div class="mb-3">
              <label class="form-label text-xs font-weight-bolder">Parent</label>
              <select name="parent_id" class="form-control border px-2" id="nav-add-parent"></select>
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" name="published" id="nav-add-published" checked>
              <label class="form-check-label text-sm" for="nav-add-published">Active</label>
            </div>
            <div id="nav-add-errors" class="text-danger text-xs mb-2"></div>
            <button type="submit" class="btn btn-success btn-sm mb-0">
              <i class="material-symbols-rounded me-1" style="font-size: 16px;">add</i> Add to menu
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- Navigation Tree --}}
    <div class="col-lg-8 mb-4">
      <div class="card">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
          <h6 class="mb-0">Navigation Tree</h6>
          <div class="d-flex align-items-center gap-3">
            <span id="nav-dirty" class="nav-dirty d-none">Unsaved changes</span>
            <button type="button" class="btn bg-gradient-dark btn-sm mb-0 nav-save">Save Menu</button>
          </div>
        </div>
        <div class="card-body">
          <p class="text-xs text-muted">Drag the <i class="material-symbols-rounded align-middle" style="font-size:14px;">drag_indicator</i> handle to reorder. Drop an item onto the dashed area under another item to nest it.</p>
          <ul id="nav-tree" class="nav-tree"></ul>
          <div id="nav-empty" class="text-muted text-sm py-3 d-none">No menu items yet. Add one on the left.</div>
          <div class="mt-3"><button type="button" class="btn bg-gradient-dark btn-sm mb-0 nav-save">Save Menu</button></div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
<script>
(() => {
  const ITEMS = @json($items);
  const PAGES = @json($activePages);
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  const tree = document.getElementById('nav-tree');
  const deleted = [];
  let dirty = false;

  const esc = (v) => String(v ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const api = async (url, method, body) => {
    const res = await fetch(url, {
      method,
      headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
      body: body ? JSON.stringify(body) : undefined,
    });
    const data = await res.json().catch(() => ({}));
    return { ok: res.ok, status: res.status, data };
  };
  const flatErrors = (data) => data.errors ? Object.values(data.errors).flat() : [data.message || 'Something went wrong.'];

  function alertBox(type, messages) {
    const box = document.getElementById('nav-alerts');
    box.innerHTML = `<div class="alert alert-${type} text-white alert-dismissible" role="alert"><ul class="mb-0 ps-3">${[].concat(messages).map(m => `<li class="text-sm">${esc(m)}</li>`).join('')}</ul><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function setDirty(value) {
    dirty = value;
    document.getElementById('nav-dirty').classList.toggle('d-none', !value);
  }
  window.addEventListener('beforeunload', (e) => { if (dirty) { e.preventDefault(); e.returnValue = ''; } });

  // ── Tree helpers ──────────────────────────────────────────
  const nodes = () => [...tree.querySelectorAll('.nav-node')];
  const childList = (li) => li.querySelector(':scope > .nav-children');
  const parentNode = (li) => li.parentElement.closest('.nav-node');
  const depthOf = (li) => { let d = 0; for (let p = parentNode(li); p; p = parentNode(p)) d++; return d; };
  const field = (li, name) => li.querySelector(`:scope > .nav-node__panel [data-f="${name}"]`);

  function pageOptions(selectedId, selectedTitle) {
    let html = '<option value="">— Custom URL —</option>';
    let found = false;
    PAGES.forEach(p => { if (Number(p.id) === Number(selectedId)) found = true; html += `<option value="${p.id}" ${Number(p.id) === Number(selectedId) ? 'selected' : ''}>${esc(p.title)}</option>`; });
    if (selectedId && !found) html += `<option value="${selectedId}" selected>${esc(selectedTitle || 'Page #' + selectedId)} (inactive)</option>`;
    return html;
  }

  function parentOptions(excludeLi, selectedId, rootLabel) {
    const banned = new Set();
    if (excludeLi) { banned.add(excludeLi); excludeLi.querySelectorAll('.nav-node').forEach(n => banned.add(n)); }
    let html = `<option value="">${rootLabel}</option>`;
    nodes().filter(n => !banned.has(n)).forEach(n => {
      html += `<option value="${n.dataset.id}" ${String(n.dataset.id) === String(selectedId ?? '') ? 'selected' : ''}>${'— '.repeat(depthOf(n) + 1)}${esc(field(n, 'title').value || 'Untitled')}</option>`;
    });
    return html;
  }

  function refreshAddParent() {
    const select = document.getElementById('nav-add-parent');
    const current = select.value;
    select.innerHTML = parentOptions(null, current, '— Top level —');
  }

  function syncPositions() {
    [tree, ...tree.querySelectorAll('.nav-children')].forEach(list => {
      [...list.children].forEach((li, i) => { const f = field(li, 'position'); if (f) f.value = i + 1; });
    });
    document.getElementById('nav-empty').classList.toggle('d-none', tree.children.length > 0);
    refreshAddParent();
  }

  function refreshOpenPanels() {
    nodes().forEach(li => {
      const panel = li.querySelector(':scope > .nav-node__panel');
      if (panel.hidden) return;
      const parent = parentNode(li);
      field(li, 'parent_id').innerHTML = parentOptions(li, parent?.dataset.id, '— Top level —');
    });
  }

  function structureChanged() { syncPositions(); refreshOpenPanels(); setDirty(true); }

  function updateBar(li) {
    const title = field(li, 'title').value.trim();
    const pageSel = field(li, 'page_id');
    const pageText = pageSel.value ? pageSel.options[pageSel.selectedIndex].text : '';
    const published = field(li, 'published').checked;
    const bar = li.querySelector(':scope > .nav-node__bar');
    li.classList.toggle('is-inactive', !published);
    bar.querySelector('.nav-node__title').textContent = title || pageText || 'Untitled';
    bar.querySelector('.nav-node__meta').innerHTML =
      `<code class="text-xxs">${esc(field(li, 'slug').value)}</code>` +
      (pageSel.value ? ` <span class="badge bg-gradient-info text-xxs">${esc(pageText)}</span>` : '') +
      (li.dataset.pageActive === '0' ? ' <span class="badge bg-gradient-warning text-xxs">Page inactive</span>' : '') +
      (!published ? ' <span class="badge bg-gradient-secondary text-xxs">Inactive</span>' : '');
    const toggle = bar.querySelector('.nav-toggle-active i');
    toggle.textContent = published ? 'toggle_on' : 'toggle_off';
    field(li, 'url').disabled = !!pageSel.value;
  }

  function makeSortable(list) {
    new Sortable(list, {
      group: 'nav', handle: '.nav-handle', animation: 150, forceFallback: true, fallbackOnBody: true, swapThreshold: 0.65,
      ghostClass: 'nav-ghost', onEnd: structureChanged,
    });
  }

  function createNode(item) {
    const li = document.createElement('li');
    li.className = 'nav-node';
    li.dataset.id = item.id;
    li.dataset.pageActive = item.page_id ? (item.page_active ? '1' : '0') : '';
    li.innerHTML = `
      <div class="nav-node__bar">
        <i class="material-symbols-rounded nav-handle" title="Drag to move">drag_indicator</i>
        <span class="nav-node__title"></span>
        <span class="nav-node__meta"></span>
        <button type="button" class="btn btn-link p-0 mb-0 nav-toggle-active" title="Activate / deactivate"><i class="material-symbols-rounded" style="font-size:28px;">toggle_on</i></button>
        <button type="button" class="btn btn-sm btn-outline-secondary mb-0 px-2 py-1 nav-edit" title="Edit"><i class="material-symbols-rounded" style="font-size:16px;">expand_more</i></button>
      </div>
      <div class="nav-node__panel" hidden>
        <div class="row g-2">
          <div class="col-md-6"><label>Title</label><input type="text" class="form-control form-control-sm border px-2" data-f="title" value="${esc(item.title)}"></div>
          <div class="col-md-6"><label>Slug</label><input type="text" class="form-control form-control-sm border px-2" data-f="slug" value="${esc(item.slug)}"></div>
          <div class="col-md-6"><label>Linked page</label><select class="form-control form-control-sm border px-2" data-f="page_id">${pageOptions(item.page_id, item.page_title)}</select></div>
          <div class="col-md-6"><label>Custom URL</label><input type="text" class="form-control form-control-sm border px-2" data-f="url" value="${esc(item.url || '')}" placeholder="https://… or /path"></div>
          <div class="col-md-6"><label>Parent</label><select class="form-control form-control-sm border px-2" data-f="parent_id"></select></div>
          <div class="col-md-3"><label>Position</label><input type="number" min="1" class="form-control form-control-sm border px-2" data-f="position" value="1"></div>
          <div class="col-md-3 d-flex align-items-end"><div class="form-check"><input type="checkbox" class="form-check-input" data-f="published" ${item.published ? 'checked' : ''}><label class="form-check-label">Active</label></div></div>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger mt-3 mb-0 nav-remove">Remove</button>
      </div>
      <ul class="nav-children"></ul>`;
    makeSortable(li.querySelector('.nav-children'));
    return li;
  }

  function attach(li, parentId, index) {
    const list = parentId ? childList(tree.querySelector(`.nav-node[data-id="${parentId}"]`)) : tree;
    const ref = list.children[index] || null;
    list.insertBefore(li, ref);
  }

  // ── Events (delegated) ────────────────────────────────────
  tree.addEventListener('click', (e) => {
    const li = e.target.closest('.nav-node');
    if (!li) return;
    if (e.target.closest('.nav-toggle-active')) {
      const cb = field(li, 'published'); cb.checked = !cb.checked; updateBar(li); setDirty(true);
    } else if (e.target.closest('.nav-edit')) {
      const panel = li.querySelector(':scope > .nav-node__panel');
      panel.hidden = !panel.hidden;
      li.querySelector(':scope > .nav-node__bar .nav-edit i').textContent = panel.hidden ? 'expand_more' : 'expand_less';
      if (!panel.hidden) refreshOpenPanels();
    } else if (e.target.closest('.nav-remove')) {
      if (!confirm('Remove this item? Its sub-items move up one level. The change applies when you save.')) return;
      const list = li.parentElement;
      [...childList(li).children].forEach(child => list.insertBefore(child, li));
      deleted.push(Number(li.dataset.id));
      li.remove();
      structureChanged();
    }
  });

  tree.addEventListener('input', (e) => {
    const li = e.target.closest('.nav-node');
    if (!li || !e.target.dataset.f) return;
    if (e.target.dataset.f === 'page_id' && e.target.value) {
      li.dataset.pageActive = '1';
      if (!field(li, 'title').value.trim()) field(li, 'title').value = e.target.options[e.target.selectedIndex].text;
    }
    updateBar(li); setDirty(true);
  });

  tree.addEventListener('change', (e) => {
    const li = e.target.closest('.nav-node');
    const f = e.target.dataset?.f;
    if (!li) return;
    if (f === 'parent_id') {
      const list = e.target.value ? childList(tree.querySelector(`.nav-node[data-id="${e.target.value}"]`)) : tree;
      list.appendChild(li); structureChanged();
    } else if (f === 'position') {
      const list = li.parentElement;
      const index = Math.min(Math.max(parseInt(e.target.value, 10) || 1, 1), list.children.length) - 1;
      list.insertBefore(li, [...list.children].filter(c => c !== li)[index] || null);
      structureChanged();
    }
  });

  // ── Save Menu ─────────────────────────────────────────────
  function collect() {
    const items = [];
    const walk = (list, parentId) => [...list.children].forEach((li, position) => {
      const pageId = field(li, 'page_id').value;
      items.push({
        id: Number(li.dataset.id), parent_id: parentId, position,
        title: field(li, 'title').value.trim() || field(li, 'page_id').selectedOptions[0]?.text.replace(/ \(inactive\)$/, '') || '',
        slug: field(li, 'slug').value.trim(),
        page_id: pageId ? Number(pageId) : null,
        url: pageId ? null : (field(li, 'url').value.trim() || null),
        published: field(li, 'published').checked,
      });
      walk(childList(li), Number(li.dataset.id));
    });
    walk(tree, null);
    return items;
  }

  document.querySelectorAll('.nav-save').forEach(btn => btn.addEventListener('click', async () => {
    document.querySelectorAll('.nav-save').forEach(b => b.disabled = true);
    const { ok, data } = await api('{{ route('navigation.save') }}', 'PUT', { items: collect(), deleted });
    document.querySelectorAll('.nav-save').forEach(b => b.disabled = false);
    if (!ok) return alertBox('danger', flatErrors(data));
    deleted.length = 0;
    render(data.items);
    setDirty(false);
    alertBox('success', 'Menu saved. The site navigation is updated.');
  }));

  // ── Add item ──────────────────────────────────────────────
  const form = document.getElementById('nav-add-form');
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const fd = new FormData(form);
    const body = {
      title: fd.get('title') || null, slug: fd.get('slug') || null,
      page_id: fd.get('page_id') || null, url: fd.get('page_id') ? null : (fd.get('url') || null),
      parent_id: fd.get('parent_id') || null, published: form.published.checked,
    };
    const { ok, data } = await api('{{ route('navigation.store') }}', 'POST', body);
    const errBox = document.getElementById('nav-add-errors');
    if (!ok) { errBox.innerHTML = flatErrors(data).map(esc).join('<br>'); return; }
    errBox.innerHTML = '';
    const li = createNode(data.item);
    attach(li, data.item.parent_id, Infinity);
    updateBar(li);
    form.reset(); form.published.checked = true;
    syncPositions(); refreshOpenPanels();
    alertBox('success', 'Item added to the menu.');
  });
  form.page_id.addEventListener('change', () => { form.url.disabled = !!form.page_id.value; });

  // ── Initial render ────────────────────────────────────────
  function render(items) {
    tree.innerHTML = '';
    const byParent = {};
    items.forEach(i => (byParent[i.parent_id ?? 0] ||= []).push(i));
    const build = (parentKey, list) => (byParent[parentKey] || [])
      .sort((a, b) => a.position - b.position || a.id - b.id)
      .forEach(item => { const li = createNode(item); list.appendChild(li); build(item.id, childList(li)); });
    build(0, tree);
    nodes().forEach(updateBar);
    makeSortable(tree);
    syncPositions();
  }
  render(ITEMS);
})();
</script>
@endpush
