import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\modul.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

old_str = '<input type="hidden" name="urutan" value="{{ ($moduls->max(\'urutan\') ?? 0) + 1 }}">'
new_str = """<div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Urutan <span class="text-red-500">*</span></label>
                <input type="number" name="urutan" min="1" value="{{ ($moduls->max('urutan') ?? 0) + 1 }}" required class="mp-input w-full">
            </div>"""

text = text.replace(old_str, new_str)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Restored urutan column successfully!")
