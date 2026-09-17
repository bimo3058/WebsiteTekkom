import re

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

pattern = r'<button type="submit" class="w-full flex items-center justify-center font-semibold text-\[13px\] rounded-\[8px\]"\s*style="padding: 10px; border: none; transition: all 0.2s; outline: none;"\s*x-bind:disabled="!selectedUser"\s*:class=".*?">\s*Simpan\s*</button>'

new_btn = """<button type="submit" 
        style="width: 100%; padding: 12px; border-radius: 8px; font-weight: 600; font-size: 13px; border: none; transition: all 0.2s; font-family: inherit; display: block; text-align: center;"
        x-bind:disabled="!selectedUser"
        :style="!selectedUser ? 'background-color: #F3F5F9; color: #293C79; cursor: not-allowed;' : 'background-color: #EEF1FA; color: #0B266E; cursor: pointer;'">
    Simpan
</button>"""

text = re.sub(pattern, new_btn, text, flags=re.DOTALL)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print("button updated again")