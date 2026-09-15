import sys
import re

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# Replace the button block
pattern = r'<button type="submit" class="mp-btn primary w-full flex items-center justify-center"[\s\S]*?</button>'
replacement = """<button type="submit" class="mp-btn primary w-full flex items-center justify-center"
                                style="border-radius: 4px; font-weight: 500; font-size: 12px; padding: 13px 10px; box-shadow: 0 1px 3px rgba(11,38,110,0.1);"
                                x-bind:disabled="!selectedUser"
                                :style="!selectedUser ? 'opacity: 0.5; cursor: not-allowed; pointer-events: none;' : ''">
                            Simpan
                        </button>"""

text = re.sub(pattern, replacement, text)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")