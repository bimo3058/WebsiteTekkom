import os
filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\daftar-praktikan.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

text = text.replace('peer-checked:bg-[#203a7a]', 'peer-checked:bg-[#0B266E]')
text = text.replace('peer-checked:border-[#203a7a]', 'peer-checked:border-[#0B266E]')

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)
