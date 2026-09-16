import sys

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

start_marker = '{{-- Pagination Custom Fungsional --}}'
start_idx = text.find(start_marker)

end_idx = text.find('@endif', text.find('@if ($pendaftaran->hasMorePages())', start_idx))
end_idx = text.find('</div>', end_idx)
end_idx = text.find('</div>', end_idx + 1)
end_idx += 6 # include </div>

extra_endif = text.find('@endif', end_idx)
if extra_endif != -1 and extra_endif - end_idx < 50:
    end_idx = extra_endif + 6

new_block = """{{-- Pagination Custom Fungsional --}}
                @if(isset($pendaftaran) && method_exists($pendaftaran, 'hasPages') && ($pendaftaran->hasPages() || $pendaftaran->total() > 0))
                    <div x-data="{
                            open: false,
                            options: [5, 10, 20],
                            perPage: '{{ request('per_page', 10) }}'
                        }"
                        style="display: flex; flex-direction: row; align-items: center; justify-content: space-between; padding: 12px 20px; border-top: 1px solid #E5E7EB; width: 100%; transition: padding-bottom 0.1s ease;"
                        :style="open ? { 'padding-bottom': '120px' } : {}">
                        
                        <div style="display: flex; align-items: center; gap: 12px;" @click.away="open = false">
                            {{-- Dropdown Per Halaman --}}
                            <div style="position: relative; font-size: 13px; font-family: inherit;">
                                <div @click="open = !open" 
                                     :style="open ? { 'border-color': '#0B266E', 'background-color': '#F8FAFC' } : { 'border-color': '#E5E7EB', 'background-color': 'white' }"
                                     style="display: flex; align-items: center; justify-content: space-between; height: 32px; width: 130px; border: 1px solid #E5E7EB; border-radius: 6px; padding: 0 12px; cursor: pointer; user-select: none;">
                                    <div style="display: flex; align-items: center; gap: 4px;">
                                        <span :style="open ? { 'color': '#666D80' } : { 'color': '#9CA3AF' }" style="font-weight: 400; color: #9CA3AF;">Per halaman</span>
                                        <span :style="open ? { 'color': '#0B266E', 'font-weight': '600' } : { 'color': '#111827', 'font-weight': '500' }" style="color: #111827; font-weight: 500;" x-text="perPage"></span>
                                    </div>
                                    <svg :style="open ? { 'color': '#0B266E', 'transform': 'rotate(180deg)' } : { 'color': '#111827', 'transform': 'rotate(0deg)' }" width="14" height="14" style="width: 14px; min-width: 14px; height: 14px; min-height: 14px; stroke-width: 2px; transition: transform 0.2s; color: #111827;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                                
                                <div x-show="open" style="position: absolute; z-index: 9999; top: calc(100% + 4px); left: 0; width: 100%; border: 1px solid #E5E7EB; border-radius: 8px; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); padding: 4px; display: flex; flex-direction: column; gap: 2px;" x-transition>
                                    <template x-for="opt in options" :key="opt">
                                        <div @click="window.location.href = '?per_page=' + opt + '&sort={{ request('sort') }}&status_dosen={{ request('status_dosen') }}&search={{ request('search') }}'"
                                             style="display: flex; align-items: center; justify-content: space-between; padding: 8px 12px; border-radius: 6px; cursor: pointer;"
                                             :style="perPage == opt ? { 'background-color': '#F8FAFC', 'color': '#0B266E' } : { 'background-color': 'transparent', 'color': '#4B5563' }"
                                             onmouseover="if(this.getAttribute('data-active') !== 'true') this.style.backgroundColor='#F3F4F6'"
                                             onmouseout="if(this.getAttribute('data-active') !== 'true') this.style.backgroundColor='transparent'"
                                             x-bind:data-active="perPage == opt">
                                            <span x-text="opt" style="font-size: 13px; font-weight: 400;"></span>
                                            <svg x-show="perPage == opt" width="14" height="14" style="width: 14px; min-width: 14px; height: 14px; min-height: 14px; color: #0B266E;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            
                            <div style="font-size: 13px; color: #4B5563; padding-left: 4px; display: flex; align-items: center;">Menampilkan <span style="margin: 0 4px;">{{ $pendaftaran->firstItem() ?? 0 }}</span> sampai <span style="margin: 0 4px;">{{ $pendaftaran->lastItem() ?? 0 }}</span> dari <span style="margin: 0 4px;">{{ $pendaftaran->total() }}</span> data</div>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 4px;">
                            @if ($pendaftaran->onFirstPage())
                                <span style="width: 32px; height: 32px; border: 1px solid #E5E7EB; background: #FAFAFA; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #9CA3AF;">
                                    <svg width="14" height="14" style="width: 14px; min-width: 14px; height: 14px; min-height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6" /></svg>
                                </span>
                            @else
                                <a href="{{ $pendaftaran->previousPageUrl() }}#daftar-pendaftaran" style="width: 32px; height: 32px; border: 1px solid #E5E7EB; background: #fff; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #4B5563; text-decoration: none;">
                                    <svg width="14" height="14" style="width: 14px; min-width: 14px; height: 14px; min-height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6" /></svg>
                                </a>
                            @endif

                            @php
                                $current = $pendaftaran->currentPage();
                                $last = $pendaftaran->lastPage();
                                $start = max(1, $current - 1);
                                $end = min($start + 2, $last);
                            @endphp

                            @for ($i = $start; $i <= $end; $i++)
                                @if ($i == $current)
                                    <span style="width: 32px; height: 32px; background: #0B266E; color: #fff; font-size: 13px; font-weight: 600; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                        {{ $i }}
                                    </span>
                                @else
                                    <a href="{{ $pendaftaran->url($i) }}#daftar-pendaftaran" style="width: 32px; height: 32px; border: 1px solid #E5E7EB; background: #fff; color: #4B5563; font-size: 13px; font-weight: 600; border-radius: 6px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                        {{ $i }}
                                    </a>
                                @endif
                            @endfor

                            @if ($pendaftaran->hasMorePages())
                                <a href="{{ $pendaftaran->nextPageUrl() }}#daftar-pendaftaran" style="width: 32px; height: 32px; border: 1px solid #E5E7EB; background: #fff; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #4B5563; text-decoration: none;">
                                    <svg width="14" height="14" style="width: 14px; min-width: 14px; height: 14px; min-height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6" /></svg>
                                </a>
                            @else
                                <span style="width: 32px; height: 32px; border: 1px solid #E5E7EB; background: #FAFAFA; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #9CA3AF;">
                                    <svg width="14" height="14" style="width: 14px; min-width: 14px; height: 14px; min-height: 14px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6" /></svg>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif"""

new_text = text[:start_idx] + new_block + '\n' + text[end_idx:]

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(new_text)

print('Replaced successfully.')