<x-manajemenmahasiswa::layouts.mahasiswa>

@include('manajemenmahasiswa::partials.kegiatan-theme')

{{-- Kartu, badge, meta grid & lightbox dipakai bersama dengan Pelaksanaan dan Laporan & Arsip.
     Konfirmasi Hapus & Ajukan memakai dialog SITKOM bersama (mkConfirmSubmit). --}}
@include('manajemenmahasiswa::partials.kegiatan-detail._styles')

@php
    // Field yang masih kosong — dipakai untuk tooltip tombol Ajukan
    $kelengkapanKurang = collect($kelengkapan ?? [])->reject(fn($i) => $i['terisi'])->pluck('label');
    $rencanaLengkap    = $kelengkapanKurang->isEmpty();

    // Peringatan jadwal (S-81) ikut ditampilkan di dialog Ajukan — peringatan, bukan larangan.
    $peringatanAjukan = collect();
    if ($cekJadwal) {
        foreach ($cekJadwal['bentrok'] as $b) {
            $peringatanAjukan->push(($b['ruang_sama'] ? 'Ruang & waktu sama: ' : 'Waktu beririsan: ') . $b['judul'] . ' (' . $b['bidang'] . ', ' . $b['jam'] . ', ' . $b['lokasi'] . ').');
        }
        if ($cekJadwal['ruangan'] && $cekJadwal['ruangan']['status'] !== 'tersedia') {
            $peringatanAjukan->push('Ruangan ' . $cekJadwal['ruangan']['nama'] . ': ' . ($cekJadwal['ruangan']['alasan'] ?? $cekJadwal['ruangan']['status']) . '.');
        }
    }
    $pesanAjukan = 'Rencana proker "' . $proker->judul . '" disetujui dan diteruskan ke Pelaksanaan Kegiatan. Rencana ini dikunci dan tetap tersimpan di Rencana Proker, sedangkan salinannya beserta dokumen rencana dibuat di Pelaksanaan Kegiatan untuk diisi realisasinya oleh ketua pelaksana.'
        . ($peringatanAjukan->isNotEmpty() ? "\n\nPerhatikan jadwal:\n• " . $peringatanAjukan->implode("\n• ") : '')
        . ($proker->ruangan_id ? "\n\nIngat: mengajukan proker tidak memesan ruangan. Pinjam ruangannya lewat Manajemen Ruangan." : '');
@endphp

