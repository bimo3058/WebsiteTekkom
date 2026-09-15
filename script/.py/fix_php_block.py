import os

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\pendaftaran-asprak.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

broken_php = """                            @php
                                 = $p->user?->email ?? '';
                                 = explode('@', )[0];
                                if (empty())  = 'â€”';
                            @endphp
                            <td style="padding:12px 16px; font-size:13px; color:#4B5563;">
                                {{  }}
                            </td>"""

fixed_php = """                            @php
                                $emailStr_c = $p->user?->email ?? '';
                                $nim_c = explode('@', $emailStr_c)[0];
                                if (empty($nim_c)) $nim_c = '—';
                            @endphp
                            <td style="padding:12px 16px; font-size:13px; color:#4B5563;">
                                {{ $nim_c }}
                            </td>"""

if broken_php in text:
    text = text.replace(broken_php, fixed_php)
else:
    # Just in case the whitespace or characters don't match exactly
    import re
    patt = r'@php\s*=\s*\$p->user\?->email \?\? \'\';\s*=\s*explode\(\'@\', \)\[0\];\s*if\s*\(empty\(\)\)\s*=\s*.*?;\s*@endphp\s*<td.*?>\s*\{\{  \}\}\s*</td>'
    text = re.sub(patt, fixed_php.strip(), text, flags=re.DOTALL)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Fixed empty variable bug!")
