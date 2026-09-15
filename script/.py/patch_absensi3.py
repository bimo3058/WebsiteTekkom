
import re
with open("Modules/EOffice/resources/views/manajemen-praktikum/asprak/absensi.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# Let me check if @endif is missing.
if "@endif" not in text[text.find("</x-eoffice::manajemen-praktikum.layout>") - 20:]:
    new_text = text.replace("</x-eoffice::manajemen-praktikum.layout>", "    @endif\n</x-eoffice::manajemen-praktikum.layout>")
    with open("Modules/EOffice/resources/views/manajemen-praktikum/asprak/absensi.blade.php", "w", encoding="utf-8") as f:
        f.write(new_text)
    print("Fixed @endif")

