import sys

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "r", encoding="utf-8") as f:
    text = f.read()

old_string = """<div style="display:flex; align-items:center; gap:12px;">
                            <div style="font-size:13px; color:var(--c-fg-sec);">Menampilkan
                                {{ $pendaftaran->firstItem() ?? 0 }} sampai {{ $pendaftaran->lastItem() ?? 0 }} dari
                                {{ $pendaftaran->total() }} data
                            </div>
                        </div>"""

new_string = """<div style="display:flex; align-items:center; gap:12px;">
                            {{-- Dropdown Per Halaman --}}
                            <div x-data="{
                                    open: false,
                                    options: [5, 10, 20],
                                    perPage: '{{ request('per_page', 10) }}'
                                }"
                                class="custom-month-wrapper relative text-[13px]"
                                :class="open ? 'is-open' : ''"
                                @click.away="open = false"
                                style="font-family: inherit;">
                                
                                <button type="button" @click="open = !open" 
                                        style="display: flex; align-items: center; justify-content: space-between; height: 32px; border: 1px solid #75839C; border-radius: 6px; padding: 0 12px; background: white; white-space: nowrap; cursor: pointer; gap: 8px;">
                                    <span style="color: #666D80; font-size: 13px;">Per halaman <span style="font-weight: 600; color: #0B266E; margin-left: 4px;" x-text="perPage"></span></span>
                                    <svg class="custom-month-chevron" style="color: #0B266E; width: 12px; height: 12px; stroke-width: 1.5; margin-top:2px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </button>
                                
                                <div class="custom-month-dropdown" style="top: calc(100% + 4px); left: 0; transform: none; min-width: 100%; width: auto; border: 1px solid #E5E7EB; border-radius: 8px; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); padding: 4px; display: flex; flex-direction: column; gap: 2px;">
                                    <template x-for="opt in options" :key="opt">
                                        <div @click="window.location.href = '?per_page=' + opt + '&sort={{ request('sort') }}&status_dosen={{ request('status_dosen') }}&search={{ request('search') }}'"
                                             style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border-radius: 6px; cursor: pointer; transition: all 0.2s; min-width: 80px;"
                                             :style="perPage == opt ? 'background: #F8FAFC; color: #0B266E;' : 'background: transparent; color: #4B5563;'"
                                             onmouseover="if (this.getAttribute('data-active') !== 'true') this.style.background = '#F3F4F6'"
                                             onmouseout="if (this.getAttribute('data-active') !== 'true') this.style.background = 'transparent'"
                                             x-bind:data-active="perPage == opt">
                                            <span x-text="opt" style="font-size: 13px; font-weight: 500;"></span>
                                            <svg x-show="perPage == opt" style="width: 14px; height: 14px; color: #0B266E; margin-left: 12px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            <div style="font-size:13px; color:#4B5563;">Menampilkan
                                {{ $pendaftaran->firstItem() ?? 0 }} sampai {{ $pendaftaran->lastItem() ?? 0 }} dari
                                {{ $pendaftaran->total() }} data
                            </div>
                        </div>"""

text = text.replace(old_string, new_string)

with open("Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php", "w", encoding="utf-8") as f:
    f.write(text)

print("done")