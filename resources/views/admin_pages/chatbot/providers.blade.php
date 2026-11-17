@extends('layouts.master')

@section('title', 'Chatbot Providers')

@php
    $descriptions = [
        'faq' => 'Free database-driven chatbot. Answers from FAQ entries; no API key or external service required.',
        'ollama' => 'Local / self-hosted AI model running on an Ollama server. No per-request cost.',
        'openai' => 'Cloud AI through the official OpenAI API. Billed per token to your OpenAI account.',
    ];
    $faq = $providers['faq']['config'];
    $ollama = $providers['ollama']['config'];
    $openai = $providers['openai']['config'];
@endphp

@section('content')
<div class="container-fluid py-2">
    @include('admin_pages.chatbot._nav', ['title' => 'AI Provider Settings', 'subtitle' => 'All providers share the same instructions, FAQ, knowledge base and conversation history. Switching needs no frontend change.'])

    {{-- Active provider --}}
    <form method="POST" action="{{ route('chatbot.admin.providers.activate') }}" class="card mb-4" id="activate-form">
        @csrf @method('PUT')
        <div class="card-header pb-0"><h6 class="mb-0">Active provider</h6></div>
        <div class="card-body">
            <div class="row">
                @foreach ($providers as $key => $p)
                    <div class="col-md-4 mb-3">
                        <label class="border rounded-3 p-3 h-100 d-block {{ $activeKey === $key ? 'border-dark border-2' : '' }}" style="cursor:pointer;">
                            <div class="d-flex align-items-center mb-1">
                                <input type="radio" name="provider" value="{{ $key }}" class="form-check-input me-2 mt-0" @checked($activeKey === $key)>
                                <strong>{{ $p['provider']->label() }}</strong>
                                @if ($activeKey === $key)<span class="badge bg-gradient-success ms-auto">Active</span>@endif
                                @if ($p['provider']->isPaid())<span class="badge bg-gradient-warning ms-1">Paid</span>@else<span class="badge bg-gradient-info ms-1">Free</span>@endif
                            </div>
                            <p class="text-xs mb-2">{{ $descriptions[$key] ?? '' }}</p>
                            @if ($p['problems'])
                                <p class="text-xs text-danger mb-0">{{ implode(' ', $p['problems']) }}</p>
                            @else
                                <p class="text-xs text-success mb-0">Ready</p>
                            @endif
                        </label>
                    </div>
                @endforeach
            </div>
            <div class="form-check mb-3 d-none" id="confirm-paid-wrap">
                <input class="form-check-input" type="checkbox" name="confirm_paid" value="1" id="confirm_paid">
                <label class="form-check-label text-sm" for="confirm_paid">I understand that OpenAI requests are billed to our OpenAI account (within the hourly/daily limits below).</label>
            </div>
            <button class="btn bg-gradient-dark mb-0">Use selected provider</button>
        </div>
    </form>

    {{-- FAQ Engine --}}
    <form method="POST" action="{{ route('chatbot.admin.providers.update', 'faq') }}" class="card mb-4">
        @csrf @method('PUT')
        <div class="card-header pb-0 d-flex align-items-center">
            <h6 class="mb-0">Option 1 · FAQ Engine</h6>
            <span class="badge bg-gradient-info ms-2">Free</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="form-check form-switch ps-0">
                        <input class="form-check-input ms-auto" type="checkbox" id="faq_enabled" name="is_enabled" value="1" @checked($faq->is_enabled)>
                        <label class="form-check-label text-body ms-3 mb-0" for="faq_enabled">Enabled</label>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Minimum matching threshold (0.1–1)</label>
                    <input type="number" step="0.05" min="0.1" max="1" name="threshold" class="form-control border px-2" value="{{ $faq->setting('threshold', 0.5) }}">
                    <p class="text-xs text-muted mb-0">Higher = stricter. 0.5 is a good start.</p>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="form-check form-switch ps-0">
                        <input class="form-check-input ms-auto" type="checkbox" id="faq_lang" name="language_matching" value="1" @checked($faq->setting('language_matching', true))>
                        <label class="form-check-label text-body ms-3 mb-0" for="faq_lang">Language matching</label>
                    </div>
                    <p class="text-xs text-muted mb-0">Prefer FAQs in the visitor's language.</p>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="form-check form-switch ps-0">
                        <input class="form-check-input ms-auto" type="checkbox" id="faq_kw" name="keyword_matching" value="1" @checked($faq->setting('keyword_matching', true))>
                        <label class="form-check-label text-body ms-3 mb-0" for="faq_kw">Keyword matching</label>
                    </div>
                    <p class="text-xs text-muted mb-0">Match on each FAQ's keywords.</p>
                </div>
            </div>
            <button class="btn bg-gradient-dark mb-0">Save</button>
            <button type="button" class="btn btn-outline-secondary mb-0 test-btn" data-url="{{ route('chatbot.admin.providers.test', 'faq') }}">Test</button>
            <span class="test-result text-sm ms-2"></span>
        </div>
    </form>

    {{-- Ollama --}}
    <form method="POST" action="{{ route('chatbot.admin.providers.update', 'ollama') }}" class="card mb-4">
        @csrf @method('PUT')
        <div class="card-header pb-0 d-flex align-items-center">
            <h6 class="mb-0">Option 2 · Ollama</h6>
            <span class="badge bg-gradient-info ms-2">Free / self-hosted</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="form-check form-switch ps-0">
                        <input class="form-check-input ms-auto" type="checkbox" id="ollama_enabled" name="is_enabled" value="1" @checked($ollama->is_enabled)>
                        <label class="form-check-label text-body ms-3 mb-0" for="ollama_enabled">Enabled</label>
                    </div>
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label">Ollama server URL *</label>
                    <input type="url" name="base_url" class="form-control border px-2" required placeholder="http://127.0.0.1:11434" value="{{ old('base_url', $ollama->base_url) }}">
                    <p class="text-xs text-muted mb-0">Any server reachable from this web server — not necessarily this machine.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Model name *</label>
                    <input type="text" name="model_name" class="form-control border px-2" required placeholder="llama3.2" value="{{ old('model_name', $ollama->model_name) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Connection timeout (seconds)</label>
                    <input type="number" name="timeout" min="1" max="300" class="form-control border px-2" value="{{ $ollama->setting('timeout', 30) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Temperature (0–2)</label>
                    <input type="number" step="0.05" min="0" max="2" name="temperature" class="form-control border px-2" value="{{ $ollama->setting('temperature') }}">
                    <p class="text-xs text-muted mb-0">Empty = default from System Instructions.</p>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Maximum response length (tokens)</label>
                    <input type="number" name="max_tokens" min="16" max="8192" class="form-control border px-2" value="{{ $ollama->setting('max_tokens', 500) }}">
                </div>
            </div>
            <button class="btn bg-gradient-dark mb-0">Save</button>
            <button type="button" class="btn btn-outline-secondary mb-0 test-btn" data-url="{{ route('chatbot.admin.providers.test', 'ollama') }}">Test connection</button>
            <span class="test-result text-sm ms-2"></span>
            <p class="text-xs text-muted mt-2 mb-0">The test uses the saved settings — save first.</p>
        </div>
    </form>

    {{-- OpenAI --}}
    <form method="POST" action="{{ route('chatbot.admin.providers.update', 'openai') }}" class="card mb-4" autocomplete="off">
        @csrf @method('PUT')
        <div class="card-header pb-0 d-flex align-items-center">
            <h6 class="mb-0">Option 3 · OpenAI</h6>
            <span class="badge bg-gradient-warning ms-2">Paid</span>
        </div>
        <div class="card-body">
            <p class="text-sm">Uses the official OpenAI API with an API key from <a href="https://platform.openai.com/api-keys" target="_blank" rel="noopener">platform.openai.com</a>. A ChatGPT subscription is <strong>not</strong> an API credential. Nothing is billed unless OpenAI is enabled <em>and</em> selected as the active provider.</p>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="form-check form-switch ps-0">
                        <input class="form-check-input ms-auto" type="checkbox" id="openai_enabled" name="is_enabled" value="1" @checked($openai->is_enabled)>
                        <label class="form-check-label text-body ms-3 mb-0" for="openai_enabled">Enabled</label>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">API key source</label>
                    <select name="api_key_source" class="form-control border px-2">
                        <option value="env" @selected($openaiKey['source'] === 'env')>.env file (OPENAI_API_KEY) — recommended</option>
                        <option value="database" @selected($openaiKey['source'] === 'database')>Stored encrypted (entered below)</option>
                    </select>
                    <p class="text-xs mb-0 {{ $openaiKey['env_present'] ? 'text-success' : 'text-muted' }}">
                        .env key: {{ $openaiKey['env_present'] ? 'present' : 'not set' }} ·
                        stored key: {{ $openaiKey['stored_present'] ? 'present' : 'not set' }}
                    </p>
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label">API key (stored encrypted, never displayed)</label>
                    <input type="password" name="api_key" class="form-control border px-2" autocomplete="new-password" placeholder="{{ $openaiKey['stored_present'] ? '•••••••• (leave empty to keep)' : 'sk-…' }}">
                    @if ($openaiKey['stored_present'])
                        <div class="form-check mt-1">
                            <input class="form-check-input" type="checkbox" name="clear_api_key" value="1" id="clear_api_key">
                            <label class="form-check-label text-xs" for="clear_api_key">Remove stored key</label>
                        </div>
                    @endif
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Model *</label>
                    <input type="text" name="model_name" class="form-control border px-2" required placeholder="e.g. gpt-4o-mini" value="{{ old('model_name', $openai->model_name) }}">
                    <p class="text-xs text-muted mb-0">Any chat model your API key can use.</p>
                </div>
                <div class="col-md-5 mb-3">
                    <label class="form-label">API base URL</label>
                    <input type="url" name="base_url" class="form-control border px-2" placeholder="https://api.openai.com/v1" value="{{ old('base_url', $openai->base_url) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Timeout (seconds)</label>
                    <input type="number" name="timeout" min="1" max="300" class="form-control border px-2" value="{{ $openai->setting('timeout', 30) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Temperature (0–2)</label>
                    <input type="number" step="0.05" min="0" max="2" name="temperature" class="form-control border px-2" value="{{ $openai->setting('temperature') }}">
                    <p class="text-xs text-muted mb-0">Empty = default; some models only accept their default.</p>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Max response tokens</label>
                    <input type="number" name="max_tokens" min="16" max="16384" class="form-control border px-2" value="{{ $openai->setting('max_tokens', 500) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Max requests / hour</label>
                    <input type="number" name="max_requests_per_hour" min="0" class="form-control border px-2" value="{{ $openai->setting('max_requests_per_hour', 60) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Max requests / day</label>
                    <input type="number" name="max_requests_per_day" min="0" class="form-control border px-2" value="{{ $openai->setting('max_requests_per_day', 300) }}">
                    <p class="text-xs text-muted mb-0">0 = no limit.</p>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Input price (USD / 1M tokens)</label>
                    <input type="number" step="0.0001" min="0" name="input_price_per_million" class="form-control border px-2" value="{{ $openai->setting('input_price_per_million') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Output price (USD / 1M tokens)</label>
                    <input type="number" step="0.0001" min="0" name="output_price_per_million" class="form-control border px-2" value="{{ $openai->setting('output_price_per_million') }}">
                    <p class="text-xs text-muted mb-0">Optional — used only for cost estimates. Check current pricing on openai.com.</p>
                </div>
            </div>
            <button class="btn bg-gradient-dark mb-0">Save</button>
            <button type="button" class="btn btn-outline-secondary mb-0 test-btn" data-url="{{ route('chatbot.admin.providers.test', 'openai') }}">Test connection</button>
            <span class="test-result text-sm ms-2"></span>
            <p class="text-xs text-muted mt-2 mb-0">The test only looks up the model (no tokens, no cost) and uses the saved settings — save first.</p>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('activate-form');
    var wrap = document.getElementById('confirm-paid-wrap');
    function syncPaid() {
        var picked = form.querySelector('input[name=provider]:checked');
        wrap.classList.toggle('d-none', !(picked && picked.value === 'openai' && '{{ $activeKey }}' !== 'openai'));
    }
    form.addEventListener('change', syncPaid);
    syncPaid();

    document.querySelectorAll('.test-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var out = btn.parentNode.querySelector('.test-result');
            btn.disabled = true;
            out.className = 'test-result text-sm ms-2 text-muted';
            out.textContent = 'Testing…';
            fetch(btn.dataset.url, {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}})
                .then(function (r) { return r.json(); })
                .then(function (d) {
                    out.className = 'test-result text-sm ms-2 ' + (d.ok ? 'text-success' : 'text-danger');
                    out.textContent = d.message || (d.ok ? 'OK' : 'Failed');
                })
                .catch(function () { out.className = 'test-result text-sm ms-2 text-danger'; out.textContent = 'Request failed.'; })
                .finally(function () { btn.disabled = false; });
        });
    });
});
</script>
@endsection
