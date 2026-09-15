import re

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# 1. Restore the Left Card Header (Koordinator Aktif)
text = text.replace(
    '''<div class="mp-card-header">\n        <span class="mp-card-title">Koordinator Aktif</span>\n    </div>''',
    '''<div style="display: flex; align-items: center; justify-content: space-between; padding: 20px 24px 0 24px; margin-bottom: 20px;">
        <div>
            <div style="font-size: 16px; font-weight: 700; color: #0D0D12; display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#293C79" stroke-width="2.5" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Koordinator Aktif
            </div>
            <div style="font-size: 13px; color: #666D80; margin-top: 4px;">Informasi koordinator praktikum saat ini</div>
        </div>
    </div>'''
)

# 2. Restore the Right Card Header (Tunjuk Koordinator)
text = text.replace(
    '''<div class="mp-card-header">\n        <div style="display: flex; align-items: center; gap: 6px;">\n            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#293C79" stroke-width="2.5" stroke-linecap="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M16 11h6"/></svg>\n            <span class="mp-card-title">Tunjuk Koordinator</span>\n        </div>\n    </div>''',
    '''<div style="display: flex; align-items: center; justify-content: space-between; padding: 20px 24px 0 24px; margin-bottom: 20px;">
        <div>
            <div style="font-size: 16px; font-weight: 700; color: #0D0D12; display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#293C79" stroke-width="2.5" stroke-linecap="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M16 11h6"/></svg>
                Tunjuk Langsung Mahasiswa
            </div>
            <div style="font-size: 13px; color: #666D80; margin-top: 4px;">Pilih mahasiswa untuk penunjukan kadept instan</div>
        </div>
    </div>'''
)

# Also fix the weird extra padding that might be caused by this padding replacement
# The content divs inside the cards probably had padding:24px, we need to adjust them so we just have padding in the body
# Left content:
text = text.replace(
    '''<div style="padding: 24px; flex: 1; display: flex; flex-direction: column; justify-content: center;">''',
    '''<div style="padding: 0 24px 24px 24px; flex: 1; display: flex; flex-direction: column; justify-content: center;">'''
)
# Right content:
text = text.replace(
    '''<div style="padding: 24px; flex: 1; background: #FAFAFA;"''',
    '''<div style="padding: 0 24px 24px 24px; flex: 1; background: #FAFAFA;"'''
)

# And let's restore the SVG header for the Form Pendaftaran!
text = text.replace(
    '''<div class="mp-card-header">\n        <span class="mp-card-title">Konfigurasi & Buka Pendaftaran</span>\n    </div>''',
    '''<div style="display: flex; align-items: center; justify-content: space-between; padding: 20px 24px 0 24px; margin-bottom: 20px; border-bottom: 1px solid #DFE1E7; padding-bottom: 20px;">
        <div>
            <div style="font-size: 16px; font-weight: 700; color: #0D0D12; display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#293C79" stroke-width="2.5" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Konfigurasi & Buka Pendaftaran
            </div>
            <div style="font-size: 13px; color: #666D80; margin-top: 4px;">Kelola soal kuis pendaftaran atau syarat berkas tambahan untuk calon koordinator.</div>
        </div>
    </div>'''
)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print("Beautiful headers restored!")