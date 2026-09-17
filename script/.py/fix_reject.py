with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

import re
tolak_pattern = r'''<form action="\{\{ route\('eoffice\.manprak\.dosen\.pendaftaran-koor\.approve', \$p->id\) \}\}" method="POST">\s*@csrf\s*<button type="submit" class="mp-btn sm" style="background:#DF1C41'''
tolak_repl = '''<form action="{{ route('eoffice.manprak.dosen.pendaftaran-koor.reject', $p->id) }}" method="POST">\n                                                    @csrf\n                                                    <button type="submit" class="mp-btn sm" style="background:#DF1C41'''
text = re.sub(tolak_pattern, tolak_repl, text, flags=re.DOTALL)

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)
print("fixed")