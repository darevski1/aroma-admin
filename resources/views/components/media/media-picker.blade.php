@props([
    'name',
    'collection',
    'model' => null,
    'current' => null,
    'multiple' => false,
    'accept' => '*',
    'label' => 'Media',
])

@php
    $uniqueId = 'media-picker-' . $name . '-' . uniqid();
    $removeName = 'remove_' . $collection;
@endphp

<div class="mb-3" id="{{ $uniqueId }}">
    <label class="text-xs font-weight-bolder mb-1 text-dark">{{ $label }}</label>

    {{-- Current media display --}}
    <div class="media-picker-current" id="{{ $uniqueId }}-current">
        @if($current)
            <div class="d-flex align-items-center gap-3 p-3 border rounded bg-light mb-2">
                <div class="media-picker-thumb flex-shrink-0" style="width:80px;height:80px;overflow:hidden;border-radius:8px;background:#f0f0f0;">
                    @if(str_starts_with($current->mime_type ?? '', 'image/'))
                        <img src="{{ $current->getUrl() }}" alt="{{ $current->name }}" style="width:100%;height:100%;object-fit:cover;">
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <i class="material-symbols-rounded text-muted" style="font-size:32px;">description</i>
                        </div>
                    @endif
                </div>
                <div class="flex-grow-1 min-width-0">
                    <div class="text-sm font-weight-bolder text-dark text-truncate">{{ $current->name }}</div>
                    <div class="text-xs text-muted">{{ $current->file_name }} &middot; {{ number_format(($current->size ?? 0) / 1024, 1) }} KB</div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="document.getElementById('{{ $uniqueId }}-file').click();">
                        <i class="material-symbols-rounded me-1" style="font-size:14px;">edit</i> Change
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Drag and drop zone --}}
    <div class="media-picker-dropzone position-relative border-2 border-dashed rounded p-4 text-center {{ $current ? 'd-none' : '' }}"
         id="{{ $uniqueId }}-dropzone"
         style="border-color:#dee2e6;cursor:pointer;min-height:120px;transition:all .2s;"
         ondragover="event.preventDefault();this.style.borderColor='#495057';this.style.background='#f8f9fa';"
         ondragleave="event.preventDefault();this.style.borderColor='#dee2e6';this.style.background='';"
         ondrop="event.preventDefault();this.style.borderColor='#dee2e6';this.style.background='';document.getElementById('{{ $uniqueId }}-file').files=event.dataTransfer.files;document.getElementById('{{ $uniqueId }}-file').dispatchEvent(new Event('change'));"
         onclick="document.getElementById('{{ $uniqueId }}-file').click();">
        <i class="material-symbols-rounded text-muted mb-2" style="font-size:36px;">cloud_upload</i>
        <div class="text-sm text-muted mb-1">Drag & drop or click to select</div>
        <div class="text-xs text-muted">
            @if($accept !== '*')
                Accepted: {{ $accept }}
            @else
                All file types
            @endif
            &middot; Max 10 MB
        </div>
    </div>

    {{-- File input --}}
    <input type="file"
           name="{{ $name }}"
           id="{{ $uniqueId }}-file"
           class="d-none"
           {{ $multiple ? 'multiple' : '' }}
           @if($accept !== '*') accept="{{ $accept }}" @endif
           onchange="window.__mediaPickerHandleFiles('{{ $uniqueId }}', this)">

    {{-- Upload preview --}}
    <div class="media-picker-preview mt-2" id="{{ $uniqueId }}-preview" style="display:none;"></div>

    {{-- Remove button for existing media --}}
    @if($current)
        <input type="hidden" name="{{ $removeName }}" value="0" id="{{ $uniqueId }}-remove-flag">
        <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="window.__mediaPickerRemove('{{ $uniqueId }}')">
            <i class="material-symbols-rounded me-1" style="font-size:14px;">delete</i> Remove
        </button>
    @endif

    {{-- Validation error --}}
    @error($name)
        <div class="text-danger text-xs mt-1">{{ $message }}</div>
    @enderror
</div>

<script>
(function() {
    window.__mediaPickerRemove = function(uid) {
        var container = document.getElementById(uid);
        if (!container) return;
        var flag = container.querySelector('[id$="-remove-flag"]');
        if (flag) flag.value = '1';
        var current = container.querySelector('.media-picker-current');
        if (current) current.innerHTML = '';
        var dropzone = container.querySelector('[id$="-dropzone"]');
        if (dropzone) dropzone.classList.remove('d-none');
        var fileInput = container.querySelector('input[type="file"]');
        if (fileInput) fileInput.value = '';
        var preview = container.querySelector('[id$="-preview"]');
        if (preview) { preview.innerHTML = ''; preview.style.display = 'none'; }
    };

    window.__mediaPickerHandleFiles = function(uid, input) {
        var container = document.getElementById(uid);
        if (!container) return;
        var preview = container.querySelector('[id$="-preview"]');
        if (!preview) return;
        preview.innerHTML = '';
        preview.style.display = input.files.length ? '' : 'none';

        Array.from(input.files).forEach(function(file) {
            var card = document.createElement('div');
            card.className = 'd-flex align-items-center gap-3 p-2 border rounded bg-light mb-2';

            var thumbHtml = '';
            if (file.type.startsWith('image/')) {
                var url = URL.createObjectURL(file);
                thumbHtml = '<div class="flex-shrink-0" style="width:60px;height:60px;border-radius:6px;overflow:hidden;background:#f0f0f0;">' +
                    '<img src="' + url + '" alt="" style="width:100%;height:100%;object-fit:cover;">' +
                    '</div>';
            } else {
                thumbHtml = '<div class="flex-shrink-0 d-flex align-items-center justify-content-center" style="width:60px;height:60px;border-radius:6px;background:#f0f0f0;">' +
                    '<i class="material-symbols-rounded text-muted" style="font-size:28px;">description</i>' +
                    '</div>';
            }

            var size = file.size < 1024 * 1024
                ? (file.size / 1024).toFixed(1) + ' KB'
                : (file.size / (1024 * 1024)).toFixed(1) + ' MB';

            card.innerHTML = thumbHtml +
                '<div class="flex-grow-1 min-width-0">' +
                    '<div class="text-sm font-weight-bolder text-dark text-truncate">' + file.name + '</div>' +
                    '<div class="text-xs text-muted">' + size + '</div>' +
                '</div>' +
                '<button type="button" class="btn btn-sm btn-outline-danger flex-shrink-0" onclick="this.closest(\'.d-flex\').remove();var p=document.getElementById(uid+\'-preview\');if(p&&!p.children.length)p.style.display=\'none\';">' +
                    '<i class="material-symbols-rounded" style="font-size:16px;">close</i>' +
                '</button>';
            preview.appendChild(card);
        });
    };
})();
</script>
