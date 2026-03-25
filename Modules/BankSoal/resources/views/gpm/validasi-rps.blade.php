<x-banksoal::layouts.gpm-master>

    @section('page-title', 'Validasi RPS')
    @section('page-subtitle', 'Pantau riwayat dokumen RPS yang telah direview')

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
        .table-rps th { text-transform: uppercase; font-size: 0.75rem; color: #64748b; font-weight: 600; padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; background-color: white; letter-spacing: 0.5px;}
        .table-rps td { padding: 1.25rem 1.5rem; vertical-align: middle; border-bottom: 1px solid #e2e8f0; }
        .table-rps tr:last-child td { border-bottom: none; }
        
        .badge-menunggu { background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a; font-weight: 600; padding: 0.35rem 0.75rem; font-size: 0.7rem; border-radius: 0.375rem; letter-spacing: 0.5px;}
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
                if(topbarTitle) topbarTitle.textContent = "Validasi RPS";
                if(topbarSubtitle) topbarSubtitle.textContent = "Pantau riwayat dokumen RPS yang telah direview";
            });
        </script>

        <div class="nav-tabs-custom mb-4 d-flex">
            <a href="#" class="nav-link active text-decoration-none">
                Menunggu Validasi <span class="badge-count">2</span>
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="search-container d-flex align-items-center px-3 py-2 flex-grow-1" 
                        style="max-width: 500px;">

                    <i class="fas fa-search text-muted me-2" 
                    style="font-size: 0.9rem;"></i>

                    <input type="text" class="form-control search-input p-0" 
                    placeholder="Cari mata kuliah atau dosen...">
                </div>
                
                <button class="btn btn-filter px-4 py-2 rounded-3 d-flex align-items-center">
                    <i class="fas fa-filter me-2 text-muted" 
                        style="font-size: 0.85rem;"></i> Filter
                </button>
            </div>
        </div>

        <div class="table-container shadow-sm mb-4">
            <div class="table-responsive">
                <table class="table table-rps mb-0">
                    <thead>
                        <tr>
                            <th width="35%">MATA KULIAH</th>
                            <th width="20%">DOSEN PENGAMPU</th>
                            <th width="15%">TANGGAL DIAJUKAN</th>
                            <th width="15%">STATUS</th>
                            <th width="15%" class="text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">Kecerdasan Buatan (CS401)</div>
                                <div class="text-muted" style="font-size: 0.8rem;">Semester Ganjil 2023/2024</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-text">RM</div>
                                    <span class="fw-medium text-dark" style="font-size: 0.9rem;">Dr. Rina M.</span>
                                </div>
                            </td>
                            <td><span class="text-muted" style="font-size: 0.9rem;">12 Sep 2023</span></td>
                            <td><span class="badge-menunggu">MENUNGGU</span></td>
                            <td class="text-end">
                                <a href="{{ route('gpm.validasi-rps.review') }}" class="btn btn-review d-inline-flex align-items-center text-decoration-none">
                                    <i class="fas fa-comment-dots me-2" style="font-size: 0.8rem;"></i> Review Sekarang
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark" style="font-size: 0.95rem;">Interaksi Manusia & Komputer (CS302)</div>
                                <div class="text-muted" style="font-size: 0.8rem;">Semester Ganjil 2023/2024</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-text">AP</div>
                                    <span class="fw-medium text-dark" style="font-size: 0.9rem;">Andi P., M.Kom</span>
                                </div>
                            </td>
                            <td><span class="text-muted" style="font-size: 0.9rem;">14 Sep 2023</span></td>
                            <td><span class="badge-menunggu">MENUNGGU</span></td>
                            <td class="text-end">
                                <a href="{{ route('gpm.validasi-rps.review') }}" class="btn btn-review d-inline-flex align-items-center text-decoration-none">
                                    <i class="fas fa-comment-dots me-2" style="font-size: 0.8rem;"></i> Review Sekarang
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-2 px-2">
            <span class="text-muted mb-3 mb-sm-0" style="font-size: 0.875rem;">Menampilkan 2 dari 2 entri</span>
            <nav>
                <ul class="pagination pagination-custom mb-0">
                    <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true"><i class="fas fa-chevron-left fa-xs"></i></a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-chevron-right fa-xs"></i></a></li>
                </ul>
            </nav>
        </div>

    </div>
</x-banksoal::layouts.gpm-master>