<x-eoffice::manajemen-praktikum.layout pageTitle="{{ $praktikum ? $praktikum->nama : 'Belum Ada Praktikum' }} / Daftar Praktikan">
    @if(isset($praktikum) && $praktikum)
        <x-eoffice::manajemen-praktikum.koor-header :praktikum="$praktikum" />
    @endif


{{-- Page Header --}}


@if(!$praktikum)
<div class="mp-alert warning flex-shrink-0">Anda belum memiliki praktikum aktif.</div>
@else

@if ($errors->any())
<div class="mp-flash mp-flash-error" style="border-radius:10px;border:1px solid #DF1C41;margin-bottom:16px;">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div style="flex:1;">
        <div style="font-weight:700;">Gagal:</div>
        <ul style="margin:4px 0 0;padding-left:20px;list-style-type:disc;">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
</div>
@endif

@if (session('success'))
<div class="mp-flash mp-flash-success" style="border-radius:10px;border:1px solid #10B981;margin-bottom:16px;padding:12px 16px;display:flex;gap:10px;background:#ECFDF5;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-top:2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    <div style="flex:1;">
        <div style="font-size:13px;color:#065F46;font-weight:500;">{{ session('success') }}</div>
    </div>
</div>
@endif

<div x-data="manajemenKelompok()">
    
    {{-- TOMBOL UTAMA KELOLA KELOMPOK --}}
    

    {{-- â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    {{-- SECTION: Tabel Praktikan Terdaftar (Read-Only)      --}}
    {{-- â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    

    <div class="mp-card flex-shrink-0">
        <div class="mp-card-header">
            <span class="mp-card-title">
                Praktikan Terdaftar
                
            </span>
            <div class="right" style="gap:8px;display:flex;align-items:center;flex-wrap:wrap;">
                {{-- Import CSV --}}
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
                    
                    
                </form>
            </div>
        </div>

        <div style="overflow-x:auto;">
            @php
                $shiftRowspan = [];
                $kelompokRowspan = [];
                
                $items = $praktikans->items();
                $totalItems = count($items);

                // Calculate rowspan for shift
                $i = 0;
                while ($i < $totalItems) {
                    $val = $items[$i]->shift;
                    if (empty($val)) {
                        $shiftRowspan[$i] = 1;
                        $i++;
                        continue;
                    }
                    $count = 1;
                    while ($i + $count < $totalItems && $items[$i + $count]->shift === $val) {
                        $count++;
                    }
                    $shiftRowspan[$i] = $count;
                    for ($j = 1; $j < $count; $j++) {
                        $shiftRowspan[$i + $j] = 0;
                    }
                    $i += $count;
                }

                // Calculate rowspan for kelompok
                $i = 0;
                while ($i < $totalItems) {
                    $valK = $items[$i]->kelompok;
                    $valS = $items[$i]->shift;
                    if (empty($valK)) {
                        $kelompokRowspan[$i] = 1;
                        $i++;
                        continue;
                    }
                    $count = 1;
                    while (
                        $i + $count < $totalItems && 
                        $items[$i + $count]->kelompok === $valK && 
                        $items[$i + $count]->shift === $valS
                    ) {
                        $count++;
                    }
                    $kelompokRowspan[$i] = $count;
                    for ($j = 1; $j < $count; $j++) {
                        $kelompokRowspan[$i + $j] = 0;
                    }
                    $i += $count;
                }
            @endphp
            <table class="mp-table" style="min-width:900px;">
                <thead>
                    <tr style="background:#F9FAFB;">
                        <th class="mp-th text-left" style="padding:10px 16px;width:40px;">NO</th>
                        <th class="mp-th text-left" style="padding:10px 16px;">NAMA MAHASISWA</th>
                        <th class="mp-th text-left" style="padding:10px 16px;width:140px;">NIM</th>
                        <th class="mp-th text-center" style="padding:10px 16px;width:150px;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;">Kelompok</th>
                        <th class="mp-th text-center" style="padding:10px 16px;width:150px;border-right:1px solid #DFE1E7;">Shift</th>
                        <th class="mp-th text-left" style="padding:10px 16px;width:100px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($praktikans as $idx => $p)
                    <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7; transition: background 0.2s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='transparent'">
                        <td style="padding:12px 16px;font-size:12px;color:#A4ABB8;font-weight:600;">
                            {{ $praktikans->firstItem() + $idx }}
                        </td>
                        <td style="padding:12px 16px;">
                            <div class="flex items-center gap-[10px]">
                                <div class="mp-av yellow" style="width:28px;height:28px;font-size:11px;">{{ strtoupper(substr($p->user?->name ?? 'M', 0, 2)) }}</div>
                                <div>
                                    <div style="font-weight:600;color:#0D0D12;font-size:13px;">{{ $p->user?->name ?? '-' }}</div>
                                    <div style="font-size:11px;color:#666D80;">{{ $p->user?->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding:12px 16px;font-size:12px;font-weight:600;color:#353849;">
                            {{ $p->user?->student?->student_number ?? '-' }}
                        </td>
                        
                        {{-- KELOMPOK COLUMN with Dynamic Rowspan --}}
                        @if(isset($kelompokRowspan[$idx]) && $kelompokRowspan[$idx] > 0)
                            <td rowspan="{{ $kelompokRowspan[$idx] }}" style="padding:16px;text-align:center;vertical-align:middle;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;background:#FFF;font-size:15px;font-weight:700;color:#0D0D12;">
                                @if($p->kelompok)
                                    {{ $p->kelompok }}
                                @else
                                    <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">-</span>
                                @endif
                            </td>
                        @endif
                        
                        {{-- SHIFT COLUMN with Dynamic Rowspan --}}
                        @if(isset($shiftRowspan[$idx]) && $shiftRowspan[$idx] > 0)
                            <td rowspan="{{ $shiftRowspan[$idx] }}" style="padding:16px;text-align:center;vertical-align:middle;border-right:1px solid #DFE1E7;background:#FFF;font-size:15px;font-weight:700;color:#0D0D12;">
                                @if($p->shift)
                                    {{ $p->shift }}
                                @else
                                    <span style="font-size:11px;color:#A4ABB8;font-weight:normal;">-</span>
                                @endif
                            </td>
                        @endif

                        <td style="padding:12px 16px;">
                            <span class="mp-badge success sm"><span class="dot"></span>{{ $p->status ?? 'terdaftar' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div style="padding:48px;text-align:center;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada praktikan yang terdaftar.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        
        @if(isset($praktikans) && method_exists($praktikans, 'hasPages') && ($praktikans->hasPages() || $praktikans->total() > 0))
            <div
                style="display:flex; align-items:center; justify-content:space-between; padding:12px 20px; border-top:1px solid #DFE1E7;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div x-data="{ open: false, selected: '{{ request('per_page', 10) }}', options: [10, 20, 30] }"
                        class="relative" @click.away="open = false">
                        <div class="flex items-center gap-2 cursor-pointer border rounded-[8px] px-3 py-1.5 transition-colors focus:outline-none"
                            :class="open ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'"
                            @click="open = !open">
                            <span class="text-[12px] text-[#666D80]" :class="open ? 'text-[#0B266E]' : ''">Per halaman</span>
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
                    <div style="font-size:13px; color:#666D80;">Menampilkan {{ $praktikans->firstItem() ?? 0 }}
                        sampai {{ $praktikans->lastItem() ?? 0 }} dari {{ $praktikans->total() }} data</div>
                </div>

                <div style="display:flex; gap:4px;">
                    @if ($praktikans->onFirstPage())
                        <span
                            style="width:32px; height:32px; border:1px solid #DFE1E7; background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#A4ABB8;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                        </span>
                    @else
                        <a href="{{ $praktikans->previousPageUrl() }}"
                            style="width:32px; height:32px; border:1px solid #DFE1E7; background:#FFF; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#353849; text-decoration:none; cursor:pointer;"
                            onmouseover="this.style.background='#F6F8FA'" onmouseout="this.style.background='#FFF'">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                        </a>
                    @endif

                    @php
                        $currentPage = $praktikans->currentPage();
                        $lastPage = $praktikans->lastPage();
                        $start = max(1, $currentPage - 1);
                        $end = min($lastPage, $currentPage + 1);
                    @endphp
                    @for ($i = $start; $i <= $end; $i++)
                        @if ($i == $currentPage)
                            <span
                                style="width:32px; height:32px; background:#0B266E; color:#FFF; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600;">
                                {{ $i }}
                            </span>
                        @else
                            <a href="{{ $praktikans->url($i) }}"
                                style="width:32px; height:32px; border:1px solid #DFE1E7; background:#FFF; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#353849; text-decoration:none; font-size:13px; font-weight:500;"
                                onmouseover="this.style.background='#F6F8FA'" onmouseout="this.style.background='#FFF'">
                                {{ $i }}
                            </a>
                        @endif
                    @endfor

                    @if ($praktikans->hasMorePages())
                        <a href="{{ $praktikans->nextPageUrl() }}"
                            style="width:32px; height:32px; border:1px solid #DFE1E7; background:#FFF; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#353849; text-decoration:none; cursor:pointer;"
                            onmouseover="this.style.background='#F6F8FA'" onmouseout="this.style.background='#FFF'">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </a>
                    @else
                        <span
                            style="width:32px; height:32px; border:1px solid #DFE1E7; background:#FAFAFA; border-radius:6px; display:flex; align-items:center; justify-content:center; color:#A4ABB8;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                        </span>
                    @endif
                </div>
            </div>
        @endif

    </div>

    {{-- â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    {{-- MODAL UTAMA: KELOLA KELOMPOK & SHIFT                  --}}
    {{-- â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    

    {{-- â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    
    
    <div class="mp-card" style="margin-top: 24px; margin-bottom: 24px;">
        <div style="padding: 24px;">
            <div style="font-weight:700; font-size:16px; color:#0D0D12; margin-bottom:16px;">Buat Kelompok & Shift</div>
            <form action="{{ route('eoffice.manprak.koor.praktikan.settings') }}" method="POST" onsubmit="return confirm('PENTING: Menyimpan pengaturan baru akan MENGHAPUS manual plotting sebelumnya dan mendistribusikan ulang (reset) semua anggota. Yakin ingin melanjutkan?');">
                        @csrf
                        <input type="hidden" name="praktikum_id" value="{{ $praktikum->id }}">
                        <div style="display:flex; gap:16px; align-items:flex-end;">
                            <div style="flex:1;">
                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Jumlah Kelompok</label>
                                <input type="number" name="jumlah_kelompok" value="{{ $praktikum->jumlah_kelompok ?? 0 }}" min="0" class="mp-input" style="width:100%; height:38px;">
                            </div>
                            <div style="flex:1;">
                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Jumlah Shift</label>
                                <input type="number" name="jumlah_shift" value="{{ $praktikum->jumlah_shift ?? 0 }}" min="0" class="mp-input" style="width:100%; height:38px;">
                            </div>
                            <div style="flex:1.5;">
                                <label class="block text-[12px] font-semibold text-[#353849] mb-1">Metode Pembagian</label>
                                <x-eoffice::manajemen-praktikum.ui.select name="method" :options="[['value' => 'urutan_sistem', 'label' => 'Berurutan'], ['value' => 'acak', 'label' => 'Acak']]" selected="urutan_sistem" />
                            </div>
                            <div style="flex:1.5;">
                                <button type="submit" class="mp-btn primary md" style="width:100%; background:#0B266E; height:38px; display:flex; align-items:center; justify-content:center; gap:6px;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                    Simpan Pengaturan
                                </button>
                            </div>
                        </div>
                    </form>
        </div>
        
        <div style="border-top:1px dashed #DFE1E7; margin:0 24px;"></div>
        
        <div style="padding: 24px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-end; margin-bottom:16px;">
                        <div>
                            <div style="font-weight:700; font-size:15px; color:#0D0D12;">Kelola Manual Anggota Kelompok</div>
                            <div style="font-size:12px; color:#666D80; margin-top:2px;">Klik ikon pensil pada kelompok untuk mengubah anggota.</div>
                        </div>
                        <div style="font-size:12px; font-weight:600; padding:6px 12px; background:#FEF2F2; color:#DC2626; border-radius:6px; border:1px solid #FECACA;" x-show="unassignedCount > 0">
                            <span x-text="unassignedCount"></span> Praktikan belum dapat kelompok!
                        </div>
                        <div style="font-size:12px; font-weight:600; padding:6px 12px; background:#ECFDF5; color:#059669; border-radius:6px; border:1px solid #A7F3D0;" x-show="unassignedCount === 0">
                            Semua praktikan sudah terdistribusi
                        </div>
                    </div>

                    @if(($praktikum->jumlah_kelompok ?? 0) > 0)
                      <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(240px, 1fr)); gap:16px;">
                          @for($i=1; $i<=($praktikum->jumlah_kelompok ?? 0); $i++)
                              @php 
                                  $groupsPerShift = max(1, ($praktikum->jumlah_kelompok ?? 1) / ($praktikum->jumlah_shift ?? 1));
                                  $defShift = (int) ceil($i / $groupsPerShift);
                                  if($defShift > ($praktikum->jumlah_shift ?? 1)) $defShift = $praktikum->jumlah_shift;
                                  $jmlAnggota = $praktikansSemua->where('kelompok', $i)->count();
                              @endphp
                              <div style="border:1px solid #DFE1E7; border-radius:10px; padding:16px; background:#FFF; display:flex; justify-content:space-between; align-items:center; transition:border 0.2s;" onmouseover="this.style.borderColor='#A4ABB8'" onmouseout="this.style.borderColor='#DFE1E7'">
                                  <div>
                                      <div style="font-weight:700; color:#0D0D12; font-size:14px; margin-bottom:4px;">Kelompok {{ $i }}</div>
                                      <div style="font-size:12px; color:#666D80;">Shift {{ $defShift }} <span style="margin:0 4px;">-</span> <strong>{{ $jmlAnggota }}</strong> Anggota</div>
                                  </div>
                                  <button @click="openEditModal('{{ $i }}', '{{ $defShift }}')" style="background:#F0F1F4; border:none; padding:8px; border-radius:6px; cursor:pointer; color:#0B266E;" title="Edit Anggota Kelompok {{ $i }}">
                                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                  </button>
                              </div>
                          @endfor
                      </div>
                  @else
                      <div style="padding:48px 24px; text-align:center; color:#666D80; font-size:14px; border:1px dashed #DFE1E7; border-radius:12px; background:#FAFAFA;">
                          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18"/><path d="M15 3v18"/><path d="M3 9h18"/><path d="M3 15h18"/></svg>
                          Kelompok belum tersedia
                      </div>
                  @endif

            <div style="margin-top:24px;">
                <form action="{{ route('eoffice.manprak.koor.praktikan.reset-plot') }}" method="POST" onsubmit="return confirm('YAKIN INGIN MENGOSONGKAN SELURUH ANGGOTA DARI SEMUA KELOMPOK? Data mahasiswa tidak akan hilang, hanya pembagian kelompoknya saja yang direset menjadi kosong.');">
                            @csrf
                            <input type="hidden" name="praktikum_id" value="{{ $praktikum->id }}">
                            <button type="submit" style="width:100%; background:#FFF0F0; color:#D32F2F; border:1px solid #FFCDD2; padding:12px 0; border-radius:8px; font-weight:600; font-size:14px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:background 0.2s;" onmouseover="this.style.background='#FFE4E4'" onmouseout="this.style.background='#FFF0F0'">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                Hapus Kelompok & Shift
                            </button>
                        </form>
            </div>
        </div>
    </div>


    {{-- MODAL SUB: EDIT ANGGOTA KELOMPOK                      --}}
    {{-- â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
    <template x-teleport="body">
        <div x-show="showEditModal" style="display:none;" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-[#0D0D12]/40 backdrop-blur-sm transition-opacity" aria-hidden="true" @click="closeEditModal()"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative inline-block align-bottom bg-white rounded-[16px] text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl w-full flex flex-col max-h-[90vh]">
                
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-[#DFE1E7] flex-shrink-0 bg-white">
                    <div style="font-weight:700; font-size:16px; color:#0D0D12;" x-text="'Pilih Anggota Kelompok ' + activeKelompok"></div>
                    <div style="font-size:12px; color:#666D80; margin-top:2px;" x-text="'Shift ' + activeShift"></div>
                </div>

                {{-- Filter --}}
                <div style="padding:16px 24px; border-bottom:1px solid #DFE1E7;">
                    <label class="block text-[12px] font-semibold text-[#353849] mb-1">Cari Anggota</label>
                    <input type="text" x-model="searchQuery" placeholder="Ketik nama atau NIM..." class="mp-input" style="width:100%;">
                </div>
                
                {{-- Daftar Mahasiswa --}}
                <div style="flex:1; overflow-y:auto; padding:8px 24px;">
                    <div style="font-size:12px; color:#A4ABB8; margin:8px 0; font-weight:600;">ANGGOTA KELOMPOK (<span x-text="filteredPraktikans.length"></span>)</div>
                    
                    <template x-for="p in filteredPraktikans" :key="p.id">
                        <label class="group" style="display:flex; align-items:center; gap:12px; padding:10px 12px; border-radius:8px; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='#F0F1F4'" onmouseout="this.style.background='transparent'">
                            <div class="relative flex items-center justify-center cursor-pointer">
                                  <input type="checkbox" :value="p.id" x-model="selectedMembers" class="hidden">
                                  <div class="w-5 h-5 rounded-[6px] border-[1.5px] transition-all flex items-center justify-center group-hover:border-[#A4ABB8]"
                                       :class="selectedMembers.includes(p.id) || selectedMembers.includes(p.id.toString()) ? 'bg-[#0B266E] border-[#0B266E]' : 'bg-white border-[#DFE1E7]'">
                                      <svg class="w-3.5 h-3.5 text-white transition-opacity" 
                                           :class="selectedMembers.includes(p.id) || selectedMembers.includes(p.id.toString()) ? 'opacity-100' : 'opacity-0'" 
                                           viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                          <polyline points="20 6 9 17 4 12"></polyline>
                                      </svg>
                                  </div>
                              </div>
                            <div class="mp-av yellow" style="width:32px;height:32px;font-size:12px;" x-text="p.inisial"></div>
                            <div style="flex:1;">
                                <div style="font-weight:600; color:#0D0D12; font-size:13px;" x-text="p.nama"></div>
                                <div style="font-size:12px; color:#666D80; " x-text="p.nim"></div>
                            </div>
                            <div x-show="p.kel === activeKelompok" style="font-size:11px; background:#ECFDF5; color:#059669; padding:2px 8px; border-radius:4px; font-weight:600;">Anggota</div>
                            <div x-show="p.kel === null || p.kel === ''" style="font-size:11px; background:#FEF2F2; color:#DC2626; padding:2px 8px; border-radius:4px; font-weight:600;">Kosong</div>
                        </label>
                    </template>
                    <div x-show="filteredPraktikans.length === 0" style="padding:32px; text-align:center; color:#666D80; font-size:13px;">
                          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round" style="margin:0 auto 12px;display:block;"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                          Kelompok <span x-text="activeKelompok"></span> belum memiliki anggota.
                      </div>
                </div>

                {{-- Footer --}}
                <div style="padding:16px 24px; border-top:1px solid #DFE1E7; display:flex; justify-content:space-between; align-items:center; background:#F9FAFB; border-radius:0 0 12px 12px;">
                    <div style="font-size:13px; font-weight:600; color:#0D0D12;">
                        Terpilih: <span style="color:#0B266E;" x-text="selectedMembers.length"></span> orang
                    </div>
                    <div style="display:flex; gap:8px;">
                        
                        <button @click="saveMembers()" :disabled="isSaving" type="button" class="mp-btn primary sm" style="min-width: 90px; justify-content: center; background:#0B266E; border-color:#0B266E;">
                            <span x-show="!isSaving">Simpan</span>
                            <span x-show="isSaving">Menyimpan...</span>
                        </button>
                    </div>
            </div>
        </div>
    </template>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('manajemenKelompok', () => ({
            showMainModal: false,
            showEditModal: false,
            activeKelompok: '',
            activeShift: '',
            searchQuery: '',
            isSaving: false,
            selectedMembers: [],
            allPraktikans: @json($praktikansSemuaJSON),
            
            get unassignedCount() {
                return this.allPraktikans.filter(p => p.kel === null || p.kel === '').length;
            },

            get filteredPraktikans() {
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
            },

            openMainModal() {
                this.showMainModal = true;
            },
            closeMainModal() {
                this.showMainModal = false;
            },
            
            openEditModal(kel, shift) {
                this.activeKelompok = kel.toString();
                this.activeShift = shift.toString();
                this.searchQuery = '';
                
                // Pre-fill checkboxes based on current members of this group
                this.selectedMembers = this.allPraktikans
                    .filter(p => p.kel === this.activeKelompok)
                    .map(p => p.id);
                    
                this.showEditModal = true;
            },
            closeEditModal() {
                this.showEditModal = false;
            },
            
            saveMembers() {
                this.isSaving = true;
                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('praktikum_id', '{{ $praktikum->id }}');
                formData.append('kelompok', this.activeKelompok);
                formData.append('shift', this.activeShift);
                
                this.selectedMembers.forEach(id => {
                    formData.append('members[]', id);
                });
                
                fetch('{{ route('eoffice.manprak.koor.praktikan.save-group-members') }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.json())
                .then(res => {
                    if(res.success) {
                        window.location.reload();
                    } else {
                        alert('Gagal menyimpan. Pastikan data valid.');
                        this.isSaving = false;
                    }
                })
                .catch(() => {
                    alert('Terjadi kesalahan jaringan.');
                    this.isSaving = false;
                });
            }
        }))
    })
</script>

@endif

</x-eoffice::manajemen-praktikum.layout>

