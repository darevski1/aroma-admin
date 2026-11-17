{{--
    Floating website chatbot. Included by the public layouts. Self-contained
    (scoped CSS + public/js/chatbot-widget.js) so it works on both the main
    site and the CenoBots mini-site without a frontend rebuild. Only public
    texts are rendered here — no provider, instruction or credential data.
--}}
@php
    $__chatbot = null;
    if (! request('editor')) {
        try {
            $__chatbot = \App\Models\Chatbot\ChatbotSetting::current();
        } catch (\Throwable $e) {
            $__chatbot = null; // tables not migrated yet: render nothing
        }
    }
@endphp
@if ($__chatbot && $__chatbot->isVisible())
@php $__cbEn = $__chatbot->default_language === 'en'; @endphp
<div id="itp-chatbot" class="itp-cb" data-state="closed"
     data-message-url="{{ route('chatbot.message') }}"
     data-history-url="{{ route('chatbot.history') }}"
     data-reset-url="{{ route('chatbot.reset') }}"
     data-csrf="{{ csrf_token() }}"
     data-max-length="{{ (int) config('chatbot.max_message_length', 1000) }}"
     data-lang="{{ $__chatbot->default_language }}">
    <template id="itp-cb-welcome">{{ $__chatbot->welcome_message }}</template>

    <button type="button" class="itp-cb__launcher" aria-controls="itp-cb-panel" aria-expanded="false"
            aria-label="{{ $__cbEn ? 'Open chat' : 'Отвори разговор' }}">
        <svg class="itp-cb__icon-open" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H8l-4 4V6a2 2 0 0 1 2-2z" fill="currentColor"/><circle cx="8.5" cy="11" r="1.3" fill="#e04534"/><circle cx="12" cy="11" r="1.3" fill="#e04534"/><circle cx="15.5" cy="11" r="1.3" fill="#e04534"/></svg>
        <svg class="itp-cb__icon-close" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/></svg>
    </button>

    <section id="itp-cb-panel" class="itp-cb__panel" role="dialog" aria-modal="false" aria-labelledby="itp-cb-title" hidden>
        <header class="itp-cb__header">
            <div class="itp-cb__avatar" aria-hidden="true">
                <svg viewBox="0 0 24 24"><rect x="4" y="7" width="16" height="12" rx="4" fill="currentColor"/><circle cx="9.5" cy="13" r="1.6" fill="#e04534"/><circle cx="14.5" cy="13" r="1.6" fill="#e04534"/><path d="M12 3v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <div class="itp-cb__titles">
                <h2 id="itp-cb-title" class="itp-cb__title">{{ $__chatbot->bot_name }}</h2>
                <p class="itp-cb__subtitle"><span class="itp-cb__dot"></span>{{ $__cbEn ? 'Online' : 'Онлајн' }}</p>
            </div>
            <button type="button" class="itp-cb__icon-btn" data-cb-reset title="{{ $__cbEn ? 'Clear conversation' : 'Избриши разговор' }}" aria-label="{{ $__cbEn ? 'Clear conversation' : 'Избриши разговор' }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12M9 7V4h6v3" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <button type="button" class="itp-cb__icon-btn" data-cb-close title="{{ $__cbEn ? 'Close' : 'Затвори' }}" aria-label="{{ $__cbEn ? 'Close chat' : 'Затвори разговор' }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
        </header>

        <div class="itp-cb__messages" role="log" aria-live="polite" aria-relevant="additions">
            <div class="itp-cb__empty">
                <p>{{ $__cbEn ? 'Ask us anything about our services.' : 'Прашајте нè за нашите услуги.' }}</p>
            </div>
        </div>

        <div class="itp-cb__typing" hidden aria-hidden="true"><span></span><span></span><span></span></div>
        <p class="itp-cb__error" role="alert" hidden></p>

        <form class="itp-cb__form" novalidate>
            <label for="itp-cb-input" class="itp-cb__sr">{{ $__cbEn ? 'Your message' : 'Ваша порака' }}</label>
            <textarea id="itp-cb-input" class="itp-cb__input" rows="1" maxlength="{{ (int) config('chatbot.max_message_length', 1000) }}"
                      placeholder="{{ $__cbEn ? 'Type your message…' : 'Напишете порака…' }}" autocomplete="off"></textarea>
            <button type="submit" class="itp-cb__send" aria-label="{{ $__cbEn ? 'Send' : 'Испрати' }}" disabled>
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 12l16-8-6 16-2.5-6.5L4 12z" fill="currentColor"/></svg>
            </button>
        </form>
    </section>
