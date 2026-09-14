import sys
import re

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    orig = f.read()

with open('extracted_table.txt', 'r', encoding='utf-8') as f:
    table_content = f.read()

with open('extracted_form.txt', 'r', encoding='utf-8') as f:
    form_content = f.read()

with open('extracted_scripts.txt', 'r', encoding='utf-8') as f:
    scripts_content = f.read()

# 1. EXTRACT PAGINATION
with open('fix_pagination_admin_perfect.py', 'r', encoding='utf-8') as f:
    t = f.read()
    pag_part = t.split('new_pagination = """')[1]
    pag_html = pag_part.split('"""')[0]

# 2. BEAUTIFUL KOORDINATOR AKTIF & TUNJUK
koordinator_aktif_layout = """
{{-- SECTION: KOORDINATOR AKTIF --}}
@if(isset($praktikum))
<div class="mp-card flex-shrink-0" style="margin-bottom: 24px; padding: 24px; display: flex; flex-direction: row; justify-content: space-between; align-items: stretch; gap: 24px; flex-wrap: wrap;">
    
    {{-- Kiri: Koordinator Aktif --}}
    <div style="flex: 1; min-width: 300px;">
        <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 20px; border-bottom: 1px solid #DFE1E7; margin-bottom: 20px;">
            <div>
                <div style="font-size: 16px; font-weight: 700; color: #0D0D12; display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#293C79" stroke-width="2.5" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Koordinator Aktif
                </div>
                <div style="font-size: 13px; color: #666D80; margin-top: 4px;">Informasi koordinator praktikum saat ini</div>
            </div>
        </div>
        
        @if($koordinator)
            <div style="font-size: 13px; font-weight: 700; color: #10B981; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.05em; display:flex; align-items:center; gap:6px;">
                <span class="dot" style="background:#10B981;"></span> Koordinator Aktif
            </div>
            <div style="display: flex; align-items: center; gap: 14px;">
                <div class="mp-av navy" style="width: 52px; height: 52px; font-size: 16px;">
                    {{ strtoupper(substr($koordinator->name ?? 'UN', 0, 2)) }}
                </div>
                <div>
                    <div style="font-size: 16px; font-weight: 700; color: #0D0D12;">{{ $koordinator->name ?? '-' }}</div>
                    <div style="font-size: 13px; color: #666D80; margin-top:2px;">{{ $koordinator->student->student_number ?? '—' }} · {{ $koordinator->email ?? '-' }}</div>
                </div>
            </div>
        @else
            <div style="display: flex; align-items: flex-start; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 50%; background: #FFF5F5; border: 1px solid #FFEBEB; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#DF1C41" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                    <div style="font-size: 14px; font-weight: 700; color: #0D0D12; margin-bottom: 4px;">Belum Ada Koordinator</div>
                    <div style="font-size: 13px; color: #666D80; line-height: 1.6; max-width: 400px;">
                        <strong>{{ $praktikum->nama ?? '-' }}</strong> belum memiliki koordinator. Silahkan buka pendaftaran koordinator di menu Periode, atau tunjuk koordinator via form di samping.
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Kanan: Tunjuk Koordinator Langsung --}}
    <div style="width: 380px; background: #FAFAFA; border: 1px solid #DFE1E7; border-radius: 12px; padding: 0;" x-data="{
         query: '', results: [], showDropdown: false, selectedUser: null, isLoading: false,
         init() { this.$watch('query', value => { if(this.selectedUser && this.selectedUser.text !== value) { this.selectedUser = null; } }); },
         async search() {
             if(this.query.length < 3) { this.results = []; return; }
             this.isLoading = true;
             try {
                 const res = await fetch(`{{ route('eoffice.manprak.dosen.search-praktikan') }}?praktikum_id={{ $praktikum->id ?? '' }}&q=${encodeURIComponent(this.query)}`);
                 const data = await res.json();
                 this.results = data;
             } catch (e) { this.results = []; }
             this.isLoading = false;
         },
         selectUser(user) { this.selectedUser = user; this.query = user.text; this.showDropdown = false; }
     }">
        <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px 24px 0 24px; margin-bottom: 20px; border-bottom: 1px solid #DFE1E7; padding-bottom: 20px;">
            <div>
                <div style="font-size: 16px; font-weight: 700; color: #0D0D12; display: flex; align-items: center; gap: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#293C79" stroke-width="2.5" stroke-linecap="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6"/><path d="M16 11h6"/></svg>
                    Tunjuk Koordinator
                </div>
                <div style="font-size: 13px; color: #666D80; margin-top: 4px;">Pilih mahasiswa untuk penunjukan kadept instan</div>
            </div>
        </div>

        <form method="POST" action="{{ route('eoffice.manprak.dosen.tunjuk-koor') }}" style="padding: 0 24px 24px 24px;">
            @csrf
            <input type="hidden" name="praktikum_id" value="{{ $praktikum->id ?? '' }}">
            <input type="hidden" name="nim" :value="selectedUser ? selectedUser.id : ''">

            <div style="position: relative; margin-bottom: 16px;">
                <input type="text" x-model="query" @input.debounce.300ms="search()" 
                       placeholder="Cari NIM atau Nama mahasiswa..."
                       class="mp-input text-[13px]" style="width: 100%; border-radius: 8px; padding-right: 36px; background: #fff;"
                       @focus="showDropdown = true" @click.away="showDropdown = false">
                
                {{-- Dropdown --}}
                <div x-show="showDropdown && query.length >= 3" 
                     style="position: absolute; z-index: 50; width: 100%; margin-top: 4px; background: white; border: 1px solid #DFE1E7; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); max-height: 220px; overflow-y: auto;">
                    <div x-show="isLoading" style="padding: 12px 16px; text-align: center; color: #666D80; font-size: 12px;">Mencari data...</div>
                    <template x-if="!isLoading && results.length > 0">
                        <div>
                            <template x-for="user in results" :key="user.id">
                                <div @click="selectUser(user)" style="padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #F3F4F6;" onmouseover="this.style.background='#FAFAFA'" onmouseout="this.style.background='transparent'">
                                    <div style="font-size: 13px; font-weight: 700; color: #0D0D12;" x-text="user.text.split(' - ')[0]"></div>
                                    <div style="font-size: 11px; color: #666D80; margin-top: 2px;" x-text="user.id"></div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <div x-show="!isLoading && results.length === 0" style="padding: 12px 16px; text-align: center; color: #DF1C41; font-size: 12px;">Tidak ditemukan.</div>
                </div>
            </div>

            <button type="submit" class="mp-btn primary w-full flex items-center justify-center gap-2"
                    style="border-radius: 9999px; font-weight: 600; font-size: 13px; padding: 10px; box-shadow: 0 4px 12px rgba(11,38,110,0.2);"
                    x-bind:disabled="!selectedUser"
                    :style="!selectedUser ? 'opacity: 0.5; cursor: not-allowed;' : ''">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg> Simpan
            </button>
        </form>
    </div>

</div>
@endif
"""

