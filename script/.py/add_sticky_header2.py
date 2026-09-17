with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    orig = f.read()

import re

# Since I just replaced Page Header with the Sticky Header, the Sticky Header starts exactly after {{-- Sticky Header Wrapper --}}
# I will wrap everything from {{-- Sticky Header Wrapper --}} down to just before {{-- Info Alert --}} with @if($praktikum)

start_idx = orig.find('{{-- Sticky Header Wrapper --}}')
end_idx = orig.find('{{-- Info Alert --}}')

header_block = orig[start_idx:end_idx]

new_header = f"""@if($praktikum)
{header_block}
@else
{{-- Page Header --}}
<div class="mp-page-header">
    <div>
        <h1 class="mp-page-title">Seleksi Koordinator Praktikum</h1>
        <p class="mp-page-sub">Review pendaftar koordinator sesuai praktikum yang Anda ampu</p>
    </div>
</div>
@endif
"""

orig = orig[:start_idx] + new_header + orig[end_idx:]

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(orig)

print("wrapped in if")