import os
import re

# 1. Update daftar-praktikan.blade.php
filepath_dp = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\daftar-praktikan.blade.php'
with open(filepath_dp, 'r', encoding='utf-8') as f:
    text_dp = f.read()

# Blur backdrop in main modal
text_dp = text_dp.replace(
    'class="fixed inset-0 bg-[#0D0D12]/40 transition-opacity"',
    'class="fixed inset-0 bg-[#0D0D12]/40 backdrop-blur-sm transition-opacity"'
)

# Blur backdrop in sub modal
text_dp = text_dp.replace(
    'class="fixed inset-0 bg-[#0D0D12]/60 transition-opacity"',
    'class="fixed inset-0 bg-[#0D0D12]/40 backdrop-blur-sm transition-opacity"'
)

# Replace Metode Pembagian logic to use the custom select component Let's be careful with the exact string.
old_select = r'<select name="method" required class="mp-input" style="width:100%; height:38px; appearance:auto; background:#FFF; cursor:pointer;">\s*<option value="urutan_sistem">Berurutan</option>\s*<option value="acak">Acak</option>\s*</select>'
new_select = r'''<x-eoffice::manajemen-praktikum.ui.select name="method" :options="[['value' => 'urutan_sistem', 'label' => 'Berurutan'], ['value' => 'acak', 'label' => 'Acak']]" selected="urutan_sistem" />'''

text_dp = re.sub(old_select, new_select, text_dp)

# Text change
text_dp = text_dp.replace(
    'Klik Edit (Pensil) pada salah satu kelompok untuk mengisi/merubah anggota.',
    'Klik ikon pensil pada kelompok untuk mengubah anggota.'
)

with open(filepath_dp, 'w', encoding='utf-8') as f:
    f.write(text_dp)


# 2. Update ui.select.blade.php
filepath_sel = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\components\manajemen-praktikum\ui\select.blade.php'
with open(filepath_sel, 'r', encoding='utf-8') as f:
    text_sel = f.read()

# Find the arrow logic
# <svg class="w-4 h-4 transition-transform duration-200 flex-shrink-0"
text_sel = text_sel.replace(
    '<svg class="w-4 h-4 transition-transform duration-200 flex-shrink-0"',
    '<svg class="w-4 h-4 transition-transform duration-200 flex-shrink-0 mr-2.5"'
)

with open(filepath_sel, 'w', encoding='utf-8') as f:
    f.write(text_sel)


print("Done updating files.")
