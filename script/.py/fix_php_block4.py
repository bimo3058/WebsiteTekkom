with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

php_block = """{{-- SECTION: FORM PENDAFTARAN --}}
@php
    $periodeAktif = \Modules\EOffice\Models\PeriodePendaftaran::where('praktikum_id', $praktikum?->id ?? '')
        ->where('jenis', 'koordinator')
        ->where('is_aktif', true)
        ->first();
@endphp
"""

text = text.replace('{{-- SECTION: FORM PENDAFTARAN --}}\n', php_block)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)
print("fixed php")