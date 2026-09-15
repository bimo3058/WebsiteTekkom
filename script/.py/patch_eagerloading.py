import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\app\Http\Controllers\ManajemenPraktikum\Koordinator\NilaiController.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

text = text.replace("Modul::where('praktikum_id', $praktikum->id)", "Modul::with(['asprak.user'])->where('praktikum_id', $praktikum->id)")
    
with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Eager Loading added.")
