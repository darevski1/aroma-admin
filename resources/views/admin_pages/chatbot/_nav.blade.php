{{-- Shared header for every Chatbot admin page: title, section tabs, flash messages. --}}
@php
    $chatbotTabs = [
        'chatbot.admin.dashboard' => ['Dashboard', 'dashboard'],
        'chatbot.admin.settings' => ['General Settings', 'tune'],
        'chatbot.admin.providers' => ['AI Provider Settings', 'hub'],
        'chatbot.admin.instructions' => ['System Instructions', 'rule'],
        'chatbot.admin.knowledge.index' => ['Knowledge Base', 'menu_book'],
        'chatbot.admin.faqs.index' => ['FAQ Management', 'quiz'],
        'chatbot.admin.categories.index' => ['Categories', 'category'],
        'chatbot.admin.conversations.index' => ['Conversations', 'forum'],
        'chatbot.admin.statistics' => ['Usage Statistics', 'monitoring'],
    ];
@endphp
<div class="ms-3 mb-3">
    <h3 class="mb-0 h4 font-weight-bolder">{{ $title }}</h3>
    @isset($subtitle)<p class="mb-0 text-sm">{{ $subtitle }}</p>@endisset
</div>

<div class="card mb-3">
    <div class="card-body py-2 px-2">
        <ul class="nav nav-pills flex-wrap gap-1 chatbot-tabs">
            @foreach ($chatbotTabs as $route => [$label, $icon])
                @php $isActive = request()->routeIs(\Illuminate\Support\Str::beforeLast($route, '.index') . '*'); @endphp
                <li class="nav-item">
                    <a href="{{ route($route) }}" class="nav-link py-1 px-3 text-sm d-flex align-items-center {{ $isActive ? 'active bg-gradient-dark text-white' : 'text-dark' }}">
                        <i class="material-symbols-rounded me-1" style="font-size:18px;">{{ $icon }}</i>{{ $label }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible text-white" role="alert">
        <span class="text-sm">{{ session('success') }}</span>
        <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible text-white" role="alert">
        <span class="text-sm">{{ session('error') }}</span>
        <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger text-white">
        <ul class="mb-0 text-sm">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif
