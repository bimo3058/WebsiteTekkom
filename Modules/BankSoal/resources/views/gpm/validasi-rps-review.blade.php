<x-banksoal::layouts.gpm-master>

    @section('page-title', 'Validasi RPS')
    @section('page-subtitle', 'Periksa kelengkapan dokumen RPS')

    <style>
        .status-banner {
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 0.75rem;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }
        .status-icon {
            background-color: #fef08a;
            color: #d97706;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }
        .status-content h6 {
            color: #b45309;
            font-weight: 700;
            margin-bottom: 0.25rem;
            font-size: 0.95rem;
        }
        .status-content p {
            color: #92400e;
            margin: 0;
            font-size: 0.85rem;
        }

        .pdf-container {
            background-color: white;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            height: calc(100vh - 280px); /* Adjust based on your header/banner height */
            min-height: 500px;
            display: flex;
            flex-direction: column;
        }
        .pdf-header {
            padding: 0.75rem 1.25rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f8fafc;
        }
        .pdf-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .pdf-actions button {
            background: transparent;
            border: none;
            color: #64748b;
            padding: 0.25rem;
            border-radius: 0.25rem;
            margin-left: 0.5rem;
        }
        .pdf-actions button:hover {
            background: #e2e8f0;
            color: #1e293b;
        }
        .pdf-viewer {
            flex: 1;
            background-color: #f1f5f9;
            padding: 2rem;
            overflow-y: auto;
            display: flex;
            justify-content: center;
        }
        .pdf-mock-page {
            background-color: white;
            width: 100%;
            max-width: 600px;
            height: 800px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .skeleton-line { background-color: #f1f5f9; height: 1.5rem; border-radius: 0.25rem; }
        .skeleton-block { background-color: #f1f5f9; height: 8rem; border-radius: 0.5rem; flex: 1; }

        .form-card {
            background-color: white;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        .form-header {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            font-size: 1.1rem;
        }
        
        .question-item {
            margin-bottom: 1.25rem;
        }
        .question-text {
            font-size: 0.9rem;
            color: #334155;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .radio-group {
            display: flex;
            gap: 1.5rem;
        }
        .radio-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: #64748b;
            cursor: pointer;
        }
        .radio-label input[type="radio"] {
            accent-color: #2563eb;
            width: 1rem;
            height: 1rem;
        }

        .score-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 0;
            border-top: 1px dashed #e2e8f0;
            border-bottom: 1px dashed #e2e8f0;
            margin: 1.5rem 0;
        }
        .score-label {
            font-weight: 600;
            color: #64748b;
            font-size: 0.95rem;
        }
        .score-value {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1d4ed8;
        }

        .revision-note label {
            font-weight: 600;
            color: #475569;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: block;
        }
        .revision-note textarea {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 0.75rem;
            font-size: 0.85rem;
            color: #334155;
            resize: vertical;
            min-height: 100px;
        }
        .revision-note textarea:focus {
            outline: none;
            border-color: #94a3b8;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        .btn-kembalikan {
            flex: 1;
            background-color: white;
            border: 1px solid #ef4444;
            color: #ef4444;
            font-weight: 600;
            border-radius: 0.5rem;
            padding: 0.6rem;
            transition: 0.2s;
        }
        .btn-kembalikan:hover {
            background-color: #fef2f2;
        }
        .btn-setujui {
            flex: 1;
            background-color: #2563eb;
            border: 1px solid #2563eb;
            color: white;
            font-weight: 600;
            border-radius: 0.5rem;
            padding: 0.6rem;
            transition: 0.2s;
        }
        .btn-setujui:hover {
            background-color: #1d4ed8;
        }

        .history-card {
            background-color: #f8fafc;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            padding: 1.25rem;
        }
        .history-title {
            font-size: 0.75rem;
            font-weight: 700;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }
        .history-item {
            position: relative;
            padding-left: 1.25rem;
            margin-bottom: 1rem;
        }
        .history-item:last-child { margin-bottom: 0; }
        .history-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0.25rem;
            bottom: -1rem;
            width: 2px;
            background-color: #e2e8f0;
        }
        .history-item:last-child::before { display: none; }
        
        .history-marker {
            position: absolute;
            left: -0.25rem;
            top: 0.35rem;
            width: 0.5rem;
            height: 0.5rem;
            border-radius: 50%;
        }
        .marker-blue { background-color: #3b82f6; }
        .marker-yellow { background-color: #eab308; }

        .history-content h6 {
            font-size: 0.85rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.15rem;
        }
        .history-content p {
            font-size: 0.75rem;
            color: #64748b;
            margin: 0;
        }
    </style>

    <div class="container-fluid py-4 px-4 px-xl-5">
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const topbarTitle = document.getElementById('topbar-title');
                const topbarSubtitle = document.getElementById('topbar-subtitle');
                if(topbarTitle) topbarTitle.textContent = "Validasi RPS";
                if(topbarSubtitle) topbarSubtitle.textContent = "Periksa kelengkapan dokumen RPS";
            });
        </script>

        <!-- Status Banner -->
        <div class="status-banner mb-4">
            <div class="status-icon">!</div>
            <div class="status-content">
                <h6>Status: {{ ucfirst($rps->status) }}</h6>
                <p>
                    Mata Kuliah: {{ $rps->mk_nama }} ({{ $rps->kode }}) &bull; 
                    Diserahkan oleh: 
                    @php
                        $dosensList = !empty($rps->dosens_list) ? array_map('trim', explode(',', $rps->dosens_list)) : [];
                    @endphp
                    @forelse($dosensList as $index => $dosenItem)
                        @php
                            [$initials, $dosenName] = explode('|', $dosenItem, 2);
                        @endphp
                        {{ $dosenName }}{{ $index < count($dosensList) - 1 ? ', ' : '' }}
                    @empty
                        -
                    @endforelse
                </p>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: PDF Viewer -->
            <div class="col-lg-7 col-xl-8">
                <div class="pdf-container">
                    <div class="pdf-header">
                        <div class="pdf-title">
                            <i class="bi bi-file-earmark-pdf text-danger"></i> {{ basename($rps->dokumen) }}
                        </div>
                        <div class="pdf-actions">
                            <button><i class="bi bi-zoom-out"></i></button>
                            <button><i class="bi bi-zoom-in"></i></button>
                            <button><i class="bi bi-download"></i></button>
                        </div>
                    </div>
                    <div class="pdf-viewer">
                        <!-- Mock PDF Page for UI purpose -->
                        <div class="pdf-mock-page">
                            <div class="skeleton-line" style="width: 60%; margin: 1rem auto 2rem;"></div>
                            
                            <div class="skeleton-line" style="width: 100%;"></div>
                            <div class="skeleton-line" style="width: 100%;"></div>
                            <div class="skeleton-line" style="width: 80%;"></div>

                            <div class="d-flex gap-3 mt-4">
                                <div class="skeleton-block"></div>
                                <div class="skeleton-block"></div>
                            </div>

                            <div class="skeleton-line" style="width: 100%; margin-top: 2rem;"></div>
                            <div class="skeleton-line" style="width: 100%;"></div>
                            <div class="skeleton-line" style="width: 60%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Form Validasi -->
            <div class="col-lg-5 col-xl-4">
                <div class="form-card">
                    <div class="form-header">
                        <i class="bi bi-card-checklist text-primary"></i> Form Penilaian GPM
                    </div>

                    <form action="#" method="POST">
                        @csrf
                        <input type="hidden" name="rps_id" value="{{ $rps->rps_id }}">
                        
                        @forelse($parameters as $index => $param)
                        <div class="question-item">
                            <div class="question-text">{{ $index + 1 }}. {{ $param->aspek }}</div>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="parameter_{{ $param->id }}" value="1" {{ isset($existingReview) ? 'checked' : '' }}> Sesuai
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="parameter_{{ $param->id }}" value="0"> Tidak Sesuai
                                </label>
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-info" role="alert">
                            Tidak ada parameter penilaian yang tersedia
                        </div>
                        @endforelse

                        <div class="score-box">
                            <div class="score-label">Skor Evaluasi:</div>
                            <div class="score-value">{{ isset($existingReview) ? $existingReview->nilai_akhir : '0' }}/100</div>
                        </div>

                        <div class="revision-note">
                            <label for="catatan">Catatan Revisi</label>
                            <textarea id="catatan" name="catatan" placeholder="Masukkan detail perbaikan jika diperlukan...">{{ isset($existingReview) ? $existingReview->catatan : '' }}</textarea>
                        </div>

                        <div class="action-buttons">
                            <button type="button" class="btn btn-kembalikan">Kembalikan</button>
                            <button type="submit" class="btn btn-setujui">Setujui RPS</button>
                        </div>
                    </form>
                </div>

                <div class="history-card">
                    <div class="history-title">HISTORY LOG</div>
                    
                    @forelse($history as $item)
                    <div class="history-item">
                        <div class="history-marker {{ $loop->first ? 'marker-blue' : 'marker-yellow' }}"></div>
                        <div class="history-content">
                            <h6>{{ ucfirst($item->action) }}</h6>
                            <p>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y - H:i') }}</p>
                            @if($item->description)
                            <p>{{ $item->description }}</p>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="alert alert-sm alert-secondary" role="alert">
                        Belum ada riwayat aktivitas
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</x-banksoal::layouts.gpm-master>