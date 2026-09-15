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

    # The block containing tabs
    # We want to match:
    # <a ...>Anggota</a>
    # <a ...>Seleksi Koordinator</a>
    
    # Let's find both tags. We know Anggota comes first right now.
    match_anggota = re.search(r'(<a\s+href=[^>]*>Anggota<\/a>)\s*', content)
    match_seleksi = re.search(r'(<a\s+href=[^>]*>Seleksi Koordinator<\/a>)\s*', content)
    
    if match_anggota and match_seleksi:
        # Get start/end indices to extract and replace the whole block exactly.
        # But wait, we can just replace 'Anggota' with a placeholder, then 'Seleksi Koordinator' with 'Anggota' HTML, then placeholder with 'Seleksi Koordinator' HTML
        
        c = content.replace(match_anggota.group(1), '@@ANGGOTA@@')
        c = c.replace(match_seleksi.group(1), match_anggota.group(1))
        c = c.replace('@@ANGGOTA@@', match_seleksi.group(1))
        
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(c)
        print(f"Swapped in {filename}")

