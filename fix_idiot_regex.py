import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\pendaftaran-asprak.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# Revert my brutal regex mistakes
text = text.replace('praktikum$p->', 'praktikum->')
text = text.replace('periodeAktif$p->', 'periodeAktif->')
text = text.replace('pendaftaran$p->', 'pendaftaran->')
text = text.replace('$loop$p->', '$loop->')
text = text.replace('request()$p->', 'request()->')
text = text.replace('$p$p->', '$p->')
text = text.replace('$$p->', '$p->')

# Any trailing $p-> that attached to a variable name like $name$p->
text = re.sub(r'(\$\w+)\$p->', r'\1->', text)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Fixed over-eager replacement!")
