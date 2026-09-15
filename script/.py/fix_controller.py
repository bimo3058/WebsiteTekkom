import sys

file_path = "Modules/EOffice/app/Http/Controllers/ManajemenPraktikum/Dosen/PendaftaranKoorController.php"
with open(file_path, "r", encoding="utf-8") as f:
    text = f.read()

text = text.replace("$query->paginate(10)->withQueryString();", "$query->paginate(request('per_page', 10))->withQueryString();")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(text)

print("done")