import sys
import re

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# I will replace the alpine dropdowns for both sort and status_dosen via regex, matching from {{-- Alpine Custom Sort Dropdown --}} to the end of the div.

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
                            class="relative" 
                            style="min-width: 125px;"
                            @click.away="open = false">
                            
                            <input type="hidden" name="sort" x-bind:value="selected">
                            
                            <div @click="open = !open" 
                                 class="flex items-center justify-between cursor-pointer"
                                 style="height: 38px; border: 1px solid #D1D5DB; border-radius: 8px; padding: 0 12px; background: white; font-size: 13px;">
                                <span x-text="options.find(o => o.value === selected)?.label || 'Terbaru'"></span>
                                
                                <svg :style="open ? 'transform: rotate(180deg)' : ''" 
                                     style="transition: transform 0.3s ease; width: 12px; height: 12px; color: #353849; margin-left: 6px;" 
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                            
                            {{-- Dropdown Body (Inheriting calendar dropdown styles) --}}
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="custom-month-dropdown"
                                 style="top: calc(100% + 8px); margin-top: 0; min-width: 100%; transform: none; left: 0; display: none;">
                                 
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
                            class="relative" 
                            style="min-width: 135px;"
                            @click.away="open = false">
                            
                            <input type="hidden" name="status_dosen" x-bind:value="selected">
                            
                            <div @click="open = !open" 
                                 class="flex items-center justify-between cursor-pointer"
                                 style="height: 38px; border: 1px solid #D1D5DB; border-radius: 8px; padding: 0 12px; background: white; font-size: 13px;">
                                <span x-text="options.find(o => o.value === selected)?.label || 'Semua Status'"></span>
                                
                                <svg :style="open ? 'transform: rotate(180deg)' : ''" 
                                     style="transition: transform 0.3s ease; width: 12px; height: 12px; color: #353849; margin-left: 6px;" 
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                            
                            {{-- Dropdown Body (Inheriting calendar dropdown styles) --}}
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="custom-month-dropdown"
                                 style="top: calc(100% + 8px); margin-top: 0; min-width: 100%; transform: none; left: 0; display: none;">
                                 
                                 <template x-for="option in options" :key="option.value">
                                     <div @click="selected = option.value; open = false;"
                                          class="custom-month-item cursor-pointer"
                                          :class="selected === option.value ? 'active' : ''"
                                          x-text="option.label">
                                     </div>
                                 </template>
                            </div>
                        </div>"""

text = re.sub(status_pattern, status_replacement, text, flags=re.DOTALL)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")