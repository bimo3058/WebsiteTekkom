import re

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

pattern = r'<button type="submit" style="width: 100%; display: flex; align-items: center; justify-content: center; padding: 10px; border-radius: 8px; font-weight: 600; font-size: 13px; font-family: inherit; transition: opacity 0.2s; border: none;"\s*x-bind:disabled="!selectedUser"\s*:style=".*?">\s*Simpan\s*</button>'

new_btn = """<button type="submit" class="w-full flex items-center justify-center font-semibold text-[13px] rounded-[8px]"
        style="padding: 10px; border: none; transition: all 0.2s; outline: none;"
        x-bind:disabled="!selectedUser"
        :class="!selectedUser ? 'bg-[#F3F5F9] text-[#293C79] cursor-not-allowed' : 'bg-[#EEF1FA] text-[#0B266E] cursor-pointer hover:bg-[#E0E7FF]'">
    Simpan
</button>"""

text = re.sub(pattern, new_btn, text, flags=re.DOTALL)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print("button updated")