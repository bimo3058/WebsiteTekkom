
with open("Modules/EOffice/resources/views/manajemen-praktikum/koordinator/nilai.blade.php", "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace("<x-eoffice::manajemen-praktikum.koor-header :praktikum=\"$praktikum\" />", "<x-eoffice::manajemen-praktikum.asprak-header :praktikum=\"$praktikum\" activeTab=\"absensi\" />")

import re
content = re.sub(r"<a href=\"\{\{ route\('eoffice.manprak.koor.nilai.export-csv'\) \}\}\".*?<\/a>", "", content, flags=re.DOTALL)

content = content.replace("$allModuls as $modul", "$modulDiampu as $modul")

edit_btn = """
                            <div style="padding: 12px 20px; border-bottom: 1px solid #DFE1E7; display:flex; justify-content:flex-end; background:#F9FAFB;">
                                <a href="{{ route('eoffice.manprak.asprak.absensi.show', $modul->id) }}" class="mp-btn primary md">Edit Absensi & Nilai</a>
                            </div>
"""

content = content.replace("<div style=\"overflow-x:auto;\">", edit_btn + "                            <div style=\"overflow-x:auto;\">")

with open("Modules/EOffice/resources/views/manajemen-praktikum/asprak/absensi.blade.php", "w", encoding="utf-8") as f:
    f.write(content)

print("Patched successfully")

