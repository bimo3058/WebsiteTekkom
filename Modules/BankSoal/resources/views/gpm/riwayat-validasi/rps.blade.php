<x-banksoal::layouts.gpm-master>

    @section('page-title', 'Riwayat Validasi RPS')
    @section('page-subtitle', 'Pantau riwayat dokumen RPS yang telah direview')

    <style>
        .nav-tabs-custom { border-bottom: 2px solid #e2e8f0; }
        .nav-tabs-custom .nav-link { border: none; color: #64748b; font-weight: 600; padding: 1rem 0; margin-right: 2rem; background: transparent; font-size: 0.95rem; }
        .nav-tabs-custom .nav-link.active { color: #2563eb; border-bottom: 2px solid #2563eb; }
        .badge-count { background-color: #dbeafe; color: #1e40af; border-radius: 9999px; padding: 0.15rem 0.6rem; font-size: 0.75rem; margin-left: 0.5rem; font-weight: 700;}

        .table-container { background-color: white; border-radius: 0.75rem; border: 1px solid #e2e8f0; overflow: hidden; margin-top: 2rem;}
        .table-custom th { text-transform: uppercase; font-size: 0.75rem; color: #64748b; font-weight: 700; padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; background-color: #f8fafc; letter-spacing: 0.5px;}
        .table-custom td { padding: 1.25rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #e2e8f0; }
        .table-custom tr:last-child td { border-bottom: none; }
        
        .badge-status { font-weight: 600; padding: 0.35rem 0.8rem; font-size: 0.7rem; border-radius: 0.375rem; letter-spacing: 0.5px;}
        .status-disetujui { background-color: #dcfce7; color: #059669; border: 1px solid #a7f3d0; }
        .status-revisi { background-color: #fee2e2; color: #e11d48; border: 1px solid #fecaca; }
        
        .btn-action { color: #2563eb; font-weight: 600; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; }
        .btn-action:hover { color: #1d4ed8; }
        
        .pagination-custom .page-link { color: #475569; border: 1px solid #e2e8f0; margin: 0 0.25rem; border-radius: 0.375rem; font-size: 0.875rem;}
        .pagination-custom .page-item.active .page-link { background-color: #2563eb; border-color: #2563eb; color: white; }
    </style>

    <div class="container-fluid py-4 px-4 px-xl-5">

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const topbarTitle = document.getElementById('topbar-title');
                const topbarSubtitle = document.getElementById('topbar-subtitle');
                if(topbarTitle) topbarTitle.textContent = "Riwayat Validasi RPS";
                if(topbarSubtitle) topbarSubtitle.textContent = "Pantau riwayat dokumen RPS yang telah direview";
            });
        </script>

        <div class="nav-tabs-custom d-flex">
            <a href="#" class="nav-link active text-decoration-none">
                Selesai Direview <span class="badge-count" style="background-color: #e0e7ff;">{{ $riwayat_rps->total() }}</span>
            </a>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-borderless table-custom mb-0">
                    <thead>
                        <tr>
                            <th width="30%">MATA KULIAH</th>
                            <th width="25%">DOSEN PENGAMPU</th>
                            <th width="20%">TANGGAL DISETUJUI</th>
                            <th width="15%">STATUS AKHIR</th>
                            <th width="10%" class="text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat_rps as $rps)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $rps->mk_nama }}</div>
                                <div class="text-muted" style="font-size: 0.8rem;">{{ $rps->kode }} &bull; {{ $rps->semester }}</div>
                            </td>
                            <td>
                                @php
                                    $dosens = collect(explode(', ', $rps->dosens_list ?? ''))
                                        ->filter()
                                        ->map(fn($d) => explode('|', $d)[1] ?? $d);
                                @endphp
                                @if($dosens->isNotEmpty())
                                    <span class="text-muted" style="font-size: 0.9rem;">{{ $dosens->join(', ') }}</span>
                                @else
                                    <span class="text-muted fst-italic" style="font-size: 0.85rem;">–</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted" style="font-size: 0.9rem;">
                                    {{ $rps->tanggal_disetujui ? \Carbon\Carbon::parse($rps->tanggal_disetujui)->translatedFormat('d F Y') : '–' }}
                                </span>
                            </td>
                            <td><span class="badge-status status-disetujui">DISETUJUI</span></td>
                            <td class="text-end">
                                <a href="{{ route('banksoal.rps.gpm.validasi-rps.review', $rps->rps_id) }}" class="btn-action">
                                    <i class="far fa-eye me-2"></i> Lihat Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-folder-open mb-3" style="font-size: 2.5rem; opacity: 0.4;"></i>
                                    <h6 class="fw-bold">Belum ada riwayat</h6>
                                    <p style="font-size: 0.9rem;">Belum ada RPS yang berstatus disetujui.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($riwayat_rps->hasPages())
            <div class="border-top px-4 py-3 d-flex justify-content-between align-items-center">
                <span class="text-muted" style="font-size: 0.85rem;">
                    Menampilkan {{ $riwayat_rps->firstItem() }}–{{ $riwayat_rps->lastItem() }} dari {{ $riwayat_rps->total() }} hasil
                </span>
                <nav>
                    {{ $riwayat_rps->links('pagination::bootstrap-5') }}
                </nav>
            </div>
            @endif
        </div>

    </div>
</x-banksoal::layouts.gpm-master>