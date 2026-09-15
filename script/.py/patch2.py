import os

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\daftar-praktikan.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

text = text.replace('<label style="display:flex;', '<label class="group" style="display:flex;')

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)
