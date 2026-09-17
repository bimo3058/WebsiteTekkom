import re

with open(r"c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\nilai.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

m = re.search(r'(@foreach\s*\(\$moduls as \$modul\).*?)(?=@endforeach|\Z)', text, flags=re.DOTALL)
if m:
    print(m.group(1)[:1500])
else:
    print("Not found")
