<x-eoffice::manajemen-praktikum.layout pageTitle="{{ $praktikum ? $praktikum->nama . ' / Absensi dan Nilai' : 'Absensi dan Nilai' }}">

@php
    /** @var \Modules\EOffice\Models\Praktikum $praktikum */
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Modul[] $allModuls */
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Modul[] $moduls */
    /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\DaftarPraktikan[] $daftarPraktikan */
    $kelompoks = isset($daftarPraktikan) ? $daftarPraktikan->pluck('kelompok')->filter(fn($val) => $val !== null && $val !== '')->unique()->sort()->values()->all() : [];
    $shifts = isset($daftarPraktikan) ? $daftarPraktikan->pluck('shift')->filter(fn($val) => $val !== null && $val !== '')->unique()->sort()->values()->all() : [];
@endphp

{{-- Pilih Praktikum --}}
@if(!$praktikum)
    <div style="padding: 48px; text-align: center; color: #808897;">Praktikum tidak ditemukan.</div>
@else

{{-- Sticky Header Wrapper --}}
    <div x-data="{ st: 0 }"
         x-init="
            const box = document.querySelector('.mp-box-body');
            if (box) {
                let ticking = false;
                box.addEventListener('scroll', () => {
                    if (!ticking) {
                        window.requestAnimationFrame(() => {
                            st = box.scrollTop;
                            ticking = false;
                        });
                        ticking = true;
                    }
                });
            }
         "
         class="sticky z-20 bg-white" style="top: -200px; margin: -20px -24px 0 -24px; padding: 20px 24px 0 24px; border-bottom: 1px solid var(--c-border);">
         
        {{-- Banner / Cover Image Container --}}
        <div style="position: relative; overflow: hidden; border-radius: 12px; border: 1px solid var(--c-border); height: 240px; margin-bottom: 4px; transform: translateZ(0);">
            
            {{-- Cover Placeholder --}}
            <div class="absolute inset-0 flex items-center justify-center bg-[#F3F4F6]" style="border-radius: inherit;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="#828896" 
                     :style="`transform: scale(${Math.max(0.6, 1 - (st / 200) * 0.4)}); opacity: ${Math.max(0, 1 - (st / 150))};`">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                </svg>
            </div>

            {{-- 1. Base Gradient --}}
            <div class="absolute inset-0 pointer-events-none"
                 style="border-radius: inherit; background: linear-gradient(to top, rgba(0,0,0,0.5) 0%, rgba(0,0,0,0.1) 30%, rgba(0,0,0,0) 70%);">
            </div>

            {{-- 2. Scrolled Overlay --}}
            <div class="absolute inset-0 pointer-events-none"
                 :style="`border-radius: inherit; opacity: ${Math.min(1, st / 200)}; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);`">
            </div>

            {{-- Title --}}
            <div class="absolute inset-0 flex flex-col justify-end pointer-events-none" style="padding: 14px 24px;">
                <h1 class="font-[800] text-white m-0 tracking-[-0.5px] origin-bottom-left"
                    :style="`font-size: 28px; transform: translateY(${-Math.min(6, (st / 200) * 6)}px) scale(${Math.max(0.714, 1 - (st / 200) * 0.286)}); text-shadow: 0 ${Math.max(1, 2 - (st/200)*1)}px ${Math.max(4, 8 - (st/200)*4)}px rgba(0,0,0,0.6);`">
                    {{ $praktikum->nama }}
                </h1>
            </div>
        </div>

        {{-- Tabs & Actions --}}
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('eoffice.manprak.dosen.praktikum.show', $praktikum->id) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Pengumuman</a>
                <a href="{{ route('eoffice.manprak.dosen.modul.index', $praktikum->id) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Modul</a>
                <a href="{{ route('eoffice.manprak.dosen.tugas.index', ['praktikum_id' => $praktikum->id]) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Tugas</a>
                <a href="#" style="padding: 12px 24px; font-weight: 600; font-size: 14px; color: #293C79; text-decoration: none; border-bottom: 2px solid #293C79;">Absensi dan Nilai</a>
                <a href="{{ route('eoffice.manprak.dosen.pendaftaran-koor.index', ['praktikum_id' => $praktikum->id]) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Seleksi Koordinator</a>
            <a href="{{ route('eoffice.manprak.dosen.asprak.index', ['praktikum_id' => $praktikum->id]) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Anggota</a>
            </div>
            
            
        </div>
    </div>

    {{-- Flash Error Notification --}}
    @if(session('error'))
    <div class="mp-flash mp-flash-error flex-shrink-0" style="margin-top: 24px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Flash Success Notification --}}
    @if(session('success'))
    <div class="mp-flash mp-flash-success flex-shrink-0" style="margin-top: 24px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif
    
    <style>
        .modul-accordion-content {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.2s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.15s ease;
        }
        .modul-accordion-content.is-open {
            max-height: 2500px;
            opacity: 1;
            transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.5s ease;
        }
    </style>
    <div style="display: flex; flex-direction: column; gap: 16px; padding-top: 4px;">