{{-- Header --}}
<x-manajemenmahasiswa::ui.page-header bordered title="Detail Rencana Proker">
    Dibuat oleh <span style="color:var(--c-fg-sec);font-weight:600;">{{ $proker->creator?->name ?? '-' }}</span>
    &bull; {{ $proker->created_at->locale('id')->translatedFormat('d M Y') }}

    <x-slot:leading>
        <a href="{{ route('manajemenmahasiswa.proker.index') }}" class="mk-btn mk-btn--secondary mk-btn--icon mk-btn--sm" aria-label="Kembali"><svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15 19l-7-7 7-7"/></svg></a>
    </x-slot:leading>

    <x-slot:actions>
    {{--
        Badge status sengaja TIDAK dirender di sini: tingginya (28px + jarak 8px)
        menjadikan blok kanan dua tingkat dan mendorong baris tombol turun sampai
        menempel banner. Status tinjauan (S-82) tampil di kartu info, di bawah judul.

        Tombol per peran (S-82): ketua pelaksana & Akses Kelola → Edit; ketua pelaksana →
        Kirim untuk Ditinjau; KABID dkk./KAHIM/admin → Hapus, dan saat Menunggu Tinjauan
        Tolak dengan catatan + Setujui & Teruskan. GPM/Kadep/DPM tidak melihat tombol apa pun.
    --}}
    <div class="d-flex flex-column align-items-end">
        <div class="d-flex gap-2 flex-wrap align-items-start justify-content-end">
            @if($canEdit)
                <a href="{{ route('manajemenmahasiswa.proker.edit', $proker->id) }}"
                   class="mk-btn mk-btn--{{ $canKirim && $rencanaLengkap ? 'secondary' : 'primary' }} mk-btn--sm">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                    {{ $proker->tahap_tinjauan === 'perbaikan' ? 'Perbaiki' : 'Edit' }}
                </a>
            @endif
            {{-- $canDelete sudah memuat aturan tahap: draft, atau rencana diajukan tanpa salinan --}}
            @if($canDelete)
                @include('manajemenmahasiswa::partials.kegiatan-detail._tombol-hapus', [
                    'action'   => route('manajemenmahasiswa.proker.destroy', $proker->id),
                    'jenis'    => 'Rencana Proker',
                    'judul'    => $proker->judul,
                    'lampiran' => $documents->isNotEmpty() ? 'banner dan dokumen rencananya' : 'banner-nya',
                ])
            @endif
            @if($canKirim)
                @if($rencanaLengkap)
                    <form action="{{ route('manajemenmahasiswa.proker.kirim', $proker->id) }}" method="POST" class="m-0"
                          onsubmit="return mkConfirmSubmit(this, @js('Rencana "' . $proker->judul . '" akan dikirim ke KABID untuk ditinjau. Selama ditinjau datanya dikunci; bila ada yang perlu diperbaiki, KABID mengembalikannya dengan catatan.'), { title: 'Kirim untuk Ditinjau', variant: 'primary', confirmText: 'Ya, Kirim' })">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="mk-btn mk-btn--primary mk-btn--sm">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9 22 2z"/></svg>
                            Kirim untuk Ditinjau
                        </button>
                    </form>
                @else
                    <button type="button" class="mk-btn mk-btn--secondary mk-btn--sm mk-btn--disabled" disabled
                            title="Lengkapi dulu: {{ $kelengkapanKurang->implode(', ') }}">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9 22 2z"/></svg>
                        Kirim untuk Ditinjau
                    </button>
                @endif
            @endif
            @if($canTolak)
                <button type="button" class="mk-btn mk-btn--secondary mk-btn--sm" style="color:#B42318;border-color:#FDA29B;"
                        @click="$dispatch('mk-buka-tolak')">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 14 4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 0 11H11"/></svg>
                    Tolak dengan catatan
                </button>
            @endif
            @if($canAjukan)
                @if($rencanaLengkap)
                    <form action="{{ route('manajemenmahasiswa.proker.ajukan', $proker->id) }}" method="POST" class="m-0"
                          onsubmit="return mkAjukanProker(this, @js($pesanAjukan))">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="mk-btn mk-btn--primary mk-btn--sm">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Setujui &amp; Teruskan ke Pelaksanaan
                        </button>
                    </form>
                @else
                    {{-- Data lama yang dikirim sebelum syarat bertambah: kembalikan dengan catatan --}}
                    <button type="button" class="mk-btn mk-btn--secondary mk-btn--sm mk-btn--disabled" disabled
                            title="Belum lengkap: {{ $kelengkapanKurang->implode(', ') }}">
                        Setujui &amp; Teruskan ke Pelaksanaan
                    </button>
                @endif
            @endif
        </div>
        {{-- Tooltip tombol nonaktif tidak terlihat di layar sentuh, jadi kekurangannya ditulis --}}
        @if($canAjukan && !$rencanaLengkap)
            <p class="mb-0 mt-1 text-end" style="max-width:340px;font-size:12px;color:var(--c-fg-muted);font-weight:500;">
                Belum bisa disetujui. Kurang: {{ $kelengkapanKurang->implode(', ') }}. Kembalikan dengan catatan.
            </p>
        @endif
        {{-- Staff sebidang yang bukan ketua pelaksana/pengelola proker ini (KegiatanPolicy) --}}
        @if($pesanBukanPengelola)
            <p class="mb-0 mt-1 text-end" style="max-width:340px;font-size:12px;color:var(--c-fg-muted);font-weight:500;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:4px;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>{{ $pesanBukanPengelola }}
            </p>
        @endif
    </div>
    </x-slot:actions>
