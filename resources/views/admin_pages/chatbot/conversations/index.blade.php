@extends('layouts.master')

@section('title', 'Chatbot Conversations')

@section('content')
<div class="container-fluid py-2">
    @include('admin_pages.chatbot._nav', ['title' => 'Conversations'])

    <div class="card mb-4">
        <div class="card-header pb-0">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-xs mb-0">Search</label>
                    <input type="search" name="search" value="{{ request('search') }}" class="form-control border px-2" placeholder="Message text, name, email…">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xs mb-0">From</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control border px-2">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xs mb-0">To</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control border px-2">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-xs mb-0">Provider</label>
                    <select name="provider" class="form-control border px-2">
                        <option value="">Any</option>
                        @foreach (['faq' => 'FAQ Engine', 'ollama' => 'Ollama', 'openai' => 'OpenAI'] as $k => $v)
                            <option value="{{ $k }}" @selected(request('provider') === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="unanswered" value="1" id="unanswered" @checked(request()->boolean('unanswered'))>
                        <label class="form-check-label text-sm" for="unanswered">Not answered only</label>
                    </div>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-sm btn-outline-dark mb-0 w-100">Filter</button>
                </div>
            </form>
        </div>
        <div class="card-body px-0 pb-2">
            <div class="table-responsive">
                <table class="table align-items-center mb-0">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-3">#</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Visitor</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Last question</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Messages</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Unanswered</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Started</th>
                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Last activity</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($conversations as $c)
                            <tr>
                                <td class="ps-3 text-xs">{{ $c->id }}</td>
                                <td class="text-xs">{{ $c->user?->name ?? $c->visitor_name ?? 'Guest' }}@if($c->visitor_email)<br><span class="text-muted">{{ $c->visitor_email }}</span>@endif</td>
                                <td class="text-xs text-wrap" style="max-width:360px;">{{ \Illuminate\Support\Str::limit($c->last_question, 120) ?: '—' }}</td>
                                <td class="text-xs">{{ $c->messages_count }}</td>
                                <td>@if ($c->unanswered_count)<span class="badge bg-gradient-warning">{{ $c->unanswered_count }}</span>@else<span class="text-xs text-muted">0</span>@endif</td>
                                <td class="text-xs text-nowrap">{{ $c->started_at?->format('d.m.Y H:i') }}</td>
                                <td class="text-xs text-nowrap">{{ $c->last_activity_at?->format('d.m.Y H:i') }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('chatbot.admin.conversations.show', $c) }}" class="btn btn-sm btn-outline-primary mb-0">Open</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No conversations found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-3 pt-3">{{ $conversations->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>
@endsection
