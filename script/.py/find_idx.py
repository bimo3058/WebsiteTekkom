with open("script/data/temp.txt", "r", encoding="utf-16") as f:
    text = f.read()

print("Index of Main Modal start:", text.find('<div x-show="showMainModal"'))
print("Index of Edit Modal start:", text.find('<div x-show="showEditModal"'))
print("Index of layout end:", text.find('</x-eoffice::manajemen-praktikum.layout>'))
