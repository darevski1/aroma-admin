@extends('layouts.master')
@section('title', 'Navigation Builder')
@section('content')
<div class="container-fluid py-2">
  <div class="row">
    <div class="ms-3">
      <h3 class="mb-0 h4 font-weight-bolder">Navigation Builder</h3>
      <p class="mb-4">
        Build and manage the site's main navigation. Link pages, set order, control visibility, and create sub-navigation items.
      </p>
    </div>
  </div>

  @if ($errors->any())
  <div class="alert alert-danger">
    <ul>
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  @if (session('success'))
  <div class="alert alert-success alert-dismissible text-white" role="alert">
    <span class="text-sm"> {{ session('success') }}</span>
    <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif

  <div class="row mt-4 mb-4">
    {{-- Create Main Navigation --}}
    <div class="col-lg-6 mb-4">
      <div class="card h-100">
        <div class="card-header pb-0">
          <h6 class="mb-0">Add Main Navigation Item</h6>
        </div>
        <div class="card-body">
          <form action="{{route('navigation.post')}}" method="POST">
            @csrf
            <div class="input-group input-group-outline my-3">
              <label class="form-label">Navigation Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="input-group input-group-outline my-3">
              <label class="form-label">Slug (auto-generated if empty)</label>
              <input type="text" name="slug" class="form-control">
            </div>
            <button type="submit" class="btn btn-success btn-sm">
              <i class="material-symbols-rounded me-1" style="font-size: 16px;">add</i>
              Add Navigation
            </button>
          </form>
        </div>
      </div>
    </div>

    {{-- Create Sub-Navigation --}}
    <div class="col-lg-6 mb-4">
      <div class="card h-100">
        <div class="card-header pb-0">
          <h6 class="mb-0">Add Sub-Navigation Item</h6>
        </div>
        <div class="card-body">
          <form action="{{route('navigation.submenu.create')}}" method="POST">
            @csrf
            <div class="input-group input-group-outline my-3">
              <select name="navigation_id" class="form-control" required>
                <option value="">Select Parent Navigation</option>
                @foreach ($navs as $item)
                <option value="{{$item->id}}">{{$item->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="input-group input-group-outline my-3">
              <label class="form-label">Sub-Navigation Name</label>
              <input type="text" name="sub_name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-info btn-sm">
              <i class="material-symbols-rounded me-1" style="font-size: 16px;">add</i>
              Add Sub-Navigation
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- Navigation Tree --}}
  <div class="row mt-2">
    <div class="col-md-12 p-0">
      <div class="card">
        <div class="card-header pb-0 px-3 d-flex justify-content-between align-items-center">
          <h6 class="mb-0">Navigation Tree</h6>
          <small class="text-muted">Drag to reorder &bull; Click items to edit &bull; Link pages from the dropdown</small>
        </div>
        <div class="card-body pt-4 p-3">
          <div id="nav-tree">
            {{-- Rendered by JS --}}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Page Link Modal --}}
<div class="modal fade" id="pageLinkModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">Link a Page</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted text-xs mb-3">Select a page to link this navigation item to. The URL will be resolved from the page's settings.</p>
        <div class="input-group input-group-outline mb-3">
          <select id="modalPageSelect" class="form-control">
            <option value="">-- No page (use custom URL) --</option>
          </select>
        </div>
        <div class="input-group input-group-outline mb-3">
          <label class="form-label">Custom URL (used if no page selected)</label>
          <input type="text" id="modalCustomUrl" class="form-control" placeholder="e.g. https://example.com">
        </div>
        <input type="hidden" id="modalLinkType">
        <input type="hidden" id="modalLinkId">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn bg-gradient-dark btn-sm" id="savePageLink">Save Link</button>
      </div>
    </div>
  </div>
</div>

@endsection

