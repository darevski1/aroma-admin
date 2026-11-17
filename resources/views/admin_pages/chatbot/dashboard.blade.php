@extends('layouts.master')

@section('title', 'Chatbot')

@section('content')
<div class="container-fluid py-2">
    @include('admin_pages.chatbot._nav', ['title' => 'Chatbot', 'subtitle' => 'Website assistant overview. The widget appears in the bottom-right corner of the public site.'])

    @if ($providerProblems)
        <div class="alert alert-warning text-white">
            <strong>{{ $activeProvider->label() }} cannot answer right now:</strong> {{ implode(' ', $providerProblems) }}
            @if ($settings->fallback_to_faq && $activeProvider->key() !== 'faq') Visitors are answered by the FAQ Engine meanwhile. @endif
        </div>
    @endif

    <div class="row">
        @php
            $cards = [
                ['Status', $settings->enabled ? ($settings->show_on_frontend ? 'Online' : 'Enabled (widget hidden)') : 'Disabled', $settings->enabled ? 'toggle_on' : 'toggle_off'],
                ['Active provider', $activeProvider->label() . ($activeProvider->isPaid() ? ' (paid)' : ''), 'hub'],
                ['Total conversations', number_format($totals['conversations']), 'forum'],
                ['Conversations today', number_format($totals['conversations_today']), 'today'],
                ['Total messages', number_format($totals['messages']), 'chat'],
                ['Unanswered questions', number_format($totals['unanswered']), 'help'],
                ['Active FAQs', number_format($faqCount), 'quiz'],
                ['Knowledge entries', number_format($knowledgeCount), 'menu_book'],
            ];
        @endphp
        @foreach ($cards as [$label, $value, $icon])
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-header p-2 ps-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-sm mb-0 text-capitalize">{{ $label }}</p>
                                <h4 class="mb-0">{{ $value }}</h4>
                            </div>
                            <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                <i class="material-symbols-rounded opacity-10">{{ $icon }}</i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0"><h6 class="mb-0">Most frequently asked questions</h6></div>
                <div class="card-body pt-2">
                    <table class="table align-items-center mb-0">
                        <tbody>
                            @forelse ($topQuestions as $row)
                                <tr>
                                    <td class="text-sm text-wrap">{{ \Illuminate\Support\Str::limit($row->question, 120) }}</td>
                                    <td class="text-end"><span class="badge bg-gradient-secondary">{{ $row->total }}×</span></td>
                                </tr>
                            @empty
                                <tr><td class="text-sm text-muted">No questions yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0 d-flex justify-content-between">
                    <h6 class="mb-0">Latest unanswered questions</h6>
                    <a href="{{ route('chatbot.admin.conversations.index', ['unanswered' => 1]) }}" class="text-sm">View all</a>
                </div>
                <div class="card-body pt-2">
                    <table class="table align-items-center mb-0">
                        <tbody>
                            @forelse ($unanswered as $msg)
                                <tr>
                                    <td class="text-sm text-wrap">
                                        <a href="{{ route('chatbot.admin.conversations.show', $msg->conversation_id) }}">{{ \Illuminate\Support\Str::limit($msg->message, 120) }}</a>
                                    </td>
                                    <td class="text-xs text-muted text-end text-nowrap">{{ $msg->created_at?->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr><td class="text-sm text-muted">Nothing unanswered. 🎉</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                    <p class="text-xs text-muted mt-2 mb-0">Tip: turn these into FAQ entries or knowledge base content.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
