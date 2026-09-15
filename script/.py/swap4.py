import os
import re

files = [
    'asprak.blade.php',
    'modul.blade.php',
    'praktikum-detail.blade.php',
    'tugas-pengumpulan.blade.php',
    'tugas.blade.php'
]

dir_path = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\dosen'

for filename in files:
    filepath = os.path.join(dir_path, filename)
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Find where tabs are located
    parts = re.split(r'(<div style="display: flex; gap: 8px;">\s*)(.*?)(<\/div>)', content, maxsplit=1, flags=re.DOTALL)
    
    if len(parts) == 5:
        # structure: prefix, <div...>, inner_html, </div>, suffix
        inner_html = parts[2]
        
        # find all <a> tags
        a_tags = re.findall(r'<a.*?<\/a>', inner_html, re.DOTALL)
        
        # We know we have 6 tags [Pengumuman, Modul, Tugas, Absensi, Anggota, Seleksi Koordinator]
        # Wait, some might have newlines inside.
        
        # Sort out Anggota and Seleksi Koordinator
        # It's simply the last two we want to swap if the second to last is not 'angota' but wait in the request they say:
        # "saat ini posisi terakhir adalah menu Anggota, bisa kah kamu tukar posisinya dengan menu Anggota" -> Seleksi Koordinator is second to last.
        # But wait, earlier I proved Anggota was 5th and Seleksi Koordinator 6th in asprak.blade.php.
        # Wait! In asprak.blade.php, Anggota is 5th, Seleksi is 6th. So maybe they don't even need swapping there?
        # The user says "saat ini posisi terakhir adalah menu Anggota". Let me check if that's true universally.
        # Let me just check the exact strings for all tags.
        
        new_tags = []
        seleksi_tag = None
        anggota_tag = None
        
        for t in a_tags:
            if 'Seleksi Koordinator' in t:
                seleksi_tag = t
            elif 'Anggota' in t:
                anggota_tag = t
        
        if seleksi_tag and anggota_tag:
            # We want Anggota to come before Seleksi Koordinator
            # Let's rebuild the inner_html maintaining the other tags
            final_tags = []
            for t in a_tags:
                if t == seleksi_tag or t == anggota_tag:
                    continue
                final_tags.append(t)
            
            # Append Anggota then Seleksi Koordinator
            final_tags.append(anggota_tag)
            final_tags.append(seleksi_tag)
            
            # Now we recreate the inner_html. We will just join with '\n            ' and some spaces
            joined_tags = '\n            '.join(final_tags)
            new_inner_html = "\n            " + joined_tags + "\n        "
            
            # Or safely replace inside the existing text if there are exact occurrences
            new_content = parts[0] + parts[1] + new_inner_html + parts[3] + parts[4]
            
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(new_content)
            print(f"Success for {filename}")

