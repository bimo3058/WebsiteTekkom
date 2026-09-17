import re

with open('Modules/EOffice/resources/views/manajemen-praktikum/koordinator/pendaftaran-asprak.blade.php', 'r', encoding='utf-8') as f:
    asprak = f.read()

# Extract form
form_match = re.search(r'<form action="[^"]*periode-pendaftaran\.store[^"]*" method="POST" class="p-6">.*?</form>', asprak, re.DOTALL)
form_content = form_match.group(0)

# Fix route
form_content = form_content.replace("route('eoffice.manprak.koordinator.periode-pendaftaran.store')", "route('eoffice.manprak.dosen.periode-pendaftaran.store')")

# Edit button
form_content = form_content.replace('Simpan Pengaturan Form & Buka Pendaftaran', 'Buka Periode Pendaftaran Koordinator')

# Edit form info text
form_content = form_content.replace('Konfigurasi soal kuis atau uji keterampilan', 'Konfigurasi soal kuis atau syarat daftar Koor')
form_content = form_content.replace('Mohon lengkapi parameter Kuis', 'Mohon lengkapi parameter formulir dan syarat berkas calon koordinator')

with open('script/data/extracted_form.txt', 'w', encoding='utf-8') as f:
    f.write(form_content)
    
scripts_match = re.search(r'(<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>.*?</script>)$', asprak, re.DOTALL)
scripts_content = scripts_match.group(0) if scripts_match else ''
with open('script/data/extracted_scripts.txt', 'w', encoding='utf-8') as f:
    f.write(scripts_content)
print("Form extracted limits")