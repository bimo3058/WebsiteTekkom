<x-eoffice::manajemen-ruangan.layout pageTitle="Persetujuan">

    <div class="mp-page-header">
        <div>
            <h1 class="mp-page-title">Verifikasi Peminjaman</h1>
            <p class="mp-page-sub">Kelola dan verifikasi seluruh permohonan peminjaman ruangan yang diajukan oleh
                pengguna.</p>
        </div>
    </div>

    <div class="mp-card" style="margin-top: 0px;" x-data="persetujuanManager()">
        
        <!-- Header Actions -->
        <div
            class="flex flex-col lg:flex-row lg:items-center justify-between px-4 sm:px-5 py-3.5 border-b border-gray-100 gap-2.5 lg:gap-4 relative z-10 w-full">
            <h2 class="text-[15px] sm:text-base font-bold text-gray-900 tracking-tight shrink-0 m-0">Daftar Antrean</h2>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full lg:w-auto">
                <form action="{{ route('eoffice.peminjaman.admin.persetujuan.index') }}" method="GET"
                    class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 m-0 relative w-full lg:w-auto">
                    {{-- Search --}}
                    <div class="relative w-full sm:flex-1 lg:w-auto lg:flex-none" x-data="{ searchQuery: '{{ addslashes(request('search')) }}' }">
                        <button type="submit" class="absolute inset-y-0 left-0 pl-3 flex items-center cursor-pointer text-gray-400 hover:text-[#0B266E] transition-colors bg-transparent border-0 outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"></path>
                            </svg>
                        </button>
                        <input type="text" name="search" x-model="searchQuery" 
                            @input="if(searchQuery.trim() === '') $el.form.submit()"
                            @scroll.window.capture="$el.blur()"
                            @touchmove.window.capture="$el.blur()"
                            placeholder="Cari Nama/NIM/Ruangan..."
                            class="w-full lg:w-56 h-[38px] pl-9 pr-8 text-[13px] bg-white border border-gray-200 rounded-lg focus:border-[#0B266E] focus:ring-2 focus:ring-blue-100 outline-none transition-all placeholder-gray-400">
                        
                        <!-- Clear Search (X) -->
                        <button type="button" x-show="searchQuery.length > 0" x-cloak
                            @click="searchQuery = ''; $nextTick(() => $el.closest('form').submit())"
                            class="absolute inset-y-0 right-0 pr-2.5 flex items-center cursor-pointer text-gray-400 hover:text-gray-700 transition-colors bg-transparent border-0 outline-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Filter Popover (Ruangan & Asal Pengajuan) --}}
                    <div class="static sm:relative w-full sm:w-auto shrink-0" x-data="{ open: false }" @click.outside="open = false">
                        <button type="button" @click="open = !open" 
                            class="flex items-center justify-center w-full sm:w-auto relative gap-2 px-3 h-[38px] bg-white border rounded-lg text-[13px] font-semibold transition-colors cursor-pointer whitespace-nowrap"
                            :class="open ? 'border-[#0B266E] text-[#0B266E]' : 'border-gray-200 text-slate-700 hover:bg-gray-50'">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                            </svg>
                            Filter
                            @if(request()->hasAny(['ruangan_id', 'source']) && array_filter(request()->only(['ruangan_id', 'source'])))
                                <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-[#0B266E] border-2 border-white"></span>
                                </span>
                            @endif
                        </button>

                        <div x-show="open" style="display: none;"
                            x-transition:enter="transition ease-out duration-100" 
                            x-transition:enter-start="opacity-0 scale-95" 
                            x-transition:enter-end="opacity-100 scale-100" 
                            x-transition:leave="transition ease-in duration-75" 
                            x-transition:leave-start="opacity-100 scale-100" 
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 right-0 sm:right-0 sm:left-auto top-full mt-2 origin-top sm:origin-top-right bg-white rounded-xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] border border-gray-100 z-50 w-full sm:w-80 overflow-visible">
                            <div class="p-4 space-y-3.5">
                                {{-- 1. Ruangan --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 relative z-10 w-full bg-white">Ruangan</label>
                                    <div x-data="roomFilterSelect('{{ request('ruangan_id') }}', '{{ addslashes(request('ruangan_id') ? ($ruangans->firstWhere('id', request('ruangan_id'))->nama ?? 'Semua Ruangan') : 'Semua Ruangan') }}')" class="relative w-full" @click.away="handleClose()">
                                        
                                        <input type="hidden" name="ruangan_id" :value="selectedId">

                                        {{-- Mobile Trigger: Pure Button (Never triggers mobile keyboard) --}}
                                        <button type="button" 
                                            @click="open = !open" 
                                            class="sm:hidden w-full flex items-center justify-between text-[13px] border border-gray-200 rounded-lg py-2 pl-3 pr-2.5 bg-white hover:border-[#0B266E] focus:outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] transition-colors cursor-pointer text-left font-medium">
                                            <span class="truncate text-gray-700" x-text="selectedId ? selectedName : 'Semua Ruangan'"></span>
                                            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200 shrink-0 ml-1" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>

                                        {{-- Desktop Trigger: Searchable Combobox Input --}}
                                        <div class="hidden sm:flex relative w-full items-center">
                                            <input type="text"
                                                x-ref="roomInput"
                                                :value="displayValue"
                                                @input="handleInput($el.value)"
                                                @focus="handleFocus(); $el.select()"
                                                @click="open = true"
                                                @keydown.enter.prevent="if (open && filteredRooms.length > 0) { selectItem(filteredRooms[0].id, filteredRooms[0].name); } else { open = false; }"
                                                @keydown.escape="handleClose()"
                                                :placeholder="selectedId ? selectedName : 'Semua Ruangan'"
                                                class="w-full text-[13px] border border-gray-200 rounded-lg py-2 pl-3 pr-8 bg-white hover:border-[#0B266E] focus:outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] transition-colors text-gray-700 placeholder-gray-500 font-medium cursor-text"
                                                autocomplete="off">

                                            <button type="button" @click.stop="open = !open; if(open) { $nextTick(() => $refs.roomInput.focus()); }" 
                                                class="absolute right-2 p-1 text-gray-400 hover:text-gray-600 transition-colors cursor-pointer">
                                                <svg class="w-3.5 h-3.5 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
                                            class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-100 rounded-xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] z-[60] overflow-hidden" style="display: none;">
                                            
                                            {{-- Search input specifically on mobile so keyboard only opens if user wants to search --}}
                                            <div class="p-2 border-b border-gray-100 sm:hidden bg-slate-50">
                                                <div class="relative flex items-center">
                                                    <svg class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                    </svg>
                                                    <input type="text"
                                                        x-model="searchTerm"
                                                        @input="isTyping = true"
                                                        placeholder="Cari nama ruangan..."
                                                        class="w-full text-xs bg-white border border-gray-200 rounded-lg py-1.5 pl-8 pr-2 focus:border-[#0B266E] focus:outline-none">
                                                </div>
                                            </div>

                                            <div class="py-2 px-1 max-h-40 overflow-y-auto">
                                                <button type="button" @click="selectItem('', 'Semua Ruangan')" 
                                                    x-show="!searchTerm.trim() || 'semua ruangan'.includes(searchTerm.toLowerCase())"
                                                    class="w-[calc(100%-8px)] text-left px-3 py-2 mx-1 mb-0.5 transition-colors cursor-pointer text-[13px] rounded-lg" 
                                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedId == '', 'text-gray-700 hover:bg-gray-50 font-medium': selectedId != ''}">
                                                    Semua Ruangan
                                                </button>
                                                
                                                <template x-for="room in filteredRooms" :key="room.id">
                                                    <button type="button" @click="selectItem(room.id, room.name)" 
                                                        class="w-[calc(100%-8px)] text-left px-3 py-2 mx-1 mb-0.5 transition-colors cursor-pointer text-[13px] rounded-lg" 
                                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedId == room.id, 'text-gray-700 hover:bg-gray-50 font-medium': selectedId != room.id}"
                                                        x-text="room.name">
                                                    </button>
                                                </template>

                                                <div x-show="filteredRooms.length === 0 && (!'semua ruangan'.includes(searchTerm.toLowerCase()))" 
                                                    class="px-3 py-3 text-xs text-gray-400 text-center font-medium">
                                                    Ruangan tidak ditemukan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 2. Asal Pengajuan --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 relative z-10 w-full bg-white">Asal Pengajuan</label>
                                    <div x-data="{ 
                                            open: false, 
                                            selectedVal: '{{ request('source') }}', 
                                            get selectedName() {
                                                const map = {
                                                    'kp': 'Seminar KP',
                                                    'capstone': 'Sidang Capstone',
                                                    'praktikum': 'Praktikum',
                                                    'umum': 'Peminjaman Langsung'
                                                };
                                                return map[this.selectedVal] || 'Semua Asal';
                                            },
                                            selectItem(val) { 
                                                this.selectedVal = val; 
                                                this.open = false; 
                                            } 
                                        }" class="relative w-full" @click.away="open = false">
                                        
                                        <input type="hidden" name="source" :value="selectedVal">

                                        <button type="button" @click="open = !open" 
                                            class="w-full flex items-center justify-between text-[13px] border border-gray-200 rounded-lg p-2 bg-white hover:border-[#0B266E] focus:outline-none focus:border-[#0B266E] focus:ring-1 focus:ring-[#0B266E] transition-colors cursor-pointer">
                                            <span x-text="selectedName" class="truncate text-gray-700"></span>
                                            <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </button>
                                        
                                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
                                            class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-100 rounded-xl shadow-[0_10px_25px_-5px_rgba(0,0,0,0.1)] z-[60] max-h-36 overflow-y-auto" style="display: none;">
                                            <div class="py-2 px-1">
                                                <button type="button" @click="selectItem('')" class="w-[calc(100%-8px)] text-left px-3 py-2 mx-1 mb-0.5 transition-colors cursor-pointer text-[13px] rounded-lg" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == '', 'text-gray-700 hover:bg-gray-50 font-medium': selectedVal != ''}">Semua Asal</button>
                                                <button type="button" @click="selectItem('kp')" class="w-[calc(100%-8px)] text-left px-3 py-2 mx-1 mb-0.5 transition-colors cursor-pointer text-[13px] rounded-lg" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'kp', 'text-gray-700 hover:bg-gray-50 font-medium': selectedVal != 'kp'}">Seminar KP</button>
                                                <button type="button" @click="selectItem('capstone')" class="w-[calc(100%-8px)] text-left px-3 py-2 mx-1 mb-0.5 transition-colors cursor-pointer text-[13px] rounded-lg" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'capstone', 'text-gray-700 hover:bg-gray-50 font-medium': selectedVal != 'capstone'}">Sidang Capstone</button>
                                                <button type="button" @click="selectItem('praktikum')" class="w-[calc(100%-8px)] text-left px-3 py-2 mx-1 mb-0.5 transition-colors cursor-pointer text-[13px] rounded-lg" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'praktikum', 'text-gray-700 hover:bg-gray-50 font-medium': selectedVal != 'praktikum'}">Praktikum</button>
                                                <button type="button" @click="selectItem('umum')" class="w-[calc(100%-8px)] text-left px-3 py-2 mx-1 mb-0.5 transition-colors cursor-pointer text-[13px] rounded-lg" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 'umum', 'text-gray-700 hover:bg-gray-50 font-medium': selectedVal != 'umum'}">Peminjaman Langsung</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Footer: Reset & Terapkan Filter --}}
                                <div class="pt-2 flex items-center justify-end gap-2 border-t border-gray-100">
                                    <a href="{{ route('eoffice.peminjaman.admin.persetujuan.index') }}"
                                        class="px-3.5 py-1.5 text-[13px] font-medium text-gray-600 hover:text-gray-900 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer text-center">Reset</a>
                                    <button type="submit"
                                        class="px-4 py-1.5 text-[13px] font-medium text-white bg-[#0B266E] rounded-lg hover:bg-[#091F5E] transition-colors cursor-pointer whitespace-nowrap text-center">Terapkan Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="mp-card-body">

            <div class="mp-table-wrap">
                <table class="mp-table">
                    <thead>
                        <tr style="border-bottom:1px solid #E2E8F0; background:#FAFAFA;">
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Peminjam</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Ruangan</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Waktu Pemakaian</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Kegiatan</th>
                            <th style="padding:11px 16px; text-align:left; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap;">Status</th>
                            <th style="padding:11px 16px; text-align:center; font-size:11px; font-weight:600; color:#64748b; white-space:nowrap; width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody x-data="{
                        init() {
                            setInterval(() => {
                                fetch(window.location.href)
                                    .then(res => res.text())
                                    .then(html => {
                                        const doc = new DOMParser().parseFromString(html, 'text/html');
                                        const newTbody = doc.querySelector('tbody');
                                        if (newTbody && this.$el.innerHTML !== newTbody.innerHTML) {
                                            this.$el.innerHTML = newTbody.innerHTML;
                                        }
                                    })
                                    .catch(err => console.error('Polling error:', err));
                            }, 15000);
                        }
                    }">
                        @forelse($peminjamans as $pinjam)
                            @php
                                $berkasUrl = $pinjam->berkas_pendukung ? \Illuminate\Support\Facades\URL::signedRoute('eoffice.peminjaman.user.saya.berkas', ['id' => $pinjam->id]) : '';
                            @endphp
                            <tr class="mp-tr">
                                {{-- Kolom 1: Peminjam --}}
                                <td>
                                    <div class="text-[13px] font-medium text-[#111827] flex items-center gap-2">
                                        @php
                                            $fullName = $pinjam->user->name ?? 'User Tidak Diketahui';
                                            $isDosen = $pinjam->user && $pinjam->user->hasRole('dosen');
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
                                        @if($isDosen)
                                            <div class="truncate max-w-[160px]" title="{{ $fullName }}">{{ $fullName }}</div>
                                        @else
                                            <div title="{{ $fullName }}">{{ $displayName }}</div>
                                        @endif
                                        @if($pinjam->created_by && $pinjam->created_by !== $pinjam->user_id)
                                            <span
                                                class="bg-slate-100 text-slate-500 border border-slate-200 text-[10px] px-1.5 py-0.5 rounded font-bold uppercase tracking-wider flex-shrink-0"
                                                title="Didaftarkan oleh Sistem/Admin">By Admin</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-0.5">
                                        @php
                                            $identityNumber = null;
                                            if ($pinjam->user) {
                                                $identityNumber = $pinjam->user->student->student_number ?? $pinjam->user->lecturer->employee_number ?? $pinjam->user->external_id;
                                            }
                                        @endphp
                                        @if($identityNumber)
                                            <span class="mr-1">({{ $identityNumber }})</span>
                                        @endif
                                        @if($pinjam->nomor_telepon)
                                            @php
                                                $waNumber = preg_replace('/^0/', '62', $pinjam->nomor_telepon);
                                            @endphp
                                            <a href="https://wa.me/{{ $waNumber }}" target="_blank" title="Hubungi via WhatsApp" class="text-[#25D366] hover:text-[#1da851] hover:underline font-semibold transition-colors">
                                                {{ $pinjam->nomor_telepon }}
                                            </a>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Kolom 2: Ruangan (nama ruangan dan dibawahnya asal usul) --}}
                                <td class="max-w-[140px]">
                                    <div class="text-[13px] font-medium text-[#111827] truncate" title="{{ $pinjam->ruangan->nama ?? 'Dihapus' }}">
                                        {{ $pinjam->ruangan->nama ?? 'Dihapus' }}
                                    </div>
                                    <div class="mt-1">
                                        @if(in_array(strtolower((string)$pinjam->source), ['kp', 'kerja_praktik']))
                                            <span class="inline-flex items-center text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">Seminar KP</span>
                                        @elseif(strtolower((string)$pinjam->source) === 'capstone')
                                            <span class="inline-flex items-center text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-purple-50 text-purple-700 border border-purple-200">Sidang Capstone</span>
                                        @elseif(strtolower((string)$pinjam->source) === 'praktikum')
                                            <span class="inline-flex items-center text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">Praktikum</span>
                                        @else
                                            <span class="inline-flex items-center text-[10px] font-medium px-1.5 py-0.5 rounded-full bg-slate-50 text-slate-600 border border-slate-200">Langsung</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Kolom 3: Waktu Pemakaian (tanggal dan bawahnya waktunya) --}}
                                <td>
                                    <div class="text-[13px] font-medium text-[#111827]">
                                        {{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('d M Y') }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-0.5">
                                        {{ \Carbon\Carbon::parse($pinjam->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($pinjam->jam_selesai)->format('H:i') }} WIB
                                    </div>
                                </td>

                                {{-- Kolom 4: Kegiatan (nama kegiatan dan dibawahnya lihat berkas) --}}
                                <td class="max-w-[140px]">
                                    <div class="text-[12px] text-gray-600 truncate" title="{{ $pinjam->tujuan }}">
                                        {{ $pinjam->tujuan }}
                                    </div>
                                    @if($pinjam->berkas_pendukung)
                                        <div class="mt-0.5">
                                            <a href="{{ $berkasUrl }}"
                                                target="_blank"
                                                class="inline-flex items-center gap-1 text-[11px] text-[#0065ff] hover:text-[#0052cc] hover:underline transition-colors"
                                                style="font-size: 11px;">
                                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                    </path>
                                                </svg>
                                                <span>Lihat Berkas</span>
                                            </a>
                                        </div>
                                    @endif
                                </td>

                                {{-- Kolom 5: Status --}}
                                <td>
                                    @php
                                        $st = ['bg' => '#F3F4F6', 'color' => '#374151', 'border' => '#E5E7EB'];
                                        if ($pinjam->status === 'disetujui')
                                            $st = ['bg' => '#ECFDF5', 'color' => '#047857', 'border' => '#A7F3D0'];
                                        elseif ($pinjam->status === 'menunggu')
                                            $st = ['bg' => '#FFF9E6', 'color' => '#B45309', 'border' => '#FFEBB3'];
                                        elseif ($pinjam->status === 'ditolak' || $pinjam->status === 'dibatalkan')
                                            $st = ['bg' => '#FFF1F2', 'color' => '#9D174D', 'border' => '#FECDD3'];
                                    @endphp
                                    <span style="font-size:11px; font-weight:700; color:{{ $st['color'] }}; background:{{ $st['bg'] }}; border:1px solid {{ $st['border'] }}; padding:3px 12px; border-radius:9999px; white-space:nowrap; letter-spacing:0.02em; text-transform:uppercase; display:inline-block;">
                                        {{ $pinjam->status }}
                                    </span>
                                </td>

                                {{-- Kolom 6: Aksi --}}
                                <td style="text-align: center;">
                                    <button
                                        @click="openAction({{ $pinjam->id }}, '{{ addslashes($pinjam->user->name ?? "User") }}', '{{ addslashes($pinjam->user->student->student_number ?? $pinjam->user->external_id ?? "-") }}', '{{ addslashes($pinjam->nomor_telepon ?? $pinjam->user->whatsapp ?? "-") }}', '{{ $pinjam->status }}', '{{ $pinjam->ruangan_id }}', '{{ $pinjam->tanggal_pinjam }}', '{{ substr($pinjam->jam_mulai, 0, 5) }}', '{{ substr($pinjam->jam_selesai, 0, 5) }}', '{{ addslashes($pinjam->ruangan->nama ?? 'Ruangan Dihapus') }}', '{{ addslashes($pinjam->tujuan) }}', '{{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('d M Y') }}', '{{ $berkasUrl }}', '{{ addslashes($pinjam->source_label) }}')"
                                        class="h-8 px-4 rounded-md bg-white border border-gray-200 text-[#0B266E] text-[12px] font-bold hover:bg-gray-50 shadow-sm transition-colors cursor-pointer">
                                        Kelola
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="mp-tr">
                                <td colspan="6" style="padding:40px; text-align:center;">
                                    <div style="font-size:13px; font-weight:500; color:#666D80;">Belum ada antrean permohonan ruangan atau jadwal berjalan yang masuk.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div
                class="border-t border-slate-200 bg-white px-4 sm:px-5 py-3 flex flex-wrap items-center justify-between gap-3 rounded-b-[12px]">
                {{-- Left: Per page selector + Showing text --}}
                <div class="flex items-center gap-2.5 sm:gap-3 text-xs text-slate-500 flex-wrap shrink-0">
                    <div class="flex items-center gap-1.5">
                        <span class="font-medium text-slate-500">Per halaman</span>
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
                                class="flex items-center justify-center gap-1.5 h-[28px] px-2 bg-white border border-slate-200 rounded-md text-xs font-bold text-slate-800 whitespace-nowrap cursor-pointer transition-colors shadow-sm hover:bg-slate-50">
                                <span x-text="selectedVal"></span>
                                <svg class="w-3 h-3 text-slate-400 transition-transform duration-200 shrink-0"
                                    :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                class="absolute left-0 bottom-full mb-1 bg-white border border-slate-200 rounded-lg shadow-lg min-w-[70px] z-50 overflow-hidden"
                                style="display: none;">
                                <div class="p-1">
                                    <button type="button"
                                        @click="selectItem(10, '{{ request()->fullUrlWithQuery(['per_page' => 10, 'page' => 1]) }}')"
                                        class="w-full text-left px-3 py-1.5 text-xs font-medium rounded-md transition-colors cursor-pointer"
                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 10, 'text-slate-700 hover:bg-slate-50': selectedVal != 10}">10</button>
                                    <button type="button"
                                        @click="selectItem(25, '{{ request()->fullUrlWithQuery(['per_page' => 25, 'page' => 1]) }}')"
                                        class="w-full text-left px-3 py-1.5 text-xs font-medium rounded-md transition-colors cursor-pointer"
                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 25, 'text-slate-700 hover:bg-slate-50': selectedVal != 25}">25</button>
                                    <button type="button"
                                        @click="selectItem(50, '{{ request()->fullUrlWithQuery(['per_page' => 50, 'page' => 1]) }}')"
                                        class="w-full text-left px-3 py-1.5 text-xs font-medium rounded-md transition-colors cursor-pointer"
                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedVal == 50, 'text-slate-700 hover:bg-slate-50': selectedVal != 50}">50</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-px h-3.5 bg-slate-200"></div>

                    <span class="text-xs text-slate-500 whitespace-nowrap">
                        Menampilkan <strong class="font-bold text-slate-800">{{ $peminjamans->firstItem() ?? 0 }}</strong>
                        sampai <strong class="font-bold text-slate-800">{{ $peminjamans->lastItem() ?? 0 }}</strong>
                        dari <strong class="font-bold text-slate-800">{{ number_format($peminjamans->total()) }}</strong> entri
                    </span>
                </div>

                {{-- Right: Page buttons --}}
                <div class="flex items-center gap-1 sm:gap-1.5">
                    @if ($peminjamans->onFirstPage())
                        <span
                            class="text-slate-300 cursor-not-allowed w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-md sm:rounded-lg border border-slate-200 bg-white shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $peminjamans->previousPageUrl() }}"
                            class="text-slate-500 hover:text-slate-700 hover:bg-slate-50 w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-md sm:rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
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
                            class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-xs w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-md sm:rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">1</a>
                        @if($startPage > 2)
                            <span class="text-slate-400 font-medium text-xs w-5 flex items-center justify-center">...</span>
                        @endif
                    @endif

                    @foreach ($peminjamans->getUrlRange($startPage, $endPage) as $page => $url)
                        @if ($page == $peminjamans->currentPage())
                            <span
                                class="bg-[#0f1b40] shadow-sm text-white font-bold text-xs w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-md sm:rounded-lg transition-colors">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                                class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-xs w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-md sm:rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($endPage < $peminjamans->lastPage())
                        @if($endPage < $peminjamans->lastPage() - 1)
                            <span class="text-slate-400 font-medium text-xs w-5 flex items-center justify-center">...</span>
                        @endif
                        <a href="{{ $peminjamans->url($peminjamans->lastPage()) }}"
                            class="text-slate-600 hover:bg-slate-50 hover:text-slate-900 font-medium text-xs w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-md sm:rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">{{ $peminjamans->lastPage() }}</a>
                    @endif

                    @if ($peminjamans->hasMorePages())
                        <a href="{{ $peminjamans->nextPageUrl() }}"
                            class="text-slate-500 hover:text-slate-700 hover:bg-slate-50 w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-md sm:rounded-lg border border-slate-200 bg-white shadow-sm transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @else
                        <span
                            class="text-slate-300 cursor-not-allowed w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-md sm:rounded-lg border border-slate-200 bg-white shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    @endif
                </div>
            </div>

            {{-- Modal Tindakan --}}
            <div x-show="modalTindakan" style="display: none;"
                class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6">
                <div x-show="modalTindakan" x-transition.opacity
                    class="fixed inset-0 bg-slate-900/60 transition-opacity" @click="closeModal()">
                </div>

                <div x-show="modalTindakan" 
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                    class="relative bg-white rounded-t-[24px] sm:rounded-[20px] rounded-b-none sm:rounded-b-[20px] shadow-2xl w-full max-w-lg max-h-[95vh] sm:max-h-[90vh] overflow-hidden flex flex-col border-0 sm:border border-gray-100">

                    <div
                        class="px-6 py-4 border-b border-[#0B266E]/10 flex-shrink-0 flex items-center justify-between bg-[#0B266E]/[0.06] z-10">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-[#0B266E] flex items-center justify-center">
                                <span class="material-symbols-outlined text-white" style="font-size:20px">verified</span>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-[#1A1C1E] tracking-tight">Verifikasi Peminjaman</h3>
                                <p class="text-[10px] text-[#0B266E] font-medium">Penanganan peminjaman dari <span class="font-bold" x-text="selectedName"></span></p>
                            </div>
                        </div>
                        <button type="button" @click="closeModal()"
                            class="text-[#0B266E] hover:text-[#091F5E] hover:bg-[#0B266E]/10 transition-colors w-8 h-8 flex items-center justify-center shrink-0 rounded-lg cursor-pointer">
                            <span class="material-symbols-outlined" style="font-size:20px">close</span>
                        </button>
                    </div>

                    <form id="actionForm" method="POST" :action="getFormAction()"
                        class="flex flex-col flex-1 overflow-hidden">
                        @csrf
                        <input type="hidden" name="status" x-model="selectedAction">

                        <div class="p-6 flex-1 overflow-y-auto bg-white">

                            <!-- Ringkasan Informasi -->
                            <div class="mb-5 p-4 bg-white border border-slate-200 rounded-xl space-y-3 shadow-sm">
                                <h4
                                    class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 border-b border-slate-100 pb-2">
                                    Informasi Peminjam</h4>
                                <div class="grid grid-cols-[80px_1fr] gap-x-2 gap-y-2 text-[13px] mb-3 border-b border-slate-100 pb-3">
                                    <div class="text-slate-500 font-medium">Nama</div>
                                    <div class="text-slate-800 font-bold" x-text="selectedName"></div>

                                    <div class="text-slate-500 font-medium">NIM/NIP</div>
                                    <div class="text-slate-800 font-medium" x-text="selectedNim"></div>

                                    <div class="text-slate-500 font-medium flex items-center">No. Telp</div>
                                    <div class="font-medium flex items-center">
                                        <template x-if="selectedTelp && selectedTelp !== '-'">
                                            <a :href="'https://wa.me/' + (selectedTelp.toString().startsWith('0') ? '62' + selectedTelp.toString().substring(1) : selectedTelp)" 
                                                target="_blank" 
                                                class="inline-flex items-center gap-1.5 text-[#25D366] hover:text-[#1da851] hover:underline transition-colors group"
                                                title="Hubungi via WhatsApp">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                                                </svg>
                                                <span x-text="selectedTelp"></span>
                                            </a>
                                        </template>
                                        <template x-if="!selectedTelp || selectedTelp === '-'">
                                            <span class="text-slate-800" x-text="selectedTelp || '-'"></span>
                                        </template>
                                    </div>
                                </div>

                                <h4
                                    class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2 border-b border-slate-100 pb-2 mt-3">
                                    Informasi Peminjaman</h4>
                                <div class="grid grid-cols-[80px_1fr] gap-x-2 gap-y-2 text-[13px]">
                                    <div class="text-slate-500 font-medium">Asal Usul</div>
                                    <div>
                                        <span class="inline-flex items-center text-xs font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-800 border border-slate-200" x-text="selectedSourceLabel"></span>
                                    </div>

                                    <div class="text-slate-500 font-medium">Ruangan</div>
                                    <div class="text-slate-800 font-bold" x-text="selectedRuanganName"></div>

                                    <div class="text-slate-500 font-medium">Waktu</div>
                                    <div class="text-slate-800 font-bold">
                                        <span x-text="selectedFormatTanggal"></span> <span
                                            class="bg-blue-50 text-[#0B266E] px-1.5 py-0.5 rounded text-xs ml-1 font-semibold border border-blue-200"
                                            x-text="selectedJamMulai + ' - ' + selectedJamSelesai + ' WIB'"></span>
                                    </div>

                                    <div class="text-slate-500 font-medium">Kegiatan</div>
                                    <div class="text-slate-800 font-medium leading-snug" x-text="selectedKegiatan">
                                    </div>

                                    <div class="text-slate-500 font-medium pt-1">Berkas</div>
                                    <div class="pt-1">
                                        <template x-if="selectedBerkasUrl">
                                            <a :href="selectedBerkasUrl" target="_blank"
                                                class="text-[13px] font-bold text-[#0065ff] hover:text-[#0052cc] hover:underline transition-colors mt-0.5 inline-block">
                                                Lihat Berkas
                                            </a>
                                        </template>
                                        <template x-if="!selectedBerkasUrl">
                                            <span class="text-gray-400 italic text-[12px]">Tidak dilampirkan</span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Jika Status Menunggu -->
                            <div x-show="selectedStatus === 'menunggu'" class="grid grid-cols-2 gap-3 mb-5">
                                <label class="cursor-pointer">
                                    <input type="radio" x-model="selectedAction" value="disetujui" class="sr-only">
                                    <div :class="selectedAction === 'disetujui' ? 'border-emerald-500 bg-emerald-50 text-emerald-700' : 'border-gray-100 hover:bg-gray-50 text-gray-700'"
                                        class="rounded-xl border-2 px-4 py-3.5 transition-all text-center font-semibold text-sm flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7">
                                            </path>
                                        </svg>
                                        Setujui
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" x-model="selectedAction" value="ditolak" class="sr-only">
                                    <div :class="selectedAction === 'ditolak' ? 'border-red-500 bg-red-50 text-red-700' : 'border-gray-100 hover:bg-gray-50 text-gray-700'"
                                        class="rounded-xl border-2 px-4 py-3.5 transition-all text-center font-semibold text-sm flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12">
                                            </path>
                                        </svg>
                                        Tolak
                                    </div>
                                </label>
                            </div>

                            <!-- Jika Status Disetujui -->
                            <div x-show="selectedStatus === 'disetujui'" style="display: none;" class="mb-5">
                                <div
                                    class="p-3 bg-amber-50 border border-amber-100 rounded-xl flex gap-3 text-amber-800 text-[13px] leading-relaxed mb-4">
                                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                    <p>Perhatian: Status permohonan ini <strong>Telah Disetujui</strong>. Segala bentuk
                                        modifikasi akan langsung berdampak pada jadwal resmi ruangan.</p>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <label class="cursor-pointer block">
                                        <input type="radio" x-model="selectedAction" value="ditolak" class="sr-only">
                                        <div :class="selectedAction === 'ditolak' ? 'border-red-500 bg-red-50 text-red-700' : 'border-gray-100 hover:bg-gray-50 text-gray-700'"
                                            class="rounded-xl border-2 px-3 py-2.5 transition-all text-center font-semibold text-[13px] flex flex-col items-center justify-center gap-1.5 h-full leading-tight">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24"
                                                :class="selectedAction === 'ditolak' ? 'text-red-700' : 'text-red-600/70'">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                            <span>Cabut Hak<br>(Batalkan)</span>
                                        </div>
                                    </label>

                                    <label class="cursor-pointer block">
                                        <input type="radio" x-model="selectedAction" value="edit" class="sr-only">
                                        <div :class="selectedAction === 'edit' ? 'border-[#0B266E] bg-blue-50 text-[#0B266E]' : 'border-gray-100 hover:bg-gray-50 text-gray-700'"
                                            class="rounded-xl border-2 px-3 py-2.5 transition-all text-center font-semibold text-[13px] flex flex-col items-center justify-center gap-1.5 h-full leading-tight">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24"
                                                :class="selectedAction === 'edit' ? 'text-[#0B266E]' : 'text-[#0B266E]/70'">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            <span>Modifikasi<br>Waktu & Ruang</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Formulir Edit Overrride (Tampil Jika dipilih) -->
                            <div x-show="selectedAction === 'edit'" x-transition
                                class="mt-4 mb-2 p-4 bg-slate-50 border border-slate-100 rounded-xl relative overflow-hidden group">
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#0B266E]"></div>
                                <h4 class="text-[13px] font-bold text-slate-800 mb-4 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-[#0B266E]" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    Konfigurasi Jadwal Baru
                                </h4>

                                <div class="grid grid-cols-1 gap-4">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Pindahkan Ke Ruangan <span class="text-red-500">*</span></label>
                                            <div x-data="{ 
                                                    open: false,
                                                    get selectedName() {
                                                        const room = [
                                                            @foreach($ruangans as $ruangan)
                                                                {id: '{{ $ruangan->id }}', name: '{{ addslashes($ruangan->nama) }}'},
                                                            @endforeach
                                                        ].find(r => r.id == currentRoom);
                                                        return room ? room.name : 'Pilih Ruangan...';
                                                    },
                                                    selectItem(id) { 
                                                        currentRoom = id;
                                                        this.open = false; 
                                                    } 
                                                }" class="relative w-full" @click.away="open = false">
                                                
                                                <input type="hidden" name="override_ruangan_id" :value="currentRoom" :required="selectedAction === 'edit'">

                                                <button type="button" @click="open = !open" 
                                                    class="mp-input w-full bg-white text-[13px] font-semibold border-slate-200 rounded-lg focus:ring-1 focus:ring-[#0B266E] flex items-center justify-between transition-colors h-[42px] px-3 cursor-pointer">
                                                    <span x-text="selectedName" class="truncate" :class="{'text-gray-400': !currentRoom, 'text-gray-800': currentRoom}"></span>
                                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                    </svg>
                                                </button>
                                                
                                                <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" 
                                                    class="absolute left-0 bottom-full mb-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-[60] max-h-48 overflow-y-auto" style="display: none;">
                                                    <div class="p-1">
                                                        <button type="button" @click="selectItem('')" class="w-full text-left px-3 py-2 text-[13px] font-medium rounded-md transition-colors cursor-pointer" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': currentRoom == '', 'text-gray-700 hover:bg-gray-50': currentRoom != ''}">Pilih Ruangan...</button>
                                                        
                                                        @foreach($ruangans as $ruangan)
                                                            <button type="button" @click="selectItem('{{ $ruangan->id }}')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors cursor-pointer" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': currentRoom == '{{ $ruangan->id }}', 'text-gray-700 hover:bg-gray-50': currentRoom != '{{ $ruangan->id }}'}">
                                                                {{ $ruangan->nama }}
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Ubah Kegiatan <span class="text-red-500">*</span></label>
                                            <input type="text" name="override_tujuan" x-model="currentTujuan"
                                                class="mp-input w-full bg-white text-gray-800 text-[13px] font-semibold border-slate-200 rounded-lg focus:ring-1 focus:ring-[#0B266E]"
                                                :required="selectedAction === 'edit'" placeholder="Misal: Rapat Internal">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Ubah
                                                Tanggal <span class="text-red-500">*</span></label>
                                            <input type="date" name="override_tanggal_pinjam" x-model="currentDate"
                                                class="mp-input w-full bg-white text-gray-800 text-[13px] font-semibold border-slate-200 rounded-lg focus:ring-1 focus:ring-[#0B266E]"
                                                :required="selectedAction === 'edit'">
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label
                                                    class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Jam
                                                    Mulai</label>
                                                <input type="time" name="override_jam_mulai" x-model="currentJamMulai"
                                                    class="mp-input w-full bg-white text-gray-800 text-[13px] font-semibold border-slate-200 rounded-lg focus:ring-1 focus:ring-[#0B266E]"
                                                    :required="selectedAction === 'edit'">
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Sampai</label>
                                                <input type="time" name="override_jam_selesai"
                                                    x-model="currentJamSelesai"
                                                    class="mp-input w-full bg-white text-gray-800 text-[13px] font-semibold border-slate-200 rounded-lg focus:ring-1 focus:ring-[#0B266E]"
                                                    :required="selectedAction === 'edit'">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div x-show="conflictError" x-transition
                                    class="mt-4 p-3 bg-red-50 border border-red-200 rounded-xl flex items-start gap-2 text-red-700 text-xs font-semibold">
                                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                    <span x-text="conflictError"></span>
                                </div>
                            </div>

                            <div x-show="selectedAction === 'ditolak'" x-transition class="mt-4 mb-2">
                                <label
                                    class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Alasan
                                    Penolakan <span class="text-red-500">*</span></label>
                                <textarea name="alasan_penolakan"
                                    class="w-full text-[13px] p-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-1 focus:ring-red-500 focus:border-red-500 outline-none transition-colors mb-2"
                                    rows="3" placeholder="Contoh: Jadwal bentrok dengan acara jurusan..."
                                    :required="selectedAction === 'ditolak'"></textarea>
                            </div>
                        </div> <!-- End of scrollable body -->

                        <!-- Fixed Footer -->
                        <div class="px-6 py-4 pb-8 sm:pb-4 flex gap-3 z-10 w-full justify-end border-t border-slate-100 bg-white">
                            <button type="button" @click="closeModal()"
                                class="flex-1 cursor-pointer py-2.5 px-4 border border-slate-200 text-slate-600 rounded-xl font-semibold text-sm hover:bg-slate-50 transition-all">Batalkan</button>
                            <button type="submit"
                                class="flex-1 cursor-pointer py-2.5 px-4 bg-[#0B266E] hover:bg-[#071946] text-white rounded-xl font-semibold text-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                                :disabled="!selectedAction || (selectedAction === 'edit' && (conflictError !== '' || isCheckingOut))">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        const appRuangans = @json($ruangans->map(fn($r) => ['id' => (string)$r->id, 'name' => $r->nama]));

        function roomFilterSelect(selectedId, selectedName) {
            return {
                open: false,
                selectedId: selectedId || '',
                selectedName: selectedName || 'Semua Ruangan',
                searchTerm: '',
                isTyping: false,
                rooms: appRuangans,
                get displayValue() {
                    if (this.isTyping) {
                        return this.searchTerm;
                    }
                    return this.selectedId ? this.selectedName : '';
                },
                get filteredRooms() {
                    if (!this.searchTerm || !this.searchTerm.trim()) {
                        return this.rooms;
                    }
                    const q = this.searchTerm.toLowerCase();
                    return this.rooms.filter(r => r.name.toLowerCase().includes(q));
                },
                selectItem(id, name) { 
                    this.selectedId = id; 
                    this.selectedName = name ? name : 'Semua Ruangan'; 
                    this.searchTerm = '';
                    this.isTyping = false;
                    this.open = false; 
                    if (document.activeElement && typeof document.activeElement.blur === 'function') {
                        document.activeElement.blur();
                    }
                },
                handleInput(val) {
                    this.isTyping = true;
                    this.searchTerm = val;
                    this.open = true;
                },
                handleFocus() {
                    this.open = true;
                    this.searchTerm = '';
                    this.isTyping = false;
                },
                handleClose() {
                    this.open = false;
                    this.isTyping = false;
                    this.searchTerm = '';
                    if (document.activeElement && typeof document.activeElement.blur === 'function') {
                        document.activeElement.blur();
                    }
                }
            };
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('persetujuanManager', () => ({
                modalTindakan: false,
                selectedId: null,
                selectedName: '',
                selectedNim: '',
                selectedTelp: '',
                selectedStatus: '',
                selectedAction: '',
                selectedSourceLabel: '',

                selectedRuanganName: '',
                selectedKegiatan: '',
                selectedFormatTanggal: '',
                selectedJamMulai: '',
                selectedJamSelesai: '',
                selectedBerkasUrl: '',

                currentRoom: '',
                currentDate: '',
                currentJamMulai: '',
                currentJamSelesai: '',
                currentTujuan: '',

                conflictError: '',
                isCheckingOut: false,
                checkTimeout: null,

                init() {
                    this.$watch('currentRoom', () => this.triggerCheck());
                    this.$watch('currentDate', () => this.triggerCheck());
                    this.$watch('currentJamMulai', () => this.triggerCheck());
                    this.$watch('currentJamSelesai', () => this.triggerCheck());
                },

                triggerCheck() {
                    if (this.selectedAction !== 'edit') {
                        this.conflictError = '';
                        return;
                    }
                    if (this.checkTimeout) clearTimeout(this.checkTimeout);

                    this.isCheckingOut = true;
                    this.checkTimeout = setTimeout(() => {
                        this.executeCheck();
                    }, 500);
                },

                async executeCheck() {
                    if (!this.currentRoom || !this.currentDate || !this.currentJamMulai || !this.currentJamSelesai) {
                        this.isCheckingOut = false;
                        return;
                    }

                    try {
                        let url = `{{ route('eoffice.peminjaman.admin.persetujuan.check-collision') }}?ruangan_id=${this.currentRoom}&tanggal_pinjam=${this.currentDate}&jam_mulai=${this.currentJamMulai}&jam_selesai=${this.currentJamSelesai}&exclude_id=${this.selectedId}`;
                        let res = await fetch(url);
                        let data = await res.json();

                        if (data.conflict) {
                            this.conflictError = data.message;
                        } else {
                            this.conflictError = '';
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.isCheckingOut = false;
                    }
                },

                openAction(id, name, nim, telp, status, ruanganId, tanggal, jamMulai, jamSelesai, ruanganName, kegiatan, formatTanggal, berkasUrl, sourceLabel = 'Peminjaman Langsung') {
                    this.selectedId = id;
                    this.selectedName = name;
                    this.selectedNim = nim;
                    this.selectedTelp = telp;
                    this.selectedStatus = status;
                    this.selectedSourceLabel = sourceLabel || 'Peminjaman Langsung';
                    this.selectedAction = '';

                    this.selectedRuanganName = ruanganName;
                    this.selectedKegiatan = kegiatan;
                    this.selectedFormatTanggal = formatTanggal;
                    this.selectedJamMulai = jamMulai;
                    this.selectedJamSelesai = jamSelesai;
                    this.selectedBerkasUrl = berkasUrl;

                    this.currentRoom = ruanganId;
                    this.currentDate = tanggal;
                    this.currentJamMulai = jamMulai;
                    this.currentJamSelesai = jamSelesai;
                    this.currentTujuan = kegiatan;
                    this.conflictError = '';

                    if (status === 'disetujui') {
                        this.selectedAction = 'ditolak';
                    } else {
                        this.selectedAction = '';
                    }

                    this.modalTindakan = true;
                },

                closeModal() {
                    this.modalTindakan = false;
                    this.selectedId = null;
                    if (this.checkTimeout) clearTimeout(this.checkTimeout);
                },

                getFormAction() {
                    if (!this.selectedId) return '#';

                    if (this.selectedAction === 'edit') {
                        let baseUrl = "{{ route('eoffice.peminjaman.admin.persetujuan.override', 'REPLACE_ID') }}";
                        return baseUrl.replace('REPLACE_ID', this.selectedId);
                    } else {
                        let baseUrl = "{{ route('eoffice.peminjaman.admin.persetujuan.update', 'REPLACE_ID') }}";
                        return baseUrl.replace('REPLACE_ID', this.selectedId);
                    }
                }
            }))
        })
    </script>
</x-eoffice::manajemen-ruangan.layout>