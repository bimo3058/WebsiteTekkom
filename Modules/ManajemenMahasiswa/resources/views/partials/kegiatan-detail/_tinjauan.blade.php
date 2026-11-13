{{--
    Status alur tinjau ketua pelaksana ↔ KABID (S-82) — dipasang _body di dalam kartu info
    detail Rencana Proker & Pelaksanaan Kegiatan, tepat di bawah judul.

    Tampilan 11 Nov 2026 (permintaan Surya): tanpa stepper. Satu kotak berwarna sesuai status
    berisi label status, waktu kirim, pesan untuk peran yang membuka, kekurangan data, dan
    catatan penolakan yang belum diperbaiki. Nama ketua pelaksana + tombol Ganti ada di kartu
    meta "Ketua Pelaksana" (_body), jadi tidak diulang di sini. Tombol aksi (Edit, Kirim,
    Setujui, Tolak) tetap di header halaman; dialog Tolak & Ganti ketua ada di _tinjauan-dialog.

    Variabel (dari $panelTinjauan halaman show):
      $kegiatan       Kegiatan (muat catatan.penulis, catatan.pemerbaiki, ketuaPelaksana.user)
      $tahap          'rencana' | 'pelaksanaan'
      $kurang         Collection label isian yang belum terpenuhi untuk dikirim
      $canKirim, $canTolak, $canSetujui, $canGantiKetua (bool)
--}}
@php
    $K        = \Modules\ManajemenMahasiswa\Models\Kegiatan::class;
    $status   = $kegiatan->tahap_tinjauan;
    $catatan  = $kegiatan->catatan;
    $terbuka  = $catatan->whereNull('diperbaiki_at');
    // Catatan terbuka sudah tampil penuh di kotak status; riwayat cukup yang sudah diperbaiki.
    $riwayat  = $status ? $catatan->whereNotNull('diperbaiki_at') : $catatan;
    $namaKetua = $kegiatan->ketuaPelaksana?->user?->name ?? 'ketua pelaksana';
    $saya     = $kegiatan->ketuaPelaksana && (int) $kegiatan->ketuaPelaksana->user_id === (int) auth()->id();
    $objek    = $tahap === 'rencana' ? 'rencana proker' : 'laporan pelaksanaan';
    $tombolSetujui = $tahap === 'rencana' ? 'Setujui & Teruskan ke Pelaksanaan' : 'Setujui & Unggah ke Arsip';
    $peninjau = $canSetujui || $canTolak || $canGantiKetua;

    $pesan = match (true) {
        $status === $K::TINJAU_DILENGKAPI && $saya
            => "Anda ketua pelaksananya. Lengkapi data {$objek} ini lewat tombol Edit, lalu tekan Kirim untuk Ditinjau.",
        $status === $K::TINJAU_DILENGKAPI && $peninjau
            => "Menunggu {$namaKetua} melengkapi data {$objek} ini. Tombol {$tombolSetujui} muncul setelah ia mengirimnya untuk ditinjau.",
        $status === $K::TINJAU_DILENGKAPI
            => "Menunggu {$namaKetua} melengkapi data {$objek} ini.",
        $status === $K::TINJAU_DITINJAU && $peninjau
            => "Periksa data di bawah. Bila sudah sesuai, tekan {$tombolSetujui}. Bila belum, tekan Tolak dengan catatan.",
        $status === $K::TINJAU_DITINJAU && $saya
            => 'Sudah Anda kirim. Data dikunci sampai KABID menyetujui atau mengembalikannya dengan catatan.',
        $status === $K::TINJAU_DITINJAU
            => "Sudah dikirim {$namaKetua} dan menunggu tinjauan KABID. Data dikunci selama ditinjau.",
        $status === $K::TINJAU_PERBAIKAN && $saya
            => 'Dikembalikan dengan catatan di bawah. Tekan Perbaiki, sesuaikan datanya, lalu kirim lagi untuk ditinjau.',
        $status === $K::TINJAU_PERBAIKAN
            => "Menunggu {$namaKetua} memperbaiki sesuai catatan di bawah, lalu mengirimnya lagi.",
        default => null,
    };
@endphp

