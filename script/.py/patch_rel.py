filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\nilai.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

text = text.replace("$modul->asistens->pluck", "$modul->asprak->pluck")

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Relationship fixed.")
