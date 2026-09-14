import re

with open("temp.txt", "r", encoding="utf-16") as f:
    text = f.read()

# 1. Remove Button "Kelola Kelompok & Shift" from header
# Look for <button @click="openMainModal()"...>...</button>
btn_rm = re.sub(r'\{\{-- Updated Kelola Kelompok Icon --\}\}.*?Kelola Kelompok &amp; Shift\s*</button>', '', text, flags=re.DOTALL)

# 2. Extract the content of showMainModal 
# We'll build the new section explicitly using the parts we found.
part_form = re.search(r'(<form action="\{\{ route\(\'eoffice\.manprak\.koor\.praktikan\.settings\'\).*?</form>)', text, flags=re.DOTALL).group(1)

part_kelompok = re.search(r'\{\{-- Bottom Section: Daftar Kelompok \(Manual Plot\) --\}\}(.*?)\{\{-- Tombol Kosongkan Plotting', text, flags=re.DOTALL).group(1)

part_hapus = re.search(r'\{\{-- Tombol Kosongkan Plotting diletakkan di paling bawah --\}\}.*?(<form action="\{\{ route\(\'eoffice\.manprak\.koor\.praktikan\.reset-plot\'\).*?</form>)', text, flags=re.DOTALL).group(1)

# Now remove the entire <template x-teleport="body"> ... showMainModal ... </template>
new_text = re.sub(r'<template x-teleport="body">\s*<div x-show="showMainModal".*?</template>', '', btn_rm, flags=re.DOTALL)

# Now we need to append the new section at the end of the <div class="mp-card"> (which is the table container)
# Actually the table container `<div class="mp-card">` ends right before `<template x-teleport="body">` (the Edit popup) or where showMainModal was.
# The table is in `<div class="mp-card">`. Let's insert the new card right after it.
# We can find the closing `</div>\n    </div>\n</x-eoffice::...`

# Let's insert the new section right before the remaining `<template x-teleport="body">` (which is showEditModal)
new_section = f"""
    {{-- NEW SECTION: Kelola Kelompok & Shift --}}
    <div class="mp-card" style="margin-top: 24px; margin-bottom: 24px;">
        <div style="padding: 24px;">
            <div style="font-weight:700; font-size:16px; color:#0D0D12; margin-bottom:16px;">Kelola Kelompok & Shift</div>
            {part_form}
        </div>
        
        <div style="border-top:1px dashed #DFE1E7; margin:0 24px;"></div>
        
        <div style="padding: 24px;">
            {part_kelompok.strip()}

            <div style="margin-top:24px;">
                {part_hapus}
            </div>
        </div>
    </div>
"""

new_text = new_text.replace('{{-- MODAL SUB: EDIT ANGGOTA KELOMPOK                      --}}', new_section + '\n\n    {{-- MODAL SUB: EDIT ANGGOTA KELOMPOK                      --}}')


with open(r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\daftar-praktikan.blade.php', "w", encoding="utf-8") as f:
    f.write(new_text)

print("Done generating new view layout.")
