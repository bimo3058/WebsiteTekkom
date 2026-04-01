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
                <div class="search-container d-flex align-items-center px-3 py-2 flex-grow-1" style="max-width: 400px;">
                    <i class="fas fa-search text-muted"></i>
                    <input type="text" class="form-control search-input ms-2 py-0" placeholder="Cari mata kuliah atau dosen...">
                </div>
                
                <div class="d-flex gap-2">
                    <label class="d-none d-md-flex align-items-center text-muted me-2" style="font-size: 0.85rem; font-weight: 500;">SEMESTER</label>
                    <select class="form-select btn-filter" style="width: auto; min-width: 180px;">
                        <option>Ganjil 2023/2024</option>
                        <option>Genap 2022/2023</option>
                    </select>
                </div>
            </div>
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
                        <tr>
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">Algoritma & Struktur Data</div>
                                <div class="text-muted" style="font-size: 0.8rem;">CS201</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-text" style="background-color: #eff6ff; color: #1e3a8a;">BS</div>
                                    <span class="fw-medium text-dark" style="font-size: 0.9rem;">Budi Santoso</span>
                                </div>
                            </td>
                            <td><span class="fw-semibold text-dark" style="font-size: 0.9rem;">40 Butir</span></td>
                            <td><span class="text-muted" style="font-size: 0.9rem;">12 Sep 2023</span></td>
                            <td><span class="badge-menunggu">MENUNGGU</span></td>
                            <td class="text-end">
                                <a href="{{ route('banksoal.soal.gpm.validasi-bank-soal.review') }}" class="btn btn-review d-inline-flex align-items-center text-decoration-none">
                                    Mulai Validasi <i class="fas fa-arrow-right ms-2" style="font-size: 0.8rem;"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">Pemrograman Web</div>
                                <div class="text-muted" style="font-size: 0.8rem;">CS305</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-text" style="background-color: #e0e7ff; color: #1e40af;">SA</div>
                                    <span class="fw-medium text-dark" style="font-size: 0.9rem;">Siti Aminah</span>
                                </div>
                            </td>
                            <td><span class="fw-semibold text-dark" style="font-size: 0.9rem;">50 Butir</span></td>
                            <td><span class="text-muted" style="font-size: 0.9rem;">13 Sep 2023</span></td>
                            <td><span class="badge-menunggu">MENUNGGU</span></td>
                            <td class="text-end">
                                <a href="{{ route('banksoal.soal.gpm.validasi-bank-soal.review') }}" class="btn btn-review d-inline-flex align-items-center text-decoration-none">
                                    Mulai Validasi <i class="fas fa-arrow-right ms-2" style="font-size: 0.8rem;"></i>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">Jaringan Komputer</div>
                                <div class="text-muted" style="font-size: 0.8rem;">CS402</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-text" style="background-color: #e0f2fe; color: #3730a3;">AF</div>
                                    <span class="fw-medium text-dark" style="font-size: 0.9rem;">Ahmad Fauzi</span>
                                </div>
                            </td>
                            <td><span class="fw-semibold text-dark" style="font-size: 0.9rem;">35 Butir</span></td>
                            <td><span class="text-muted" style="font-size: 0.9rem;">14 Sep 2023</span></td>
                            <td><span class="badge-menunggu">MENUNGGU</span></td>
                            <td class="text-end">
                                <a href="{{ route('banksoal.soal.gpm.validasi-bank-soal.review') }}" class="btn btn-review d-inline-flex align-items-center text-decoration-none">
                                    Mulai Validasi <i class="fas fa-arrow-right ms-2" style="font-size: 0.8rem;"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="border-top px-4 py-3 d-flex justify-content-between align-items-center">
                <span class="text-muted" style="font-size: 0.85rem;">Showing 1 to 3 of 12 courses</span>
                <nav>
                    <ul class="pagination pagination-custom mb-0">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1"><i class="fas fa-chevron-left" style="font-size: 0.7rem;"></i></a>
                        </li>
                        <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></a></li>
                    </ul>
                </nav>
            </div>
        </div>

    </div>
</x-banksoal::layouts.gpm-master>