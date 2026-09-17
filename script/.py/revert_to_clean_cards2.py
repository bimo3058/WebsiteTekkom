import re

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# I will just write a function to safely replace replacing `re.sub` with simple `.replace()` where possible, or just build the file from string blocks.

# Actually, I can just use python to replace everything!

# 1. Left Card Header
heavy_left = r'<div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 20px; border-bottom: 1px solid #DFE1E7; margin-bottom: 20px;">\s*<div>\s*<div style="font-size: 16px; font-weight: 700; color: #0D0D12; display: flex; align-items: center; gap: 8px;">\s*<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#293C79" stroke-width="2.5" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>\s*Koordinator Aktif\s*</div>\s*<div style="font-size: 13px; color: #666D80; margin-top: 4px;">Informasi koordinator praktikum saat ini</div>\s*</div>\s*</div>'
clean_left = """<div class="mp-card-header">
            <span class="mp-card-title">Koordinator Aktif</span>
        </div>"""
text = re.sub(heavy_left, clean_left, text)
text = text.replace(
    '{{-- Kiri: Koordinator Aktif --}}\n    <div style="flex: 1; min-width: 300px;">',
    '{{-- Kiri: Koordinator Aktif --}}\n    <div class="mp-card" style="flex: 1; min-width: 300px; padding: 0;">'
)
text = text.replace(
    '<div class="mp-card-header">\n            <span class="mp-card-title">Koordinator Aktif</span>\n        </div>',
    '<div class="mp-card-header">\n            <span class="mp-card-title">Koordinator Aktif</span>\n        </div>\n        <div style="padding: 24px;">'
)
text = text.replace(
    '{{-- Kanan: Tunjuk Koordinator Langsung --}}',
    '</div>\n\n    {{-- Kanan: Tunjuk Koordinator Langsung --}}'
)

# 2. Right Card Header
heavy_right = r'<div style="display: flex; align-items: center; justify-content: space-between; padding: 20px 24px 0 24px; margin-bottom: 20px; border-bottom: 1px solid #DFE1E7; padding-bottom: 20px;">\s*<div>\s*<div style="font-size: 16px; font-weight: 700; color: #0D0D12; display: flex; align-items: center; gap: 8px;">\s*<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#293C79" stroke-width="2.5" stroke-linecap="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M16 11h6"/></svg>\s*Tunjuk Koordinator\s*</div>\s*<div style="font-size: 13px; color: #666D80; margin-top: 4px;">Pilih mahasiswa untuk penunjukan kadept instan</div>\s*</div>\s*</div>'
clean_right = """<div class="mp-card-header">
            <div style="display: flex; align-items: center; gap: 6px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#293C79" stroke-width="2" stroke-linecap="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M16 11h6"/></svg>
                <span class="mp-card-title">Tunjuk Koordinator</span>
            </div>
        </div>"""
text = re.sub(heavy_right, clean_right, text)

text = text.replace(
    '''{{-- Kanan: Tunjuk Koordinator Langsung --}}\n    <div style="width: 380px; background: #FAFAFA; border: 1px solid #DFE1E7; border-radius: 12px; padding: 0;"''',
    '''{{-- Kanan: Tunjuk Koordinator Langsung --}}\n    <div class="mp-card" style="width: 380px; padding: 0;"'''
)
text = text.replace(
    '''<div style="position: relative; margin-bottom: 16px;">''',
    '''<label class="block text-[12px] font-semibold text-[#353849] mb-1">Cari Mahasiswa</label>\n            <div style="position: relative; margin-bottom: 16px;">'''
)
text = text.replace('Cari NIM atau Nama mahasiswa...', 'Ketik nama atau NIM...')

# 3. Right Card Button
heavy_btn = r'<button type="submit" class="mp-btn primary w-full flex items-center justify-center gap-2"\s*style="border-radius: 9999px; font-weight: 600; font-size: 13px; padding: 10px; box-shadow: 0 4px 12px rgba\(11,38,110,0\.2\);"\s*x-bind:disabled="!selectedUser"\s*:style="!selectedUser \? \'opacity: 0\.5; cursor: not-allowed;\' : \'\'">\s*<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2\.5" stroke-linecap="round" stroke-linejoin="round">\s*<line x1="12" y1="5" x2="12" y2="19"></line>\s*<line x1="5" y1="12" x2="19" y2="12"></line>\s*</svg> Simpan\s*</button>'
clean_btn = """<button type="submit" class="mp-btn w-full"
                    style="border-radius: 8px; font-weight: 600; font-size: 13px; padding: 10px; transition: all 0.2s;"
                    x-bind:disabled="!selectedUser"
                    :class="selectedUser ? 'primary' : ''"
                    :style="!selectedUser ? 'background: #F3F4F6; color: #0B266E; border: none; cursor: not-allowed;' : 'display: flex; display: flex; align-items: center; justify-content: center; gap: 4px; border: none;'">
                Simpan
            </button>"""
text = re.sub(heavy_btn, clean_btn, text)

# 4. Form Pendaftaran Koordinator Header
heavy_form_header = r'<div style="display: flex; align-items: center; justify-content: space-between; padding: 20px 24px 0 24px; margin-bottom: 20px; border-bottom: 1px solid #DFE1E7; padding-bottom: 20px;">\s*<div>\s*<div style="font-size: 16px; font-weight: 700; color: #0D0D12; display: flex; align-items: center; gap: 8px;">\s*<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#293C79" stroke-width="2.5" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>\s*Konfigurasi & Buka Pendaftaran\s*</div>\s*<div style="font-size: 13px; color: #666D80; margin-top: 4px;">Kelola soal kuis pendaftaran atau syarat berkas tambahan untuk calon koordinator.</div>\s*</div>\s*</div>'
clean_form_header = """<div class="mp-card-header">
        <span class="mp-card-title">Form Pendaftaran Koordinator</span>
    </div>"""
text = re.sub(heavy_form_header, clean_form_header, text)

# Now we need to remove the wrapper sec-head that says "Form Pendaftaran Koordinator"
# We match everything closely safely
for_re = r'<div class="sec-head" style="margin-top: 24px;">\s*<span class="sec-bar"></span>\s*<span class="sec-title">Form Pendaftaran Koordinator</span>\s*<span class="sec-rule"></span>\s*</div>'
text = re.sub(for_re, '', text)

# Wait, in the outer container, we had:
# <div class="mp-card flex-shrink-0" style="margin-bottom: 24px; padding: 24px; display: flex; flex-direction: row; justify-content: space-between; align-items: stretch; gap: 24px; flex-wrap: wrap;">
# We must REMOVE `class="mp-card"` from this wrapper because the Left and Right columns are NOW `.mp-card` themselves!
text = text.replace(
    '<div class="mp-card flex-shrink-0" style="margin-bottom: 24px; padding: 24px; display: flex; flex-direction: row; justify-content: space-between; align-items: stretch; gap: 24px; flex-wrap: wrap;">',
    '<div style="margin-bottom: 24px; display: flex; flex-direction: row; justify-content: space-between; align-items: stretch; gap: 24px; flex-wrap: wrap;">'
)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print("cards cleaned and restructured properly")