import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\daftar-praktikan.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# 1. rubah Kelola Kelompok & Shift menjadi Buat Kelompok & Shift
text = text.replace('Kelola Kelompok & Shift', 'Buat Kelompok & Shift', 1) 
# Note: First occurrence is the card header. Wait! Let's ensure it's the exact string in the card header.
text = text.replace('<div style="font-weight:700; font-size:16px; color:#0D0D12; margin-bottom:16px;">Kelola Kelompok & Shift</div>',
                    '<div style="font-weight:700; font-size:16px; color:#0D0D12; margin-bottom:16px;">Buat Kelompok & Shift</div>')


# 2. secara default, bagian Kelola Kelompok & Shift itu Jumlah Kelompok dan Shift 0
text = text.replace('value="{{ $praktikum->jumlah_kelompok ?? 1 }}" min="1"', 'value="{{ $praktikum->jumlah_kelompok ?? 0 }}" min="0"')
text = text.replace('value="{{ $praktikum->jumlah_shift ?? 1 }}" min="1"', 'value="{{ $praktikum->jumlah_shift ?? 0 }}" min="0"')

# 3. ubah Assign Manual Anggota Kelompok menjadi Kelola Manual Anggota Kelompok
text = text.replace('Assign Manual Anggota Kelompok', 'Kelola Manual Anggota Kelompok')

# 4. Kalau belum submit (jumlah_kelompok 0), card kelompok kosong dan tulisan Kelompok belum tersedia.
# We will find the grid and replace it with an @if wrap.
# The grid starts at <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:16px;">
# and ends at </div> just before `margin-top:24px;` (the Hapus form)

old_grid = r'''<div style="display:grid; grid-template-columns:repeat\(auto-fill, minmax\(240px, 1fr\)\); gap:16px;">
                        @for\(\$i=1; \$i<=\(\$praktikum->jumlah_kelompok \?\? 0\); \$i\+\+\)
                            @php 
                                \$groupsPerShift = max\(1, \(\$praktikum->jumlah_kelompok \?\? 1\) / \(\$praktikum->jumlah_shift \?\? 1\)\);
                                \$defShift = \(int\) ceil\(\$i / \$groupsPerShift\);
                                if\(\$defShift > \(\$praktikum->jumlah_shift \?\? 1\)\) \$defShift = \$praktikum->jumlah_shift;
                                \$jmlAnggota = \$praktikansSemua->where\(\'kelompok\', \$i\)->count\(\);
                            @endphp
                            <div style="border:1px solid #DFE1E7; border-radius:10px; padding:16px; background:#FFF; display:flex; justify-content:space-between; align-items:center; transition:border 0\.2s;" onmouseover="this\.style\.borderColor=\'#A4ABB8\'" onmouseout="this\.style\.borderColor=\'#DFE1E7\'">
                                <div>
                                    <div style="font-weight:700; color:#0D0D12; font-size:14px; margin-bottom:4px;">Kelompok \{\{ \$i \}\}</div>
                                    <div style="font-size:12px; color:#666D80;">Shift \{\{ \$defShift \}\} <span style="margin:0 4px;">-</span> <strong>\{\{ \$jmlAnggota \}\}</strong> Anggota</div>
                                </div>
                                <button @click="openEditModal\(\'\{\{ \$i \}\}\', \'\{\{ \$defShift \}\}\'\)" style="background:#F0F1F4; border:none; padding:8px; border-radius:6px; cursor:pointer; color:#0B266E;" title="Edit Anggota Kelompok \{\{ \$i \}\}">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 20h9"/><path d="M16\.5 3\.5a2\.121 2\.121 0 0 1 3 3L7 19l-4 1 1-4L16\.5 3\.5z"/></svg>
                                </button>
                            </div>
                        @endfor
                    </div>'''

new_grid = """@if(($praktikum->jumlah_kelompok ?? 0) > 0)
                      <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:16px;">
                          @for($i=1; $i<=($praktikum->jumlah_kelompok ?? 0); $i++)
                              @php 
                                  $groupsPerShift = max(1, ($praktikum->jumlah_kelompok ?? 1) / ($praktikum->jumlah_shift ?? 1));
                                  $defShift = (int) ceil($i / $groupsPerShift);
                                  if($defShift > ($praktikum->jumlah_shift ?? 1)) $defShift = $praktikum->jumlah_shift;
                                  $jmlAnggota = $praktikansSemua->where('kelompok', $i)->count();
                              @endphp
                              <div style="border:1px solid #DFE1E7; border-radius:10px; padding:16px; background:#FFF; display:flex; justify-content:space-between; align-items:center; transition:border 0.2s;" onmouseover="this.style.borderColor='#A4ABB8'" onmouseout="this.style.borderColor='#DFE1E7'">
                                  <div>
                                      <div style="font-weight:700; color:#0D0D12; font-size:14px; margin-bottom:4px;">Kelompok {{ $i }}</div>
                                      <div style="font-size:12px; color:#666D80;">Shift {{ $defShift }} <span style="margin:0 4px;">-</span> <strong>{{ $jmlAnggota }}</strong> Anggota</div>
                                  </div>
                                  <button @click="openEditModal('{{ $i }}', '{{ $defShift }}')" style="background:#F0F1F4; border:none; padding:8px; border-radius:6px; cursor:pointer; color:#0B266E;" title="Edit Anggota Kelompok {{ $i }}">
                                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                  </button>
                              </div>
                          @endfor
                      </div>
                  @else
                      <div style="padding:48px 24px; text-align:center; color:#666D80; font-size:14px; border:1px dashed #DFE1E7; border-radius:12px; background:#FAFAFA;">
                          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18"/><path d="M15 3v18"/><path d="M3 9h18"/><path d="M3 15h18"/></svg>
                          Kelompok belum tersedia
                      </div>
                  @endif"""
text = re.sub(old_grid, new_grid, text, flags=re.DOTALL)


# 5. ketika button pensil diklik, hapus button batal
text = text.replace('<button @click="closeEditModal()" type="button" class="mp-btn secondary sm">Batal</button>', '')


# 6. ketika pop up pensil ditutup, berikan animasi fade out seperti saat fade in
# The overlay
target_overlay = r'x-transition:enter-end="opacity-100" class="fixed inset-0'
replace_overlay = r'x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0'
text = text.replace(target_overlay, replace_overlay)

# The modal box
target_box = r'x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative inline-block'
replace_box = r'x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block'
text = text.replace(target_box, replace_box)


with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("View patched successfully!")
