import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\pendaftaran-asprak.blade.php'

with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# 1. Search Bar 
search_html_old = '''<input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa..."
                    class="mp-input" style="padding:0 12px; height:36px; font-size:13px; width:250px;">'''

search_html_new = '''<div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama mahasiswa..."
                        class="mp-input w-full pl-9 pr-3" style="height:36px; font-size:13px; width:250px;">
                </div>'''
text = text.replace(search_html_old, search_html_new)

# 2. Filter Button SVG remove
filter_btn_old = '''<button type="submit" class="mp-btn primary sm" style="height:36px; padding:0 16px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;">
                        <circle cx="11" cy="11" r="8" />
                        <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    </svg>
                    Filter
                </button>'''
filter_btn_new = '''<button type="submit" class="mp-btn primary sm" style="height:36px; padding:0 16px;">
                    Filter
                </button>'''
text = text.replace(filter_btn_old, filter_btn_new)

# 3. Columns
text = text.replace('<th class="mp-th text-left" style="padding:10px 16px;">Mahasiswa</th>', '<th class="mp-th text-left" style="padding:10px 16px; padding-left: 0;">NAMA MAHASISWA</th>\n                        <th class="mp-th text-left" style="padding:10px 16px;">NIM</th>')
text = text.replace('<th class="mp-th text-left" style="padding:10px 16px;">IPK / Motivasi</th>', '<th class="mp-th text-left" style="padding:10px 16px;">IPK</th>')
text = text.replace('<th class="mp-th text-left" style="padding:10px 16px;">Berkas Tambahan</th>', '<th class="mp-th text-left" style="padding:10px 16px;">TRANSKRIP NILAI</th>\n                        <th class="mp-th text-left" style="padding:10px 16px;">KEANGGOTAAN CERC</th>')
text = text.replace('<td colspan="8"', '<td colspan="10"')

# 4. Table Row Data (Mahasiswa)
# add NIM after name column
name_block = '''<td style="padding:12px 16px;">
                                <div class="flex items-center gap-[10px]">
                                    <div class="mp-av yellow">{{ strtoupper(substr(->user?->name ?? 'M', 0, 2)) }}</div>
                                    <div>
                                        <div style="font-weight:600;color:#0D0D12;">{{ ->user?->name ?? '--' }}</div>
                                        <div style="font-size:11px;color:#666D80;">{{ ->user?->email }}</div>
                                    </div>
                                </div>
                            </td>'''

nim_calc = '''@php
                                 = ->user?->email ?? '';
                                 = explode('@', )[0];
                                if (empty())  = '—';
                            @endphp'''

name_block_new = name_block.replace('<td style="padding:12px 16px;">', '<td style="padding:12px 16px; padding-left: 0;">') + f'''
                            {nim_calc}
                            <td style="padding:12px 16px; font-size:13px; color:#4B5563;">
                                {{{{  }}}}
                            </td>'''
text = text.replace(name_block, name_block_new)

# 5. IPK Column
ipk_block = '''<td style="padding:12px 16px;">
                                <div style="font-weight:700;color:{{ (->ipk ?? 0) >= 3.0 ? '#16a34a' : '#DF1C41' }};">
                                    IPK: {{ number_format(->ipk ?? 0, 2) }}
                                </div>
                                <div style="font-size:12px;color:#666D80;max-width:180px;" class="line-clamp-2">
                                    {{ ->motivasi ?? '--' }}
                                </div>
                            </td>'''
ipk_block_new = '''<td style="padding:12px 16px;">
                                <div style="font-weight:400; font-size:12px; color:#666D80;">
                                    {{ number_format(->ipk ?? 0, 2) }}
                                </div>
                            </td>'''
text = text.replace(ipk_block, ipk_block_new)

# 6. Berkas Column
berkas_block_old = '''<td style="padding:12px 16px;">
                                @if(->cv_path)
                                <a href="{{ app(\\App\\Services\\SupabaseStorage::class)->publicUrl(->cv_path, 'eoffice') }}" target="_blank" style="font-size:11px;font-weight:600;color:#0B266E;display:block;" class="hover:underline">CV</a>
                                @endif
                                @if(->transkrip_path)
                                <a href="{{ app(\\App\\Services\\SupabaseStorage::class)->publicUrl(->transkrip_path, 'eoffice') }}" target="_blank" style="font-size:11px;font-weight:600;color:#0B266E;display:block;" class="hover:underline">Transkrip</a>
                                @endif
                                @if(->berkas_cerc_path)
                                <a href="{{ app(\\App\\Services\\SupabaseStorage::class)->publicUrl(->berkas_cerc_path, 'eoffice') }}" target="_blank" style="font-size:11px;font-weight:600;color:#0B266E;display:block;" class="hover:underline">CERC</a>
                                @endif
                            </td>'''
berkas_block_new = '''<td style="padding:12px 16px;">
                                @if(->transkrip_path)
                                <a href="{{ app(\\App\\Services\\SupabaseStorage::class)->publicUrl(->transkrip_path, 'eoffice') }}" target="_blank" style="font-size:13px;font-weight:600;color:#0B266E;" class="hover:underline">Lihat Berkas</a>
                                @else
                                <span style="font-size:13px;color:#808897;">—</span>
                                @endif
                            </td>
                            <td style="padding:12px 16px;">
                                @if(->berkas_cerc_path)
                                <a href="{{ app(\\App\\Services\\SupabaseStorage::class)->publicUrl(->berkas_cerc_path, 'eoffice') }}" target="_blank" style="font-size:13px;font-weight:600;color:#0B266E;" class="hover:underline">Lihat Berkas</a>
                                @else
                                <span style="font-size:13px;color:#808897;">—</span>
                                @endif
                            </td>'''
text = text.replace(berkas_block_old, berkas_block_new)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Applied UX refactor strictly.")
