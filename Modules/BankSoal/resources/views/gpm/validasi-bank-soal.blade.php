<x-banksoal::layouts.gpm-master>

    @section('page-title', 'Validasi Bank Soal')
    @section('page-subtitle', 'Pilih paket soal mata kuliah yang perlu dievaluasi')

    <style>
        .nav-tabs-custom { border-bottom: 2px solid #e2e8f0; }
        .nav-tabs-custom .nav-link { border: none; color: #64748b; font-weight: 600; padding: 1rem 0; margin-right: 2rem; background: transparent; font-size: 0.95rem; }
        .nav-tabs-custom .nav-link.active { color: #2563eb; border-bottom: 2px solid #2563eb; }
        .badge-count { background-color: #dbeafe; color: #1e40af; border-radius: 9999px; padding: 0.15rem 0.6rem; font-size: 0.75rem; margin-left: 0.5rem; font-weight: 700;}

        .search-container { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem; }
        .search-input { border: none; background: transparent; box-shadow: none !important; }
        .search-input:focus { outline: none; box-shadow: none; background: transparent; }
        .btn-filter { border: 1px solid #e2e8f0; background-color: white; color: #475569; font-weight: 500; font-size: 0.9rem;}

        .table-container { background-color: white; border-radius: 0.75rem; border: 1px solid #e2e8f0; overflow: hidden; }
        .table-rps th { text-transform: uppercase; font-size: 0.75rem; color: #64748b; font-weight: 700; padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; background-color: #f8fafc; letter-spacing: 0.5px;}
        .table-rps td { padding: 1.25rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #e2e8f0; }
        .table-rps tr:last-child td { border-bottom: none; }
        
        .badge-menunggu { background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a; font-weight: 600; padding: 0.35rem 0.8rem; font-size: 0.7rem; border-radius: 0.375rem; letter-spacing: 0.5px;}
        .avatar-text { width: 32px; height: 32px; border-radius: 50%; background-color: #eff6ff; color: #2563eb; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 600; margin-right: 0.75rem; }
        
        .btn-review { background-color: #2563eb; color: white; border-radius: 0.375rem; font-weight: 500; font-size: 0.85rem; padding: 0.5rem 1.25rem; border: none; transition: 0.2s; }
        .btn-review:hover { background-color: #1d4ed8; color: white;}
        
        .pagination-custom .page-link { color: #475569; border: 1px solid #e2e8f0; margin: 0 0.25rem; border-radius: 0.375rem; font-size: 0.875rem;}
        .pagination-custom .page-item.active .page-link { background-color: #2563eb; border-color: #2563eb; color: white; }

        .badge-count { background-color: #dbeafe; color: #1e40af; border-radius: 9999px; padding: 0.15rem 0.6rem; font-size: 0.75rem; margin-left: 0.5rem; font-weight: 700; }
    </style>

    <div class="container-fluid py-4 px-4 px-xl-5">

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const topbarTitle = document.getElementById('topbar-title');
                const topbarSubtitle = document.getElementById('topbar-subtitle');
                if(topbarTitle) topbarTitle.textContent = "Validasi Bank Soal";
                if(topbarSubtitle) topbarSubtitle.textContent = "Pilih paket soal mata kuliah yang perlu dievaluasi";
            });
        </script>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <form action="{{ route('banksoal.soal.gpm.validasi-bank-soal') }}" method="GET" class="search-container d-flex align-items-center px-3 py-2 flex-grow-1" style="max-width: 400px; margin: 0;">
                    <i class="fas fa-search text-muted"></i>
                    <input type="text" name="search" autocomplete="off" list="datalistAntrean" class="form-control search-input ms-2 py-0" placeholder="Cari mata kuliah..." value="{{ request('search') }}" onchange="this.form.submit()">
                    <datalist id="datalistAntrean">
                        @foreach($all_paket_soal as $item)
                            <option value="{{ $item->mk_nama }}"></option>
                            <option value="{{ $item->mk_kode }}"></option>
                        @endforeach
                    </datalist>
                </form>
                
                <div class="d-flex gap-2">
                    <label class="d-none d-md-flex align-items-center text-muted me-2" style="font-size: 0.85rem; font-weight: 500;">SEMESTER</label>
                    <select class="form-select btn-filter" style="width: auto; min-width: 180px;">
                        <option>Ganjil 2023/2024</option>
                        <option>Genap 2022/2023</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center border-bottom mb-4" style="gap: 2.5rem; border-color: #e2e8f0 !important;">
            <a href="#" class="text-decoration-none pb-3 position-relative d-flex align-items-center" style="color: #2563eb; font-weight: 600; font-size: 0.95rem;">
                Menunggu Validasi 
                <span class="badge-count">{{ $counts->menunggu ?? 0 }}</span>
                <div class="position-absolute bottom-0 start-0 w-100" style="height: 2px; background-color: #2563eb; border-radius: 2px;"></div>
            </a>

            <a href="{{ route('banksoal.soal.gpm.riwayat-validasi.bank-soal') }}" class="text-decoration-none pb-3 text-muted fw-semibold hover-primary d-flex align-items-center" style="font-size: 0.95rem; transition: color 0.2s;">
                Selesai Direview
                <span class="badge-count">{{ $counts->selesai ?? 0 }}</span>
            </a>
            
        </div>

        <div class="table-container mb-4">
            <div class="table-responsive">
                <table class="table table-borderless table-rps mb-0">
                    <thead>
                        <tr>
                            <th width="28%">MATA KULIAH</th>
                            <th width="22%">DOSEN PENGAMPU</th>
                            <th width="12%">JUMLAH SOAL</th>
                            <th width="15%">TANGGAL DIAJUKAN</th>
                            <th width="10%">STATUS</th>
                            <th width="13%" class="text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paket_soal as $paket)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $paket->mk_nama }}</div>
                                <div class="text-muted" style="font-size: 0.8rem;">{{ $paket->mk_kode }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    {{-- Mengambil 2 huruf awal dari nama mata kuliah sebagai avatar dummy sementara --}}
                                    <div class="avatar-text" style="background-color: #eff6ff; color: #1e3a8a;">
                                        {{ strtoupper(substr($paket->mk_nama, 0, 2)) }}
                                    </div>
                                    <span class="fw-medium text-dark" style="font-size: 0.9rem;">
                                        {{-- TODO: Hubungkan relasi nama dosen dari database --}}
                                        Dosen Pengampu
                                    </span>
                                </div>
                            </td>
                            <td><span class="fw-semibold text-dark" style="font-size: 0.9rem;">{{ $paket->jumlah_soal }} Butir</span></td>
                            <td><span class="text-muted" style="font-size: 0.9rem;">-</span></td>
                            <td><span class="badge-menunggu">MENUNGGU</span></td>
                            <td class="text-end">
                                <a href="{{ route('banksoal.soal.gpm.validasi-bank-soal.review') }}" class="btn btn-review d-inline-flex align-items-center text-decoration-none">
                                    Mulai Validasi <i class="fas fa-arrow-right ms-2" style="font-size: 0.8rem;"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <i class="fas fa-check-circle text-success mb-3" style="font-size: 3rem; opacity: 0.5;"></i>
                                    <h5 class="fw-bold text-muted">Antrean Kosong</h5>
                                    <p class="text-secondary mb-0" style="font-size: 0.9rem;">Saat ini tidak ada bank soal yang menunggu untuk divalidasi oleh GPM.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($paket_soal->count() > 0)
            <div class="border-top px-4 py-3 d-flex justify-content-between align-items-center">
                <span class="text-muted" style="font-size: 0.85rem;">Menampilkan {{ $paket_soal->count() }} mata kuliah</span>
                <nav>
                    <ul class="pagination pagination-custom mb-0">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1"><i class="fas fa-chevron-left" style="font-size: 0.7rem;"></i></a>
                        </li>
                        <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></a></li>
                    </ul>
                </nav>
            </div>
            @endif
        </div>

    </div>
</x-banksoal::layouts.gpm-master>