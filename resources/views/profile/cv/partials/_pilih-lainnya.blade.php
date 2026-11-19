{{--
    Kolom "pilih dari daftar yang umum, atau Lainnya" untuk formulir tambah manual.
    Memilih daftar mengisi kolom state langsung; memilih "Lainnya" membuka kotak
    isian di bawahnya supaya pengguna menulis sendiri. Nilai akhir selalu satu string
    di $form.$name, jadi payload & template CV tidak berubah.

    Parameter:
      $form         nama state Alpine di cvWizard(), mis. 'newLang'
      $name         nama field di state itu, mis. 'nama'
      $label        teks label
      $required     (opsional) tanda * — dicek saat Tambah ditekan
      $options      daftar pilihan (array string), ATAU
      $groups       daftar pilihan berkelompok ['Judul kelompok' => [..]]
      $placeholder  teks tombol sebelum memilih, mis. 'Pilih bahasa'
      $lainnyaHint  placeholder kotak isian bila memilih Lainnya
--}}
@php
    $required     = $required ?? false;
    $options      = $options ?? [];
    $groups       = $groups ?? null;
    $placeholder  = $placeholder ?? 'Pilih salah satu';
    $lainnyaHint  = $lainnyaHint ?? 'Tulis sendiri';
    $inputId      = 'cvb-' . $form . '-' . $name;
    $target       = "'{$form}', '{$name}'";
    $kunci        = "{$form}.{$name}";
    $daftar       = $groups ?? ['' => $options];
@endphp
<div x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
    <label for="{{ $inputId }}-pilih" class="form-label-custom">
        {{ $label }}@if($required) <span class="required">*</span>@endif
    </label>

    <div class="relative">
        <button type="button" id="{{ $inputId }}-pilih" @click="open = !open"
                class="form-control-custom cvb-select-btn flex items-center justify-between gap-2 text-left"
                :class="{ 'is-open': open, 'is-invalid': hasError({{ $target }}) && !lainnya['{{ $kunci }}'] }">
            <span class="truncate"
                  :style="({{ $form }}.{{ $name }} || lainnya['{{ $kunci }}']) ? '' : 'color: var(--c-fg-placeholder);'"
                  x-text="lainnya['{{ $kunci }}'] ? 'Lainnya' : ({{ $form }}.{{ $name }} || @js($placeholder))"></span>
            <svg class="shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                 width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" style="color: var(--c-fg-placeholder);">
                <path d="M6 9l6 6 6-6"/>
            </svg>
        </button>

        <div x-show="open"
             x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="absolute left-0 top-full mt-1 w-full rounded-lg overflow-hidden py-1 max-h-56 overflow-y-auto z-[100]"
             style="display: none; background: #fff; border: 1px solid var(--c-border); box-shadow: 0 10px 25px rgba(0,0,0,.1);">
            @foreach($daftar as $kelompok => $isi)
                @if($kelompok !== '')
                    <p class="cvb-opt-group">{{ $kelompok }}</p>
                @endif
                @foreach($isi as $opsi)
                    <button type="button" @click="pilihOpsi({{ $target }}, @js($opsi)); open = false"
                            class="cvb-opt" :class="{ 'is-selected': {{ $form }}.{{ $name }} === @js($opsi) && !lainnya['{{ $kunci }}'] }">
                        {{ $opsi }}
                    </button>
                @endforeach
            @endforeach
            <button type="button" @click="pilihLainnya({{ $target }}); open = false"
                    class="cvb-opt is-last" :class="{ 'is-selected': lainnya['{{ $kunci }}'] }">
                Lainnya…
            </button>
        </div>
    </div>

    <template x-if="lainnya['{{ $kunci }}']">
        <input id="{{ $inputId }}" type="text" x-model="{{ $form }}.{{ $name }}" placeholder="{{ $lainnyaHint }}" autocomplete="off"
               class="form-control-custom mt-2" :class="{ 'is-invalid': hasError({{ $target }}) }"
               @input="clearError({{ $target }})">
    </template>

    <p class="cvb-field-error" x-show="hasError({{ $target }})" x-text="errors['{{ $form }}']?.['{{ $name }}']" style="display: none;"></p>
</div>
