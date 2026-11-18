@extends('layouts.master')

@section('title', 'Site Images')

@section('content')
<div class="container-fluid py-2">
    <div class="d-flex justify-content-between align-items-start flex-wrap ms-3 me-3">
        <div>
            <h3 class="mb-0 h4 font-weight-bolder">Site Images</h3>
            <p class="mb-3">Every image shown on the public website, page by page. Replace any of them from the Media Library — no code needed. The original image comes back with one click.</p>
        </div>
        <form method="POST" action="{{ route('site-images.scan') }}" class="mb-3">
            @csrf
            <button class="btn btn-sm btn-outline-secondary mb-0" title="Visit every public page and refresh this list">
                <i class="material-symbols-rounded me-1" style="font-size:16px;">refresh</i> Rescan website
            </button>
        </form>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible text-white" role="alert"><span class="text-sm">{{ session('success') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible text-white" role="alert"><span class="text-sm">{{ session('error') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
    @endif

    <div class="row mb-3">
        @foreach ([['Pages', $stats['pages']], ['Image slots', $stats['images']], ['Unique files', $stats['unique']], ['Replaced', $stats['replaced']]] as [$label, $value])
            <div class="col-6 col-md-3 mb-2"><div class="card"><div class="card-body py-2">
                <div class="text-xs text-uppercase text-secondary font-weight-bolder">{{ $label }}</div>
                <div class="h5 mb-0 font-weight-bolder">{{ $value }}</div>
            </div></div></div>
        @endforeach
    </div>

    <div class="card mb-3"><div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-4"><div class="input-group input-group-outline">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search file, alt text or section…"></div></div>
            <div class="col-md-3"><select name="path" class="form-select">
                <option value="">All pages</option>
                @foreach ($pages as $p)
                    <option value="{{ $p->path }}" @selected(request('path') === $p->path)>{{ $p->path }}</option>
                @endforeach
            </select></div>
            <div class="col-md-3"><select name="state" class="form-select">
                <option value="">All images</option>
                <option value="replaced" @selected(request('state') === 'replaced')>Replaced by admin</option>
                <option value="original" @selected(request('state') === 'original')>Still original</option>
                <option value="shared" @selected(request('state') === 'shared')>Used on several pages</option>
            </select></div>
            <div class="col-md-2"><button class="btn btn-sm bg-gradient-dark mb-0 w-100">Filter</button></div>
        </form>
    </div></div>

    @forelse ($groups as $path => $slots)
        <div class="card mb-3">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h6 class="mb-0">{{ $slots->first()->page_title ?: $path }} <span class="text-secondary text-xs ms-1">{{ $path }}</span></h6>
                    <span class="text-xs text-secondary">{{ $slots->count() }} images</span>
                </div>
                <a href="{{ url($path) }}" target="_blank" class="btn btn-sm btn-outline-secondary mb-0">View page</a>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach ($slots as $slot)
                        @php
                            $o = $slot->override;
                            $payload = [
                                'path' => $slot->path, 'src_key' => $slot->src_key, 'kind' => $slot->kind, 'orig_alt' => $slot->alt,
                                'shared' => $slot->shared_count,
                                'override' => $o ? [
                                    'scope' => $o->isGlobal() ? 'global' : 'page',
                                    'media' => $o->media ? ['id' => $o->media->id, 'url' => \App\Services\SiteImages\MediaOptimizer::publicUrl($o->media), 'name' => $o->media->name] : null,
                                    'mobile' => $o->mobileMedia ? ['id' => $o->mobileMedia->id, 'url' => \App\Services\SiteImages\MediaOptimizer::publicUrl($o->mobileMedia), 'name' => $o->mobileMedia->name] : null,
                                    'alt' => $o->alt, 'title' => $o->title, 'object_position' => $o->object_position,
                                    'object_fit' => $o->object_fit, 'hidden' => $o->hidden,
                                ] : null,
                            ];
                        @endphp
                        <div class="col-6 col-md-4 col-xl-3">
                            <div class="border rounded h-100 d-flex flex-column site-img-card">
                                <div style="aspect-ratio:4/3;background:#f0f2f5 repeating-conic-gradient(#e6e8ec 0% 25%, transparent 0% 50%) 50%/16px 16px;" class="d-flex align-items-center justify-content-center overflow-hidden rounded-top">
                                    @if ($o && $o->hidden)
                                        <span class="text-xs text-secondary">Hidden</span>
                                    @else
                                        <img src="{{ $slot->effective }}" alt="" loading="lazy" style="max-width:100%;max-height:100%;object-fit:contain;">
                                    @endif
                                </div>
                                <div class="p-2 flex-grow-1">
                                    <div class="d-flex flex-wrap gap-1 mb-1">
                                        <span class="badge bg-gradient-{{ $slot->kind === 'img' ? 'info' : 'secondary' }}">{{ $slot->kind === 'img' ? 'Image' : 'Background' }}</span>
                                        @if ($o)<span class="badge bg-gradient-success">Replaced{{ $o->isGlobal() ? ' · all pages' : '' }}</span>@endif
                                        @if ($slot->shared_count > 1)<span class="badge bg-gradient-warning" title="Same file appears on {{ $slot->shared_count }} pages">Shared · {{ $slot->shared_count }} pages</span>@endif
                                    </div>
                                    @if ($slot->context)<div class="text-xs text-dark font-weight-bold text-truncate" title="{{ $slot->context }}">{{ $slot->context }}</div>@endif
                                    <div class="text-xxs text-secondary text-break">{{ basename($slot->src_key) }}</div>
                                    @if ($slot->alt)<div class="text-xxs text-secondary text-truncate" title="{{ $slot->alt }}">alt: {{ $slot->alt }}</div>@endif
                                </div>
                                <div class="p-2 pt-0">
                                    <button type="button" class="btn btn-sm bg-gradient-dark w-100 mb-0 si-edit" data-slot='@json($payload)'>
                                        {{ $o ? 'Edit / replace' : 'Replace image' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @empty
        <div class="card"><div class="card-body text-center py-5">
            <p class="mb-2">No images recorded yet.</p>
            <form method="POST" action="{{ route('site-images.scan') }}">@csrf<button class="btn bg-gradient-dark">Scan the website</button></form>
        </div></div>
    @endforelse
</div>

{{-- Replace / edit modal --}}
<div class="modal fade" id="siModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content">
    <div class="modal-header pb-0 border-bottom-0">
      <div><h5 class="modal-title font-weight-bolder" id="siTitle">Replace image</h5>
        <div class="text-xs text-secondary" id="siSub"></div></div>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body pt-2">
      <div class="row">
        <div class="col-lg-7">
          <div class="row g-2 mb-3">
            <div class="col-6">
              <div class="text-xs font-weight-bolder mb-1">Original</div>
              <div class="border rounded d-flex align-items-center justify-content-center" style="height:130px;background:#f0f2f5;"><img id="siOrig" src="" alt="" style="max-width:100%;max-height:100%;object-fit:contain;"></div>
            </div>
            <div class="col-6">
              <div class="text-xs font-weight-bolder mb-1">Replacement (desktop)</div>
              <div class="border rounded d-flex align-items-center justify-content-center" style="height:130px;background:#f0f2f5;"><img id="siNew" src="" alt="" style="max-width:100%;max-height:100%;object-fit:contain;display:none;"><span id="siNewNone" class="text-xs text-secondary">None — original is shown</span></div>
            </div>
          </div>
          <div class="d-flex gap-2 mb-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-outline-dark mb-0" id="siPickDesktop">Choose desktop image</button>
            <button type="button" class="btn btn-sm btn-outline-dark mb-0" id="siPickMobile">Choose mobile image</button>
            <button type="button" class="btn btn-sm btn-outline-secondary mb-0 d-none" id="siClearMobile">Remove mobile image</button>
            <span class="text-xs text-secondary align-self-center" id="siMobileName"></span>
          </div>
          <div class="border rounded p-2" id="siLibrary" style="display:none;">
            <div class="d-flex gap-2 align-items-center mb-2">
              <div class="input-group input-group-outline flex-grow-1"><input type="search" id="siSearch" class="form-control" placeholder="Search the Media Library…"></div>
              <label class="btn btn-sm bg-gradient-dark text-nowrap mb-0"><i class="material-symbols-rounded me-1" style="font-size:16px;">upload</i> Upload new
                <input type="file" id="siUpload" accept="image/jpeg,image/png,image/webp,image/avif,image/gif,image/svg+xml" hidden></label>
            </div>
            <div class="text-xs mb-1" id="siLibTitle"></div>
            <div id="siStatus" class="text-xs text-muted mb-1"></div>
            <div id="siGrid" class="row g-2" style="max-height:300px;overflow:auto;"></div>
            <div class="text-center mt-2"><button type="button" id="siMore" class="btn btn-sm btn-outline-secondary d-none">Load more</button></div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="mb-2"><label class="text-xs font-weight-bolder mb-1 text-dark">Alt text <span class="text-secondary">(SEO &amp; accessibility)</span></label>
            <div class="input-group input-group-outline"><input type="text" id="siAlt" class="form-control" maxlength="500"></div>
            <div class="text-xxs text-secondary mt-1" id="siOrigAlt"></div></div>
          <div class="mb-2"><label class="text-xs font-weight-bolder mb-1 text-dark">Title</label>
            <div class="input-group input-group-outline"><input type="text" id="siImgTitle" class="form-control" maxlength="255"></div></div>
          <div class="row g-2 mb-2">
            <div class="col-6"><label class="text-xs font-weight-bolder mb-1 text-dark">Position</label>
              <select id="siPos" class="form-select"><option value="">Unchanged</option>@foreach ($positions as $p)<option value="{{ $p }}">{{ ucfirst($p) }}</option>@endforeach</select></div>
            <div class="col-6"><label class="text-xs font-weight-bolder mb-1 text-dark">Fit</label>
              <select id="siFit" class="form-select"><option value="">Unchanged</option>@foreach ($fits as $f)<option value="{{ $f }}">{{ ucfirst($f) }}</option>@endforeach</select></div>
          </div>
          <div class="form-check mb-3"><input class="form-check-input" type="checkbox" id="siHidden"><label class="form-check-label text-sm" for="siHidden">Hide this image</label></div>
          <div class="border rounded p-2 mb-2">
            <div class="text-xs font-weight-bolder mb-1">Where should the change apply?</div>
            <div class="form-check"><input class="form-check-input" type="radio" name="siScope" id="siScopePage" value="page" checked><label class="form-check-label text-sm" for="siScopePage">Only on this page <code id="siPath"></code></label></div>
            <div class="form-check" id="siScopeGlobalWrap"><input class="form-check-input" type="radio" name="siScope" id="siScopeGlobal" value="global"><label class="form-check-label text-sm" for="siScopeGlobal">Everywhere this image is used <span id="siSharedNote" class="text-secondary"></span></label></div>
          </div>
          <div id="siError" class="text-danger text-xs d-none"></div>
        </div>
      </div>
    </div>
    <div class="modal-footer justify-content-between">
      <button type="button" class="btn btn-outline-danger btn-sm mb-0 d-none" id="siReset">Restore original</button>
      <div><button type="button" class="btn btn-outline-secondary btn-sm mb-0" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="siSave" class="btn btn-sm bg-gradient-dark mb-0">Save &amp; publish</button></div>
    </div>
  </div></div>
</div>
@endsection

@push('scripts')
<script>
(function () {
  const csrf = '{{ csrf_token() }}';
  const urls = { picker: '{{ route('media.picker') }}', upload: '{{ route('media.picker.upload') }}', save: '{{ route('site-images.save') }}', reset: '{{ route('site-images.reset') }}' };
  const $ = id => document.getElementById(id);
  const esc = v => { const d = document.createElement('div'); d.textContent = v == null ? '' : String(v); return d.innerHTML; };
  let modal, slot, state, target = 'desktop', page = 1, last = 1, token = 0, items = {}, timer;

  function err(m) { $('siError').textContent = m || ''; $('siError').classList.toggle('d-none', !m); }

  function render() {
    $('siNew').style.display = state.media ? '' : 'none';
    $('siNewNone').style.display = state.media ? 'none' : '';
    if (state.media) $('siNew').src = state.media.url;
    $('siMobileName').textContent = state.mobile ? 'Mobile: ' + state.mobile.name : '';
    $('siClearMobile').classList.toggle('d-none', !state.mobile);
  }

  document.querySelectorAll('.si-edit').forEach(b => b.addEventListener('click', function () {
    slot = JSON.parse(this.dataset.slot);
    const o = slot.override || {};
    state = { media: o.media || null, mobile: o.mobile || null };
    $('siTitle').textContent = slot.kind === 'img' ? 'Replace image' : 'Replace background image';
    $('siSub').textContent = slot.path + ' · ' + slot.src_key;
    $('siOrig').src = slot.src_key;
    $('siAlt').value = o.alt ?? slot.orig_alt ?? '';
    $('siOrigAlt').textContent = slot.orig_alt ? 'Original alt: ' + slot.orig_alt : '';
    $('siImgTitle').value = o.title || '';
    $('siPos').value = o.object_position || '';
    $('siFit').value = o.object_fit || '';
    $('siHidden').checked = !!o.hidden;
    $('siPath').textContent = slot.path;
    $('siScopeGlobalWrap').style.display = slot.shared > 1 || o.scope === 'global' ? '' : 'none';
    $('siSharedNote').textContent = '(' + slot.shared + ' pages)';
    (o.scope === 'global' ? $('siScopeGlobal') : $('siScopePage')).checked = true;
    $('siReset').classList.toggle('d-none', !slot.override);
    $('siLibrary').style.display = 'none';
    err('');
    render();
    modal = modal || bootstrap.Modal.getOrCreateInstance($('siModal'));
    modal.show();
  }));

  function openLibrary(which) {
    target = which;
    $('siLibTitle').textContent = which === 'mobile' ? 'Pick the image for phones (shown below 768px wide)' : 'Pick the replacement image';
    $('siLibrary').style.display = '';
    $('siSearch').value = '';
    load(true);
  }
  $('siPickDesktop').onclick = () => openLibrary('desktop');
  $('siPickMobile').onclick = () => openLibrary('mobile');
  $('siClearMobile').onclick = () => { state.mobile = null; render(); };

  function tile(i) {
    return '<div class="col-4 col-md-3" data-id="' + i.id + '"><div class="border rounded p-1 h-100" style="cursor:pointer;">' +
      '<div style="aspect-ratio:1;overflow:hidden;background:#f0f0f0;" class="rounded"><img src="' + esc(i.thumb_url || i.url) + '" loading="lazy" alt="" style="width:100%;height:100%;object-fit:cover;"></div>' +
      '<div class="text-xxs text-truncate mt-1">' + esc(i.name) + '</div>' +
      '<div class="text-xxs text-secondary">' + (i.width ? i.width + '×' + i.height + ' · ' : '') + Math.round((i.size || 0) / 1024) + ' KB</div></div></div>';
  }
  function load(reset) {
    const t = ++token;
    if (reset) { page = 1; $('siGrid').innerHTML = ''; items = {}; }
    $('siStatus').textContent = 'Loading…';
    const q = new URLSearchParams({ mime_type: 'image/', per_page: 24, page, search: $('siSearch').value.trim() });
    fetch(urls.picker + '?' + q, { headers: { Accept: 'application/json' } }).then(r => r.json()).then(d => {
      if (t !== token) return;
      last = d.last_page || 1;
      (d.data || []).forEach(i => items[i.id] = i);
      $('siGrid').insertAdjacentHTML('beforeend', (d.data || []).map(tile).join(''));
      $('siMore').classList.toggle('d-none', page >= last);
      $('siStatus').textContent = d.total ? '' : 'No images found.';
    }).catch(() => { $('siStatus').textContent = 'Could not load the Media Library.'; });
  }
  function choose(i) {
    const m = { id: i.id, url: new URL(i.url, location.origin).pathname, name: i.name };
    if (target === 'mobile') state.mobile = m; else state.media = m;
    if (target === 'desktop' && !$('siAlt').value) $('siAlt').value = (i.custom_properties || {}).alt_text || '';
    render();
  }
  $('siGrid').addEventListener('click', e => { const c = e.target.closest('[data-id]'); if (c) choose(items[c.dataset.id]); });
  $('siMore').onclick = () => { page++; load(false); };
  $('siSearch').addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(() => load(true), 300); });
  $('siUpload').addEventListener('change', function () {
    const f = this.files[0]; const input = this; if (!f) return;
    const body = new FormData(); body.append('file', f); body.append('collection', 'site-images');
    $('siStatus').textContent = 'Uploading…'; err('');
    fetch(urls.upload, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' }, body })
      .then(r => r.json().then(d => { if (!r.ok || !d.id) throw new Error((d.errors && Object.values(d.errors)[0][0]) || d.message || 'Upload failed.'); return d; }))
      .then(i => { items[i.id] = i; $('siGrid').insertAdjacentHTML('afterbegin', tile(i)); $('siStatus').textContent = ''; choose(i); })
      .catch(e => { $('siStatus').textContent = ''; err(e.message); })
      .finally(() => input.value = '');
  });

  function post(url, payload) {
    return fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
      .then(r => r.json().then(d => { if (!r.ok) throw new Error((d.errors && Object.values(d.errors)[0][0]) || d.message || 'Request failed.'); return d; }));
  }
  const scope = () => document.querySelector('input[name=siScope]:checked').value;

  $('siSave').onclick = function () {
    err('');
    this.disabled = true;
    post(urls.save, {
      scope: scope(), path: slot.path, src_key: slot.src_key,
      media_id: state.media ? state.media.id : null, mobile_media_id: state.mobile ? state.mobile.id : null,
      alt: $('siAlt').value, title: $('siImgTitle').value,
      object_position: $('siPos').value || null, object_fit: $('siFit').value || null, hidden: $('siHidden').checked ? 1 : 0,
    }).then(() => location.reload()).catch(e => { err(e.message); this.disabled = false; });
  };
  $('siReset').onclick = function () {
    if (!confirm('Restore the original image?')) return;
    post(urls.reset, { scope: slot.override.scope, path: slot.path, src_key: slot.src_key }).then(() => location.reload()).catch(e => err(e.message));
  };
})();
</script>
@endpush