</div>

<style>
#itp-chatbot{--cb-primary:#e04534;--cb-primary-dark:#ce3b2a;--cb-ink:#171717;--cb-muted:#62676d;--cb-bg:#ffffff;--cb-soft:#f7f8fa;--cb-line:#ebebeb;
  position:fixed;right:20px;bottom:20px;z-index:2147483000;font-family:inherit;font-size:15px;line-height:1.45;color:var(--cb-ink);}
#itp-chatbot *,#itp-chatbot *::before,#itp-chatbot *::after{box-sizing:border-box;}
#itp-chatbot .itp-cb__sr{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap;}
#itp-chatbot .itp-cb__launcher{width:60px;height:60px;border-radius:50%;border:0;cursor:pointer;background:var(--cb-primary);color:#fff;
  display:flex;align-items:center;justify-content:center;box-shadow:0 10px 28px rgba(224,69,52,.38);transition:transform .2s ease,background .2s ease;margin-left:auto;}
#itp-chatbot .itp-cb__launcher:hover{background:var(--cb-primary-dark);transform:translateY(-2px);}
#itp-chatbot .itp-cb__launcher:focus-visible,#itp-chatbot button:focus-visible,#itp-chatbot textarea:focus-visible{outline:3px solid rgba(224,69,52,.45);outline-offset:2px;}
#itp-chatbot .itp-cb__launcher svg{width:28px;height:28px;}
#itp-chatbot .itp-cb__icon-close{display:none;}
#itp-chatbot[data-state=open] .itp-cb__icon-open{display:none;}
#itp-chatbot[data-state=open] .itp-cb__icon-close{display:block;}
#itp-chatbot .itp-cb__panel{position:absolute;right:0;bottom:76px;width:380px;max-width:calc(100vw - 32px);height:560px;max-height:calc(100vh - 120px);
  background:var(--cb-bg);border-radius:18px;box-shadow:0 24px 60px rgba(23,23,23,.22);display:flex;flex-direction:column;overflow:hidden;
  opacity:0;transform:translateY(12px) scale(.98);transform-origin:bottom right;transition:opacity .2s ease,transform .2s ease;}
