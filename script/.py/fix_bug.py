import os

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\pendaftaran-asprak.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

text = text.replace('praktikum?$p->id', 'praktikum?->id') 

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)
