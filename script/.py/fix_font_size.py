import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\pendaftaran-asprak.blade.php'

with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# NIM font size is 13px (color:#4B5563)
# We need to make IPK, TRANSKRIP NILAI, JADWAL, STATUS, AKSI font size 13px

# IPK:
# <div style="font-weight:400; font-size:12px; color:#666D80;">
text = text.replace('<div style="font-weight:400; font-size:12px; color:#666D80;">\n                                    {{ number_format($p->ipk ?? 0, 2) }}\n                                </div>', 
                    '<div style="font-weight:400; font-size:13px; color:#666D80;">\n                                    {{ number_format($p->ipk ?? 0, 2) }}\n                                </div>')

# TRANSKRIP NILAI / CERC
# <span style="font-size:13px;color:#808897;">—</span> (Already 13px for links and dashes)
# Wait, are they already 13px? 
# <a href="..." target="_blank" style="font-size:13px;font-weight:600;color:#0B266E;" class="hover:underline">Lihat Berkas</a>
# Yes, they are already 13px.

# JADWAL
# <td style="padding:12px 16px;font-size:11px;color:#666D80;">
text = text.replace('<td style="padding:12px 16px;font-size:11px;color:#666D80;">\n                                {{ collect($p->jadwal ?? [])->join(', ') ?: '—' }}\n                            </td>', 
                    '<td style="padding:12px 16px;font-size:13px;color:#666D80;">\n                                {{ collect($p->jadwal ?? [])->join(', ') ?: '—' }}\n                            </td>')

# STATUS (Menunggu Admin)
# <div style="font-size:10px;color:#666D80;margin-top:2px;">Menunggu Admin</div>
text = text.replace('<div style="font-size:10px;color:#666D80;margin-top:2px;">Menunggu Admin</div>', 
                    '<div style="font-size:13px;color:#666D80;margin-top:4px;">Menunggu Admin</div>')

# AKSI (Sudah diproses)
# <span style="font-size:11px;color:#808897;">Sudah diproses</span>
text = text.replace('<span style="font-size:11px;color:#808897;">Sudah diproses</span>', 
                    '<span style="font-size:13px;color:#808897;">Sudah diproses</span>')

# Fix Jadwal replacement that might not have worked perfectly because of the join(', ') syntax
import re
text = re.sub(r'<td style="padding:12px 16px;font-size:11px;color:#666D80;">(\s*\{\{\s*collect\(\$p->jadwal.*?\}\}\s*)</td>', 
              r'<td style="padding:12px 16px;font-size:13px;color:#666D80;">\1</td>', text, flags=re.DOTALL)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Updated font sizes securely!")
