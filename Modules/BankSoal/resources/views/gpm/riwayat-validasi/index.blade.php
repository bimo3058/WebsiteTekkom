<x-banksoal::layouts.gpm-master>

    @section('page-title', 'Menu Riwayat Validasi')
    @section('page-subtitle', 'Pilih kategori riwayat dokumen yang ingin Anda pantau')

    <style>
        .menu-card {
            background-color: white;
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 3rem 2rem 2rem;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: all 0.3s ease;
        }
        .menu-card:hover {
            border-color: #2563eb;
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.1), 0 4px 6px -2px rgba(37, 99, 235, 0.05);
            transform: translateY(-5px);
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background-color: #eff6ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .icon-circle i {
            font-size: 2rem;
            color: #2563eb;
        }

        .menu-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .menu-desc {
            font-size: 0.95rem;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 3rem;
        }

        .btn-menu {
            margin-top: auto;
            width: 100%;
            background-color: #1d4ed8;
            color: white;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            transition: 0.2s;
        }
        .btn-menu:hover {
            background-color: #1e40af;
            color: white;
        }

        .stat-card {
            background-color: white;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .stat-blue .stat-icon { background-color: #eff6ff; color: #2563eb; }
        .stat-green .stat-icon { background-color: #ecfdf5; color: #10b981; }
        .stat-orange .stat-icon { background-color: #fff7ed; color: #f59e0b; }

        .stat-info span {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }
        .stat-info h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
        }
    </style>

    <div class="container-fluid py-5 px-4 px-xl-5">

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const topbarTitle = document.getElementById('topbar-title');
                const topbarSubtitle = document.getElementById('topbar-subtitle');
                if(topbarTitle) topbarTitle.textContent = "Menu Riwayat Validasi";
                if(topbarSubtitle) topbarSubtitle.textContent = "Pilih kategori riwayat dokumen yang ingin Anda pantau";
            });
        </script>

        <div class="row g-4 justify-content-center mb-5">
            <div class="col-md-6 col-lg-5">
                <div class="menu-card">
                    <div class="icon-circle">
                        <i class="far fa-file-alt"></i>
                    </div>
                    <div class="menu-title">Riwayat Validasi RPS</div>
                    <div class="menu-desc">
                        Pantau status, tanggal diajukan, dan hasil review dokumen Rencana Pembelajaran Semester (RPS) untuk setiap program studi.
                    </div>
                    <a href="{{ route('banksoal.rps.gpm.riwayat-validasi.rps') }}" class="btn-menu">
                        Buka Riwayat RPS <i class="fas fa-arrow-right ms-2 fs-6"></i>
                    </a>
                </div>
            </div>

            <div class="col-md-6 col-lg-5">
                <div class="menu-card">
                    <div class="icon-circle">
                        <i class="far fa-question-circle"></i>
                    </div>
                    <div class="menu-title">Riwayat Validasi Bank Soal</div>
                    <div class="menu-desc">
                        Pantau status, jumlah butir soal, dan hasil review untuk paket Bank Soal mata kuliah pada tiap semester aktif.
                    </div>
                    <a href="{{ route('banksoal.soal.gpm.riwayat-validasi.bank-soal') }}" class="btn-menu">
                        Buka Riwayat Bank Soal <i class="fas fa-arrow-right ms-2 fs-6"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4 justify-content-center" style="max-width: 1000px; margin: 0 auto;">
            <div class="col-md-4">
                <div class="stat-card stat-blue">
                    <div class="stat-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="stat-info">
                        <span>Menunggu Review</span>
                        <h5>12 Dokumen</h5>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="stat-card stat-green">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <span>Selesai Validasi</span>
                        <h5>48 Dokumen</h5>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat-card stat-orange">
                    <div class="stat-icon">
                        <i class="far fa-calendar-alt"></i>
                    </div>
                    <div class="stat-info">
                        <span>Update Terakhir</span>
                        <h5>2 Jam Lalu</h5>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-banksoal::layouts.gpm-master>