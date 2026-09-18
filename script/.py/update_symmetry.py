import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\modul.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# 1. Change items-start to items-stretch for the grid
text = text.replace('<div class="grid grid-cols-2 gap-[14px] items-start">', '<div class="grid grid-cols-2 gap-[14px] items-stretch">')

# 2. Add flex flex-col h-full to Tambah Modul's mp-card
text = text.replace('<div class="mp-card " style="padding:20px;">', '<div class="mp-card flex flex-col h-full" style="padding:20px;">')

# 3. Form flex-1 inside Tambah Modul
text = text.replace('<form method="POST" action="{{ route(\'eoffice.manprak.koor.modul.store\') }}" class="flex flex-col gap-3"', '<form method="POST" action="{{ route(\'eoffice.manprak.koor.modul.store\') }}" class="flex flex-col gap-3 flex-1"')

# 4. Form flex-1 inside Penugasan
# Wait, did Penugasan mp-card get h-full?
penugasan_card = '<div class="mp-card" style="padding:20px;">\n                <div style="font-weight:700;font-size:14px;color:#0D0D12;margin-bottom:16px;">Penugasan Asisten Modul</div>'
penugasan_card_rep = '<div class="mp-card flex flex-col h-full" style="padding:20px;">\n                <div style="font-weight:700;font-size:14px;color:#0D0D12;margin-bottom:16px;">Penugasan Asisten Modul</div>'
text = text.replace(penugasan_card, penugasan_card_rep)

# And Penugasan form
penugasan_form = '<form method="POST" action="{{ route(\'eoffice.manprak.koor.bagi-modul.store\') }}" \n                      class="flex flex-col gap-3" '
penugasan_form_rep = '<form method="POST" action="{{ route(\'eoffice.manprak.koor.bagi-modul.store\') }}" \n                      class="flex flex-col gap-3 flex-1" '
text = text.replace(penugasan_form, penugasan_form_rep)

# 5. Make buttons mt-auto instead of mt-2
text = text.replace('class="md w-full font-bold rounded-[8px] transition-colors duration-200 mt-2"', 'class="md w-full font-bold rounded-[8px] transition-colors duration-200 mt-auto"')

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Updated height symmetry for Tambah Modul and Penugasan Asisten form!")
