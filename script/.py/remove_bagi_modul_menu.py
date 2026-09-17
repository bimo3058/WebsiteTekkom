import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\components\manajemen-praktikum\koor-header.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# Try to remove the Bagi Modul anchor
match = re.search(r'<a href="\{\{\s*route\(\'eoffice\.manprak\.koor\.bagi-modul\.index\'\)\s*\}\}".*?>Bagi Modul</a>', text, re.DOTALL)
if match:
    text = text.replace(match.group(0), '')

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Removed Bagi Modul menu.")