<script>
window.addEventListener('DOMContentLoaded', () => {
  // Load all pages for the link modal
  fetch('/pages/all/json')
    .then(r => r.json())
    .then(pages => {
      const select = document.getElementById('modalPageSelect');
      pages.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = p.title + (p.published ? '' : ' (draft)');
        opt.dataset.routeName = p.route_name || '';
        opt.dataset.slug = p.slug || '';
        select.appendChild(opt);
      });
    });

  // Save page link
  document.getElementById('savePageLink').addEventListener('click', () => {
    const type = document.getElementById('modalLinkType').value; // 'nav' or 'sub'
    const id = document.getElementById('modalLinkId').value;
    const pageId = document.getElementById('modalPageSelect').value || null;
    const url = document.getElementById('modalCustomUrl').value || null;

    const endpoint = type === 'nav'
      ? `/navigation/${id}/link-page`
      : `/navigation/submenu/${id}/link-page`;

    fetch(endpoint, {
      method: 'PATCH',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: JSON.stringify({ page_id: pageId, url: url }),
    })
    .then(r => r.json())
    .then(data => {
      bootstrap.Modal.getInstance(document.getElementById('pageLinkModal')).hide();
      Toastify({
        text: data.message || 'Page linked successfully',
        backgroundColor: 'green',
        duration: 3000,
      }).showToast();
      loadNavTree();
    });
  });

  loadNavTree();
});
</script>

<script>
// ─── Navigation Tree Builder ─────────────────────────────────────
async function loadNavTree() {
  const container = document.getElementById('nav-tree');
  if (!container) return;

  try {
    const response = await fetch('/navigation/get');
    if (!response.ok) return;
    const navs = await response.json();

    let html = '<ul class="list-group" id="sortable-nav">';

    for (const nav of navs) {
      const pubBadge = nav.published
        ? '<span class="badge bg-gradient-success ms-2">Published</span>'
        : '<span class="badge bg-gradient-secondary ms-2">Hidden</span>';

      const pageInfo = nav.page_id
        ? '<span class="badge bg-gradient-info ms-1 text-xxs" title="Linked to page">Page linked</span>'
        : '';

      html += `
        <li class="list-group-item border-0 bg-light border-radius-lg mb-2 nav-item-card" data-nav-id="${nav.id}" data-position="${nav.position}">
          <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-2">
            <div class="d-flex align-items-center gap-2 flex-grow-1">
              <i class="material-symbols-rounded text-muted" style="cursor:grab;font-size:18px;" title="Drag to reorder">drag_indicator</i>
              <strong class="text-sm" id="nav-name-${nav.id}">${nav.name}</strong>
              <code class="text-xxs text-muted">${nav.slug}</code>
              ${pageInfo}
              ${pubBadge}
            </div>
            <div class="d-flex align-items-center gap-1">
              <button class="btn btn-sm btn-outline-info" title="Edit name" onclick="toggleEditNav(${nav.id})">
                <i class="material-symbols-rounded" style="font-size:14px;">edit</i>
              </button>
              <button class="btn btn-sm btn-outline-secondary" title="Link page" onclick="openPageLinkModal('nav', ${nav.id})">
                <i class="material-symbols-rounded" style="font-size:14px;">link</i>
              </button>
              <button class="btn btn-sm ${nav.published ? 'btn-outline-warning' : 'btn-outline-success'}" title="${nav.published ? 'Hide from nav' : 'Show in nav'}" onclick="toggleNavPublish(${nav.id})">
                <i class="material-symbols-rounded" style="font-size:14px;">${nav.published ? 'visibility_off' : 'visibility'}</i>
              </button>
              <button class="btn btn-sm btn-outline-danger" title="Delete" onclick="deleteNav(${nav.id})">
                <i class="material-symbols-rounded" style="font-size:14px;">delete</i>
              </button>
            </div>
          </div>

          {{-- Edit form (hidden by default) --}}
          <div id="nav-edit-${nav.id}" style="display:none;" class="px-3 pb-3">
            <div class="input-group input-group-outline mb-2">
              <input type="text" id="nav-name-input-${nav.id}" class="form-control form-control-sm" value="${nav.name}">
            </div>
            <div class="input-group input-group-outline mb-2">
              <input type="text" id="nav-slug-input-${nav.id}" class="form-control form-control-sm" value="${nav.slug}">
            </div>
            <button class="btn btn-sm bg-gradient-dark" onclick="saveNavEdit(${nav.id})">Save</button>
            <button class="btn btn-sm btn-outline-secondary" onclick="toggleEditNav(${nav.id})">Cancel</button>
          </div>

          {{-- Sub-navigation items --}}
          <ul class="list-group ms-4 mt-1" id="subnav-list-${nav.id}">
            <li class="list-group-item border-0 text-muted small py-1">Loading sub-items...</li>
          </ul>
        </li>
      `;
    }

    html += '</ul>';
    container.innerHTML = html;

    // Load sub-navigation for each item
    for (const nav of navs) {
      loadSubNavItems(nav.id);
    }

    // Init sortable if available
    initSortable();
  } catch (err) {
    console.error('Error loading nav tree:', err);
  }
}

