@php
    $jenisTugasMap = [
        'tugas_pendahuluan' => 'Tugas Pendahuluan',
        'laporan' => 'Laporan',
        'responsi' => 'Responsi',
        'tugas_pengganti' => 'Tugas Pengganti'
    ];
    $jenisLabel = $jenisTugasMap[$tugas->jenis_tugas] ?? ucwords(str_replace('_', ' ', $tugas->jenis_tugas));
    $praktikum = $tugas->modul?->praktikum;
@endphp
<x-eoffice::manajemen-praktikum.layout :pageTitle="($praktikum?->nama ?? 'Praktikum') . ' / ' . $jenisLabel . ' / Pengumpulan Tugas'">

    {{-- Header --}}
    <div class="mp-page-header">
        <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <h1 class="mp-page-title">{{ $tugas->judul }}</h1>
            </div>
            <p class="mp-page-sub">{{ $tugas->modul?->nama }} &ndash; {{ $praktikum?->nama }}</p>
        </div>
        <div class="mp-page-actions">
            <a href="{{ route('eoffice.manprak.dosen.tugas.index', ['praktikum_id' => $praktikum?->id]) }}" class="mp-btn secondary md"
                style="text-decoration:none;">Kembali</a>
        </div>
    </div>

    {{-- Info Section (Stat Cards) --}}
    <div class="mp-stats-grid cols-4" style="margin-bottom:24px;">
        @php
            $totalMhs = method_exists($praktikans, 'total') ? $praktikans->total() : $praktikans->count();

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

    @if($praktikans->count() > 0)
        <div class="mp-card">
            <div class="mp-card-header">
                <span class="mp-card-title">Pengumpulan Tugas</span>
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
                    style="width:100%;border-collapse:collapse;min-width:1250px;font-size:13px;text-align:left;">
                    <thead>
                        <tr style="background:#F9FAFB;border-bottom:1px solid #DFE1E7;">
                            <th class="mp-th text-left" style="padding:14px 16px;width:40px;">NO.</th>
                            <th class="mp-th text-left" style="padding:14px 16px;width:200px;">Mahasiswa</th>
                            <th class="mp-th text-left" style="padding:14px 16px;width:120px;">NIM</th>
                            <th class="mp-th text-center"
                                style="padding:14px 16px;width:100px;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;">
                                Kelompok</th>
                            <th class="mp-th text-center"
                                style="padding:14px 16px;width:100px;border-right:1px solid #DFE1E7;">Shift</th>
                            <th class="mp-th text-left" style="padding:14px 16px;width:150px;">Dokumen</th>
                            <th class="mp-th text-left" style="padding:14px 16px;width:140px;">Waktu Submit</th>
                            <th class="mp-th text-left" style="padding:14px 16px;width:110px;">Nilai</th>
                            <th class="mp-th text-left" style="padding:14px 16px;width:90px;">Status</th>
                            <th class="mp-th text-left" style="padding:14px 16px;width:90px;">Kirim Revisi</th>
                            <th class="mp-th text-left" style="padding:14px 16px;width:150px;">Dokumen Revisi</th>
                            <th class="mp-th text-left" style="padding:14px 16px;width:140px;">Waktu Submit Revisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($praktikans as $idx => $pr)
                            @php
                                $p = $pengumpulan[$pr->id] ?? null;
                                $st = $p ? ($p->status_pengumpulan ?? 'belum_dicek') : 'belum_kumpul';
                                $waktuSubmit = null;
                                $latestRevision = null;
                                if ($p) {
                                    $firstSub = $p->riwayat->where('is_revision', false)->sortBy('created_at')->first() ?? $p->riwayat->sortBy('created_at')->first();
                                    $waktuSubmit = $firstSub ? $firstSub->created_at : $p->created_at;
                                    $latestRevision = $p->riwayat->where('is_revision', true)->sortByDesc('created_at')->first();
                                }
                                $noUrut = method_exists($praktikans, 'currentPage') ? (($praktikans->currentPage() - 1) * $praktikans->perPage()) + $idx + 1 : $idx + 1;
                            @endphp
                            <tr style="border-bottom:1px solid #EEF0F5;transition:background .1s;"
                                onmouseover="this.style.background='#FAFBFC'" onmouseout="this.style.background=''">
                                <td style="padding:14px 16px;vertical-align:middle;color:#808897;font-size:12px;">{{ $noUrut }}
                                </td>

                                {{-- Mahasiswa --}}
                                <td style="padding:14px 16px;vertical-align:middle;">
                                    <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                                        <div class="mp-av yellow" style="width:34px;height:34px;flex-shrink:0;">
                                            {{ strtoupper(substr($pr->user?->name ?? 'M', 0, 2)) }}</div>
                                        <div style="min-width:0;">
                                            <div style="font-size:13px;font-weight:600;color:#0D0D12;">
                                                {!! $pr->user?->name ?? '&ndash;' !!}</div>
                                            <div style="font-size:11px;color:#666D80;">{{ $pr->user?->email }}</div>
                                            
                                        </div>
                                    </div>
                                </td>

                                {{-- NIM --}}
                                <td
                                    style="padding:14px 16px;vertical-align:middle;font-size:12px;font-weight:600;color:#353849;">
                                    {!! $pr->user?->student?->student_number ?? '&ndash;' !!}
                                </td>

                                {{-- KELOMPOK COLUMN with Dynamic Rowspan --}}
                                @if($kelompokRowspan[$idx] > 0)
                                    <td rowspan="{{ $kelompokRowspan[$idx] }}"
                                        style="padding:16px;text-align:center;vertical-align:middle;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;background:#FFF;font-size:18px;font-weight:700;color:#0D0D12;">
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
                                        style="padding:16px;text-align:center;vertical-align:middle;border-right:1px solid #DFE1E7;background:#FFF;font-size:18px;font-weight:700;color:#0D0D12;">
                                        @if($pr->shift)
                                            {{ $pr->shift }}
                                        @else
                                            <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">&ndash;</span>
                                        @endif
                                    </td>
                                @endif

                                {{-- Dokumen --}}
                                <td style="padding:14px 16px;vertical-align:middle;">
                                    @if($p && $firstSub && !empty($firstSub->files))
                                        <div style="display:flex;flex-direction:column;gap:4px;">
                                            @foreach($firstSub->files as $fPathObj)
                                                @php 
                                                    $fPath = is_array($fPathObj) && isset($fPathObj['path']) ? $fPathObj['path'] : $fPathObj;
                                                    $fName = is_array($fPathObj) && isset($fPathObj['original_name']) ? $fPathObj['original_name'] : pathinfo($fPath, PATHINFO_BASENAME);
                                                @endphp
                                                <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($fPath, 'eoffice') }}"
                                                    target="_blank"
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
                                    @elseif($p && $p->file_path)
                                        <div style="display:flex;flex-direction:column;gap:4px;">
                                            <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($p->file_path, 'eoffice') }}"
                                                target="_blank"
                                                style="font-size:12px;font-weight:600;color:#0B266E;text-decoration:none;"
                                                title="{{ basename($p->file_path) }}">
                                                {{ Str::limit(basename($p->file_path), 15) }}
                                            </a>
                                        </div>
                                    @else
                                        <span style="font-size:12px;color:#A4ABB8;">Belum mengumpulkan</span>
                                    @endif
                                </td>

                                {{-- Waktu Submit --}}
                                <td style="padding:14px 16px;vertical-align:middle;">
                                    @if($waktuSubmit)
                                        <span style="color:#0F6E56;font-weight:600;font-size:12px;">
                                            {{ \Carbon\Carbon::parse($waktuSubmit)->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}
                                            WIB
                                        </span>
                                    @else
                                        <span style="font-size:12px;color:#999;">&ndash;</span>
                                    @endif
                                </td>

                                {{-- Nilai (READ ONLY - Dosen hanya melihat) --}}
                                <td style="padding:14px 16px;vertical-align:middle;">
                                    @php
                                        $displayNilai = $p ? $p->nilai : null;
                                    @endphp
                                    <span style="font-size:12px;font-weight:600;color:#111827;">
                                        {!! $displayNilai !== null ? $displayNilai : '&ndash;' !!}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td style="padding:14px 16px;vertical-align:middle;">
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

                                {{-- Kirim Revisi (READ ONLY - Dosen hanya melihat info revisi) --}}
                                <td style="padding:14px 16px;vertical-align:middle;">
                                    @if($p && ($p->file_revisi_asprak || $p->catatan_revisi))
                                        <div style="padding:6px 8px;background:#FEF2F2;border:1px solid #FEE2E2;border-radius:6px;font-size:11px;width:100%;box-sizing:border-box;">
                                            @if($p->file_revisi_asprak)
                                                <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($p->file_revisi_asprak, 'eoffice') }}"
                                                    target="_blank"
                                                    style="font-weight:600;color:#95122B;text-decoration:none;display:block;word-break:break-all;"
                                                    title="{{ basename($p->file_revisi_asprak) }}">
                                                    &#128196; {{ Str::limit(basename($p->file_revisi_asprak), 12) }}
                                                </a>
                                            @endif
                                            @if($p->catatan_revisi)
                                                <div style="color:#7C1028;font-style:italic;margin-top:2px;word-break:break-word;"
                                                    title="{{ $p->catatan_revisi }}">&#128172;: {{ Str::limit($p->catatan_revisi, 25) }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <span style="font-size:12px;color:#999;">&ndash;</span>
                                    @endif
                                </td>

                                {{-- Dokumen Revisi --}}
                                <td style="padding:14px 16px;vertical-align:middle;">
                                    @if($latestRevision && !empty($latestRevision->files))
                                        <div style="display:flex;flex-direction:column;gap:4px;">
                                            @foreach($latestRevision->files as $fPathObj)
                                                @php 
                                                    $fPath = is_array($fPathObj) && isset($fPathObj['path']) ? $fPathObj['path'] : $fPathObj;
                                                    $fName = is_array($fPathObj) && isset($fPathObj['original_name']) ? $fPathObj['original_name'] : pathinfo($fPath, PATHINFO_BASENAME);
                                                @endphp
                                                <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl($fPath, 'eoffice') }}"
                                                    target="_blank"
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
                                <td style="padding:14px 16px;vertical-align:middle;">
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
                                    Belum ada praktikan terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($praktikans, 'total') && $praktikans->total() > 0)
                <div style="display:flex; justify-content:space-between; align-items:center; width:100%; padding:12px 20px; border-top:1px solid #DFE1E7; box-sizing:border-box; flex-wrap:nowrap; gap:16px;">
                    {{-- Left: Per halaman dropdown + info teks --}}
                    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                        <div x-data="{ openP: false, options: [10, 20, 30], perPage: {{ $praktikans->perPage() }} }"
                            class="relative" @click.away="openP = false">
                            <div class="flex items-center gap-2 cursor-pointer border rounded-[8px] px-3 py-1.5 transition-colors focus:outline-none"
                                :class="openP ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'"
                                @click="openP = !openP">
                                <span class="text-[12px] text-[#666D80]" :class="openP ? 'text-[#0B266E]' : ''">Per halaman</span>
                                <div class="flex items-center gap-1 font-semibold text-[12px]">
                                    <span x-text="perPage"></span>
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                        :class="{'rotate-180': openP, 'text-[#0B266E]': openP, 'text-[#666D80]': !openP}"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                            </div>
                            <div x-show="openP" @click.away="openP = false" style="display: none;"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                class="absolute bottom-full left-0 mb-2 z-10 w-full min-w-[80px] bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] py-2 px-1.5 flex flex-col">
                                <template x-for="option in options" :key="option">
                                    <a :href="`?${new URLSearchParams({...Object.fromEntries(new URLSearchParams(window.location.search)), per_page: option, page: 1}).toString()}`"
                                        class="flex items-center justify-between px-3 py-2 rounded-[6px] cursor-pointer text-[12px] transition-colors mb-0.5 last:mb-0 no-underline"
                                        :class="perPage == option ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                                        <span x-text="option"></span>
                                        <svg x-show="perPage == option" class="w-3.5 h-3.5 flex-shrink-0 text-[#0B266E] ml-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </a>
                                </template>
                            </div>
                        </div>
                        <div style="font-size:12px; color:#666D80;">
                            Menampilkan {{ $praktikans->firstItem() }} sampai {{ $praktikans->lastItem() }} dari {{ $praktikans->total() }} data
                        </div>
                    </div>

                    {{-- Right: pagination buttons --}}
                    <div style="display:flex; align-items:center; gap:8px;">
                        @if ($praktikans->onFirstPage())
                            <span style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:#F8FAFC; border:1px solid #DFE1E7; color:#A4ABB8; cursor:not-allowed;">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                            </span>
                        @else
                            <a href="{{ $praktikans->previousPageUrl() }}" style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:white; border:1px solid #DFE1E7; color:#353849; cursor:pointer; text-decoration:none;" class="hover:bg-gray-50">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="15 18 9 12 15 6"></polyline>
                                </svg>
                            </a>
                        @endif

                        <span style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:#0B266E; color:white; font-size:13px; font-weight:600;">
                            {{ $praktikans->currentPage() }}
                        </span>

                        @if ($praktikans->hasMorePages())
                            <a href="{{ $praktikans->nextPageUrl() }}" style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:white; border:1px solid #DFE1E7; color:#353849; cursor:pointer; text-decoration:none;" class="hover:bg-gray-50">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </a>
                        @else
                            <span style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:#F8FAFC; border:1px solid #DFE1E7; color:#A4ABB8; cursor:not-allowed;">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="mp-card" style="display:flex;align-items:center;justify-content:center;min-height:240px;padding:48px;">
            <div style="text-align:center;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                    stroke-linecap="round" style="margin:0 auto 16px;display:block;opacity:0.6;">
                    <path
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <div style="font-size:14px;font-weight:600;color:#666D80;">Belum ada praktikan terdaftar.</div>
            </div>
        </div>
    @endif

</x-eoffice::manajemen-praktikum.layout>