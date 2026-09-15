with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# Fix the specific label style
text = text.replace(
    '<label class="block text-[12px] font-semibold text-[#353849] mb-1">Cari Mahasiswa</label>',
    '<label style="display: block; font-size: 13px; font-weight: 500; color: #666D80; margin-bottom: 6px;">Cari Mahasiswa</label>'
)

# Fix the padding of the form to include top padding
text = text.replace(
    '<form method="POST" action="{{ route(\'eoffice.manprak.dosen.tunjuk-koor\') }}" style="padding: 0 24px 24px 24px;">',
    '<form method="POST" action="{{ route(\'eoffice.manprak.dosen.tunjuk-koor\') }}" style="padding: 24px;">'
)

# Fix the wrapper of the Right Card to have #FAFAFA background
text = text.replace(
    '{{-- Kanan: Tunjuk Koordinator Langsung --}}\n    <div class="mp-card" style="width: 380px; padding: 0;"',
    '{{-- Kanan: Tunjuk Koordinator Langsung --}}\n    <div class="mp-card" style="width: 380px; padding: 0; background: #FAFAFA;"'
)

# Fix the mp-card-header in the Right Card to have a white background so it contrasts with FAFAFA
# It is located immediately after selectUser(user) {...} }">
target_header = """}
     }">
        <div class="mp-card-header">"""
repl_header = """}
     }">
        <div class="mp-card-header" style="background: #fff; border-bottom: 1px solid #DFE1E7;">"""
text = text.replace(target_header, repl_header)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print("right card fixed")