async function loadSubNavItems(parentId) {
  const container = document.getElementById(`subnav-list-${parentId}`);
  if (!container) return;

  try {
    const response = await fetch(`/get-submenu/${parentId}`);
    if (!response.ok) return;
    const subs = await response.json();

    if (subs.length === 0) {
      container.innerHTML = '<li class="list-group-item border-0 text-muted text-xxs py-1">No sub-items</li>';
      return;
    }

    let html = '';
    for (const sub of subs) {
      const pubBadge = sub.published
        ? '<span class="badge bg-gradient-success text-xxs">Published</span>'
        : '<span class="badge bg-gradient-secondary text-xxs">Hidden</span>';

      const pageInfo = sub.page_id
        ? '<span class="badge bg-gradient-info text-xxs ms-1">Page linked</span>'
        : '';

      html += `
        <li class="list-group-item border-0 bg-white border-radius-sm mb-1 d-flex align-items-center justify-content-between flex-wrap gap-1 py-2 subnav-item-card" data-subnav-id="${sub.id}" data-position="${sub.position}">
          <div class="d-flex align-items-center gap-2 flex-grow-1">
            <i class="material-symbols-rounded text-muted" style="cursor:grab;font-size:16px;">drag_indicator</i>
            <span class="text-xs" id="sub-name-${sub.id}">${sub.sub_name}</span>
            <code class="text-xxs text-muted">${sub.slug}</code>
            ${pageInfo}
            ${pubBadge}
          </div>
          <div class="d-flex align-items-center gap-1">
            <button class="btn btn-sm btn-outline-secondary" title="Link page" onclick="openPageLinkModal('sub', ${sub.id})">
              <i class="material-symbols-rounded" style="font-size:12px;">link</i>
            </button>
            <button class="btn btn-sm ${sub.published ? 'btn-outline-warning' : 'btn-outline-success'}" title="${sub.published ? 'Hide' : 'Show'}" onclick="toggleSubPublish(${sub.id})">
              <i class="material-symbols-rounded" style="font-size:12px;">${sub.published ? 'visibility_off' : 'visibility'}</i>
            </button>
            <button class="btn btn-sm btn-outline-danger" title="Delete" onclick="deleteSub(${sub.id})">
              <i class="material-symbols-rounded" style="font-size:12px;">delete</i>
            </button>
          </div>
        </li>
      `;
    }

    container.innerHTML = html;
  } catch (err) {
    console.error('Error loading sub-nav:', err);
  }
}

// ─── CRUD Operations ──────────────────────────────────────────────

function toggleEditNav(id) {
  const el = document.getElementById(`nav-edit-${id}`);
  el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

async function saveNavEdit(id) {
  const name = document.getElementById(`nav-name-input-${id}`).value;
  const slug = document.getElementById(`nav-slug-input-${id}`).value;

  await fetch(`/navigation/update/${id}`, {
    method: 'PATCH',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    },
    body: JSON.stringify({ name, slug }),
  });

  Toastify({ text: 'Navigation updated', backgroundColor: 'green', duration: 3000 }).showToast();
  loadNavTree();
}

