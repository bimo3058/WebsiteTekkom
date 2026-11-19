{{--
    Satu kolom isian di formulir "tambah manual" langkah 2–5 CV Builder. Label,
    tanda wajib, kotak isian, petunjuk, dan pesan galat dibuat di satu tempat
    supaya semua langkah tampil sama (gaya .form-label-custom / .form-control-custom
    dari partials/sitkom-ui; tanda wajib & keadaan .is-invalid ikut kit itu).

    Parameter:
      $form         nama state Alpine di cvWizard(), mis. 'newEdu'
      $name         nama field di state itu, mis. 'institusi'
      $label        teks label
      $required     (opsional) beri tanda * — dicek saat tombol Tambah ditekan
      $placeholder  (opsional)
      $hint         (opsional) petunjuk kecil di bawah kotak
      $year         (opsional) isian tahun: papan angka + maksimal 4 digit
      $type         (opsional) tipe <input>, bawaan text
      $rows         (opsional) bila diisi, tampil sebagai textarea sebanyak baris itu
--}}
@php
    $required    = $required ?? false;
    $placeholder = $placeholder ?? '';
    $hint        = $hint ?? null;
    $year        = $year ?? false;
    $type        = $type ?? 'text';
    $rows        = $rows ?? null;
    $inputId     = 'cvb-' . $form . '-' . $name;
    $target      = "'{$form}', '{$name}'";
@endphp
<div>
    <label for="{{ $inputId }}" class="form-label-custom">
        {{ $label }}@if($required) <span class="required">*</span>@endif
    </label>

    @if($rows)
        <textarea id="{{ $inputId }}" x-model="{{ $form }}.{{ $name }}" rows="{{ $rows }}"
                  class="form-control-custom" placeholder="{{ $placeholder }}"
                  :class="{ 'is-invalid': hasError({{ $target }}) }"
                  @input="clearError({{ $target }})"></textarea>
    @else
        <input id="{{ $inputId }}" type="{{ $type }}" x-model="{{ $form }}.{{ $name }}"
               class="form-control-custom" placeholder="{{ $placeholder }}"
               @if($year) inputmode="numeric" maxlength="4" autocomplete="off" @endif
               :class="{ 'is-invalid': hasError({{ $target }}) }"
               @input="clearError({{ $target }})">
    @endif

    <p class="cvb-field-error" x-show="hasError({{ $target }})" x-text="errors['{{ $form }}']?.['{{ $name }}']" style="display: none;"></p>
    @if($hint)
        <p class="form-hint">{{ $hint }}</p>
    @endif
</div>
