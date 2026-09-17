import re

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

pattern = r'<button type="submit"\s*style="width: 100%; padding: 12px; border-radius: 8px; font-weight: 600; font-size: 13px; border: none; transition: all 0.2s; font-family: inherit; display: block; text-align: center;"\s*x-bind:disabled="!selectedUser"\s*:style=".*?">\s*Simpan\s*</button>'

new_btn = """<button type="submit" 
        style="width: 100%; padding: 10px; border-radius: 8px; font-weight: 600; font-size: 13px; border: none; transition: all 0.2s; font-family: inherit; display: flex; align-items: center; justify-content: center;"
        x-bind:disabled="!selectedUser"
        x-bind:style="{ backgroundColor: !selectedUser ? '#F3F5F9' : '#EEF1FA', color: !selectedUser ? '#293C79' : '#0B266E', cursor: !selectedUser ? 'not-allowed' : 'pointer' }">
    Simpan
</button>"""

text = re.sub(pattern, new_btn, text, flags=re.DOTALL)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print("button fully fixed")