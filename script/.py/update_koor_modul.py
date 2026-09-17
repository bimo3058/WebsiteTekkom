import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\modul.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

pattern = r'\{\{-- Page Header --.*?</div>\s*</div>'
replacement = """@if($praktikum)
    <x-eoffice::manajemen-praktikum.koor-header :praktikum="$praktikum" />
@else
    <div class="mp-page-header">
        <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <h1 class="mp-page-title">Kelola Modul Praktikum</h1>
                <span class="mp-badge" style="background:#E0E7FF;color:#6366F1;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:5px;"><span class="dot" style="background:#6366F1;"></span>Koordinator</span>
            </div>
            <p class="mp-page-sub">Belum ada praktikum aktif</p>
        </div>
    </div>
@endif"""

text = re.sub(pattern, replacement, text, flags=re.DOTALL)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

