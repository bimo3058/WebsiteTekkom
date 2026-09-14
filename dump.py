with open("temp.txt", "r", encoding="utf-16") as f:
    text = f.read()

start = 20534
end = 30208
with open("modal_main.txt", "w", encoding="utf-8") as out:
    out.write(text[start:end])
