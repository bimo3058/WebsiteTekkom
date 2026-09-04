import os

filepath_modul = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\modul.blade.php'
filepath_extracted = r'c:\Users\User\manajemen_praktikum_\extracted_bagi_modul.txt'

with open(filepath_modul, 'r', encoding='utf-8') as f:
    modul_text = f.read()

with open(filepath_extracted, 'r', encoding='utf-16') as f:
    bagi_text = f.read()

lines = bagi_text.split("\n")
cleaned_lines = []
for line in lines:
    if "</x-eoffice::manajemen-praktikum.layout>" in line:
        continue
    if line.startswith("> "):
        line = line[2:]
    
    cleaned_lines.append(line)

bagi_cleaned = "\n".join(cleaned_lines)

# Clean up spacing
bagi_cleaned = bagi_cleaned.replace(" \r", "").replace("\r", "")

if "</x-eoffice::manajemen-praktikum.layout>" in modul_text:
    modul_text = modul_text.replace("</x-eoffice::manajemen-praktikum.layout>", "\n" + bagi_cleaned + "\n</x-eoffice::manajemen-praktikum.layout>")

with open(filepath_modul, 'w', encoding='utf-8') as f:
    f.write(modul_text)

print("Merged Pembagian Modul to Kelola Modul successfully!")
