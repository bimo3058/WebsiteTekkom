<x-manajemenmahasiswa::layouts.admin>

    @push('styles')
        <style>
            /* Token desain global SITKOM — disamakan dengan dashboard Super Admin
               (resources/views/components/sidebar.blade.php). Layout modul ini tidak
               mendefinisikan token tersebut, jadi harus dideklarasikan ulang di sini. */
            :root {
                --c-primary: #0B266E;
                --c-primary-hover: #091958;
                --c-primary-subtle: rgba(11, 38, 110, 0.08);
                --c-primary-border: #5C78B8;
                --c-bg: #F6F8FA;
                --c-fg: #0D0D12;
                --c-fg-sec: #353849;
                --c-fg-muted: #666D80;
                --c-fg-placeholder: #808897;
                --c-border: #DFE1E7;
                --c-border-strong: #C1C7CF;
                --c-success: #287F6E;
                --c-success-subtle: #DDF2EE;
                --c-error: #DF1C41;
                --c-error-subtle: #FADAE1;
                --c-warning: #956321;
                --c-warning-subtle: #F9ECCB;
                --c-sky: #0C4D6E;
                --c-sky-subtle: #D1F0F9;
                --shadow-card: 0px 1px 2px 0px rgba(228, 229, 231, 0.5);
            }

            .main-wrapper { background: transparent !important; box-shadow: none !important; padding: 0 !important; }

            /* ── Shell kotak: mengikuti dashboard Super Admin ───────────── */
            .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
            .dash-wrap { display: flex; flex-direction: column; height: calc(100vh - 60px); padding: 10px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif; }
            .dash-box { display: flex; flex-direction: column; flex: 1; min-height: 0; background: #fff; border: 1px solid var(--c-border, #DFE1E7); border-radius: 12px; box-shadow: var(--shadow-card, 0px 1px 2px 0px rgba(228,229,231,0.5)); overflow: hidden; width: 100%; box-sizing: border-box; }
            .dash-box-header { background: #fff; border-bottom: 1px solid var(--c-border, #DFE1E7); flex-shrink: 0; width: 100%; box-sizing: border-box; padding: 16px 24px; }
            .dash-box-body { flex: 1; overflow-y: auto; padding: 20px 24px; }
            .dash-box-body::-webkit-scrollbar { width: 6px; }
            .dash-box-body::-webkit-scrollbar-thumb { background: var(--c-border-strong, #C1C7CF); border-radius: 10px; }
            @media (max-width: 767px) {
                .sitkom-content { padding: 8px 8px 80px !important; display: block !important; overflow: visible !important; }
                .dash-wrap { height: auto !important; min-height: 0 !important; padding: 0; }
                .dash-box { flex: none !important; min-height: 0 !important; overflow: visible !important; border-radius: 10px; }
                .dash-box-header { padding: 12px 14px; position: sticky; top: 52px; z-index: 10; }
                .dash-box-body { overflow-y: visible !important; flex: none !important; padding: 14px; }
            }

            /* ── Kartu section: pola table-card / chart-card Super Admin ── */
            .vr-card {
                background: #fff;
                border: 1px solid var(--c-border, #DFE1E7);
                border-radius: 14px;
                box-shadow: var(--shadow-card, 0px 1px 2px 0px rgba(228,229,231,0.5));
                margin-bottom: 10px;
                overflow: hidden;
            }
            .vr-card-header {
                display: flex; align-items: center; gap: 8px;
                padding: 13px 18px;
                border-bottom: 1px solid var(--c-border, #DFE1E7);
                font-size: 13px; font-weight: 700; color: var(--c-fg, #0D0D12);
            }
            .vr-card-header svg { color: var(--c-primary, #0B266E); flex-shrink: 0; }
            .vr-card-body { padding: 18px; }

            /* ── Ringkasan ──────────────────────────────────────────────── */
            .summary-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
            @media (max-width: 640px) { .summary-grid { grid-template-columns: 1fr; } }
            .summary-item label {
                display: block;
                font-size: 11px; font-weight: 600;
                color: var(--c-fg-muted, #666D80);
                margin-bottom: 4px;
                letter-spacing: .03em;
            }
            .summary-item span { font-size: 13px; font-weight: 600; color: var(--c-fg-sec, #353849); }
            .summary-title { grid-column: 1 / -1; }
            .summary-title span { font-size: 16px; font-weight: 700; color: var(--c-fg, #0D0D12); }

            /* ── Badge ──────────────────────────────────────────────────── */
            .status-badge {
                display: inline-flex; align-items: center; gap: 5px;
                padding: 4px 9px; border-radius: 8px;
                font-size: 11px; font-weight: 700; letter-spacing: .02em;
            }
            .badge-pending { background: var(--c-warning-subtle, #F9ECCB); color: var(--c-warning, #956321); }

            /* ── Alerts ─────────────────────────────────────────────────── */
            .alert-info,
            .alert-danger {
                border-radius: 10px; padding: 12px 16px;
                font-size: 12px; font-weight: 500; margin-bottom: 10px;
            }
            .alert-info {
                background: var(--c-sky-subtle, #D1F0F9);
                color: var(--c-sky, #0C4D6E);
                border: 1px solid var(--c-sky, #0C4D6E);
                display: flex; align-items: flex-start; gap: 10px;
            }
            .alert-info svg { flex-shrink: 0; margin-top: 1px; }
            .alert-danger {
                background: var(--c-error-subtle, #FADAE1);
                color: var(--c-error, #DF1C41);
                border: 1px solid var(--c-error, #DF1C41);
            }

            /* ── Form controls ──────────────────────────────────────────── */
            .form-group { margin-bottom: 10px; }
            .form-group label {
                display: block;
                font-size: 12px; font-weight: 600; color: var(--c-fg-sec, #353849);
                margin-bottom: 6px;
            }
            .form-group label .required { color: var(--c-error, #DF1C41); margin-left: 2px; }
            .form-group label .hint { color: var(--c-fg-placeholder, #808897); font-weight: 400; }

            .form-select-custom,
            .form-textarea {
                width: 100%;
                box-sizing: border-box;
                padding: 9px 12px;
                border: 1px solid var(--c-border, #DFE1E7);
                border-radius: 8px;
                font-size: 13px;
                color: var(--c-fg-sec, #353849);
                background: #fff;
                transition: border-color .15s, box-shadow .15s;
                outline: none;
                font-family: inherit;
            }
            .form-select-custom:focus,
            .form-textarea:focus {
                border-color: var(--c-primary, #0B266E);
                box-shadow: 0 0 0 3px rgba(11, 38, 110, 0.1);
            }
            .form-select-custom {
                appearance: none;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23808897' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: right 12px center;
                padding-right: 36px;
                cursor: pointer;
            }
            .form-textarea { min-height: 100px; resize: vertical; line-height: 1.6; }
            .form-textarea::placeholder { color: var(--c-fg-placeholder, #808897); }
            .form-error { font-size: 11.5px; color: var(--c-error, #DF1C41); margin-top: 5px; display: block; }

            /* ── Actions ────────────────────────────────────────────────── */
            .form-actions {
                display: flex; align-items: center; gap: 8px; flex-wrap: wrap;
                margin-top: 16px; padding-top: 16px;
                border-top: 1px solid var(--c-border, #DFE1E7);
            }
            .fa-spacer { flex: 1; }

            .btn-cancel-draft {
                display: inline-flex; align-items: center; gap: 6px;
                padding: 8px 14px;
                border: 1px solid var(--c-border, #DFE1E7);
                border-radius: 8px;
                background: #fff;
                color: var(--c-fg-sec, #353849);
                font-size: 12px; font-weight: 600;
                cursor: pointer; text-decoration: none;
                transition: all .15s; font-family: inherit;
                box-shadow: 0 1px 2px rgba(0,0,0,.04);
            }
            .btn-cancel-draft:hover {
                background: var(--c-bg, #F6F8FA);
                border-color: var(--c-border-strong, #C1C7CF);
                color: var(--c-fg-sec, #353849);
            }

            .btn-submit-verif {
                display: inline-flex; align-items: center; gap: 6px;
                padding: 8px 16px;
                border: 1px solid var(--c-primary, #0B266E);
                border-radius: 8px;
                background: var(--c-primary, #0B266E);
                color: #fff;
                font-size: 12px; font-weight: 600;
                cursor: pointer; transition: all .15s; font-family: inherit;
                box-shadow: 0 2px 6px rgba(11, 38, 110, 0.3);
            }
            .btn-submit-verif:hover {
                background: var(--c-primary-hover, #091958);
                box-shadow: 0 4px 12px rgba(11, 38, 110, 0.4);
            }
            .btn-submit-verif:disabled { opacity: .7; cursor: default; }

            @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        </style>
    @endpush

    <div class="dash-wrap">
        <div class="dash-box">

            {{-- ── Header ─────────────────────────────────── --}}
            <div class="dash-box-header">
                <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
                    <div>
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:3px;">
                            <h1 style="font-size:22px; font-weight:700; color:var(--c-fg, #0D0D12); letter-spacing:-0.02em; line-height:1.2; margin:0;">Pengajuan Verifikasi</h1>
                            <span style="font-size:10px; font-weight:600; color:var(--c-primary, #0B266E); background:rgba(11,38,110,0.09); border:1px solid rgba(11,38,110,0.18); padding:2px 8px; border-radius:9999px; letter-spacing:0.03em;">Modul Mahasiswa</span>
                        </div>
                        <p style="font-size:12px; color:var(--c-fg-muted, #666D80); margin:0;">
                            Pengumuman Anda perlu diverifikasi oleh atasan sebelum dapat dipublikasikan
                        </p>
                    </div>

                    <a href="{{ route('manajemenmahasiswa.pengumuman.index') }}" class="btn-cancel-draft">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                        </svg>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>

            <div class="dash-box-body">

                {{-- Info Alert --}}
                @if(session('info'))
                    <div class="alert-info">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
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

                {{-- ── Ringkasan Pengumuman ─────────────────── --}}
                <div class="vr-card">
                    <div class="vr-card-header">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                        <span>Ringkasan Pengumuman</span>
                        <span class="status-badge badge-pending" style="margin-left: auto;">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Menunggu Verifikasi
                        </span>
                    </div>
                    <div class="vr-card-body">
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
                </div>

                {{-- ── Form Verifikasi ──────────────────────── --}}
                <form action="{{ route('manajemenmahasiswa.pengumuman.verification.submit', $pengumuman->id) }}" method="POST" id="verificationForm">
                    @csrf

                    <div class="vr-card">
                        <div class="vr-card-header">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <span>Pilih Verifikator</span>
                        </div>
                        <div class="vr-card-body">

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
                                <label>Pesan ke Verifikator <span class="hint">(opsional)</span></label>
                                <textarea name="pesan_pengaju" class="form-textarea" placeholder="Contoh: Mohon diverifikasi untuk pengumuman kegiatan besok pagi...">{{ old('pesan_pengaju') }}</textarea>
                                @error('pesan_pengaju') <span class="form-error">{{ $message }}</span> @enderror
                            </div>

                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('manajemenmahasiswa.pengumuman.index') }}" class="btn-cancel-draft">Batal</a>

                        <div class="fa-spacer"></div>

                        <button type="submit" class="btn-submit-verif" id="btnSubmitVerif">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2L11 13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            <span>Kirim Pengajuan Verifikasi</span>
                        </button>
                    </div>
                </form>

            </div> <!-- end dash-box-body -->
        </div> <!-- end dash-box -->
    </div> <!-- end dash-wrap -->

    @push('scripts')
        <script>
            // Prevent double-submit
            document.getElementById('verificationForm').addEventListener('submit', function() {
                const btn = document.getElementById('btnSubmitVerif');
                btn.disabled = true;
                btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="animation:spin 1s linear infinite;"><circle cx="12" cy="12" r="10"/></svg><span>Mengirim...</span>';
            });
        </script>
    @endpush

</x-manajemenmahasiswa::layouts.admin>
