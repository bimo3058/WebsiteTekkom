<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard Mahasiswa">
    @php
        /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\DaftarPraktikan[] $daftarPraktikan */
        /** @var \Modules\EOffice\Models\Praktikum|null $terdaftarDi */
        /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Tugas[] $tugasMendatang */
        /** @var \Illuminate\Support\Collection|object[] $nilaiList */
        /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Pengumuman[] $pengumuman */

        $name = auth()->user()->name;
        $nameParts = explode(' ', $name);
        $firstName = $nameParts[0];
        $pct = isset($absensiStat) && $absensiStat['total'] > 0
            ? round($absensiStat['hadir'] / $absensiStat['total'] * 100) : 0;
    @endphp

    @if($belumTerdaftar)

        {{-- ── BELUM TERDAFTAR STATE ─────────────────────────────────────────── --}}

        {{-- Hero Banner --}}
        <div
            style="background:linear-gradient(135deg,#0E1E54 0%,#0B266E 60%,#4C619A 100%);border-radius:16px;padding:24px 28px;flex-shrink:0;position:relative;overflow:hidden;">
            {{-- Decorative circles --}}
            <div
                style="position:absolute;right:-40px;top:-40px;width:200px;height:200px;border-radius:999px;background:radial-gradient(circle,rgba(255,255,255,.07),transparent 70%);pointer-events:none;">
            </div>
            <div
                style="position:absolute;right:60px;bottom:-30px;width:120px;height:120px;border-radius:999px;background:radial-gradient(circle,rgba(255,255,255,.04),transparent 70%);pointer-events:none;">
            </div>

            <div style="position:relative;z-index:1;">
                <div
                    style="font-size:11px;font-weight:600;color:rgba(255,255,255,.6);text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px;">
                    Manajemen Praktikum · SIPERKOM</div>
                <h2
                    style="font-family:'Inter Tight',sans-serif;font-size:24px;font-weight:700;color:#fff;margin:0 0 8px;line-height:1.2;">
                    Halo, {{ $firstName }}! 👋</h2>
                <p style="font-size:13.5px;color:rgba(255,255,255,.78);margin:0 0 20px;line-height:1.5;max-width:480px;">
                    Belum terdaftar di kelas praktikum. Ikuti langkah di bawah untuk bergabung ke kelas praktikum {{ $semesterLabel }}.
                </p>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:10px;font-family:'Inter Tight',sans-serif;font-size:13.5px;font-weight:600;background:rgba(255,255,255,0.1);color:#fff;">
                        Mohon tunggu Admin mendaftarkan Anda ke kelas praktikum.
                    </span>
                </div>
            </div>
        </div>

        {{-- Stepper pendaftaran --}}
        <div style="background:#F9FAFB;border:1px solid #DFE1E7;border-radius:16px;padding:18px 24px;flex-shrink:0;">
            <div
                style="font-size:11px;font-weight:600;color:#666D80;text-transform:uppercase;letter-spacing:.06em;margin-bottom:16px;">
                Langkah Bergabung ke Kelas Praktikum</div>
            <div style="display:grid;grid-template-columns:1fr auto 1fr auto 1fr auto 1fr;align-items:flex-start;gap:0;">
                {{-- Step 1: Unggah IRS --}}
                <div style="text-align:center;">
                    <div
                        style="width:36px;height:36px;border-radius:999px;background:#0B266E;color:#fff;display:flex;align-items:center;justify-content:center;font:700 13px/1 'Inter Tight',sans-serif;margin:0 auto 8px;">
                        1</div>
                    <div style="font-size:13px;font-weight:600;color:#0D0D12;">Unggah IRS</div>
                    <div style="font-size:11px;color:#666D80;margin-top:2px;">Daftarkan dirimu saat periode dibuka</div>
                </div>
                <div style="height:2px;background:#DFE1E7;margin-top:18px;width:100%;min-width:20px;"></div>
                {{-- Step 2: Tunggu Verifikasi --}}
                <div style="text-align:center;">
                    <div
                        style="width:36px;height:36px;border-radius:999px;background:#ECEFF3;color:#666D80;display:flex;align-items:center;justify-content:center;font:700 13px/1 'Inter Tight',sans-serif;margin:0 auto 8px;">
                        2</div>
                    <div style="font-size:13px;font-weight:600;color:#0D0D12;">Tunggu Verifikasi</div>
                    <div style="font-size:11px;color:#666D80;margin-top:2px;">Koordinator memverifikasi IRS kamu</div>
                </div>
                <div style="height:2px;background:#DFE1E7;margin-top:18px;width:100%;min-width:20px;"></div>
                {{-- Step 3: Masukkan Kode --}}
                <div style="text-align:center;">
                    <div
                        style="width:36px;height:36px;border-radius:999px;background:#ECEFF3;color:#666D80;display:flex;align-items:center;justify-content:center;font:700 13px/1 'Inter Tight',sans-serif;margin:0 auto 8px;">
                        3</div>
                    <div style="font-size:13px;font-weight:600;color:#0D0D12;">Masukkan Kode</div>
                    <div style="font-size:11px;color:#666D80;margin-top:2px;">Kode dari Koordinator / Dashboard</div>
                </div>
                <div style="height:2px;background:#DFE1E7;margin-top:18px;width:100%;min-width:20px;"></div>
                {{-- Step 4: Bergabung --}}
                <div style="text-align:center;">
                    <div
                        style="width:36px;height:36px;border-radius:999px;background:#ECEFF3;color:#666D80;display:flex;align-items:center;justify-content:center;font:700 13px/1 'Inter Tight',sans-serif;margin:0 auto 8px;">
                        4</div>
                    <div style="font-size:13px;font-weight:600;color:#0D0D12;">Mulai Praktikum</div>
                    <div style="font-size:11px;color:#666D80;margin-top:2px;">Akses modul, absensi &amp; tugas</div>
                </div>
            </div>
        </div>

        {{-- IRS Disetujui — siap gabung --}}

        {{-- Pengumuman Sistem Global --}}
        @if(isset($pengumuman) && $pengumuman->isNotEmpty())
        <div class="sec-head" style="margin-top:20px;">
            <span class="sec-bar"></span>
            <span class="sec-title">Pengumuman Sistem</span>
            <span class="sec-rule"></span>
        </div>

        <div class="mp-card flex-shrink-0" style="margin-top:10px;">
            <div class="overflow-y-auto" style="max-height: 320px;">
                @foreach($pengumuman as $p)
                @if($p->tipe_sistem === 'buka')
                <a href="{{ route('eoffice.manprak.mahasiswa.daftar-asprak.index', ['praktikum_id' => $p->praktikum_id]) }}" style="text-decoration:none;display:block;padding:12px 20px;border-bottom:1px solid #ECEFF3;transition:background 0.2s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='transparent'">
                    <div style="font-size:13px;font-weight:600;color:#0B266E;margin-bottom:4px;">{{ $p->judul }} <span style="font-size:10px;margin-left:4px;" class="mp-badge success sm">Buka</span></div>
                    <div style="font-size:12px;color:#666D80;line-height:1.5;" class="line-clamp-2">{{ $p->konten }}</div>
                    <div style="font-size:11px;color:#808897;margin-top:6px;">{{ $p->created_at->diffForHumans() }}</div>
                </a>
                @else
                <div style="padding:12px 20px;border-bottom:1px solid #ECEFF3;">
                    <div style="font-size:13px;font-weight:600;color:#0D0D12;margin-bottom:4px;">{{ $p->judul }}</div>
                    <div style="font-size:12px;color:#666D80;line-height:1.5;" class="line-clamp-2">{{ $p->konten }}</div>
                    <div style="font-size:11px;color:#808897;margin-top:6px;">{{ $p->created_at->diffForHumans() }}</div>
                </div>
                @endif
                @endforeach
            </div>
        </div>
        @endif

        {{-- Empty state if nothing to show --}}
        @if(!isset($daftarPraktikan) || $daftarPraktikan->isEmpty())
            <div class="mp-card flex-shrink-0" style="padding:40px;text-align:center;">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                    stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                    <path d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z" />
                </svg>
                <div style="font-size:13px;font-weight:500;color:#666D80;">Anda belum terdaftar di kelas praktikum manapun.</div>
                <div style="font-size:12px;color:#808897;margin-top:4px;">Silakan hubungi Admin atau Koordinator untuk informasi lebih lanjut.</div>
            </div>
        @endif

    @else

        {{-- ── SUDAH TERDAFTAR STATE ─────────────────────────────────────────── --}}

        {{-- Page Header --}}
        <x-slot name="header">
            <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
                <div>
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                        <h1 class="mp-page-title">Dashboard Mahasiswa</h1>
                        <span class="mp-badge neutral sm"><span class="dot"></span>Mahasiswa</span>
                    </div>
                    <p class="mp-page-sub">Halo, <strong style="color: #0D0D12;">{{ $firstName }}</strong> ·
                {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} · {{ $semesterLabel }}
                    </p>
                </div>
            </div>
        </x-slot>

        {{-- Stat Cards --}}
        <div class="mp-stats-grid cols-3">
            {{-- Card 1: Praktikum Aktif --}}
            <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; padding:16px 20px; box-shadow:0 1px 3px rgba(0,0,0,.06);">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                    <div class="mp-stat-icon sky">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z" />
                        </svg>
                    </div>
                    <div class="mp-stat-label" style="font-size:14px; color:#0D0D12; margin:0; font-weight:600;">Praktikum Aktif</div>
                </div>
                <div class="mp-stat-value" style="font-size:32px; color:#0D0D12;">{{ count($daftarPraktikan) }}</div>
                <div class="mp-stat-sub" style="font-size:13px; color:#666D80;">praktikum terdaftar</div>
            </div>

            {{-- Card 2: Tugas Mendatang --}}
            <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; padding:16px 20px; box-shadow:0 1px 3px rgba(0,0,0,.06);">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                    <div class="mp-stat-icon yellow">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                    </div>
                    <div class="mp-stat-label" style="font-size:14px; color:#0D0D12; margin:0; font-weight:600;">Tugas Mendatang</div>
                </div>
                <div class="mp-stat-value" style="font-size:32px; color:#0D0D12;">{{ count($tugasMendatang ?? []) }}</div>
                <div class="mp-stat-sub" style="font-size:13px; color:#666D80;">belum dikumpulkan</div>
            </div>

            {{-- Card 3: Tugas Terlambat --}}
            <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; padding:16px 20px; box-shadow:0 1px 3px rgba(0,0,0,.06);">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                    <div class="mp-stat-icon red">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                    </div>
                    <div class="mp-stat-label" style="font-size:14px; color:#0D0D12; margin:0; font-weight:600;">Tugas Terlambat</div>
                </div>
                <div class="mp-stat-value" style="font-size:32px; color:#DF1C41;">{{ count($tugasTerlambat ?? []) }}</div>
                <div class="mp-stat-sub" style="font-size:13px; color:#666D80;">melewati tenggat waktu</div>
            </div>
        </div>



        <div class="flex gap-[20px] flex-col lg:flex-row mb-1 items-start">
            
            {{-- Pengumuman Praktikum Terdaftar --}}
            <div class="mp-card flex-1 min-w-0 w-full lg:w-1/2">
                <div class="mp-card-header" style="flex-shrink:0;">
                    <span class="mp-card-title">Pengumuman Praktikum</span>
                </div>
                <div class="overflow-y-auto" style="max-height: 480px;">
                    @forelse($pengumumanPraktikum ?? [] as $p)
                        <div style="padding:16px 20px;border-bottom:1px solid #ECEFF3;">
                            <div style="font-size:11px;font-weight:600;color:#6366F1;text-transform:uppercase;margin-bottom:6px;letter-spacing:0.04em;">
                                {{ $p->praktikum?->nama ?? 'Praktikum' }}
                            </div>
                            <div style="font-size:14px;font-weight:700;color:#0D0D12;margin-bottom:6px;line-height:1.4;">{{ $p->judul }}</div>
                            <div style="font-size:13px;color:#4B5563;line-height:1.6;" class="line-clamp-3">{{ $p->konten }}</div>
                            <div style="font-size:11px;color:#808897;margin-top:8px;">Dipublikasikan {{ $p->created_at->diffForHumans() }}</div>
                        </div>
                    @empty
                        <div style="padding:48px 20px;text-align:center;">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" />
                            </svg>
                            <div style="font-size:14px;font-weight:500;color:#353849;">Belum ada pengumuman</div>
                            <div style="font-size:12px;color:#808897;margin-top:4px;">Pembaruan praktikum akan muncul di sini.</div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Pengumuman Rekrutmen & Status --}}
            <div class="flex-1 w-full lg:w-1/2 flex flex-col gap-[20px]">
                
                {{-- Status Seleksi (Jika mendaftar) --}}
                @if(isset($statusAsprak) && $statusAsprak)
                    <div class="mp-card" style="padding:20px;background:linear-gradient(to right, #F8FAFC, #FFFFFF);">
                        <div style="font-size:11px;font-weight:600;color:#666D80;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">Status Seleksi Asisten/Koor</div>
                        <div style="font-size:15px;font-weight:700;color:#0D0D12;margin-bottom:2px;">{{ $statusAsprak->praktikum?->nama ?? '—' }}</div>
                        <div style="font-size:13px;color:#666D80;margin-bottom:12px;">Posisi: Asisten Praktikum</div>
                        
                        <div style="display:flex;align-items:center;gap:12px;">
                            @if($statusAsprak->status === 'pending')
                                <span class="mp-badge warning md" style="font-size:13px;padding:6px 12px;"><span class="dot"></span>Menunggu Review Berkas</span>
                                <span style="font-size:12px;color:#666D80;">Sedang dalam pengecekan panitia seleksi.</span>
                            @elseif($statusAsprak->status === 'approved')
                                <span class="mp-badge success md" style="font-size:13px;padding:6px 12px;"><span class="dot"></span>Lolos Seleksi</span>
                                <span style="font-size:12px;color:#10B981;font-weight:500;">Selamat, Anda telah diterima!</span>
                            @else
                                <span class="mp-badge error md" style="font-size:13px;padding:6px 12px;"><span class="dot"></span>Tidak Lolos</span>
                                <span style="font-size:12px;color:#DF1C41;font-weight:500;">Tetap semangat dan coba lagi kesempatan berikutnya.</span>
                            @endif
                        </div>
                    </div>
                @endif
                
                {{-- Tabel Info Rekrutmen --}}
                <div class="mp-card flex-1 min-w-0">
                    <div class="mp-card-header" style="flex-shrink:0;background:#F9FAFB;border-bottom:1px solid #ECEFF3;">
                        <span class="mp-card-title">Pengumuman Pendaftaran Koordinator dan Asisten</span>
                    </div>
                    <div class="overflow-y-auto" style="max-height: 480px;">
                        @forelse($pengumumanRekrutmen ?? [] as $p)
                            @php
                                // Detect if it's a reminder or closing soon based on tags/logic if available
                                $isBuka = $p->tipe_sistem === 'buka';
                                $isTutup = $p->tipe_sistem === 'tutup';
                            @endphp
                            <div style="padding:16px 20px;border-bottom:1px solid #ECEFF3;display:flex;gap:16px;">
                                <div style="flex-shrink:0;">
                                    @if($isBuka)
                                        <div style="width:40px;height:40px;border-radius:10px;background:#ECFDF5;border:1px solid #D1FAE5;display:flex;align-items:center;justify-content:center;color:#059669;">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                                <path d="M7 11V7a5 5 0 019.9-1"/>
                                            </svg>
                                        </div>
                                    @else
                                        <div style="width:40px;height:40px;border-radius:10px;background:#FEF2F2;border:1px solid #FEE2E2;display:flex;align-items:center;justify-content:center;color:#DC2626;">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                                <path d="M7 11V7a5 5 0 0110 0v4"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                                        <div style="font-size:14px;font-weight:700;color:#0D0D12;">{{ $p->judul }}</div>
                                        @if($isBuka)
                                            <span class="mp-badge success sm" style="font-size:10px;padding:2px 6px;">Buka Pendaftaran</span>
                                        @else
                                            <span class="mp-badge error sm" style="font-size:10px;padding:2px 6px;">Pendaftaran Ditutup</span>
                                        @endif
                                    </div>
                                    <div style="font-size:13px;color:#4B5563;line-height:1.6;" class="line-clamp-2">{{ $p->konten }}</div>
                                    <div style="font-size:11px;color:#808897;margin-top:8px;">{{ $p->created_at->diffForHumans() }}</div>
                                    
                                    @if($isBuka && !isset($statusAsprak))
                                        <div style="margin-top:12px;">
                                            <a href="{{ route('eoffice.manprak.mahasiswa.daftar-asprak.index') }}" class="mp-btn primary sm flex-shrink-0" style="text-decoration:none;">Ikuti Seleksi</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div style="padding:48px 20px;text-align:center;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                                    <circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/>
                                </svg>
                                <div style="font-size:13px;font-weight:500;color:#353849;">Tidak ada info pendaftaran.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        <div class="flex flex-col mb-1 items-start">
            
            {{-- Daftar Tugas (Seluruh Lebar) --}}
            <div class="mp-card w-full">
                <div class="mp-card-header" style="flex-shrink:0;">
                    <span class="mp-card-title">Daftar Tugas</span>
                </div>
                <div class="overflow-y-auto" style="max-height: 400px;">
                    @php
                        // Gabungkan tugas terlambat dan mendatang untuk grid ini (Maksimal 6)
                        $tugasGabungan = collect($tugasTerlambat ?? [])->merge($tugasMendatang ?? [])->take(6);
                    @endphp
                    @forelse($tugasGabungan as $t)
                        @php
                            $dl = \Carbon\Carbon::parse($t->deadline);
                            $sisa = now()->diffInDays($dl, false);
                            $isLate = now()->gt($dl);
                            $urgent = $sisa <= 2 && !$isLate;
                        @endphp
                        <div style="padding:16px 20px;border-bottom:1px solid #ECEFF3;display:flex;align-items:center;justify-content:space-between;gap:12px;">
                            <div class="min-w-0">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                                    <span style="font-size:11px;font-weight:600;color:#6366F1;text-transform:uppercase;">{{ $t->modul->praktikum->nama ?? '-' }} - {{ $t->modul->judul ?? '-' }}</span>
                                    @if($isLate)
                                        <span class="mp-badge error sm" style="font-size:10px;padding:2px 6px;">Terlambat</span>
                                    @endif
                                </div>
                                <div style="font-size:14px;font-weight:600;color:#0D0D12;" class="truncate">{{ $t->judul }}</div>
                                <div style="font-size:12px;margin-top:4px;color:{{ $isLate ? '#DF1C41' : ($urgent ? '#D39C3D' : '#666D80') }};font-weight:{{ ($isLate || $urgent) ? '600' : '400' }};">
                                    Berakhir: {{ $dl->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}
                                </div>
                            </div>
                            <a href="{{ route('eoffice.manprak.mahasiswa.tugas.index') }}" class="mp-btn {{ $isLate ? 'secondary' : 'primary' }} sm flex-shrink-0" style="text-decoration:none;">{{ $isLate ? 'Detail' : 'Kerjakan' }}</a>
                        </div>
                    @empty
                        <div style="padding:48px 20px;text-align:center;">
                            <div style="font-size:13px;color:#666D80;">Seluruh tugas telah selesai.</div>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    @endif {{-- end belumTerdaftar --}}

</x-eoffice::manajemen-praktikum.layout>