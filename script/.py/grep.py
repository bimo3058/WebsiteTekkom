
import re, sys
lines = open('Modules/EOffice/resources/views/manajemen-praktikum/koordinator/nilai.blade.php').read().split('
')
for i, line in enumerate(lines):
    if '<td' in line:
        print('
'.join(lines[i:i+15]))
        break
