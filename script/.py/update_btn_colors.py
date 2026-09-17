with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

target = "x-bind:style=\"{ backgroundColor: !selectedUser ? '#F3F5F9' : '#EEF1FA', color: !selectedUser ? '#293C79' : '#0B266E', cursor: !selectedUser ? 'not-allowed' : 'pointer' }\""
repl = "x-bind:style=\"{ backgroundColor: !selectedUser ? '#F3F5F9' : '#0B266E', color: !selectedUser ? '#293C79' : '#FFFFFF', cursor: !selectedUser ? 'not-allowed' : 'pointer' }\""

text = text.replace(target, repl)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print("done updating btn colors")