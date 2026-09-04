<x-eoffice::manajemen-praktikum.layout pageTitle="{{ $praktikum ? $praktikum->nama : 'Belum Ada Praktikum' }} / Modul">

@if($praktikum)
    <x-eoffice::manajemen-praktikum.koor-header :praktikum="$praktikum" />
@else
<div class="flex flex-col flex-1 overflow-y-auto pr-2 pb-6 min-h-0">

    <div class="mp-page-header">
        <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <h1 class="mp-page-title">Kelola Modul Praktikum</h1>
                <span class="mp-badge" style="background:#E0E7FF;color:#6366F1;border-radius:999px;padding:3px 10px;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:5px;"><span class="dot" style="background:#6366F1;"></span>Koordinator</span>
            </div>
            <p class="mp-page-sub">Belum ada praktikum aktif</p>
        </div>
    </div>
@endif

@if(!$praktikum)
<div class="mp-alert warning flex-shrink-0">
    Anda belum ditugaskan sebagai koordinator praktikum aktif.
</div>
@else



<div class="grid grid-cols-1 gap-[14px]">
    <div class="mp-card">
        <div class="mp-card-header">
            <span class="mp-card-title">Daftar Modul</span>
            
        </div>
        <div class="overflow-x-auto flex-1">
            <table class="mp-table">
                <thead>
                    <tr style="background:#F9FAFB;">
                        <th class="mp-th text-center" style="padding:10px 16px; width:60px;">NO</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">MODUL</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">ASISTEN</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">KONTEN</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($moduls as $m)
                    <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">
                        <td style="padding:12px 16px; text-align:center; font-weight:600; color:#666D80; font-size:12px;">
                            {{ $m->urutan }}
                        </td>
                        <td style="padding:12px 16px;">
                            <div style="font-weight:600; color:#0D0D12; font-size:13px;">{{ $m->nama }}</div>
                        </td>

                        <td style="padding:12px 16px;font-size:12px;color:#666D80;">{{ $m->modulAsprak->pluck('asprak.user.name')->filter()->join(', ') ?: '-' }}</td>
                        <td style="padding:12px 16px;font-size:12px;color:#666D80;">{{ $m->materi->count() }} materi, {{ $m->tugas->count() }} tugas</td>
                        <td style="padding:12px 16px;">
                            <div class="flex gap-2">
                                <div x-data="{ showDetail: false }" class="inline-block">
                                    <button type="button" @click="showDetail = true" class="mp-btn primary sm" style="text-decoration:none; font-size:11px; padding-top:6px; padding-bottom:6px;">Detail</button>

                                    <template x-teleport="body">
                                        <div x-show="showDetail" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm transition-all duration-300" style="display: none;" x-cloak>
                                            <div @click.away="showDetail = false" x-show="showDetail"
                                                 x-transition:enter="transition ease-out duration-300" 
                                                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                                                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                 x-transition:leave="transition ease-in duration-200" 
                                                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                                                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                 class="bg-white rounded-[16px] shadow-2xl w-full max-w-lg mx-4 flex flex-col max-h-[90vh]">
                                                
                                                <div class="px-6 py-4 border-b border-[#DFE1E7] flex-shrink-0">
                                                    <div class="font-bold text-[16px] text-[#0D0D12]">Detail Modul</div>
                                                </div>
                                                
                                                <div class="overflow-y-auto flex-1">
                                                    <form method="POST" action="{{ route('eoffice.manprak.koor.modul.update', $m->id) }}" class="p-6">
                                                        @csrf
                                                        @method('PUT')
                                                        
                                                        <div class="flex flex-col gap-4">
                                                            <div>
                                                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Modul <span class="text-red-500">*</span></label>
                                                                <input type="text" name="nama" value="{{ $m->nama }}" class="mp-input w-full" required>
                                                            </div>
                                                            <div>
                                                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Urutan <span class="text-red-500">*</span></label>
                                                                <input type="number" name="urutan" value="{{ $m->urutan }}" class="mp-input w-full" required>
                                                            </div>

                                                            <!-- Materi Modul -->
                                                            <div>
                                                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Materi Modul</label>
                                                                @if($m->materi->count() > 0)
                                                                    <div class="border border-[#DFE1E7] rounded-[8px] bg-white overflow-hidden shadow-sm border-b-0">
                                                                        @foreach($m->materi as $mat)
                                                                            <div class="px-3 py-2 border-b border-[#DFE1E7] text-[13px] text-[#353849] truncate">
                                                                                {{ $mat->judul }}
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <input type="text" class="mp-input w-full" value="Belum ada materi." disabled style="background: #F9FAFB; color: #808897; cursor: not-allowed;">
                                                                @endif
                                                            </div>

                                                            <!-- Tugas Modul -->
                                                            <div>
                                                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Tugas Modul</label>
                                                                @if($m->tugas->count() > 0)
                                                                    <div class="border border-[#DFE1E7] rounded-[8px] bg-white overflow-hidden shadow-sm border-b-0">
                                                                        @foreach($m->tugas as $tug)
                                                                            <div class="px-3 py-2 border-b border-[#DFE1E7] text-[13px] text-[#353849] truncate">
                                                                                {{ $tug->judul }}
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <input type="text" class="mp-input w-full" value="Belum ada tugas." disabled style="background: #F9FAFB; color: #808897; cursor: not-allowed;">
                                                                @endif
                                                            </div>

                                                            <!-- Asisten Praktikum Modul -->
                                                            <div>
                                                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Asisten Modul</label>
                                                                @if(($m->modulAsprak ?? collect())->count() > 0)
                                                                    <div class="border border-[#DFE1E7] rounded-[8px] bg-white overflow-hidden shadow-sm border-b-0">
                                                                        @foreach($m->modulAsprak as $ma)
                                                                            <div class="px-3 py-2 border-b border-[#DFE1E7] text-[13px] text-[#353849] truncate">
                                                                                {{ $ma->asprak->user->name }}
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                @else
                                                                    <input type="text" class="mp-input w-full" value="Belum ada asisten." disabled style="background: #F9FAFB; color: #808897; cursor: not-allowed;">
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="flex gap-3 justify-end mt-6 pt-5 border-t border-[#DFE1E7]">
                                                            <button type="button" @click="showDetail = false" class="mp-btn secondary md px-5" style="border-radius: 8px;">Batal</button>
                                                            <button type="submit" class="mp-btn primary md px-5" style="border-radius: 8px; background-color: #0B266E; border-color: #0B266E; color: white;">Simpan Perubahan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                @if($praktikum->is_active)
                                <form method="POST" action="{{ route('eoffice.manprak.koor.modul.destroy', $m->id) }}" onsubmit="return confirm('Hapus modul ini?')">
                                    @csrf @method('DELETE')
                                    <button class="mp-btn destructive sm" style="font-size:11px; padding-top:6px; padding-bottom:6px;">Hapus</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div style="padding:48px;text-align:center;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada modul.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
            </div>

