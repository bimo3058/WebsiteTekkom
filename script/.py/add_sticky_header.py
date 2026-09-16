with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/praktikum-detail.blade.php', 'r', encoding='utf-8') as f:
    text_praktikum = f.read()

import re
header_match = re.search(r'(\{\{-- Sticky Header Wrapper --\}\}.*?)\{\{-- Content: Pengumuman --\}\}', text_praktikum, re.DOTALL)
header_html = header_match.group(1)

# Now we need to modify the active state of the Tabs in header_html
# In praktikum-detail.blade.php, 'Pengumuman' was active:
# <a href="#" style="padding: 12px 24px; font-weight: 600; font-size: 14px; color: #293C79; text-decoration: none; border-bottom: 2px solid #293C79;">Pengumuman</a>
# We change Pengumuman to a standard link, and change Seleksi Koordinator to active!

header_html = header_html.replace(
    '''<a href="#" style="padding: 12px 24px; font-weight: 600; font-size: 14px; color: #293C79; text-decoration: none; border-bottom: 2px solid #293C79;">Pengumuman</a>''',
    '''<a href="{{ route('eoffice.manprak.dosen.praktikum.show', $praktikum->id) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Pengumuman</a>'''
)

header_html = header_html.replace(
    '''<a href="{{ route('eoffice.manprak.dosen.pendaftaran-koor.index', ['praktikum_id' => $praktikum->id]) }}" style="padding: 12px 24px; font-weight: 500; font-size: 14px; color: var(--c-fg-muted); text-decoration: none;">Seleksi Koordinator</a>''',
    '''<a href="#" style="padding: 12px 24px; font-weight: 600; font-size: 14px; color: #293C79; text-decoration: none; border-bottom: 2px solid #293C79;">Seleksi Koordinator</a>'''
)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text_koor = f.read()

# Replace {{-- Page Header --}} ... </div> completely
page_header_pattern = r'\{\{-- Page Header --\}\}.*?</div>\s*</div>'
text_koor = re.sub(page_header_pattern, header_html, text_koor, flags=re.DOTALL)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text_koor)

print("added sticky header")