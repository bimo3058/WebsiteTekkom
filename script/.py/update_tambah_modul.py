import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\modul.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# Replace the Tambah Modul form
old_form = """        <form method="POST" action="{{ route('eoffice.manprak.koor.modul.store') }}" class="flex flex-col gap-3">
            @csrf
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Modul</label>
                <input name="nama" required class="mp-input w-full" placeholder="Modul 1">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Urutan</label>
                <input type="number" name="urutan" min="1" value="{{ ($moduls->max('urutan') ?? 0) + 1 }}" required class="mp-input w-full">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Jadwal / Minggu</label>
                <input name="jadwal_minggu" class="mp-input w-full" placeholder="Minggu ke-1">
            </div>
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Deskripsi</label>
                <textarea name="deskripsi" rows="4" class="mp-input w-full resize-none"></textarea>
            </div>
            <button class="mp-btn primary md w-full">Simpan Modul</button>
        </form>"""

new_form = """        <form method="POST" action="{{ route('eoffice.manprak.koor.modul.store') }}" class="flex flex-col gap-3" x-data="{ namaModul: '' }">
            @csrf
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Modul <span class="text-red-500">*</span></label>
                <input name="nama" x-model="namaModul" required class="mp-input w-full" placeholder="Misal: Pengenalan Jaringan">
            </div>
            
            <input type="hidden" name="urutan" value="{{ ($moduls->max('urutan') ?? 0) + 1 }}">
            <input type="hidden" name="jadwal_minggu" value="">
            <input type="hidden" name="deskripsi" value="">
            
            <button 
                type="submit" 
                class="md w-full font-bold rounded-[8px] transition-colors duration-200 mt-2"
                style="height: 40px; font-size: 13px;"
                :class="namaModul.trim() ? 'bg-[#0B266E] text-white hover:bg-[#081e59]' : 'bg-[#F4F6F9] text-[#0B266E] pointer-events-none'"
                :disabled="!namaModul.trim()">
                Simpan
            </button>
        </form>"""

if old_form in text:
    text = text.replace(old_form, new_form)
else:
    # If indentation differs, fallback to regex
    pattern_form = r'<form method="POST" action="\{\{ route\(\'eoffice\.manprak\.koor\.modul\.store\'\) \}\}".*?</form>'
    text = re.sub(pattern_form, new_form, text, flags=re.DOTALL)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Updated Tambah Modul form successfully!")