<div x-data="{ globalSearch: '', globalKelompok: '', globalShift: '' }" style="display: flex; flex-direction: column; gap: 16px;">

    {{-- Global Filter Bar --}}
    <div style="background: #fff; padding: 16px 24px; border: 1px solid var(--c-border); border-radius: 8px; margin-bottom: 0; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.04); gap: 16px;">
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        
        
        <input type="text" x-model="globalSearch" placeholder="Cari nama atau NIM..." class="mp-input" style="padding:0 12px; height:36px; font-size:13px; width:250px;">
        
        {{-- Combobox Kelompok --}}
        <div x-data="{
                open: false,
                searchStr: '',
                options: {{ json_encode($kelompoks) }},
                get filtered() {
                    return this.searchStr === '' 
                        ? this.options 
                        : this.options.filter(o => o.toLowerCase().includes(this.searchStr.toLowerCase()))
                },
                selectOption(opt) {
                    globalKelompok = opt;
                    this.searchStr = '';
                    this.open = false;
                }
            }" 
            class="relative" 
            @click.away="open = false"
        >
            <div class="mp-input flex items-center justify-between cursor-pointer" style="padding:0 12px; height:36px; font-size:13px; width:160px; background:#fff;" @click="open = !open; if(open) $nextTick(() => $refs.searchKel.focus())">
                <span x-text="globalKelompok === '' ? 'Semua Kelompok' : globalKelompok" class="truncate" :style="globalKelompok === '' ? 'color:#808897' : 'color:#0D0D12; font-weight:500;'"></span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :style="open ? 'transform:rotate(180deg)' : ''" style="transition:transform 0.2s;"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>

            <div x-show="open" x-transition class="absolute z-[50] mt-1 w-full bg-white border border-[#DFE1E7] rounded-[8px] shadow-lg" style="display: none; max-height: 250px; display:flex; flex-direction:column; overflow:hidden;">
                <div style="padding: 8px; border-bottom: 1px solid #EDF0F4; background:#F9FAFB;">
                    <input x-ref="searchKel" type="text" x-model="searchStr" placeholder="Cari kelompok..." class="mp-input" style="padding: 6px 10px; font-size:12px; width:100%; border-color:#DFE1E7; box-shadow:none;">
                </div>
                <div style="overflow-y:auto; flex:1; max-height: 200px;">
                    <div @click="selectOption('')" class="px-3 py-[8px] text-[13px] cursor-pointer transition-colors" :style="globalKelompok === '' ? 'background:#EAF0FA; color:#0D1D53; font-weight:600;' : 'color:#353849;'" onmouseover="if(globalKelompok !== '') this.style.background='#F6F8FA'" onmouseout="if(globalKelompok !== '') this.style.background='transparent'">
                        Semua Kelompok
                    </div>
                    <template x-for="opt in filtered" :key="opt">
                        <div @click="selectOption(opt)" class="px-3 py-[8px] text-[13px] cursor-pointer transition-colors" :style="globalKelompok === opt ? 'background:#EAF0FA; color:#0D1D53; font-weight:600;' : 'color:#353849;'" onmouseover="if(globalKelompok !== opt) this.style.background='#F6F8FA'" onmouseout="if(globalKelompok !== opt) this.style.background='transparent'" x-text="opt">
                        </div>
                    </template>
                    <div x-show="filtered.length === 0" class="px-3 py-[12px] text-[12px] text-center" style="color:#A4ABB8;">
                        Hasil Tidak Ditemukan
                    </div>
                </div>
            </div>
        </div>

        {{-- Combobox Shift --}}
        <div x-data="{
                open: false,
                searchStr: '',
                options: {{ json_encode($shifts) }},
                get filtered() {
                    return this.searchStr === '' 
                        ? this.options 
                        : this.options.filter(o => o.toLowerCase().includes(this.searchStr.toLowerCase()))
                },
                selectOption(opt) {
                    globalShift = opt;
                    this.searchStr = '';
                    this.open = false;
                }
            }" 
            class="relative" 
            @click.away="open = false"
        >
            <div class="mp-input flex items-center justify-between cursor-pointer" style="padding:0 12px; height:36px; font-size:13px; width:160px; background:#fff;" @click="open = !open; if(open) $nextTick(() => $refs.searchShf.focus())">
                <span x-text="globalShift === '' ? 'Semua Shift' : globalShift" class="truncate" :style="globalShift === '' ? 'color:#808897' : 'color:#0D0D12; font-weight:500;'"></span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :style="open ? 'transform:rotate(180deg)' : ''" style="transition:transform 0.2s;"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>

            <div x-show="open" x-transition class="absolute z-[50] mt-1 w-full bg-white border border-[#DFE1E7] rounded-[8px] shadow-lg" style="display: none; max-height: 250px; display:flex; flex-direction:column; overflow:hidden;">
                <div style="padding: 8px; border-bottom: 1px solid #EDF0F4; background:#F9FAFB;">
                    <input x-ref="searchShf" type="text" x-model="searchStr" placeholder="Cari shift..." class="mp-input" style="padding: 6px 10px; font-size:12px; width:100%; border-color:#DFE1E7; box-shadow:none;">
                </div>
                <div style="overflow-y:auto; flex:1; max-height: 200px;">
                    <div @click="selectOption('')" class="px-3 py-[8px] text-[13px] cursor-pointer transition-colors" :style="globalShift === '' ? 'background:#EAF0FA; color:#0D1D53; font-weight:600;' : 'color:#353849;'" onmouseover="if(globalShift !== '') this.style.background='#F6F8FA'" onmouseout="if(globalShift !== '') this.style.background='transparent'">
                        Semua Shift
                    </div>
                    <template x-for="opt in filtered" :key="opt">
                        <div @click="selectOption(opt)" class="px-3 py-[8px] text-[13px] cursor-pointer transition-colors" :style="globalShift === opt ? 'background:#EAF0FA; color:#0D1D53; font-weight:600;' : 'color:#353849;'" onmouseover="if(globalShift !== opt) this.style.background='#F6F8FA'" onmouseout="if(globalShift !== opt) this.style.background='transparent'" x-text="opt">
                        </div>
                    </template>
                    <div x-show="filtered.length === 0" class="px-3 py-[12px] text-[12px] text-center" style="color:#A4ABB8;">
                        Hasil Tidak Ditemukan
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div>
            @if($daftarPraktikan->isNotEmpty())
            <a href="{{ route('eoffice.manprak.dosen.nilai.export-csv', ['praktikumId' => $praktikum->id]) }}" class="mp-btn neutral sm" style="height:36px; padding:0 16px; display:inline-flex; align-items:center; background:#fff;border:1px solid #DFE1E7;box-shadow:0 1px 2px rgba(0,0,0,0.05); text-decoration:none; gap:6px; font-weight:600; color:#353849;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download CSV
            </a>
            @endif
        </div>

    </div>


