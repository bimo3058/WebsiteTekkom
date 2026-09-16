import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\modul.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# I will parse everything before <div class="grid...
prefix_split = text.split('<div class="grid grid-cols-[360px_1fr]', 1)
prefix = prefix_split[0]
content_and_suffix = prefix_split[1]

# Extract Tambah Modul form
tambah_modul_match = re.search(r'(<div class="mp-card flex-shrink-0" style="padding:20px;">.*?Tambah Modul</div>\s*<form.*?</form>\s*</div>)', content_and_suffix, re.DOTALL)
tambah_modul_html = tambah_modul_match.group(1)

# Modify Tambah Modul html if needed (remove flex-shrink-0)
tambah_modul_html = tambah_modul_html.replace('flex-shrink-0', '')

# Extract Daftar Modul
daftar_modul_match = re.search(r'(<div class="mp-card">.*?<div class="mp-card-header">.*?<span class="mp-card-title">Daftar Modul</span>.*?</table>\s*</div>\s*</div>)', content_and_suffix, re.DOTALL)
if not daftar_modul_match:
    # try slightly different
    daftar_modul_match = re.search(r'(<div class="mp-card".*?<span class="mp-card-title">Daftar Modul</span>.*?</table>\s*</div>\s*</div>)', content_and_suffix, re.DOTALL)
daftar_modul_html = daftar_modul_match.group(1)

# Extract Form Assign
form_assign_match = re.search(r'<div style="font-weight:700;font-size:14px;color:#0D0D12;margin-bottom:16px;">Assign Asisten Praktikum ke Modul</div>.*?<form.*?</form>', content_and_suffix, re.DOTALL)
if not form_assign_match:
    form_assign_match = re.search(r'(<form method="POST" action="\{\{ route\(\'eoffice\.manprak\.koor\.bagi-modul\.store\'\) \}\}".*?</form>)', content_and_suffix, re.DOTALL)

# Rebuild Form Assign HTML
new_assign_html = """            <div class="mp-card" style="padding:20px;">
                <div style="font-weight:700;font-size:14px;color:#0D0D12;margin-bottom:16px;">Penugasan Asisten Modul</div>
                <form method="POST" action="{{ route('eoffice.manprak.koor.bagi-modul.store') }}" class="flex flex-col gap-3" x-data="{ asprak_id: '', modul_id: '' }">
                    @csrf
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Pilih Asisten <span class="text-red-500">*</span></label>
                        <select name="asprak_id" required class="mp-input w-full" x-model="asprak_id" style="min-height: 38px;">
                            <option value="">— Pilih Asisten Praktikum —</option>
                            @foreach($asistenList ?? [] as $a)
                                <option value="{{ $a->id }}">{{ $a->user?->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Pilih Modul <span class="text-red-500">*</span></label>
                        <select name="modul_id" required class="mp-input w-full" x-model="modul_id" style="min-height: 38px;">
                            <option value="">— Pilih Modul Praktikum —</option>
                            @if(isset($modulList))
                                @foreach($modulList as $m)
                                    <option value="{{ $m->id }}">{{ $m->nama }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <button 
                        type="submit" 
                        class="md w-full font-bold rounded-[8px] transition-colors duration-200 mt-2"
                        style="height: 40px; font-size: 13px;"
                        :class="(asprak_id && modul_id) ? 'bg-[#0B266E] text-white hover:bg-[#081e59]' : 'bg-[#F4F6F9] text-[#0B266E] pointer-events-none'"
                        :disabled="!(asprak_id && modul_id)">
                        Simpan
                    </button>
                </form>
            </div>"""

new_structure = f"""<div class="grid grid-cols-1 gap-[14px]">
    {daftar_modul_html}
    
    <div class="grid grid-cols-2 gap-[14px] items-start">
        {tambah_modul_html}
        {new_assign_html}
    </div>
</div>
</div>
</x-eoffice::manajemen-praktikum.layout>"""


output_text = prefix + new_structure

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(output_text)

print("Rebuilt structure successfully!")
