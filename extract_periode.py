import sys

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/periode-pendaftaran.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

idx = text.find('Buka Periode Pendaftaran')

with open("extracted_periode.txt", "w", encoding="utf-8") as out:
    out.write(text[max(0, idx-1000):idx+2500])