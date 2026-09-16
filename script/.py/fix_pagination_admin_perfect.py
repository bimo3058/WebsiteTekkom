import sys
import re

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# I will extract everything between {{-- Pagination Custom Fungsional --}} and the end </div>
start_marker = "{{-- Pagination Custom Fungsional --}}"
start_idx = text.find(start_marker)

# Find the end by looking for @endif followed by </div> </div> </div> @endif
# Alternatively, I can just replace everything from start_marker up to the final </div> that precedes </x-eoffice::manajemen-praktikum.layout>
end_idx = text.find('</x-eoffice::manajemen-praktikum.layout>', start_idx)

# Backtrack to the first </div> before the end of the Pagination Custom @endif
end_marker = "@endif"
match = list(re.finditer(end_marker, text[start_idx:end_idx]))[-1]
actual_end_idx = start_idx + match.end()

new_pagination = """{{-- Pagination Custom Fungsional --}}
                @if(isset($pendaftaran) && method_exists($pendaftaran, 'hasPages') && ($pendaftaran->hasPages() || $pendaftaran->total() > 0))
                <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid var(--c-border);">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div x-data="{ open: false, selected: '{{ request('per_page', 10) }}', options: [5, 10, 20] }" 
                             class="relative"
                             @click.away="open = false">
                            <div class="flex items-center gap-2 cursor-pointer border rounded-[8px] px-3 py-1.5 transition-colors focus:outline-none"
                                 :class="open ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'"
                                 @click="open = !open">
                                <span class="text-[12px] text-[#666D80]" :class="open ? 'text-[#0B266E]' : ''">Per halaman</span>
                                <div class="flex items-center gap-1 font-semibold text-[12px]">
                                    <span x-text="selected"></span>
                                    <svg class="w-3.5 h-3.5 transition-transform duration-200" 
                                         :class="{'rotate-180': open, 'text-[#0B266E]': open, 'text-[#666D80]': !open}" 
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </div>
                            </div>
                            <div x-show="open" @click.away="open = false" style="display: none;" 
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute top-full left-0 mt-2 z-10 w-full min-w-[80px] bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] py-2 px-1.5 flex flex-col">
                                <template x-for="option in options" :key="option">
                                    <a :href="'?per_page=' + option + '&sort={{ request('sort') }}&status_dosen={{ request('status_dosen') }}&search={{ request('search') }}#daftar-pendaftaran'" 
                                           class="flex items-center justify-between px-3 py-2 rounded-[6px] cursor-pointer text-[12px] transition-colors mb-0.5 last:mb-0 no-underline"
                                           :class="selected == option ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                                        <span x-text="option"></span>
                                        <svg x-show="selected == option" class="w-3.5 h-3.5 flex-shrink-0 text-[#0B266E] ml-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </a>
                                </template>
                            </div>
                        </div>
                        <div style="font-size:13px; color:var(--c-fg-sec);">Menampilkan {{ $pendaftaran->firstItem() ?? 0 }} sampai {{ $pendaftaran->lastItem() ?? 0 }} dari {{ $pendaftaran->total() }} data</div>
                    </div>
                    
                    <div style="display:flex; gap:4px;">
                        @if ($pendaftaran->onFirstPage())
                            <span style="width:32px; height:32px; border:1px solid var(--c-border); background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:var(--c-border-strong);">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                            </span>
                        @else
                            <a href="{{ $pendaftaran->previousPageUrl() }}#daftar-pendaftaran" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
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
                                <span style="width:32px; height:32px; background:#0B266E; color:#fff; font-size:13px; font-weight:600; border-radius:6px; display:flex; align-items:center; justify-content:center;">
                                    {{ $i }}
                                </span>
                            @else
                                <a href="{{ $pendaftaran->url($i) }}#daftar-pendaftaran" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; color:var(--c-fg-sec); font-size:13px; font-weight:600; border-radius:6px; display:flex; align-items:center; justify-content:center; text-decoration:none; transition:all 0.2s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                                    {{ $i }}
                                </a>
                            @endif
                        @endfor
            
                        @if ($pendaftaran->hasMorePages())
                            <a href="{{ $pendaftaran->nextPageUrl() }}#daftar-pendaftaran" style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                            </a>
                        @else
                            <span style="width:32px; height:32px; border:1px solid var(--c-border); background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:var(--c-border-strong);">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                            </span>
                        @endif
                    </div>
                </div>
                @endif"""

text = text[:start_idx] + new_pagination + text[actual_end_idx:]

with open('Modules/EOffice/resources/views/manajemen-praktikum/dosen/pendaftaran-koor.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)

print('Admin Code Dropped Perfectly')