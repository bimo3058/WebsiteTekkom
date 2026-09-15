import sys
import re

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

pattern = r'<button type="submit" class="mp-btn primary w-full flex items-center justify-center"[\s\S]*?</button>'

replacement = """<button type="submit" class="w-full flex items-center justify-center transition-colors hover:bg-blue-800"
                                style="background: #0B266E; color: #ffffff; border-radius: 6px; font-weight: 500; font-size: 12px; padding: 13px 10px; border: none; cursor: pointer; box-shadow: 0 4px 6px rgba(11,38,110,0.15);"
                                x-bind:disabled="!selectedUser"
                                :style="!selectedUser ? 'opacity: 0.5; cursor: not-allowed;' : ''">
                            Simpan
                        </button>"""

text = re.sub(pattern, replacement, text)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")