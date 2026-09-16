# -*- coding: utf-8 -*-
import os
filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\pendaftaran-asprak.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

text = text.replace('@forelse( as )', '@forelse($pendaftaran as $p)')
text = text.replace('{{ ->iteration }}', '{{ $loop->iteration }}')
text = text.replace('{{ ->user?->name ?? \'M\' }}', '{{ $p->user?->name ?? \'M\' }}')
text = text.replace('{{ ->user?->name ?? \'--\' }}', '{{ $p->user?->name ?? \'--\' }}')
text = text.replace('{{ ->user?->email }}', '{{ $p->user?->email }}')
text = text.replace('{{ (->ipk ?? 0) >= 3.0 ? \'#16a34a\' : \'#DF1C41\' }}', '{{ ($p->ipk ?? 0) >= 3.0 ? \'#16a34a\' : \'#DF1C41\' }}')
text = text.replace('{{ number_format(->ipk ?? 0, 2) }}', '{{ number_format($p->ipk ?? 0, 2) }}')
text = text.replace('{{ ->motivasi ?? \'--\' }}', '{{ $p->motivasi ?? \'--\' }}')
text = text.replace('@if(->cv_path)', '@if($p->cv_path)')
text = text.replace('{{ app(\\App\\Services\\SupabaseStorage::class)->publicUrl(->cv_path, \'eoffice\') }}', '{{ app(\\App\\Services\\SupabaseStorage::class)->publicUrl($p->cv_path, \'eoffice\') }}')
text = text.replace('@if(->transkrip_path)', '@if($p->transkrip_path)')
text = text.replace('{{ app(\\App\\Services\\SupabaseStorage::class)->publicUrl(->transkrip_path, \'eoffice\') }}', '{{ app(\\App\\Services\\SupabaseStorage::class)->publicUrl($p->transkrip_path, \'eoffice\') }}')
text = text.replace('@if(->berkas_cerc_path)', '@if($p->berkas_cerc_path)')
text = text.replace('{{ app(\\App\\Services\\SupabaseStorage::class)->publicUrl(->berkas_cerc_path, \'eoffice\') }}', '{{ app(\\App\\Services\\SupabaseStorage::class)->publicUrl($p->berkas_cerc_path, \'eoffice\') }}')
text = text.replace('{{ collect(->jadwal ?? [])->join(\', \') ?: \'--\' }}', '{{ collect($p->jadwal ?? [])->join(\', \') ?: \'--\' }}')
text = text.replace('@if(->status_koor === \'disetujui\')', '@if($p->status_koor === \'disetujui\')')

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)
print("Fixed interpolations.")