async function deleteNav(id) {
  if (!confirm('Delete this navigation item and all its sub-items?')) return;

  await fetch(`/navigation/delete/${id}`, {
    method: 'DELETE',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    },
  });

  Toastify({ text: 'Navigation deleted', backgroundColor: 'red', duration: 3000 }).showToast();
  loadNavTree();
}

async function toggleNavPublish(id) {
  const res = await fetch(`/navigation/${id}/toggle-publish`, {
    method: 'PATCH',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    },
  });
  const data = await res.json();
  Toastify({ text: data.message, backgroundColor: 'green', duration: 3000 }).showToast();
  loadNavTree();
}

async function toggleSubPublish(id) {
  await fetch(`/navigation/submenu/update-status/${id}`, {
    method: 'PATCH',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    },
  });
  Toastify({ text: 'Status updated', backgroundColor: 'green', duration: 3000 }).showToast();
  loadNavTree();
}

async function deleteSub(id) {
  if (!confirm('Delete this sub-navigation item?')) return;

  await fetch(`/navigation/submenu/delete/${id}`, {
    method: 'DELETE',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    },
  });

  Toastify({ text: 'Sub-navigation deleted', backgroundColor: 'red', duration: 3000 }).showToast();
  loadNavTree();
}

// ─── Page Link Modal ──────────────────────────────────────────────

function openPageLinkModal(type, id) {
  document.getElementById('modalLinkType').value = type;
  document.getElementById('modalLinkId').value = id;
  document.getElementById('modalPageSelect').value = '';
  document.getElementById('modalCustomUrl').value = '';
  new bootstrap.Modal(document.getElementById('pageLinkModal')).show();
}

// ─── Drag & Drop Sorting ──────────────────────────────────────────

function initSortable() {
  if (typeof Sortable === 'undefined') {
    // Dynamically load SortableJS if not present
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js';
    script.onload = () => setupDragDrop();
    document.head.appendChild(script);
  } else {
    setupDragDrop();
  }
}

function setupDragDrop() {
  const navList = document.getElementById('sortable-nav');
  if (!navList || typeof Sortable === 'undefined') return;

  new Sortable(navList, {
    handle: '.material-symbols-rounded',
    animation: 200,
    ghostClass: 'bg-gray-200',
    onEnd: async function (evt) {
      const items = navList.querySelectorAll('.nav-item-card');
      for (let i = 0; i < items.length; i++) {
        const navId = items[i].dataset.navId;
        const newPos = i;
        if (parseInt(items[i].dataset.position) !== newPos) {
          await fetch(`/navigation/${navId}/position`, {
            method: 'PATCH',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ position: newPos }),
          });
          items[i].dataset.position = newPos;
        }
      }
      Toastify({ text: 'Navigation order saved', backgroundColor: 'green', duration: 2000 }).showToast();
    },
  });

  // Also init sub-nav sortable lists
  document.querySelectorAll('[id^="subnav-list-"]').forEach(ul => {
    new Sortable(ul, {
      handle: '.material-symbols-rounded',
      animation: 200,
      group: 'subnav',
      onEnd: async function (evt) {
        const items = ul.querySelectorAll('.subnav-item-card');
        for (let i = 0; i < items.length; i++) {
          const subId = items[i].dataset.subnavId;
          const newPos = i;
          await fetch(`/navigation/submenu/${subId}/position`, {
            method: 'PATCH',
            headers: {
              'Accept': 'application/json',
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({ position: newPos }),
          });
          items[i].dataset.position = newPos;
        }
        Toastify({ text: 'Sub-navigation order saved', backgroundColor: 'green', duration: 2000 }).showToast();
      },
    });
  });
}

// Make functions globally accessible
window.loadNavTree = loadNavTree;
window.toggleEditNav = toggleEditNav;
window.saveNavEdit = saveNavEdit;
window.deleteNav = deleteNav;
window.toggleNavPublish = toggleNavPublish;
window.toggleSubPublish = toggleSubPublish;
window.deleteSub = deleteSub;
window.openPageLinkModal = openPageLinkModal;
</script>
