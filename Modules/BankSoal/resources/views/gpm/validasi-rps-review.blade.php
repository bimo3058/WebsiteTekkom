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
            transition: all 0.3s ease;
        }
        .status-banner.status-revisi {
            background-color: #fee2e2;
            border-color: #fecaca;
        }
        .status-banner.status-disetujui {
            background-color: #dcfce7;
            border-color: #bbf7d0;
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
            transition: all 0.3s ease;
        }
        .status-revisi .status-icon {
            background-color: #fecaca;
            color: #dc2626;
        }
        .status-disetujui .status-icon {
            background-color: #bbf7d0;
            color: #059669;
        }
        .status-content h6 {
            color: #b45309;
            font-weight: 700;
            margin-bottom: 0.25rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        .status-revisi .status-content h6 {
            color: #991b1b;
        }
        .status-disetujui .status-content h6 {
            color: #166534;
        }
        .status-content p {
            color: #92400e;
            margin: 0;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        .status-revisi .status-content p {
            color: #7f1d1d;
        }
        .status-disetujui .status-content p {
            color: #15803d;
        }

        .pdf-container {
            background-color: white;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            min-height: 500px;
            flex:1;
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
            min-height: 400px;
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
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .question-text .bobot {
            font-weight: 600;
            color: #2563eb;
            background-color: #dbeafe;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.8rem;
            white-space: nowrap;
            margin-left: 1rem;
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
            cursor: pointer;
        }
        .btn-setujui:hover:not(:disabled) {
            background-color: #1d4ed8;
        }
        .btn-setujui:disabled {
            background-color: #cbd5e1;
            border-color: #cbd5e1;
            color: #94a3b8;
            cursor: not-allowed;
            opacity: 0.6;
        }
        .btn-setujui:disabled:hover {
            background-color: #cbd5e1;
        }

        .btn-secondary {
            background-color: #6b7280;
            border: 1px solid #6b7280;
            color: white;
            font-weight: 600;
            border-radius: 0.5rem;
            padding: 0.6rem;
            transition: 0.2s;
            cursor: pointer;
        }
        .btn-secondary:hover {
            background-color: #4b5563;
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

        .snackbar {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            min-width: 300px;
            max-width: 500px;
            padding: 14px 16px;
            padding-right: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
            z-index: 9999;
            animation: slideUpIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes slideUpIn {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        @keyframes slideOutDown {
            from {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
            to {
                opacity: 0;
                transform: translateX(-50%) translateY(20px);
            }
        }

        .snackbar-success {
            background-color: #10b981;
            color: #ffffff;
            border-left: 4px solid #059669;
        }

        .snackbar-error {
            background-color: #ef4444;
            color: #ffffff;
            border-left: 4px solid #dc2626;
        }

        .snackbar i {
            flex-shrink: 0;
            font-size: 18px;
        }

        .snackbar span {
            flex: 1;
        }

        .snackbar-close {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: inherit;
            opacity: 0.8;
            padding: 4px 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.2s;
        }

        .snackbar-close:hover {
            opacity: 1;
        }

        .w-100 {
            width: 100%;
        }

        @media (max-width: 600px) {
            .snackbar {
                bottom: 16px;
                left: 16px;
                right: 16px;
                transform: none;
                max-width: none;
            }
            
            @keyframes slideUpIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            @keyframes slideOutDown {
                from {
                    opacity: 1;
                    transform: translateY(0);
                }
                to {
                    opacity: 0;
                    transform: translateY(20px);
                }
            }
        }
    </style>

    <div class="container-fluid py-4 px-4 px-xl-5">
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const topbarTitle = document.getElementById('topbar-title');
                const topbarSubtitle = document.getElementById('topbar-subtitle');
                if(topbarTitle) topbarTitle.textContent = "Validasi RPS";
                if(topbarSubtitle) topbarSubtitle.textContent = "Periksa kelengkapan dokumen RPS";

                window.hitungSkor = function() {
                    const form = document.getElementById('validasiForm');
                    const nilaiAkhirEl = document.getElementById('nilaiAkhir');
                    let totalNilai = 0;

                    const inputs = form.querySelectorAll('input[type="radio"]:checked');
                    
                    inputs.forEach(input => {
                        if (input.value === '1') {
                            totalNilai += parseInt(input.getAttribute('data-bobot')) || 0;
                        }
                    });

                    nilaiAkhirEl.textContent = totalNilai + '/100';
                    updateButtonState(totalNilai);
                };

                window.updateButtonState = function(score) {
                    const MIN_SCORE = 60;
                    const btnSetujui = document.getElementById('btnSetujui');
                    
                    if (score < MIN_SCORE) {
                        btnSetujui.disabled = true;
                        btnSetujui.setAttribute('title', 'Nilai di bawah standar, ajukan revisi terlebih dahulu');
                    } else {
                        btnSetujui.disabled = false;
                        btnSetujui.setAttribute('title', '');
                    }
                };

                window.validateParametersNotEmpty = function() {
                    const form = document.getElementById('validasiForm');
                    const radioLabels = form.querySelectorAll('.question-item');
                    let emptyParameters = [];

                    radioLabels.forEach((questionItem, index) => {
                        const radioButtons = questionItem.querySelectorAll('input[type="radio"]');
                        const isChecked = Array.from(radioButtons).some(radio => radio.checked);
                        
                        if (!isChecked) {
                            const questionText = questionItem.querySelector('.question-text span:first-child')?.textContent || `Parameter ${index + 1}`;
                            emptyParameters.push(questionText);
                        }
                    });

                    if (emptyParameters.length > 0) {
                        const message = `Parameter belum lengkap: ${emptyParameters.join(', ')}`;
                        showSnackbar(message, 'error');
                        return false;
                    }

                    return true;
                };

                const btnKembalikan = document.getElementById('btnKembalikan');
                const btnSetujui = document.getElementById('btnSetujui');
                const form = document.getElementById('validasiForm');
                const actionInput = document.getElementById('actionInput');
                const catatanTextarea = document.getElementById('catatan');

                updateButtonState(0);

                if (btnKembalikan) {
                    btnKembalikan.addEventListener('click', function(e) {
                        e.preventDefault();

                        if (!validateParametersNotEmpty()) {
                            return;
                        }

                        const catatan = catatanTextarea.value.trim();

                        if (!catatan) {
                            showSnackbar('Catatan revisi harus diisi sebelum mengembalikan RPS', 'error');
                            catatanTextarea.focus();
                            catatanTextarea.style.borderColor = '#ef4444';
                            setTimeout(() => {
                                catatanTextarea.style.borderColor = '#e2e8f0';
                            }, 3000);
                            return;
                        }

                        actionInput.value = 'revisi';
                        submitForm();
                    });
                }

                if (btnSetujui) {
                    btnSetujui.addEventListener('click', function(e) {
                        e.preventDefault();

                        if (!validateParametersNotEmpty()) {
                            return;
                        }

                        actionInput.value = 'setuju';
                        submitForm();
                    });

                    btnSetujui.addEventListener('mousedown', function(e) {
                        if (this.disabled) {
                            e.preventDefault();
                            showSnackbar('Nilai di bawah standar, ajukan revisi terlebih dahulu', 'error');
                        }
                    });
                }

                function submitForm() {
                    const formData = new FormData(form);

                    fetch('{{ route("banksoal.rps.gpm.validasi-rps.store") }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData,
                        credentials: 'same-origin'
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(data => {
                                throw new Error(data.message || 'Terjadi kesalahan');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        const message = data.message + ' (Skor: ' + data.nilai_akhir + '/100)';
                        showSnackbar(message, 'success');

                        updateStatusBanner(data.status);

                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showSnackbar(error.message || 'Gagal menyimpan validasi', 'error');
                    });
                }

                function updateStatusBanner(status) {
                    const banner = document.getElementById('statusBanner');
                    const statusIcon = document.getElementById('statusIcon');
                    const statusText = document.getElementById('statusText');

                    banner.classList.remove('status-revisi', 'status-disetujui');

                    if (status === 'revisi') {
                        banner.classList.add('status-revisi');
                        statusIcon.textContent = '◄';
                        statusText.textContent = 'Revisi';
                    } else if (status === 'disetujui') {
                        banner.classList.add('status-disetujui');
                        statusIcon.textContent = '✓';
                        statusText.textContent = 'Disetujui';
                    }
                }

                function showSnackbar(message, type = 'success') {
                    const snackbar = document.createElement('div');
                    snackbar.className = `snackbar snackbar-${type}`;
                    snackbar.setAttribute('role', 'alert');

                    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
                    snackbar.innerHTML = `
                        <i class="${icon}"></i>
                        <span>${message}</span>
                        <button type="button" class="snackbar-close" aria-label="Tutup">&times;</button>
                    `;

                    document.body.appendChild(snackbar);

                    const closeBtn = snackbar.querySelector('.snackbar-close');
                    closeBtn.addEventListener('click', function() {
                        dismissSnackbar(snackbar);
                    });

                    setTimeout(() => {
                        dismissSnackbar(snackbar);
                    }, 5000);

                function dismissSnackbar(snackbar) {
                    if (!snackbar) return;
                    snackbar.style.animation = 'slideOutDown 0.3s ease-out forwards';

                    setTimeout(() => {
                        if (snackbar.parentElement) {
                            snackbar.remove();
                        }
                    }, 300);
                }
            });
        </script>

        <!-- Status Banner -->
        <div class="status-banner mb-4" id="statusBanner">
            <div class="status-icon" id="statusIcon">!</div>
            <div class="status-content">
                <h6>Status: <span id="statusText">{{ ucfirst($rps->status) }}</span></h6>
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

        <div class="row g-4 align-items-stretch">
            <div class="col-lg-7 col-xl-8 d-flex flex-column">
                <div class="pdf-container" style="flex:0.55;">
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
                        <iframe id="pdfFrame" 
                                src="{{ route('banksoal.rps.gpm.validasi-rps.preview', ['rpsId' => $rps->rps_id]) }}"
                                style="width: 100%; height: 100%; border: none; background: #f1f5f9;">
                        </iframe>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-xl-4 d-flex flex-column">
                <div class="form-card">
                    <div class="form-header">
                        <i class="bi bi-card-checklist text-primary"></i> Form Penilaian GPM
                    </div>

                    <form id="validasiForm" method="POST">
                        @csrf
                        <input type="hidden" name="rps_id" value="{{ $rps->rps_id }}">
                        <input type="hidden" name="action" id="actionInput" value="">
                        <div style="flex: 1; overflow-y: auto; max-height: 320px; padding-right: 0.5rem; margin-bottom: 0.5rem;">
                            @forelse($parameters as $index => $param)
                            <div class="question-item">
                                <div class="question-text">
                                    <span>{{ $index + 1 }}. {{ $param->aspek }}</span>
                                    <span class="bobot">{{ $param->bobot }} poin</span>
                                </div>
                                <div class="radio-group">
                                    <label class="radio-label">
                                        <input type="radio" name="parameter_{{ $param->id }}" value="1" data-bobot="{{ $param->bobot }}" onchange="hitungSkor()"> Sesuai
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" name="parameter_{{ $param->id }}" value="0" data-bobot="{{ $param->bobot }}" onchange="hitungSkor()"> Tidak Sesuai
                                    </label>
                                </div>
                            </div>
                            @empty
                            <div class="alert alert-info" role="alert">
                                Tidak ada parameter penilaian yang tersedia
                            </div>
                            @endforelse
                        </div>
                        <div class="score-box">
                            <div class="score-label">Skor Evaluasi:</div>
                            <div class="score-value" id="nilaiAkhir">{{ isset($existingReview) ? $existingReview->nilai_akhir : '0' }}/100</div>
                        </div>

                        <div class="revision-note">
                            <label for="catatan">Catatan Revisi</label>
                            <textarea id="catatan" name="catatan" placeholder="Masukkan detail perbaikan jika diperlukan...">{{ isset($existingReview) ? $existingReview->catatan : '' }}</textarea>
                        </div>

                        <div class="action-buttons">
                            <button type="button" class="btn btn-kembalikan" id="btnKembalikan">Kembalikan</button>
                            <button type="button" class="btn btn-setujui" id="btnSetujui">Setujui RPS</button>
                        </div>
                    </form>

                    <div style="margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary w-100" onclick="window.history.back()">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </button>
                    </div>
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