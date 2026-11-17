@extends('layouts.master')

@section('title', 'Chatbot FAQ')

@section('content')
<div class="container-fluid py-2">
    @include('admin_pages.chatbot._nav', ['title' => 'FAQ Management', 'subtitle' => 'Answered directly by the free FAQ Engine and used as context by the AI providers. Macedonian (Cyrillic or Latin) and English are matched.'])

    <div class="card mb-4">
        <div class="card-header pb-0">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control border px-2" placeholder="Search question, answer, keywords…">
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-control border px-2">
                        <option value="">All categories</option>
                        @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(request('category') == $c->id)>{{ $c->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="language" class="form-control border px-2">
                        <option value="">All languages</option>
                        <option value="mk" @selected(request('language') === 'mk')>Macedonian</option>
                        <option value="en" @selected(request('language') === 'en')>English</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-control border px-2">
                        <option value="">Any status</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button class="btn btn-sm btn-outline-dark mb-0">Filter</button>
                    <a href="{{ route('chatbot.admin.faqs.create') }}" class="btn btn-sm bg-gradient-dark mb-0 ms-auto">Add FAQ</a>
                </div>
            </form>
        </div>
        <div class="card-body px-0 pb-2">
            <form method="POST" action="{{ route('chatbot.admin.faqs.bulk') }}" id="bulk-form" onsubmit="return this.elements['action'].value !== 'delete' || confirm('Delete the selected FAQs?');">
                @csrf
                <div class="d-flex align-items-center gap-2 px-3 mb-2">
                    <select name="action" class="form-control border px-2" style="max-width:220px;">
                        <option value="activate">Activate selected</option>
                        <option value="deactivate">Deactivate selected</option>
                        <option value="delete">Delete selected</option>
                    </select>
                    <button class="btn btn-sm btn-outline-dark mb-0">Apply</button>
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="ps-3" style="width:32px;"><input type="checkbox" id="check-all" class="form-check-input"></th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Question / Answer</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Keywords</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Category</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lang</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Order</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($faqs as $faq)
                                <tr>
                                    <td class="ps-3"><input type="checkbox" name="ids[]" value="{{ $faq->id }}" class="form-check-input row-check"></td>
                                    <td class="text-wrap" style="max-width:420px;">
                                        <span class="text-sm font-weight-bold d-block">{{ $faq->question }}</span>
                                        <span class="text-xs text-muted">{{ \Illuminate\Support\Str::limit($faq->answer, 140) }}</span>
                                    </td>
                                    <td class="text-xs text-wrap" style="max-width:200px;">{{ implode(', ', $faq->keywords ?? []) ?: '—' }}</td>
                                    <td class="text-xs">{{ $faq->category?->name ?? '—' }}</td>
                                    <td class="text-xs text-uppercase">{{ $faq->language }}</td>
                                    <td class="text-xs">{{ $faq->sort_order }}</td>
                                    <td><span class="badge status-badge {{ $faq->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">{{ $faq->is_active ? 'Active' : 'Inactive' }}</span></td>
                                    <td class="text-center text-nowrap">
                                        <button type="button" class="btn btn-sm btn-outline-secondary toggle-btn mb-0" data-url="{{ route('chatbot.admin.faqs.toggle', $faq) }}">{{ $faq->is_active ? 'Disable' : 'Enable' }}</button>
                                        <a href="{{ route('chatbot.admin.faqs.edit', $faq) }}" class="btn btn-sm btn-outline-primary mb-0">Edit</a>
                                        <button type="submit" form="delete-faq-{{ $faq->id }}" class="btn btn-sm btn-outline-danger mb-0">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-muted py-4">No FAQ entries found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
            @foreach ($faqs as $faq)
                <form id="delete-faq-{{ $faq->id }}" action="{{ route('chatbot.admin.faqs.destroy', $faq) }}" method="POST" class="d-none" onsubmit="return confirm('Delete this FAQ?');">
                    @csrf @method('DELETE')
                </form>
            @endforeach
            <div class="px-3 pt-3">{{ $faqs->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>
@include('admin_pages.chatbot._toggle-script')
<script>
document.getElementById('check-all').addEventListener('change', function () {
    var on = this.checked;
    document.querySelectorAll('.row-check').forEach(function (c) { c.checked = on; });
});
</script>
@endsection
