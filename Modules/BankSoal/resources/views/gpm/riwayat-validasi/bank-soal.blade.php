<x-banksoal::layouts.gpm-master>

    @section('page-title', 'Riwayat Validasi Bank Soal')
    @section('page-subtitle', 'Pantau riwayat paket soal mata kuliah yang telah selesai dievaluasi')

    <style>
        .nav-tabs-custom { border-bottom: 2px solid #e2e8f0; }
        .nav-tabs-custom .nav-link { border: none; color: #64748b; font-weight: 600; padding: 1rem 0; margin-right: 2rem; background: transparent; font-size: 0.95rem; }
        .nav-tabs-custom .nav-link.active { color: #2563eb; border-bottom: 2px solid #2563eb; }
        .badge-count { background-color: #dbeafe; color: #1e40af; border-radius: 9999px; padding: 0.15rem 0.6rem; font-size: 0.75rem; margin-left: 0.5rem; font-weight: 700;}

        .toolbar-container { display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; margin-bottom: 1.5rem; }
        
        .search-box {
            background-color: white;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            width: 300px;
        }
        .search-box input {
            border: none;
            background: transparent;
            box-shadow: none !important;
            padding: 0;
            margin-left: 0.5rem;
            font-size: 0.9rem;
        }
        .search-box input:focus { outline: none; }
        .btn-filter {
            background-color: white;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: 0.2s;
        }
        .btn-filter:hover { background-color: #f8fafc; }

        .table-container { background-color: white; border-radius: 0.75rem; border: 1px solid #e2e8f0; overflow: hidden; }
        .table-custom th { text-transform: uppercase; font-size: 0.75rem; color: #64748b; font-weight: 700; padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; background-color: #f8fafc; letter-spacing: 0.5px;}
        .table-custom td { padding: 1.25rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #e2e8f0; }
        .table-custom tr:last-child td { border-bottom: none; }
        
        .badge-status { font-weight: 600; padding: 0.35rem 0.8rem; font-size: 0.7rem; border-radius: 0.375rem; letter-spacing: 0.5px;}
        .status-disetujui { background-color: #dcfce7; color: #059669; border: 1px solid #a7f3d0; }
        
        .btn-action { color: #2563eb; font-weight: 600; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; flex-direction: column; }
        .btn-action i { font-size: 1.1rem; margin-bottom: 0.2rem; }
        .btn-action:hover { color: #1d4ed8; }
        
        .pagination-custom .page-link { color: #475569; border: 1px solid #e2e8f0; margin: 0 0.25rem; border-radius: 0.375rem; font-size: 0.875rem;}
        .pagination-custom .page-item.active .page-link { background-color: #2563eb; border-color: #2563eb; color: white; }
    </style>

    <div class="container-fluid py-4 px-4 px-xl-5">

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const topbarTitle = document.getElementById('topbar-title');
                const topbarSubtitle = document.getElementById('topbar-subtitle');
                if(topbarTitle) topbarTitle.textContent = "Riwayat Validasi Bank Soal";
                if(topbarSubtitle) topbarSubtitle.textContent = "Pantau riwayat paket soal mata kuliah yang telah selesai dievaluasi";
            });
        </script>

        <div class="nav-tabs-custom d-flex">
            <a href="{{ route('banksoal.soal.gpm.validasi-bank-soal') }}" class="nav-link text-decoration-none text-muted">
                Menunggu Validasi <span class="badge-count">{{ $counts->menunggu ?? 0 }}</span>
            </a>
            <a href="#" class="nav-link active text-decoration-none ms-3">
                Selesai Direview <span class="badge-count">{{ $counts->selesai ?? $riwayat_soal->count() }}</span>
            </a>
        </div>

        <div class="toolbar-container d-flex justify-content-between align-items-center">
            <form action="{{ route('banksoal.soal.gpm.riwayat-validasi.bank-soal') }}" method="GET" class="search-box m-0 p-0" style="padding: 0 !important; overflow: hidden;">
                <div class="d-flex align-items-center w-100" style="padding: 0.5rem 1rem;">
                    <i class="fas fa-search text-muted"></i>
                    <input type="text" name="search" autocomplete="off" list="datalistRiwayat" value="{{ request('search') }}" class="form-control border-0 shadow-none bg-transparent" placeholder="Cari mata kuliah... ketik abjad" onchange="this.form.submit()">
                    <datalist id="datalistRiwayat">
                        @foreach($all_riwayat_soal as $item)
                            <option value="{{ $item->mk_nama }}"></option>
                            <option value="{{ $item->mk_kode }}"></option>
                        @endforeach
                    </datalist>
                </div>
            </form>
            <button class="btn btn-filter">
                <i class="fas fa-filter"></i> Filters
            </button>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-borderless table-custom mb-0">
                    <thead>
                        <tr>
                            <th width="25%">MATA KULIAH</th>
                            <th width="20%">DOSEN PENGAMPU</th>
                            <th width="15%">JUMLAH SOAL</th>
                            <th width="15%">TANGGAL REVIEW TERAKHIR</th>
                            <th width="15%">STATUS</th>
                            <th width="10%" class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat_soal as $riwayat)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $riwayat->mk_nama }}</div>
                                <div class="text-muted" style="font-size: 0.8rem;">{{ $riwayat->mk_kode }}</div>
                            </td>
                            <td><span class="text-muted" style="font-size: 0.9rem;">Dosen Pengampu</span></td>
                            <td><span class="text-muted" style="font-size: 0.9rem;">{{ $riwayat->jumlah_soal }} Butir Direview</span></td>
                            <td>
                                <span class="text-muted" style="font-size: 0.9rem;">
                                    {{ $riwayat->tanggal_review ? \Carbon\Carbon::parse($riwayat->tanggal_review)->format('d M Y') : '-' }}
                                </span>
                            </td>
                            <td><span class="badge-status status-disetujui">SELESAI</span></td>
                            <td class="text-center">
                                <a href="{{ route('banksoal.soal.gpm.riwayat-validasi.bank-soal.detail', $riwayat->mk_id) }}" class="btn btn-link text-decoration-none p-0 d-flex flex-column align-items-center">
                                    <i class="fas fa-eye fs-5"></i>
                                    <span style="font-size: 0.75rem; font-weight: 600;">Lihat Detail</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-history text-muted mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <h5 class="fw-bold text-muted">Belum Ada Riwayat</h5>
                                    <p class="text-secondary mb-0" style="font-size: 0.9rem;">Belum ada paket soal mata kuliah yang selesai divalidasi oleh GPM.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($riwayat_soal->count() > 0)
            <div class="border-top px-4 py-3 d-flex justify-content-between align-items-center">
                <span class="text-muted" style="font-size: 0.85rem;">Menampilkan {{ $riwayat_soal->count() }} item</span>
                <nav>
                    <ul class="pagination pagination-custom mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-chevron-left" style="font-size: 0.7rem;"></i></a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></a></li>
                    </ul>
                </nav>
            </div>
            @endif
        </div>

    </div>
</x-banksoal::layouts.gpm-master>