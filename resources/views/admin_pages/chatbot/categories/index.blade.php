@extends('layouts.master')

@section('title', 'Chatbot Categories')

@section('content')
<div class="container-fluid py-2">
    @include('admin_pages.chatbot._nav', ['title' => 'Categories', 'subtitle' => 'Organize FAQ and knowledge entries. Entries in an inactive category are not used by the chatbot.'])

    <div class="card mb-4">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">All categories</h6>
            <a href="{{ route('chatbot.admin.categories.create') }}" class="btn btn-sm bg-gradient-dark mb-0">Add category</a>
        </div>
        <div class="card-body px-0 pb-2">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">Order</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Slug</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">FAQs</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Knowledge</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td class="ps-3 text-xs">{{ $category->sort_order }}</td>
                                <td>
                                    <span class="text-sm font-weight-bold d-block">{{ $category->name }}</span>
                                    @if ($category->description)<span class="text-xs text-muted">{{ \Illuminate\Support\Str::limit($category->description, 100) }}</span>@endif
                                </td>
                                <td class="text-xs">{{ $category->slug }}</td>
                                <td class="text-xs"><a href="{{ route('chatbot.admin.faqs.index', ['category' => $category->id]) }}">{{ $category->faqs_count }}</a></td>
                                <td class="text-xs"><a href="{{ route('chatbot.admin.knowledge.index', ['category' => $category->id]) }}">{{ $category->knowledge_count }}</a></td>
                                <td><span class="badge status-badge {{ $category->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td class="text-center text-nowrap">
                                    <button type="button" class="btn btn-sm btn-outline-secondary toggle-btn mb-0" data-url="{{ route('chatbot.admin.categories.toggle', $category) }}">{{ $category->is_active ? 'Disable' : 'Enable' }}</button>
                                    <a href="{{ route('chatbot.admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary mb-0">Edit</a>
                                    <form action="{{ route('chatbot.admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category? Its entries are kept without a category.');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger mb-0">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No categories yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@include('admin_pages.chatbot._toggle-script')
@endsection
