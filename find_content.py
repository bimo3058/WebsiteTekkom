with open("temp.txt", "r", encoding="utf-16") as f:
    text = f.read()

start = text.find('<div x-show="showMainModal"')
end = text.find('<div x-show="showEditModal"')
print(text[start:start+1000])

