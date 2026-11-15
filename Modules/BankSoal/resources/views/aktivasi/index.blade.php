<x-banksoal::layouts.admin>
    @section('breadcrumbs')
        <a href="#" class="hover:text-primary transition-colors text-gray-500">Ujian Komprehensif</a>
        <span class="mx-2 text-gray-300">/</span>
        <span class="text-gray-900 font-semibold">Monitoring Ujian</span>
    @endsection

    @push('scripts')
        <script>
            (() => {
                const init = () => {
                    const root = document.querySelector('[data-violation-status-url]');
                    if (!root) return;

                    const statusUrl = root.dataset.violationStatusUrl;
                    let latestId = Number(root.dataset.latestViolationId || 0);
                    let isPolling = false;
                    let isReloading = false;

                    const poll = async () => {
                        if (isPolling || isReloading || document.hidden) return;

                        isPolling = true;
                        try {
                            const response = await fetch(statusUrl, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                                credentials: 'same-origin',
                                cache: 'no-store',
                            });

                            if (!response.ok) return;

                            const data = await response.json();
                            if (Number(data.latest_id || 0) > latestId) {
                                isReloading = true;
                                window.location.reload();
                            }
                        } catch (error) {
                        } finally {
                            isPolling = false;
                        }
                    };

                    setInterval(poll, 5000);
                    document.addEventListener('visibilitychange', () => {
                        if (!document.hidden) poll();
                    });
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', init);
                } else {
                    init();
                }
            })();
        </script>
    @endpush

    {{-- Style Box Wrap khas SITKOM (Mengadopsi Manajemen Jadwal & Sesi) --}}
    <style>
        .sitkom-content { padding: 0 !important; display: flex; flex-direction: column; flex: 1; overflow: hidden; }

        main.overflow-y-auto { overflow: hidden !important; }
        #banksoal-main-content { padding: 0 !important; max-width: 100% !important; height: 100% !important; display: flex; flex-direction: column; }

        .dash-wrap {
            display: flex; flex-direction: column; height: 100%;
            padding: 16px; box-sizing: border-box; font-family: 'Inter Tight', sans-serif;
        }

        .dash-box {
            display: flex; flex-direction: column; flex: 1; min-height: 0;
            background: #fff; border: 1px solid var(--c-border);
            border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            overflow: hidden; width: 100%; box-sizing: border-box;
        }

        .dash-box-header {
            background: #fff;
            border-bottom: 1px solid var(--c-border);
            flex-shrink: 0; width: 100%; box-sizing: border-box;
            padding: 24px 30px;
        }

        .dash-box-body {
            flex: 1; overflow-y: auto; padding: 32px 30px 40px;
            display: flex; flex-direction: column; gap: 32px;
        }

        .dash-box-body > * {
            flex-shrink: 0;
            width: 100%;
            min-width: 0;
        }

        .dash-box-body::-webkit-scrollbar { width: 6px; }
        .dash-box-body::-webkit-scrollbar-thumb {
            background: var(--c-border-strong);
            border-radius: 10px;
        }

        .mon-content {
            width: 100%;
            min-width: 0;
        }

        .section-title {
            margin: 0 0 8px;
            font-size: 16px;
            line-height: 1.35;
            font-weight: 700;
            color: var(--c-fg);
        }

        /* Tabel Sesi & Token: full-width, tanpa kartu */
        .sesi-table {
            width: 100%;
            min-width: 720px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .sesi-table th {
            padding: 8px 0;
            border-bottom: 1px solid var(--c-border);
            font-size: 12px;
            font-weight: 500;
            color: var(--c-fg-muted);
            text-align: left;
            white-space: nowrap;
        }

        .sesi-table td {
            padding: 16px 0;
            border-bottom: 1px solid #ECEFF3;
            vertical-align: middle;
            font-size: 13px;
            color: var(--c-fg);
        }

        .sesi-date td {
            padding: 8px 0;
            border-bottom: 1px solid #ECEFF3;
            font-size: 12px;
            font-weight: 500;
            color: var(--c-fg-muted);
        }

        .sesi-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--c-fg);
        }

        .sesi-time {
            font-size: 14px;
            font-weight: 500;
            color: #1A1B25;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .sesi-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 500;
            color: var(--c-fg-muted);
            white-space: nowrap;
        }

        .sesi-status.is-live { color: #2A3A7C; }

        .sesi-dot {
            width: 6px;
            height: 6px;
            box-sizing: border-box;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .sesi-dot.is-done { background: #A4ABB8; }
        .sesi-dot.is-live { background: #2A3A7C; }
        .sesi-dot.is-waiting { background: transparent; border: 1px solid #A4ABB8; }

        .sesi-count {
            font-size: 13px;
            font-weight: 500;
            color: #353849;
            font-variant-numeric: tabular-nums;
            white-space: nowrap;
        }

        .sesi-token {
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .04em;
            color: #2A3A7C;
        }

        .sesi-copy {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border: 0;
            padding: 0;
            margin-left: 8px;
            background: transparent;
            border-radius: 6px;
            color: #2A3A7C;
            cursor: pointer;
        }

        .sesi-copy:hover { background: #F6F8FA; }

        .sesi-copy svg {
            width: 14px;
            height: 14px;
        }

        .sesi-copy:focus-visible,
        .sesi-activate:focus-visible,
        .live-end:focus-visible {
            outline: 2px solid #2A3A7C;
            outline-offset: 2px;
        }

        .sesi-activate {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 30px;
            padding: 0 12px;
            background: #fff;
            border: 1px solid var(--c-border);
            border-radius: 8px;
            color: #2A3A7C;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            transition: background .15s, border-color .15s;
            cursor: pointer;
        }

        .sesi-activate:hover {
            background: #F6F8FA;
            border-color: #AAB4D5;
        }

        .sesi-token-empty {
            color: #A4ABB8;
            font-size: 13px;
        }

        .sesi-empty {
            padding: 16px 0;
            border-bottom: 1px solid #ECEFF3;
            font-size: 13px;
            color: var(--c-fg-muted);
        }

        /* Panel Sedang Ujian: header abu + empty state */
        .live-panel {
            background: #fff;
            border: 1px solid var(--c-border);
            border-radius: 12px;
            overflow: hidden;
        }

        .live-table {
            width: 100%;
            min-width: 720px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .live-table th {
            padding: 12px 16px;
            background: #F8F9FB;
            border-bottom: 1px solid var(--c-border);
            font-size: 12px;
            font-weight: 500;
            color: var(--c-fg-muted);
            text-align: left;
            white-space: nowrap;
        }

        .live-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #ECEFF3;
            vertical-align: middle;
            font-size: 13px;
            color: var(--c-fg);
        }

        .live-table tbody tr:last-child td { border-bottom: 0; }

        .live-table th.cell-right,
        .live-table td.cell-right { text-align: right; }

        .live-empty-cell {
            padding: 32px 16px !important;
            text-align: center !important;
            color: var(--c-fg-muted);
            font-size: 13px;
            border-bottom: 0 !important;
        }

        .live-name {
            margin: 0;
            font-size: 13px;
            font-weight: 600;
            color: var(--c-fg);
        }

        .live-sub {
            margin: 2px 0 0;
            font-size: 11px;
            color: var(--c-fg-muted);
        }

        .live-sub.mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        }

        .live-time {
            margin: 0;
            font-size: 13px;
            font-weight: 500;
            color: #1A1B25;
            font-variant-numeric: tabular-nums;
        }

        .live-progress {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .live-progress-track {
            width: 110px;
            height: 7px;
            flex-shrink: 0;
            overflow: hidden;
            border-radius: 99px;
            background: #ECEFF3;
        }

        .live-progress-fill {
            height: 100%;
            border-radius: 99px;
            background: #2A3A7C;
        }

        .live-progress-text {
            font-size: 12px;
            font-weight: 600;
            color: #353849;
            font-variant-numeric: tabular-nums;
        }

        .live-warning {
            font-size: 13px;
            font-weight: 600;
            color: #956321;
        }

        .live-warning-btn {
            display: inline-flex;
            align-items: flex-start;
            gap: 6px;
            max-width: 100%;
            border: 0;
            padding: 0;
            background: transparent;
            text-align: left;
            cursor: pointer;
        }

        .live-warning-label {
            display: block;
            min-width: 0;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }

        .live-warning-btn:hover {
            text-decoration: underline;
        }

        .live-end {
            border: 0;
            padding: 0;
            background: transparent;
            color: #DF1C41;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .live-end:hover { text-decoration: underline; }

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
                padding: 14px;
                position: sticky; top: 0; z-index: 20;
            }
            .dash-box-body {
                padding: 16px;
                overflow-y: visible;
                display: block;
            }
            .dash-box-body > * + * { margin-top: 24px; }
        }
    </style>

    <div x-data="{
            confirmModal: false,
            targetSession: null,
            isSubmitting: false,
            violationModal: false,
            violationTarget: null,
            openForceConfirm(s) { this.targetSession = s; this.confirmModal = true; document.body.style.overflow = 'hidden'; },
            closeForceConfirm() { if (this.isSubmitting) return; this.confirmModal = false; document.body.style.overflow = ''; setTimeout(() => { this.targetSession = null; }, 300); },
            submitForce() { if (!this.targetSession || this.isSubmitting) return; this.isSubmitting = true; const f = document.getElementById('form-force-submit'); f.action = '{{ route('banksoal.admin.cbt.force-submit', 'REPLACE_ID') }}'.replace('REPLACE_ID', this.targetSession.id); f.submit(); },
            openViolation(s) { this.violationTarget = s; this.violationModal = true; document.body.style.overflow = 'hidden'; },
            closeViolation() { this.violationModal = false; document.body.style.overflow = ''; setTimeout(() => { this.violationTarget = null; }, 300); }
         }" @keydown.escape.window="if (violationModal) closeViolation()" class="dash-wrap"
         data-latest-violation-id="{{ (int) ($latestViolationId ?? 0) }}"
         data-violation-status-url="{{ route('banksoal.aktivasi.violation-status') }}">

        <div class="dash-box">

        <!-- Box Header -->
        <div class="dash-box-header">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div>
                    <h1 class="text-[24px] font-bold text-gray-900 tracking-tight">Monitoring Ujian</h1>
                    <p class="text-[13px] text-gray-500 mt-0.5">Aktivasi sesi, token, dan pantau peserta ujian secara real-time.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3 flex-shrink-0">
                    @if($periodes->isEmpty())
                        <div class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-50 border border-amber-200 text-amber-700 text-sm font-medium rounded-xl flex-shrink-0">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Belum ada periode. <a href="{{ route('banksoal.periode.setup') }}" class="underline font-bold">Buat Periode Pertama</a>
                        </div>
                    @else
                    @php
                        $periodesForJs = $periodes->map(fn($p) => [
                            'id'   => $p->id,
                            'nama' => $p->nama_periode,
                            'url'  => route('banksoal.aktivasi.index', ['periode_id' => $p->id]),
                        ])->values();
                    @endphp
                    <div x-data="{
                            open: false,
                            search: '',
                            periodeList: {{ $periodesForJs->toJson() }},
                            get filtered() {
                                const q = this.search.trim().toLowerCase();
                                if (!q) return this.periodeList;
                                return this.periodeList.filter(p => p.nama.toLowerCase().includes(q));
                            },
                            toggle() {
                                this.open = !this.open;
                                if (this.open) this.$nextTick(() => this.$refs.searchInput?.focus());
                            },
                            close() { this.open = false; this.search = ''; }
                         }"
                         class="relative flex-shrink-0"
                         @keydown.escape.window="close()">

                        <!-- Trigger Button -->
                        <button type="button" @click="toggle()"
                                class="group inline-flex items-center gap-2.5 h-10 pl-3.5 pr-3 max-w-[360px] bg-white border border-slate-300 rounded-lg text-[13px] font-semibold text-slate-700 shadow-sm transition-colors duration-200 hover:border-primary/40 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer"
                                :aria-expanded="open">
                            @if($selectedPeriode)
                                <span class="w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0"></span>
                            @else
                                <svg class="w-4 h-4 flex-shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            @endif
                            <span class="min-w-0 truncate" title="{{ $selectedPeriode ? $selectedPeriode->nama_periode : 'Pilih Periode Ujian' }}">{{ $selectedPeriode ? $selectedPeriode->nama_periode : 'Pilih Periode Ujian' }}</span>
                            <svg class="w-4 h-4 flex-shrink-0 text-slate-400 transition-transform duration-200"
                                 :class="open ? 'rotate-180' : ''"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Popover Panel -->
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                             @click.outside="close()"
                             class="absolute top-full right-0 mt-2 z-50 bg-white border border-slate-200 rounded-2xl shadow-2xl w-72 overflow-hidden"
                             style="display:none">

                            <!-- Search Header -->
                            <div class="p-3 border-b border-slate-100">
                                <div class="relative">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"></path></svg>
                                    <input x-ref="searchInput"
                                           x-model="search"
                                           type="text"
                                           placeholder="Cari nama periode..."
                                           class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-[13px] text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                </div>
                                <p class="text-[11px] text-slate-400 mt-2 pl-1">
                                    <span x-text="filtered.length"></span> dari {{ $periodes->count() }} periode
                                </p>
                            </div>

                            <!-- Period List -->
                            <div class="overflow-y-auto max-h-64 py-1.5">
                                <template x-for="p in filtered" :key="p.id">
                                    <a :href="p.url"
                                       class="flex items-center gap-3 px-4 py-2.5 text-[13px] font-medium transition-colors hover:bg-slate-50 group/item"
                                       :class="p.id == {{ $selectedPeriodeId ?? 'null' }} ? 'bg-primary/10 text-primary' : 'text-slate-700'">
                                        <span class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                              :class="p.id == {{ $selectedPeriodeId ?? 'null' }} ? 'bg-primary' : 'bg-slate-300'"></span>
                                        <span x-text="p.nama" class="flex-1 whitespace-normal break-words text-left"></span>
                                        <svg x-show="p.id == {{ $selectedPeriodeId ?? 'null' }}" class="w-3.5 h-3.5 text-primary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </a>
                                </template>

                                <!-- Empty state -->
                                <div x-show="filtered.length === 0" class="px-4 py-8 text-center">
                                    <svg class="w-8 h-8 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"></path></svg>
                                    <p class="text-[13px] text-slate-400 font-medium">Tidak ditemukan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <button type="button" onclick="window.location.reload()"
                            class="inline-flex h-10 items-center justify-center gap-2 px-4 bg-white border border-slate-300 rounded-lg text-[13px] font-semibold text-slate-700 shadow-sm transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary cursor-pointer"
                            aria-label="Refresh" title="Refresh">
                        <svg class="h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>Refresh</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Box Body -->
        <div class="dash-box-body">

            <!-- Section: Sesi & Token -->
            <section class="mon-content">
                <h2 class="section-title">Sesi &amp; Token</h2>

                @if($jadwals->isEmpty())
                    <div class="sesi-empty">
                        @if($selectedPeriode)
                            Belum ada sesi pada periode ini.
                        @elseif($periodes->isNotEmpty())
                            Pilih periode ujian untuk menampilkan sesi.
                        @else
                            Belum ada periode ujian. <a href="{{ route('banksoal.periode.setup') }}" class="font-semibold text-primary hover:underline">Buat periode pertama</a>
                        @endif
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="sesi-table">
                            <colgroup>
                                <col style="width:43%">
                                <col style="width:11%">
                                <col style="width:10%">
                                <col style="width:8%">
                                <col style="width:28%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>Sesi</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                    <th>Peserta</th>
                                    <th>Token / Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwals->groupBy(fn($j) => optional($j->tanggal_ujian)->format('Y-m-d') ?? 'tanpa-tanggal') as $tanggal => $items)
                                    <tr class="sesi-date">
                                        <td colspan="5">
                                            {{ $tanggal === 'tanpa-tanggal' ? 'Tanpa tanggal' : \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                                        </td>
                                    </tr>

                                    @foreach($items as $jadwal)
                                        @php
                                            $isExpired = false;
                                            if ($jadwal->tanggal_ujian && $jadwal->waktu_selesai) {
                                                $waktuSelesai = \Carbon\Carbon::parse($jadwal->tanggal_ujian->format('Y-m-d') . ' ' . $jadwal->waktu_selesai);
                                                $isExpired = now()->gte($waktuSelesai);
                                            }
                                            $statusValue = $isExpired ? 'selesai' : ($jadwal->status?->value ?? 'menunggu_jadwal');
                                            $namaSesi = is_numeric($jadwal->nama_sesi) ? 'Sesi ' . $jadwal->nama_sesi : $jadwal->nama_sesi;
                                        @endphp

                                        <tr>
                                            <td><span class="sesi-name">{{ $namaSesi }}</span></td>

                                            <td>
                                                <span class="sesi-time">
                                                    {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }}&ndash;{{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}
                                                </span>
                                            </td>

                                            <td>
                                                <span class="sesi-status {{ $statusValue === 'aktif' ? 'is-live' : '' }}">
                                                    @if($statusValue === 'aktif')
                                                        <span class="sesi-dot is-live"></span>
                                                        Berjalan
                                                    @elseif($statusValue === 'menunggu_jadwal')
                                                        <span class="sesi-dot is-waiting"></span>
                                                        Menunggu
                                                    @else
                                                        <span class="sesi-dot is-done"></span>
                                                        Selesai
                                                    @endif
                                                </span>
                                            </td>

                                            <td>
                                                <span class="sesi-count">{{ $jadwal->pendaftars_count }} / {{ $jadwal->kuota ?? '—' }}</span>
                                            </td>

                                            <td>
                                                @if($statusValue === 'aktif' && $jadwal->token)
                                                    <span x-data="{ copied: false, copy() { if (navigator.clipboard) { navigator.clipboard.writeText('{{ $jadwal->token }}'); } this.copied = true; setTimeout(() => this.copied = false, 1500); } }">
                                                        <span class="sesi-token">{{ $jadwal->token }}</span>
                                                        <button type="button" @click="copy()" aria-label="Salin token {{ $namaSesi }}" title="Salin token" class="sesi-copy">
                                                            <svg x-show="!copied" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <rect x="9" y="9" width="13" height="13" rx="2" />
                                                                <path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1" />
                                                            </svg>
                                                            <svg x-show="copied" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 6L9 17l-5-5" />
                                                            </svg>
                                                        </button>
                                                    </span>
                                                @elseif($statusValue === 'aktif' || $statusValue === 'menunggu_jadwal')
                                                    <form action="{{ route('banksoal.aktivasi.toggle', $jadwal->id) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="aktif">
                                                        <button type="submit" class="sesi-activate">Aktifkan sesi</button>
                                                    </form>
                                                @else
                                                    <span class="sesi-token-empty">&mdash;</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <!-- Section: Sedang Ujian -->
            <section class="mon-content">
                <h2 class="section-title">Sedang Ujian</h2>

                <div class="live-panel">
                    <div class="overflow-x-auto">
                        <table class="live-table">
                            <colgroup>
                                <col style="width:20%">
                                <col style="width:13%">
                                <col style="width:14%">
                                <col style="width:17%">
                                <col style="width:25%">
                                <col style="width:11%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>Peserta</th>
                                    <th>Sesi</th>
                                    <th>Mulai</th>
                                    <th>Progres</th>
                                    <th>Pelanggaran</th>
                                    <th class="cell-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($sessions->isEmpty())
                                    <tr>
                                        <td colspan="6" class="live-empty-cell">Tidak ada peserta yang sedang ujian.</td>
                                    </tr>
                                @else
                                    @foreach($sessions as $session)
                                        @php
                                            $totalSoal = $session->jawabans_count;
                                            $terjawab  = $session->terjawab_count;
                                            $progres   = $totalSoal > 0 ? round(($terjawab / $totalSoal) * 100) : 0;

                                            $namaPeserta = $session->user?->name ?? 'Peserta';
                                            $nimPeserta  = $session->user?->student?->student_number ?? 'NIM tidak tersedia';
                                            $latestViolation = $latestViolations[$session->id] ?? null;
                                        @endphp
                                        <tr>
                                            <td>
                                                <p class="live-name">{{ $namaPeserta }}</p>
                                                <p class="live-sub mono">{{ $nimPeserta }}</p>
                                            </td>

                                            <td>
                                                <p class="live-name">
                                                    @if($session->jadwal)
                                                        {{ is_numeric($session->jadwal->nama_sesi) ? 'Sesi ' . $session->jadwal->nama_sesi : $session->jadwal->nama_sesi }}
                                                    @else
                                                        {{ $session->title }}
                                                    @endif
                                                </p>
                                            </td>

                                            <td>
                                                <p class="live-time">{{ \Carbon\Carbon::parse($session->started_at)->format('H:i') }}</p>
                                            </td>

                                            <td>
                                                <div class="live-progress">
                                                    <div class="live-progress-track">
                                                        <div class="live-progress-fill" style="width: {{ $progres }}%;"></div>
                                                    </div>
                                                    <span class="live-progress-text">{{ $terjawab }}/{{ $totalSoal }}</span>
                                                </div>
                                            </td>

                                            <td>
                                                @if($latestViolation)
                                                    <button type="button"
                                                        class="live-warning live-warning-btn"
                                                        title="Lihat rincian pelanggaran"
                                                        aria-haspopup="dialog"
                                                        @click="openViolation({
                                                            name: @js($namaPeserta),
                                                            nim: @js($nimPeserta),
                                                            total: {{ $session->cheat_logs_count }},
                                                            logs: @js($violationDetails[$session->id] ?? [])
                                                        })">
                                                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                        </svg>
                                                        <span class="live-warning-label">{{ $latestViolation['label'] }}</span>
                                                    </button>
                                                @else
                                                    <span class="text-[13px] text-[#A4ABB8]">Tidak ada</span>
                                                @endif
                                            </td>

                                            <td class="cell-right">
                                                <button type="button"
                                                    @click="openForceConfirm({ id: {{ $session->id }}, name: @js($namaPeserta), nim: @js($nimPeserta) })"
                                                    class="live-end"
                                                    title="Akhiri sesi ujian">
                                                    Akhiri
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

        </div> {{-- end .dash-box-body --}}
        </div> {{-- end .dash-box --}}

        <!-- Modal Popup: Rincian Pelanggaran -->
        <div x-show="violationModal" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="violation-modal-title" class="fixed inset-0 z-[70] flex items-center justify-center p-4 sm:p-6" style="display: none;" x-cloak>
            <div x-show="violationModal"
                  x-transition:enter="ease-out duration-300"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  x-transition:leave="ease-in duration-200"
                  x-transition:leave-start="opacity-100"
                  x-transition:leave-end="opacity-0"
                  class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                  @click="closeViolation()">
            </div>

            <div x-show="violationModal"
                  x-transition:enter="ease-out duration-300"
                  x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                  x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                  x-transition:leave="ease-in duration-200"
                  x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                  x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                  class="relative flex max-h-[85vh] w-full max-w-lg flex-col overflow-hidden rounded-xl bg-white shadow-2xl">

                <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-4">
                    <div class="min-w-0">
                        <h3 id="violation-modal-title" class="text-[17px] font-semibold tracking-tight text-slate-800">Rincian Pelanggaran</h3>
                        <p class="mt-0.5 truncate text-[13px] font-medium text-slate-600" x-text="violationTarget?.name"></p>
                        <p class="font-mono text-[12px] text-slate-400" x-text="violationTarget?.nim"></p>
                    </div>
                    <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-[12px] font-semibold text-[#956321]">
                        <span x-text="violationTarget?.total ?? 0"></span> catatan
                    </span>
                </div>

                <div class="flex-1 overflow-y-auto px-6 py-4">
                    <template x-if="violationTarget && violationTarget.logs.length">
                        <ul class="flex flex-col gap-3">
                            <template x-for="(log, idx) in violationTarget.logs" :key="idx">
                                <li class="flex gap-3 rounded-lg border border-slate-200 p-3">
                                    <span class="shrink-0 font-mono text-[12px] text-slate-500" x-text="log.time"></span>
                                    <div class="min-w-0">
                                        <p class="text-[13px] font-semibold text-slate-800" x-text="log.label"></p>
                                        <p class="text-[12px] leading-relaxed text-slate-500" x-text="log.description || '-'"></p>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </template>
                    <p x-show="!violationTarget || !violationTarget.logs.length" class="text-[13px] text-slate-500">Tidak ada rincian pelanggaran.</p>

                    <p class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-[12px] leading-relaxed text-[#956321]">
                        Satu kejadian dapat tercatat lebih dari sekali (mis. pindah tab memicu &quot;Berpindah aplikasi/jendela&quot; dan &quot;Pindah tab / minimize&quot;).
                    </p>
                </div>

                <div class="border-t border-slate-100 bg-slate-50 px-6 py-4 rounded-b-xl">
                    <button type="button" @click="closeViolation()" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-[13px] font-semibold text-slate-700 shadow-sm transition-colors hover:bg-slate-50 focus:outline-none cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Popup: Konfirmasi Force Submit -->
        <div x-show="confirmModal" tabindex="-1" role="dialog" aria-modal="true" aria-labelledby="force-modal-title" class="fixed inset-0 z-[70] flex items-center justify-center p-4 sm:p-6" style="display: none;" x-cloak>
            <div x-show="confirmModal"
                  x-transition:enter="ease-out duration-300"
                  x-transition:enter-start="opacity-0"
                  x-transition:enter-end="opacity-100"
                  x-transition:leave="ease-in duration-200"
                  x-transition:leave-start="opacity-100"
                  x-transition:leave-end="opacity-0"
                  class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
                  @click="closeForceConfirm()">
            </div>

            <div x-show="confirmModal"
                  x-transition:enter="ease-out duration-300"
                  x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                  x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                  x-transition:leave="ease-in duration-200"
                  x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                  x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                  class="relative flex max-h-full w-full max-w-sm flex-col overflow-hidden rounded-xl bg-white shadow-2xl">

                <div class="px-6 pb-4 pt-6 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-50">
                        <svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 id="force-modal-title" class="mb-2 text-[17px] font-semibold tracking-tight text-slate-800">Akhiri Paksa Ujian?</h3>
                    <p class="text-[13px] font-medium leading-relaxed text-slate-500">
                        Anda yakin ingin memaksa selesai sesi ujian mahasiswa ini?
                    </p>
                    <div x-show="targetSession" class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-3 text-left">
                        <p class="text-[13px] font-bold text-slate-800" x-text="targetSession?.name"></p>
                        <p class="text-[12px] font-mono text-slate-500" x-text="targetSession?.nim"></p>
                    </div>
                </div>

                <div class="flex items-center gap-3 border-t border-slate-100 bg-slate-50 rounded-b-xl px-6 py-4">
                    <button type="button" @click="closeForceConfirm()" class="flex-1 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-[13px] font-semibold text-slate-700 shadow-sm transition-colors hover:bg-slate-50 focus:outline-none cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="submitForce()" :disabled="isSubmitting" class="flex flex-1 cursor-pointer items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-[13px] font-semibold text-white shadow-sm transition-colors hover:bg-red-700 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50">
                        <svg x-show="isSubmitting" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span x-text="isSubmitting ? 'Memproses...' : 'Ya, Akhiri Paksa'"></span>
                    </button>
                </div>
            </div>
        </div>

        <form id="form-force-submit" method="POST" style="display:none;">
            @csrf
        </form>
    </div>
</x-banksoal::layouts.admin>
