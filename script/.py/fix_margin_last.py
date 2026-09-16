import sys

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

text = text.replace(":style=\"open ? { 'padding-bottom': '115px' } : {}\"", ":style=\"open ? { 'padding-bottom': '105px' } : { 'padding-bottom': '12px' }\"")
text = text.replace(":style=\"open ? { 'padding-bottom': '120px' } : {}\"", ":style=\"open ? { 'padding-bottom': '105px' } : { 'padding-bottom': '12px' }\"")

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print('Fixed Stuck Padding!')