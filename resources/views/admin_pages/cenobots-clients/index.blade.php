@extends('layouts.master')

@section('title', 'CenoBots Projects')

@section('content')
<div class="container-fluid py-2">
    <div class="ms-3">
        <h3 class="mb-0 h4 font-weight-bolder">CenoBots Projects</h3>
        <p class="mb-4">
            Projects shown in the "Проекти" slider on /services/cenobots, each with its client logo and photo from the Media Library. Drag rows to reorder; changes save automatically.
        </p>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible text-white" role="alert">
            <span class="text-sm">{{ session('success') }}</span>
            <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">All Projects</h6>
            <a href="{{ route('cenobots-clients.create') }}" class="btn btn-sm bg-gradient-dark mb-0">
                <i class="material-symbols-rounded me-1" style="font-size:16px;">add</i> Add New Project
            </a>
        </div>
        <div class="card-body px-0 pb-2">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th style="width:40px;"></th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Order</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Logo</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Photo</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Project</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Category</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="clients-body">
                        @forelse ($clients as $client)
                            <tr data-id="{{ $client->id }}">
                                <td class="align-middle">
                                    <i class="material-symbols-rounded text-muted drag-handle" style="font-size:18px;cursor:grab;">drag_indicator</i>
                                </td>
                                <td class="align-middle"><span class="text-xs font-weight-bold order-badge">{{ $loop->iteration }}</span></td>
                                <td class="align-middle">
                                    @if ($client->logo)
                                        <img src="{{ $client->logo->getUrl() }}" alt="{{ $client->logo_alt ?: $client->name }}" style="height:36px;max-width:90px;object-fit:contain;">
                                    @else
                                        <span class="text-xs text-muted">—</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if ($client->photo)
                                        <img src="{{ $client->photo->getUrl() }}" alt="" style="height:36px;width:64px;object-fit:cover;border-radius:4px;">
                                    @else
                                        <span class="text-xs text-muted">Default</span>
                                    @endif
                                </td>
                                <td class="align-middle"><span class="text-xs font-weight-bold">{{ $client->name }}</span></td>
                                <td class="align-middle"><span class="text-xs">{{ $client->category ?: '—' }}</span></td>
                                <td class="align-middle">
                                    <span class="badge status-badge {{ $client->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">{{ $client->is_active ? 'Active' : 'Inactive' }}</span>
                                </td>
                                <td class="align-middle text-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary toggle-btn mb-0" data-id="{{ $client->id }}">
                                        {{ $client->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                    <a href="{{ route('cenobots-clients.edit', $client) }}" class="btn btn-sm btn-outline-primary mb-0">Edit</a>
                                    <form action="{{ route('cenobots-clients.destroy', $client) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this project?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger mb-0">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No projects yet. The public page shows the original three projects until you add one.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/sortablejs/1.15.0/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var tbody = document.getElementById('clients-body');
    var csrf = '{{ csrf_token() }}';
    var hdr = {'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'};

    tbody.addEventListener('click', function (e) {
        var btn = e.target.closest('.toggle-btn');
        if (!btn) return;
        btn.disabled = true;
        fetch('{{ url('/admin/cenobots-clients') }}/' + btn.dataset.id + '/toggle', {method: 'PATCH', headers: hdr})
            .then(function (r) { return r.json(); })
            .then(function (d) {
                var badge = btn.closest('tr').querySelector('.status-badge');
                badge.className = 'badge status-badge ' + (d.is_active ? 'bg-gradient-success' : 'bg-gradient-secondary');
                badge.textContent = d.is_active ? 'Active' : 'Inactive';
                btn.textContent = d.is_active ? 'Disable' : 'Enable';
            })
            .catch(function () { alert('Failed to update project.'); })
            .finally(function () { btn.disabled = false; });
    });

    if (window.Sortable && tbody.querySelector('tr[data-id]')) {
        Sortable.create(tbody, {
            handle: '.drag-handle',
            animation: 150,
            onEnd: function () {
                var order = [];
                tbody.querySelectorAll('tr[data-id]').forEach(function (row, i) {
                    row.querySelector('.order-badge').textContent = i + 1;
                    order.push(row.dataset.id);
                });
                fetch('{{ route('cenobots-clients.reorder') }}', {
                    method: 'POST',
                    headers: Object.assign({'Content-Type': 'application/json'}, hdr),
                    body: JSON.stringify({order: order})
                }).then(function (r) { if (!r.ok) throw new Error(); })
                  .catch(function () { alert('Failed to save order.'); });
            }
        });
    }
});
</script>
@endsection
