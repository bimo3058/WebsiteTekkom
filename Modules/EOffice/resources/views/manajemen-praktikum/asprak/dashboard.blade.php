<x-eoffice::manajemen-praktikum.layout pageTitle="Dashboard Asisten">

    @php
        /** @var \Modules\EOffice\Models\AsistenPraktikum $asprak */
        /** @var \Illuminate\Support\Collection|\Modules\EOffice\Models\AsistenPraktikum[] $allAsprak */
        /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Tugas[] $tugasMendatang */
        /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\PengumpulanTugas[] $pengumpulanPending */
        /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Pengumuman[] $pengumumanTerbaru */
    @endphp@php
        $name = auth()->user()->name;
        $nameParts = explode(' ', $name);
        $firstName = $nameParts[0];
    @endphp

    {{-- Page Header --}}
    <x-slot name="header">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
            <div>
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:3px;">
                    <h1
                        style="font-size:22px; font-weight:700; color:var(--c-fg, #0D0D12); letter-spacing:-0.02em; line-height:1.2; margin:0;">
                        Dashboard Asisten</h1>
                    <span class="mp-badge sm" style="background:#E0E7FF;color:#6366F1;"><span
                            class="dot"></span>Asisten</span>
                </div>
                <p style="font-size:12px; color:var(--c-fg-muted, #666D80); margin:0;">
                    Halo, <span style="color:var(--c-fg, #0D0D12); font-weight:600;">{{ $firstName }}</span>
                    <span style="margin-left:4px; color:var(--c-fg-placeholder, #A4ABB8);">·</span>
                    <span
                        style="margin-left:4px; color:var(--c-fg-muted, #666D80);">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                        · {{ $semesterLabel }}</span>
                </p>
            </div>
            <div>
                @if(isset($allAsprak) && $allAsprak->count() > 0)
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="font-size:13px; font-weight:600; color:#353849;">Informasi mengenai:</span>
                        <form method="GET" action="{{ url()->current() }}" style="margin:0;">
                            @foreach(request()->except('praktikum_id') as $key => $value)
                                @if(is_array($value))
                                    @foreach($value as $v)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            @php
                                $activeAspraks = $allAsprak->filter(function ($ap) {
                                    return $ap->praktikum && $ap->praktikum->is_active;
                                });
                                $praktikumOptions = $activeAspraks->map(function ($ap) {
                                    return ['value' => $ap->praktikum_id, 'label' => $ap->praktikum->nama];
                                })->toArray();

                                $selectedPraktikum = $asprak?->praktikum_id ?? ($activeAspraks->first()?->praktikum_id ?? '');
                            @endphp
                            @if(count($praktikumOptions) > 0)
                                <x-eoffice::manajemen-praktikum.ui.select name="praktikum_id" :options="$praktikumOptions"
                                    :selected="$selectedPraktikum" placeholder="Pilih Praktikum..."
                                    onChange="$event.target.form.submit()" minWidth="250px" />
                            @endif
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </x-slot>



    @if(!isset($asprak) || !$asprak)
        <div class="mp-alert warning flex-shrink-0">
            <div style="font-size:13px;font-weight:600;margin-bottom:4px;">Status asisten praktikum Anda belum aktif.</div>
            <div style="font-size:12px;">Hubungi koordinator untuk mengaktifkan akun asisten praktikum Anda.</div>
        </div>
    @else

        {{-- Section: Ringkasan --}}


        {{-- Stat Cards --}}
        <div class="mp-stats-grid cols-4">
            <div class="mp-stat"
                style="border:1px solid var(--c-border, #DFE1E7); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                    <div class="mp-stat-icon navy">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                        </svg>
                    </div>
                    <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg, #0D0D12); margin:0;">Modul Diampu
                    </div>
                </div>
                <div class="mp-stat-value" style="font-size:32px;">{{ $totalModul ?? 0 }}</div>
                <div class="mp-stat-sub" style="font-size:13px;">modul dikelola</div>
            </div>

            <div class="mp-stat"
                style="border:1px solid var(--c-border, #DFE1E7); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                    <div class="mp-stat-icon sky">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8zM14 2v6h6M16 13H8M16 17H8M10 9H8" />
                        </svg>
                    </div>
                    <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg, #0D0D12); margin:0;">Total Materi
                    </div>
                </div>
                <div class="mp-stat-value" style="font-size:32px;">{{ $totalMateri ?? 0 }}</div>
                <div class="mp-stat-sub" style="font-size:13px;">materi diunggah</div>
            </div>

            <div class="mp-stat"
                style="border:1px solid var(--c-border, #DFE1E7); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                    <div class="mp-stat-icon green">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 11l3 3L22 4" />
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
                        </svg>
                    </div>
                    <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg, #0D0D12); margin:0;">Pengumpulan
                        Tugas</div>
                </div>
                @php
                    $totalTugasDibuat = isset($modulDiampu) ? $modulDiampu->sum(fn($m) => $m->tugas ? $m->tugas->count() : 0) : 0;
                @endphp
                <div class="mp-stat-value" style="font-size:32px;">{{ $totalTugasDibuat }}</div>
                <div class="mp-stat-sub" style="font-size:13px;">pengumpulan tugas yang sudah dibuat</div>
            </div>

            <div class="mp-stat"
                style="border:1px solid var(--c-border, #DFE1E7); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                    <div class="mp-stat-icon yellow">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg>
                    </div>
                    <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg, #0D0D12); margin:0;">Tugas Perlu
                        Dinilai</div>
                </div>
                <div class="mp-stat-value" style="font-size:32px;">{{ $tugasPendingNilai ?? 0 }}</div>
                <div class="mp-stat-sub" style="font-size:13px;">pengumpulan masuk</div>
            </div>
        </div>

        {{-- Section: Modul Diampu Header Removed --}}

        @if(!isset($modulDiampu) || $modulDiampu->isEmpty())
            <div class="mp-card flex-shrink-0"
                style="background:#fff; border:1px solid var(--c-border, #DFE1E7); border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); margin-bottom:24px;">
                <div style="padding:40px;text-align:center;">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                        stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg>
                    <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada modul yang diampu.</div>
                    <div style="font-size:12px;color:#808897;margin-top:4px;">Hubungi koordinator untuk pendistribusian modul.
                    </div>
                </div>
            </div>
        @else
            <div style="display:flex; flex-direction:column; margin-bottom:24px;">
                @php
                    $cbCheck = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" style="flex-shrink:0; margin-right:8px;"><rect width="18" height="18" rx="4" fill="#293C79" /><path d="M12.5 6L7 11.5L5 9" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>';
                    $cbUncheck = '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" style="flex-shrink:0; margin-right:8px;"><rect x="1" y="1" width="16" height="16" rx="4" stroke="#DFE1E7" stroke-width="2" fill="white" /></svg>';
                @endphp
                @foreach($modulDiampu as $m)
                    <div
                        style="border:1px solid #DFE1E7; background:#fff; border-radius:14px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); margin-bottom: 24px;">

                        {{-- Optional: Modul Title --}}
                        <div style="padding:12px 16px; border-bottom:1px solid #DFE1E7;">
                            <div style="font-weight:700; font-size:14px; color:#0D0D12; margin-bottom: 2px;">
                                {{ $m->nama ?? 'Modul' }}
                            </div>
                            <div style="font-size:13px; color:#353849;">
                                Asisten:
                                {{ $m->asprak && $m->asprak->count() ? $m->asprak->map(fn($a) => $a->user->name ?? '—')->implode(', ') : (auth()->user()->name ?? '—') }}
                            </div>
                        </div>

                        <div style="display:grid; grid-template-columns: repeat(3, 1fr);">

                            {{-- Column 1: Modul --}}
                            <div style="padding:16px; border-right:1px solid #DFE1E7;">
                                <div style="font-size:14px; font-weight:700; margin-bottom:16px; color:#0D0D12;">Modul</div>
                                @php
                                    $modulUnggah = $m->materi && $m->materi->count() > 0;
                                @endphp
                                <div style="display:flex; align-items:center;">
                                    {!! $modulUnggah ? $cbCheck : $cbUncheck !!}
                                    <span style="font-size:13px; color:#353849;">Modul Unggah</span>
                                </div>
                            </div>

                            {{-- Column 2: Tugas --}}
                            <div style="padding:16px; border-right:1px solid #DFE1E7;">
                                <div style="font-size:14px; font-weight:700; margin-bottom:16px; color:#0D0D12;">Tugas</div>
                                @php
                                    $tp = $m->tugas->firstWhere('jenis_tugas', 'tugas_pendahuluan');
                                    $laporan = $m->tugas->firstWhere('jenis_tugas', 'laporan');
                                    $responsi = $m->tugas->firstWhere('jenis_tugas', 'responsi');
                                    $pengganti = $m->tugas->firstWhere('jenis_tugas', 'tugas_pengganti') ?? $m->tugas->firstWhere('jenis_tugas', 'pengganti');
                                @endphp
                                <div style="display:flex; flex-direction:column; gap:12px;">
                                    <div style="display:flex; align-items:center;">
                                        {!! $tp ? $cbCheck : $cbUncheck !!}
                                        <span style="font-size:13px; color:#353849;">Unggah Form Tugas Pendahuluan</span>
                                    </div>
                                    <div style="display:flex; align-items:center;">
                                        {!! $laporan ? $cbCheck : $cbUncheck !!}
                                        <span style="font-size:13px; color:#353849;">Unggah Form Laporan</span>
                                    </div>
                                    <div style="display:flex; align-items:center;">
                                        {!! $responsi ? $cbCheck : $cbUncheck !!}
                                        <span style="font-size:13px; color:#353849;">Unggah Form Responsi</span>
                                    </div>
                                    <div style="display:flex; align-items:center;">
                                        {!! $pengganti ? $cbCheck : $cbUncheck !!}
                                        <span style="font-size:13px; color:#353849;">Unggah Form Tugas Pengganti</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Column 3: Beri Nilai --}}
                            <div style="padding:16px;">
                                <div style="font-size:14px; font-weight:700; margin-bottom:16px; color:#0D0D12;">Beri Nilai
                                </div>
                                @php
                                    $isDinilai = function ($t) {
                                        if (!$t)
                                            return false;
                                        $peng = $t->pengumpulan ?? collect();
                                        if ($peng->isEmpty())
                                            return false;
                                        // Terceklis apabila semua nilai sudah dimasukkan (tidak ada yang belum_dicek)
                                        return $peng->where('status_pengumpulan', 'belum_dicek')->isEmpty();
                                    };
                                @endphp
                                <div style="display:flex; flex-direction:column; gap:12px;">
                                    <div style="display:flex; align-items:center;">
                                        {!! $isDinilai($tp) ? $cbCheck : $cbUncheck !!}
                                        <span style="font-size:13px; color:#353849;">Nilai Tugas Pendahuluan</span>
                                    </div>
                                    <div style="display:flex; align-items:center;">
                                        {!! $isDinilai($laporan) ? $cbCheck : $cbUncheck !!}
                                        <span style="font-size:13px; color:#353849;">Nilai Laporan</span>
                                    </div>
                                    <div style="display:flex; align-items:center;">
                                        {!! $isDinilai($responsi) ? $cbCheck : $cbUncheck !!}
                                        <span style="font-size:13px; color:#353849;">Nilai Responsi</span>
                                    </div>
                                    <div style="display:flex; align-items:center;">
                                        {!! $isDinilai($pengganti) ? $cbCheck : $cbUncheck !!}
                                        <span style="font-size:13px; color:#353849;">Nilai Tugas Pengganti</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    @endif

</x-eoffice::manajemen-praktikum.layout>