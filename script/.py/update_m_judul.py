import os

files = [
    r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\bagi-modul.blade.php',
    r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\nilai.blade.php'
]

for filepath in files:
    if os.path.exists(filepath):
        with open(filepath, 'r', encoding='utf-8') as f:
            text = f.read()
        
        text = text.replace("$m->judul", "$m->nama")
        
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(text)

print("Updated judul to nama for Modul.")
