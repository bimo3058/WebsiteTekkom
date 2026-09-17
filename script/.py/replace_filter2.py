import sys
import re

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

old_html_start = "{{-- SECTION: Daftar Calon Koordinator --}}"
# I'll replace everything I just inserted
idx1 = text.find(old_html_start)
idx2 = text.find('<table class="w-full" style="font-size:13px; min-width: max-content;">')

new_html = """{{-- SECTION: Daftar Calon Koordinator --}}
            <div class="mp-card flex-1 min-h-0" style="margin-top: -1px; display: flex; flex-direction: column;">
                {{-- Card Header identical to Form Pendaftaran Koordinator --}}
                <div class="mp-card-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E5E7EB;">
                    <span class="mp-card-title">Daftar Calon Koordinator</span>
                    <span style="font-size:12px;font-weight:600;color:#0D0D12;">{{ $pendaftaran->total() }} pendaftar</span>
                </div>

                {{-- Filtering Flex Row --}}
                <div style="padding: 16px 20px; border-bottom: 1px solid #E5E7EB; flex-shrink: 0;">
                    <form method="GET" style="display: flex; gap: 12px; margin: 0; align-items: center;" class="flex-wrap">
                        <input type="hidden" name="praktikum_id" value="{{ $praktikum->id }}">
                        
                        {{-- Search --}}
                        <div style="position: relative; flex: 1; min-width: 200px;">
                            <svg style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9CA3AF;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama mahasiswa..." style="width: 100%; border: 1px solid #D1D5DB; border-radius: 8px; padding: 10px 12px 10px 36px; font-size: 13px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#0B266E'" onblur="this.style.borderColor='#D1D5DB'">
                        </div>

                        {{-- Sort --}}
                        <select name="sort" style="border: 1px solid #D1D5DB; border-radius: 8px; padding: 10px 12px; font-size: 13px; outline:none; background:white; min-width: 140px; appearance: auto; cursor: pointer;">
                            <option value="terbaru" {{ request('sort', 'terbaru') == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                            <option value="ipk_tertinggi" {{ request('sort') == 'ipk_tertinggi' ? 'selected' : '' }}>IPK Tertinggi</option>
                        </select>

                        {{-- Status --}}
                        <select name="status_dosen" style="border: 1px solid #D1D5DB; border-radius: 8px; padding: 10px 12px; font-size: 13px; outline:none; background:white; min-width: 140px; appearance: auto; cursor: pointer;">
                            <option value="">Semua Status</option>
                            <option value="menunggu" {{ request('status_dosen') == 'menunggu' ? 'selected' : '' }}>Menunggu Review</option>
                            <option value="disetujui" {{ request('status_dosen') == 'disetujui' ? 'selected' : '' }}>Sudah Disetujui</option>
                            <option value="ditolak" {{ request('status_dosen') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>

                        {{-- Button --}}
                        <button type="submit" style="background:#0B266E; color:white; border-radius: 8px; padding: 10px 16px; font-weight:600; font-size:13px; display:flex; gap:6px; align-items:center; border:none; cursor:pointer;" onmouseover="this.style.background='#081b4f'" onmouseout="this.style.background='#0B266E'">
                            <span style="font-size: 16px; font-weight: 400; line-height: 1;"></span> Filter
                        </button>
                    </form>
                </div>

                {{-- The Table --}}
                <div class="overflow-x-auto flex-1">
                    <table class="w-full" style="font-size:13px; min-width: max-content;">"""

if idx1 != -1 and idx2 != -1:
    text = text[:idx1] + new_html + text[idx2+len('<table class="w-full" style="font-size:13px; min-width: max-content;">'):]
    with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
        f.write(text)
    print("done")
else:
    print("markers not found")