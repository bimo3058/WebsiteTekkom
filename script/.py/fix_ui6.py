import sys
import re

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

old_block = """<div
                        style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid var(--c-border);">
                        <div style="display:flex; align-items:center; gap:12px;">
                            {{-- Dropdown Per Halaman --}}
                            <div x-data="{
                                    open: false,
                                    options: [5, 10, 20],
                                    perPage: '{{ request('per_page', 10) }}'
                                }"
                                class="custom-month-wrapper relative text-[13px]"
                                :class="open ? 'is-open' : ''"
                                :style="open ? 'margin-bottom: 115px !important' : ''"
                                @click.away="open = false"
                                style="font-family: inherit; transition: margin-bottom 0.1s ease;">
                                
                                <button type="button" @click="open = !open" 
                                        :style="open ? 'border: 1px solid #0B266E; background: #F8FAFC;' : 'border: 1px solid #E5E7EB; background: white;'"
                                        style="display: flex; align-items: center; justify-content: space-between; height: 32px; border-radius: 6px; padding: 0 12px; white-space: nowrap; cursor: pointer; gap: 8px; width: 100%; transition: all 0.2s;">
                                    <span :style="open ? 'color: #666D80;' : 'color: #9CA3AF;'" style="font-size: 13px; font-weight: 400; transition: color 0.1s;">
                                        Per halaman <span :style="open ? 'color: #0B266E; font-weight: 600;' : 'color: #111827; font-weight: 500;'" style="margin-left: 4px;" x-text="perPage"></span>
                                    </span>
                                    <svg class="custom-month-chevron" :style="open ? 'color: #0B266E;' : 'color: #111827;'" style="width: 12px; height: 12px; stroke-width: 1.5; margin-top:2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </button>
                                
                                <div class="custom-month-dropdown" style="top: calc(100% + 4px); left: 0; transform: none; min-width: 100%; width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); padding: 4px; display: flex; flex-direction: column; gap: 2px;">
                                    <template x-for="opt in options" :key="opt">
                                        <div @click="window.location.href = '?per_page=' + opt + '&sort={{ request('sort') }}&status_dosen={{ request('status_dosen') }}&search={{ request('search') }}'"
                                             style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border-radius: 6px; cursor: pointer; transition: all 0.2s;"
                                             :style="perPage == opt ? 'background: #F8FAFC; color: #0B266E;' : 'background: transparent; color: #4B5563;'"
                                             onmouseover="if (this.getAttribute('data-active') !== 'true') this.style.background = '#F3F4F6'"
                                             onmouseout="if (this.getAttribute('data-active') !== 'true') this.style.background = 'transparent'"
                                             x-bind:data-active="perPage == opt">
                                            <span x-text="opt" style="font-size: 13px; font-weight: 400;"></span>
                                            <svg x-show="perPage == opt" style="width: 14px; height: 14px; color: #0B266E; margin-left: auto;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            <div style="font-size:13px; color:#4B5563;">Menampilkan
                                {{ $pendaftaran->firstItem() ?? 0 }} sampai {{ $pendaftaran->lastItem() ?? 0 }} dari
                                {{ $pendaftaran->total() }} data
                            </div>
                        </div>"""

new_block = """<div x-data="{
                                open: false,
                                options: [5, 10, 20],
                                perPage: '{{ request('per_page', 10) }}'
                            }"
                            style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid var(--c-border); transition: padding-bottom 0.1s ease;"
                            :style="open ? 'padding-bottom: 120px;' : ''">
                            <div style="display:flex; align-items:center; gap:12px; position: relative;" @click.away="open = false">
                                {{-- Dropdown Per Halaman --}}
                                <div class="relative text-[13px]" style="font-family: inherit;">
                                    <button type="button" @click="open = !open" 
                                            :style="open ? 'border: 1px solid #0B266E; background: #F8FAFC;' : 'border: 1px solid #E5E7EB; background: white;'"
                                            style="display: flex; align-items: center; justify-content: space-between; height: 32px; border-radius: 6px; padding: 0 12px; cursor: pointer; gap: 8px; width: 140px; transition: all 0.2s;">
                                        <span :style="open ? 'color: #666D80;' : 'color: #9CA3AF;'" style="font-size: 13px; font-weight: 400; transition: color 0.1s; white-space: nowrap;">
                                            Per halaman <span :style="open ? 'color: #0B266E; font-weight: 600;' : 'color: #111827; font-weight: 500;'" style="margin-left: 2px;" x-text="perPage"></span>
                                        </span>
                                        <svg :style="open ? 'color: #0B266E; transform: rotate(180deg);' : 'color: #111827; transform: rotate(0deg);'" style="width: 14px; height: 14px; stroke-width: 1.5; transition: transform 0.2s ease, color 0.1s;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="6 9 12 15 18 9"></polyline>
                                        </svg>
                                    </button>
                                    
                                    <div x-show="open" style="position: absolute; z-index: 50; top: calc(100% + 4px); left: 0; width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); padding: 4px; display: flex; flex-direction: column; gap: 2px;" x-transition>
                                        <template x-for="opt in options" :key="opt">
                                            <div @click="window.location.href = '?per_page=' + opt + '&sort={{ request('sort') }}&status_dosen={{ request('status_dosen') }}&search={{ request('search') }}'"
                                                 style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border-radius: 6px; cursor: pointer; transition: all 0.2s;"
                                                 :style="perPage == opt ? 'background: #F8FAFC; color: #0B266E;' : 'background: transparent; color: #4B5563;'"
                                                 onmouseover="if (this.getAttribute('data-active') !== 'true') this.style.background = '#F3F4F6'"
                                                 onmouseout="if (this.getAttribute('data-active') !== 'true') this.style.background = 'transparent'"
                                                 x-bind:data-active="perPage == opt">
                                                <span x-text="opt" style="font-size: 13px; font-weight: 400;"></span>
                                                <svg x-show="perPage == opt" style="width: 14px; height: 14px; color: #0B266E; margin-left: auto;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                
                                <div style="font-size:13px; color:#4B5563;">Menampilkan {{ $pendaftaran->firstItem() ?? 0 }} sampai {{ $pendaftaran->lastItem() ?? 0 }} dari {{ $pendaftaran->total() }} data</div>
                            </div>"""

# Ensure the whitespace formatting doesn't break simple replace!
# I will use a Regex dotall replace just in case.
text = re.sub(r'<div\s+style="display:flex;\s*align-items:center;\s*justify-content:space-between;\s*padding:12px\s*20px;\s*border-top:1px\s*solid\s*var\(--c-border\);">\s*<div\s*style="display:flex;\s*align-items:center;\s*gap:12px;">.*?(?=\s*<div\s*style="display:flex;\s*gap:4px;">)', new_block, text, flags=re.DOTALL)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")