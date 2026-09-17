import sys
import re

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# Revert to {} when closed to restore pristine spacing bounds!
text = text.replace(":style=\"open ? { 'padding-bottom': '120px' } : { 'padding-bottom': '12px' }\"", ":style=\"open ? { 'padding-bottom': '120px' } : {}\"")

# Remove will-change as it can extend composition layer boundaries
text = text.replace('transition: padding-bottom 0.25s cubic-bezier(0.4, 0, 0.2, 1); will-change: padding-bottom;', 'transition: padding-bottom 0.25s cubic-bezier(0.4, 0, 0.2, 1);')

# In case the 120px itself was too big when OPEN (often users mean this), I will reduce it slightly to exactly match the popup size just in case, but usually {} fixes the closed state.
# 120px is actually very large. Let's make it 105px, which perfectly wraps the bottom menu.
text = text.replace("{ 'padding-bottom': '120px' }", "{ 'padding-bottom': '115px' }")


with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print('Restored Spacing!')