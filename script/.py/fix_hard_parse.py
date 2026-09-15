import os

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\nilai.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# I will find the EXACT string and replace it.
bad_header = '<x-eoffice::manajemen-praktikum.layout pageTitle="Rekap Nilai — {{ $praktikum ? $praktikum->\n    @if(isset($praktikum) && $praktikum)\n        <x-eoffice::manajemen-praktikum.koor-header :praktikum="$praktikum" />\n    @endif\nnama : \'Belum Ada Praktikum\' }}">\n'
good_header = '<x-eoffice::manajemen-praktikum.layout pageTitle="Rekap Nilai — {{ $praktikum ? $praktikum->nama : \'Belum Ada Praktikum\' }}">\n    @if(isset($praktikum) && $praktikum)\n        <x-eoffice::manajemen-praktikum.koor-header :praktikum="$praktikum" />\n    @endif\n'

text = text.replace(bad_header, good_header)

# If it still doesn't match because of weird characters like â€" ...
# Let's brute force it string by string

header_lines = text.split("\n")[:10]
if "nama : 'Belum Ada Praktikum'" in text:
    print("Found it!")

    # Let's reconstruct the file by just deleting the bad block
    # Find index of '<x-eoffice::manajemen-praktikum.layout pageTitle="Rekap Nilai'
    # And index of 'nama : \'Belum Ada Praktikum\' }}">'
    
    first_part = text.split('@php', 1)[1]
    
    # We will build the new text from scratch for the first few lines
    new_text = '<x-eoffice::manajemen-praktikum.layout pageTitle="Rekap Nilai — {{ $praktikum ? $praktikum->nama : \'Belum Ada Praktikum\' }}">\n'
    new_text += '    @if(isset($praktikum) && $praktikum)\n'
    new_text += '        <x-eoffice::manajemen-praktikum.koor-header :praktikum="$praktikum" />\n'
    new_text += '    @endif\n'
    new_text += '\n@php' + first_part
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(new_text)
    print("Fixed via string splitting.")
else:
    print("Could not find the target string.")

