import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\modul.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# 1. Remove the right div (modul badge)
text = re.sub(r'<div class="right">\s*<span class="mp-badge neutral sm">\{\{ \$moduls->count\(\) \}\} modul</span>\s*</div>', '', text)

# 2. Update the thead
old_thead = """                    <tr style="background:#F9FAFB;">
                        <th class="mp-th text-left" style="padding:10px 16px;">Modul</th>

                        <th class="mp-th text-left" style="padding:10px 16px;">Asisten Praktikum</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">Konten</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">Aksi</th>
                    </tr>"""

new_thead = """                    <tr style="background:#F9FAFB;">
                        <th class="mp-th text-center" style="padding:10px 16px; width:60px;">NO</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">MODUL</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">ASISTEN</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">KONTEN</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">AKSI</th>
                    </tr>"""
text = text.replace(old_thead, new_thead)

# 3. Update the tbody inner rows
old_td_modul = """                        <td style="padding:12px 16px;">
                            <div style="font-weight:600;color:#0D0D12;">{{ $m->urutan }}. {{ $m->nama }}</div>
                            <div style="font-size:11px;color:#666D80;">{{ $m->jadwal_minggu ?? 'Jadwal belum diisi' }}</div>
                        </td>"""

new_td_modul = """                        <td style="padding:12px 16px; text-align:center; font-weight:600; color:#666D80; font-size:12px;">
                            {{ $m->urutan }}
                        </td>
                        <td style="padding:12px 16px;">
                            <div style="font-weight:600; color:#0D0D12; font-size:13px;">{{ $m->nama }}</div>
                        </td>"""
text = text.replace(old_td_modul, new_td_modul)

# 4. Update the tbody empty state span colspan from 5, Oh wait it was 4, I need to make it 5!
text = text.replace('<td colspan="4">', '<td colspan="5">')
text = text.replace('<td colspan="3">', '<td colspan="5">')

# 5. Button styles
old_btn_detail = '<a href="{{ route(\'eoffice.manprak.koor.modul.show\', $m->id) }}" class="mp-btn primary sm" style="text-decoration:none;">Detail</a>'
new_btn_detail = '<a href="{{ route(\'eoffice.manprak.koor.modul.show\', $m->id) }}" class="mp-btn primary sm" style="text-decoration:none; font-size:11px; padding-top:6px; padding-bottom:6px;">Detail</a>'
text = text.replace(old_btn_detail, new_btn_detail)

old_btn_hapus = '<button class="mp-btn destructive sm">Hapus</button>'
new_btn_hapus = '<button class="mp-btn destructive sm" style="font-size:11px; padding-top:6px; padding-bottom:6px;">Hapus</button>'
text = text.replace(old_btn_hapus, new_btn_hapus)

# Fix any stray colspans
text = text.replace('<td colspan="5">\n                            <div style="padding:48px;text-align:center;">', '<td colspan="5">\n                            <div style="padding:48px;text-align:center;">')

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Updated table successfully!")
