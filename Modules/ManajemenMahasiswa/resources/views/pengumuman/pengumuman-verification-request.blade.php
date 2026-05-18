<x-manajemenmahasiswa::layouts.admin>

    @push('styles')
        <style>
            .main-wrapper {
                background: transparent !important;
                box-shadow: none !important;
                padding: 0 !important;
            }

            .vr-header {
                margin-bottom: 28px;
            }
            .vr-header h4 {
                font-size: 1.5rem;
                font-weight: 700;
                color: #1e1b4b;
                margin-bottom: 4px;
            }
            .vr-header p {
                font-size: 0.95rem;
                color: #6b7280;
                margin-bottom: 0;
            }

            .vr-card {
                background: #fff;
                border-radius: 16px;
                border: 1px solid #e5e7eb;
                padding: 32px;
                margin-bottom: 24px;
            }

            .vr-section-title {
                font-size: 0.95rem;
                font-weight: 700;
                color: #1e1b4b;
                margin-bottom: 20px;
                padding-bottom: 12px;
                border-bottom: 1px solid #f3f4f6;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .vr-section-title svg {
                color: #6B4FF4;
                flex-shrink: 0;
            }

            /* Summary */
            .summary-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 16px;
                margin-bottom: 8px;
            }
            @media (max-width: 640px) {
                .summary-grid { grid-template-columns: 1fr; }
            }
            .summary-item label {
                display: block;
                font-size: 0.82rem;
                font-weight: 600;
                color: #9ca3af;
                margin-bottom: 4px;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            .summary-item span {
                font-size: 0.95rem;
                font-weight: 600;
                color: #374151;
            }
            .summary-title {
                grid-column: 1 / -1;
            }
            .summary-title span {
                font-size: 1.1rem;
                font-weight: 700;
                color: #1e1b4b;
            }

            /* Badge */
            .status-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 14px;
                border-radius: 50px;
                font-size: 0.8rem;
                font-weight: 700;
                letter-spacing: 0.02em;
            }
            .badge-pending {
                background: #fef3c7;
                color: #d97706;
                border: 1px solid #fde68a;
            }

            /* Info alert */
            .alert-info {
                background: #eff6ff;
                color: #1e40af;
                border: 1px solid #bfdbfe;
                border-radius: 12px;
                padding: 14px 18px;
                font-size: 0.9rem;
                font-weight: 500;
                margin-bottom: 24px;
                display: flex;
                align-items: flex-start;
                gap: 12px;
            }
            .alert-info svg {
                flex-shrink: 0;
                margin-top: 2px;
            }

            .alert-danger {
                background: #fee2e2;
                color: #991b1b;
                border: 1px solid #fca5a5;
                border-radius: 12px;
                padding: 14px 18px;
                font-size: 0.9rem;
                font-weight: 500;
                margin-bottom: 24px;
            }

            /* Form Controls */
            .form-group {
                margin-bottom: 22px;
            }
            .form-group label {
                display: block;
                font-size: 0.88rem;
                font-weight: 600;
                color: #374151;
                margin-bottom: 8px;
            }
            .form-group label .required {
                color: #ef4444;
                margin-left: 2px;
            }
            .form-select-custom {
                width: 100%;
                padding: 12px 16px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                font-size: 0.9rem;
                color: #374151;
                background: #fafafa;
                transition: all 0.25s ease;
                outline: none;
                font-family: inherit;
                appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 14px center;
                padding-right: 40px;
                cursor: pointer;
            }
            .form-select-custom:focus {
                border-color: #6B4FF4;
                background-color: #fff;
                box-shadow: 0 0 0 3px rgba(107, 79, 244, 0.12);
            }
            .form-textarea {
                width: 100%;
                padding: 12px 16px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                font-size: 0.9rem;
                color: #374151;
                background: #fafafa;
                transition: all 0.25s ease;
                outline: none;
                font-family: inherit;
                min-height: 100px;
                resize: vertical;
                line-height: 1.6;
            }
            .form-textarea:focus {
                border-color: #6B4FF4;
                background: #fff;
                box-shadow: 0 0 0 3px rgba(107, 79, 244, 0.12);
            }
            .form-textarea::placeholder {
                color: #9ca3af;
            }
            .form-error {
                font-size: 0.82rem;
                color: #ef4444;
                margin-top: 6px;
            }

            /* Verifier option styling */
            .verifier-role-tag {
                font-size: 0.75rem;
                font-weight: 600;
                color: #6B4FF4;
                background: #F5F3FF;
                padding: 2px 8px;
                border-radius: 6px;
                margin-left: 6px;
            }

            /* Buttons */
            .form-actions {
                display: flex;
                justify-content: flex-end;
                gap: 12px;
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #f3f4f6;
            }
            .btn-cancel-draft {
                padding: 12px 28px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                background: #fff;
                color: #374151;
                font-size: 0.9rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }
            .btn-cancel-draft:hover {
                border-color: #9ca3af;
                background: #f9fafb;
                color: #374151;
            }
            .btn-submit-verif {
                padding: 12px 28px;
                border: none;
                border-radius: 12px;
                background: #6B4FF4;
                color: #fff;
                font-size: 0.9rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }
            .btn-submit-verif:hover {
                background: #8266F5;
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(107, 79, 244, 0.3);
            }
        </style>
    @endpush

    <!-- Header -->
    <div class="vr-header">
        <h4>Pengajuan Verifikasi Pengumuman</h4>
        <p>Pengumuman Anda perlu diverifikasi oleh atasan sebelum dapat dipublikasikan</p>
    </div>

    <!-- Info Alert -->
    @if(session('info'))
        <div class="alert-info">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="alert-danger">
            <strong>Terdapat kesalahan pada input:</strong>
            <ul style="margin: 6px 0 0; padding-left: 18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Pengumuman Summary -->
    <div class="vr-card">
        <div class="vr-section-title">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
            Ringkasan Pengumuman
            <span class="status-badge badge-pending" style="margin-left: auto;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Menunggu Verifikasi
            </span>
        </div>

        <div class="summary-grid">
            <div class="summary-item summary-title">
                <label>Judul</label>
                <span>{{ $pengumuman->judul }}</span>
            </div>
            <div class="summary-item">
                <label>Kategori</label>
                <span>{{ ucfirst(str_replace('_', ' ', $pengumuman->kategori ?? '-')) }}</span>
            </div>
            <div class="summary-item">
                <label>Target Audiens</label>
                <span>{{ $pengumuman->target_audience === 'all' ? 'Semua' : ucfirst($pengumuman->target_audience) }}</span>
            </div>
        </div>
    </div>

    <!-- Verification Form -->
    <form action="{{ route('manajemenmahasiswa.pengumuman.verification.submit', $pengumuman->id) }}" method="POST" id="verificationForm">
        @csrf

        <div class="vr-card">
            <div class="vr-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Pilih Verifikator
            </div>

            <div class="form-group">
                <label>Verifikator <span class="required">*</span></label>
                <select name="verifier_id" class="form-select-custom" required id="verifierSelect">
                    <option value="">— Pilih Ketua Verifikator —</option>
                    @foreach($verifiers as $verifier)
                        @php
                            $roleLabel = $verifier->roles->pluck('name')->map(function($r) {
                                return ucwords(str_replace('_', ' ', $r));
                            })->implode(', ');
                        @endphp
                        <option value="{{ $verifier->id }}" {{ old('verifier_id') == $verifier->id ? 'selected' : '' }}>
                            {{ $verifier->name }} — {{ $roleLabel }}
                        </option>
                    @endforeach
                </select>
                @error('verifier_id') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label>Pesan ke Verifikator <span style="color: #9ca3af; font-weight: 400;">(opsional)</span></label>
                <textarea name="pesan_pengaju" class="form-textarea" placeholder="Contoh: Mohon diverifikasi untuk pengumuman kegiatan besok pagi...">{{ old('pesan_pengaju') }}</textarea>
                @error('pesan_pengaju') <span class="form-error">{{ $message }}</span> @enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('manajemenmahasiswa.pengumuman.index') }}" class="btn-cancel-draft">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                    Kembali
                </a>
                <button type="submit" class="btn-submit-verif" id="btnSubmitVerif">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Kirim Pengajuan Verifikasi
                </button>
            </div>
        </div>
    </form>

    @push('scripts')
        <script>
            // Prevent double-submit
            document.getElementById('verificationForm').addEventListener('submit', function() {
                const btn = document.getElementById('btnSubmitVerif');
                btn.disabled = true;
                btn.innerHTML = '<span style="display:inline-flex;align-items:center;gap:6px;"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite;"><circle cx="12" cy="12" r="10"/></svg> Mengirim...</span>';
            });
        </script>
        <style>
            @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        </style>
    @endpush

</x-manajemenmahasiswa::layouts.admin>
