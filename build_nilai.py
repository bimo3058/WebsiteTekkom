import re

with open(r"c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\nilai.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# Ectract loop logic for tbody
m = re.search(r'(<tbody>\s*@forelse.*?</tbody>)', text, flags=re.DOTALL)
if m:
    tbody_code = m.group(1)
else:
    print("WARNING: couldn't find tbody code")
    exit(1)

m2 = re.search(r'(\{\{-- Publikasi Nilai Section --\}\}.*?</x-eoffice::manajemen-praktikum\.layout>)', text, flags=re.DOTALL)
if m2:
    publikasi_code = m2.group(1)
else:
    print("WARNING: couldn't find publikasi code")
    exit(1)

new_blade = f"""<x-eoffice::manajemen-praktikum.layout pageTitle="{{{{ $praktikum ? $praktikum->nama : 'Belum Ada Praktikum' }}}} / Absensi & Nilai">
    @if(isset($praktikum) && $praktikum)
        <x-eoffice::manajemen-praktikum.koor-header :praktikum="$praktikum" />
    @endif

@php
    /** @var \\Modules\\EOffice\\Models\\Praktikum|null $praktikum */
    /** @var \\Illuminate\\Database\\Eloquent\\Collection|\\Modules\\EOffice\\Models\\Modul[] $allModuls */
    /** @var \\Illuminate\\Database\\Eloquent\\Collection|\\Modules\\EOffice\\Models\\Modul[] $moduls */
    /** @var \\Illuminate\\Database\\Eloquent\\Collection|\\Modules\\EOffice\\Models\\DaftarPraktikan[] $daftarPraktikan */
@endphp

@if(!$praktikum)
<div class="mp-alert warning flex-shrink-0" style="margin-top:24px;">Anda belum memiliki praktikum aktif.</div>
@else

<div class="sec-head" style="margin-top: 24px;">
    <span class="sec-bar"></span>
    <span class="sec-title">Absensi & Nilai</span>
    <span class="sec-rule"></span>
</div>

<div x-data="{{ globalSearch: '', globalKelompok: '', globalShift: '' }}">

    {{{{-- Global Filter Bar --}}}}
    <div class="mp-card" style="margin-bottom:24px; padding:16px 24px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px;">
        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <div style="position:relative;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" x-model="globalSearch" placeholder="Cari nama mahasiswa / NIM..." class="mp-input" style="width:240px; padding-left:32px;">
            </div>
            
            <select x-model="globalKelompok" class="mp-input" style="width:180px;">
                <option value="">Semua Kelompok</option>
                @php
                    $kels = $daftarPraktikan->pluck('kelompok')->filter()->unique()->sort();
                @endphp
                @foreach($kels as $k)
                    <option value="{{{{ $k }}}}">Kelompok {{{{ $k }}}}</option>
                @endforeach
            </select>

            <select x-model="globalShift" class="mp-input" style="width:160px;">
                <option value="">Semua Shift</option>
                @php
                    $shfs = $daftarPraktikan->pluck('shift')->filter()->unique()->sort();
                @endphp
                @foreach($shfs as $s)
                    <option value="{{{{ $s }}}}">Shift {{{{ $s }}}}</option>
                @endforeach
            </select>
        </div>

        <div>
            <a href="{{{{ route('eoffice.manprak.koor.nilai.export-csv') }}}}" class="mp-btn neutral md" style="background:#fff;border:1px solid #DFE1E7;box-shadow:0 1px 2px rgba(0,0,0,0.05); display:inline-flex; align-items:center; gap:6px; text-decoration:none;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download CSV
            </a>
        </div>
    </div>

    {{{{-- Rendering Modules Accordion --}}}}
    @forelse($allModuls as $modul)
    <div x-data="{{ expanded: false }}" class="mp-card" style="margin-bottom:16px;">
        <div style="padding:16px 24px; cursor:pointer; display:flex; justify-content:space-between; align-items:center;" @click="expanded = !expanded" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='transparent'" class="transition-colors">
            <div>
                <div style="font-size:18px; font-weight:700; color:#000;">Modul {{{{ $modul->urutan ?? $loop->iteration }}}} {{{{ $modul->nama }}}}</div>
                <div style="font-size:13px; color:#000; margin-top:4px;">Asisten Praktikum: {{{{ $modul->asistens->pluck('user.name')->join(', ') ?: '-' }}}}</div>
            </div>
            <div style="display:flex; align-items:center; gap:20px;">
                <a href="{{{{ route('eoffice.manprak.koor.nilai.export-csv', ['modul_id' => $modul->id]) }}}}" class="mp-btn" style="background:#000; color:#fff; border:none; padding:8px 16px; border-radius:4px; font-weight:600; font-size:12px; text-decoration:none;" @click.stop>
                    Download CSV
                </a>
                <svg class="transition-transform duration-200" :class="expanded ? 'rotate-180' : ''" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>

        <div x-show="expanded" style="display:none; border-top:1px solid #DFE1E7;" x-transition>
            <div style="overflow-x:auto;">
                <table class="mp-table" style="min-width:1100px;">
                    <thead>
                        <tr style="background:#F9FAFB;">
                            <th class="mp-th text-left" style="padding:12px 16px;width:50px;">NO</th>
                            <th class="mp-th text-left" style="padding:12px 16px;width:250px;">PRAKTIKAN</th>
                            <th class="mp-th text-left" style="padding:12px 16px;width:150px;">NIM</th>
                            <th class="mp-th text-center" style="padding:12px 16px;width:100px;border-left:1px solid #DFE1E7;">Kelompok</th>
                            <th class="mp-th text-center" style="padding:12px 16px;width:100px;border-right:1px solid #DFE1E7;">Shift</th>
                            <th class="mp-th text-center" style="padding:12px 16px;width:100px;">Kehadiran</th>
                            <th class="mp-th text-center" style="padding:12px 16px;width:100px;border-left:1px solid #DFE1E7;background:#EEF2FF;color:#4338CA;">T. Pendahuluan</th>
                            <th class="mp-th text-center" style="padding:12px 16px;width:100px;background:#F0FDF4;color:#15803D;">Praktikum</th>
                            <th class="mp-th text-center" style="padding:12px 16px;width:100px;background:#FEFCE8;color:#A16207;">Laporan</th>
                            <th class="mp-th text-center" style="padding:12px 16px;width:100px;border-right:1px solid #DFE1E7;background:#FFF7ED;color:#C2410C;">Responsi</th>
                            <th class="mp-th text-left" style="padding:12px 16px;">Keterangan</th>
                        </tr>
                    </thead>
                    {tbody_code}
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

{publikasi_code}
"""

with open(r"c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\nilai.blade.php", "w", encoding="utf-8") as f:
    f.write(new_blade)

print("Updated nilai.blade.php layout completely.")
