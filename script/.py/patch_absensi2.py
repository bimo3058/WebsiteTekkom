
import re
with open("Modules/EOffice/resources/views/manajemen-praktikum/asprak/absensi.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# We want to remove from {{-- Publikasi Nilai Section --}} up to its end.
# The section ends right before @endif of the @if(!$praktikum) or at the end of the file.
start_idx = text.find("{{-- Publikasi Nilai Section --}}")

if start_idx != -1:
    end_idx = text.find("</x-eoffice::manajemen-praktikum.layout>")
    # We want to just remove everything from start_idx up to end_idx
    new_text = text[:start_idx] + "\n" + text[end_idx:]
    with open("Modules/EOffice/resources/views/manajemen-praktikum/asprak/absensi.blade.php", "w", encoding="utf-8") as f:
        f.write(new_text)
    print("Publikasi Nilai removed.")
else:
    print("Publikasi Nilai not found.")

