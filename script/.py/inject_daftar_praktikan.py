import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\daftar-praktikan.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

header_insert = """
    @if(isset($praktikum) && $praktikum)
        <x-eoffice::manajemen-praktikum.koor-header :praktikum="$praktikum" />
    @endif
"""

if "koor-header" not in text:
    text = re.sub(r'(<x-eoffice::manajemen-praktikum\.layout[^>]*>)', r'\1' + header_insert, text, count=1)
    
    # Remove mp-page-header h1 titles
    text = re.sub(r'<div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">.*?</h1>.*?</div>', '', text, flags=re.DOTALL)
    text = re.sub(r'<p class="mp-page-sub">.*?</p>', '', text)
    
    # Remove mp-page-header entirely if it becomes empty
    text = re.sub(r'<div class="mp-page-header">\s*<div>\s*</div>\s*</div>', '', text, flags=re.DOTALL)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Added koor-header to Daftar Praktikan.")
