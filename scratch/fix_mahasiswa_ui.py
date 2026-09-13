import os
import re

file_path = r"c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\mahasiswa\nilai.blade.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Update Layout Header
header_old = """<x-eoffice::manajemen-praktikum.layout pageTitle="Absensi & Nilai Saya">

{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
            <h1 class="mp-page-title">Absensi & Nilai Saya</h1>
            <span class="mp-badge warning sm"><span class="dot"></span>Mahasiswa</span>
        </div>
        <p class="mp-page-sub">Rekap kehadiran dan nilai tugas setelah dipublikasikan oleh dosen &amp; koordinator · {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
</div>"""

header_new = """<x-eoffice::manajemen-praktikum.layout
    pageTitle="{{ $praktikum ? $praktikum->nama : 'Belum Ada Praktikum' }} / Absensi & Nilai Modul">
    @if(isset($praktikum) && $praktikum)
        <x-eoffice::manajemen-praktikum.mhs-header :praktikum="$praktikum" />
    @endif"""

content = content.replace(header_old, header_new)

# 2. Remove "Pilih Praktikum" section
# Let's use regex to remove from {{-- Pilih Praktikum (jika lebih dari satu) --}} to @endif before @if(!$praktikum)
dropdown_pattern = re.compile(r"\{\{-- Pilih Praktikum \(jika lebih dari satu\) --\}\}.*?</div>\s*@endif\s*", re.DOTALL)
content = dropdown_pattern.sub("", content)

# 3. Update loops to use $daftarPraktikan instead of [$dp]
content = content.replace("rows: [\n                                                                                                                                                                                                                                                                                                                                                @foreach([$dp] as $idx => $dp_item)", "rows: [\n                                                                                                                                                                                                                                                                                                                                                @foreach($daftarPraktikan as $idx => $dp_item)")

content = content.replace("@forelse([$dp] as $idx => $dp_item)", "@forelse($daftarPraktikan as $idx => $dp_item)")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("Updates applied.")
