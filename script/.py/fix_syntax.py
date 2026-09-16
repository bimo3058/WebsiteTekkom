import os

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\modul.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# Add @endif before the closing layout tags
if "</x-eoffice::manajemen-praktikum.layout>" in text:
    text = text.replace("</x-eoffice::manajemen-praktikum.layout>", "@endif\n</x-eoffice::manajemen-praktikum.layout>")

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)
