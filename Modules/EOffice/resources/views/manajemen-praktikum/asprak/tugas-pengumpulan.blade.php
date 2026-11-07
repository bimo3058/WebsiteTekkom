@php
    $jenisTugasMap = [
        'tugas_pendahuluan' => 'Tugas Pendahuluan',
        'laporan' => 'Laporan',
        'responsi' => 'Responsi',
        'tugas_pengganti' => 'Tugas Pengganti'
    ];
    $jenisLabel = $tugas->jenisLabel();
@endphp
<x-eoffice::manajemen-praktikum.layout :breadcrumbPraktikum="$praktikum ?? $tugas->modul?->praktikum ?? null" :pageTitle="($tugas->modul?->praktikum?->nama ?? 'Praktikum') . ' / ' . $jenisLabel . ' / Pengumpulan Tugas'">

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    {{-- Header --}}
    <x-slot name="header">
    <div class="mp-page-header" style="margin:0">
        <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <h1 class="mp-page-title">{{ $tugas->judul }}</h1>
            </div>
            <p class="mp-page-sub">{{ $tugas->modul?->nama }} &ndash; {{ $tugas->modul?->praktikum?->nama }}</p>
        </div>
        <div class="mp-page-actions">
            <a href="{{ route('eoffice.manprak.asprak.tugas.index') }}" class="mp-btn secondary md"
                style="text-decoration:none;">Kembali</a>
        </div>
    </div>

    </x-slot>

    {{-- Info Section (Stat Cards) --}}
    <div class="mp-stats-grid cols-4" style="margin-bottom:24px;">
        @php
            $totalMhs = $totalPraktikan ?? (method_exists($praktikans, 'total') ? $praktikans->total() : $praktikans->count());

            $sudahDikumpul = $pengumpulan->count();
            $menungguPenilaian = $pengumpulan->where('status_pengumpulan', '!=', 'acc')->where('status_pengumpulan', '!=', 'revisi')->count();
            $sudahDinilai = $pengumpulan->where('nilai', '!=', null)->count();
            $belumMengumpulkan = $totalMhs - $sudahDikumpul;
        @endphp

        {{-- 1. Sudah Dikumpul --}}
        <div class="mp-stat"
            style="border:1px solid var(--c-border, #DFE1E7); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                <div class="mp-stat-icon green">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
                <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg, #0D0D12); margin:0;">Sudah Dikumpul
                </div>
            </div>
            <div class="mp-stat-value" style="font-size:32px;">{{ $sudahDikumpul }}</div>
            <div class="mp-stat-sub" style="font-size:13px; color:var(--c-fg-muted, #666D80);">dari {{ $totalMhs }}
                mahasiswa</div>
        </div>

        {{-- 2. Menunggu Penilaian --}}
        <div class="mp-stat"
            style="border:1px solid var(--c-border, #DFE1E7); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                <div class="mp-stat-icon yellow">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg, #0D0D12); margin:0;">Menunggu
                    Penilaian</div>
            </div>
            <div class="mp-stat-value" style="font-size:32px;">{{ $menungguPenilaian }}</div>
            <div class="mp-stat-sub" style="font-size:13px; color:var(--c-fg-muted, #666D80);">dari {{ $totalMhs }}
                mahasiswa</div>
        </div>

        {{-- 3. Sudah Dinilai --}}
        <div class="mp-stat"
            style="border:1px solid var(--c-border, #DFE1E7); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                <div class="mp-stat-icon sky">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
                <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg, #0D0D12); margin:0;">Sudah Dinilai
                </div>
            </div>
            <div class="mp-stat-value" style="font-size:32px;">{{ $sudahDinilai }}</div>
            <div class="mp-stat-sub" style="font-size:13px; color:var(--c-fg-muted, #666D80);">dari {{ $totalMhs }}
                mahasiswa</div>
        </div>

        {{-- 4. Belum Mengumpulkan --}}
        <div class="mp-stat"
            style="border:1px solid var(--c-border, #DFE1E7); border-radius:14px; box-shadow:0 1px 3px rgba(0,0,0,.04);">
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:12px;">
                <div class="mp-stat-icon"
                    style="background:var(--c-bg-subtle, #F3F4F6); color:var(--c-fg-muted, #666D80);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                    </svg>
                </div>
                <div class="mp-stat-label" style="font-size:14px; color:var(--c-fg, #0D0D12); margin:0;">Belum
                    Mengumpulkan</div>
            </div>
            <div class="mp-stat-value" style="font-size:32px;">{{ $belumMengumpulkan }}</div>
            <div class="mp-stat-sub" style="font-size:13px; color:var(--c-fg-muted, #666D80);">dari {{ $totalMhs }}
                mahasiswa</div>
        </div>
    </div>


        <div class="mp-card" style="border:1px solid #DFE1E7;border-radius:12px;overflow:hidden" x-data="{changed:{},get hasChanges(){return Object.values(this.changed).some(Boolean)}}">
            <div class="mp-card-header" style="padding:16px 20px;border-bottom:1px solid #DFE1E7;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
                <span class="mp-card-title">Pengumpulan Tugas</span>
                <form method="GET" action="{{ url()->current() }}" x-ref="submissionFilters" x-data="{globalSearch:{{ json_encode(request('search','')) }},globalKelompok:{{ json_encode(request('kelompok','')) }},globalShift:{{ json_encode(request('shift','')) }},timer:null,apply(){clearTimeout(this.timer);this.timer=setTimeout(()=>this.$refs.submissionFilters.requestSubmit(),400)}}" x-init="$watch('globalSearch',()=>apply());$watch('globalKelompok',()=>apply());$watch('globalShift',()=>apply())" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-left:auto">
                    <div style="position:relative"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg><input type="search" name="search" x-model="globalSearch" placeholder="Cari nama / NIM..." aria-label="Cari nama atau NIM" class="mp-input" style="padding-left:32px;height:36px;width:210px;font-size:13px"></div>
                    <input type="hidden" name="kelompok" :value="globalKelompok"><input type="hidden" name="shift" :value="globalShift"><input type="hidden" name="per_page" value="{{ request('per_page',10) }}">
                    <x-eoffice::manajemen-praktikum.ui.grade-group-filter model="globalKelompok" label="Semua Kelompok" searchPlaceholder="Cari kelompok..." />
                    <x-eoffice::manajemen-praktikum.ui.grade-group-filter model="globalShift" label="Semua Shift" searchPlaceholder="Cari shift..." />
                    <button type="submit" hidden>Terapkan Filter</button>
                </form>
                <form id="moduleSubmissionGrades" method="POST" action="{{ route('eoffice.manprak.asprak.tugas.nilai-jenis',$tugas->id) }}" @submit="if(!hasChanges) $event.preventDefault()">
                    @csrf
                    <button type="submit" class="mp-btn primary md" style="height:36px;min-width:100px;padding:0 12px;border-radius:8px;font-size:13px;font-weight:700" disabled :disabled="!hasChanges || {{ !$tugas->modul?->praktikum?->is_active ? 'true':'false' }}" :style="{cursor:hasChanges ? 'pointer' : 'not-allowed',background:hasChanges ? '#0B266E' : '#F4F6F8',color:hasChanges ? '#fff' : '#0B266E',border:hasChanges ? '1px solid #0B266E' : '1px solid #DFE1E7',pointerEvents:'auto'}">Simpan</button>
                </form>
            </div>

            @php
                $shiftRowspan = [];
                $kelompokRowspan = [];

                $items = method_exists($praktikans, 'items') ? $praktikans->items() : $praktikans->all();
                $totalItems = count($items);

                // Calculate rowspan for shift
                $i = 0;
                while ($i < $totalItems) {
                    $val = $items[$i]->shift;
                    if (empty($val)) {
                        $shiftRowspan[$i] = 1;
                        $i++;
                        continue;
                    }
                    $count = 1;
                    while ($i + $count < $totalItems && $items[$i + $count]->shift === $val) {
                        $count++;
                    }
                    $shiftRowspan[$i] = $count;
                    for ($j = 1; $j < $count; $j++) {
                        $shiftRowspan[$i + $j] = 0;
                    }
                    $i += $count;
                }

                // Calculate rowspan for kelompok (must match same kelompok AND same shift)
                $i = 0;
                while ($i < $totalItems) {
                    $valK = $items[$i]->kelompok;
                    $valS = $items[$i]->shift;
                    if (empty($valK)) {
                        $kelompokRowspan[$i] = 1;
                        $i++;
                        continue;
                    }
                    $count = 1;
                    while (
                        $i + $count < $totalItems &&
                        $items[$i + $count]->kelompok === $valK &&
                        $items[$i + $count]->shift === $valS
                    ) {
                        $count++;
                    }
                    $kelompokRowspan[$i] = $count;
                    for ($j = 1; $j < $count; $j++) {
                        $kelompokRowspan[$i + $j] = 0;
                    }
                    $i += $count;
                }
            @endphp

            <div style="overflow-x:auto;">
                <table class="mp-table"
                    style="width:100%;border-collapse:collapse;min-width:1400px;font-size:13px;text-align:left;">
                    <thead>
                        <tr style="background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
                            <th class="mp-th text-center" style="padding:12px 16px;width:50px;text-align:center;">NO</th>
                            <th class="mp-th text-left" style="padding:12px 16px;width:250px;">NAMA MAHASISWA</th>
                            <th class="mp-th text-left" style="padding:12px 16px;width:150px;">NIM</th>
                            <th class="mp-th text-center"
                                style="padding:12px 16px;width:100px;">
                                KELOMPOK</th>
                            <th class="mp-th text-center"
                                style="padding:12px 16px;width:100px;">SHIFT</th>
                            <th class="mp-th text-left" style="padding:12px 16px;width:150px;">DOKUMEN</th>
                            <th class="mp-th text-left" style="padding:12px 16px;width:140px;">WAKTU PENGUMPULAN</th>
                            <th class="mp-th text-left" style="padding:12px 16px;width:110px;">NILAI</th>
                            <th class="mp-th text-left" style="padding:12px 16px;width:90px;">STATUS</th>
                            <th class="mp-th text-left" style="padding:12px 16px;width:90px;">REVISI</th>
                            <th class="mp-th text-left" style="padding:12px 16px;width:150px;">DOKUMEN REVISI</th>
                            <th class="mp-th text-left" style="padding:12px 16px;width:140px;">WAKTU PENGUMPULAN REVISI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($praktikans as $idx => $pr)
                                @php
                                    $p = $pengumpulan[$pr->id] ?? null;
                                    $st = $p ? ($p->status_pengumpulan ?? 'belum_dicek') : 'belum_kumpul';
                                    $waktuSubmit = null;
                                    $latestRevision = null;
                                    $firstSub = null;
                                    if ($p) {
                                        $firstSub = $p->riwayat->where('is_revision', false)->sortByDesc('created_at')->first() ?? $p->riwayat->sortByDesc('created_at')->first();
                                        $waktuSubmit = $firstSub ? $firstSub->created_at : $p->created_at;
                                        $latestRevision = $p->riwayat->where('is_revision', true)->sortByDesc('created_at')->first();
                                    }
                                    $submittedFiles=($firstSub && !empty($firstSub->files)) ? $firstSub->files : ($p?->files ?? []);
                                    $noUrut = method_exists($praktikans, 'currentPage') ? (($praktikans->currentPage() - 1) * $praktikans->perPage()) + $idx + 1 : $idx + 1;
                                @endphp
                            <tr style="border-bottom:1px solid #EEF0F5;transition:background .1s;"
                                onmouseover="this.style.background='#FAFBFC'" onmouseout="this.style.background=''">
                                <td style="padding:12px 16px;vertical-align:middle;color:#666D80;font-size:13px;text-align:center;font-weight:700;">{{ $noUrut }}
                                </td>

                                {{-- Mahasiswa --}}
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                                        <div class="mp-av sky" style="width:28px;height:28px;font-size:11px;flex-shrink:0;">
                                            {{ strtoupper(substr($pr->user?->name ?? 'M', 0, 2)) }}</div>
                                        <div style="min-width:0;">
                                            <div style="font-size:13px;font-weight:600;color:#0D0D12;">
                                                {{ $pr->user?->name ?? '—' }}</div>
                                            <div style="font-size:11.5px;color:#666D80;margin-top:2px;">{{ $pr->user?->email }}</div>
                                            
                                        </div>
                                    </div>
                                </td>

                                {{-- NIM --}}
                                <td
                                    style="padding:12px 16px;vertical-align:middle;font-size:12px;font-weight:600;color:#0D0D12;">
                                    {{ $pr->user?->student?->student_number ?? '—' }}
                                </td>

                                {{-- KELOMPOK COLUMN with Dynamic Rowspan --}}
                                @if($kelompokRowspan[$idx] > 0)
                                    <td rowspan="{{ $kelompokRowspan[$idx] }}"
                                        style="padding:12px 16px;text-align:center;vertical-align:middle;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;background:#FFF;font-size:15px;font-weight:700;color:#0D0D12;">
                                        @if($pr->kelompok)
                                            {{ $pr->kelompok }}
                                        @else
                                            <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">&ndash;</span>
                                        @endif
                                    </td>
                                @endif

                                {{-- SHIFT COLUMN with Dynamic Rowspan --}}
                                @if($shiftRowspan[$idx] > 0)
                                    <td rowspan="{{ $shiftRowspan[$idx] }}"
                                        style="padding:12px 16px;text-align:center;vertical-align:middle;border-right:1px solid #DFE1E7;background:#FFF;font-size:15px;font-weight:700;color:#0D0D12;">
                                        @if($pr->shift)
                                            {{ $pr->shift }}
                                        @else
                                            <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">&ndash;</span>
                                        @endif
                                    </td>
                                @endif

                                {{-- Dokumen --}}
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    @if($p && !empty($submittedFiles))
                                        <div style="display:flex;flex-direction:column;gap:4px;">
                                            @foreach($submittedFiles as $fPathObj)
                                                @php 
                                                    $fPath = is_array($fPathObj) && isset($fPathObj['path']) ? $fPathObj['path'] : $fPathObj;
                                                    $fName = is_array($fPathObj) && isset($fPathObj['original_name']) ? $fPathObj['original_name'] : pathinfo($fPath, PATHINFO_BASENAME);
                                                @endphp
                                                <a href="{{ app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumAttachments::class)->publicUrl($fPath, 'eoffice', $fName) }}"
                                                    target="_blank" rel="noopener noreferrer"
                                                    style="display:flex; align-items:center; padding:6px 10px; border:1px solid #DFE1E7; border-radius:6px; background:#fff; text-decoration:none; transition:border-color 0.2s; margin-bottom:4px;"
                                                    onmouseover="this.style.borderColor='#0B266E'" onmouseout="this.style.borderColor='#DFE1E7'"
                                                    title="{{ $fName }}">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-right:8px;">
                                                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                        <polyline points="13 2 13 9 20 9"></polyline>
                                                    </svg>
                                                    <span style="font-size:11px; font-weight:500; color:#353849; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width: 120px;">{{ $fName }}</span>
                                                </a>
                                            @endforeach
                                            
                                            
                                        </div>
                                    @else
                                        <span style="font-size:12px;color:#A4ABB8;">Belum mengumpulkan</span>
                                    @endif
                                </td>

                                {{-- Waktu Submit --}}
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    @if($waktuSubmit)
                                        <span style="color:#0F6E56;font-weight:600;font-size:12px;">
                                            {{ \Carbon\Carbon::parse($waktuSubmit)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}
                                            WIB
                                        </span>
                                    @else
                                        <span style="font-size:12px;color:#999;">&ndash;</span>
                                    @endif
                                </td>

                                {{-- Nilai --}}
                                <td class="mp-grade-history-cell" style="position:relative;padding-right:32px!important;padding:12px 16px;vertical-align:middle;">
                                    @php
                                        $njValue = $nilaiJenis[$pr->id]?->nilai ?? null;
                                        $currentNilai = $p ? $p->nilai : null;
                                        $displayNilai = $currentNilai ?? $njValue ?? '';
                                    @endphp

                                    @if($tugas->modul?->praktikum?->is_active)
                                    <div x-data="{score:{{ json_encode(old('nilai.'.$pr->id.'.nilai_jenis',(string)$displayNilai)) }},initial:{{ json_encode((string)$displayNilai) }},get dirty(){return (this.score==='' || this.score===null)!==(this.initial==='') || (this.score!=='' && this.score!==null && Number(this.score)!==Number(this.initial))}}" x-effect="changed[{{ json_encode((string)$pr->id) }}]=dirty">
                                        <x-eoffice::manajemen-praktikum.ui.grade-score-input :integer="true" model="score" :label="'Nilai '.$pr->user?->name" form="moduleSubmissionGrades" />
                                        <input type="hidden" form="moduleSubmissionGrades" name="nilai[{{ $pr->id }}][nilai_jenis]" :value="score" :disabled="!dirty">
                                    </div>
                                    @else
                                        <span style="font-size:12px;font-weight:600">{{ $displayNilai!==''?str_replace('.',',',(string)$displayNilai):'—' }}</span>
                                    @endif
                                <x-eoffice::manajemen-praktikum.ui.module-score-history :module-id="$tugas->modul_id" :student-id="$pr->id" :score-key="$tugas->jenis_tugas" :score-label="$tugas->judul ?? 'Nilai'" :student-name="$pr->user?->name ?? 'Mahasiswa'" /></td>

                                {{-- Status --}}
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    @if($st === 'acc')
                                        <span class="mp-badge success sm" style="padding:2px 6px;font-size:10px;"><span
                                                class="dot"></span>ACC</span>
                                    @elseif($st === 'revisi')
                                        <span class="mp-badge error sm" style="padding:2px 6px;font-size:10px;"><span
                                                class="dot"></span>Revisi</span>
                                    @elseif($st === 'belum_kumpul')
                                        <span class="mp-badge neutral sm" style="padding:2px 6px;font-size:10px;"><span
                                                class="dot"></span>Belum Kumpul</span>
                                    @else
                                        <span class="mp-badge warning sm" style="padding:2px 6px;font-size:10px;"><span
                                                class="dot"></span>Menunggu</span>
                                    @endif
                                </td>

                                {{-- Kirim Revisi --}}
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    @if($st === 'acc' || $st === 'belum_kumpul')
                                        <span style="font-size:12px;color:#999;">&ndash;</span>
                                    @else
                                        <div style="display:flex;flex-direction:column;gap:6px;align-items:stretch;">
                                            @if(isset($tugas->modul->praktikum) && $tugas->modul->praktikum->is_active)
                                                @include('eoffice::manajemen-praktikum.asprak.module-revision-modal')
                                            @endif

                                            @if($p && $p->file_revisi_asprak)
                                                <div style="display:flex;flex-direction:column;gap:4px;">
                                                    @php 
                                                        $revFiles = json_decode($p->file_revisi_asprak, true);
                                                    @endphp
                                                    @if(is_array($revFiles))
                                                        @foreach($revFiles as $rf)
                                                            @php 
                                                                $rPath = is_array($rf) ? ($rf['path'] ?? null) : $rf;
                                                                $rName = is_array($rf) ? ($rf['original_name'] ?? basename($rPath)) : basename($rPath);
                                                            @endphp
                                                            @if($rPath)
                                                                <a href="{{ app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumAttachments::class)->publicUrl($rPath, 'eoffice', $rName) }}"
                                                                    target="_blank" rel="noopener noreferrer"
                                                                    style="display:flex; align-items:center; padding:6px 10px; border:1px solid #DFE1E7; border-radius:6px; background:#fff; text-decoration:none; transition:border-color 0.2s; margin-bottom:4px;"
                                                                    onmouseover="this.style.borderColor='#0B266E'" onmouseout="this.style.borderColor='#DFE1E7'"
                                                                    title="{{ $rName }}">
                                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-right:8px;">
                                                                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                                        <polyline points="13 2 13 9 20 9"></polyline>
                                                                    </svg>
                                                                    <span style="font-size:11px; font-weight:500; color:#353849; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width: 120px;">{{ $rName }}</span>
                                                                </a>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                {{-- Dokumen Revisi --}}
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    @if($latestRevision && !empty($latestRevision->files))
                                        <div style="display:flex;flex-direction:column;gap:4px;">
                                            @foreach($latestRevision->files as $fPathObj)
                                                @php 
                                                    $fPath = is_array($fPathObj) && isset($fPathObj['path']) ? $fPathObj['path'] : $fPathObj;
                                                    $fName = is_array($fPathObj) && isset($fPathObj['original_name']) ? $fPathObj['original_name'] : pathinfo($fPath, PATHINFO_BASENAME);
                                                @endphp
                                                <a href="{{ app(\Modules\EOffice\Services\ManajemenPraktikum\PracticumAttachments::class)->publicUrl($fPath, 'eoffice', $fName) }}"
                                                    target="_blank" rel="noopener noreferrer"
                                                    style="display:flex; align-items:center; padding:6px 10px; border:1px solid #DFE1E7; border-radius:6px; background:#fff; text-decoration:none; transition:border-color 0.2s; margin-bottom:4px;"
                                                    onmouseover="this.style.borderColor='#0F6E56'" onmouseout="this.style.borderColor='#DFE1E7'"
                                                    title="{{ $fName }}">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-right:8px;">
                                                        <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                                                        <polyline points="13 2 13 9 20 9"></polyline>
                                                    </svg>
                                                    <span style="font-size:11px; font-weight:500; color:#353849; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width: 120px;">{{ $fName }}</span>
                                                </a>
                                            @endforeach
                                            
                                        </div>
                                    @else
                                        <span style="font-size:12px;color:#999;">&ndash;</span>
                                    @endif
                                </td>

                                {{-- Waktu Submit Revisi --}}
                                <td style="padding:12px 16px;vertical-align:middle;">
                                    @if($latestRevision)
                                        <span style="color:#0F6E56;font-weight:600;font-size:12px;">
                                            {{ \Carbon\Carbon::parse($latestRevision->created_at)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}
                                            WIB
                                        </span>
                                    @else
                                        <span style="font-size:12px;color:#999;">&ndash;</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" style="padding:48px;text-align:center;color:#666D80;">
                                    {{ filled(request('search')) || filled(request('kelompok')) || filled(request('shift')) ? 'Data tidak ditemukan.' : 'Belum ada praktikan terdaftar.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(isset($praktikans) && $praktikans instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <x-eoffice::manajemen-praktikum.ui.pagination :paginator="$praktikans" :options="[10, 20, 30]" />
            @endif
        </div>


</x-eoffice::manajemen-praktikum.layout>
