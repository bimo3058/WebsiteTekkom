import re

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    base = f.read()

# Get Asprak form for 'judul, deskripsi, form'
with open('Modules/EOffice/resources/views/manajemen-praktikum/koordinator/pendaftaran-asprak.blade.php', 'r', encoding='utf-8') as f:
    asprak = f.read()

form_match = re.search(r'<form action="[^"]*periode-pendaftaran\.store[^"]*" method="POST" class="p-6">.*?</form>', asprak, re.DOTALL)
form_content = form_match.group(0)

# Fix route inside form
form_content = form_content.replace("route('eoffice.manprak.koordinator.periode-pendaftaran.store')", "route('eoffice.manprak.dosen.periode-pendaftaran.store')")
form_content = form_content.replace('', '')

# Format Form Section
form_section = f'''
{{{{-- Section: Tunjuk Koordinator Langsung --}}}}
<div class="sec-head" style="margin-top:24px;">
    <span class="sec-bar"></span>
    <span class="sec-title">Tunjuk Koordinator Langsung</span>
    <span class="sec-rule"></span>
</div>
<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Cari Mahasiswa</span>
    </div>
    <div style="padding:14px 18px;">
        <form method="POST" action="#" class="flex gap-2 flex-wrap">
            <input type="hidden" name="_token" value="{{{{ csrf_token() }}}}">
            <input type="text" name="search_nim" placeholder="Masukkan NIM atau Nama mahasiswa..." class="mp-input" style="width:300px;">
            <button type="button" class="mp-btn primary sm" style="font-weight:600;">
                Tunjuk Sebagai Koordinator
            </button>
        </form>
    </div>
</div>

{{{{-- Section: Buka Periode Pendaftaran --}}}}
<div class="sec-head" style="margin-top:24px;">
    <span class="sec-bar"></span>
    <span class="sec-title">Buka Periode Pendaftaran</span>
    <span class="sec-rule"></span>
</div>
<div class="mp-card flex-shrink-0">
    <div class="mp-card-header">
        <span class="mp-card-title">Formulir Persyaratan & Soal Kuis</span>
    </div>
    {form_content}
</div>
'''

# We inject the new sections right before the Filter Pendaftaran
filter_anchor = r'\{\{-- Section: Filter --\}\}'
base_modified = base.replace(filter_anchor, form_section + '\n' + filter_anchor)

# Extract scripts from asprak (for flatpickr and alpine quiz builder)
scripts_match = re.search(r'(<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>.*?</script>)$', asprak, re.DOTALL)
if scripts_match:
    scripts = scripts_match.group(0).replace('', '')
    base_modified = base_modified.replace('</x-eoffice::manajemen-praktikum.layout>', f'\\n{scripts}\\n</x-eoffice::manajemen-praktikum.layout>')

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(base_modified)

print("Done inserting new columns")