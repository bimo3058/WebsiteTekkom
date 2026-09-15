import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\daftar-praktikan.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# 1. Remove the middle empty state
text = re.sub(r'<div x-show="filteredPraktikans\.length === 0" style="padding: 24px; text-align: center; color: #666D80; font-size: 13px;">\s*Kelompok <span x-text="activeKelompok"></span> belum memiliki anggota\.\s*</div>', '', text, flags=re.DOTALL)


# 2. Update the bottom empty state
old_empty = r'<div x-show="filteredPraktikans\.length === 0" style="padding:32px; text-align:center; color:#666D80; font-size:13px;">\s*<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8".*?</svg>\s*Semua mahasiswa sudah dimasukkan ke kelompok lain, atau pencarian tidak ditemukan\.\s*</div>'

new_empty = """<div x-show="filteredPraktikans.length === 0" style="padding:32px; text-align:center; color:#666D80; font-size:13px;">
                          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                          Kelompok <span x-text="activeKelompok"></span> belum memiliki anggota.
                      </div>"""

text = re.sub(old_empty, new_empty, text, flags=re.DOTALL)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Empty state updated!")
