
text = open("Modules/EOffice/resources/views/manajemen-praktikum/admin/praktikum.blade.php").read()
print(text[text.find("Tambah Praktikum Baru"):text.find("Tambah Praktikum Baru")+5000])

