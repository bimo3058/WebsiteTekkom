import os
import re

files = [
    'asprak.blade.php',
    'modul.blade.php',
    'nilai.blade.php',
    'praktikum-detail.blade.php',
    'tugas-pengumpulan.blade.php',
    'tugas.blade.php'
]

dir_path = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\dosen'

for filename in files:
    filepath = os.path.join(dir_path, filename)
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Find the block between 'Absensi' and '</div>'
    match = re.search(r'(<a[^>]*>Absensi.*?<\/a>)(.*?)(<\/div>)', content, re.DOTALL)
    if match:
        middle_chunk = match.group(2)
        # Extract the two anchor tags in middle_chunk
        tags = re.findall(r'<a.*?>.*?<\/a>', middle_chunk, re.DOTALL)
        if len(tags) == 2:
            tag1, tag2 = tags
            if 'Anggota' in tag1 and 'Seleksi Koordinator' in tag2:
                new_chunk = middle_chunk.replace(tag1, '@@T1@@').replace(tag2, tag1).replace('@@T1@@', tag2)
                content = content.replace(middle_chunk, new_chunk)
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(content)
                print(f"Success for {filename}")
            elif 'Seleksi Koordinator' in tag1 and 'Anggota' in tag2:
                new_chunk = middle_chunk.replace(tag1, '@@T1@@').replace(tag2, tag1).replace('@@T1@@', tag2)
                content = content.replace(middle_chunk, new_chunk)
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(content)
                print(f"Success for {filename} (swapped reverse)")
            else:
                print(f"Tags found but string didn't match in {filename}")
        else:
            print(f"Could not find exactly 2 tags after Absensi in {filename} : found {len(tags)}")
