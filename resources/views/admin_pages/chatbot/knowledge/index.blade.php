@extends('layouts.master')

@section('title', 'Chatbot Knowledge Base')

@section('content')
<div class="container-fluid py-2">
    @include('admin_pages.chatbot._nav', ['title' => 'Knowledge Base', 'subtitle' => 'Company information, services, products, pricing, policies and contacts. Shared by all providers; AI providers receive only the entries relevant to each question.'])

    <div class="card mb-4">
        <div class="card-header pb-0">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control border px-2" placeholder="Search title or content…">
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
                    <a href="{{ route('chatbot.admin.knowledge.create') }}" class="btn btn-sm bg-gradient-dark mb-0 ms-auto">Add entry</a>
                </div>
            </form>
        </div>
        <div class="card-body px-0 pb-2">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Title</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Type</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Category</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Lang</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Updated</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($entries as $entry)
                            <tr>
                                <td class="ps-3 text-wrap" style="max-width:420px;">
                                    <span class="text-sm font-weight-bold d-block">{{ $entry->title }}</span>
                                    <span class="text-xs text-muted">{{ \Illuminate\Support\Str::limit($entry->content, 140) }}</span>
                                </td>
                                <td class="text-xs">{{ \App\Models\Chatbot\ChatbotKnowledge::TYPES[$entry->type] ?? $entry->type }}</td>
                                <td class="text-xs">{{ $entry->category?->name ?? '—' }}</td>
                                <td class="text-xs text-uppercase">{{ $entry->language }}</td>
                                <td><span class="badge status-badge {{ $entry->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">{{ $entry->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td class="text-xs">{{ $entry->updated_at?->format('d.m.Y') }}</td>
                                <td class="text-center text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-secondary toggle-btn mb-0" data-url="{{ route('chatbot.admin.knowledge.toggle', $entry) }}">{{ $entry->is_active ? 'Disable' : 'Enable' }}</button>
                                    <a href="{{ route('chatbot.admin.knowledge.edit', $entry) }}" class="btn btn-sm btn-outline-primary mb-0">Edit</a>
                                    <form action="{{ route('chatbot.admin.knowledge.destroy', $entry) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this entry?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger mb-0">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No knowledge entries found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-3 pt-3">{{ $entries->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>
@include('admin_pages.chatbot._toggle-script')
@endsection
