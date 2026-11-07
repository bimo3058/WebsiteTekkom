<x-banksoal::layouts.admin>
    @section('breadcrumbs')
        <a href="#" class="text-gray-500 hover:text-primary transition-colors">Ujian Komprehensif</a>
        <span class="mx-2 text-gray-300">/</span>
        <a href="{{ route('banksoal.admin.cbt.riwayat') }}"
            class="text-gray-500 hover:text-primary transition-colors">Riwayat Ujian</a>
        <span class="mx-2 text-gray-300">/</span>
        <span class="text-gray-800 font-medium">Detail Hasil</span>
    @endsection

   
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
            padding: 16px 24px;
        }

        .dash-box-body {
            flex: 1; overflow-y: auto; padding: 20px 24px;
            display: flex; flex-direction: column; gap: 2px;
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

    @php
        $isNoShow = $session->title === 'Tidak Mengerjakan';
        $totalSoal = $session->jawabans->count();
        $benar = $session->jawabans->filter(fn($j) => $j->opsiTerpilih && $j->opsiTerpilih->is_benar)->count();
        $salah = $session->jawabans->filter(fn($j) => $j->jawaban_dipilih && (!$j->opsiTerpilih || !$j->opsiTerpilih->is_benar))->count();
        $tidakDijawab = $totalSoal - ($benar + $salah);
        $skor = (int) ($session->score ?? 0);
        $lulus = $skor >= 60;
        $pct = min(100, $skor);
        $initial = strtoupper(substr($session->user->name ?? 'M', 0, 1));

        $durasiText = '—';
        if (!$isNoShow && $session->started_at && $session->finished_at) {
            $diff = \Carbon\Carbon::parse($session->started_at)->diff(\Carbon\Carbon::parse($session->finished_at));
            $durasiText = $diff->i . ' Menit ' . $diff->s . ' Detik';
        }
    @endphp

    <div class="dash-wrap">
        <div class="dash-box">

        {{-- ── Box Header ── --}}
        <div class="dash-box-header"
            style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
            <div>
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:3px;">
                    <h1
                        style="font-size:22px; font-weight:700; color:var(--c-fg); letter-spacing:-0.02em; line-height:1.2;">
                        Detail Hasil Ujian</h1>
                </div>
                <p style="font-size:12px; color:var(--c-fg-muted);">
                    <span style="color:var(--c-fg); font-weight:600;">{{ $session->user->name }}</span>
                    <span style="margin-left:4px; color:var(--c-fg-placeholder);">·</span>
                    <span style="margin-left:4px; color:var(--c-fg-muted);">NIM:
                        {{ optional($session->user->student)->student_number ?? '—' }}</span>
                </p>
            </div>
            <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                {{-- Kembali button --}}
                <a href="{{ route('banksoal.admin.cbt.riwayat') }}"
                    style="display:inline-flex; align-items:center; gap:6px; padding:8px 14px; background:#fff; border:1px solid var(--c-border); border-radius:8px; font-size:12px; font-weight:600; color:var(--c-fg-sec); text-decoration:none; transition:all .15s; white-space:nowrap; box-shadow:0 1px 2px rgba(0,0,0,.04);"
                    onmouseover="this.style.background='var(--c-bg)'; this.style.borderColor='var(--c-border-strong)'; this.style.boxShadow='0 2px 6px rgba(0,0,0,.07)'"
                    onmouseout="this.style.background='#fff'; this.style.borderColor='var(--c-border)'; this.style.boxShadow='0 1px 2px rgba(0,0,0,.04)'">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"
                        stroke-linecap="round">
                        <path d="M15 19l-7-7 7-7" />
                    </svg>
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        <!-- Box Body -->
        <div class="dash-box-body">

        {{-- ── Summary Panel (Stats Grid) ── --}}
        <div class="dash-stats" style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:24px;">
            {{-- Score --}}
            <div style="border:1px solid var(--c-border);border-radius:12px;padding:14px 16px;box-shadow:var(--shadow-card);transition:border-color .15s,box-shadow .15s;cursor:default;"
                onmouseover="this.style.borderColor='var(--c-primary-border)';this.style.boxShadow='0 4px 14px rgba(11,38,110,0.07)'"
                onmouseout="this.style.borderColor='var(--c-border)';this.style.boxShadow='var(--shadow-card)'">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                    <div
                        style="width:28px;height:28px;border-radius:8px;background:var(--c-primary-subtle);color:var(--c-primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            @if($lulus)
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            @else
                                <path d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            @endif
                        </svg>
                    </div>
                    <p style="font-size:12px;font-weight:500;color:var(--c-fg-muted);">Nilai Total
                        ({{ $lulus ? 'Lulus' : 'Tidak Lulus' }})</p>
                </div>
                <p
                    style="font-size:24px;font-weight:700;color:var(--c-fg);line-height:1;letter-spacing:-.02em;">
                    {{ $skor }}<span style="font-size:14px;color:var(--c-fg-muted);font-weight:500;">/100</span>
                </p>
            </div>

            {{-- Benar --}}
            <div style="border:1px solid var(--c-border);border-radius:12px;padding:14px 16px;box-shadow:var(--shadow-card);transition:border-color .15s,box-shadow .15s;cursor:default;"
                onmouseover="this.style.borderColor='var(--c-primary-border)';this.style.boxShadow='0 4px 14px rgba(11,38,110,0.07)'"
                onmouseout="this.style.borderColor='var(--c-border)';this.style.boxShadow='var(--shadow-card)'">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                    <div
                        style="width:28px;height:28px;border-radius:8px;background:var(--c-primary-subtle);color:var(--c-primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p style="font-size:12px;font-weight:500;color:var(--c-fg-muted);">Jawaban Benar</p>
                </div>
                <p style="font-size:24px;font-weight:700;color:var(--c-fg);line-height:1;letter-spacing:-.02em;">
                    {{ $benar }}
                </p>
            </div>

            {{-- Salah --}}
            <div style="border:1px solid var(--c-border);border-radius:12px;padding:14px 16px;box-shadow:var(--shadow-card);transition:border-color .15s,box-shadow .15s;cursor:default;"
                onmouseover="this.style.borderColor='var(--c-primary-border)';this.style.boxShadow='0 4px 14px rgba(11,38,110,0.07)'"
                onmouseout="this.style.borderColor='var(--c-border)';this.style.boxShadow='var(--shadow-card)'">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                    <div
                        style="width:28px;height:28px;border-radius:8px;background:var(--c-primary-subtle);color:var(--c-primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <p style="font-size:12px;font-weight:500;color:var(--c-fg-muted);">Jawaban Salah</p>
                </div>
                <p style="font-size:24px;font-weight:700;color:var(--c-fg);line-height:1;letter-spacing:-.02em;">
                    {{ $salah }}
                </p>
            </div>

            {{-- Kosong --}}
            <div style="border:1px solid var(--c-border);border-radius:12px;padding:14px 16px;box-shadow:var(--shadow-card);transition:border-color .15s,box-shadow .15s;cursor:default;"
                onmouseover="this.style.borderColor='var(--c-primary-border)';this.style.boxShadow='0 4px 14px rgba(11,38,110,0.07)'"
                onmouseout="this.style.borderColor='var(--c-border)';this.style.boxShadow='var(--shadow-card)'">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                    <div
                        style="width:28px;height:28px;border-radius:8px;background:var(--c-primary-subtle);color:var(--c-primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 12H4" />
                        </svg>
                    </div>
                    <p style="font-size:12px;font-weight:500;color:var(--c-fg-muted);">Tidak Dijawab</p>
                </div>
                <p style="font-size:24px;font-weight:700;color:var(--c-fg);line-height:1;letter-spacing:-.02em;">
                    {{ $tidakDijawab }}
                </p>
            </div>

            {{-- Durasi --}}
            <div style="border:1px solid var(--c-border);border-radius:12px;padding:14px 16px;box-shadow:var(--shadow-card);transition:border-color .15s,box-shadow .15s;cursor:default;"
                onmouseover="this.style.borderColor='var(--c-primary-border)';this.style.boxShadow='0 4px 14px rgba(11,38,110,0.07)'"
                onmouseout="this.style.borderColor='var(--c-border)';this.style.boxShadow='var(--shadow-card)'">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                    <div
                        style="width:28px;height:28px;border-radius:8px;background:var(--c-primary-subtle);color:var(--c-primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p style="font-size:12px;font-weight:500;color:var(--c-fg-muted);">Durasi Pengerjaan</p>
                </div>
                <p style="font-size:15px;font-weight:700;color:var(--c-fg);line-height:1.5;letter-spacing:-.02em;">
                    {{ $durasiText }}
                </p>
            </div>
        </div>

        <style>
            @media (max-width: 1280px) {
                .dash-stats {
                    grid-template-columns: repeat(3, 1fr) !important;
                }
            }

            @media (max-width: 640px) {
                .dash-stats {
                    grid-template-columns: repeat(2, 1fr) !important;
                }
            }
        </style>

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

        {{-- ── Review Jawaban (urut nomor = urut CPL) ── --}}
        <div>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h2 style="font-size:16px; font-weight:700; color:var(--c-fg); margin-bottom:2px;">Review Jawaban
                    </h2>
                    <p style="font-size:12px; color:var(--c-fg-muted);">Detail rincian soal dan opsi yang dipilih
                        peserta.</p>
                </div>

                <div class="flex items-center gap-5 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-sm"
                    style="border-color:var(--c-border);">
                    {{-- Legend --}}
                    <div class="hidden md:flex items-center gap-3">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-[12px] font-medium text-gray-500">Benar</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span class="text-[12px] font-medium text-gray-500">Salah</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-gray-300"></span>
                            <span class="text-[12px] font-medium text-gray-500">Kosong</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Daftar jawaban (urut nomor = urut CPL) --}}
            <div>
                @if($session->jawabans->isEmpty())
                    <div class="text-center py-16 bg-white rounded-xl border border-gray-200 shadow-sm"
                        style="border-color:var(--c-border);">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <p class="text-gray-500 font-medium text-sm">Belum ada data jawaban tersimpan.</p>
                    </div>
                @else
                    <div>
                        @foreach($session->jawabans->sortBy('urutan_soal') as $kompreJawaban)
                            @include('banksoal::admin.cbt.partials._jawaban-card', ['kompreJawaban' => $kompreJawaban])
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        </div> {{-- end .dash-box-body --}}
        </div> {{-- end .dash-box --}}

    </div>

</x-banksoal::layouts.admin>