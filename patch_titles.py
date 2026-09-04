import os
import re

directory = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator'

files = {
    'daftar-praktikan.blade.php': 'Daftar Praktikan',
    'modul.blade.php': 'Modul',
    'nilai.blade.php': 'Absensi & Nilai',
    'pendaftaran-asprak.blade.php': 'Seleksi Asisten',
    'pengumuman.blade.php': 'Pengumuman',
}

for f, suffix in files.items():
    filepath = os.path.join(directory, f)
    with open(filepath, 'r', encoding='utf-8') as file:
        text = file.read()
    
    # replace first <x-eoffice::manajemen-praktikum.layout pageTitle="...">
    text = re.sub(r'<x-eoffice::manajemen-praktikum\.layout pageTitle="[^"]+">', 
                  f'<x-eoffice::manajemen-praktikum.layout pageTitle="{{{{ $praktikum ? $praktikum->nama : \'Belum Ada Praktikum\' }}}} / {suffix}">', 
                  text, count=1)
    
    if f == 'pendaftaran-asprak.blade.php':
        text = text.replace('Selaksi Asisten', 'Seleksi Asisten') # just in case typo
    
    with open(filepath, 'w', encoding='utf-8') as file:
        file.write(text)

print("Updated page titles!")
