import os

filepath = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum\koordinator\daftar-praktikan.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    text = f.read()

# 1. Update text "DAFTAR PRAKTIKAN" to "ANGGOTA KELOMPOK" and add empty state
old_header = '<div style="font-size:12px; color:#A4ABB8; margin:8px 0; font-weight:600;">DAFTAR PRAKTIKAN \n(<span x-text="filteredPraktikans.length"></span>)</div>'

new_header = """<div style="font-size:12px; color:#A4ABB8; margin:8px 0; font-weight:600;">ANGGOTA KELOMPOK (<span x-text="filteredPraktikans.length"></span>)</div>
                    <div x-show="filteredPraktikans.length === 0" style="padding: 24px; text-align: center; color: #666D80; font-size: 13px;">
                        Kelompok <span x-text="activeKelompok"></span> belum memiliki anggota.
                    </div>"""
# The old header might have a newline because in my CLI it printed with \n. Let's do string replace with careful spacing.
import re
text = re.sub(r'<div style="font-size:12px; color:#A4ABB8; margin:8px 0; font-weight:600;">DAFTAR PRAKTIKAN(.*?)</div>', new_header, text, flags=re.DOTALL)


# 2. Safely replace Checkbox CSS
old_checkbox = r'<div class="relative flex items-center justify-center cursor-pointer">.*?<input type="checkbox" :value="p.id" x-model="selectedMembers" class="peer sr-only">.*?<div class="w-5 h-5 rounded-\[6px\].*?<polyline points="20 6 9 17 4 12"></polyline>.*?</svg>.*?</div>.*?</div>'

new_checkbox = """<div class="relative flex items-center justify-center cursor-pointer">
                                  <input type="checkbox" :value="p.id" x-model="selectedMembers" class="hidden">
                                  <div class="w-5 h-5 rounded-[6px] border-[1.5px] transition-all flex items-center justify-center group-hover:border-[#A4ABB8]"
                                       :class="selectedMembers.includes(p.id) || selectedMembers.includes(p.id.toString()) ? 'bg-[#0B266E] border-[#0B266E]' : 'bg-white border-[#DFE1E7]'">
                                      <svg class="w-3.5 h-3.5 text-white transition-opacity" 
                                           :class="selectedMembers.includes(p.id) || selectedMembers.includes(p.id.toString()) ? 'opacity-100' : 'opacity-0'" 
                                           viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                          <polyline points="20 6 9 17 4 12"></polyline>
                                      </svg>
                                  </div>
                              </div>"""
text = re.sub(old_checkbox, new_checkbox, text, flags=re.DOTALL)


# 3. Update filteredPraktikans logic
old_js = r'get filteredPraktikans\(\) \{.*?let q = this\.searchQuery\.toLowerCase\(\);.*?// Tampilkan hanya yg kosong ATAU miliki kelompok ini.*?return this\.allPraktikans\.filter\(p => \{.*?let isEligible = \(p\.kel === this\.activeKelompok\) \|\| \(p\.kel === null \|\| p\.kel === \'\'\);.*?if\(\!isEligible\) return false;.*?if\(q === \'\'\) return true;.*?return p\.nama\.toLowerCase\(\)\.includes\(q\) \|\| p\.nim\.toLowerCase\(\)\.includes\(q\);.*?\}\);.*?\},'

new_js = """get filteredPraktikans() {
                let q = this.searchQuery.toLowerCase();
                
                return this.allPraktikans.filter(p => {
                    let isMemberOrSelected = (p.kel === this.activeKelompok) || this.selectedMembers.includes(p.id) || this.selectedMembers.includes(p.id.toString());
                    
                    if (q === '') {
                        return isMemberOrSelected;
                    } else {
                        let isEligible = isMemberOrSelected || (p.kel === null || p.kel === '');
                        if(!isEligible) return false;
                        return p.nama.toLowerCase().includes(q) || p.nim.toLowerCase().includes(q);
                    }
                });
            },"""
text = re.sub(old_js, new_js, text, flags=re.DOTALL)

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(text)

print("Patch 8 complete.")
