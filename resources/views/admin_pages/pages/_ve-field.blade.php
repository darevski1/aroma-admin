{{-- ═══════════════════════════════════════════════════════
     Visual Editor – Single Field Partial
     Variables: $field, $fieldKey, $fieldValue
     ═══════════════════════════════════════════════════════ --}}

@if ($field['type'] === 'image')
    {{-- ── Image Field ──────────────────────────────────── --}}
    <div class="ve-field">
        <label class="ve-field-label">{{ $field['label'] }}</label>
        <div class="ve-image-field" data-field-key="{{ $fieldKey }}">
            <div class="ve-image-thumb {{ $fieldValue ? 'has-image' : '' }}">
                @if ($fieldValue)
                    <img src="{{ $fieldValue }}" alt="">
                @else
                    <i class="material-symbols-rounded placeholder-icon">image</i>
                @endif
            </div>
            <div>
                <div class="ve-image-actions">
                    <button type="button" class="btn btn-sm btn-outline-primary"
                            onclick="openMediaLibrary('{{ $fieldKey }}')">
                        <i class="material-symbols-rounded me-1" style="font-size:14px;">perm_media</i>
                        Change Image
                    </button>
                    @if ($fieldValue)
                        <button type="button" class="btn btn-sm btn-outline-danger ve-image-remove"
                                onclick="clearImage('{{ $fieldKey }}')">
                            <i class="material-symbols-rounded me-1" style="font-size:14px;">delete</i>
                            Remove
                        </button>
                    @endif
                </div>
                <input type="hidden" class="ve-image-url" data-field-key="{{ $fieldKey }}"
                       value="{{ $fieldValue }}">
                @if ($fieldValue)
                    <small class="text-muted d-block mt-1" style="font-size:0.7rem;">{{ basename($fieldValue) }}</small>
                @endif
            </div>
        </div>
    </div>

@elseif ($field['type'] === 'textarea')
    {{-- ── Rich-Text (Quill) Field ─────────────────────── --}}
    <div class="ve-field">
        <label class="ve-field-label">{{ $field['label'] }}</label>
        <div class="ve-quill-editor" data-field-key="{{ $fieldKey }}">{!! old($fieldKey, $fieldValue) !!}</div>
    </div>

@else
    {{-- ── Text Input Field ─────────────────────────────── --}}
    <div class="ve-field">
        <label class="ve-field-label">{{ $field['label'] }}</label>
        <input type="text" class="ve-field-input ve-text-input"
               data-field-key="{{ $fieldKey }}"
               value="{{ old($fieldKey, $fieldValue) }}">
    </div>
@endif
