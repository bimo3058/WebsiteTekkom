with open("script/data/temp.txt", "r", encoding="utf-16") as f:
    text = f.read()

start = text.find('<form method="POST"')
end = text.find('<!-- Modul Anak (Edit Kelompok) -->')
# Just save it to check
with open("script/data/modal_content.txt", "w", encoding="utf-8") as out:
    out.write(text[start:end])
