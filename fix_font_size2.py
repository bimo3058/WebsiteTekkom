import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\pendaftaran-asprak.blade.php'

with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# IPK
text = re.sub(
    r'<div style="font-weight:400; font-size:12px; color:#666D80;">(.*?)</div>',
    r'<div style="font-weight:400; font-size:13px; color:#666D80;">\1</div>',
    text,
    flags=re.DOTALL
)

# JADWAL
text = re.sub(
    r'<td style="padding:12px 16px;font-size:11px;color:#666D80;">\s*\{\{\s*collect\(\$p->jadwal.*?\}\}\s*</td>',
    lambda m: m.group(0).replace('font-size:11px;', 'font-size:13px;'),
    text,
    flags=re.DOTALL
)

# STATUS (Menunggu Admin)
text = text.replace('font-size:10px;color:#666D80;margin-top:2px', 'font-size:13px;color:#666D80;margin-top:4px')

# AKSI (Sudah diproses)
text = text.replace('font-size:11px;color:#808897', 'font-size:13px;color:#808897')

# Belum ada pendaftar (was 13px already)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Updated font sizes via regex!")
