import re

with open('Modules/EOffice/resources/views/manajemen-praktikum/koordinator/pendaftaran-asprak.blade.php', 'r', encoding='utf-8') as f:
    asprak = f.read()

# Extract form
form_match = re.search(r'<form action="[^"]*periode-pendaftaran\.store[^"]*" method="POST" class="p-6">.*?</form>', asprak, re.DOTALL)
form_content = form_match.group(0)

# Fix route
form_content = form_content.replace("route('eoffice.manprak.koordinator.periode-pendaftaran.store')", "route('eoffice.manprak.dosen.periode-pendaftaran.store')")

# Replace variable
form_content = form_content.replace('', '')

# Edit button
form_content = form_content.replace('Simpan Pengaturan Form & Buka Pendaftaran', 'Buka Periode Pendaftaran Koordinator')

# Edit form info text
form_content = form_content.replace('Konfigurasi soal kuis atau uji keterampilan', 'Konfigurasi soal kuis atau syarat daftar Koor')
form_content = form_content.replace('Mohon lengkapi parameter Kuis', 'Mohon lengkapi parameter formulir dan syarat berkas calon koordinator')

# Extract scripts
scripts_match = re.search(r'(<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>.*?</script>)$', asprak, re.DOTALL)
scripts_content = scripts_match.group(0) if scripts_match else ''
scripts_content = scripts_content.replace('', '')

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    target = f.read()

# Inject into target Form Section
target_html = target.replace('''<div style="padding:14px 18px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
                <div style="font-size:13px;color:#666D80;">Kelola soal kuis pendaftaran atau syarat berkas tambahan untuk calon koordinator.</div>
                <button type="button" class="mp-btn primary sm">
                    Atur Form
                </button>
            </div>''', f'''{form_content}''')

# Inject scripts at bottom
target_html = target_html.replace('</x-eoffice::manajemen-praktikum.layout>', f'''\n{scripts_content}\n</x-eoffice::manajemen-praktikum.layout>''')

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(target_html)
print("Done")