import re

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

btn_pattern = r'<button type="submit" class="mp-btn w-full"(.*?)</button>'
new_btn = """<button type="submit" style="width: 100%; border: none; background: #F1F5F9; color: #1E3A8A; padding: 10px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: none; display: flex; align-items: center; justify-content: center;"
        x-bind:disabled="!selectedUser"
        :style="!selectedUser ? 'background: #F8FAFC; color: #64748B; cursor: not-allowed;' : 'background: #EEF2FF; color: #1E3A8A; cursor: pointer;'">
    Simpan
</button>"""

text = re.sub(btn_pattern, new_btn, text, flags=re.DOTALL)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print("done")