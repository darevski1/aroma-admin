@extends('layouts.master')

@php $isEdit = $faq->exists; @endphp

@section('title', $isEdit ? 'Edit FAQ' : 'Add FAQ')

@section('content')
<div class="container-fluid py-2">
    @include('admin_pages.chatbot._nav', ['title' => $isEdit ? 'Edit FAQ' : 'Add FAQ'])

    <form method="POST" action="{{ $isEdit ? route('chatbot.admin.faqs.update', $faq) : route('chatbot.admin.faqs.store') }}">
        @csrf
        @if ($isEdit) @method('PUT') @endif
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label">Question *</label>
                        <input type="text" name="question" class="form-control border px-2" required maxlength="1000" value="{{ old('question', $faq->question) }}">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Answer *</label>
                        <textarea name="answer" rows="6" required maxlength="10000" class="form-control border px-2">{{ old('answer', $faq->answer) }}</textarea>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Keywords (comma separated)</label>
                        <input type="text" name="keywords" class="form-control border px-2" maxlength="1000" value="{{ old('keywords', implode(', ', $faq->keywords ?? [])) }}" placeholder="контакт, телефон, contact, phone">
                        <p class="text-xs text-muted mb-0">Add synonyms in both languages; Cyrillic and Latin spellings are matched automatically.</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-control border px-2">
                            <option value="">— None —</option>
                            @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $faq->category_id) == $c->id)>{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Language</label>
                        <select name="language" class="form-control border px-2">
                            <option value="mk" @selected(old('language', $faq->language) === 'mk')>Macedonian</option>
                            <option value="en" @selected(old('language', $faq->language) === 'en')>English</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Sort order</label>
                        <input type="number" name="sort_order" min="0" class="form-control border px-2" value="{{ old('sort_order', $faq->sort_order) }}">
                    </div>
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <div class="form-check form-switch ps-0">
                            <input class="form-check-input ms-auto" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $faq->is_active))>
                            <label class="form-check-label text-body ms-3 mb-0" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="btn bg-gradient-dark mb-0">{{ $isEdit ? 'Save changes' : 'Add FAQ' }}</button>
        <a href="{{ route('chatbot.admin.faqs.index') }}" class="btn btn-outline-secondary mb-0">Cancel</a>
    </form>
</div>
@endsection
