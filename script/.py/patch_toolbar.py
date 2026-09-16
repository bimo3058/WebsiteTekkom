import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\nilai.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

old_filter_bar = r'\{\{-- Global Filter Bar --\}\}.*?</a>\s*</div>\s*</div>'

new_filter_bar = """{{-- Global Filter Bar --}}
    <div class="mp-card" style="margin-bottom:24px; padding:16px 24px; display:flex; gap:16px; align-items:center; width:100%;">
        
        <div style="position:relative; flex:1;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" x-model="globalSearch" placeholder="Cari nama mahasiswa / NIM..." class="mp-input" style="width:100%; padding-left:36px; height: 38px;">
        </div>
        
        <select x-model="globalKelompok" class="mp-input" style="width:170px; height: 38px; flex-shrink:0;">
            <option value="">Semua Kelompok</option>
            @php
                $kels = $daftarPraktikan->pluck('kelompok')->filter()->unique()->sort();
            @endphp
            @foreach($kels as $k)
                <option value="{{ $k }}">Kelompok {{ $k }}</option>
            @endforeach
        </select>

        <select x-model="globalShift" class="mp-input" style="width:170px; height: 38px; flex-shrink:0;">
            <option value="">Semua Shift</option>
            @php
                $shfs = $daftarPraktikan->pluck('shift')->filter()->unique()->sort();
            @endphp
            @foreach($shfs as $s)
                <option value="{{ $s }}">Shift {{ $s }}</option>
            @endforeach
        </select>

        <a href="{{ route('eoffice.manprak.koor.nilai.export-csv') }}" class="mp-btn neutral md" style="height:38px; background:#fff; border:1px solid #DFE1E7; box-shadow:0 1px 2px rgba(0,0,0,0.05); display:inline-flex; align-items:center; justify-content:center; gap:6px; text-decoration:none; flex-shrink:0; cursor:pointer;" onmouseover="this.style.background=\'#F9FAFB\'" onmouseout="this.style.background=\'#FFF\'">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download CSV
        </a>
    </div>"""

text = re.sub(old_filter_bar, new_filter_bar, text, flags=re.DOTALL)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Toolbar UI updated")