@forelse($moduls as $modul)
<div x-data="{ modulOpen: false }" style="background: #fff; border: 1px solid var(--c-border); border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden;">
    <div style="padding: 16px 24px; background: #fff; border-bottom: 1px solid var(--c-border); cursor: pointer; display: flex; justify-content: space-between; align-items: center; transition: background 0.15s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
        <div @click="modulOpen = !modulOpen" style="flex: 1;">
            <h3 style="font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 4px 0; text-transform: capitalize;">Modul {{ $modul->urutan }} {{ strtolower($modul->nama) }}</h3>
            <div style="font-size: 13px; color: #6B7280;">
                Asisten Praktikum: {{ $modul->modulAsprak->map(fn($ma) => $ma->asprak->user?->name)->filter()->join(', ') ?: '-' }}
            </div>
        </div>
        
        <div style="display:flex; align-items:center;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :style="modulOpen ? 'transform: rotate(180deg)' : ''" style="transition: transform 0.2s; margin-left: 16px;" @click="modulOpen = !modulOpen"><polyline points="6 9 12 15 18 9"></polyline></svg>
        </div>
    </div>
    <div class="modul-accordion-content" :class="modulOpen ? 'is-open' : ''">
    <div style="overflow-x: auto;">
        <table class="mp-table" style="min-width: 900px; font-family: 'Inter', sans-serif;">
            <thead>
                <tr style="background:#fff;">
                    <th class="mp-th text-left" style="padding:12px 16px; width:40px;">NO</th>
                    <th class="mp-th text-left" style="padding:12px 16px; min-width:180px;">NAMA MAHASISWA</th>
                    <th class="mp-th text-left" style="padding:12px 16px;">NIM</th>
                    <th class="mp-th text-center" style="padding:12px 16px;width:90px;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;">Kelompok</th>
                    <th class="mp-th text-center" style="padding:12px 16px;width:70px;border-right:1px solid #DFE1E7;">Shift</th>
                    <th class="mp-th text-center" style="padding:12px 16px; width:90px;">Kehadiran</th>
                    <th class="mp-th text-center" style="padding:12px 16px; min-width:120px; background:#EAF0FA;">Tugas Pendahuluan</th>
                    <th class="mp-th text-center" style="padding:12px 16px; min-width:100px; background:#F0FDF4;">Praktikum</th>
                    <th class="mp-th text-center" style="padding:12px 16px; min-width:100px; background:#FEF9C3;">Laporan</th>
                    <th class="mp-th text-center" style="padding:12px 16px; min-width:100px; background:#FFF7ED;">Responsi</th>
                    <th class="mp-th text-left" style="padding:12px 16px;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
            @forelse($daftarPraktikan as $idx => $dp)
                @php
                    $absensi     = $dp->absensi->firstWhere('modul_id', $modul->id);
                    $statusAbsen = $absensi?->status;
                    $njMap       = $nilaiJenisMap[$modul->id][$dp->id] ?? [];
                @endphp
                <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;"
                    x-data="{ name: '{{ addslashes(strtolower($dp->user?->name ?? '')) }}', nim: '{{ addslashes(strtolower($dp->user?->student?->student_number ?? $dp->user?->email ?? '')) }}', kel: '{{ addslashes($dp->kelompok ?? '') }}', shf: '{{ addslashes($dp->shift ?? '') }}' }"
                    x-show="(globalSearch === '' || name.includes(globalSearch.toLowerCase()) || nim.includes(globalSearch.toLowerCase())) && (globalKelompok === '' || globalKelompok === kel) && (globalShift === '' || globalShift === shf)">
                    <td style="padding:12px 16px; color:#666D80; font-size:13px;">{{ $idx + 1 }}</td>
                    <td style="padding:12px 16px;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div class="mp-av sky" style="width:28px;height:28px;font-size:11px;flex-shrink:0;">{{ strtoupper(substr($dp->user?->name ?? 'M', 0, 2)) }}</div>
                            <div style="display:flex;flex-direction:column;gap:2px;">
                                <div style="font-weight:600;color:#0D0D12;font-size:13px;line-height:1.2;">{{ $dp->user?->name ?? '-' }}</div>
                                <div style="font-size:11px;color:#666D80;line-height:1.2;">{{ $dp->user?->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px; color:#0D0D12; font-size:13px; font-weight:600;">
                        {{ $dp->user?->student?->student_number ?? $dp->user?->email ?? '-' }}
                    </td>
                    <td style="padding:12px 16px;text-align:center;font-size:15px;font-weight:700;color:#0D0D12;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;">
                        {{ $dp->kelompok ?? '—' }}
                    </td>
                    <td style="padding:12px 16px;text-align:center;font-size:15px;font-weight:700;color:#0D0D12;border-right:1px solid #DFE1E7;">
                        {{ $dp->shift ?? '—' }}
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if($statusAbsen === 'hadir')
                            <span class="mp-badge success sm" style="background:#ECFDF5; color:#10B981;">Hadir</span>
                        @elseif($statusAbsen === 'izin')
                            <span class="mp-badge sky sm" style="background:#EFF6FF; color:#3B82F6;">Izin</span>
                        @elseif($statusAbsen === 'tidak_hadir')
                            <span class="mp-badge danger sm" style="background:#FEF2F2; color:#EF4444;">Alpha</span>
                        @else
                            <span style="color:#A4ABB8; font-size:12px;">—</span>
                        @endif
                    </td>
                    {{-- Tugas Pendahuluan --}}
                    <td style="padding:12px 16px;text-align:center;background:#EAF0FA;font-weight:700;color:#4338CA;font-size:13px;">
                        {{ isset($njMap['tugas_pendahuluan']) ? number_format($njMap['tugas_pendahuluan'], 1) : '—' }}
                    </td>
                    {{-- Praktikum --}}
                    <td style="padding:12px 16px;text-align:center;background:#F0FDF4;font-weight:700;color:#15803D;font-size:13px;">
                        {{ isset($njMap['praktikum']) ? number_format($njMap['praktikum'], 1) : '—' }}
                    </td>
                    {{-- Laporan --}}
                    <td style="padding:12px 16px;text-align:center;background:#FEF9C3;font-weight:700;color:#A16207;font-size:13px;">
                        {{ isset($njMap['laporan']) ? number_format($njMap['laporan'], 1) : '—' }}
                    </td>
                    {{-- Responsi --}}
                    <td style="padding:12px 16px;text-align:center;background:#FFF7ED;font-weight:700;color:#C2410C;font-size:13px;">
                        {{ isset($njMap['responsi']) ? number_format($njMap['responsi'], 1) : '—' }}
                    </td>
                    {{-- Keterangan --}}
                    <td style="padding:12px 16px;font-size:12px;color:#666D80;">
                        {{ $absensi?->keterangan ?? '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="padding:32px; text-align:center; color:#666D80; font-size:13px;">Belum ada data praktikan.</td>
                </tr>
            @endforelse
</div>

            </tbody>
        </table>
    </div>
    </div>
</div>
@empty
<div class="mp-card flex-shrink-0" style="padding:48px; text-align:center;">
    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
    <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada modul yang tersedia.</div>
</div>
@endforelse
</div>


{{-- Publikasi Nilai Section --}}
@php
    $totalPraktikan = $daftarPraktikan->count();
    $koorCount = 0;
    $dosenCount = 0;
    foreach($daftarPraktikan as $dp) {
        if ($dp->nilai?->disetujui_koor) $koorCount++;
        if ($dp->nilai?->disetujui_dosen) $dosenCount++;
    }
    $allKoorApproved = $totalPraktikan > 0 && $koorCount === $totalPraktikan;
    $allDosenApproved = $totalPraktikan > 0 && $dosenCount === $totalPraktikan;
@endphp

<div class="mp-card flex-shrink-0" style="margin-top: 32px; margin-bottom: 48px;">
    <div style="padding:20px; display:flex; flex-wrap:wrap; gap:20px; align-items:center; justify-content:space-between;">
        <div style="flex:1; min-width:300px;">
            <h3 style="font-size:16px; font-weight:700; color:#0D0D12; margin:0 0 8px 0;">Publikasi Nilai</h3>
            <p style="font-size:13px; color:#666D80; line-height:1.6; margin:0; max-width:700px;">
                Penyetujuan nilai ini dilakukan oleh dosen dan koordinator. Jika keduanya sudah menyetujui, maka nilai praktikum ini akan dipublikasikan sehingga mahasiswa dapat melihatnya.
            </p>
            <div style="display:flex; gap:16px; margin-top:12px;">
                <span style="display:flex; align-items:center; gap:6px; font-size:13px; font-weight:500; color:{{ $allDosenApproved ? '#059669' : '#666D80' }};">
                    @if($allDosenApproved)
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                    @else
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="3"/></svg>
                    @endif
                    Disetujui Dosen
                </span>
                <span style="display:flex; align-items:center; gap:6px; font-size:13px; font-weight:500; color:{{ $allKoorApproved ? '#059669' : '#666D80' }};">
                    @if($allKoorApproved)
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="20 6 9 17 4 12"/></svg>
                    @else
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="3"/></svg>
                    @endif
                    Disetujui Koordinator
                </span>
            </div>
        </div>

        <div style="flex-shrink:0;">
            @if(!$allDosenApproved)
                <form method="POST" action="{{ route('eoffice.manprak.dosen.nilai.approve', $praktikum->id) }}">
                    @csrf
                    <button type="submit" class="mp-btn primary md" style="background:#0D1D53;box-shadow:0 2px 6px rgba(13,29,83,.3);" onmouseover="this.style.background='#09143B'" onmouseout="this.style.background='#0D1D53'" onclick="return confirm('Publikasi semua nilai di praktikum ini?')">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        Setujui Publikasi Nilai
                    </button>
                </form>
            @else
                <div style="display:flex; gap:10px; align-items:center;">
                    <span class="mp-badge success md" style="font-size:13px; padding:6px 12px;">
                        <span class="dot"></span>Nilai Telah Dipublikasikan
                    </span>
                    <form method="POST" action="{{ route('eoffice.manprak.dosen.nilai.unapprove', $praktikum->id) }}">
                        @csrf
                        <button type="submit" class="mp-btn neutral md" onclick="return confirm('Batalkan publikasi nilai? Mahasiswa tidak akan dapat melihat nilai lagi.')" style="color:#DC2626; border-color:#FECACA; background:#FEF2F2;">
                            Batalkan Publikasi
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

@endif

</x-eoffice::manajemen-praktikum.layout>
