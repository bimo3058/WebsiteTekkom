import sys
import re

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# Replace the button block
pattern = r'<button type="submit" class="mp-btn primary w-full flex items-center justify-center gap-2"[\s\S]*?</button>'
replacement = """<button type="submit" class="mp-btn primary w-full flex items-center justify-center gap-2"
                                style="border-radius: 9999px; font-weight: 600; font-size: 14px; padding: 10px; box-shadow: 0 4px 12px rgba(11,38,110,0.15); letter-spacing: 0.02em;"
                                x-bind:disabled="!selectedUser"
                                :style="!selectedUser ? 'opacity: 0.5; cursor: not-allowed; pointer-events: none;' : ''">
                            <span style="font-size: 18px; font-weight: 400; line-height: 1;">+</span> Simpan
                        </button>"""

text = re.sub(pattern, replacement, text)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")