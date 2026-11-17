@extends('layouts.master')

@section('title', 'Chatbot Instructions')

@section('content')
<div class="container-fluid py-2">
    @include('admin_pages.chatbot._nav', ['title' => 'System Instructions', 'subtitle' => 'Defines how the AI providers (Ollama, OpenAI) behave. Never shown to visitors. The FAQ Engine uses the fallback message and response limit from General Settings.'])

    <form method="POST" action="{{ route('chatbot.admin.instructions.update') }}">
        @csrf @method('PUT')
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    @foreach ($labels as $key => $label)
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="s_{{ $key }}">{{ $label }}</label>
                            <textarea id="s_{{ $key }}" name="sections[{{ $key }}]" rows="3" maxlength="5000" class="form-control border px-2">{{ old('sections.' . $key, $sections[$key] ?? '') }}</textarea>
                        </div>
                    @endforeach
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Default temperature (0–2)</label>
                        <input type="number" step="0.05" min="0" max="2" name="temperature" class="form-control border px-2" value="{{ old('temperature', $temperature) }}">
                        <p class="text-xs text-muted mb-0">Used when a provider has no own value. Empty = not sent.</p>
                    </div>
                </div>
            </div>
        </div>
        <button class="btn bg-gradient-dark mb-0">Save instructions</button>
    </form>
    <form method="POST" action="{{ route('chatbot.admin.instructions.reset') }}" class="d-inline" onsubmit="return confirm('Replace the instructions with the default template?');">
        @csrf
        <button class="btn btn-outline-secondary mt-2 mb-0">Restore default template</button>
    </form>

    <div class="card mt-4">
        <div class="card-header pb-0"><h6 class="mb-0">Preview of the system prompt sent to AI providers</h6></div>
        <div class="card-body">
            <pre class="text-xs bg-gray-100 p-3 border-radius-md mb-0" style="white-space:pre-wrap;">{{ $preview }}</pre>
        </div>
    </div>
</div>
@endsection
