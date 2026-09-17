# -*- coding: utf-8 -*-
import os

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\pendaftaran-asprak.blade.php'

with open(filepath, 'r', encoding='utf-8') as f:
    lines = f.readlines()

new_lines = []
skip = False
for i, line in enumerate(lines):
    if 'style="padding:10px 20px; width:40px;">No</th>' in line:
        new_lines.append(line)
        new_lines.append('                        <th class="mp-th text-left" style="padding:10px 16px;">Mahasiswa</th>\n')
        new_lines.append('                        <th class="mp-th text-left" style="padding:10px 16px;">IPK / Motivasi</th>\n')
        new_lines.append('                        <th class="mp-th text-left" style="padding:10px 16px;">Berkas Tambahan</th>\n')
        new_lines.append('                        <th class="mp-th text-left" style="padding:10px 16px;">Jadwal</th>\n')
        new_lines.append('                        <th class="mp-th text-left" style="padding:10px 16px;">Status</th>\n')
        new_lines.append('                        <th class="mp-th text-left" style="padding:10px 16px;">Aksi</th>\n')
        new_lines.append('                    </tr>\n')
        new_lines.append('                </thead>\n')
        new_lines.append('                <tbody>\n')
        new_lines.append('                    @forelse( as )\n')
        new_lines.append('                        <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">\n')
        new_lines.append('                            <td style="padding:12px 20px;color:#808897;font-size:12px;">\n')
        new_lines.append('                                {{ ->iteration }}\n')
        new_lines.append('                            </td>\n')
        new_lines.append('                            <td style="padding:12px 16px;">\n')
        new_lines.append('                                <div class="flex items-center gap-[10px]">\n')
        new_lines.append('                                    <div class="mp-av yellow">{{ strtoupper(substr(->user?->name ?? \'M\', 0, 2)) }}</div>\n')
        new_lines.append('                                    <div>\n')
        new_lines.append('                                        <div style="font-weight:600;color:#0D0D12;">{{ ->user?->name ?? \'--\' }}</div>\n')
        new_lines.append('                                        <div style="font-size:11px;color:#666D80;">{{ ->user?->email }}</div>\n')
        new_lines.append('                                    </div>\n')
        new_lines.append('                                </div>\n')
        new_lines.append('                            </td>\n')
        new_lines.append('                            <td style="padding:12px 16px;">\n')
        new_lines.append('                                <div style="font-weight:700;color:{{ (->ipk ?? 0) >= 3.0 ? \'#16a34a\' : \'#DF1C41\' }};">\n')
        new_lines.append('                                    IPK: {{ number_format(->ipk ?? 0, 2) }}\n')
        new_lines.append('                                </div>\n')
        new_lines.append('                                <div style="font-size:12px;color:#666D80;max-width:180px;" class="line-clamp-2">\n')
        new_lines.append('                                    {{ ->motivasi ?? \'--\' }}\n')
        new_lines.append('                                </div>\n')
        new_lines.append('                            </td>\n')
        new_lines.append('                            <td style="padding:12px 16px;">\n')
        new_lines.append('                                @if(->cv_path)\n')
        new_lines.append('                                <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl(->cv_path, \'eoffice\') }}" target="_blank" style="font-size:11px;font-weight:600;color:#0B266E;display:block;" class="hover:underline">CV</a>\n')
        new_lines.append('                                @endif\n')
        new_lines.append('                                @if(->transkrip_path)\n')
        new_lines.append('                                <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl(->transkrip_path, \'eoffice\') }}" target="_blank" style="font-size:11px;font-weight:600;color:#0B266E;display:block;" class="hover:underline">Transkrip</a>\n')
        new_lines.append('                                @endif\n')
        new_lines.append('                                @if(->berkas_cerc_path)\n')
        new_lines.append('                                <a href="{{ app(\App\Services\SupabaseStorage::class)->publicUrl(->berkas_cerc_path, \'eoffice\') }}" target="_blank" style="font-size:11px;font-weight:600;color:#0B266E;display:block;" class="hover:underline">CERC</a>\n')
        new_lines.append('                                @endif\n')
        new_lines.append('                            </td>\n')
        new_lines.append('                            <td style="padding:12px 16px;font-size:11px;color:#666D80;">\n')
        new_lines.append('                                {{ collect(->jadwal ?? [])->join(\', \') ?: \'--\' }}\n')
        new_lines.append('                            </td>\n')
        new_lines.append('                            <td style="padding:12px 16px;">\n')
        new_lines.append('                                @if(->status_koor === \'disetujui\')\n')
        new_lines.append('                                    <div>\n')
        new_lines.append('                                        <span class="mp-badge success sm"><span class="dot"></span>Disetujui Koor</span>\n')
        
        skip = True
        
    if skip and 'Menunggu Admin</div>' in line:
        skip = False
        new_lines.append(line)
        continue
    
    if not skip:
        new_lines.append(line)

with open(filepath, 'w', encoding='utf-8') as f:
    f.writelines(new_lines)

print("Restored missing table columns!")
