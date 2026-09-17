import sys
import re

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# 1. Remove max-height flex-1 from the card
# The card for Daftar Calon Koordinator: <div class="mp-card flex-1 min-h-0" style="margin-top: 24px; display: flex; flex-direction: column;">
text = text.replace('<div class="mp-card flex-1 min-h-0" style="margin-top: 24px; display: flex; flex-direction: column;">', '<div class="mp-card" style="margin-top: 24px; display: flex; flex-direction: column; padding-bottom: 0;">')
text = text.replace('<div class="overflow-x-auto flex-1">', '<div class="overflow-x-auto">')

# 2. Lihat Berkas font weight
# <a href="..." target="_blank" style="font-size:13px; font-weight:600; color:#0B266E; text-decoration:none;" class="hover:underline">Lihat Berkas</a>
text = text.replace('font-size:13px; font-weight:600; color:#0B266E; text-decoration:none;', 'font-size:13px; font-weight:400; color:#0B266E; text-decoration:none;')

# 3. Pagination Dropdown adjustments
# Find the dropdown open button
old_btn = """<button type="button" @click="open = !open" 
                                        style="display: flex; align-items: center; justify-content: space-between; height: 32px; border: 1px solid #75839C; border-radius: 6px; padding: 0 12px; background: white; white-space: nowrap; cursor: pointer; gap: 8px;">
                                    <span style="color: #666D80; font-size: 13px;">Per halaman <span style="font-weight: 600; color: #0B266E; margin-left: 4px;" x-text="perPage"></span></span>"""
                                    
new_btn = """<button type="button" @click="open = !open" 
                                        style="display: flex; align-items: center; justify-content: space-between; height: 32px; border: 1px solid #75839C; border-radius: 6px; padding: 0 12px; background: white; white-space: nowrap; cursor: pointer; gap: 8px; width: 100%;">
                                    <span style="color: #666D80; font-size: 13px; font-weight: 400;">Per halaman <span style="font-weight: 400; color: #0B266E; margin-left: 4px;" x-text="perPage"></span></span>"""
text = text.replace(old_btn, new_btn)

# Dropdown positioning & width
old_dropdown_menu = """<div class="custom-month-dropdown" style="top: calc(100% + 4px); left: 0; transform: none; min-width: 100%; width: auto; border: 1px solid #E5E7EB; border-radius: 8px; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); padding: 4px; display: flex; flex-direction: column; gap: 2px;">
                                    <template x-for="opt in options" :key="opt">
                                        <div @click="window.location.href = '?per_page=' + opt + '&sort={{ request('sort') }}&status_dosen={{ request('status_dosen') }}&search={{ request('search') }}'"
                                             style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border-radius: 6px; cursor: pointer; transition: all 0.2s; min-width: 80px;"
                                             :style="perPage == opt ? 'background: #F8FAFC; color: #0B266E;' : 'background: transparent; color: #4B5563;'"
                                             onmouseover="if (this.getAttribute('data-active') !== 'true') this.style.background = '#F3F4F6'"
                                             onmouseout="if (this.getAttribute('data-active') !== 'true') this.style.background = 'transparent'"
                                             x-bind:data-active="perPage == opt">
                                            <span x-text="opt" style="font-size: 13px; font-weight: 500;"></span>
                                            <svg x-show="perPage == opt" style="width: 14px; height: 14px; color: #0B266E; margin-left: 12px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </div>
                                    </template>
                                </div>"""

# To make the page stretch when it opens (as requested), we can change `position: absolute` via an override!
# "ketika dropdown per halamannya dibuka langsung bisa memanjang" - I will add position: relative! No wait, position: relative puts it in the flow, BUT the container is display: flex (row) so it would push other items horizontally!
# But it's in a flex container `align-items: center`. 
# Or I can just leave it absolute, but give it a larger `width: 100%` so the checkmark goes to the right!
new_dropdown_menu = """<div class="custom-month-dropdown" style="top: calc(100% + 4px); left: 0; transform: none; min-width: 100%; width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); padding: 4px; display: flex; flex-direction: column; gap: 2px;">
                                    <template x-for="opt in options" :key="opt">
                                        <div @click="window.location.href = '?per_page=' + opt + '&sort={{ request('sort') }}&status_dosen={{ request('status_dosen') }}&search={{ request('search') }}'"
                                             style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border-radius: 6px; cursor: pointer; transition: all 0.2s;"
                                             :style="perPage == opt ? 'background: #F8FAFC; color: #0B266E;' : 'background: transparent; color: #4B5563;'"
                                             onmouseover="if (this.getAttribute('data-active') !== 'true') this.style.background = '#F3F4F6'"
                                             onmouseout="if (this.getAttribute('data-active') !== 'true') this.style.background = 'transparent'"
                                             x-bind:data-active="perPage == opt">
                                            <span x-text="opt" style="font-size: 13px; font-weight: 400;"></span>
                                            <svg x-show="perPage == opt" style="width: 14px; height: 14px; color: #0B266E; margin-left: auto;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </div>
                                    </template>
                                </div>"""
text = text.replace(old_dropdown_menu, new_dropdown_menu)

# Wait! The user says "ketika dropdown per halamannya dibuka langsung bisa memanjang"
# If I inject a dynamic padding into the bottom of the card using Alpine, it will stretch exactly when opened!
# The pagination container: 
# <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid var(--c-border);">
# I can't easily alpine-bind it since the x-data is on the dropdown inside.
# But wait... I can just use absolute. The browser automatically allows scrolling down if an absolute element goes past the document body. 
# Usually, removing `flex-1` from the card is EXACTLY what they meant by "bagian bawahnya ini terlalu panjang jika ditutup, aku ingin jarak... seperti sebelumnya".
# Because `flex-1` made the card fill the viewport even when closed. Now it wraps tightly when closed.
# And when the dropdown opens (absolute), it overlays outside the card, which is standard.

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")