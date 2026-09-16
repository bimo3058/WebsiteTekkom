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

    {{-- ── UNIFIED DASHBOARD STATE ─────────────────────────────────────────── --}}

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



    <div class="flex flex-col lg:flex-row mb-4 items-stretch" style="gap: 15px;">
        
        {{-- Pengumuman Praktikum Terdaftar --}}
        <div class="mp-card flex-1 min-w-0 w-full lg:w-1/2" style="display:flex; flex-direction:column; height: 420px; overflow:hidden;">
            <div class="mp-card-header" style="flex-shrink:0;">
                <span class="mp-card-title">Pengumuman Praktikum</span>
            </div>
            <div class="overflow-y-auto" style="flex:1; min-height:0;">
                @forelse($pengumumanPraktikum ?? [] as $p)
                    <div style="padding:16px 20px;border-bottom:1px solid #ECEFF3;">
                        <div style="font-size:11px;font-weight:600;color:#1E3A8A;text-transform:uppercase;margin-bottom:6px;letter-spacing:0.04em;">
                            {{ $p->praktikum?->nama ?? 'Praktikum' }}
                        </div>
                        <div style="font-size:14px;font-weight:700;color:#0D0D12;margin-bottom:6px;line-height:1.4;">{{ $p->judul }}</div>
                        <div style="font-size:13px;color:#4B5563;line-height:1.6;" class="line-clamp-3">{{ $p->konten }}</div>
                        <div style="font-size:11px;color:#808897;margin-top:8px;">Dipublikasikan {{ $p->created_at->diffForHumans() }}
                            @if($p->user)
                                · oleh <span style="font-weight:600;color:#4B5563;">{{ $p->user->name }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="padding:48px 20px;text-align:center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0" />
                        </svg>
                        @if($belumTerdaftar)
                            <div style="font-size:14px;font-weight:500;color:#353849;">Anda belum terdaftar di praktikum manapun.</div>
                            <div style="font-size:12px;color:#808897;margin-top:4px;">Silakan hubungi Admin atau Koordinator untuk informasi lebih lanjut.</div>
                        @else
                            <div style="font-size:14px;font-weight:500;color:#353849;">Belum ada pengumuman</div>
                            <div style="font-size:12px;color:#808897;margin-top:4px;">Pembaruan praktikum akan muncul di sini.</div>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Daftar Tugas (Sekarang dikanan atas) --}}
        <div id="daftar-tugas" class="mp-card flex-1 min-w-0 w-full lg:w-1/2" style="display:flex; flex-direction:column; height: 420px; overflow:hidden;">
            <div class="mp-card-header flex items-center justify-between" style="flex-shrink:0;">
                <span class="mp-card-title">Daftar Tugas</span>
                
                <form id="tugas_filter_form" method="GET" action="{{ url()->current() }}#daftar-tugas" style="display:none;"></form>
            </div>
            <div class="overflow-y-auto" style="flex:1; min-height:0;">
                @forelse($tugasPaginator as $t)
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
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                            <path d="M9 11l3 3L22 4" />
                            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
                        </svg>
                        @if($belumTerdaftar)
                            <div style="font-size:14px;font-weight:500;color:#353849;">Anda belum terdaftar di praktikum manapun.</div>
                            <div style="font-size:12px;color:#808897;margin-top:4px;">Silakan hubungi Admin atau Koordinator untuk informasi lebih lanjut.</div>
                        @else
                            <div style="font-size:14px;font-weight:500;color:#353849;">Seluruh tugas telah selesai.</div>
                            <div style="font-size:12px;color:#808897;margin-top:4px;">Selamat! Anda tidak memiliki tugas yang mendesak.</div>
                        @endif
                    </div>
                @endforelse
            </div>

            {{-- Pagination Custom Fungsional --}}
            @if($tugasPaginator->hasPages() || $tugasPaginator->total() > 0)
            <div style="flex-shrink:0; display:flex; align-items:center; justify-content:flex-end; padding:12px 20px; border-top:1px solid var(--c-border); background:#fff;">
                <div style="display:flex; gap:8px;">
                    {{-- Tombol Prev --}}
                    @if ($tugasPaginator->onFirstPage())
                        <span style="width:36px; height:36px; border:1px solid #DFE1E7; background:#F9FAFB; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#A4ABB8;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                        </span>
                    @else
                        <a href="{{ $tugasPaginator->previousPageUrl() }}#daftar-tugas" style="width:36px; height:36px; border:1px solid #DFE1E7; background:#fff; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#353849; text-decoration:none;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                        </a>
                    @endif

                    {{-- Current Page --}}
                    <span style="width:36px; height:36px; border:none; background:#0B266E; color:#fff; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:15px; font-weight:600; box-shadow: 0 2px 4px rgba(11, 38, 110, 0.2);">
                        {{ $tugasPaginator->currentPage() }}
                    </span>
                    {{-- Tombol Next --}}
                    @if ($tugasPaginator->hasMorePages())
                        <a href="{{ $tugasPaginator->nextPageUrl() }}#daftar-tugas" style="width:36px; height:36px; border:1px solid #DFE1E7; background:#fff; border-radius:8px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#353849; text-decoration:none;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                    @else
                        <span style="width:36px; height:36px; border:1px solid #DFE1E7; background:#F9FAFB; border-radius:8px; display:flex; align-items:center; justify-content:center; color:#A4ABB8;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                        </span>
                    @endif
                </div>
            </div>
            @endif
        </div>

        </div>

        <div class="flex flex-col mb-1 items-start w-full" style="gap: 15px;">
                

                
                {{-- Tabel Info Rekrutmen --}}
                <div class="mp-card w-full" style="display:flex; flex-direction:column; max-height: 420px;">
                    <div class="mp-card-header" style="flex-shrink:0;background:#F9FAFB;border-bottom:1px solid #ECEFF3;">
                        <span class="mp-card-title">Pengumuman Pendaftaran Koordinator dan Asisten</span>
                    </div>
                    <div class="overflow-y-auto" style="flex:1; min-height:0;">
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

    <br/>

</x-eoffice::manajemen-praktikum.layout>