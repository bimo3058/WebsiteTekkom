import os

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\daftar-praktikan.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if '@else' in line and 'span' in lines[i+1]:
        print("Found at line", i+1, repr(lines[i+1]))
