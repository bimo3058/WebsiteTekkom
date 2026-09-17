import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\nilai.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

pattern = r'<div class="mp-card" style="margin-bottom:24px; padding:16px 24px; display:flex; gap:16px; align-items:center; \n?width:100%; justify-content:space-between;">.*?</a>\s*</div>\s*</div>'

new_header = """<div class="mp-card" style="margin-bottom: 24px; padding: 16px 24px;">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <div class="text-[16px] font-bold text-[#0D0D12] flex-shrink-0 whitespace-nowrap text-left">
                Absensi & Nilai
            </div>
            
            <div class="flex flex-wrap md:flex-nowrap items-center justify-end gap-3 w-full md:w-auto flex-1">
                
                <div class="relative w-full md:w-auto md:max-w-[260px] flex-1">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" x-model="globalSearch" placeholder="Cari nama / NIM..." class="mp-input w-full pl-9 h-[38px] text-[13px]">
                </div>

                <select x-model="globalKelompok" class="mp-input h-[38px] w-full md:w-[160px] flex-shrink-0 text-[13px]">
                    <option value="">Semua Kelompok</option>
                    @php
                        $kels = $daftarPraktikan->pluck('kelompok')->filter()->unique()->sort();
                    @endphp
                    @foreach($kels as $k)
                        <option value="{{ $k }}">Kelompok {{ $k }}</option>
                    @endforeach
                </select>

                <select x-model="globalShift" class="mp-input h-[38px] w-full md:w-[140px] flex-shrink-0 text-[13px]">
                    <option value="">Semua Shift</option>
                    @php
                        $shfs = $daftarPraktikan->pluck('shift')->filter()->unique()->sort();
                    @endphp
                    @foreach($shfs as $s)
                        <option value="{{ $s }}">Shift {{ $s }}</option>
                    @endforeach
                </select>

                <a href="{{ route('eoffice.manprak.koor.nilai.export-csv') }}" class="mp-btn neutral md flex items-center justify-center gap-2 h-[38px] bg-white border border-[#DFE1E7] hover:bg-gray-50 flex-shrink-0 whitespace-nowrap px-4 w-full md:w-auto" style="box-shadow:0 1px 2px rgba(0,0,0,0.05); text-decoration:none;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    <span class="text-[13px] font-semibold">Download CSV</span>
                </a>
                
            </div>
        </div>
    </div>"""

text = re.sub(pattern, new_header, text, flags=re.DOTALL)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Tailwind layout updated")
