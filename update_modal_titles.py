import os

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\modul.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# 1. Edit Modul -> Detail Modul
text = text.replace('<div class="font-bold text-[16px] text-[#0D0D12]">Edit Modul</div>', '<div class="font-bold text-[16px] text-[#0D0D12]">Detail Modul</div>')

# 2. Asisten Praktikum Modul -> Asisten Modul
text = text.replace('<label class="block text-[12px] font-semibold text-[#353849] mb-1">Asisten Praktikum Modul</label>', '<label class="block text-[12px] font-semibold text-[#353849] mb-1">Asisten Modul</label>')

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Updated modal titles successfully!")
