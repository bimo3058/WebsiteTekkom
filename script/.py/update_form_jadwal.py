import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\pendaftaran-asprak.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# Replace the label
text = text.replace(
    '<label class="block text-[11px] font-semibold text-[#353849] mb-2">Jadwal Praktikum <span class="text-[#808897] font-normal">(Pilihan yang akan tampil)</span></label>',
    '<label class="block text-[11px] font-semibold text-[#353849] mb-2">Jadwal <span class="text-[#808897] font-normal">(Pilih hari luang)</span></label>'
)

# Merge the rows
# The original structure:
# <div class="flex flex-col gap-2">
#     <div class="flex gap-6">
#          ... Senin to Kamis
#     </div>
#     <div class="flex gap-6 mt-1">
#          ... Jumat to Minggu
#     </div>
# </div>

# We will regex replace the whole block
pattern = r'<div class="flex flex-col gap-2">\s*<div class="flex gap-6">(.*?)</div>\s*<div class="flex gap-6 mt-1">(.*?)</div>\s*</div>'
match = re.search(pattern, text, re.DOTALL)
if match:
    row1 = match.group(1).strip()
    row2 = match.group(2).strip()
    new_html = f'<div class="flex gap-6 flex-wrap">\n                                    {row1}\n                                    {row2}\n                                </div>'
    text = text[:match.start()] + new_html + text[match.end():]
    
with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Updated Jadwal form fields successfully!")
