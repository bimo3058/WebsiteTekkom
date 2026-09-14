import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\pendaftaran-asprak.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

text = text.replace('@forelse( as )', '@forelse($pendaftaran as $p)')
text = text.replace('{{ ->iteration }}', '{{ $loop->iteration }}')
text = text.replace('{{ strtoupper(substr(->user?->name ?? \'M\', 0, 2)) }}', '{{ strtoupper(substr($p->user?->name ?? \'M\', 0, 2)) }}')
text = text.replace('{{ ->user?->name ?? \'--\' }}', '{{ $p->user?->name ?? \'--\' }}')
text = text.replace('{{ ->user?->email }}', '{{ $p->user?->email }}')

text = text.replace('$emailStr_c = ->user?->email ?? \'\';', '$emailStr_c = $p->user?->email ?? \'\';')
text = text.replace('{{ number_format(->ipk ?? 0, 2) }}', '{{ number_format($p->ipk ?? 0, 2) }}')

text = text.replace('@if(->transkrip_path)', '@if($p->transkrip_path)')
text = text.replace('publicUrl(->transkrip_path', 'publicUrl($p->transkrip_path')

text = text.replace('@if(->berkas_cerc_path)', '@if($p->berkas_cerc_path)')
text = text.replace('publicUrl(->berkas_cerc_path', 'publicUrl($p->berkas_cerc_path')

text = text.replace('{{ collect(->jadwal ?? [])', '{{ collect($p->jadwal ?? [])')
text = text.replace('@if(->status_koor === \'disetujui\')', '@if($p->status_koor === \'disetujui\')')
text = text.replace('@elseif(->status_koor === \'ditolak\' || ->status === \'rejected\')', '@elseif($p->status_koor === \'ditolak\' || $p->status === \'rejected\')')
text = text.replace('@if(->status_koor === \'menunggu\')', '@if($p->status_koor === \'menunggu\')')

# I will also just brutally regex replace anything resembling "->var" to "$p->var" safely
text = re.sub(r'\(->(.*?)\)', r'($p->\1)', text)
text = re.sub(r'->(status|status_koor|user|ipk|motivasi|cv_path|transkrip_path|berkas_cerc_path|jadwal|id)', r'$p->\1', text)
text = text.replace('$$p->', '$p->').replace('$$p->', '$p->')

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Fixed the powershell interpolation bug!")
