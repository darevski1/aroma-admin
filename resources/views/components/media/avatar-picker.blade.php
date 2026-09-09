@props([
    'model',
    'current' => null,
])

@php
    $uniqueId = 'avatar-picker-' . uniqid();
@endphp

<div class="mb-3 text-center" id="{{ $uniqueId }}">
    <label class="text-xs font-weight-bolder mb-1 text-dark">Profile Photo</label>

    {{-- Avatar preview --}}
    <div class="d-flex justify-content-center mt-2 mb-3">
        <div class="position-relative" style="width:120px;height:120px;border-radius:50%;overflow:hidden;background:#e9ecef;border:3px solid #dee2e6;cursor:pointer;"
             id="{{ $uniqueId }}-frame"
             onclick="document.getElementById('{{ $uniqueId }}-file').click();">
            @if($current)
                <img src="{{ $current->getUrl('avatar') ?: $current->getUrl() }}" alt="Avatar"
                     id="{{ $uniqueId }}-img"
                     style="width:100%;height:100%;object-fit:cover;">
            @else
                <div class="d-flex align-items-center justify-content-center h-100" id="{{ $uniqueId }}-placeholder">
                    <i class="material-symbols-rounded text-muted" style="font-size:40px;">person</i>
                </div>
                <img src="" alt="Avatar" id="{{ $uniqueId }}-img" class="d-none"
                     style="width:100%;height:100%;object-fit:cover;">
            @endif
        </div>
    </div>

    {{-- File input --}}
    <input type="file"
           name="avatar_file"
           id="{{ $uniqueId }}-file"
           class="d-none"
           accept="image/jpeg,image/png,image/webp,image/avif,image/gif"
           onchange="window.__avatarPickerPreview('{{ $uniqueId }}', this)">

    {{-- Buttons --}}
    <div class="d-flex justify-content-center gap-2">
        <button type="button" class="btn btn-outline-dark btn-sm" onclick="document.getElementById('{{ $uniqueId }}-file').click();">
            <i class="material-symbols-rounded me-1" style="font-size:14px;">photo_camera</i> Change Avatar
        </button>
        @if($current)
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="window.__avatarPickerRemove('{{ $uniqueId }}')">
                <i class="material-symbols-rounded me-1" style="font-size:14px;">delete</i> Remove
            </button>
            <input type="hidden" name="remove_avatar" value="0" id="{{ $uniqueId }}-remove-flag">
        @endif
    </div>

    {{-- File info --}}
    <div class="text-xs text-muted mt-2" id="{{ $uniqueId }}-info" style="display:none;"></div>

    {{-- Validation error --}}
    @error('avatar_file')
        <div class="text-danger text-xs mt-1">{{ $message }}</div>
    @enderror
</div>

<script>
(function() {
    window.__avatarPickerPreview = function(uid, input) {
        if (!input.files || !input.files[0]) return;
        var file = input.files[0];
        var img = document.getElementById(uid + '-img');
        var placeholder = document.getElementById(uid + '-placeholder');
        var info = document.getElementById(uid + '-info');
        var removeFlag = document.getElementById(uid + '-remove-flag');
        if (removeFlag) removeFlag.value = '0';

        var url = URL.createObjectURL(file);
        if (img) { img.src = url; img.classList.remove('d-none'); }
        if (placeholder) placeholder.classList.add('d-none');

        var size = file.size < 1024 * 1024
            ? (file.size / 1024).toFixed(1) + ' KB'
            : (file.size / (1024 * 1024)).toFixed(1) + ' MB';
        if (info) {
            info.textContent = file.name + ' (' + size + ')';
            info.style.display = '';
        }
    };

    window.__avatarPickerRemove = function(uid) {
        var img = document.getElementById(uid + '-img');
        var placeholder = document.getElementById(uid + '-placeholder');
        var info = document.getElementById(uid + '-info');
        var fileInput = document.getElementById(uid + '-file');
        var removeFlag = document.getElementById(uid + '-remove-flag');

        if (img) { img.src = ''; img.classList.add('d-none'); }
        if (placeholder) placeholder.classList.remove('d-none');
        if (info) { info.textContent = ''; info.style.display = 'none'; }
        if (fileInput) fileInput.value = '';
        if (removeFlag) removeFlag.value = '1';
    };
})();
</script>
