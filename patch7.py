filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\daftar-praktikan.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

text = text.replace('\xe2\u20ac\u201d', '-')
text = text.replace('\xe2\u20ac\xa2', '-')

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)
