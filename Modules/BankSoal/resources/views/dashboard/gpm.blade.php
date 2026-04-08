<x-banksoal::layouts.gpm-master>

    @section('page-title', 'Dashboard GPM')
    @section('page-subtitle', 'Ringkasan aktivitas penjaminan mutu akademik')

    <style>
        .text-purple { color: #6f42c1; }
        .bg-purple-subtle { background-color: rgba(111, 66, 193, 0.1); }
        .table-custom th { font-size: 0.75rem; color: #6c757d; font-weight: 600; text-transform: uppercase; padding-top: 1rem; padding-bottom: 1rem; border-bottom: 2px solid #f8f9fa;}
        .table-custom td { padding-top: 1.25rem; padding-bottom: 1.25rem; vertical-align: middle; border-bottom: 1px solid #f8f9fa; }
        .card-shadow { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03); border: 1px solid #f1f1f1; }
    </style>

    <div class="container-fluid py-4 px-4 px-xl-5">

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const topbarTitle = document.getElementById('topbar-title');
                const topbarSubtitle = document.getElementById('topbar-subtitle');
                if(topbarTitle) topbarTitle.textContent = "Dashboard GPM";
                if(topbarSubtitle) topbarSubtitle.textContent = "Ringkasan aktivitas penjaminan mutu akademik";
            });
        </script>
        
        <h4 class="fw-bold text-dark mb-4 mt-2">Selamat datang kembali, Prof. Aris!</h4>

        <div class="card mb-4 border-0 rounded-3" style="background-color: #fffaf0; border: 1px solid #ffeeba !important;">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 px-4">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="bg-warning bg-opacity-25 rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 45px; height: 45px; flex-shrink: 0;">
                        <i class="fas fa-bell text-warning fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Perhatian: Ada 3 RPS dan 5 Paket Soal yang mendekati batas waktu review.</h6>
                        <p class="text-muted mb-0" style="font-size: 0.85rem;">Segera lakukan peninjauan sebelum batas waktu berakhir untuk menjaga kualitas akademik.</p>
                    </div>
                </div>
                <button class="btn btn-warning text-white fw-bold px-4 rounded-3 shadow-sm" style="background-color: #f59e0b; border: none;">Lihat Detail</button>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xl-4 col-md-6">
                <div class="card card-shadow border-0 h-100 rounded-4">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="bg-primary bg-opacity-10 rounded-3 d-flex justify-content-center align-items-center" style="width: 42px; height: 42px;">
                                <i class="fas fa-file-alt text-primary"></i>
                            </div>
                            <span class="badge rounded-pill fw-semibold px-3 py-2" style="background-color: #ffeef0; color: #e63946; font-size: 0.75rem;">2 Urgent</span>
                        </div>
                        <div>
                            <p class="text-muted fw-semibold mb-1" style="font-size: 0.85rem;">RPS Menunggu Validasi</p>
                            <h2 class="fw-bold text-dark mb-0">5</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6">
                <div class="card card-shadow border-0 h-100 rounded-4">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="bg-purple-subtle rounded-3 d-flex justify-content-center align-items-center" style="width: 42px; height: 42px;">
                                <i class="fas fa-question-circle text-purple"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-muted fw-semibold mb-1" style="font-size: 0.85rem;">Bank Soal Menunggu</p>
                            <h2 class="fw-bold text-dark mb-0">12</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-12">
                <div class="card card-shadow border-0 h-100 rounded-4">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="bg-success bg-opacity-10 rounded-3 d-flex justify-content-center align-items-center" style="width: 42px; height: 42px;">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-muted fw-semibold mb-1" style="font-size: 0.85rem;">Selesai Direview Bulan Ini</p>
                            <h2 class="fw-bold text-success mb-0">34</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-shadow border-0 rounded-4 mb-5">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold text-dark mb-0 fs-5">Tugas Prioritas</h6>
                <a href="#" class="text-decoration-none fw-semibold" style="font-size: 0.85rem; color: #2563eb;">Lihat Semua</a>
            </div>
            <div class="card-body px-4 pb-4 mt-2">
                <div class="table-responsive">
                    <table class="table table-custom mb-0 align-middle">
                        <thead>
                            <tr>
                                <th scope="col" width="15%">TIPE DOKUMEN</th>
                                <th scope="col" width="40%">MATA KULIAH</th>
                                <th scope="col" width="25%">DEADLINE</th>
                                <th scope="col" width="20%" class="text-end">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-semibold" style="font-size: 0.75rem;">RPS</span></td>
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 0.95rem;">Struktur Data</div>
                                    <div class="text-muted" style="font-size: 0.8rem;">INF201 • Informatika</div>
                                </td>
                                <td><span class="text-muted" style="font-size: 0.9rem;">Besok, 12:00 WIB</span></td>
                                <td class="text-end">
                                    <a href="{{ route('banksoal.rps.gpm.validasi-rps.review', ['rpsId' => 1]) }}" class="btn text-white rounded-3 px-3 py-2 fw-semibold text-decoration-none" style="background-color: #2563eb; font-size: 0.8rem;">Review Sekarang</a>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-purple-subtle text-purple rounded-pill px-3 py-2 fw-semibold" style="font-size: 0.75rem;">Bank Soal</span></td>
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 0.95rem;">Algoritma Pemrograman</div>
                                    <div class="text-muted" style="font-size: 0.8rem;">INF102 • Informatika</div>
                                </td>
                                <td><span class="text-muted" style="font-size: 0.9rem;">3 Hari lagi</span></td>
                                <td class="text-end">
                                    <a href="{{ route('banksoal.soal.gpm.validasi-bank-soal') }}" class="btn text-white rounded-3 px-3 py-2 fw-semibold text-decoration-none" style="background-color: #2563eb; font-size: 0.8rem;">Review Sekarang</a>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-semibold" style="font-size: 0.75rem;">RPS</span></td>
                                <td>
                                    <div class="fw-bold text-dark" style="font-size: 0.95rem;">Basis Data Lanjut</div>
                                    <div class="text-muted" style="font-size: 0.8rem;">INF305 • Sistem Informasi</div>
                                </td>
                                <td><span class="text-muted" style="font-size: 0.9rem;">5 Hari lagi</span></td>
                                <td class="text-end">
                                    <a href="{{ route('banksoal.rps.gpm.validasi-rps.review', ['rpsId' => 1]) }}" class="btn text-white rounded-3 px-3 py-2 fw-semibold text-decoration-none" style="background-color: #2563eb; font-size: 0.8rem;">Review Sekarang</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-banksoal::layouts.gpm-master>