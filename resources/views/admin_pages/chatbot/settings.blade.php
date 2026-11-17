@extends('layouts.master')

@section('title', 'Chatbot Settings')

@section('content')
<div class="container-fluid py-2">
    @include('admin_pages.chatbot._nav', ['title' => 'General Settings'])

    <form method="POST" action="{{ route('chatbot.admin.settings.update') }}">
        @csrf @method('PUT')
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="form-check form-switch ps-0">
                            <input class="form-check-input ms-auto" type="checkbox" id="enabled" name="enabled" value="1" @checked(old('enabled', $settings->enabled))>
                            <label class="form-check-label text-body ms-3 mb-0" for="enabled">Chatbot enabled</label>
                        </div>
                        <p class="text-xs text-muted mb-0">When disabled the public endpoints refuse requests and the widget is hidden.</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-check form-switch ps-0">
                            <input class="form-check-input ms-auto" type="checkbox" id="show_on_frontend" name="show_on_frontend" value="1" @checked(old('show_on_frontend', $settings->show_on_frontend))>
                            <label class="form-check-label text-body ms-3 mb-0" for="show_on_frontend">Show widget on the website</label>
                        </div>
                        <p class="text-xs text-muted mb-0">Hide the floating button while keeping the chatbot configured.</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-check form-switch ps-0">
                            <input class="form-check-input ms-auto" type="checkbox" id="fallback_to_faq" name="fallback_to_faq" value="1" @checked(old('fallback_to_faq', $settings->fallback_to_faq))>
                            <label class="form-check-label text-body ms-3 mb-0" for="fallback_to_faq">Use FAQ Engine if the AI provider fails</label>
                        </div>
                        <p class="text-xs text-muted mb-0">Free fallback only. A paid provider is never used as a fallback.</p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bot name *</label>
                        <input type="text" name="bot_name" class="form-control border px-2" required maxlength="100" value="{{ old('bot_name', $settings->bot_name) }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Default language *</label>
                        <select name="default_language" class="form-control border px-2">
                            <option value="mk" @selected(old('default_language', $settings->default_language) === 'mk')>Macedonian</option>
                            <option value="en" @selected(old('default_language', $settings->default_language) === 'en')>English</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Maximum response length (characters) *</label>
                        <input type="number" name="response_limit" min="100" max="10000" class="form-control border px-2" required value="{{ old('response_limit', $settings->response_limit) }}">
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Welcome message</label>
                        <textarea name="welcome_message" rows="3" maxlength="2000" class="form-control border px-2">{{ old('welcome_message', $settings->welcome_message) }}</textarea>
                        <p class="text-xs text-muted mb-0">Shown when a visitor opens the chat.</p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Fallback message *</label>
                        <textarea name="fallback_message" rows="3" maxlength="2000" required class="form-control border px-2">{{ old('fallback_message', $settings->fallback_message) }}</textarea>
                        <p class="text-xs text-muted mb-0">Returned when no sufficiently relevant answer is found.</p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">Unavailable message *</label>
                        <textarea name="unavailable_message" rows="2" maxlength="2000" required class="form-control border px-2">{{ old('unavailable_message', $settings->unavailable_message) }}</textarea>
                        <p class="text-xs text-muted mb-0">Shown when the active provider cannot be reached and no fallback applies.</p>
                    </div>
                </div>
            </div>
        </div>
        <button class="btn bg-gradient-dark mb-0">Save settings</button>
    </form>
</div>
@endsection
