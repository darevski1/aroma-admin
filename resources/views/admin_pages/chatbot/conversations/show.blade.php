@extends('layouts.master')

@section('title', 'Conversation #' . $conversation->id)

@php
    $providerLabels = ['faq' => 'FAQ Engine', 'ollama' => 'Ollama', 'openai' => 'OpenAI'];
    $typeLabels = ['faq' => 'FAQ match', 'ai' => 'AI answer', 'fallback' => 'Fallback', 'error' => 'Error'];
@endphp

@section('content')
<div class="container-fluid py-2">
    @include('admin_pages.chatbot._nav', ['title' => 'Conversation #' . $conversation->id])

    <div class="card mb-3">
        <div class="card-body d-flex flex-wrap gap-4 text-sm">
            <div><span class="text-muted">Visitor:</span> {{ $conversation->user?->name ?? $conversation->visitor_name ?? 'Guest' }}</div>
            @if ($conversation->visitor_email)<div><span class="text-muted">Email:</span> {{ $conversation->visitor_email }}</div>@endif
            <div><span class="text-muted">Started:</span> {{ $conversation->started_at?->format('d.m.Y H:i:s') }}</div>
            <div><span class="text-muted">Last activity:</span> {{ $conversation->last_activity_at?->format('d.m.Y H:i:s') }}</div>
            <div class="ms-auto">
                <a href="{{ route('chatbot.admin.conversations.index') }}" class="btn btn-sm btn-outline-secondary mb-0">Back</a>
                <form action="{{ route('chatbot.admin.conversations.destroy', $conversation) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this conversation permanently?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger mb-0">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @forelse ($conversation->messages as $m)
                @php $isUser = $m->sender === 'user'; @endphp
                <div class="d-flex mb-3 {{ $isUser ? 'justify-content-end' : 'justify-content-start' }}">
                    <div class="p-3 border-radius-lg {{ $isUser ? 'bg-gradient-dark text-white' : 'bg-gray-100' }}" style="max-width:75%;">
                        {{-- Escaped output: visitor and model text are never rendered as HTML. --}}
                        <div class="text-sm" style="white-space:pre-wrap;">{{ $m->message }}</div>
                        <div class="text-xxs mt-2 {{ $isUser ? 'text-white opacity-8' : 'text-muted' }}">
                            {{ $isUser ? 'Visitor' : 'Bot' }} · {{ $m->created_at?->format('d.m.Y H:i:s') }}
                            @if ($m->provider) · {{ $providerLabels[$m->provider] ?? $m->provider }} @endif
                            @if (! $isUser && $m->response_type) · {{ $typeLabels[$m->response_type] ?? $m->response_type }} @endif
                            @if ($isUser && $m->answered === false) · <strong>not answered</strong> @endif
                            @if (! $isUser && $m->matchedFaq) · FAQ: “{{ \Illuminate\Support\Str::limit($m->matchedFaq->question, 60) }}” @endif
                            @if ($m->prompt_tokens !== null || $m->completion_tokens !== null) · tokens {{ (int) $m->prompt_tokens }}+{{ (int) $m->completion_tokens }} @endif
                            @if ($m->estimated_cost !== null) · ≈ ${{ number_format($m->estimated_cost, 6) }} @endif
                            @if (! $isUser && ! empty($m->metadata['fallback_from'])) · fallback from {{ $providerLabels[$m->metadata['fallback_from']] ?? $m->metadata['fallback_from'] }} @endif
                        </div>
                        @if (! $isUser && ! empty($m->metadata['error']))
                            <div class="text-xxs text-danger mt-1">Provider error: {{ $m->metadata['error'] }}</div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-muted text-sm mb-0">This conversation has no messages.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
