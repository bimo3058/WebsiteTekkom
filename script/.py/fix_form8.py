import sys

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# I will target the Daftar Calon Koordinator mp-card
old_start = """{{-- SECTION: Daftar Calon Koordinator --}}
            <div class="mp-card flex-1 min-h-0" style="margin-top: -1px; display: flex; flex-direction: column;">"""

new_start = """{{-- SECTION: Daftar Calon Koordinator --}}
            <div class="mp-card flex-1 min-h-0" style="margin-top: 24px; display: flex; flex-direction: column;">"""

text = text.replace(old_start, new_start)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")