</x-manajemenmahasiswa::ui.page-header>

<x-manajemenmahasiswa::ui.flash type="success" :message="session('success')" class="mb-3" />
<x-manajemenmahasiswa::ui.flash type="error" :message="session('error')" class="mb-3" />
<x-manajemenmahasiswa::ui.flash type="error" :messages="$errors->all()" class="mb-3" />

{{-- Badan detail dipakai bersama dengan Pelaksanaan; tanpa galeri foto, dengan Dokumen Rencana.
     Status tinjau (S-82) & cek jadwal (S-81) tampil di dalam kartu info _body. --}}
@include('manajemenmahasiswa::partials.kegiatan-detail._body', [
    'showDokumentasi' => false,
    'showDokumen'     => true,
    'cekJadwal'       => $cekJadwal,
    'panelTinjauan'   => [
        'kegiatan'      => $proker,
        'tahap'         => 'rencana',
        'kurang'        => $kelengkapanKurang,
        'canKirim'      => $canKirim,
        'canTolak'      => $canTolak,
        'canSetujui'    => $canAjukan,
        'canGantiKetua' => $canGantiKetua,
        'calonKetua'    => $calonKetua,
        'routeTolak'    => route('manajemenmahasiswa.proker.tolak', $proker->id),
        'routeKetua'    => route('manajemenmahasiswa.proker.ketua', $proker->id),
    ],
])

{{-- Baris lama (sebelum S-49) yang berpindah status: link ke barisnya sendiri --}}
@if($proker->status === 'disetujui')
<div class="detail-card" style="background:var(--c-primary-subtle);border:1px solid var(--c-primary-border);">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <div style="font-weight:700;color:var(--c-primary-hover);margin-bottom:4px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:6px;"><path d="M22 2L11 13"/><path d="M22 2L15 22 11 13 2 9 22 2z"/></svg>Proker Diajukan!</div>
            <div style="font-size:14px;color:var(--c-primary);font-weight:500;">Cek kesesuaian data dengan realisasi di halaman Pelaksanaan Kegiatan.</div>
        </div>
        <a href="{{ route('manajemenmahasiswa.pelaksanaan.show', $proker->id) }}"
           class="mk-btn mk-btn--primary">
            Lihat di Pelaksanaan &rarr;
        </a>
    </div>
</div>
@endif

@if($proker->status === 'selesai')
<div class="detail-card" style="background:var(--c-success-subtle);border:1px solid var(--c-success-border);">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <div style="font-weight:700;color:var(--c-success);margin-bottom:4px;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;margin-right:6px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>Kegiatan Selesai &amp; Diarsipkan</div>
            <div style="font-size:14px;color:var(--c-success);font-weight:500;">Laporan akhir kegiatan ini ada di halaman Laporan &amp; Arsip.</div>
        </div>
        <a href="{{ route('manajemenmahasiswa.kegiatan.show', $proker->id) }}"
           class="mk-btn mk-btn--primary">
            Lihat di Arsip &rarr;
        </a>
    </div>
</div>
@endif

@include('manajemenmahasiswa::partials.kegiatan-detail._lightbox')

<script>
// Ajukan menyalin banner & dokumen rencana ke Pelaksanaan (S-49) dan bisa makan waktu
// belasan detik. Tanpa keterangan, halaman tampak macet dan tombol rawan diklik ulang.
function mkAjukanProker(form, pesan) {
    window.mkConfirm({ message: pesan, title: 'Setujui & Teruskan ke Pelaksanaan', variant: 'primary', confirmText: 'Ya, Setujui' })
        .then(function (ok) {
            if (!ok) return;
            const tombol = form.querySelector('button[type="submit"]');
            if (tombol) {
                tombol.disabled = true;
                tombol.textContent = 'Menyalin data & dokumen…';
            }
            form.submit();
        });

    return false;
}
</script>

</x-manajemenmahasiswa::layouts.mahasiswa>
