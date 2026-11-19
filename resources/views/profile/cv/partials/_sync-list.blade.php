{{--
    Daftar data Auto-Sync (diambil dari sistem, tidak bisa diubah di CV Builder).
    Satu bentuk untuk semua langkah — judul dengan ikon sync, kartu bertanda
    Auto-Sync, kotak kosong, tinggi maksimal — supaya Pendidikan, Pengalaman,
    Kegiatan, dan Prestasi tampil sama. Barisnya sudah dinormalkan oleh cvWizard()
    menjadi { judul, sub, meta }.

    Parameter:
      $judul   judul daftar
      $items   nama getter Alpine di cvWizard(), mis. 'syncPendidikan'
      $kosong  teks bila daftar kosong
--}}
<div>
    <h4 class="cvb-heading is-sync">
        <x-icon name="sync" size="18" />
        {{ $judul }}
    </h4>
    <div class="cvb-sync-list space-y-3">
        <template x-for="(it, index) in {{ $items }}" :key="index">
            <div class="cvb-item is-sync">
                <div>
                    <h5 class="cvb-item-title" x-text="it.judul"></h5>
                    <p class="cvb-item-sub" x-show="it.sub" x-text="it.sub"></p>
                    <p class="cvb-item-meta" x-show="it.meta" x-text="it.meta"></p>
                </div>
                <span class="cvb-chip">Auto-Sync</span>
            </div>
        </template>
        <div x-show="{{ $items }}.length === 0" class="cvb-empty">{{ $kosong }}</div>
    </div>
</div>
