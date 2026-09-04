import os
import re

files_to_edit = [
    'asprak.blade.php',
    'modul.blade.php',
    'nilai.blade.php',
    'praktikum-detail.blade.php',
    'tugas-pengumpulan.blade.php',
    'tugas.blade.php'
]

dir_path = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\dosen'

for filename in files_to_edit:
    filepath = os.path.join(dir_path, filename)
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    match_anggota = re.search(r'<a[^>]*>Anggota<\/a>', content)
    match_seleksi = re.search(r'<a[^>]*>Seleksi Koordinator<\/a>', content)
    match_absensi = re.search(r'<a[^>]*>Absensi dan Nilai<\/a>', content, re.MULTILINE | re.DOTALL)
    
    if match_anggota and match_seleksi:
        c = content.replace(match_anggota.group(0), '@@ANGGOTA@@')
        c = c.replace(match_seleksi.group(0), match_anggota.group(0))
        c = c.replace('@@ANGGOTA@@', match_seleksi.group(0))
        
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(c)
        print(f"Swapped in {filename}")
    else:
        print(f"Failed to match in {filename}")