{{-- Pagination Custom Fungsional --}}
        @if(isset($moduls) && method_exists($moduls, 'hasPages') && ($moduls->hasPages() || $moduls->total() > 0))
            <div
                style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid var(--c-border);">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div x-data="{ open: false, selected: '{{ request('per_page', 10) }}', options: [5, 10, 20] }"
                        class="relative" @click.away="open = false">
                        <div class="flex items-center gap-2 cursor-pointer border rounded-[8px] px-3 py-1.5 transition-colors focus:outline-none"
                            :class="open ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'"
                            @click="open = !open">
                            <span class="text-[12px] text-[#666D80]" :class="open ? 'text-[#0B266E]' : ''">Per
                                halaman</span>
                            <div class="flex items-center gap-1 font-semibold text-[12px]">
                                <span x-text="selected"></span>
                                <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                    :class="{'rotate-180': open, 'text-[#0B266E]': open, 'text-[#666D80]': !open}"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                        </div>
                        <div x-show="open" @click.away="open = false" style="display: none;"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute bottom-full left-0 mb-2 z-10 w-full min-w-[80px] bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] py-2 px-1.5 flex flex-col">
                            <template x-for="option in options" :key="option">
                                <a :href="'?per_page=' + option + '&' + decodeURIComponent(new URLSearchParams(Object.fromEntries(Object.entries(Object.fromEntries(new URLSearchParams(window.location.search))).filter(([k,v])=>k!=='per_page'))).toString())"
                                    class="flex items-center justify-between px-3 py-2 rounded-[6px] cursor-pointer text-[12px] transition-colors mb-0.5 last:mb-0 no-underline"
                                    :class="selected == option ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                                    <span x-text="option"></span>
                                    <svg x-show="selected == option" class="w-3.5 h-3.5 flex-shrink-0 text-[#0B266E] ml-2"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                </a>
                            </template>
                        </div>
                    </div>
                    <div style="font-size:13px; color:var(--c-fg-sec);">Menampilkan {{ $moduls->firstItem() ?? 0 }}
                        sampai {{ $moduls->lastItem() ?? 0 }} dari {{ $moduls->total() }} data</div>
                </div>

                <div style="display:flex; gap:4px;">
                    @if ($moduls->onFirstPage())
                        <span
                            style="width:32px; height:32px; border:1px solid var(--c-border); background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:var(--c-border-strong);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round">
                                <polyline points="15 18 9 12 15 6" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $moduls->previousPageUrl() }}"
                            style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round">
                                <polyline points="15 18 9 12 15 6" />
                            </svg>
                        </a>
                    @endif

                    @php
                        $current = $moduls->currentPage();
                        $last = $moduls->lastPage();
                        $start = max(1, $current - 1);
                        $end = min($start + 2, $last);
                    @endphp

                    @for ($i = $start; $i <= $end; $i++)
                        @if ($i == $current)
                            <span
                                style="width:32px; height:32px; background:#0B266E; color:#fff; font-size:13px; font-weight:600; border-radius:6px; display:flex; align-items:center; justify-content:center;">
                                {{ $i }}
                            </span>
                        @else
                            <a href="{{ $moduls->url($i) }}"
                                style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; color:var(--c-fg-sec); font-size:13px; font-weight:600; border-radius:6px; display:flex; align-items:center; justify-content:center; text-decoration:none; transition:all 0.2s;"
                                onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#fff'">
                                {{ $i }}
                            </a>
                        @endif
                    @endfor

                    @if ($moduls->hasMorePages())
                        <a href="{{ $moduls->nextPageUrl() }}"
                            style="width:32px; height:32px; border:1px solid var(--c-border); background:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; cursor:pointer; color:var(--c-fg); text-decoration:none;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round">
                                <polyline points="9 18 15 12 9 6" />
                            </svg>
                        </a>
                    @else
                        <span
                            style="width:32px; height:32px; border:1px solid var(--c-border); background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:var(--c-border-strong);">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round">
                                <polyline points="9 18 15 12 9 6" />
                            </svg>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </div>
    
    <div class="grid grid-cols-2 gap-[14px] items-stretch">
        <div class="mp-card flex flex-col h-full" style="padding:20px;">
        <div style="font-weight:700;font-size:14px;color:#0D0D12;margin-bottom:16px;">Tambah Modul</div>
        <form method="POST" action="{{ route('eoffice.manprak.koor.modul.store') }}" class="flex flex-col gap-3 flex-1" x-data="{ namaModul: '' }">
            @csrf
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Nama Modul <span class="text-red-500">*</span></label>
                <input name="nama" x-model="namaModul" required class="mp-input w-full" placeholder="Misal: Pengenalan Jaringan">
            </div>
            
            <div>
                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Urutan <span class="text-red-500">*</span></label>
                <input type="number" name="urutan" min="1" value="{{ ($moduls->max('urutan') ?? 0) + 1 }}" required class="mp-input w-full">
            </div>
            <input type="hidden" name="jadwal_minggu" value="">
            <input type="hidden" name="deskripsi" value="">
            
            <button 
                type="submit" 
                class="md w-full font-bold rounded-[8px] transition-colors duration-200 mt-auto"
                style="height: 40px; font-size: 13px;"
                :class="namaModul.trim() ? 'bg-[#0B266E] text-white hover:bg-[#081e59]' : 'bg-[#F4F6F9] text-[#0B266E] pointer-events-none'"
                :disabled="!namaModul.trim()">
                Simpan
            </button>
        </form>
    </div>
                    <div class="mp-card flex flex-col h-full" style="padding:20px;">
                <div style="font-weight:700;font-size:14px;color:#0D0D12;margin-bottom:16px;">Penugasan Asisten Modul</div>
                <form method="POST" action="{{ route('eoffice.manprak.koor.bagi-modul.store') }}" 
                      class="flex flex-col gap-3 flex-1" 
                      x-data="{ asprak_id: '', modul_id: '' }"
                      @change-asprak.window="asprak_id = $event.detail"
                      @change-modul.window="modul_id = $event.detail">
                    @csrf
                    
                    @php
                        $asistenOptions = [];
                        foreach($asistenList ?? [] as $a) {
                            $asistenOptions[] = ['value' => (string)$a->id, 'label' => $a->user?->name];
                        }
                        
                        $modulOpt = [];
                        if (isset($modulList)) {
                            foreach($modulList as $m) {
                                $modulOpt[] = ['value' => (string)$m->id, 'label' => $m->nama];
                            }
                        }
                    @endphp

                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Pilih Asisten <span class="text-red-500">*</span></label>
                        <x-eoffice::manajemen-praktikum.ui.select 
                            name="asprak_id" 
                            :options="$asistenOptions"
                            placeholder="Pilih Asisten Praktikum"
                            onChange="$dispatch('change-asprak', option.value)" />
                    </div>
                    <div>
                        <label class="block text-[12px] font-semibold text-[#353849] mb-1">Pilih Modul <span class="text-red-500">*</span></label>
                        <x-eoffice::manajemen-praktikum.ui.select 
                            name="modul_id" 
                            :options="$modulOpt"
                            placeholder="Pilih Modul Praktikum"
                            onChange="$dispatch('change-modul', option.value)" />
                    </div>
                    
                    <button 
                        type="submit" 
                        class="md w-full font-bold rounded-[8px] transition-colors duration-200 mt-auto"
                        style="height: 40px; font-size: 13px;"
                        :class="(asprak_id && modul_id) ? 'bg-[#0B266E] text-white hover:bg-[#081e59]' : 'bg-[#F4F6F9] text-[#0B266E] pointer-events-none'"
                        :disabled="!(asprak_id && modul_id)">
                        Simpan
                    </button>
                </form>
            </div>
    </div>
</div>
</div>
@endif
</x-eoffice::manajemen-praktikum.layout>