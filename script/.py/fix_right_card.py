with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

import re

# 1. Update the wrapper to add background FAFAFA and standard mp-card styling but with overflow hidden
pattern_wrapper = r'\{\{-- Kanan: Tunjuk Koordinator Langsung --\}\}\s*<div class="mp-card" style="width: 380px; padding: 0;"'
repl_wrapper = r'''{{-- Kanan: Tunjuk Koordinator Langsung --}}
    <div class="mp-card flex-shrink-0" style="width: 380px; padding: 0; background: #FAFAFA; overflow: hidden;"'''
text = re.sub(pattern_wrapper, repl_wrapper, text)

# 2. Card Header (Needs background white to contrast with FAFAFA body)
pattern_header = r'<div class="mp-card-header">'
repl_header = r'<div class="mp-card-header" style="background: #fff;">'
# Wait! This will replace ALL mp-card-headers!
# Oh no, I should replace it specifically within the Kanan block.