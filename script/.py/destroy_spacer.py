import sys
import re

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# Replace the spacer and the messed up closing tags
text = re.sub(
    r'\s*</div>\s*</div>\s*\{\{-- Spacer.*?</div>\s*</div>\s*@endif',
    '\n                        </div>\n                    </div>\n                </div>\n                @endif',
    text,
    flags=re.DOTALL
)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print('DELETED SPACER')