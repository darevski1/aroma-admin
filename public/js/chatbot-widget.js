/*
 * ITPlus website chatbot widget.
 * Talks to one Laravel endpoint regardless of the active AI provider.
 * All message text is inserted with textContent (links are built as DOM
 * nodes), so visitor or model output can never inject HTML.
 */
(function () {
    'use strict';

    var root = document.getElementById('itp-chatbot');
    if (!root || root.dataset.ready) return;
    root.dataset.ready = '1';

    var en = root.dataset.lang === 'en';
    var t = {
        error: en ? 'Something went wrong. Please try again.' : 'Настана грешка. Обидете се повторно.',
        network: en ? 'Connection problem. Check your internet and try again.' : 'Проблем со врската. Проверете ја интернет врската и обидете се повторно.',
        tooMany: en ? 'You are sending messages too quickly. Please wait a moment.' : 'Испраќате пораки пребрзо. Почекајте малку.',
        expired: en ? 'Your session has expired. Please reload the page.' : 'Сесијата истече. Ве молиме освежете ја страницата.',
        clearConfirm: en ? 'Clear this conversation?' : 'Да се избрише разговорот?'
    };

    var launcher = root.querySelector('.itp-cb__launcher');
    var panel = root.querySelector('.itp-cb__panel');
    var list = root.querySelector('.itp-cb__messages');
    var empty = root.querySelector('.itp-cb__empty');
    var typing = root.querySelector('.itp-cb__typing');
    var errorBox = root.querySelector('.itp-cb__error');
    var form = root.querySelector('.itp-cb__form');
    var input = root.querySelector('.itp-cb__input');
    var send = root.querySelector('.itp-cb__send');
    var welcomeTpl = document.getElementById('itp-cb-welcome');
    var welcome = welcomeTpl ? (welcomeTpl.content ? welcomeTpl.content.textContent : welcomeTpl.textContent).trim() : '';
    var maxLength = parseInt(root.dataset.maxLength, 10) || 1000;

    var loaded = false;
    var busy = false;

    function store(key, value) {
        try {
            if (value === undefined) return window.sessionStorage.getItem(key);
            if (value === null) window.sessionStorage.removeItem(key);
            else window.sessionStorage.setItem(key, value);
        } catch (e) { /* storage unavailable: ignore */ }
        return null;
    }

    function headers(json) {
        var h = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': root.dataset.csrf
        };
        if (json) h['Content-Type'] = 'application/json';
        return h;
    }

    // Appends text to el, turning URLs, e-mails and phone numbers into safe links.
    function appendRich(el, text) {
        var re = /(https?:\/\/[^\s<>"']+|[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}|\+?\d[\d\s\/-]{7,}\d)/g;
        var last = 0;
        var m;
        while ((m = re.exec(text)) !== null) {
            if (m.index > last) el.appendChild(document.createTextNode(text.slice(last, m.index)));
            var value = m[0];
            var a = document.createElement('a');
            if (/^https?:/i.test(value)) {
                a.href = value;
                a.target = '_blank';
                a.rel = 'noopener noreferrer nofollow';
            } else if (value.indexOf('@') !== -1) {
                a.href = 'mailto:' + value;
            } else {
                a.href = 'tel:' + value.replace(/[^\d+]/g, '');
            }
            a.textContent = value;
            el.appendChild(a);
            last = m.index + value.length;
        }
        if (last < text.length) el.appendChild(document.createTextNode(text.slice(last)));
    }

    function addMessage(sender, text, isError) {
        empty.hidden = true;
        var bubble = document.createElement('div');
        bubble.className = 'itp-cb__msg itp-cb__msg--' + (sender === 'user' ? 'user' : 'bot') + (isError ? ' itp-cb__msg--error' : '');
        if (sender === 'user') bubble.textContent = text;
        else appendRich(bubble, text);
        list.appendChild(bubble);
        list.scrollTop = list.scrollHeight;
        return bubble;
    }

    function showWelcome() {
        if (welcome) addMessage('bot', welcome);
        else empty.hidden = false;
    }

    function clearMessages() {
        list.querySelectorAll('.itp-cb__msg').forEach(function (n) { n.remove(); });
        empty.hidden = false;
    }

    function setError(text) {
        errorBox.textContent = text || '';
        errorBox.hidden = !text;
    }

    function setBusy(state) {
        busy = state;
        typing.hidden = !state;
        root.setAttribute('aria-busy', state ? 'true' : 'false');
        input.disabled = state;
        updateSend();
        if (state) list.scrollTop = list.scrollHeight;
    }

    function updateSend() {
        send.disabled = busy || input.value.trim() === '';
    }

    function autoGrow() {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 120) + 'px';
    }

    function errorFor(status, data) {
        if (status === 419) return t.expired;
        if (status === 429) return t.tooMany;
        if (data && typeof data.message === 'string' && data.message) return data.message;
        return t.error;
    }

    function loadHistory() {
        if (loaded) return;
        loaded = true;
        fetch(root.dataset.historyUrl, { headers: headers(false), credentials: 'same-origin' })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (res) {
                clearMessages();
                var messages = res.ok && res.data && Array.isArray(res.data.messages) ? res.data.messages : [];
                showWelcome();
                messages.forEach(function (m) { addMessage(m.sender, String(m.message || '')); });
            })
            .catch(function () {
                clearMessages();
                showWelcome();
            });
    }

    function open(restoring) {
        root.dataset.state = 'open';
        panel.hidden = false;
        launcher.setAttribute('aria-expanded', 'true');
        requestAnimationFrame(function () { panel.classList.add('is-visible'); });
        store('itpCbOpen', '1');
        loadHistory();
        if (!restoring) setTimeout(function () { input.focus(); }, 60);
    }

    function close() {
        root.dataset.state = 'closed';
        panel.classList.remove('is-visible');
        launcher.setAttribute('aria-expanded', 'false');
        store('itpCbOpen', null);
        setTimeout(function () { if (root.dataset.state === 'closed') panel.hidden = true; }, 200);
        launcher.focus();
    }

    function submit() {
        var text = input.value.trim();
        if (!text || busy) return;
        if (text.length > maxLength) text = text.slice(0, maxLength);

        setError('');
        addMessage('user', text);
        input.value = '';
        autoGrow();
        setBusy(true);

        fetch(root.dataset.messageUrl, {
            method: 'POST',
            headers: headers(true),
            credentials: 'same-origin',
            body: JSON.stringify({ message: text })
        })
            .then(function (r) {
                return r.json().catch(function () { return null; }).then(function (d) { return { status: r.status, ok: r.ok, data: d }; });
            })
            .then(function (res) {
                if (res.ok && res.data && res.data.success) {
                    addMessage('bot', String(res.data.message || ''));
                } else if (res.status === 503 && res.data && res.data.message) {
                    addMessage('bot', String(res.data.message), true);
                } else {
                    setError(errorFor(res.status, res.data));
                }
            })
            .catch(function () { setError(t.network); })
            .finally(function () {
                setBusy(false);
                input.focus();
            });
    }

    function reset() {
        if (busy || !window.confirm(t.clearConfirm)) return;
        setError('');
        fetch(root.dataset.resetUrl, { method: 'POST', headers: headers(true), credentials: 'same-origin', body: '{}' })
            .then(function (r) {
                if (!r.ok) throw r;
                clearMessages();
                showWelcome();
            })
            .catch(function (r) { setError(errorFor(r && r.status, null)); });
    }

    launcher.addEventListener('click', function () {
        if (root.dataset.state === 'open') close(); else open(false);
    });
    root.querySelector('[data-cb-close]').addEventListener('click', close);
    root.querySelector('[data-cb-reset]').addEventListener('click', reset);
    form.addEventListener('submit', function (e) { e.preventDefault(); submit(); });
    input.addEventListener('input', function () { autoGrow(); updateSend(); });
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) {
            e.preventDefault();
            submit();
        }
    });
    root.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && root.dataset.state === 'open') close();
    });

    if (store('itpCbOpen') === '1') open(true);
})();
