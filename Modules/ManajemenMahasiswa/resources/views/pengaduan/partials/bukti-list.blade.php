{{--
    Daftar bukti dukung sebuah tiket. Param: $pengaduan, $token (opsional: bila ada,
    tautan memakai route magic link sehingga pelapor konfidensial bisa membukanya).
    Path storage tidak pernah ditulis ke halaman; hanya indeks berkas.
--}}
@php
    $buktiFiles = array_values((array) data_get($pengaduan, 'data_template.bukti', []));
    $legacyLink = data_get($pengaduan, 'data_template.link_bukti'); // tiket lama sebelum bukti berupa berkas

    $buktiItems = \Modules\ManajemenMahasiswa\Support\PengaduanBukti::toItems(
        $buktiFiles,
        fn (int $i) => isset($token)
            ? route('manajemenmahasiswa.pengaduan.anon.bukti', ['token' => $token, 'index' => $i])
            : route('manajemenmahasiswa.pengaduan.bukti', ['pengaduan' => $pengaduan->id, 'index' => $i])
    );
@endphp
@if (count($buktiItems) > 0 || $legacyLink)
    @include('manajemenmahasiswa::pengaduan.partials.bukti-items', ['items' => $buktiItems])
    @if ($legacyLink)
        <div class="bk-list">
            {{-- Tautan eksternal tidak bisa disematkan di pop-up, jadi tetap buka tab baru. --}}
            <a href="{{ $legacyLink }}" target="_blank" rel="noopener noreferrer" class="bk-row">
                <span class="bk-icon">URL</span>
                <span class="bk-text">
                    <span class="bk-name">Lihat Bukti (tautan)</span>
                    <span class="bk-sub">Tautan eksternal</span>
                </span>
            </a>
        </div>
    @endif
@else
    <span style="color: #cbd5e1;">—</span>
@endif
