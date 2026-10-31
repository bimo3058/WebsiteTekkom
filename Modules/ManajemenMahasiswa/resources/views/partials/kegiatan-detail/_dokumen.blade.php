{{--
    Kartu "Dokumen & Laporan" detail kegiatan — dipakai _body untuk Pelaksanaan &
    Laporan & Arsip, dan untuk Dokumen Rencana di Rencana Proker (S-49). Hanya untuk
    role KegiatanPolicy::LIHAT_INTERNAL ($canViewRestricted).

    Dokumen dikelompokkan per jenis (S-56) mengikuti urutan JenisDokumenKegiatan::LABEL;
    dokumen lama tanpa jenis ditaruh paling akhir sebagai "Belum dikelompokkan".

    Variabel: $documents, $canViewRestricted, $judulKartu (opsional).
--}}
@if($documents->count() > 0 && isset($canViewRestricted) && $canViewRestricted)
@php
    $urutanJenis = array_flip(array_keys(\Modules\ManajemenMahasiswa\Support\JenisDokumenKegiatan::LABEL));
    $kelompokDokumen = $documents
        ->groupBy(fn ($doc) => $doc->jenis_dokumen ?? '')
        ->sortBy(fn ($isi, $jenis) => $urutanJenis[$jenis] ?? PHP_INT_MAX);
@endphp
<div class="detail-card">
    <div class="gallery-header">
        <div class="detail-card-title" style="margin-bottom: 0;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline></svg> {{ $judulKartu ?? 'Dokumen & Laporan' }}</div>
        <span class="gallery-count">{{ $documents->count() }} dokumen</span>
    </div>
    @foreach($kelompokDokumen as $jenis => $isiKelompok)
    <div class="document-group-title">
        {{ \Modules\ManajemenMahasiswa\Support\JenisDokumenKegiatan::label($jenis ?: null) }}
        @if($isiKelompok->count() > 1)<span>{{ $isiKelompok->count() }}</span>@endif
    </div>
    <div class="document-list">
        @foreach($isiKelompok as $doc)
            @php
                $ext = strtolower(pathinfo($doc->nama_file, PATHINFO_EXTENSION));
                $iconMap = [
                    'pdf'  => ['PDF', 'doc-icon-pdf', 'ext-pdf'],
                    'doc'  => ['DOC', 'doc-icon-word', 'ext-doc'],
                    'docx' => ['DOC', 'doc-icon-word', 'ext-docx'],
                    'xls'  => ['XLS', 'doc-icon-excel', 'ext-xls'],
                    'xlsx' => ['XLS', 'doc-icon-excel', 'ext-xlsx'],
                    'ppt'  => ['PPT', 'doc-icon-ppt', 'ext-ppt'],
                    'pptx' => ['PPT', 'doc-icon-ppt', 'ext-pptx'],
                ];
                $iconInfo = $iconMap[$ext] ?? ['FILE', 'doc-icon-other', 'ext-default'];
            @endphp
            <a href="{{ $doc->url_unduh }}" target="_blank" class="document-card" download>
                <div class="doc-icon-wrapper {{ $iconInfo[1] }}">
                    {{ $iconInfo[0] }}
                </div>
                <div class="doc-details">
                    <div class="doc-title">{{ $doc->judul_file ?: $doc->nama_file }}</div>
                    <div class="doc-meta">
                        <span class="doc-ext-badge {{ $iconInfo[2] }}">{{ strtoupper($ext) }}</span>
                        <span>Klik untuk mengunduh</span>
                    </div>
                </div>
                <div class="doc-download-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                </div>
            </a>
        @endforeach
    </div>
    @endforeach
</div>
@endif
