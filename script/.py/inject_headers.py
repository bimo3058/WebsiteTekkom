import os
import re

files = [
    r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\pengumuman.blade.php',
    r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\pendaftaran-praktikan.blade.php',
    r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\nilai.blade.php'
]

header_insert = """
    @if(isset($praktikum) && $praktikum)
        <x-eoffice::manajemen-praktikum.koor-header :praktikum="$praktikum" />
    @endif
"""

for filepath in files:
    if os.path.exists(filepath):
        with open(filepath, 'r', encoding='utf-8') as f:
            text = f.read()

        # Check if already has it
        if "koor-header" not in text:
            # Insert after <x-eoffice::...layout ...>
            text = re.sub(r'(<x-eoffice::manajemen-praktikum\.layout[^>]*>)', r'\1' + header_insert, text, count=1)
            
            # Optionally remove the mp-page-header so it's not redundant? We'll just remove the title div and keep the actions, 
            # actually let's just remove the entire mp-page-header if it doesn't have crucial actions?
            # Pengumuman has 'Buat Pengumuman', Nilai has 'Export', Pendaftaran Praktikan has nothing.
            
            # Let's remove the h1 and badge to prevent duplication in UI
            text = re.sub(r'<div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">.*?</h1>.*?</div>', '', text, flags=re.DOTALL)
            # Remove the empty paragraph / subtitles just to clean it
            text = re.sub(r'<p class="mp-page-sub">.*?</p>', '', text)
            
            # Remove mp-page-header entirely if it becomes empty
            text = re.sub(r'<div class="mp-page-header">\s*<div>\s*</div>\s*</div>', '', text, flags=re.DOTALL)

            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(text)

print("Added koor-header to Pengumuman, Praktikan, Nilai.")
