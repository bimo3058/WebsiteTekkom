@props([
    'name'     => 'file',
    'accept'   => '.pdf',
    'maxLabel' => 'PDF (Maks. 1MB)',
    'disabled' => false,
    'required' => false,
    'inputId'  => null,
])
@php
    $inputId = $inputId ?? 'upload-' . $name;
@endphp

<label class="upload-zone {{ $disabled ? 'closed' : '' }}" id="zone-{{ $inputId }}">
    <input
        type="file"
        name="{{ $name }}"
        id="{{ $inputId }}"
        accept="{{ $accept }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        onchange="handleUploadChange('{{ $inputId }}')">
    <i class="fas fa-cloud-upload-alt" id="icon-{{ $inputId }}"></i>
    <strong id="text-{{ $inputId }}">
        {{ $disabled ? 'Upload tidak tersedia' : 'Klik untuk unggah atau seret file ke sini' }}
    </strong>
    <span id="sub-{{ $inputId }}">{{ $maxLabel }}</span>
</label>

<script>
if (typeof handleUploadChange === 'undefined') {
    function handleUploadChange(inputId) {
        const input  = document.getElementById(inputId);
        if (!input || !input.files[0]) return;

        const file   = input.files[0];
        const textEl = document.getElementById('text-' + inputId);
        const subEl  = document.getElementById('sub-'  + inputId);
        const iconEl = document.getElementById('icon-' + inputId);

        if (textEl) textEl.textContent = file.name;

        if (subEl) {
            const kb = file.size / 1024;
            subEl.textContent = kb >= 1024
                ? (kb / 1024).toFixed(1) + ' MB'
                : kb.toFixed(0) + ' KB';
        }

        if (iconEl) {
            iconEl.className   = 'fas fa-file-check';
            iconEl.style.color = 'var(--primary-blue)';
        }
    }
}
</script>
