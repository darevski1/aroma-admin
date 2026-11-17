@extends('layouts.master')

@php $isEdit = $entry->exists; @endphp

@section('title', $isEdit ? 'Edit Knowledge Entry' : 'Add Knowledge Entry')

@section('content')
<div class="container-fluid py-2">
    @include('admin_pages.chatbot._nav', ['title' => $isEdit ? 'Edit Knowledge Entry' : 'Add Knowledge Entry'])

    <form method="POST" action="{{ $isEdit ? route('chatbot.admin.knowledge.update', $entry) : route('chatbot.admin.knowledge.store') }}">
        @csrf
        @if ($isEdit) @method('PUT') @endif
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control border px-2" required maxlength="255" value="{{ old('title', $entry->title) }}" placeholder="e.g. Работно време / Opening hours">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-control border px-2">
                            <option value="">— None —</option>
                            @foreach ($categories as $c)<option value="{{ $c->id }}" @selected(old('category_id', $entry->category_id) == $c->id)>{{ $c->name }}</option>@endforeach
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Content *</label>
                        <textarea name="content" rows="10" required maxlength="50000" class="form-control border px-2">{{ old('content', $entry->content) }}</textarea>
                        <p class="text-xs text-muted mb-0">Write facts plainly. The AI only answers from information like this and never invents prices or services.</p>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-control border px-2">
                            @foreach (\App\Models\Chatbot\ChatbotKnowledge::TYPES as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', $entry->type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label">Source (URL or document name, optional)</label>
                        <input type="text" name="source" class="form-control border px-2" maxlength="255" value="{{ old('source', $entry->source) }}">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Language</label>
                        <select name="language" class="form-control border px-2">
                            <option value="mk" @selected(old('language', $entry->language) === 'mk')>Macedonian</option>
                            <option value="en" @selected(old('language', $entry->language) === 'en')>English</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <div class="form-check form-switch ps-0">
                            <input class="form-check-input ms-auto" type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $entry->is_active))>
                            <label class="form-check-label text-body ms-3 mb-0" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button class="btn bg-gradient-dark mb-0">{{ $isEdit ? 'Save changes' : 'Add entry' }}</button>
        <a href="{{ route('chatbot.admin.knowledge.index') }}" class="btn btn-outline-secondary mb-0">Cancel</a>
    </form>
</div>
@endsection
