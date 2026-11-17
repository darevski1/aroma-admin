@extends('layouts.master')

@php $isEdit = $category->exists; @endphp

@section('title', $isEdit ? 'Edit Category' : 'Add Category')

@section('content')
<div class="container-fluid py-2">
    @include('admin_pages.chatbot._nav', ['title' => $isEdit ? 'Edit Category' : 'Add Category'])

    <form method="POST" action="{{ $isEdit ? route('chatbot.admin.categories.update', $category) : route('chatbot.admin.categories.store') }}">
        @csrf
        @if ($isEdit) @method('PUT') @endif
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control border px-2" required maxlength="255" value="{{ old('name', $category->name) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slug (generated when empty)</label>
                        <input type="text" name="slug" class="form-control border px-2" maxlength="255" value="{{ old('slug', $category->slug) }}">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" maxlength="2000" class="form-control border px-2">{{ old('description', $category->description) }}</textarea>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Sort order</label>
                        <input type="number" name="sort_order" min="0" class="form-control border px-2" value="{{ old('sort_order', $category->sort_order) }}">
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <div class="form-check form-switch ps-0">
                            <input class="form-check-input ms-auto" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $category->is_active))>
                            <label class="form-check-label text-body ms-3 mb-0" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="btn bg-gradient-dark mb-0">{{ $isEdit ? 'Save changes' : 'Add category' }}</button>
        <a href="{{ route('chatbot.admin.categories.index') }}" class="btn btn-outline-secondary mb-0">Cancel</a>
    </form>
</div>
@endsection
