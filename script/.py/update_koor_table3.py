import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\pendaftaran-asprak.blade.php'

with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

prefix = '@forelse( as )'
suffix = '@empty'

start_idx = text.find(prefix)
end_idx = text.find(suffix, start_idx)

if start_idx != -1 and end_idx != -1:
    new_tr = '''
                        <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                            <td style="padding:12px 20px;color:#808897;font-size:12px;">
                                {{ ->iteration }}
                            </td>
                            <td style="padding:12px 16px; padding-left: 0;">
                                <div class="flex items-center gap-[10px]">
                                    <div class="mp-av yellow">{{ strtoupper(substr(->user?->name ?? 'M', 0, 2)) }}</div>
                                    <div>
                                        <div style="font-weight:600;color:#0D0D12;">{{ ->user?->name ?? '--' }}</div>
                                        <div style="font-size:11px;color:#666D80;">{{ ->user?->email }}</div>
                                    </div>
                                </div>
                            </td>
                            @php
                                 = ->user?->email ?? '';
                                 = explode('@', )[0];
                                if (empty())  = '—';
                            @endphp
                            <td style="padding:12px 16px; font-size:13px; color:#4B5563;">
                                {{  }}
                            </td>
                            <td style="padding:12px 16px;">
                                <div style="font-weight:400; font-size:12px; color:#666D80;">
                                    {{ number_format(->ipk ?? 0, 2) }}
                                </div>
                            </td>
                            <td style="padding:12px 16px;">
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
                            </td>
                            <td style="padding:12px 16px;font-size:11px;color:#666D80;">
                                {{ collect(->jadwal ?? [])->join(', ') ?: '—' }}
                            </td>
                            <td style="padding:12px 16px;">
                                @if(->status_koor === 'disetujui')
                                    <div>
                                        <span class="mp-badge success sm"><span class="dot"></span>Disetujui Koor</span>
                                        <div style="font-size:10px;color:#666D80;margin-top:2px;">Menunggu Admin</div>
                                    </div>
                                @elseif(->status_koor === 'ditolak' || ->status === 'rejected')
                                    <span class="mp-badge error sm"><span class="dot"></span>Ditolak</span>
                                @else
                                    <span class="mp-badge warning sm"><span class="dot"></span>Menunggu Review</span>
                                @endif
                            </td>
                            <td style="padding:12px 16px;">
                                @if(->status_koor === 'menunggu')
                                    <div class="flex gap-2" x-data="{ catatan: '' }">
                                        <form method="POST"
                                            action="{{ route('eoffice.manprak.koor.pendaftaran-asprak.approve', ->id) }}">
                                            @csrf
                                            <input type="hidden" name="catatan_koor" value="">
                                            <button type="submit" class="mp-btn ghost sm">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                                                    <polyline points="20 6 9 17 4 12" />
                                                </svg>
                                                Setujui
                                            </button>
                                        </form>
                                        <form method="POST"
                                            action="{{ route('eoffice.manprak.koor.pendaftaran-asprak.reject', ->id) }}"
                                            x-data="{ alasan: '' }">
                                            @csrf
                                            <input type="hidden" name="alasan_penolakan" :value="alasan">
                                            <button type="button"
                                                @click="alasan = prompt('Alasan penolakan:'); if(alasan !== null) .closest('form').submit()"
                                                class="mp-btn destructive sm">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                                                    <line x1="18" y1="6" x2="6" y2="18" />
                                                    <line x1="6" y1="6" x2="18" y2="18" />
                                                </svg>
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span style="font-size:11px;color:#808897;">Sudah diproses</span>
                                @endif
                            </td>
                        </tr>
                        '''
    
    # We replace everything between prefix + len(prefix) and end_idx
    start_replace = start_idx + len(prefix)
    text = text[:start_replace] + new_tr + text[end_idx:]
    print("Replaced successfully!")
else:
    print("Could not find boundaries!")

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)
