import re

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# First, revert the previous button replacement if it exists
text = re.sub(r'<button type="submit" style="width: 100%; border: none;(.*?)</button>', '$$BUTTON$$', text, flags=re.DOTALL)

# In case it didn't match because of something, also search for the mp-btn style
text = re.sub(r'<button type="submit" class="mp-btn w-full"(.*?)</button>', '$$BUTTON$$', text, flags=re.DOTALL)

new_btn = """<button type="submit" style="width: 100%; display: flex; align-items: center; justify-content: center; padding: 10px; border-radius: 8px; font-weight: 600; font-size: 13px; font-family: inherit; transition: opacity 0.2s; border: none;"
    x-bind:disabled="!selectedUser"
    :style="!selectedUser ? 'background: #F3F4F6; color: #0B266E; cursor: not-allowed; opacity: 0.8;' : 'background: #EEF1FA; color: #0B266E; cursor: pointer; opacity: 1;'">
    Simpan
</button>"""

text = text.replace('$$BUTTON$$', new_btn)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print("done button 2")