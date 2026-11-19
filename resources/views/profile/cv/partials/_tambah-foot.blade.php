{{-- Kaki formulir tambah yang sedang dibuka: Batal (kosongkan + tutup) dan
     Tambahkan (masukkan ke daftar lalu tutup). $form = nama formulir, $metode = fungsi tambah. --}}
<div class="cvb-add-foot">
    <button type="button" @click="tutupForm('{{ $form }}')" class="cvb-add-btn">
        Batal
    </button>
    <button type="button" @click="{{ $metode }}()" class="cvb-add-btn is-primary">
        <x-icon name="check" size="14" />
        Tambahkan
    </button>
</div>
