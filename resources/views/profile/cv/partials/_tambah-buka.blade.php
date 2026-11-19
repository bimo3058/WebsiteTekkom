{{-- Tombol pembuka formulir tambah manual. Formulirnya baru tampil setelah tombol
     ini ditekan, supaya langkah tidak penuh formulir kosong (R-35).
     $form = nama formulir di cvWizard (mis. newProj), $label = teks tombol. --}}
<div x-show="!terbuka.{{ $form }}">
    <button type="button" @click="bukaForm('{{ $form }}')" class="cvb-add-btn">
        <x-icon name="plus" size="14" />
        {{ $label }}
    </button>
</div>
