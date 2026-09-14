with open("temp.txt", "r", encoding="utf-16") as f:
    text = f.read()

start = text.find('route(\'eoffice.manprak.koor.praktikan.generate\')')
# move back to find <form
start = text.rfind('<form', 0, start)
end = text.find('<!-- Modul Anak (Edit Kelompok) -->')
# Just save it to check
with open("modal_content.txt", "w", encoding="utf-8") as out:
    out.write(text[start:end])