# Extract Filter Form from Pristine!
filter_match = re.search(r'<form method="GET" class="flex gap-2 flex-wrap">.*?</form>', orig, re.DOTALL)
filter_form_html = filter_match.group(0)

# Replace table actions exactly so they don't break routes
table_content = table_content.replace("pendaftaran-koor.update-status", "eoffice.manprak.dosen.pendaftaran-koor.approve")
table_content = table_content.replace("@method('PUT')", "")
table_content = table_content.replace("<input type=\"hidden\" name=\"status_dosen\" value=\"disetujui\">", "")
# the reject route
table_content = table_content.replace("pendaftaran-koor.update-status", "eoffice.manprak.dosen.pendaftaran-koor.reject")
table_content = table_content.replace("<input type=\"hidden\" name=\"status_dosen\" value=\"ditolak\">", "")

# 3. CONSTRUCT ENTIRE NEW CONTENT
new_layout = f"""{koordinator_aktif_layout}

{{-- SECTION: FORM PENDAFTARAN --}}
@php
    $periodeAktif = \Modules\EOffice\Models\PeriodePendaftaran::where('praktikum_id', $praktikum?->id ?? '')
        ->where('jenis', 'koordinator')
        ->where('is_aktif', true)
        ->first();
@endphp
<div class="mp-card flex-shrink-0" style="margin-bottom: 24px;">
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px 24px 0 24px; margin-bottom: 20px; border-bottom: 1px solid #DFE1E7; padding-bottom: 20px;">
        <div>
            <div style="font-size: 16px; font-weight: 700; color: #0D0D12; display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#293C79" stroke-width="2.5" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Konfigurasi & Buka Pendaftaran
            </div>
            <div style="font-size: 13px; color: #666D80; margin-top: 4px;">Kelola soal kuis pendaftaran atau syarat berkas tambahan untuk calon koordinator.</div>
        </div>
    </div>
    {form_content}
</div>

{{-- SECTION: DAFTAR CALON KOORDINATOR --}}
<div class="sec-head">
    <span class="sec-bar"></span>
    <span class="sec-title">Daftar Calon Koordinator</span>
    <span class="sec-rule"></span>
    <span class="mp-badge navy sm">{{{{ $pendaftaran->total() }}}} pendaftar</span>
</div>

<div class="mp-card flex-1 min-h-0" style="display: flex; flex-direction: column; padding-bottom: 0;">
    {{-- Pencarian & Filter --}}
    <div style="padding:14px 18px; border-bottom: 1px solid var(--c-border); background: #F8FAFC; border-radius: 12px 12px 0 0;">
        {filter_form_html}
    </div>
    
    <div class="overflow-x-auto">
        {table_content}
    </div>
    
    {pag_html}
</div>
"""

# CUT Pristine up to {{-- Section: Filter --}}
top_pristine = orig[:orig.find('{{-- Section: Filter --}}')]
combined = top_pristine + new_layout + f'\n{scripts_content}\n</x-eoffice::manajemen-praktikum.layout>'

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(combined)

print("God Tier Assembly Success")