import sys
import re

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    orig = f.read()

with open('script/data/extracted_table.txt', 'r', encoding='utf-8') as f:
    table_content = f.read()

with open('script/data/extracted_form.txt', 'r', encoding='utf-8') as f:
    form_content = f.read()

with open('script/data/extracted_scripts.txt', 'r', encoding='utf-8') as f:
    scripts_content = f.read()

# 1. Update Simpan button with SVG inside Tunjuk Langsung
orig = orig.replace('''<button type="submit" class="mp-btn primary w-full flex items-center justify-center gap-2"
                                style="border-radius: 9999px; font-weight: 600; font-size: 14px; padding: 10px; box-shadow: 0 4px 12px rgba(11,38,110,0.15); letter-spacing: 0.02em;"
                                x-bind:disabled="!selectedUser"
                                :style="!selectedUser ? 'opacity: 0.5; cursor: not-allowed; pointer-events: none;' : ''">
                            <span style="font-size: 18px; font-weight: 400; line-height: 1;">+</span> Simpan
                        </button>''', 
                        '''<button type="submit" class="mp-btn primary w-full flex items-center justify-center gap-2"
                                style="border-radius: 9999px; font-weight: 600; font-size: 13px; padding: 10px; box-shadow: 0 4px 12px rgba(11,38,110,0.2);"
                                x-bind:disabled="!selectedUser"
                                :style="!selectedUser ? 'opacity: 0.5; cursor: not-allowed; pointer-events: none;' : ''">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg> Simpan
                        </button>''')

# 2. Extract Filter Form
filter_match = re.search(r'<form method="GET" class="flex gap-2 flex-wrap">.*?</form>', orig, re.DOTALL)
filter_form_html = filter_match.group(0)

# 3. Extract Pagination
pagination_idx = orig.find('@if(isset($pendaftaran) && method_exists($pendaftaran, \'hasPages\')')
if pagination_idx != -1:
    pagination_html = orig[pagination_idx : orig.find('</div>\n\n</x-eoffice')]
else:
    pagination_html = ''

# 4. Remove everything from {{-- Section: Filter --}} to the end of the file except layout tag
orig = orig[:orig.find('{{-- Section: Filter --}}')]

# We will inject the new layout directly after the Koordinator Aktif block
new_layout = f"""{{-- SECTION: FORM PENDAFTARAN --}}
<div class="sec-head" style="margin-top: 24px;">
    <span class="sec-bar"></span>
    <span class="sec-title">Form Pendaftaran Koordinator</span>
    <span class="sec-rule"></span>
</div>
<div class="mp-card flex-shrink-0" style="margin-bottom: 24px;">
    <div class="mp-card-header">
        <span class="mp-card-title">Konfigurasi & Buka Pendaftaran</span>
    </div>
    {form_content}
</div>

{{-- SECTION: DAFTAR CALON KOORDINATOR --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Calon Koordinator</span>
    <span class="sec-rule"></span>
    <span class="mp-badge navy sm">{{{{ $pendaftaran->total() }}}} pendaftar</span>
</div>

<div class="mp-card flex-1 min-h-0" style="display: flex; flex-direction: column; padding-bottom: 0;">
    {{-- Pencarian & Filter --}}
    <div style="padding:14px 18px; border-bottom: 1px solid var(--c-border); background: #F8FAFC; border-radius: 12px 12px 0 0;">
        {filter_form_html}
    </div>
    
    <div class="overflow-x-auto">
        {table_content}
    </div>
    
    {pagination_html}
</div>
"""

orig = orig + new_layout + f'\n{scripts_content}\n</x-eoffice::manajemen-praktikum.layout>'

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(orig)

print("Master rebuild complete!")