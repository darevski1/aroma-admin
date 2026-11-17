{{-- Active/inactive toggle for rows with .toggle-btn[data-url] and a .status-badge. --}}
<script>
document.addEventListener('click', function (e) {
    var btn = e.target.closest('.toggle-btn');
    if (!btn) return;
    btn.disabled = true;
    fetch(btn.dataset.url, {method: 'PATCH', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'}})
        .then(function (r) { if (!r.ok) throw new Error(); return r.json(); })
        .then(function (d) {
            var badge = btn.closest('tr').querySelector('.status-badge');
            badge.className = 'badge status-badge ' + (d.is_active ? 'bg-gradient-success' : 'bg-gradient-secondary');
            badge.textContent = d.is_active ? 'Active' : 'Inactive';
            btn.textContent = d.is_active ? 'Disable' : 'Enable';
        })
        .catch(function () { alert('Failed to update.'); })
        .finally(function () { btn.disabled = false; });
});
</script>
