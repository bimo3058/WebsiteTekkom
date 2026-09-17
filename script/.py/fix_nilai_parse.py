import os

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\nilai.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# Fix the broken layout tag
wrong = """<x-eoffice::manajemen-praktikum.layout pageTitle="Rekap Nilai â€” {{ $praktikum ? $praktikum->
    @if(isset($praktikum) && $praktikum)
        <x-eoffice::manajemen-praktikum.koor-header :praktikum="$praktikum" />
    @endif
nama : 'Belum Ada Praktikum' }}">"""

correct = """<x-eoffice::manajemen-praktikum.layout pageTitle="Rekap Nilai â€” {{ $praktikum ? $praktikum->nama : 'Belum Ada Praktikum' }}">
    @if(isset($praktikum) && $praktikum)
        <x-eoffice::manajemen-praktikum.koor-header :praktikum="$praktikum" />
    @endif"""

text = text.replace(wrong.replace('â€"', '—'), correct.replace('â€"', '—'))

# In case encoding issue makes replacing tricky, let's use regex
import re
text = re.sub(r'<x-eoffice::manajemen-praktikum\.layout pageTitle="Rekap Nilai [^\n]*\n\s*@if\(isset\(\$praktikum\).*?@endif\nnama : \'Belum Ada Praktikum\' "\}\}">', 
              r'<x-eoffice::manajemen-praktikum.layout pageTitle="Rekap Nilai — {{ $praktikum ? $praktikum->nama : \'Belum Ada Praktikum\' }}">\n    @if(isset($praktikum) && $praktikum)\n        <x-eoffice::manajemen-praktikum.koor-header :praktikum="$praktikum" />\n    @endif', 
              text, flags=re.DOTALL)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Fixed nilai.blade.php ParseError.")
