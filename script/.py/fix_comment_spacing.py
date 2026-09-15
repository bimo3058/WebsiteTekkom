with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# 1. Fix the malformed comment - delete it entirely (no need to render it)
text = text.replace('{-- SECTION: FORM PENDAFTARAN --}', '')

# 2. Fix the margin-bottom of the Koordinator Aktif section wrapper to match top padding (24px)
# Currently the wrapper has margin-bottom: 24px
# The top content starts with 24px padding-top from the layout
# We want the spacing between Koordinator Aktif and Form Pendaftaran to be exactly 24px too
# Let's check - the wrapper already has margin-bottom: 24px, so this should be fine.
# But let's clean up extra blank lines from the deleted comment

import re
text = re.sub(r'\n{3,}', '\n\n', text)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print("fixed comment and cleaned spacing")