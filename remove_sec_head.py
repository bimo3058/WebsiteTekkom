import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\modul.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

pattern = r'<div class="sec-head">\s*<span class="sec-bar"></span>\s*<span class="sec-title">Tambah & Kelola Modul</span>\s*<span class="sec-rule"></span>\s*</div>'
text = re.sub(pattern, '', text)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Removed sec-head block")
