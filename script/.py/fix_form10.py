import sys
import re

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

# Select block 1 (sort)
# We will use regex to capture the old select
sort_pattern = r'<select name="sort".*?</select>'
sort_alpine = """{{-- Alpine Custom Sort Dropdown --}}
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
                                     style="transition: transform 0.3s ease; width: 14px; height: 14px; color: #353849; margin-left: 6px;" 
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                            
                            {{-- Dropdown Body --}}
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 style="position: absolute; top: calc(100% + 8px); left: 0; width: 100%; min-width: 135px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15), 0 8px 10px -6px rgba(0,0,0,0.1); padding: 8px; z-index: 50; display: flex; flex-direction: column; gap: 4px;"
                                 style="display: none;">
                                 
                                 <template x-for="option in options" :key="option.value">
                                     <div @click="selected = option.value; open = false;"
                                          class="cursor-pointer"
                                          style="padding: 10px 12px; border-radius: 6px; font-size: 13px; font-weight: 500; transition: background 0.1s, color 0.1s; line-height: 1;"
                                          :style="selected === option.value ? 'background: #0B266E; color: white;' : 'color: #0B266E; background: transparent;'"
                                          x-on:mouseover="if(selected !== option.value) $el.style.background = '#F3F4F6'"
                                          x-on:mouseout="if(selected !== option.value) $el.style.background = 'transparent'"
                                          x-text="option.label">
                                     </div>
                                 </template>
                            </div>
                        </div>"""

text = re.sub(sort_pattern, sort_alpine, text, flags=re.DOTALL)

# Select block 2 (status_dosen)
status_pattern = r'<select name="status_dosen".*?</select>'
status_alpine = """{{-- Alpine Custom Status Dropdown --}}
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
                                     style="transition: transform 0.3s ease; width: 14px; height: 14px; color: #353849; margin-left: 6px;" 
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                            
                            {{-- Dropdown Body --}}
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 style="position: absolute; top: calc(100% + 8px); left: 0; width: 100%; min-width: 145px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15), 0 8px 10px -6px rgba(0,0,0,0.1); padding: 8px; z-index: 50; display: flex; flex-direction: column; gap: 4px;"
                                 style="display: none;">
                                 
                                 <template x-for="option in options" :key="option.value">
                                     <div @click="selected = option.value; open = false;"
                                          class="cursor-pointer"
                                          style="padding: 10px 12px; border-radius: 6px; font-size: 13px; font-weight: 500; transition: background 0.1s, color 0.1s; line-height: 1;"
                                          :style="selected === option.value ? 'background: #0B266E; color: white;' : 'color: #0B266E; background: transparent;'"
                                          x-on:mouseover="if(selected !== option.value) $el.style.background = '#F3F4F6'"
                                          x-on:mouseout="if(selected !== option.value) $el.style.background = 'transparent'"
                                          x-text="option.label">
                                     </div>
                                 </template>
                            </div>
                        </div>"""

text = re.sub(status_pattern, status_alpine, text, flags=re.DOTALL)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")