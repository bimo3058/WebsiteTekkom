import sys

with open('script/fix_cards.py', 'r', encoding='utf-8') as f:
    text = f.read()

part = text.split('new_section = """')[1]
new_section = part.split('"""')[0]

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    orig = f.read()

target = '{{-- Section: Filter --}}'
if target in orig:
    orig = orig.replace(target, new_section + '\n' + target)
    with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
        f.write(orig)
    print('INJECTED KOORDINATOR AKTIF!')
else:
    print('FAIL')
