import os
import re

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\daftar-praktikan.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# 1. Update the Search Input to have magnifying glass, and add Import CSV button (and update its icon, also change Export CSV icon).
# The current form area:
old_search_area = r"""                \{\{-- Export CSV --\}\}
                <a href="\{\{ route\('eoffice\.manprak\.koor\.praktikan\.export'\) \}\}"
                   class="mp-btn secondary sm" style="display:inline-flex;align-items:center;gap:5px;text-decoration:none;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Export CSV
                </a>
                \{\{-- Search --\}\}
                <form method="GET" style="display:flex;gap:4px;">
                    <input name="search" value="\{\{ \$search \}\}" placeholder="Cari nama / NIM\.\.\." class="mp-input" style="width:180px;">
                    <button type="submit" class="mp-btn primary sm">Cari</button>
                    <button @click="openMainModal\(\)" type="button" class="mp-btn primary sm" style="background:#0B266E; border-color:#0B266E;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4-4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Kelola Kelompok &amp; Shift
            </button>
                </form>"""

new_search_area = """                {{-- Import CSV --}}
                <button type="button" class="mp-btn secondary sm" style="display:inline-flex;align-items:center;gap:5px;text-decoration:none;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Import CSV
                </button>
                {{-- Export CSV --}}
                <a href="{{ route('eoffice.manprak.koor.praktikan.export') }}"
                   class="mp-btn secondary sm" style="display:inline-flex;align-items:center;gap:5px;text-decoration:none;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Export CSV
                </a>
                
                <span style="width:1px;height:24px;background:#DFE1E7;margin:0 4px;"></span>

                {{-- Search --}}
                <form method="GET" style="display:flex;gap:8px;">
                    <div style="position:relative;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input name="search" value="{{ $search }}" placeholder="Cari nama / NIM..." class="mp-input" style="width:200px;padding-left:32px;">
                    </div>
                    
                    {{-- Updated Kelola Kelompok Icon --}}
                    <button @click="openMainModal()" type="button" class="mp-btn primary sm" style="background:#0B266E; border-color:#0B266E;display:inline-flex;align-items:center;gap:6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                        Kelola Kelompok &amp; Shift
                    </button>
                </form>"""
text = re.sub(old_search_area, new_search_area, text)

# 2. Main Modal Styling (Edit Praktikum Admin-like UI)
old_main_modal = r"""<div x-show="showMainModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba\(0,0,0,0\.5\);" x-transition>
            <div style="display:flex; align-items:center; justify-content:center; width:100%; height:100%; padding:20px;">
                <div @click\.outside="closeMainModal\(\)" style="background:#FFF; width:100%; max-width:850px; border-radius:12px; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 10px 25px rgba\(0,0,0,0\.1\);">
                
                \{\{-- Modal Header \(Sticky\) --\}\}
                <div style="padding:20px 24px; border-bottom:1px solid #DFE1E7; background:#FFF; border-radius:12px 12px 0 0; position:sticky; top:0; z-index:10; box-shadow:0 2px 5px rgba\(0,0,0,0\.02\);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                        <div style="font-weight:700; font-size:16px;">Kelola Kelompok & Shift</div>
                        <button @click="closeMainModal\(\)" style="background:none; border:none; cursor:pointer; color:#666D80;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>"""

new_main_modal = """<div x-show="showMainModal" style="display:none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showMainModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-[#0D0D12]/40 transition-opacity" aria-hidden="true" @click="closeMainModal()"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showMainModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block align-bottom bg-white rounded-[16px] text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-[850px] w-full flex flex-col max-h-[90vh]">
                
                {{-- Modal Header --}}
                <div class="px-6 py-4 border-b border-[#DFE1E7] flex-shrink-0 bg-white shadow-[0_2px_5px_rgba(0,0,0,0.02)] relative z-10">
                    <div style="font-weight:700; font-size:16px; color:#0D0D12; margin-bottom:16px;">Kelola Kelompok & Shift</div>
"""
text = re.sub(old_main_modal, new_main_modal, text)

