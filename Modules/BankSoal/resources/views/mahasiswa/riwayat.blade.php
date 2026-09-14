<x-banksoal::layouts.mahasiswa>
    @section('breadcrumbs')
        <span class="text-slate-900 font-semibold">Riwayat Ujian</span>
    @endsection

    {{-- Style Box Wrap khas SITKOM untuk Dashboard --}}
    <style>
        /* Hilangkan padding default agar wrap bisa full 100vh tanpa scroll ganda */
        .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }
        
        /* Override layout default (main area) untuk layout Mahasiswa */
        main.overflow-y-auto { overflow: hidden !important; }
        main.overflow-y-auto > div { padding: 0 !important; max-width: none !important; height: 100% !important; display: flex; flex-direction: column; }
        #banksoal-main-content { padding: 0 !important; max-width: 100% !important; height: 100% !important; display: flex; flex-direction: column; }

        /* Container luar */
        .dash-wrap {
            display: flex; flex-direction: column; height: 100%;
            padding: 16px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif;
        }

        /* Kotak utama (Box) */
        .dash-box {
            display: flex; flex-direction: column; flex: 1; min-height: 0;
            background: #fff; border: 1px solid var(--c-border);
            border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            overflow: hidden; width: 100%; box-sizing: border-box;
        }

        /* Area Header Box (Fixed di atas kotak) */
        .dash-box-header {
            background: #fff;
            border-bottom: 1px solid var(--c-border);
            flex-shrink: 0; width: 100%; box-sizing: border-box;
            padding: 16px 24px;
        }

        /* Area Konten Box (Scrollable) */
        .dash-box-body {
            flex: 1; overflow-y: auto; padding: 20px 24px;
            display: flex; flex-direction: column; gap: 24px;
        }

        .dash-box-body > * {
            flex-shrink: 0;
            width: 100%;
            min-width: 0;
        }

        /* Opsional: Percantik scrollbar */
        .dash-box-body::-webkit-scrollbar { width: 6px; }
        .dash-box-body::-webkit-scrollbar-thumb {
            background: var(--c-border-strong);
            border-radius: 10px;
        }

        /* ── Mobile ── */
        @media (max-width: 767px) {
            .sitkom-content {
                padding: 8px 8px 80px !important;
                display: block !important;
                overflow: visible !important;
            }
            .dash-wrap {
                height: auto !important;
                min-height: 0 !important;
                padding: 0;
            }
            .dash-box {
                border-radius: 10px;
                display: block;
                height: auto;
                overflow: visible;
            }
            .dash-box-header {
                padding: 12px 14px;
                position: sticky; top: 0; z-index: 20;
            }
            .dash-box-body {
                padding: 14px;
                overflow-y: visible;
                display: block;
            }
        }
    </style>

    <div class="dash-wrap">
        <div class="dash-box">
            
            {{-- Area Header (Diam) --}}
            <div class="dash-box-header">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-[22px] font-bold text-gray-900 tracking-tight">Riwayat Ujian</h1>
                        <p class="text-[13px] text-gray-500 mt-0.5">Rekap seluruh ujian komprehensif yang pernah Anda ikuti.</p>
                    </div>
                </div>
            </div>

            {{-- Area Konten (Bisa di-scroll) --}}
            <div class="dash-box-body">
                {{-- Statistik Ringkasan --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-5 mb-8">
        {{-- Total Ujian --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col justify-center">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Total Ujian</p>
            <div class="flex items-baseline gap-2">
                <p class="text-4xl font-bold tracking-tight text-gray-900">{{ $totalUjian }}</p>
            </div>
        </div>

        {{-- Nilai Tertinggi --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col justify-center">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Tertinggi</p>
            <div class="flex items-baseline gap-2">
                <p class="text-4xl font-bold tracking-tight text-gray-900">
                    {{ $nilaiTertinggi > 0 ? (int)$nilaiTertinggi : '—' }}
                </p>
                @if($nilaiTertinggi > 0)
                <p class="text-sm text-gray-400 font-medium">/ 100</p>
                @endif
            </div>
        </div>

        {{-- Rata-rata --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col justify-center col-span-2 md:col-span-1">
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Rata-rata</p>
            <div class="flex items-baseline gap-2">
                <p class="text-4xl font-bold tracking-tight text-gray-900">
                    {{ $nilaiRataRata > 0 ? $nilaiRataRata : '—' }}
                </p>
                @if($nilaiRataRata > 0)
                @endif
            </div>
        </div>
    </div>

    {{-- Tabel Riwayat --}}
    <div style="background:#fff; border:1px solid var(--c-border); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); display:flex; flex-direction:column;">
        
        {{-- Table Toolbar --}}
        <div style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid var(--c-border); gap:10px; flex-wrap:wrap;">
            <h2 style="font-size:14px; font-weight:700; color:var(--c-fg); margin:0; flex-shrink:0;">Daftar Ujian</h2>

            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; min-width:0;">
                <form method="GET" action="{{ route('komprehensif.mahasiswa.riwayat') }}" id="searchForm" style="display:flex; align-items:center; gap:8px; margin:0;">
                    <input type="hidden" name="sort_by"  value="{{ request('sort_by', 'finished_at') }}">
                    <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'desc') }}">
                    <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">

                    {{-- Search --}}
                    <div style="position:relative; width:min(220px, calc(100vw - 200px)); min-width:120px;">
                        <svg style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--c-fg-placeholder); pointer-events:none;" width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari periode ujian..." style="width:100%; height:34px; padding:0 12px 0 34px; border:1px solid var(--c-border); border-radius:8px; font-size:12.5px; color:var(--c-fg); font-family:inherit; outline:none; transition:all .15s; box-sizing:border-box; background:#fff;" onfocus="this.style.borderColor='var(--c-primary)'; this.style.boxShadow='0 0 0 3px rgba(94,83,244,0.08)'" onblur="this.style.borderColor='var(--c-border)'; this.style.boxShadow='none'">
                    </div>

                    {{-- SORT BY DROPDOWN --}}
                    @php
                        $currentSortBy  = request('sort_by', 'finished_at');
                        $currentSortDir = request('sort_dir', 'desc');
                        $activeSortLabel = match($currentSortBy) {
                            'score'       => $currentSortDir === 'asc' ? 'Skor Terendah' : 'Skor Tertinggi',
                            'finished_at' => $currentSortDir === 'asc' ? 'Terlama' : 'Terbaru',
                            default      => 'Terbaru',
                        };
                    @endphp
                    <div style="position:relative; display:inline-block;" x-data="{ sortOpen: false }">
                        <button type="button" @click="sortOpen = !sortOpen" class="flex flex-row items-center justify-center gap-1.5 h-[34px] px-3.5 bg-white border rounded-lg text-[12.5px] font-semibold whitespace-nowrap cursor-pointer transition-all box-border" style="border-color:var(--c-border); color:var(--c-fg-sec); font-family:inherit;" :style="sortOpen ? 'border-color:var(--c-primary); color:var(--c-primary);' : ''">
                            @if($currentSortDir === 'asc')
                            <svg class="shrink-0 w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h12M3 18h6"/><path d="M19 9V3m0 0l-2 2m2-2l2 2"/></svg>
                            @else
                            <svg class="shrink-0 w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h12M3 18h6"/><path d="M19 15v6m0 0l-2-2m2 2l2-2"/></svg>
                            @endif
                            <span class="leading-none">Sort: {{ $activeSortLabel }}</span>
                            @if($currentSortBy !== 'finished_at' || $currentSortDir !== 'desc')
                                <span class="shrink-0 w-1.5 h-1.5 rounded-full bg-[var(--c-primary)] ml-0.5"></span>
                            @endif
                        </button>

                        <div x-show="sortOpen" x-cloak @click="sortOpen = false" style="position:fixed; inset:0; z-index:48; display:none; background:transparent;"></div>

                        <div x-show="sortOpen" x-cloak
                             x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                             style="position:absolute; right:0; top:calc(100% + 6px); z-index:49; min-width:200px; background:#fff; border:1px solid var(--c-border); border-radius:12px; box-shadow:0 8px 24px rgba(0,0,0,.1); padding:8px; display:none;">

                            {{-- Waktu --}}
                            <p style="font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-placeholder); margin:0 0 5px 6px;">Pelaksanaan</p>
                            @php $regNew = $currentSortBy === 'finished_at' && $currentSortDir === 'desc'; @endphp
                            @php $regOld = $currentSortBy === 'finished_at' && $currentSortDir === 'asc'; @endphp
                            <button type="button" onclick="setSortRiwayat('finished_at', 'desc')" style="width:100%; display:flex; align-items:center; justify-content:space-between; gap:8px; padding:7px 10px; border:none; border-radius:7px; background:{{ $regNew ? 'rgba(11,38,110,0.06)' : 'transparent' }}; font-size:12px; font-weight:{{ $regNew ? '700' : '500' }}; color:{{ $regNew ? 'var(--c-primary)' : 'var(--c-fg-sec)' }}; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;" onmouseover="if(!{{ $regNew ? 'true' : 'false' }}) this.style.background='var(--c-bg)'" onmouseout="if(!{{ $regNew ? 'true' : 'false' }}) this.style.background='transparent'">
                                <div style="display:flex; align-items:center; gap:7px;">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M19 15v6m0 0l-2-2m2 2l2-2"/><path d="M3 6h18M3 12h12M3 18h6"/></svg>
                                    <span>Terbaru</span>
                                </div>
                                @if($regNew)<svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>@endif
                            </button>
                            <button type="button" onclick="setSortRiwayat('finished_at', 'asc')" style="width:100%; display:flex; align-items:center; justify-content:space-between; gap:8px; padding:7px 10px; border:none; border-radius:7px; background:{{ $regOld ? 'rgba(11,38,110,0.06)' : 'transparent' }}; font-size:12px; font-weight:{{ $regOld ? '700' : '500' }}; color:{{ $regOld ? 'var(--c-primary)' : 'var(--c-fg-sec)' }}; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;" onmouseover="if(!{{ $regOld ? 'true' : 'false' }}) this.style.background='var(--c-bg)'" onmouseout="if(!{{ $regOld ? 'true' : 'false' }}) this.style.background='transparent'">
                                <div style="display:flex; align-items:center; gap:7px;">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M19 9V3m0 0l-2 2m2-2l2 2"/><path d="M3 6h18M3 12h12M3 18h6"/></svg>
                                    <span>Terlama</span>
                                </div>
                                @if($regOld)<svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>@endif
                            </button>

                            <div style="height:1px; background:var(--c-border); margin:7px 0;"></div>

                            {{-- Skor --}}
                            <p style="font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--c-fg-placeholder); margin:0 0 5px 6px;">Skor</p>
                            @php $skorTinggi = $currentSortBy === 'score' && $currentSortDir === 'desc'; @endphp
                            @php $skorRendah = $currentSortBy === 'score' && $currentSortDir === 'asc'; @endphp
                            <button type="button" onclick="setSortRiwayat('score', 'desc')" style="width:100%; display:flex; align-items:center; justify-content:space-between; gap:8px; padding:7px 10px; border:none; border-radius:7px; background:{{ $skorTinggi ? 'rgba(11,38,110,0.06)' : 'transparent' }}; font-size:12px; font-weight:{{ $skorTinggi ? '700' : '500' }}; color:{{ $skorTinggi ? 'var(--c-primary)' : 'var(--c-fg-sec)' }}; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;" onmouseover="if(!{{ $skorTinggi ? 'true' : 'false' }}) this.style.background='var(--c-bg)'" onmouseout="if(!{{ $skorTinggi ? 'true' : 'false' }}) this.style.background='transparent'">
                                <div style="display:flex; align-items:center; gap:7px;">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M19 15v6m0 0l-2-2m2 2l2-2"/><path d="M3 6h18M3 12h12M3 18h6"/></svg>
                                    <span>Poin Tertinggi</span>
                                </div>
                                @if($skorTinggi)<svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>@endif
                            </button>
                            <button type="button" onclick="setSortRiwayat('score', 'asc')" style="width:100%; display:flex; align-items:center; justify-content:space-between; gap:8px; padding:7px 10px; border:none; border-radius:7px; background:{{ $skorRendah ? 'rgba(11,38,110,0.06)' : 'transparent' }}; font-size:12px; font-weight:{{ $skorRendah ? '700' : '500' }}; color:{{ $skorRendah ? 'var(--c-primary)' : 'var(--c-fg-sec)' }}; cursor:pointer; font-family:inherit; text-align:left; transition:background .12s;" onmouseover="if(!{{ $skorRendah ? 'true' : 'false' }}) this.style.background='var(--c-bg)'" onmouseout="if(!{{ $skorRendah ? 'true' : 'false' }}) this.style.background='transparent'">
                                <div style="display:flex; align-items:center; gap:7px;">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><path d="M19 9V3m0 0l-2 2m2-2l2 2"/><path d="M3 6h18M3 12h12M3 18h6"/></svg>
                                    <span>Poin Terendah</span>
                                </div>
                                @if($skorRendah)<svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M20 6L9 17l-5-5"/></svg>@endif
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table Content --}}
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:780px;">
                <thead>
                    <tr style="border-bottom:1px solid var(--c-border); background:#FAFAFA;">
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; width:48px;">No</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; min-width:200px;">Periode Ujian</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Tanggal & Waktu</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Durasi</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Poin</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap;">Status</th>
                        <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:var(--c-fg-muted); white-space:nowrap; text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $index => $session)
                        @php
                            $rowNo = ($sessions->currentPage() - 1) * $sessions->perPage() + $index + 1;
                            
                            $score       = (int) ($session->score ?? 0);
                            $isNoShow    = $session->title === 'Tidak Mengerjakan';
                            $durasi      = null;

                            if ($isNoShow) {
                                $durasi = '0 Menit';
                            } elseif ($session->started_at && $session->finished_at) {
                                $diffInMinutes = $session->started_at->diffInMinutes($session->finished_at);
                                $jam = floor($diffInMinutes / 60);
                                $menit = $diffInMinutes % 60;
                                
                                if ($jam > 0 && $menit > 0) {
                                    $durasi = "{$jam} Jam {$menit} Menit";
                                } elseif ($jam > 0) {
                                    $durasi = "{$jam} Jam";
                                } else {
                                    $durasi = "{$menit} Menit";
                                }
                            }

                            $lulus = $score >= 60;
                            // Meniru status badge superadmin dengan CSS inline
                            $badgeColor = $isNoShow ? '#475569' : ($lulus ? '#059669' : '#DC2626');
                            $badgeBg = $isNoShow ? '#F1F5F9' : ($lulus ? '#ECFDF5' : '#FEF2F2');
                            $badgeLabel = $isNoShow ? 'Tidak Hadir' : ($lulus ? 'Lulus' : 'Tidak Lulus');
                        @endphp
                        <tr style="border-bottom:1px solid #F3F4F6; transition:background .12s;" onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                            {{-- No --}}
                            <td style="padding:14px 16px; font-size:13px; font-weight:400; color:var(--c-fg-muted);">
                                {{ $rowNo }}
                            </td>

                            {{-- Periode --}}
                            <td style="padding:14px 16px; min-width:200px;">
                                <div style="display:flex; align-items:center; gap:5px;">
                                    <p style="font-size:13px; font-weight:600; color:var(--c-fg); margin:0;">
                                        {{ $session->jadwal->periode->nama_periode ?? 'Ujian Komprehensif' }}
                                    </p>
                                    @if($session->jadwal)
                                        <span style="font-size:11px; font-weight:500; color:var(--c-fg-muted); background:var(--c-bg); padding:1px 6px; border-radius:4px;">Sesi {{ $session->jadwal->nama_sesi ?? '-' }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Tanggal --}}
                            <td style="padding:14px 16px;">
                                @if($session->finished_at)
                                    <p style="font-size:13px; font-weight:500; color:var(--c-fg); margin:0;">
                                        {{ \Carbon\Carbon::parse($session->finished_at)->translatedFormat('d F Y') }}
                                    </p>
                                    <p style="font-size:11px; color:var(--c-fg-muted); margin:2px 0 0 0;">
                                        {{ \Carbon\Carbon::parse($session->started_at)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->finished_at)->format('H:i') }}
                                    </p>
                                @else
                                    <span style="font-size:13px; color:var(--c-fg-muted);">—</span>
                                @endif
                            </td>

                            {{-- Durasi --}}
                            <td style="padding:14px 16px;">
                                <span style="font-size:13px; font-weight:400; color:var(--c-fg);">
                                    {{ $durasi ?? '—' }}
                                </span>
                            </td>

                            {{-- Skor --}}
                            <td style="padding:14px 16px;">
                                <div style="display:flex; align-items:baseline; gap:2px;">
                                    <span style="font-size:14px; font-weight:700; color:#0D0D12;">
                                        {{ $score }}
                                    </span>
                                    <span style="font-size:11px; font-weight:500; color:var(--c-fg-muted);">/100</span>
                                </div>
                            </td>

                            {{-- Status Badge --}}
                            <td style="padding:12px 16px;">
                                <span style="display:inline-flex; align-items:center; gap:5px; padding:3px 8px 3px 6px; border-radius:9999px; font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:0.04em; background:{{ $badgeBg }}; color:{{ $badgeColor }};">
                                    <span style="width:6px; height:6px; border-radius:50%; background:{{ $badgeColor }};"></span>
                                    {{ $badgeLabel }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td style="padding:12px 16px; text-align:center;">
                                <a href="{{ route('komprehensif.mahasiswa.riwayat.detail', $session->id) }}" class="inline-flex items-center justify-center bg-white border border-gray-200 text-gray-600 hover:text-primary hover:border-primary px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors shadow-sm whitespace-nowrap">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:60px 20px; text-align:center;">
                                <div style="display:flex; flex-direction:column; items-center:center; justify-content:center;">
                                    <div style="width:56px; height:56px; background:#F9FAFB; display:flex; align-items:center; justify-content:center; border-radius:16px; margin:0 auto 16px; border:1px solid #F3F4F6;">
                                        <svg style="width:28px; height:28px; color:var(--c-fg-muted);" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 style="font-size:14px; font-weight:600; color:var(--c-fg); margin:0 0 4px;">Belum Ada Riwayat Ujian</h3>
                                    <p style="font-size:13px; color:var(--c-fg-muted); margin:0;">
                                        Belum ada ujian komprehensif yang sesuai kriteria.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Table Footer (Pagination & Per Page) --}}
        <div style="padding:14px 16px; border-top:1px solid var(--c-border); background:#fff; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="font-size:12px; color:var(--c-fg-muted); font-weight:500;">Per page</span>
                    <select onchange="window.location.href = '{{ request()->fullUrlWithQuery(['page' => 1]) }}&per_page=' + this.value;"
                        style="height:30px; padding:0 24px 0 10px; border:1px solid var(--c-border); border-radius:6px; font-size:12px; color:var(--c-fg); font-family:inherit; outline:none; cursor:pointer; background:#fff;">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                @if ($sessions instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <span style="font-size:12px; color:var(--c-fg-muted);">
                    Showing <span style="font-weight:600; color:var(--c-fg);">{{ $sessions->firstItem() ?? 0 }}</span> to <span style="font-weight:600; color:var(--c-fg);">{{ $sessions->lastItem() ?? 0 }}</span> of <span style="font-weight:600; color:var(--c-fg);">{{ $sessions->total() }}</span> results
                </span>
                @endif
            </div>

            @if ($sessions instanceof \Illuminate\Pagination\LengthAwarePaginator && $sessions->hasPages())
            <div class="sm:ml-auto">
                {{ $sessions->links('pagination::tailwind') }}
            </div>
            @endif
        </div>
    </div>
    
            </div> {{-- End dash-box-body --}}
        </div> {{-- End dash-box --}}
    </div> {{-- End dash-wrap --}}

    <script>
        function setSortRiwayat(col, dir) {
            const form = document.getElementById('searchForm');
            form.querySelector('input[name="sort_by"]').value  = col;
            form.querySelector('input[name="sort_dir"]').value = dir;
            const pageInput = form.querySelector('input[name="page"]');
            if (pageInput) pageInput.remove();
            form.submit();
        }
    </script>
</x-banksoal::layouts.mahasiswa>
