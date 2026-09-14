with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

text = text.replace(
    'style="display: none; position: absolute; right: 10px; top: 12px; cursor: pointer;"',
    'style="display: none; position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer;"'
)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print("done tweaking icon")