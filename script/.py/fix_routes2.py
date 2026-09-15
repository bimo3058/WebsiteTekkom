import sys

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

text = text.replace("route('dosen.pendaftaran-koor.approve', $p->id)", "route('eoffice.manprak.dosen.pendaftaran-koor.approve', $p->id)")
text = text.replace("route('dosen.pendaftaran-koor.reject', $p->id)", "route('eoffice.manprak.dosen.pendaftaran-koor.reject', $p->id)")


with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")