import sys
import re

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

pattern = r'<button type="submit" class="w-full flex items-center justify-center transition-colors"[\s\S]*?</button>'

replacement = """<button type="submit" class="w-full flex items-center justify-center transition-colors"
                                :style="!selectedUser 
                                    ? 'background: #EEF2F6; color: #0B266E; border-radius: 8px; font-weight: 600; font-size: 13px; padding: 10px; border: none; cursor: not-allowed;'
                                    : 'background: #0B266E; color: #ffffff; border-radius: 8px; font-weight: 600; font-size: 13px; padding: 10px; border: none; cursor: pointer; box-shadow: 0 4px 6px rgba(11,38,110,0.15);'"
                                x-bind:disabled="!selectedUser">
                            Simpan
                        </button>"""

text = re.sub(pattern, replacement, text)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")