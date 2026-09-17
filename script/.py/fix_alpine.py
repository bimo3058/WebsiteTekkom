import sys

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# 1. Change title
text = text.replace('<span class="mp-card-title">Tunjuk Langsung Mahasiswa</span>', '<span class="mp-card-title">Tunjuk Koordinator</span>')

# 2. Change hidden input from user_id to nim
text = text.replace('<input type="hidden" name="user_id" :value="selectedUser ? selectedUser.id : \'\'">', '<input type="hidden" name="nim" :value="selectedUser ? selectedUser.id : \'\'">')

# 3. Alpine watch query logic fix
text = text.replace('if(this.selectedUser && this.selectedUser.name !== value) {', 'if(this.selectedUser && this.selectedUser.text !== value) {')

# 4. selectUser logic fix
text = text.replace('this.query = user.name;', 'this.query = user.text;')

# 5. Dropdown template fix
old_template = """<div style="font-size: 13px; font-weight: 700; color: #0D0D12;" x-text="user.name"></div>
                                                <div style="font-size: 11px; color: #666D80; margin-top: 2px;" x-text="(user.student ? user.student.student_number : '') + ' · ' + user.email"></div>"""
new_template = """<div style="font-size: 13px; font-weight: 700; color: #0D0D12;" x-text="user.text.split(' - ')[0]"></div>
                                                <div style="font-size: 11px; color: #666D80; margin-top: 2px;" x-text="user.id"></div>"""
text = text.replace(old_template, new_template)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")