@if($status)
<div class="mk-tinjau mk-tinjau--{{ $K::TINJAUAN_NADA[$status] }}" role="status" aria-label="Status tinjauan">
    <span class="mk-tinjau__ikon" aria-hidden="true">
        @if($status === $K::TINJAU_DITINJAU)
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
        @elseif($status === $K::TINJAU_PERBAIKAN)
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 14 4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 0 11H11"/></svg>
        @else
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/></svg>
        @endif
    </span>

    <div class="mk-tinjau__badan">
        <div class="mk-tinjau__kepala">
            <span class="mk-tinjau__judul">{{ $K::TINJAUAN_LABELS[$status] }}</span>
            @if($status === $K::TINJAU_DITINJAU && $kegiatan->dikirim_tinjau_at)
                <span class="mk-tinjau__waktu">Dikirim {{ $kegiatan->dikirim_tinjau_at->locale('id')->translatedFormat('j M Y, H.i') }}</span>
            @endif
        </div>

        @if($pesan)
            <p class="mk-tinjau__pesan">{{ $pesan }}</p>
        @endif

        @if($canKirim && $kurang->isNotEmpty())
            <p class="mk-tinjau__kurang">Belum bisa dikirim. Lengkapi dulu: {{ $kurang->implode(', ') }}.</p>
        @endif

        @foreach($terbuka as $c)
            <div class="mk-tinjau__catatan">
                <div class="mk-tinjau__catatan-kepala">Catatan {{ $c->penulis?->name ?? 'pengguna terhapus' }} · {{ $c->created_at->locale('id')->translatedFormat('j M Y, H.i') }}</div>
                <div class="mk-tinjau__catatan-isi">{{ $c->isi }}</div>
            </div>
        @endforeach

        @if($riwayat->isNotEmpty())
            @include('manajemenmahasiswa::partials.kegiatan-detail._tinjauan-riwayat', ['riwayat' => $riwayat])
        @endif
    </div>
</div>
@elseif($riwayat->isNotEmpty())
{{-- Sudah lewat tahap tinjau (mis. sudah diarsipkan): cukup riwayatnya --}}
<div class="mk-tinjau-lepas">
    @include('manajemenmahasiswa::partials.kegiatan-detail._tinjauan-riwayat', ['riwayat' => $riwayat])
</div>
@endif

@once
<style>
    .mk-tinjau { --t-fg: var(--c-sky); --t-bg: color-mix(in srgb, var(--c-sky-subtle) 40%, var(--c-surface));
        --t-border: color-mix(in srgb, var(--c-sky-50) 70%, transparent); --t-ikon: var(--c-sky-subtle);
        display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; border-radius: 10px;
        background: var(--t-bg); border: 1px solid var(--t-border); }
    .mk-tinjau--warning { --t-fg: var(--c-warning); --t-bg: color-mix(in srgb, var(--c-warning-subtle) 40%, var(--c-surface));
        --t-border: var(--c-warning-50); --t-ikon: var(--c-warning-subtle); }
    .mk-tinjau--error { --t-fg: var(--c-error-200); --t-bg: var(--c-error-0);
        --t-border: color-mix(in srgb, var(--c-error-50) 60%, transparent); --t-ikon: var(--c-error-subtle); }
    .mk-tinjau__ikon { flex: none; width: 32px; height: 32px; border-radius: 8px; display: grid; place-items: center;
        background: var(--t-ikon); color: var(--t-fg); }
    .mk-tinjau__badan { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 4px; }
    .mk-tinjau__kepala { display: flex; align-items: baseline; flex-wrap: wrap; gap: 2px 10px; min-height: 22px; }
    .mk-tinjau__judul { font-size: 14px; font-weight: 700; color: var(--t-fg); line-height: 1.4; }
    .mk-tinjau__waktu { font-size: 12px; font-weight: 500; color: var(--c-fg-muted); }
    .mk-tinjau__pesan { margin: 0; font-size: 13.5px; line-height: 1.55; color: var(--c-fg-sec); }
    .mk-tinjau__kurang { margin: 2px 0 0; font-size: 13px; font-weight: 600; color: var(--c-warning); }
    .mk-tinjau__catatan { margin-top: 6px; padding: 10px 12px; border: 1px solid var(--c-border); border-left: 3px solid var(--c-error);
        border-radius: 8px; background: var(--c-surface); }
    .mk-tinjau__catatan-kepala { font-size: 12px; font-weight: 600; color: var(--c-fg-muted); }
    .mk-tinjau__catatan-isi { margin-top: 2px; font-size: 14px; line-height: 1.5; color: var(--c-fg); white-space: pre-line; overflow-wrap: anywhere; }
    .mk-tinjau-lepas { margin-top: 4px; padding: 10px 14px; border: 1px solid var(--c-border); border-radius: 10px; background: var(--c-surface-subtle); }
    @media (max-width: 575px) {
        .mk-tinjau { padding: 12px; gap: 10px; }
        .mk-tinjau__ikon { width: 28px; height: 28px; }
    }
</style>
@endonce
