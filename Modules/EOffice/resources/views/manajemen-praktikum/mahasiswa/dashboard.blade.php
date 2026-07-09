<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard Mahasiswa — Manajemen Praktikum">
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
        <div class="mp-page-header">
            <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                    <h1 class="mp-page-title">Halo, {{ $firstName }}!</h1>
                    <span class="mp-badge warning sm"><span class="dot"></span>Mahasiswa</span>
                </div>
                <p class="mp-page-sub">Selamat datang, {{ $firstName }} ·
            {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }} · {{ $semesterLabel }}
                    @if(isset($terdaftarDi) && $terdaftarDi) · {{ $terdaftarDi->nama }} @endif
                </p>
            </div>
            <div class="mp-page-actions">
                <!-- Aksi dinonaktifkan karena pendaftaran di-handle oleh Admin -->
            </div>
        </div>

        {{-- Section: Ringkasan Kehadiran --}}
        <div class="sec-head">
            <span class="sec-bar"></span>
            <span class="sec-title">Ringkasan</span>
            <span class="sec-rule"></span>
            @if($pct < 75 && ($absensiStat['total'] ?? 0) > 0)
                <span class="mp-badge error sm"><span class="dot"></span>Kehadiran di bawah 75%</span>
            @endif
        </div>

        {{-- Stat Cards --}}
        <div class="mp-stats-grid cols-4">
            <div class="mp-stat">
                <div class="mp-stat-icon {{ $pct >= 75 ? 'green' : 'red' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="mp-stat-label">Kehadiran</div>
                <div class="mp-stat-value" style="color:{{ $pct >= 75 ? '#40C4AA' : '#DF1C41' }};">{{ $pct }}%</div>
                <div class="mp-stat-sub">{{ $absensiStat['hadir'] ?? 0 }} dari {{ $absensiStat['total'] ?? 0 }} sesi</div>
            </div>
            <div class="mp-stat">
                <div class="mp-stat-icon yellow">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                </div>
                <div class="mp-stat-label">Tugas Pending</div>
                <div class="mp-stat-value">{{ count($tugasMendatang ?? []) }}</div>
                <div class="mp-stat-sub">belum dikumpulkan</div>
            </div>
            <div class="mp-stat">
                <div class="mp-stat-icon sky">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 016.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z" />
                    </svg>
                </div>
                <div class="mp-stat-label">Modul Tersedia</div>
                <div class="mp-stat-value">{{ count($nilaiList ?? []) }}</div>
                <div class="mp-stat-sub">modul praktikum</div>
            </div>
            <div class="mp-stat">
                <div class="mp-stat-icon navy">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" />
                    </svg>
                </div>
                <div class="mp-stat-label">Pengumuman</div>
                <div class="mp-stat-value">{{ count($pengumuman ?? []) }}</div>
                <div class="mp-stat-sub">aktif</div>
            </div>
        </div>

        {{-- Info Praktikum Aktif + Progress Kehadiran --}}
        @if(isset($terdaftarDi) && $terdaftarDi)
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;flex-shrink:0;">

                {{-- Info Praktikum --}}
                <div class="mp-card" style="padding:20px;">
                    <div
                        style="font-size:11px;font-weight:600;color:#666D80;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">
                        Praktikum Aktif</div>
                    <div style="font-size:18px;font-weight:700;color:#0D0D12;margin-bottom:10px;line-height:1.2;">
                        {{ $terdaftarDi->nama }}</div>
                    <div class="flex flex-col gap-2">

                        <div style="font-size:12px;color:#666D80;display:flex;align-items:center;gap:6px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="2"
                                stroke-linecap="round">
                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8" />
                            </svg>
                            Dosen: <span style="font-weight:600;color:#353849;">{{ $terdaftarDi->dosen?->name ?? '—' }}</span>
                        </div>
                        @if(isset($terdaftarDi->koordinator) && $terdaftarDi->koordinator)
                            <div style="font-size:12px;color:#666D80;display:flex;align-items:center;gap:6px;">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="2"
                                    stroke-linecap="round">
                                    <path
                                        d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8M22 11l-3.5 3.5-1.5-1.5" />
                                </svg>
                                Koordinator: <span
                                    style="font-weight:600;color:#353849;">{{ $terdaftarDi->koordinator->name }}</span>
                            </div>
                        @endif
                    </div>
                    <div style="margin-top:14px;">
                        <span class="mp-badge success sm"><span class="dot"></span>Aktif</span>
                    </div>
                </div>

                {{-- Progress Kehadiran --}}
                <div class="mp-card" style="padding:20px;">
                    <div
                        style="font-size:11px;font-weight:600;color:#666D80;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px;">
                        Statistik Kehadiran</div>
                    <div class="flex items-end justify-between mb-3">
                        <div style="font-size:13px;color:#666D80;">{{ $absensiStat['hadir'] ?? 0 }} hadir dari
                            {{ $absensiStat['total'] ?? 0 }} sesi</div>
                        <div
                            style="font-size:32px;font-weight:700;color:{{ $pct >= 75 ? '#40C4AA' : '#DF1C41' }};line-height:1;letter-spacing:-.02em;">
                            {{ $pct }}%</div>
                    </div>
                    <div style="width:100%;background:#ECEFF3;border-radius:999px;height:8px;overflow:hidden;">
                        <div
                            style="height:8px;border-radius:999px;width:{{ $pct }}%;background:{{ $pct >= 75 ? 'linear-gradient(90deg,#40C4AA,#0D6B55)' : 'linear-gradient(90deg,#DF1C41,#f87171)' }};transition:width .4s ease;">
                        </div>
                    </div>
                    @if($pct < 75 && ($absensiStat['total'] ?? 0) > 0)
                        <div style="margin-top:10px;display:flex;align-items:center;gap:6px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#DF1C41" stroke-width="2"
                                stroke-linecap="round">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="8" x2="12" y2="12" />
                                <line x1="12" y1="16" x2="12.01" y2="16" />
                            </svg>
                            <span style="font-size:11px;color:#DF1C41;font-weight:500;">Kehadiran di bawah 75% — risiko tidak lulus
                                praktikum</span>
                        </div>
                    @elseif(($absensiStat['total'] ?? 0) > 0)
                        <div style="margin-top:10px;display:flex;align-items:center;gap:6px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#287F6E" stroke-width="2"
                                stroke-linecap="round">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                            <span style="font-size:11px;color:#287F6E;font-weight:500;">Kehadiran aman — pertahankan!</span>
                        </div>
                    @endif
                </div>

            </div>
        @endif

        {{-- Section: Tugas, Nilai & Pengumuman --}}
        <div class="sec-head">
            <span class="sec-bar"></span>
            <span class="sec-title">Aktivitas Praktikum</span>
            <span class="sec-rule"></span>
        </div>

        {{-- Bottom Grid: Tugas + Nilai + Pengumuman --}}
        <div class="flex gap-[14px] flex-1 min-h-0 mb-1">

            {{-- Tugas Mendatang --}}
            <div class="mp-card flex-1 min-w-0">
                <div class="mp-card-header" style="flex-shrink:0;">
                    <span class="mp-card-title">Tugas Mendatang</span>
                    @if(count($tugasMendatang ?? []) > 0)
                        <span class="mp-badge warning sm"><span class="dot"></span>{{ count($tugasMendatang) }}</span>
                    @endif
                    <div class="right">
                        <a href="{{ route('eoffice.manprak.mahasiswa.tugas.index') }}" class="mp-btn secondary sm"
                            style="text-decoration:none;">Lihat Semua →</a>
                    </div>
                </div>
                <div class="overflow-y-auto flex-1">
                    @forelse($tugasMendatang ?? [] as $t)
                        @php
                            $dl = \Carbon\Carbon::parse($t['deadline']);
                            $sisa = now()->diffInDays($dl, false);
                            $sudah = $t['sudah_kumpul'];
                            $urgent = $sisa <= 2 && !$sudah;
                        @endphp
                        <div style="padding:12px 20px;border-bottom:1px solid #ECEFF3;">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <div style="font-size:13px;font-weight:600;color:#0D0D12;" class="truncate">
                                        {{ $t['judul'] }}</div>
                                    <div
                                        style="font-size:11px;margin-top:3px;color:{{ $urgent ? '#DF1C41' : '#666D80' }};font-weight:{{ $urgent ? '600' : '400' }};">
                                        Deadline: {{ $dl->locale('id')->isoFormat('D MMM YYYY, HH:mm') }}
                                    </div>
                                </div>
                                @if($sudah)
                                    <span class="mp-badge success sm flex-shrink-0"><span class="dot"></span>Dikumpul</span>
                                @else
                                    <a href="{{ route('eoffice.manprak.mahasiswa.tugas.index') }}"
                                        class="mp-badge warning sm flex-shrink-0" style="text-decoration:none;">Kumpul</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div style="padding:40px;text-align:center;">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                                stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                            <div style="font-size:13px;font-weight:500;color:#666D80;">Tidak ada tugas mendatang.</div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Nilai Praktikum --}}
            <div class="mp-card flex-1 min-w-0">
                <div class="mp-card-header" style="flex-shrink:0;">
                    <span class="mp-card-title">Nilai Praktikum</span>
                    <div class="right">
                        <a href="{{ route('eoffice.manprak.mahasiswa.nilai.index') }}" class="mp-btn secondary sm"
                            style="text-decoration:none;">Detail →</a>
                    </div>
                </div>
                <div class="overflow-y-auto flex-1">
                    @forelse($nilaiList ?? [] as $n)
                        @php
                            $nilai = $n->nilai_akhir ?? null;
                            $nilaiColor = !$nilai ? '#808897' : ($nilai >= 75 ? '#40C4AA' : ($nilai >= 50 ? '#D39C3D' : '#DF1C41'));
                        @endphp
                        <div class="mp-tr flex items-center justify-between"
                            style="padding:12px 20px;border-bottom:1px solid #ECEFF3;">
                            <div style="font-size:13px;font-weight:500;color:#0D0D12;">{{ $n->modul ?? 'Modul' }}</div>
                            <span
                                style="font-size:18px;font-weight:700;color:{{ $nilaiColor }};font-family:'Inter Tight',sans-serif;letter-spacing:-.01em;">{{ $nilai ?? '—' }}</span>
                        </div>
                    @empty
                        <div style="padding:40px;text-align:center;">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                                stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8zM14 2v6h6M9 15l2 2 4-4" />
                            </svg>
                            <div style="font-size:13px;font-weight:500;color:#666D80;">Nilai belum tersedia.</div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Pengumuman --}}
            <div class="mp-card flex-1 min-w-0">
                <div class="mp-card-header" style="flex-shrink:0;">
                    <span class="mp-card-title">Pengumuman</span>
                </div>
                <div class="overflow-y-auto flex-1">
                    @forelse($pengumuman ?? [] as $p)
                        @if($p->tipe_sistem === 'buka')
                            <a href="{{ route('eoffice.manprak.mahasiswa.daftar-asprak.index') }}?praktikum_id={{ $p->praktikum_id }}"
                                style="text-decoration:none;display:block;padding:12px 20px;border-bottom:1px solid #ECEFF3;transition:background 0.2s;"
                                onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='transparent'">
                                <div style="font-size:13px;font-weight:600;color:#0B266E;margin-bottom:4px;">{{ $p->judul }} <span
                                        style="font-size:10px;margin-left:4px;" class="mp-badge success sm">Buka</span></div>
                                <div style="font-size:12px;color:#666D80;line-height:1.5;" class="line-clamp-2">{{ $p->konten }}
                                </div>
                                <div style="font-size:11px;color:#808897;margin-top:6px;">{{ $p->created_at->diffForHumans() }}
                                </div>
                            </a>
                        @else
                            <div style="padding:12px 20px;border-bottom:1px solid #ECEFF3;">
                                <div style="font-size:13px;font-weight:600;color:#0D0D12;margin-bottom:4px;">{{ $p->judul }}</div>
                                <div style="font-size:12px;color:#666D80;line-height:1.5;" class="line-clamp-2">{{ $p->konten }}
                                </div>
                                <div style="font-size:11px;color:#808897;margin-top:6px;">{{ $p->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @endif
                    @empty
                        <div style="padding:40px;text-align:center;">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                                stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                                <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" />
                            </svg>
                            <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada pengumuman.</div>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Status / CTA Daftar Asisten Praktikum --}}
        <div class="sec-head">
            <span class="sec-bar"></span>
            <span class="sec-title">Asisten Praktikum</span>
            <span class="sec-rule"></span>
        </div>

        @if(isset($statusAsprak) && $statusAsprak)
            <div class="mp-card flex-shrink-0"
                style="padding:18px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;">
                <div class="min-w-0">
                    <div style="font-size:13px;font-weight:700;color:#0D0D12;margin-bottom:3px;">Status Pendaftaran Asisten
                        Praktikum</div>
                    <div style="font-size:12px;color:#666D80;">{{ $statusAsprak->praktikum?->nama ?? '—' }}</div>
                </div>
                @if($statusAsprak->status === 'pending')
                    <span class="mp-badge warning sm flex-shrink-0"><span class="dot"></span>Menunggu Review</span>
                @elseif($statusAsprak->status === 'approved')
                    <span class="mp-badge success sm flex-shrink-0"><span class="dot"></span>Diterima</span>
                @else
                    <span class="mp-badge error sm flex-shrink-0"><span class="dot"></span>Ditolak</span>
                @endif
            </div>
        @else
            <div class="mp-card flex-shrink-0"
                style="padding:18px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;">
                <div class="min-w-0">
                    <div style="font-size:13px;font-weight:700;color:#0D0D12;margin-bottom:3px;">Tertarik jadi Asisten
                        Praktikum?</div>
                    <div style="font-size:12px;color:#666D80;">Daftarkan diri sebagai calon asisten praktikum {{ $semesterLabel }}.</div>
                </div>
                <a href="{{ route('eoffice.manprak.mahasiswa.daftar-asprak.index') }}" class="mp-btn primary md flex-shrink-0"
                    style="text-decoration:none;">Daftar Sekarang</a>
            </div>
        @endif

    @endif {{-- end belumTerdaftar --}}

</x-eoffice::manajemen-praktikum.layout>