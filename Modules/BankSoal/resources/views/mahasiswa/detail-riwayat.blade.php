<x-banksoal::layouts.mahasiswa>
    @section('breadcrumbs')
        <a href="{{ route('komprehensif.mahasiswa.riwayat') }}"
            class="text-slate-500 hover:text-primary transition-colors">Riwayat Ujian</a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-900 font-semibold">Detail Hasil</span>
    @endsection

    {{-- Style Box Wrap khas SITKOM untuk Dashboard --}}
    <style>
        .sitkom-content {
            padding: 0 !important;
            display: flex;
            flex-direction: column;
            flex: 1;
            overflow: hidden;
        }

        main.overflow-y-auto {
            overflow: hidden !important;
        }

        main.overflow-y-auto>div {
            padding: 0 !important;
            max-width: none !important;
            height: 100% !important;
            display: flex;
            flex-direction: column;
        }

        #banksoal-main-content {
            padding: 0 !important;
            max-width: 100% !important;
            height: 100% !important;
            display: flex;
            flex-direction: column;
        }

        .dash-wrap {
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter Tight', sans-serif;
        }

        .dash-box {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            background: #fff;
            border: none;
            border-bottom: 1px solid var(--c-border);
            border-radius: 0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            width: 100%;
            box-sizing: border-box;
        }

        .dash-box-header {
            background: #fff;
            border-bottom: 1px solid var(--c-border);
            flex-shrink: 0;
            width: 100%;
            box-sizing: border-box;
            padding: 16px 24px;
        }

        .dash-box-body {
            flex: 1;
            overflow-y: auto;
            padding: 20px 24px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .dash-box-body::-webkit-scrollbar {
            width: 6px;
        }

        .dash-box-body::-webkit-scrollbar-thumb {
            background: var(--c-border-strong);
            border-radius: 10px;
        }

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
                border-radius: 0;
                border: none;
                border-bottom: 1px solid var(--c-border);
                display: block;
                height: auto;
                overflow: visible;
            }

            .dash-box-header {
                padding: 12px 14px;
                position: sticky;
                top: 0;
                z-index: 20;
            }

            .dash-box-body {
                padding: 14px;
                overflow-y: visible;
                display: block;
            }
        }
    </style>

    @php
        $totalSoal = $session->jawabans->count();
        $benar = $session->jawabans->filter(fn($j) => $j->opsiTerpilih && $j->opsiTerpilih->is_benar)->count();
        $salah = $session->jawabans->filter(fn($j) => $j->jawaban_dipilih && (!$j->opsiTerpilih || !$j->opsiTerpilih->is_benar))->count();
        $tidakDijawab = $totalSoal - ($benar + $salah);
        $skor = (int) ($session->score ?? 0);
        $lulus = $skor >= 60;
        $pct = min(100, $skor);
        $initial = strtoupper(substr($session->user->name ?? 'M', 0, 1));
    @endphp

    <div class="dash-wrap">
        <div class="dash-box">
            {{-- ── Header ── --}}
            <div class="dash-box-header">
                <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
                    <div>
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:3px;">
                            <h1
                                style="font-size:22px; font-weight:700; color:var(--c-fg); letter-spacing:-0.02em; line-height:1.2;">
                                Detail Hasil Ujian</h1>
                        </div>
                        <p style="font-size:13px; color:var(--c-fg-muted);">
                            {{ $session->jadwal->periode->nama_periode ?? 'Ujian Komprehensif' }} - Sesi
                            {{ $session->jadwal->nama_sesi ?? '-' }}
                        </p>
                    </div>
                    <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                        {{-- Kembali button --}}
                        <a href="{{ route('komprehensif.mahasiswa.riwayat') }}"
                            style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-weight:600; color:var(--c-fg-sec); text-decoration:none; transition:all .15s; white-space:nowrap; box-shadow:0 1px 2px rgba(0,0,0,.04);"
                            onmouseover="this.style.background='var(--c-bg)'; this.style.borderColor='var(--c-border-strong)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,.07)'"
                            onmouseout="this.style.background='#fff'; this.style.borderColor='var(--c-border)'; this.style.boxShadow='0 1px 2px rgba(0,0,0,.04)'">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                stroke-width="1.8" stroke-linecap="round">
                                <path d="M15 19l-7-7 7-7" />
                            </svg>
                            <span>Kembali</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="dash-box-body">
                {{-- ── Ringkasan Hasil ── --}}
                @php
                    $pctBenar = $totalSoal > 0 ? round($benar / $totalSoal * 100, 1) : 0;
                    $pctSalah = $totalSoal > 0 ? round($salah / $totalSoal * 100, 1) : 0;
                    $pctKosong = $totalSoal > 0 ? round($tidakDijawab / $totalSoal * 100, 1) : 0;
                    $waktuMulaiTes = $session->started_at ? \Carbon\Carbon::parse($session->started_at)->translatedFormat('d M Y, H.i.s') : '-';
                    $waktuSelesaiTes = $session->finished_at ? \Carbon\Carbon::parse($session->finished_at)->translatedFormat('d M Y, H.i.s') : '-';
                @endphp
                <div class="bg-white border border-[#DFE1E6] rounded-[12px] p-5 sm:p-6 w-full">
                    <div class="flex items-start gap-10 sm:gap-16">
                        <div>
                            <p class="text-[20px] font-semibold text-[#2A3A7C] leading-tight">{{ number_format((float)$skor, 2, '.', '') }}</p>
                            <p class="text-[12px] font-normal text-[#666D80] mt-1">Nilai Total</p>
                        </div>
                        <div>
                            <p class="text-[20px] font-semibold leading-tight @if($lulus) text-[#287F6E] @else text-[#DF1C41] @endif">{{ $lulus ? 'Lulus' : 'Tidak Lulus' }}</p>
                            <p class="text-[12px] font-normal text-[#666D80] mt-1">Status</p>
                        </div>
                    </div>
                    <div class="mt-4 flex h-2 w-full overflow-hidden rounded-full bg-[#ECEFF3]">
                        <div class="h-full bg-[#287F6E]" style="width: {{ $pctBenar }}%"></div>
                        <div class="h-full bg-[#DF1C41]" style="width: {{ $pctSalah }}%"></div>
                        <div class="h-full bg-[#AAB4D5]" style="width: {{ $pctKosong }}%"></div>
                    </div>
                    <div class="mt-3 grid max-w-xl grid-cols-3 gap-3">
                        <div>
                            <p class="flex items-center gap-1.5 text-[12px] font-normal text-[#666D80]"><span class="w-2 h-2 rounded-full bg-[#287F6E] shrink-0"></span>Benar</p>
                            <p class="text-[20px] font-semibold text-[#0D0D12] mt-1">{{ $benar }}</p>
                        </div>
                        <div>
                            <p class="flex items-center gap-1.5 text-[12px] font-normal text-[#666D80]"><span class="w-2 h-2 rounded-full bg-[#DF1C41] shrink-0"></span>Salah</p>
                            <p class="text-[20px] font-semibold text-[#0D0D12] mt-1">{{ $salah }}</p>
                        </div>
                        <div>
                            <p class="flex items-center gap-1.5 text-[12px] font-normal text-[#666D80]"><span class="w-2 h-2 rounded-full bg-[#AAB4D5] shrink-0"></span>Tidak dijawab</p>
                            <p class="text-[20px] font-semibold text-[#0D0D12] mt-1">{{ $tidakDijawab }}</p>
                        </div>
                    </div>
                    <div class="border-t border-[#DFE1E6] mt-4 pt-4">
                        <dl class="flex max-w-2xl flex-col gap-2.5 text-[13px] leading-relaxed">
                            <div class="flex items-start justify-between gap-3">
                                <dt class="font-normal text-[#666D80] shrink-0">Waktu Mulai Tes</dt>
                                <dd class="font-medium text-[#0D0D12] text-right">{{ $waktuMulaiTes }}</dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="font-normal text-[#666D80] shrink-0">Waktu Selesai Tes</dt>
                                <dd class="font-medium text-[#0D0D12] text-right">{{ $waktuSelesaiTes }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- ── Nilai per Capaian Pembelajaran (CPL) ── --}}
                @if(isset($cplStats) && $cplStats->isNotEmpty())
                    @php
                        $tglUjian = optional($session->jadwal)->tanggal_ujian ?? $session->started_at;
                        $tglUjianLabel = $tglUjian ? \Carbon\Carbon::parse($tglUjian)->format('dmY') : '-';
                        $waktuMulaiLabel = $session->started_at ? \Carbon\Carbon::parse($session->started_at)->format('Y-m-d H:i:s') : '—';
                        $waktuSelesaiLabel = $session->finished_at ? \Carbon\Carbon::parse($session->finished_at)->format('Y-m-d H:i:s') : '—';
                    @endphp

                    <div class="flex flex-col gap-6 mb-6">
                        {{-- Tabel Nilai per CPL --}}
                    <div class="w-full flex flex-col"
                        style="min-width: 0; background:#fff; border:1px solid var(--c-border); border-radius:14px; box-shadow:var(--shadow-card); overflow:hidden;">
                        <div style="padding:16px 20px 14px; border-bottom:1px solid var(--c-border);">
                            <h3 style="font-size:13px; font-weight:700; color:var(--c-fg); margin-bottom:0;">Nilai per CPL</h3>
                        </div>
                        <div style="overflow-x:auto;">
                            <table style="width:100%; min-width:680px; text-align:left; border-collapse:collapse;">
                                <thead>
                                    <tr>
                                        <th
                                            style="padding:10px 20px; font-size:11px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:.04em; border-bottom:1px solid var(--c-border); background:var(--c-bg); width:56px; text-align:center;">
                                            No</th>
                                        <th
                                            style="padding:10px 20px; font-size:11px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:.04em; border-bottom:1px solid var(--c-border); background:var(--c-bg);">
                                            Nama Tes</th>
                                        <th
                                            style="padding:10px 20px; font-size:11px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:.04em; border-bottom:1px solid var(--c-border); background:var(--c-bg);">
                                            Waktu Mulai Tes</th>
                                        <th
                                            style="padding:10px 20px; font-size:11px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:.04em; border-bottom:1px solid var(--c-border); background:var(--c-bg);">
                                            Waktu Selesai Tes</th>
                                        <th
                                            style="padding:10px 20px; font-size:11px; font-weight:600; color:var(--c-fg-muted); text-transform:uppercase; letter-spacing:.04em; border-bottom:1px solid var(--c-border); background:var(--c-bg);">
                                            Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cplStats as $i => $stat)
                                        <tr style="border-bottom:1px solid var(--c-border);">
                                            <td
                                                style="padding:12px 20px; font-size:12px; color:var(--c-fg-muted); text-align:center;">
                                                {{ $i + 1 }}</td>
                                            <td
                                                style="padding:12px 20px; font-size:12px; font-weight:600; color:var(--c-fg);">
                                                {{ preg_replace('/^CPL-0*/', 'CPL ', $stat['kode']) }} ({{ $tglUjianLabel }})</td>
                                            <td
                                                style="padding:12px 20px; font-size:12px; color:var(--c-fg-muted); white-space:nowrap;">
                                                {{ $waktuMulaiLabel }}</td>
                                            <td
                                                style="padding:12px 20px; font-size:12px; color:var(--c-fg-muted); white-space:nowrap;">
                                                {{ $waktuSelesaiLabel }}</td>
                                            <td
                                                style="padding:12px 20px; font-size:12px; font-weight:700; color:var(--c-fg);">
                                                {{ number_format($stat['benar'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    </div>
                @endif


            </div>

        </div> {{-- End dash-box-body --}}
    </div> {{-- End dash-box --}}
    </div> {{-- End dash-wrap --}}
</x-banksoal::layouts.mahasiswa>