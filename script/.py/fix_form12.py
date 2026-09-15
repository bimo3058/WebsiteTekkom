import sys
import re

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# We need to replace the entire x-data div for sort and status_dosen.

sort_pattern = r'\{\{-- Alpine Custom Sort Dropdown --\}\}.*?</div>\s*</div>'
sort_replacement = """{{-- Alpine Custom Sort Dropdown --}}
                        <div x-data="{
                                open: false,
                                options: [
                                    { value: 'terbaru', label: 'Terbaru' },
                                    { value: 'ipk_tertinggi', label: 'IPK Tertinggi' }
                                ],
                                selected: '{{ request('sort', 'terbaru') }}'
                            }" 
                            class="custom-month-wrapper"
                            :class="open ? 'is-open' : ''"
                            style="min-width: 125px; padding: 0 !important; font-weight: normal; color: inherit; height: 38px;"
                            @click.away="open = false">
                            
                            <input type="hidden" name="sort" x-bind:value="selected">
                            
                            <div @click="open = !open" 
                                 class="flex items-center justify-between cursor-pointer w-full"
                                 style="height: 100%; border: 1px solid #D1D5DB; border-radius: 8px; padding: 0 12px; background: white; font-size: 13px;">
                                <span x-text="options.find(o => o.value === selected)?.label || 'Terbaru'"></span>
                                
                                <svg class="custom-month-chevron" style="color: #666D80; margin-left: 6px; stroke-width: 1.5; width: 12px; height: 12px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                            
                            {{-- Dropdown Body --}}
                            <div class="custom-month-dropdown"
                                 style="top: calc(100% + 4px); margin-top: 0; min-width: 100%; transform: none; left: 0;">
                                 
                                 <template x-for="option in options" :key="option.value">
                                     <div @click="selected = option.value; open = false;"
                                          class="custom-month-item cursor-pointer"
                                          :class="selected === option.value ? 'active' : ''"
                                          x-text="option.label">
                                     </div>
                                 </template>
                            </div>
                        </div>"""

status_pattern = r'\{\{-- Alpine Custom Status Dropdown --\}\}.*?</div>\s*</div>'
status_replacement = """{{-- Alpine Custom Status Dropdown --}}
                        <div x-data="{
                                open: false,
                                options: [
                                    { value: '', label: 'Semua Status' },
                                    { value: 'menunggu', label: 'Menunggu Review' },
                                    { value: 'disetujui', label: 'Sudah Disetujui' },
                                    { value: 'ditolak', label: 'Ditolak' }
                                ],
                                selected: '{{ request('status_dosen', '') }}'
                            }" 
                            class="custom-month-wrapper"
                            :class="open ? 'is-open' : ''"
                            style="min-width: 135px; padding: 0 !important; font-weight: normal; color: inherit; height: 38px;"
                            @click.away="open = false">
                            
                            <input type="hidden" name="status_dosen" x-bind:value="selected">
                            
                            <div @click="open = !open" 
                                 class="flex items-center justify-between cursor-pointer w-full"
                                 style="height: 100%; border: 1px solid #D1D5DB; border-radius: 8px; padding: 0 12px; background: white; font-size: 13px;">
                                <span x-text="options.find(o => o.value === selected)?.label || 'Semua Status'"></span>
                                
                                <svg class="custom-month-chevron" style="color: #666D80; margin-left: 6px; stroke-width: 1.5; width: 12px; height: 12px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                            
                            {{-- Dropdown Body --}}
                            <div class="custom-month-dropdown"
                                 style="top: calc(100% + 4px); margin-top: 0; min-width: 100%; transform: none; left: 0;">
                                 
                                 <template x-for="option in options" :key="option.value">
                                     <div @click="selected = option.value; open = false;"
                                          class="custom-month-item cursor-pointer"
                                          :class="selected === option.value ? 'active' : ''"
                                          x-text="option.label">
                                     </div>
                                 </template>
                            </div>
                        </div>"""

text = re.sub(sort_pattern, sort_replacement, text, flags=re.DOTALL)
text = re.sub(status_pattern, status_replacement, text, flags=re.DOTALL)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")