# 3. Edit Modal (Nested Modal) Styling
old_sub_modal = r"""<div x-show="showEditModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba\(0,0,0,0\.6\);" x-transition>
            <div style="display:flex; align-items:center; justify-content:center; width:100%; height:100%; padding:20px;">
                <div @click\.outside="closeEditModal\(\)" style="background:#FFF; width:100%; max-width:600px; border-radius:12px; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 10px 40px rgba\(0,0,0,0\.2\);">
                
                \{\{-- Header --\}\}
                <div style="padding:16px 24px; border-bottom:1px solid #DFE1E7; display:flex; justify-content:space-between; align-items:center; background:#F9FAFB; border-radius:12px 12px 0 0;">
                    <div>
                        <div style="font-weight:700; font-size:16px; color:#0D0D12;" x-text="'Pilih Anggota Kelompok ' \+ activeKelompok"></div>
                        <div style="font-size:12px; color:#666D80; margin-top:2px;" x-text="'Shift ' \+ activeShift"></div>
                    </div>
                    <button @click="closeEditModal\(\)" style="background:none; border:none; cursor:pointer; color:#666D80;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>"""

new_sub_modal = """<div x-show="showEditModal" style="display:none;" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-[#0D0D12]/60 transition-opacity" aria-hidden="true" @click="closeEditModal()"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative inline-block align-bottom bg-white rounded-[16px] text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full flex flex-col max-h-[90vh]">
                
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-[#DFE1E7] flex-shrink-0 bg-white">
                    <div style="font-weight:700; font-size:16px; color:#0D0D12;" x-text="'Pilih Anggota Kelompok ' + activeKelompok"></div>
                    <div style="font-size:12px; color:#666D80; margin-top:2px;" x-text="'Shift ' + activeShift"></div>
                </div>"""
text = re.sub(old_sub_modal, new_sub_modal, text)

# Close Modal Fix (Since we removed `</div></div></div>` at the end of Main Modal, we need to adapt)
# Actually the `</div></div></div>` matched for `old_sub_modal` was `</div></div></div>` at the end. We need to be careful.
# `new_main_modal` has `<div class="relative ..."><div class="px-6">....`
# Let's just fix the ending tags for standard Tailwind UI modal.

text = text.replace('</div>\n                </div>\n            </div>\n        </div>\n    </template>', 
                    '</div>\n            </div>\n        </div>\n    </template>')

# We also need to style the "Simpan" and "Batal" buttons inside the Edit Modal to match admin modal exactly.
# Admin modal uses standard mp-btn classes, but let's check old one.
text = text.replace("""<button @click="closeEditModal()" style="background:#F0F1F4; border:1px solid #DFE1E7; color:#353849; padding:8px 16px; border-radius:6px; font-weight:600; font-size:13px; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='#E4E6EB'" onmouseout="this.style.background='#F0F1F4'">Batal</button>""",
                    """<button @click="closeEditModal()" type="button" class="mp-btn secondary sm">Batal</button>""")
text = text.replace("""<button @click="saveMembers()" :disabled="isSaving" style="background:#0B266E; border:none; color:#FFF; padding:8px 16px; border-radius:6px; font-weight:600; font-size:13px; cursor:pointer; min-width:90px; transition:background 0.2s;" onmouseover="this.style.background='#081d54'" onmouseout="this.style.background='#0B266E'">""",
                    """<button @click="saveMembers()" :disabled="isSaving" type="button" class="mp-btn primary sm" style="min-width: 90px; justify-content: center; background:#0B266E; border-color:#0B266E;">""")


# We ALSO want a Close/Batal and Simpan for the main form in the Main Modal? 
# The main modal has "Simpan Pengaturan" and "Kosongkan Plotting". That's fine.

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Updated view daftar-praktikan.")
