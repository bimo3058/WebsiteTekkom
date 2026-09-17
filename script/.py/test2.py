filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\daftar-praktikan.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

import re
matches = re.finditer(r'<span[^>]*>(.[^<]*)</span>', text)
for m in matches:
    if len(m.group(1)) < 5 and 'style' in m.group(0):
        print(repr(m.group(1)), [hex(ord(c)) for c in m.group(1)])
