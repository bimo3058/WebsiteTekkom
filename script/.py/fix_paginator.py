import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\pendaftaran-asprak.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# Let's fix it safely using regex
pattern = r'@if\(\$pendaftaran->hasPages\(\)\)\s*(.*?\{\{-- Pagination Custom Fungsional.*?@endif)\s*@endif'
text = re.sub(pattern, r'\1', text, flags=re.DOTALL)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Fixed pendaftaran-asprak pagination logic!")
