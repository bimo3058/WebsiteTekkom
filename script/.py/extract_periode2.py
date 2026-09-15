import sys

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/periode-pendaftaran.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

idx = text.find('Buka Periode Pendaftaran')

with open("script/data/extracted_periode2.txt", "w", encoding="utf-8") as out:
    out.write(text[idx-500:idx+4000])