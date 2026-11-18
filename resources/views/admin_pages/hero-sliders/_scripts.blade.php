<script>
// Shared helpers for the Hero Sliders admin screens.
window.heroAdmin = (function () {
    var headers = {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    };

    function copyText(text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        }
        // http:// dev hosts have no async clipboard API.
        return new Promise(function (resolve, reject) {
            var ta = document.createElement('textarea');
            ta.value = text;
            ta.setAttribute('readonly', '');
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            var ok = false;
            try { ok = document.execCommand('copy'); } catch (e) {}
            document.body.removeChild(ta);
            ok ? resolve() : reject();
        });
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.hs-copy');
        if (!btn) return;
        var label = btn.querySelector('.hs-copy-label');
        copyText(btn.dataset.copy).then(function () {
            if (label) label.textContent = 'Copied!';
            btn.classList.replace('btn-outline-dark', 'btn-success');
        }).catch(function () {
            if (label) label.textContent = 'Press Ctrl+C';
            var code = btn.parentNode.querySelector('.hs-shortcode');
            if (code) { var r = document.createRange(); r.selectNodeContents(code); var s = getSelection(); s.removeAllRanges(); s.addRange(r); }
        }).finally(function () {
            setTimeout(function () {
                if (label) label.textContent = 'Copy';
                btn.classList.replace('btn-success', 'btn-outline-dark');
            }, 1800);
        });
    });

    function bindToggles(container, onLabel, offLabel) {
        if (!container) return;
        container.addEventListener('click', function (e) {
            var btn = e.target.closest('.toggle-btn');
            if (!btn) return;
            btn.disabled = true;
            fetch(btn.dataset.url, { method: 'PATCH', headers: headers })
                .then(function (r) { if (!r.ok) throw new Error(); return r.json(); })
                .then(function (d) {
                    var row = btn.closest('[data-id]');
                    var badge = row.querySelector('.status-badge');
                    badge.className = 'badge status-badge ' + (d.is_active ? 'bg-gradient-success' : 'bg-gradient-secondary');
                    badge.textContent = d.is_active ? onLabel : offLabel;
                    btn.textContent = d.is_active ? 'Disable' : 'Enable';
                    row.classList.toggle('opacity-6', !d.is_active && row.tagName === 'TR' && row.dataset.dim === '1');
                })
                .catch(function () { alert('Could not update the status.'); })
                .finally(function () { btn.disabled = false; });
        });
    }

    return { headers: headers, copyText: copyText, bindToggles: bindToggles };
})();
</script>
