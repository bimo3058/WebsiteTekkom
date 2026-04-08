<x-banksoal::layouts.gpm-master>

    @section('page-title', 'Validasi Bank Soal')
    @section('page-subtitle', 'Evaluasi kesesuaian butir soal dengan CPL')

    <style>
        .progress-header {
            background-color: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            color: #475569;
            margin-top: -1.5rem;
            margin-left: -1.5rem;
            margin-right: -1.5rem;
            margin-bottom: 2rem;
        }

        .progress-bar-container {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-weight: 600;
            color: #1e3a8a;
        }
        
        .progress-custom {
            height: 8px;
            width: 150px;
            background-color: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-custom .progress-fill {
            height: 100%;
            background-color: #2563eb;
            width: 12.5%; /* 5 of 40 */
        }

        .cpl-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.5rem;
            height: 100%;
        }

        .cpl-title {
            color: #1d4ed8;
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .cpl-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .cpl-value {
            font-weight: 700;
            color: #1e293b;
            font-size: 1.1rem;
            margin-bottom: 1.25rem;
        }

        .cpl-desc {
            color: #475569;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .badge-cognitive {
            background-color: #2563eb;
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.4rem 0.8rem;
            border-radius: 9999px;
            display: inline-block;
        }

        .question-card {
            background-color: white;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .badge-soal {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 0.35rem 0.75rem;
            border-radius: 0.375rem;
            letter-spacing: 0.5px;
        }

        .question-type {
            font-size: 0.8rem;
            color: #94a3b8;
        }

        .question-text {
            font-size: 1.1rem;
            color: #1e293b;
            font-weight: 700;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .option-item {
            display: flex;
            align-items: center;
            padding: 1rem 1.25rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            gap: 1rem;
        }

        .option-letter {
            width: 32px;
            height: 32px;
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: 700;
            font-size: 0.85rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .option-text {
            color: #475569;
            font-size: 0.95rem;
            flex-grow: 1;
        }

        .option-item.correct {
            background-color: #ecfdf5;
            border-color: #a7f3d0;
        }

        .option-item.correct .option-letter {
            background-color: #10b981;
            color: white;
        }

        .option-item.correct .option-text {
            color: #065f46;
            font-weight: 500;
        }

        .correct-icon {
            color: #10b981;
            font-size: 1.2rem;
        }

        .decision-section {
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 1px dashed #e2e8f0;
        }

        .decision-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }

        .decision-group {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .btn-decision {
            flex: 1;
            background-color: white;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-weight: 600;
            padding: 0.75rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: 0.2s;
            cursor: pointer;
        }

        .btn-decision:hover {
            background-color: #f8fafc;
        }

        .btn-decision.active {
            background-color: #eff6ff;
            border-color: #93c5fd;
            color: #2563eb;
        }

        .revision-note textarea {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1rem;
            font-size: 0.9rem;
            color: #334155;
            resize: vertical;
            min-height: 120px;
        }

        .revision-note textarea:focus {
            outline: none;
            border-color: #94a3b8;
        }

        .action-footer {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
        }

        .btn-prev {
            background-color: white;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-weight: 600;
            padding: 0.6rem 1.25rem;
            border-radius: 0.375rem;
            transition: 0.2s;
        }

        .btn-prev:hover {
            background-color: #f8fafc;
        }

        .btn-next {
            background-color: #2563eb;
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.6rem 1.25rem;
            border-radius: 0.375rem;
            transition: 0.2s;
        }

        .btn-next:hover {
            background-color: #1d4ed8;
        }
    </style>

    <div class="progress-header px-4 px-xl-5">
        <div>
            <span class="fw-bold">Mata Kuliah:</span> {{ $soal->mk_nama }} ({{ $soal->mk_kode }}) &nbsp; <span class="text-muted">|</span> &nbsp; 
            <span class="fw-bold">Dosen:</span> Budi Santoso {{-- TODO: Hubungkan dengan relasi Dosen nanti ya --}}
        </div>
        <div class="progress-bar-container">
            Review Progress: Soal {{ $soal->id }}
            <div class="progress-custom">
                <div class="progress-fill"></div>
            </div>
        </div>
    </div>

    <div class="container-fluid pb-5 px-4 px-xl-5">
        
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const topbarTitle = document.getElementById('topbar-title');
                const topbarSubtitle = document.getElementById('topbar-subtitle');
                if(topbarTitle) topbarTitle.textContent = "Validasi Bank Soal";
                if(topbarSubtitle) topbarSubtitle.textContent = "Evaluasi kesesuaian butir soal dengan CPL";
            });
        </script>

        <div class="row g-4">
            <!-- Left Column: CPL Info -->
            <div class="col-lg-4">
                <div class="cpl-card">
                    <div class="cpl-title">
                        <i class="far fa-dot-circle" style="font-size: 1.25rem;"></i> Target Capaian Pembelajaran (CPL)
                    </div>
                    
                    <div class="cpl-label">KODE CAPAIAN</div>
                    <div class="cpl-value">{{ $soal->cpl_kode }}</div>
                    
                    <div class="cpl-label">DESKRIPSI KOMPETENSI</div>
                    <div class="cpl-desc">
                        {{ $soal->cpl_deskripsi }}
                    </div>
                    
                    <div class="badge-cognitive">
                        LEVEL KOGNITIF: C4 (MENGANALISIS) {{-- TODO: Ganti ini jika sudah ada di DB --}}
                    </div>
                </div>
            </div>

            <!-- Right Column: Question Review -->
            <div class="col-lg-8">
                <div class="question-card">
                    <div class="question-header">
                        <span class="badge-soal">SOAL ID. {{ $soal->id }}</span>
                        <span class="question-type">Tipe: Pilihan Ganda</span>
                    </div>

                    <div class="question-text">
                        {{ $soal->soal }}
                    </div>

                    <div class="options-container">
                        @foreach($opsi_jawaban as $opsi)
                            <div class="option-item {{ $opsi->is_benar ? 'correct' : '' }}">
                                <div class="option-letter">{{ $opsi->opsi }}</div>
                                <div class="option-text">{{ $opsi->deskripsi }}</div>
                                @if($opsi->is_benar)
                                    <i class="far fa-check-circle correct-icon"></i>
                                @endif
                            </div>
                        @endforeach
                    </div>

            <form action="{{ route('banksoal.soal.gpm.validasi-bank-soal.store') }}" method="POST">                        @csrf
                        <input type="hidden" name="pertanyaan_id" value="{{ $soal->id }}">

                <div class="decision-section">
                    <div class="decision-title">KEPUTUSAN GPM</div>
                            
                     <div class="decision-group">
                             <label class="btn-decision" onclick="selectDecision(this)">
                                <input type="radio" name="status_review" value="Sesuai" class="d-none" required>
                                <i class="fas fa-check"></i> Sesuai
                            </label>
                                
                                <label class="btn-decision" onclick="selectDecision(this)">
                                    <input type="radio" name="status_review" value="Kurang Sesuai" class="d-none">
                                    <i class="fas fa-exclamation-triangle"></i> Kurang Sesuai
                                </label>
                                
                                <label class="btn-decision" onclick="selectDecision(this)">
                                    <input type="radio" name="status_review" value="Revisi Total" class="d-none">
                                    <i class="fas fa-exclamation-circle"></i> Revisi Total
                                </label>
                            </div>

                            <div class="revision-note">
                                <div class="decision-title">CATATAN REVISI</div>
                                <textarea name="catatan" placeholder="Masukkan feedback untuk dosen..." required></textarea>
                            </div>
                        </div>

                        <div class="action-footer">
                            <a href="{{ route('banksoal.soal.gpm.validasi-bank-soal') }}" class="btn-prev text-decoration-none text-dark d-inline-flex align-items-center">
                                <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar
                            </a>
                            
                            <button type="submit" class="btn-next">
                                Simpan & Lanjut Berikutnya <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>

                    <script>
                        function selectDecision(element) {
                            // 1. Hapus class 'active' dari semua tombol
                            document.querySelectorAll('.btn-decision').forEach(btn => {
                                btn.classList.remove('active');
                            });
                            // 2. Tambahkan class 'active' ke tombol yang sedang diklik
                            element.classList.add('active');
                        }
                    </script>
    
                    <script>
                        function selectDecision(element) {
                            // 1. Hapus class 'active' dari semua tombol
                            document.querySelectorAll('.btn-decision').forEach(btn => {
                                btn.classList.remove('active');
                            });
                            // 2. Tambahkan class 'active' ke tombol yang sedang diklik
                            element.classList.add('active');
                        }
                    </script>

                </div> </div> </div> </div> </x-banksoal::layouts.gpm-master>