#itp-chatbot .itp-cb__panel[hidden]{display:none;}
#itp-chatbot[data-state=open] .itp-cb__panel.is-visible{opacity:1;transform:none;}
#itp-chatbot .itp-cb__header{display:flex;align-items:center;gap:10px;padding:14px 12px 14px 16px;background:var(--cb-ink);color:#fff;}
#itp-chatbot .itp-cb__avatar{width:38px;height:38px;border-radius:50%;background:#fff;color:var(--cb-ink);display:flex;align-items:center;justify-content:center;flex:none;}
#itp-chatbot .itp-cb__avatar svg{width:24px;height:24px;}
#itp-chatbot .itp-cb__titles{flex:1;min-width:0;}
#itp-chatbot .itp-cb__title{margin:0;font-size:15px;font-weight:700;color:#fff;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
#itp-chatbot .itp-cb__subtitle{margin:2px 0 0;font-size:12px;color:#cbd5e1;display:flex;align-items:center;gap:6px;}
#itp-chatbot .itp-cb__dot{width:7px;height:7px;border-radius:50%;background:#22c55e;display:inline-block;}
#itp-chatbot .itp-cb__icon-btn{width:34px;height:34px;border:0;border-radius:10px;background:transparent;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;flex:none;}
#itp-chatbot .itp-cb__icon-btn:hover{background:rgba(255,255,255,.12);}
#itp-chatbot .itp-cb__icon-btn svg{width:19px;height:19px;}
#itp-chatbot .itp-cb__messages{flex:1;overflow-y:auto;padding:16px;background:var(--cb-soft);display:flex;flex-direction:column;gap:10px;overscroll-behavior:contain;}
#itp-chatbot .itp-cb__empty{margin:auto;text-align:center;color:var(--cb-muted);font-size:14px;max-width:240px;}
#itp-chatbot .itp-cb__empty p{margin:0;}
#itp-chatbot .itp-cb__msg{max-width:85%;padding:10px 14px;border-radius:16px;white-space:pre-wrap;overflow-wrap:anywhere;word-break:break-word;animation:itpCbIn .18s ease;}
#itp-chatbot .itp-cb__msg--bot{align-self:flex-start;background:#fff;border:1px solid var(--cb-line);border-bottom-left-radius:4px;}
#itp-chatbot .itp-cb__msg--user{align-self:flex-end;background:var(--cb-primary);color:#fff;border-bottom-right-radius:4px;}
#itp-chatbot .itp-cb__msg--error{border-color:#fecaca;background:#fef2f2;}
#itp-chatbot .itp-cb__msg a{color:inherit;text-decoration:underline;}
#itp-chatbot .itp-cb__msg--bot a{color:var(--cb-primary-dark);}
#itp-chatbot .itp-cb__typing{display:flex;gap:4px;padding:0 16px 10px;background:var(--cb-soft);}
#itp-chatbot .itp-cb__typing[hidden]{display:none;}
#itp-chatbot .itp-cb__typing span{width:8px;height:8px;border-radius:50%;background:#94a3b8;animation:itpCbDot 1s infinite ease-in-out;}
#itp-chatbot .itp-cb__typing span:nth-child(2){animation-delay:.15s;}
#itp-chatbot .itp-cb__typing span:nth-child(3){animation-delay:.3s;}
#itp-chatbot .itp-cb__error{margin:0;padding:8px 16px;font-size:13px;color:#b91c1c;background:#fef2f2;}
#itp-chatbot .itp-cb__error[hidden]{display:none;}
#itp-chatbot .itp-cb__form{display:flex;align-items:flex-end;gap:8px;padding:12px;border-top:1px solid var(--cb-line);background:#fff;margin:0;}
#itp-chatbot .itp-cb__input{flex:1;resize:none;border:1px solid var(--cb-line);border-radius:12px;padding:10px 12px;font:inherit;font-size:15px;color:var(--cb-ink);
  max-height:120px;min-height:44px;background:#fff;outline:none;box-shadow:none;}
#itp-chatbot .itp-cb__input:focus{border-color:var(--cb-primary);}
#itp-chatbot .itp-cb__send{width:44px;height:44px;flex:none;border:0;border-radius:12px;background:var(--cb-primary);color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;}
#itp-chatbot .itp-cb__send:disabled{background:#e5e7eb;color:#9ca3af;cursor:not-allowed;}
#itp-chatbot .itp-cb__send svg{width:20px;height:20px;}
@keyframes itpCbIn{from{opacity:0;transform:translateY(4px);}to{opacity:1;transform:none;}}
@keyframes itpCbDot{0%,80%,100%{transform:scale(.6);opacity:.5;}40%{transform:scale(1);opacity:1;}}
@media (max-width:480px){
  #itp-chatbot{right:14px;bottom:14px;}
  #itp-chatbot[data-state=open] .itp-cb__launcher{display:none;}
  #itp-chatbot .itp-cb__panel{position:fixed;inset:0;width:100%;max-width:none;height:100%;max-height:none;border-radius:0;bottom:auto;}
  #itp-chatbot .itp-cb__input{font-size:16px;}
}
@media (prefers-reduced-motion:reduce){#itp-chatbot *{animation:none!important;transition:none!important;}}
</style>
<script src="{{ asset('js/chatbot-widget.js') }}?v={{ @filemtime(public_path('js/chatbot-widget.js')) }}" defer></script>
@endif
