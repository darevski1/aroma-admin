@extends('layouts.master')

@section('title', 'Chatbot Statistics')

@php
    $providerLabels = ['faq' => 'FAQ Engine', 'ollama' => 'Ollama', 'openai' => 'OpenAI'];
    $maxDay = max(1, max($perDay ?: [0]));
@endphp

@section('content')
<div class="container-fluid py-2">
    @include('admin_pages.chatbot._nav', ['title' => 'Usage Statistics'])

    <div class="row">
        @foreach ([
            ['Total messages', $totals['messages']],
            ['Visitor questions', $totals['questions']],
            ['FAQ matches', $types['faq']],
            ['AI-generated responses', $types['ai']],
            ['Fallback responses', $types['fallback']],
            ['Unanswered questions', $totals['unanswered']],
        ] as [$label, $value])
            <div class="col-xl-2 col-md-4 col-6 mb-4">
                <div class="card"><div class="card-body p-3">
                    <p class="text-xs mb-0">{{ $label }}</p>
                    <h4 class="mb-0">{{ number_format($value) }}</h4>
                </div></div>
            </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0"><h6 class="mb-0">Conversations per day (last 14 days)</h6></div>
                <div class="card-body">
                    <div class="d-flex align-items-end gap-1" style="height:180px;" role="img" aria-label="Conversations per day">
                        @foreach ($perDay as $day => $count)
                            <div class="flex-fill d-flex flex-column align-items-center justify-content-end h-100" title="{{ $day }}: {{ $count }}">
                                <span class="text-xxs">{{ $count ?: '' }}</span>
                                <div class="w-100 bg-gradient-dark border-radius-sm" style="height:{{ max(2, round($count / $maxDay * 140)) }}px;"></div>
                                <span class="text-xxs text-muted mt-1">{{ \Carbon\Carbon::parse($day)->format('d.m') }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0"><h6 class="mb-0">Bot replies by provider</h6></div>
                <div class="card-body">
                    <table class="table mb-0">
                        <thead><tr>
                            <th class="text-xxs text-uppercase opacity-7">Provider</th>
                            <th class="text-xxs text-uppercase opacity-7">FAQ</th>
                            <th class="text-xxs text-uppercase opacity-7">AI</th>
                            <th class="text-xxs text-uppercase opacity-7">Fallback</th>
                            <th class="text-xxs text-uppercase opacity-7">Error</th>
                        </tr></thead>
                        <tbody>
                            @forelse ($byProvider as $provider => $counts)
                                <tr class="text-sm">
                                    <td>{{ $providerLabels[$provider] ?? ($provider ?: '—') }}</td>
                                    <td>{{ $counts['faq'] ?? 0 }}</td>
                                    <td>{{ $counts['ai'] ?? 0 }}</td>
                                    <td>{{ $counts['fallback'] ?? 0 }}</td>
                                    <td>{{ $counts['error'] ?? 0 }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-sm text-muted">No replies yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header pb-0 d-flex align-items-center"><h6 class="mb-0">OpenAI usage &amp; cost</h6><span class="badge bg-gradient-warning ms-2">tracked separately</span></div>
        <div class="card-body">
            <div class="row text-sm">
                <div class="col-md-3 mb-2"><span class="text-muted d-block text-xs">Requests (all time)</span>{{ number_format($openai['requests']) }}</div>
                <div class="col-md-3 mb-2"><span class="text-muted d-block text-xs">Prompt / completion tokens</span>{{ number_format($openai['prompt_tokens']) }} / {{ number_format($openai['completion_tokens']) }}</div>
                <div class="col-md-3 mb-2"><span class="text-muted d-block text-xs">This month</span>{{ number_format($openai['month_requests']) }} requests · {{ number_format($openai['month_tokens']) }} tokens</div>
                <div class="col-md-3 mb-2">
                    <span class="text-muted d-block text-xs">Estimated cost</span>
                    @if (! $pricingConfigured)
                        Not available — set prices in AI Provider Settings.
                    @elseif ($openai['cost'] === null)
                        —
                    @else
                        ≈ ${{ number_format($openai['cost'], 4) }} (this month ≈ ${{ number_format($openai['month_cost'] ?? 0, 4) }})
                    @endif
                </div>
            </div>
            <p class="text-xs text-muted mb-0">Estimates use the token counts reported by OpenAI and the prices you configured; they are not an invoice.
                @if ($openai['unpriced']) {{ $openai['unpriced'] }} request(s) have no estimate because prices or token usage were unavailable at the time. @endif
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0"><h6 class="mb-0">Most matched FAQs</h6></div>
                <div class="card-body pt-2">
                    <table class="table mb-0"><tbody>
                        @forelse ($faqMatches as $row)
                            <tr><td class="text-sm text-wrap">{{ $row->matchedFaq?->question ?? 'Deleted FAQ' }}</td><td class="text-end"><span class="badge bg-gradient-secondary">{{ $row->total }}</span></td></tr>
                        @empty
                            <tr><td class="text-sm text-muted">No FAQ matches yet.</td></tr>
                        @endforelse
                    </tbody></table>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0"><h6 class="mb-0">Unanswered questions</h6></div>
                <div class="card-body pt-2">
                    <table class="table mb-0"><tbody>
                        @forelse ($unanswered as $m)
                            <tr>
                                <td class="text-sm text-wrap"><a href="{{ route('chatbot.admin.conversations.show', $m->conversation_id) }}">{{ \Illuminate\Support\Str::limit($m->message, 120) }}</a></td>
                                <td class="text-xs text-muted text-nowrap text-end">{{ $m->created_at?->format('d.m.Y') }}</td>
                            </tr>
                        @empty
                            <tr><td class="text-sm text-muted">None.</td></tr>
                        @endforelse
                    </tbody></table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
