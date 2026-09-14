import sys
import re

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# Replace the button block
pattern = r'<button type="submit" class="mp-btn primary w-full flex items-center justify-center gap-2"[\s\S]*?</button>'
replacement = """<button type="submit" class="mp-btn primary w-full flex items-center justify-center"
                                style="border-radius: 6px; font-weight: 500; font-size: 11px; padding: 13px 10px; box-shadow: 0 2px 8px rgba(11,38,110,0.1);"
                                x-bind:disabled="!selectedUser"
                                :style="!selectedUser ? 'opacity: 0.5; cursor: not-allowed; pointer-events: none;' : ''">
                            Simpan
                        </button>"""

text = re.sub(pattern, replacement, text)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")