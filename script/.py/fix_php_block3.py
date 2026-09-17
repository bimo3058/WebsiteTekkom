import re

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

bad_block = r"""@php\s*= \\Modules\\EOffice\\Models\\PeriodePendaftaran::where\('praktikum_id', ->id \?\? ''\)\s*->where\('tipe', 'koordinator'\)\s*->where\('is_aktif', true\)\s*->first\(\);\s*@endphp"""

new_block = """@php
    $periodeAktif = \Modules\EOffice\Models\PeriodePendaftaran::where('praktikum_id', $praktikum?->id ?? '')
        ->where('tipe', 'koordinator')
        ->where('is_aktif', true)
        ->first();
@endphp"""

match = re.search(bad_block, text)
if match:
    text = text[:match.start()] + new_block + text[match.end():]
    with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
        f.write(text)
    print("fixed")
else:
    print("not found bad block")