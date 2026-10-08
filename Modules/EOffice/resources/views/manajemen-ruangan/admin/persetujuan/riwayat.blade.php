<x-eoffice::manajemen-ruangan.layout pageTitle="Arsip & Rekap">

    <div class="mp-page-header flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="mp-page-title">Arsip & Rekap</h1>
            <p class="mp-page-sub">Gudang arsip seluruh data histori pengajuan ruangan yang telah berstatus Selesai, Ditolak, atau Dibatalkan.</p>
        </div>

        {{-- Quick Export Dropdown --}}
        <div class="mp-page-actions flex flex-wrap items-center gap-2 w-full md:w-auto">
            <div class="relative z-[60]" x-data="{ 
                openExport: false,
                dropdownStyles: '',
                checkScroll() {
                    if(this.openExport) this.openExport = false;
                }
            }">
                <button type="button" 
                    @click="
                        const rect = $el.getBoundingClientRect();
                        const viewportHeight = window.innerHeight;
                        let style = `position: fixed; `;
                        let origin = '';
                        
                        if (rect.left < 200) {
                            style += `left: ${rect.left}px; `;
                            origin += 'left ';
                        } else {
                            style += `right: ${window.innerWidth - rect.right}px; `;
                            origin += 'right ';
                        }

                        if (rect.bottom + 250 > viewportHeight) {
                            style += `bottom: ${viewportHeight - rect.top + 8}px; `;
                            origin = 'bottom ' + origin.trim();
                        } else {
                            style += `top: ${rect.bottom + 8}px; `;
                            origin = 'top ' + origin.trim();
                        }
                        
                        style += `transform-origin: ${origin};`;
                        dropdownStyles = style;
                        openExport = !openExport;
                    "
                    class="mp-btn primary md cursor-pointer">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Ekspor Laporan
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="openExport" @click.outside="openExport = false" style="display: none;"
                    class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden z-[60] w-56"
                    :style="dropdownStyles"
                    @scroll.window.capture="checkScroll()">
                    <div class="py-1 px-1">
                        <div class="px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                            PILIH FORMAT</div>
                            
                        <a href="{{ route('eoffice.peminjaman.admin.riwayat.export-pdf', request()->query()) }}" target="_blank" class="w-full text-left px-3 py-2 text-[13px] rounded-md transition-colors flex items-center gap-2 text-gray-700 hover:bg-gray-50 cursor-pointer">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Cetak PDF
                        </a>
                        <a href="{{ route('eoffice.peminjaman.admin.riwayat.export-excel', request()->query()) }}" target="_blank" class="w-full text-left px-3 py-2 text-[13px] rounded-md transition-colors flex items-center gap-2 text-gray-700 hover:bg-gray-50 cursor-pointer">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Unduh Excel
                        </a>
                        
                        <div class="border-t border-gray-100 my-1"></div>
                        
                        <div class="px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                            EKSPOR CEPAT</div>
                            
                        @php
                            $startBulanIni = now()->startOfMonth()->format('Y-m-d');
                            $endBulanIni = now()->endOfMonth()->format('Y-m-d');
                        @endphp
                        <a href="{{ route('eoffice.peminjaman.admin.riwayat.export-excel', ['start_date' => $startBulanIni, 'end_date' => $endBulanIni]) }}" target="_blank" class="w-full text-left px-3 py-2 text-[13px] rounded-md transition-colors flex items-center gap-2 text-gray-700 hover:bg-gray-50 cursor-pointer" title="Unduh data bulan berjalan">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Laporan Bulan Berjalan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="mp-card" style="margin-top: 0px;"
        x-data="arsipManager()">
        
        <!-- Header Actions -->
        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between px-5 py-4 border-b border-gray-100 gap-4 relative z-10 w-full">
            <h2 class="text-base font-bold text-gray-900 tracking-tight">Arsip Peminjaman</h2>

            <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
                <form action="{{ route('eoffice.peminjaman.admin.riwayat.index') }}" method="GET"
                    class="flex flex-col sm:flex-row flex-wrap sm:items-center gap-2.5 w-full sm:w-auto">
                    {{-- Search --}}
                    <div class="relative w-full sm:w-auto" x-data="{ searchQuery: '{{ addslashes(request('search')) }}' }">
                        <button type="submit" class="absolute inset-y-0 left-0 pl-3 flex items-center cursor-pointer text-gray-400 hover:text-[#0B266E] transition-colors bg-transparent border-0 outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path>
                            </svg>
                        </button>
                        <input type="text" name="search" x-model="searchQuery" 
                            @input="if(searchQuery.trim() === '') $el.form.submit()"
                            @scroll.window.capture="$el.blur()"
                            @touchmove.window.capture="$el.blur()"
                            placeholder="Search"
                            class="w-full sm:w-56 h-[38px] pl-9 pr-8 text-[13px] bg-white border border-gray-200 rounded-lg focus:border-[#0B266E] focus:ring-2 focus:ring-blue-100 outline-none transition-all placeholder-gray-400">
                        
                        <!-- Clear Search (X) -->
                        <button type="button" x-show="searchQuery.length > 0" x-cloak
                            @click="searchQuery = ''; $nextTick(() => $el.closest('form').submit())"
                            class="absolute inset-y-0 right-0 pr-2.5 flex items-center cursor-pointer text-gray-400 hover:text-gray-700 transition-colors bg-transparent border-0 outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="relative w-full sm:w-auto" x-data="{ open: false }">
                        <button type="button" @click="open = !open" 
                            class="flex items-center justify-center w-full sm:w-auto relative gap-2 px-3 h-[38px] bg-white border rounded-lg text-[13px] font-semibold transition-colors cursor-pointer"
                            :class="open ? 'border-[#0B266E] text-[#0B266E]' : 'border-gray-300 text-slate-700 hover:bg-gray-50'">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                            </svg>
                            Filter
                            @if(request()->hasAny(['status', 'ruangan_id', 'start_date', 'end_date']) && array_filter(request()->only(['status', 'ruangan_id', 'start_date', 'end_date'])))
                                <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-[#0B266E] border-2 border-white"></span>
                                </span>
                            @endif
                        </button>

                        <div x-show="open" @click.outside="open = false" style="display: none;"
                            x-transition:enter="transition ease-out duration-100" 
                            x-transition:enter-start="opacity-0 scale-95" 
                            x-transition:enter-end="opacity-100 scale-100" 
                            x-transition:leave="transition ease-in duration-75" 
                            x-transition:leave-start="opacity-100 scale-100" 
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 sm:right-0 sm:left-auto top-full mt-2 origin-top sm:origin-top-right bg-white rounded-xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] border border-gray-100 z-50 w-full sm:w-80 max-w-sm">
                            <div class="p-4 space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 relative z-10 w-full bg-white">Rentang Waktu</label>
                                    <div class="flex flex-col sm:flex-row gap-2">
                                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                                            class="w-full sm:w-1/2 text-xs border border-gray-200 rounded block p-2 outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] cursor-text">
                                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                                            class="w-full sm:w-1/2 text-xs border border-gray-200 rounded block p-2 outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] cursor-text">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 relative z-10 w-full bg-white">Ruangan</label>
                                    <div x-data="{ 
                                            open: false, 
                                            selectedId: '{{ request('ruangan_id') }}', 
                                            selectedName: '{{ request('ruangan_id') ? addslashes($ruangans->firstWhere('id', request('ruangan_id'))->nama ?? 'Semua Ruangan') : 'Semua Ruangan' }}',
                                            selectItem(id, name) { 
                                                this.selectedId = id; 
                                                this.selectedName = name; 
                                                this.open = false; 
                                            } 
                                        }" class="relative w-full" @click.away="open = false">
                                        
                                        <input type="hidden" name="ruangan_id" :value="selectedId">

                                        <button type="button" @click="open = !open" 
                                            class="w-full flex items-center justify-between text-[13px] border border-gray-200 rounded p-2 bg-white hover:border-[#0B266E] focus:outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] transition-colors cursor-pointer">
                                            <span x-text="selectedName" class="truncate text-gray-700"></span>
                                            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        
                                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
                                            class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-100 rounded-xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] z-[60] max-h-48 overflow-y-auto" style="display: none;">
                                            <div class="py-2 px-1">
                                                <button type="button" @click="selectItem('', 'Semua Ruangan')" class="w-[calc(100%-8px)] text-left px-3 py-2 mx-1 mb-0.5 transition-colors cursor-pointer text-[13px] rounded-lg" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedId == '', 'text-gray-700 hover:bg-gray-50 font-medium': selectedId != ''}">Semua Ruangan</button>
                                                
                                                @foreach($ruangans as $ruangan)
                                                    <button type="button" @click="selectItem('{{ $ruangan->id }}', '{{ addslashes($ruangan->nama) }}')" class="w-[calc(100%-8px)] text-left px-3 py-2 mx-1 mb-0.5 transition-colors cursor-pointer text-[13px] rounded-lg" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedId == '{{ $ruangan->id }}', 'text-gray-700 hover:bg-gray-50 font-medium': selectedId != '{{ $ruangan->id }}'}">
                                                        {{ $ruangan->nama }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 relative z-10 w-full bg-white">Status</label>
                                    <div x-data="{ 
                                            open: false, 
                                            selectedVal: '{{ request('status') }}', 
                                            get selectedName() {
                                                const map = {
                                                    'disetujui': 'Disetujui',
                                                    'ditolak': 'Ditolak',
                                                    'dibatalkan': 'Dibatalkan',
                                                    'selesai': 'Selesai'
                                                };
                                                return map[this.selectedVal] || 'Semua Status';
                                            },
                                            selectItem(val) { 
                                                this.selectedVal = val; 
                                                this.open = false; 
                                            } 
                                        }" class="relative w-full" @click.away="open = false">
                                        
                                        <input type="hidden" name="status" :value="selectedVal">

                                        <button type="button" @click="open = !open" 
                                            class="w-full flex items-center justify-between text-[13px] border border-gray-200 rounded p-2 bg-white hover:border-[#0B266E] focus:outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] transition-colors cursor-pointer">
                                            <span x-text="selectedName" class="truncate text-gray-700"></span>
                                            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        
                                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
                                            class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-100 rounded-xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] z-[60] overflow-hidden" style="display: none;">
                                            <div class="py-2 px-1">
                                                <button type="button" @click="selectItem('')" class="w-[calc(100%-8px)] text-left px-3 py-2 mx-1 mb-0.5 transition-colors cursor-pointer text-[13px] rounded-lg" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == '', 'text-gray-700 hover:bg-gray-50 font-medium': selectedVal != ''}">Semua Status</button>
                                                <button type="button" @click="selectItem('disetujui')" class="w-[calc(100%-8px)] text-left px-3 py-2 mx-1 mb-0.5 transition-colors cursor-pointer text-[13px] rounded-lg" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'disetujui', 'text-gray-700 hover:bg-gray-50 font-medium': selectedVal != 'disetujui'}">Disetujui</button>
                                                <button type="button" @click="selectItem('ditolak')" class="w-[calc(100%-8px)] text-left px-3 py-2 mx-1 mb-0.5 transition-colors cursor-pointer text-[13px] rounded-lg" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'ditolak', 'text-gray-700 hover:bg-gray-50 font-medium': selectedVal != 'ditolak'}">Ditolak</button>
                                                <button type="button" @click="selectItem('dibatalkan')" class="w-[calc(100%-8px)] text-left px-3 py-2 mx-1 mb-0.5 transition-colors cursor-pointer text-[13px] rounded-lg" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'dibatalkan', 'text-gray-700 hover:bg-gray-50 font-medium': selectedVal != 'dibatalkan'}">Dibatalkan</button>
                                                <button type="button" @click="selectItem('selesai')" class="w-[calc(100%-8px)] text-left px-3 py-2 mx-1 mb-0.5 transition-colors cursor-pointer text-[13px] rounded-lg" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'selesai', 'text-gray-700 hover:bg-gray-50 font-medium': selectedVal != 'selesai'}">Selesai</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pt-2 flex justify-end gap-2 border-t border-gray-100">
                                    <a href="{{ route('eoffice.peminjaman.admin.riwayat.index') }}"
                                        class="px-3 py-1.5 text-[13px] font-medium text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer">Reset</a>
                                    <button type="submit"
                                        class="px-3 py-1.5 text-[13px] font-medium text-white bg-[#0B266E] rounded-lg hover:bg-[#091F5E] transition-colors cursor-pointer">Terapkan
                                        Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>



            </div>
        </div>

        <div class="mp-card-body">
            <div class="mp-table-wrap" @scroll.passive="$dispatch('close-action-dropdowns')">
                <table class="mp-table">
                    <thead>
                        <tr style="border-bottom:1px solid #E2E8F0; background:#FAFAFA;">
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Peminjam</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Ruangan</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Waktu Pemakaian</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Kegiatan</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Status</th>
                            <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap; width: 80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($peminjamans as $pinjam)
                        @php
                            $fullName = $pinjam->user->name ?? 'User Tidak Diketahui';
                            $isDosen = $pinjam->user && $pinjam->user->hasRole('dosen');
                            $identityNumber = $pinjam->user ? ($pinjam->user->student->student_number ?? $pinjam->user->lecturer->employee_number ?? $pinjam->user->external_id) : '-';
                            $roleName = $isDosen ? 'Dosen' : 'Mahasiswa';
                            
                            $displayName = $fullName;
                            if (!$isDosen) {
                                $nameParts = explode(' ', $fullName);
                                if (count($nameParts) > 2) {
                                    $displayName = $nameParts[0] . ' ' . $nameParts[1];
                                    for ($i = 2; $i < count($nameParts); $i++) {
                                        $displayName .= ' ' . strtoupper(substr($nameParts[$i], 0, 1)) . '.';
                                    }
                                }
                            }
                        @endphp
                        <tr class="mp-tr">
                            <td>
                                @if($isDosen)
                                    <div class="text-[13px] font-semibold text-[#111827] truncate max-w-[160px]" title="{{ $fullName }}">
                                        {{ $fullName }}
                                    </div>
                                @else
                                    <div class="text-[13px] font-semibold text-[#111827]" title="{{ $fullName }}">
                                        {{ $displayName }}
                                    </div>
                                @endif
                                <div class="text-[11px] text-gray-500 mt-0.5">
                                    {{ $identityNumber }} • {{ $pinjam->nomor_telepon ?: '-' }}
                                </div>
                            </td>
                            <td class="max-w-[140px]">
                                <div class="text-[13px] font-medium text-[#111827] truncate" title="{{ $pinjam->ruangan->nama ?? 'Dihapus' }}">
                                    {{ $pinjam->ruangan->nama ?? 'Dihapus' }}
                                </div>
                            </td>
                            <td>
                                <div class="text-[13px] font-medium text-[#111827]">
                                    {{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('d M Y') }}
                                </div>
                                <div class="text-[11px] text-gray-500 mt-0.5">
                                    {{ \Carbon\Carbon::parse($pinjam->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($pinjam->jam_selesai)->format('H:i') }} WIB
                                </div>
                            </td>
                            <td class="max-w-[140px]">
                                <div class="text-[12px] text-gray-600 truncate" title="{{ $pinjam->tujuan }}">
                                    {{ $pinjam->tujuan }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $st = ['bg' => '#F3F4F6', 'color' => '#374151', 'border' => '#E5E7EB'];
                                    if (strtolower($pinjam->status) === 'disetujui' || strtolower($pinjam->status) === 'selesai')
                                        $st = ['bg' => '#ECFDF5', 'color' => '#047857', 'border' => '#A7F3D0'];
                                    elseif (strtolower($pinjam->status) === 'ditolak' || strtolower($pinjam->status) === 'dibatalkan')
                                        $st = ['bg' => '#FFF1F2', 'color' => '#9D174D', 'border' => '#FECDD3'];
                                    elseif (strtolower($pinjam->status) === 'menunggu')
                                        $st = ['bg' => '#FFF9E6', 'color' => '#B45309', 'border' => '#FFEBB3'];
                                @endphp
                                <span style="font-size:11px; font-weight:700; color:{{ $st['color'] }}; background:{{ $st['bg'] }}; border:1px solid {{ $st['border'] }}; padding:3px 10px; border-radius:9999px; white-space:nowrap; text-transform:uppercase; display:inline-block;">
                                    {{ $pinjam->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="relative inline-flex flex-col items-center justify-center w-full"
                                    x-data="{ 
                                        showDropdown: false,
                                        dropdownStyles: '',
                                        checkScroll() {
                                            if(this.showDropdown) this.showDropdown = false;
                                        }
                                    }"
                                    :class="{'z-50': showDropdown, 'z-[1]': !showDropdown}">
                                    
                                    <button type="button" 
                                        @click="
                                            const rect = $el.getBoundingClientRect();
                                            const viewportHeight = window.innerHeight;
                                            let style = `position: fixed; right: ${window.innerWidth - rect.right}px; `;
                                            if (rect.bottom + 150 > viewportHeight) {
                                                style += `bottom: ${viewportHeight - rect.top + 8}px; transform-origin: bottom right;`;
                                            } else {
                                                style += `top: ${rect.bottom + 8}px; transform-origin: top right;`;
                                            }
                                            dropdownStyles = style;
                                            showDropdown = !showDropdown;
                                        " 
                                        @click.away="showDropdown = false"
                                        class="inline-flex items-center justify-center w-[32px] h-[32px] rounded-lg border border-[#E2E8F0] bg-white text-[#64748B] hover:bg-[#F8FAFC] transition-colors cursor-pointer">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><circle cx="5" cy="12" r="1.5"></circle><circle cx="12" cy="12" r="1.5"></circle><circle cx="19" cy="12" r="1.5"></circle></svg>
                                    </button>

                                    <div x-show="showDropdown" style="display:none;"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="bg-white rounded-xl shadow-[0_4px_16px_rgba(0,0,0,0.08)] border border-gray-100 p-1.5"
                                        :style="dropdownStyles"
                                        @scroll.window.capture="checkScroll()"
                                        @close-action-dropdowns.window="checkScroll()">

                                        <button type="button" @click="showDropdown = false; openDetail({{ json_encode([
                                            'nama' => $fullName,
                                            'nim_nip' => $identityNumber,
                                            'telepon' => $pinjam->nomor_telepon ?: '-',
                                            'role' => $roleName,
                                            'ruangan' => $pinjam->ruangan->nama ?? '-',
                                            'tanggal' => \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('l, d F Y'),
                                            'jam' => \Carbon\Carbon::parse($pinjam->jam_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($pinjam->jam_selesai)->format('H:i') . ' WIB',
                                            'durasi' => \Carbon\Carbon::parse($pinjam->jam_mulai)->diffInHours(\Carbon\Carbon::parse($pinjam->jam_selesai)) . ' Jam',
                                            'kegiatan' => $pinjam->tujuan,
                                            'berkas' => $pinjam->berkas_pendukung ? app(\App\Services\SupabaseStorage::class)->getPublicUrl($pinjam->berkas_pendukung) : null,
                                            'waktu_pengajuan' => \Carbon\Carbon::parse($pinjam->created_at)->translatedFormat('d M Y, H:i'),
                                            'waktu_diproses' => $pinjam->waktu_approval ? \Carbon\Carbon::parse($pinjam->waktu_approval)->translatedFormat('d M Y, H:i') : '-',
                                            'diproses_oleh' => '-',
                                            'alasan_penolakan' => $pinjam->alasan_penolakan,
                                            'status' => $pinjam->status
                                        ]) }})"
                                            class="w-full text-left px-2.5 py-1.5 text-[12px] text-gray-700 hover:bg-gray-100 font-semibold rounded-md focus:outline-none flex items-center gap-2 transition-colors cursor-pointer">
                                            <svg class="w-[14px] h-[14px] text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Detail Arsip
                                        </button>

                                        @if(auth()->user()->hasRole('superadmin'))
                                            <button type="button" @click="showDropdown = false; openDelete('{{ route('eoffice.peminjaman.admin.riwayat.destroy', $pinjam->id) }}')" 
                                                class="w-full text-left px-2.5 py-1.5 mt-0.5 text-[12px] text-red-600 hover:bg-red-50 font-semibold rounded-md focus:outline-none flex items-center gap-2 transition-colors cursor-pointer">
                                                <svg class="w-[14px] h-[14px] text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Hapus Arsip
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="mp-tr">
                            <td colspan="6" style="padding:40px; text-align:center;">
                                <div style="font-size:13px; font-weight:500; color:#666D80;">Belum ada arsip peminjaman yang tersedia.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-200 bg-slate-50/50 px-5 py-3 flex flex-col md:flex-row items-center justify-between gap-4 mt-2 rounded-b-[12px]">
            <div class="flex flex-col md:flex-row items-center gap-4 text-[13px] text-slate-500 w-full md:w-auto">
                <div class="flex items-center justify-center gap-2 w-full md:w-auto">
                    <span class="font-medium">Per halaman</span>
                    <div x-data="{ 
                        open: false, 
                        selectedVal: '{{ request('per_page', 10) }}',
                        selectItem(val, url) {
                            this.selectedVal = val;
                            this.open = false;
                            window.location.href = url;
                        }
                    }" class="relative" @click.away="open = false">
                        <button type="button" @click="open = !open"
                            class="flex items-center justify-between px-3 py-1.5 text-slate-900 font-bold bg-white outline-none cursor-pointer hover:bg-slate-50 border border-slate-200 rounded-lg shadow-sm gap-2 min-w-[64px] transition-colors">
                            <span x-text="selectedVal"></span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200"
                                :class="{'rotate-180': open}" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 bottom-full mb-1 w-full bg-white border border-slate-200 rounded-md shadow-lg z-50 overflow-hidden"
                            style="display: none;">
                            <div class="p-1">
                                <button type="button"
                                    @click="selectItem(10, '{{ request()->fullUrlWithQuery(['per_page' => 10, 'page' => 1]) }}')"
                                    class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors cursor-pointer"
                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 10, 'text-slate-700 hover:bg-slate-50': selectedVal != 10}">10</button>
                                <button type="button"
                                    @click="selectItem(25, '{{ request()->fullUrlWithQuery(['per_page' => 25, 'page' => 1]) }}')"
                                    class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors cursor-pointer"
                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 25, 'text-slate-700 hover:bg-slate-50': selectedVal != 25}">25</button>
                                <button type="button"
                                    @click="selectItem(50, '{{ request()->fullUrlWithQuery(['per_page' => 50, 'page' => 1]) }}')"
                                    class="w-full text-left px-3 py-1.5 text-[13px] font-medium rounded-md transition-colors cursor-pointer"
                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 50, 'text-slate-700 hover:bg-slate-50': selectedVal != 50}">50</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hidden md:block w-px h-4 bg-slate-200"></div>

                <p class="hidden md:block font-medium text-slate-500">
                    Menampilkan <span class="font-bold text-slate-800">{{ $peminjamans->firstItem() ?? 0 }}</span>
                    sampai <span class="font-bold text-slate-800">{{ $peminjamans->lastItem() ?? 0 }}</span>
                    dari <span class="font-bold text-slate-800">{{ $peminjamans->total() }}</span> entri
                </p>
            </div>

            <div class="flex items-center justify-center gap-1.5 w-full md:w-auto">
                @if ($peminjamans->onFirstPage())
                    <button disabled
                        class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                @else
                    <a href="{{ $peminjamans->previousPageUrl() }}"
                        class="text-slate-500 hover:text-slate-700 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                @endif

                @php
                    $startPage = max(1, $peminjamans->currentPage() - 1);
                    $endPage = min($peminjamans->lastPage(), $peminjamans->currentPage() + 1);

                    // Adjust start and end to always show at least 3 pages if possible
                    if ($endPage - $startPage < 2) {
                        if ($startPage == 1) {
                            $endPage = min($peminjamans->lastPage(), 3);
                        } elseif ($endPage == $peminjamans->lastPage()) {
                            $startPage = max(1, $peminjamans->lastPage() - 2);
                        }
                    }
                @endphp

                @if($startPage > 1)
                    <a href="{{ $peminjamans->url(1) }}"
                        class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">1</a>
                    @if($startPage > 2)
                        <span
                            class="text-slate-400 font-medium text-[13px] w-6 flex items-center justify-center">...</span>
                    @endif
                @endif

                @foreach ($peminjamans->getUrlRange($startPage, $endPage) as $page => $url)
                    @if ($page == $peminjamans->currentPage())
                        <span
                            class="bg-[#0f1b40] shadow-md shadow-[#0f1b40]/20 text-white font-bold text-[13px] w-8 h-8 flex items-center justify-center rounded-lg transition-colors">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                            class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">{{ $page }}</a>
                    @endif
                @endforeach

                @if($endPage < $peminjamans->lastPage())
                    @if($endPage < $peminjamans->lastPage() - 1)
                        <span
                            class="text-slate-400 font-medium text-[13px] w-6 flex items-center justify-center">...</span>
                    @endif
                    <a href="{{ $peminjamans->url($peminjamans->lastPage()) }}"
                        class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-[13px] w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">{{ $peminjamans->lastPage() }}</a>
                @endif

                @if ($peminjamans->hasMorePages())
                    <a href="{{ $peminjamans->nextPageUrl() }}"
                        class="text-slate-500 hover:text-slate-700 hover:bg-slate-50 w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @else
                    <button disabled
                        class="text-slate-300 cursor-not-allowed w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>
        </div>

        <!-- Pop-up Modal Detail Peminjaman -->
        <div x-show="isDetailOpen" x-cloak style="display: none;"
            class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6" 
            aria-labelledby="modal-title" role="dialog" aria-modal="true">

            <!-- 1. BACKDROP OVERLAY (Latar Belakang Gelap) -->
            <div x-show="isDetailOpen" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" 
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 transition-opacity" 
                aria-hidden="true" @click="closeDetail()"></div>

            <!-- 2. MODAL PANEL (Kotak Utama Modal) -->
            <div x-show="isDetailOpen" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                class="relative bg-white rounded-t-[24px] sm:rounded-t-[20px] rounded-b-none sm:rounded-b-[20px] border border-gray-100 shadow-2xl w-full max-w-2xl max-h-[95vh] sm:max-h-[90vh] flex flex-col overflow-hidden">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-[#0B266E]/10 flex-shrink-0 flex justify-between items-center bg-[#0B266E]/[0.06] z-10">
                    <div class="flex items-center gap-3">
                        <div class="bg-[#0B266E] p-2.5 rounded-xl">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-[#1A1C1E]">Detail Arsip Peminjaman</h3>
                            <p class="text-xs text-[#0B266E] mt-0.5">Arsip peminjaman dari <span class="font-bold" x-text="detailData.nama"></span></p>
                        </div>
                    </div>
                    <button type="button" @click="closeDetail()" class="text-[#0B266E] hover:text-[#091F5E] hover:bg-[#0B266E]/10 transition-colors w-8 h-8 flex items-center justify-center shrink-0 rounded-lg cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body (Scrollable) -->
                <div class="p-6 flex-1 overflow-y-auto bg-slate-50/50 custom-scrollbar">
                    
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mx-1">
                        <!-- HEADER -->
                        <div class="px-5 py-4 border-b border-slate-100 bg-white">
                            <h4 class="text-[13px] font-bold text-slate-800 flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#0B266E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Informasi Lengkap Peminjaman
                            </h4>
                        </div>
                        
                        <!-- BODY -->
                        <div class="p-5 grid grid-cols-[100px_1fr] sm:grid-cols-[140px_1fr] gap-x-4 gap-y-4 text-[13px]">
                            
                            <!-- Nama Pemohon -->
                            <div class="text-slate-500 font-medium">Nama Pemohon</div>
                            <div class="text-slate-900 font-bold" x-text="detailData.nama"></div>

                            <!-- NIM/NIP -->
                            <div class="text-slate-500 font-medium">NIM / NIP</div>
                            <div class="text-slate-900 font-medium" x-text="detailData.nim_nip"></div>

                            <!-- Role -->
                            <div class="text-slate-500 font-medium flex items-start mt-0.5">Role / Peran</div>
                            <div class="flex items-center">
                                <span class="bg-blue-50 text-[#0B266E] px-2 py-0.5 rounded text-[11px] font-semibold border border-blue-200" x-text="detailData.role"></span>
                            </div>

                            <!-- No. Telp -->
                            <div class="text-slate-500 font-medium">No. Telepon</div>
                            <div class="text-slate-900 font-medium flex items-center">
                                <template x-if="detailData.telepon && detailData.telepon !== '-'">
                                    <a :href="'https://wa.me/' + (detailData.telepon.toString().startsWith('0') ? '62' + detailData.telepon.toString().substring(1) : detailData.telepon)" 
                                        target="_blank" 
                                        class="inline-flex items-center gap-1.5 text-[#25D366] hover:text-[#1da851] hover:underline transition-colors"
                                        title="Hubungi via WhatsApp">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                                        </svg>
                                        <span x-text="detailData.telepon"></span>
                                    </a>
                                </template>
                                <template x-if="!detailData.telepon || detailData.telepon === '-'">
                                    <span class="text-slate-800" x-text="detailData.telepon || '-'"></span>
                                </template>
                            </div>

                            <div class="col-span-full my-2 border-t border-slate-100"></div>

                            <!-- Ruangan -->
                            <div class="text-slate-500 font-medium">Ruangan</div>
                            <div class="text-slate-900 font-bold" x-text="detailData.ruangan"></div>

                            <!-- Tanggal -->
                            <div class="text-slate-500 font-medium">Tanggal</div>
                            <div class="text-slate-900 font-bold" x-text="detailData.tanggal"></div>

                            <!-- Waktu -->
                            <div class="text-slate-500 font-medium flex items-start mt-0.5">Waktu</div>
                            <div class="text-slate-900 font-bold flex items-center">
                                <span class="bg-blue-50 text-[#0B266E] px-1.5 py-0.5 rounded text-[11px] font-semibold border border-blue-200" x-text="detailData.jam"></span>
                                <span class="text-slate-600 font-medium text-[12px] ml-2" x-text="detailData.durasi"></span>
                            </div>

                            <!-- Kegiatan -->
                            <div class="text-slate-500 font-medium">Kegiatan</div>
                            <div class="text-slate-900 font-medium leading-relaxed" x-text="detailData.kegiatan"></div>

                            <!-- Berkas -->
                            <div class="text-slate-500 font-medium flex items-start mt-0.5">Berkas</div>
                            <div class="text-slate-900 font-medium">
                                <template x-if="detailData.berkas">
                                    <a :href="detailData.berkas" target="_blank" class="text-[13px] font-bold text-[#0065ff] hover:text-[#0052cc] hover:underline transition-colors inline-block">
                                        Lihat Berkas Terlampir
                                    </a>
                                </template>
                                <template x-if="!detailData.berkas">
                                    <span class="text-gray-400 italic text-[12px]">Tidak dilampirkan</span>
                                </template>
                            </div>
                            
                            <div class="col-span-full my-2 border-t border-slate-100"></div>
                            
                            <!-- JEJAK AUDIT -->
                            <!-- Status -->
                            <div class="text-slate-500 font-medium flex items-start mt-1">Status Akhir</div>
                            <div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border"
                                    :class="{
                                        'bg-[#ECFDF5] text-[#047857] border-[#A7F3D0]': detailData.status === 'disetujui',
                                        'bg-[#FFF1F2] text-[#9D174D] border-[#FECDD3]': detailData.status === 'ditolak' || detailData.status === 'dibatalkan',
                                        'bg-[#FFF9E6] text-[#B45309] border-[#FFEBB3]': detailData.status === 'menunggu',
                                        'bg-[#F1E9FF] text-[#5E53F4] border-[#D1BFFF]': detailData.status === 'selesai'
                                    }"
                                    x-text="detailData.status">
                                </span>
                            </div>

                            <template x-if="detailData.status === 'ditolak' || detailData.status === 'dibatalkan'">
                                <div class="col-span-full grid grid-cols-[100px_1fr] sm:grid-cols-[140px_1fr] gap-x-4 gap-y-1 mt-1">
                                    <div class="text-slate-500 font-medium">Alasan Penolakan</div>
                                    <div class="text-red-600 font-semibold leading-relaxed" x-text="detailData.alasan_penolakan || 'Tidak ada keterangan tambahan.'"></div>
                                </div>
                            </template>
                            
                            <!-- Waktu Pengajuan -->
                            <div class="text-slate-500 font-medium">Waktu Diajukan</div>
                            <div class="text-slate-900 font-medium" x-text="detailData.waktu_pengajuan"></div>
                            
                            <!-- Waktu Diproses -->
                            <div class="text-slate-500 font-medium">Waktu Diproses</div>
                            <div class="text-slate-900 font-medium" x-text="detailData.waktu_diproses"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pop-up Modal Konfirmasi Hapus -->
        <div x-show="isDeleteModalOpen" x-cloak style="display: none;"
            class="fixed inset-0 z-[110] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6" 
            aria-labelledby="modal-title" role="dialog" aria-modal="true">

            <!-- 1. BACKDROP OVERLAY (Latar Belakang Gelap) -->
            <div x-show="isDeleteModalOpen" 
                x-transition:enter="ease-out duration-300" 
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" 
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" 
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 transition-opacity" 
                aria-hidden="true" @click="closeDelete()"></div>

            <!-- 2. MODAL PANEL (Kotak Utama Modal) -->
            <div x-show="isDeleteModalOpen" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                class="relative bg-white rounded-t-[24px] sm:rounded-t-[20px] rounded-b-none sm:rounded-b-[20px] border-0 sm:border border-gray-100 shadow-2xl w-full max-w-md max-h-[95vh] sm:max-h-[90vh] flex flex-col overflow-hidden">
                
                <div class="p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Konfirmasi Hapus Permanen</h3>
                    <p class="text-sm text-gray-500 text-center mb-6">Apakah Anda yakin ingin menghapus data arsip ini beserta file yang terlampir secara permanen dari database? Aksi ini tidak dapat dibatalkan.</p>
                    
                    <form :action="deleteFormAction" method="POST" class="flex gap-3 w-full">
                        @csrf
                        @method('DELETE')
                        <button type="button" @click="closeDelete()" class="w-1/2 flex items-center justify-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="w-1/2 flex items-center justify-center px-4 py-2.5 bg-red-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-red-700 transition-colors cursor-pointer shadow-sm shadow-red-200">
                            Ya, Hapus Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function arsipManager() {
            return {
                isDetailOpen: false,
                detailData: {},
                isDeleteModalOpen: false,
                deleteFormAction: '',
                openDetail(data) {
                    this.detailData = data;
                    this.isDetailOpen = true;
                    document.body.style.overflow = 'hidden';
                },
                closeDetail() {
                    this.isDetailOpen = false;
                    setTimeout(() => {
                        this.detailData = {};
                        document.body.style.overflow = '';
                    }, 300);
                },
                openDelete(url) {
                    this.deleteFormAction = url;
                    this.isDeleteModalOpen = true;
                    document.body.style.overflow = 'hidden';
                },
                closeDelete() {
                    this.isDeleteModalOpen = false;
                    setTimeout(() => {
                        this.deleteFormAction = '';
                        document.body.style.overflow = '';
                    }, 300);
                }
            };
        }
    </script>
</x-eoffice::manajemen-ruangan.layout>