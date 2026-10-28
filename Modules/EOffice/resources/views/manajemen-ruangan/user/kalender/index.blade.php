<x-eoffice::manajemen-ruangan.layout pageTitle="Kalender Ruangan">
    @php
        $bukaInt = (int) substr($jamBuka, 0, 2);
        $tutupInt = (int) substr($jamTutup, 0, 2);
        $jamList = range($bukaInt, max($bukaInt, $tutupInt - 1)); // Dinamis berdasar jam buka/tutup admin

        // Build week days array: Mon-Sun
        $weekDays = [];
        for ($d = 0; $d < 7; $d++) {
            $weekDays[] = $weekStart->copy()->addDays($d);
        }

        
        // Build absolute events map: [date][ruangan_id] => list of events
        $eventsMap = [];
        $pxPerMinute = 1; // 1 pixel = 1 menit, 1 jam = 60 pixel
        
        foreach ($bookingsRaw as $b) {
            $tgl = is_string($b->tanggal_pinjam) ? $b->tanggal_pinjam : $b->tanggal_pinjam->format('Y-m-d');
            $eventsMap[$tgl][$b->ruangan_id][] = [
                'id' => 'pm_' . $b->id,
                'status' => $b->status,
                'tujuan' => $b->tujuan ?? '',
                'pengguna' => $b->user->name ?? 'Mahasiswa',
                'user_id' => $b->user_id,
                'jam_mulai' => substr($b->jam_mulai, 0, 5),
                'jam_selesai' => substr($b->jam_selesai, 0, 5),
                'source' => $b->source ?? null,
            ];
        }

        $rutins = collect($internalSchedules)->where('tipe_jadwal', 'rutin');
        $spesifiks = collect($internalSchedules)->where('tipe_jadwal', 'spesifik');

        foreach ([$rutins, $spesifiks] as $scheduleGroup) {
            foreach ($scheduleGroup as $j) {
                if ($j->tipe_jadwal === 'spesifik') {
                    $tgl = \Carbon\Carbon::parse($j->tanggal_spesifik)->format('Y-m-d');
                    $eventsMap[$tgl][$j->ruangan_id][] = [
                        'id' => 'it_' . $j->id,
                        'status' => 'internal',
                        'tipe_jadwal' => 'spesifik',
                        'type' => $j->kategori ?? 'Agenda Internal',
                        'tujuan' => $j->keterangan ?? '',
                        'jam_mulai' => substr($j->jam_mulai, 0, 5),
                        'jam_selesai' => substr($j->jam_selesai, 0, 5)
                    ];
                } else if ($j->tipe_jadwal === 'rutin') {
                    foreach ($weekDays as $day) {
                        if ($day->dayOfWeekIso == $j->hari) {
                            $tgl = $day->format('Y-m-d');
                            if (!empty($j->tgl_mulai_efektif) && $tgl < $j->tgl_mulai_efektif) continue;
                            if (!empty($j->tgl_selesai_efektif) && $tgl > $j->tgl_selesai_efektif) continue;
                            $eventsMap[$tgl][$j->ruangan_id][] = [
                                'id' => 'it_' . $j->id,
                                'status' => 'internal',
                                'tipe_jadwal' => 'rutin',
                                'type' => $j->kategori ?? 'Jadwal Akademik (Kuliah)',
                                'tujuan' => $j->keterangan ?? '',
                                'jam_mulai' => substr($j->jam_mulai, 0, 5),
                                'jam_selesai' => substr($j->jam_selesai, 0, 5)
                            ];
                        }
                    }
                }
            }
        }
        
        // HUKUM MENGALAH (VISUAL OVERRIDE)
        // Menghapus kotak jadwal rutin dari UI jika bertabrakan waktu dengan jadwal spesifik (Blokir Ruangan)
        foreach ($eventsMap as $tgl => &$ruanganEvents) {
            foreach ($ruanganEvents as $rId => &$events) {
                $spesifikEvents = array_filter($events, fn($e) => isset($e['tipe_jadwal']) && $e['tipe_jadwal'] === 'spesifik');
                if (count($spesifikEvents) > 0) {
                    $events = array_filter($events, function($e) use ($spesifikEvents) {
                        if (!isset($e['tipe_jadwal']) || $e['tipe_jadwal'] !== 'rutin') return true;
                        
                        // Cek apakah waktu rutin ini bertabrakan dengan jadwal spesifik apapun di ruangan dan hari yang sama
                        foreach ($spesifikEvents as $se) {
                            if ($e['jam_mulai'] < $se['jam_selesai'] && $e['jam_selesai'] > $se['jam_mulai']) {
                                return false; // Gusur rutin (jangan di-render ke UI)
                            }
                        }
                        return true;
                    });
                    $events = array_values($events);
                }
            }
        }
        
        $prevWeek = $weekStart->copy()->subWeek()->format('Y-m-d');
        $nextWeek = $weekStart->copy()->addWeek()->format('Y-m-d');

        // Time travel restrictions
        $now = \Carbon\Carbon::now();
        $currentWeekStart = $now->copy()->startOfWeek(\Carbon\Carbon::MONDAY)->format('Y-m-d');
        $canGoBackWeek = $weekStart->format('Y-m-d') > $currentWeekStart;
    @endphp

    {{-- =================== PAGE HEADER =================== --}}
    <div class="mp-page-header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between w-full gap-3 sm:gap-4">
            <div>
                <h1 class="mp-page-title">Kalender Ruangan</h1>
                <p class="mp-page-sub hidden sm:block">Lihat ketersediaan seluruh ruangan dan langsung booking slot yang kosong.</p>
            </div>

            <div class="w-full sm:w-auto flex justify-start sm:justify-end">
                {{-- Room Filter --}}
                <form id="roomFilterForm" method="GET" action="{{ route('eoffice.peminjaman.user.kalender') }}"
                    class="w-full sm:w-auto">
                    <input type="hidden" name="week_start" value="{{ $weekStart->format('Y-m-d') }}">

                    <div x-data="{
                            open: false,
                            search: '',
                            activeTab: 'Semua',
                            selectedCat: '{{ $selectedKategori }}',
                            selectedRoomId: '{{ $selectedRoomId }}',
                            selectedRoomName: '{{ $selectedRoomId ? addslashes($allRuangansDaftar->firstWhere('id', $selectedRoomId)->nama ?? 'Semua Ruangan') : 'Semua Ruangan' }}',
                            rooms: {{ $allRuangansDaftar->map(fn($r) => ['id' => $r->id, 'nama' => $r->nama, 'kategori' => $r->kategori])->toJson() }},
                            categories: {{ $kategoriList->toJson() }},
                            get displayLabel() {
                                if (this.selectedRoomId && this.selectedRoomName && this.selectedRoomName !== 'Semua Ruangan') {
                                    return this.selectedRoomName;
                                }
                                if (this.selectedCat && this.selectedCat !== 'Semua Kategori' && this.selectedCat !== 'Semua') {
                                    return 'Kategori: ' + this.selectedCat;
                                }
                                return 'Semua Ruangan';
                            },
                            get filteredRooms() {
                                let list = this.rooms;
                                if (this.activeTab !== 'Semua') {
                                    list = list.filter(r => r.kategori === this.activeTab);
                                }
                                if (this.search.trim() !== '') {
                                    const q = this.search.toLowerCase().trim();
                                    list = list.filter(r => r.nama.toLowerCase().includes(q) || (r.kategori && r.kategori.toLowerCase().includes(q)));
                                }
                                return list;
                            },
                            selectCat(cat) {
                                this.selectedCat = cat;
                                this.selectedRoomId = '';
                                this.selectedRoomName = (cat === 'Semua' || cat === 'Semua Kategori') ? 'Semua Ruangan' : 'Semua ' + cat;
                                $refs.ruanganInput.value = '';
                                $refs.kategoriInput.value = (cat === 'Semua' ? 'Semua Kategori' : cat);
                                this.open = false;
                                document.getElementById('roomFilterForm').submit();
                            },
                            selectRoom(id, name, cat) {
                                this.selectedRoomId = id;
                                this.selectedRoomName = name;
                                this.selectedCat = cat || this.selectedCat;
                                $refs.ruanganInput.value = id;
                                $refs.kategoriInput.value = this.selectedCat;
                                this.open = false;
                                document.getElementById('roomFilterForm').submit();
                            },
                            init() {
                                if (this.selectedRoomId !== '') {
                                    let currentRoom = this.rooms.find(r => r.id == this.selectedRoomId);
                                    if (currentRoom && currentRoom.kategori) {
                                        this.selectedCat = currentRoom.kategori;
                                        this.activeTab = currentRoom.kategori;
                                        $refs.kategoriInput.value = currentRoom.kategori;
                                    }
                                } else if (this.selectedCat && this.selectedCat !== 'Semua Kategori' && this.selectedCat !== 'Semua') {
                                    this.activeTab = this.selectedCat;
                                }
                            }
                        }" 
                        class="relative w-full sm:w-auto" 
                        @click.away="open = false"
                        @keydown.escape.window="open = false">

                        <input type="hidden" name="ruangan_id" x-ref="ruanganInput" :value="selectedRoomId">
                        <input type="hidden" name="kategori" x-ref="kategoriInput" :value="selectedCat">

                        <!-- Trigger Button -->
                        <button type="button" 
                            @click="open = !open; if(open && window.innerWidth >= 768 && window.matchMedia('(pointer: fine)').matches) { $nextTick(() => $refs.searchInput && $refs.searchInput.focus()); }"
                            class="w-full sm:w-64 md:w-72 flex items-center justify-between gap-2.5 px-3 py-1.5 text-[13px] text-slate-800 font-semibold bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#0B266E]/20 shadow-sm transition-all cursor-pointer">
                            <span x-text="displayLabel" class="truncate font-semibold"></span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0"
                                :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Combobox Popover -->
                        <div x-show="open" x-cloak 
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" 
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75" 
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 top-full mt-1.5 w-full bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-2.5 text-left"
                            style="display: none;">
                            
                            <!-- Search Input (Formal, No Icons) -->
                            <div class="relative mb-2">
                                <input type="text"
                                    x-ref="searchInput"
                                    x-model="search"
                                    placeholder="Cari nama ruangan..."
                                    class="w-full pl-3 pr-7 py-1.5 text-[12px] bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#0B266E] focus:border-[#0B266E] text-slate-800 placeholder-slate-400 transition-all">
                                <button x-show="search.length > 0" 
                                    @click="search = ''; $refs.searchInput.focus()" 
                                    type="button" 
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                                    &#x2715;
                                </button>
                            </div>

                            <!-- Category Filter Tabs / Pills -->
                            <div class="flex items-center gap-1 overflow-x-auto pb-1.5 mb-1.5 border-b border-slate-100 text-[11px] [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden">
                                <button type="button" 
                                    @click="activeTab = 'Semua'"
                                    class="px-2.5 py-1 rounded-md transition-colors cursor-pointer shrink-0"
                                    :class="activeTab === 'Semua' ? 'bg-[#0B266E] text-white font-semibold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                                    Semua
                                </button>
                                <template x-for="cat in categories" :key="cat">
                                    <button type="button" 
                                        @click="activeTab = cat"
                                        class="px-2.5 py-1 rounded-md transition-colors cursor-pointer shrink-0"
                                        :class="activeTab === cat ? 'bg-[#0B266E] text-white font-semibold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                        x-text="cat">
                                    </button>
                                </template>
                            </div>

                            <!-- Macro View: Tampilkan Semua Kategori Ini -->
                            <div class="mb-1">
                                <button type="button" 
                                    @click="selectCat(activeTab)"
                                    class="w-full flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-md text-[12px] font-semibold text-left transition-colors cursor-pointer"
                                    :class="(activeTab === 'Semua' ? (selectedCat === 'Semua Kategori' && !selectedRoomId) : (selectedCat === activeTab && !selectedRoomId))
                                        ? 'bg-blue-50 text-[#0B266E]' 
                                        : 'text-slate-700 hover:bg-slate-50'">
                                    <span class="text-left" x-text="activeTab === 'Semua' ? 'Tampilkan Semua' : 'Tampilkan Semua ' + activeTab"></span>
                                    <template x-if="activeTab === 'Semua' ? (selectedCat === 'Semua Kategori' && !selectedRoomId) : (selectedCat === activeTab && !selectedRoomId)">
                                        <svg class="w-4 h-4 text-[#0B266E] shrink-0 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </template>
                                </button>
                            </div>

                            <div class="h-px bg-slate-100 my-1"></div>

                            <!-- List of Rooms with Checkmark and Badges -->
                            <div class="max-h-52 overflow-y-auto space-y-0.5">
                                <template x-for="r in filteredRooms" :key="r.id">
                                    <button type="button" 
                                        @click="selectRoom(r.id, r.nama, r.kategori)"
                                        class="w-full flex items-center justify-between px-2.5 py-1.5 rounded-md text-[12px] transition-colors cursor-pointer group"
                                        :class="selectedRoomId == r.id ? 'bg-blue-50 text-[#0B266E] font-semibold' : 'text-slate-700 hover:bg-slate-50'">
                                        
                                        <span x-text="r.nama" class="truncate"></span>

                                        <template x-if="selectedRoomId == r.id">
                                            <svg class="w-4 h-4 text-[#0B266E] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </template>
                                    </button>
                                </template>

                                <div x-show="filteredRooms.length === 0" class="py-4 text-center text-xs text-slate-400">
                                    Ruangan tidak ditemukan
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Alpine Wrapper Start --}}
    <div x-data="bookingKalender()" @mouseup.window="stopDragAbsolute()">

        {{-- =================== LEGEND =================== --}}
        <div class="flex flex-wrap items-center gap-4 mt-3 mb-5 text-[12px] font-medium text-gray-600">
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-emerald-400 inline-block"></span> Tersedia (klik untuk booking)
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-amber-400 inline-block"></span> Booking Saya (Menunggu)
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-purple-400 inline-block"></span> Terpakai
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-blue-400 inline-block"></span> Jadwal Kuliah
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-red-400 inline-block"></span> Libur / Tutup
            </div>
        </div>

        {{-- =================== JADWAL KALENDER =================== --}}
            {{-- Week Nav --}}
            <div class="flex items-center justify-between mb-4 mt-2 gap-1.5 sm:gap-3">
                <div class="flex items-center gap-1 sm:gap-2">
                    @if($canGoBackWeek)
                        <a href="{{ request()->fullUrlWithQuery(['week_start' => $prevWeek]) }}"
                            class="inline-flex items-center justify-center gap-1.5 text-[12px] sm:text-[13px] font-semibold px-2.5 sm:px-3.5 py-1.5 sm:py-2 bg-white border border-gray-200 text-gray-700 rounded-lg shadow-sm hover:bg-gray-50 hover:text-[#0B266E] hover:border-gray-300 transition-all min-w-[34px] sm:min-w-[40px] md:min-w-[120px]"
                            title="Minggu Lalu">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span class="hidden md:inline">Minggu Lalu</span>
                        </a>
                    @else
                        <div class="w-8 sm:w-auto"></div>
                    @endif

                    {{-- Hari Ini Button --}}
                    @php
                        $todayDate = \Carbon\Carbon::now()->format('Y-m-d');
                        $isTodayView = $todayDate === $weekStart->format('Y-m-d');
                        $todayUrl = request()->fullUrlWithQuery(['week_start' => $todayDate]);
                    @endphp
                    <a href="{{ $todayUrl }}"
                        class="inline-flex items-center justify-center px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-lg text-[11px] sm:text-[12px] font-bold transition-all border {{ $isTodayView ? 'bg-gray-50 text-gray-400 border-gray-200 cursor-default pointer-events-none' : 'bg-white text-[#0B266E] border-gray-200 hover:bg-[#EFF6FF] hover:border-[#0B266E]/30 shadow-sm' }}"
                        {{ $isTodayView ? 'onclick="return false;"' : '' }} title="Kembali ke Hari Ini">
                        Hari Ini
                    </a>
                </div>

                {{-- Date Picker Dropdown (Weekly) --}}
                <div x-data="{ open: false }" class="relative flex-1 flex justify-center min-w-0">
                    <button @click="open = !open" type="button"
                        class="flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3.5 py-1.5 sm:py-2 text-[12px] sm:text-[13px] md:text-[14px] font-bold text-[#0B266E] bg-slate-50 hover:bg-white border border-slate-200 hover:border-slate-300 shadow-sm rounded-lg sm:rounded-xl transition-all cursor-pointer truncate max-w-full group focus:outline-none focus:ring-2 focus:ring-[#0B266E]/15">
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[#0B266E] shrink-0 opacity-80 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="truncate">{{ $weekStart->translatedFormat('d M') }} — {{ $weekEnd->translatedFormat('d M Y') }}</span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0 group-hover:text-[#0B266E]" :class="{'rotate-180': open}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                        x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute left-1/2 -translate-x-1/2 top-full mt-2 w-[200px] md:w-[220px] bg-white border border-gray-200 rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] z-50 p-3 md:p-4"
                        style="display: none;">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-2 text-center">Pindah ke
                            Tanggal</p>
                        <form method="GET" action="{{ url()->current() }}" class="flex flex-col gap-2">
                            @if(request('ruangan_id'))
                                <input type="hidden" name="ruangan_id" value="{{ request('ruangan_id') }}">
                            @endif
                            @if(request('kategori'))
                                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                            @endif
                            <input type="date" name="week_start" value="{{ $weekStart->format('Y-m-d') }}"
                                class="w-full text-[13px] border-gray-300 rounded-md shadow-sm focus:ring-[#0B266E] focus:border-[#0B266E] cursor-pointer">
                            <button type="submit"
                                class="w-full bg-[#0B266E] text-white text-[12px] font-bold py-1.5 rounded-md hover:bg-[#091F5E] transition-colors cursor-pointer">Pergi</button>
                        </form>
                    </div>
                </div>

                {{-- Right: Next Week --}}
                <a href="{{ request()->fullUrlWithQuery(['week_start' => $nextWeek]) }}"
                    class="inline-flex items-center justify-center gap-1.5 text-[12px] sm:text-[13px] font-semibold px-2.5 sm:px-3.5 py-1.5 sm:py-2 bg-white border border-gray-200 text-gray-700 rounded-lg shadow-sm hover:bg-gray-50 hover:text-[#0B266E] hover:border-gray-300 transition-all min-w-[34px] sm:min-w-[40px] md:min-w-[120px]"
                    title="Minggu Depan">
                    <span class="hidden md:inline">Minggu Depan</span>
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            
            @php 
                $minTWidth = 64 + (7 * $ruangans->count() * 75); 
                $pxPerHour = 60; // 1 menit = 1 pixel
                $totalHours = count($jamList);
                $gridHeight = $totalHours * $pxPerHour;
            @endphp
            {{-- Calendar Grid (Absolute Positioning) --}}
            <div id="calendar-grid-wrapper" class="mp-card overflow-hidden select-none">
                <div id="table-scroll-container" style="overflow-x: auto; position: relative;">
                    <table style="width: 100%; table-layout: fixed; border-collapse: collapse; font-size: 12px; min-width: {{ max(900, $minTWidth) }}px;">
                        <thead>
                            <tr style="background: #F1F3F9;">
                                <th style="width: 64px; min-width:64px; border: 1px solid #E5E7EB; padding: 10px 8px; text-align:center; background:#F8F9FB; color: #4B5563; font-weight: 700; position: sticky; left: 0; z-index: 30; border-right: 2px solid #D1D5DB;">Jam</th>
                                @foreach($weekDays as $day)
                                    <th colspan="{{ $ruangans->count() }}" style="border: 1px solid #E5E7EB; padding: 8px 6px; text-align:center; font-weight: 700; color: #0B266E; {{ $day->isToday() ? 'background: #EFF6FF;' : 'background: #F8F9FB;' }}">
                                        <div style="position: sticky; left: 74px; width: fit-content; margin: 0 auto; display: inline-flex; flex-direction: column; align-items: center; justify-content: center;">
                                            <div style="font-size:13px; font-weight: 700;">{{ $day->translatedFormat('D') }}</div>
                                            <div style="font-size:11px; font-weight:600; color: #0B266E; margin-top:1px;">{{ $day->format('d/m') }}</div>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                            <tr style="background: #FAFAFA;">
                                <th style="border: 1px solid #E5E7EB; background: #FAFAFA; position: sticky; left: 0; z-index: 30; border-right: 2px solid #D1D5DB;"></th>
                                @foreach($weekDays as $day)
                                    @foreach($ruangans as $ruang)
                                        <th style="border: 1px solid #E5E7EB; padding: 6px 4px; text-align:center; font-size:10px; font-weight:700; color:#6B7280; min-width: 72px;">{{ $ruang->nama }}</th>
                                    @endforeach
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                {{-- Kolom Jam --}}
                                <td style="border: 1px solid #E5E7EB; padding: 0; background:#F8F9FB; position: relative; height: {{ $gridHeight }}px; vertical-align: top; width: 64px; min-width: 64px; position: sticky; left: 0; z-index: 20; border-right: 2px solid #D1D5DB;">
                                    
                                    

                                    @foreach($jamList as $index => $jam)
                                        <div style="position: absolute; top: {{ $index * $pxPerHour }}px; width: 100%; height: {{ $pxPerHour }}px; text-align: center; font-weight:700; font-size:11px; color:#374151; box-sizing: border-box; padding-top: 4px; transform: translateY(-50%);">
                                            {{ str_pad($jam, 2, '0', STR_PAD_LEFT) }}.00
                                        </div>
                                    @endforeach
                                </td>
                                
                                {{-- Kolom Hari & Ruangan --}}
                                @foreach($weekDays as $day)
                                    @foreach($ruangans as $ruang)
                                        @php
                                            $dateStr = $day->format('Y-m-d');
                                            $isPastDay = $day->isPast() && !$day->isToday();
                                            $isHoliday = isset($holidays[$dateStr]);
                                            $isClosedWeekend = !$bukaAkhirPekan && $day->isWeekend();
                                            $minDate = \Carbon\Carbon::today()->addDays($batasHMinBooking);
                                            $isTooEarly = $day->copy()->startOfDay()->lt($minDate);
                                            $isClosedAllDay = $isPastDay || $isHoliday || $isClosedWeekend || $isTooEarly;
                                            $bgCell = $isClosedAllDay ? '#F3F4F6' : '#FFFFFF';
                                        @endphp
                                            @php
                                                $events = $eventsMap[$dateStr][$ruang->id] ?? [];
                                                $eventBounds = [];
                                                foreach($events as $ev) {
                                                    $mStartArr = explode(':', $ev['jam_mulai']);
                                                    $mEndArr = explode(':', $ev['jam_selesai']);
                                                    $startMinutes = ( (int)$mStartArr[0] * 60 + (int)$mStartArr[1] ) - ($bukaInt * 60);
                                                    $endMinutes = ( (int)$mEndArr[0] * 60 + (int)$mEndArr[1] ) - ($bukaInt * 60);
                                                    if ($startMinutes < 0) $startMinutes = 0;
                                                    if ($endMinutes > ($totalHours * 60)) $endMinutes = ($totalHours * 60);
                                                    if ($endMinutes > $startMinutes) {
                                                        $eventBounds[] = [$startMinutes, $endMinutes];
                                                    }
                                                }
                                                $eventBoundsJson = json_encode($eventBounds);
                                            @endphp
                                            <td style="border: 1px solid #E5E7EB; padding: 0; position: relative; height: {{ $gridHeight }}px; vertical-align: top; background: {{ $bgCell }}; min-width: 72px;"
                                                @if(!$isClosedAllDay)
                                                    @mousedown.prevent="startDragAbsolute($event, '{{ $ruang->id }}', '{{ addslashes($ruang->nama) }}', '{{ $dateStr }}', {{ $bukaInt }}, {{ $totalHours }}, {{ $eventBoundsJson }})"
                                                @mousemove.prevent="doDragAbsolute($event)"
                                                @mouseup.prevent="stopDragAbsolute()"
                                                @touchstart.passive="handleTouchStart($event)"
                                                @touchmove.passive="handleTouchMove($event)"
                                                @touchend="handleTouchEnd($event, '{{ $ruang->id }}', '{{ addslashes($ruang->nama) }}', '{{ $dateStr }}', {{ $bukaInt }}, {{ $totalHours }}, {{ $eventBoundsJson }})"
                                                @mouseenter="hoverCol = '{{ $dateStr }}_{{ $ruang->id }}'"
                                                @mouseleave="hoverCol = null; if(isDragging) stopDragAbsolute()"
                                                :style="hoverCol === '{{ $dateStr }}_{{ $ruang->id }}' && !isDragging ? 'background: #F8FAFC;' : ''"
                                                class="cursor-crosshair relative"
                                            @endif
                                        >
                                            <div style="position: relative; width: 100%; height: 100%; min-height: {{ $gridHeight }}px;">
                                            {{-- Garis Grid per Jam --}}
                                            @foreach($jamList as $index => $jam)
                                                <div style="position: absolute; top: {{ $index * $pxPerHour }}px; width: 100%; height: {{ $pxPerHour }}px; border-top: 1px dashed #E5E7EB; box-sizing: border-box; pointer-events: none;"></div>
                                            @endforeach

                                            {{-- Events --}}
                                            @php
                                                $events = $eventsMap[$dateStr][$ruang->id] ?? [];
                                            @endphp
                                            @foreach($events as $ev)
                                                @php
                                                    $mStartArr = explode(':', $ev['jam_mulai']);
                                                    $mEndArr = explode(':', $ev['jam_selesai']);
                                                    
                                                    $startMinutes = ( (int)$mStartArr[0] * 60 + (int)$mStartArr[1] ) - ($bukaInt * 60);
                                                    $endMinutes = ( (int)$mEndArr[0] * 60 + (int)$mEndArr[1] ) - ($bukaInt * 60);
                                                    
                                                    if ($startMinutes < 0) $startMinutes = 0;
                                                    if ($endMinutes > ($totalHours * 60)) $endMinutes = ($totalHours * 60);
                                                    
                                                    $top = $startMinutes; // 1 menit = 1 px
                                                    $height = $endMinutes - $startMinutes;
                                                    if ($height <= 0) continue;

                                                    $status = $ev['status'];
                                                    $type = $ev['type'] ?? '';
                                                    $source = $ev['source'] ?? null;
                                                    $isPraktikum = strtolower((string)$source) === 'praktikum';
                                                    $isOwn = ($ev['user_id'] ?? null) === auth()->id();
                                                    $tujuan = $ev['tujuan'];
                                                    
                                                    if ($status === 'disetujui') {
                                                        if ($isPraktikum) {
                                                            $bg = 'rgba(219, 234, 254, 0.95)'; $border = '#60A5FA'; $tColor = '#1E40AF';
                                                            $label = $tujuan ?: 'Praktikum';
                                                            $onClick = "openDetailModal('".addslashes($label)."', '-', '".addslashes($ruang->nama)."', '".$day->translatedFormat('l, d M Y')."', '{$ev['jam_mulai']} - {$ev['jam_selesai']}', 'praktikum', '')";
                                                        } else {
                                                            $bg = 'rgba(237, 233, 254, 0.9)'; $border = '#C4B5FD'; $label = 'Terpakai'; $tColor = '#5B21B6';
                                                            $onClick = "openDetailModal('Terpakai', '-', '".addslashes($ruang->nama)."', '".$day->translatedFormat('l, d M Y')."', '{$ev['jam_mulai']} - {$ev['jam_selesai']}', 'terpakai', '')";
                                                        }
                                                    } elseif ($status === 'internal') {
                                                        $isKuliah = in_array($type, ['Jadwal Akademik (Kuliah)', 'Kuliah Pengganti', 'Pindah Kelas', 'Pindah / Pengganti Kelas']);
                                                        $isMaintenance = in_array($type, ['Pemeliharaan / Penutupan', 'Pemeliharaan/Penutupan', 'Maintenance / Perbaikan', 'Sterilisasi Ruangan', 'Penutupan Khusus']);
                                                        $isLainnya = in_array($type, ['Lainnya', 'Lainnya...', 'Event / Kegiatan', 'Rapat Internal']);
                                                        $isUjian = in_array($type, ['Ujian & Evaluasi', 'Ujian / Evaluasi', 'Ujian / Evaluasi (UTS/UAS)']);

                                                        if ($isKuliah) {
                                                            $bg = 'rgba(219, 234, 254, 0.9)'; $border = '#60A5FA'; $tColor = '#1E40AF';
                                                        } elseif ($isMaintenance) {
                                                            $bg = 'rgba(254, 226, 226, 0.9)'; $border = '#F87171'; $tColor = '#991B1B';
                                                        } else {
                                                            // Ujian & Evaluasi dan Lainnya -> UNGU
                                                            $bg = 'rgba(237, 233, 254, 0.9)'; $border = '#C4B5FD'; $tColor = '#5B21B6';
                                                        }

                                                        if ($isLainnya) {
                                                            $label = 'Terpakai';
                                                            $onClick = "openDetailModal('Terpakai', '-', '".addslashes($ruang->nama)."', '".$day->translatedFormat('l, d M Y')."', '{$ev['jam_mulai']} - {$ev['jam_selesai']}', 'terpakai', '')";
                                                        } else {
                                                            $cleanTujuan = trim(str_ireplace(['digunakan untuk', ' - Kelas ', ' (Kelas ', ')'], ['', '-', '-', ''], $tujuan));
                                                            $label = $cleanTujuan ?: ($type ?: 'Kegiatan');
                                                            $matkul = $label; $kelas = '-';
                                                            if ($isKuliah && strpos($label, '-') !== false) {
                                                                $parts = explode('-', $label);
                                                                $kelas = trim(array_pop($parts));
                                                                $matkul = trim(implode('-', $parts));
                                                            }
                                                            $onClick = "openDetailModal('".addslashes($matkul)."', '".addslashes($kelas)."', '".addslashes($ruang->nama)."', '".$day->translatedFormat('l, d M Y')."', '{$ev['jam_mulai']} - {$ev['jam_selesai']}', 'internal', '')";
                                                        }
                                                    } elseif ($status === 'menunggu' && $isPraktikum) {
                                                        $bg = 'rgba(219, 234, 254, 0.9)'; $border = '#60A5FA'; $label = 'Agenda Akademik'; $tColor = '#1E40AF';
                                                        $onClick = "openDetailModal('Agenda Akademik', '-', '".addslashes($ruang->nama)."', '".$day->translatedFormat('l, d M Y')."', '{$ev['jam_mulai']} - {$ev['jam_selesai']}', 'agenda_akademik', '')";
                                                    } elseif ($status === 'menunggu' && $isOwn) {
                                                        $bg = 'rgba(254, 249, 195, 0.9)'; $border = '#FBBF24'; $label = 'Menunggu'; $tColor = '#B45309';
                                                        $onClick = "openDetailModal('Menunggu Konfirmasi', '-', '".addslashes($ruang->nama)."', '".$day->translatedFormat('l, d M Y')."', '{$ev['jam_mulai']} - {$ev['jam_selesai']}', 'menunggu', '".addslashes($tujuan)."')";
                                                    } elseif ($status === 'menunggu') {
                                                        $bg = 'rgba(237, 233, 254, 0.9)'; $border = '#C4B5FD'; $label = 'Terpakai'; $tColor = '#5B21B6';
                                                        $onClick = "openDetailModal('Terpakai', '-', '".addslashes($ruang->nama)."', '".$day->translatedFormat('l, d M Y')."', '{$ev['jam_mulai']} - {$ev['jam_selesai']}', 'terpakai', '')";
                                                    }
                                                @endphp
                                                <div @mousedown.stop @click.stop="{!! $onClick !!}"
                                                    class="absolute left-0.5 right-0.5 rounded shadow-sm overflow-hidden flex flex-col justify-center items-center px-1 py-0.5 cursor-pointer hover:shadow-md transition-all z-10"
                                                    style="top: {{ $top }}px; height: {{ $height }}px; background: {{ $bg }}; border: 1px solid {{ $border }}; backdrop-filter: blur(2px);"
                                                    @mouseover="$el.style.transform='translateY(-2px)'" @mouseout="$el.style.transform='translateY(0)'">
                                                    <span style="font-size: 9px; font-weight: 800; color: {{ $tColor }}; text-align: center; line-height: 1.1; word-break: break-word;">{{ $label }}</span>
                                                    @if($height >= 30)
                                                    <span style="font-size: 8px; font-weight: 600; color: {{ $tColor }}; opacity: 0.8; margin-top: 1px;">{{ $ev['jam_mulai'] }}-{{ $ev['jam_selesai'] }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                            
                                            {{-- Indikator Waktu Saat Ini --}}
                                            @if($day->isToday())
                                                @php
                                                    $now = \Carbon\Carbon::now();
                                                    $nowMinutes = ($now->hour * 60 + $now->minute) - ($bukaInt * 60);
                                                @endphp
                                                @if($nowMinutes >= 0 && $nowMinutes <= ($totalHours * 60))
                                                    <div style="position: absolute; top: {{ $nowMinutes }}px; left: 0; right: 0; height: 2px; background: #EF4444; z-index: 15; pointer-events: none;">
                                                        <div style="position: absolute; left: -4px; top: -3px; width: 8px; height: 8px; border-radius: 50%; background: #EF4444;"></div>
                                                    </div>
                                                @endif
                                            @endif

                                            {{-- Area Drag (Aktif saat di-drag) --}}
                                            <template x-if="isDragging && dragRoom == '{{ $ruang->id }}' && dragDate == '{{ $dateStr }}'">
                                                <div class="absolute left-0 right-0 bg-emerald-500/50 z-20 pointer-events-none"
                                                     :style="`top: ${dragTop}px; height: ${dragHeight}px;`">
                                                </div>
                                            </template>
                                            </div>
                                        </td>
                                    @endforeach
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>





        {{-- =================== DETAIL MODAL =================== --}}
        <div x-show="showDetailModal" x-cloak style="display: none;"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
            aria-labelledby="detail-modal-title" role="dialog" aria-modal="true">

            {{-- Backdrop --}}
            <div x-show="showDetailModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 transition-opacity" aria-hidden="true"
                @click="closeDetailModal"></div>

            {{-- Modal Panel --}}
            <div x-show="showDetailModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative bg-white rounded-[20px] border border-gray-100 shadow-2xl w-full max-w-sm flex flex-col overflow-hidden">

                <div class="px-5 py-4 flex flex-col items-center">
                    <div class="text-[#0B266E] font-extrabold text-[15px] mb-1 text-center w-full pb-3 border-b border-gray-100"
                        x-text="detailData.kegiatan"></div>

                    <div class="w-full mt-3 space-y-2">
                        {{-- Kelas: hanya untuk Jadwal Kuliah (internal) --}}
                        <div class="flex justify-between items-center text-[12px]"
                            x-show="detailData.status === 'internal' && detailData.kelas !== '-'">
                            <span class="text-gray-500 font-medium">Kelas</span>
                            <span class="text-gray-800 font-bold" x-text="detailData.kelas"></span>
                        </div>
                        <div class="flex justify-between items-center text-[12px]">
                            <span class="text-gray-500 font-medium">Ruangan</span>
                            <span class="text-gray-800 font-bold" x-text="detailData.ruangan"></span>
                        </div>
                        {{-- Tanggal & Waktu: selalu tampil --}}
                        <div class="flex justify-between items-center text-[12px]">
                            <span class="text-gray-500 font-medium">Tanggal</span>
                            <span class="text-gray-800 font-bold" x-text="detailData.tanggal"></span>
                        </div>
                        <div class="flex justify-between items-center text-[12px]">
                            <span class="text-gray-500 font-medium">Waktu</span>
                            <span class="text-gray-800 font-bold" x-text="detailData.waktu"></span>
                        </div>
                        {{-- Tujuan: hanya untuk booking milik sendiri (menunggu) --}}
                        <div class="flex justify-between items-center text-[12px]"
                            x-show="detailData.status === 'menunggu' && detailData.tujuan">
                            <span class="text-gray-500 font-medium">Keperluan</span>
                            <span class="text-gray-800 font-bold text-right max-w-[55%]"
                                x-text="detailData.tujuan"></span>
                        </div>
                        {{-- Pesan konfirmasi untuk booking sendiri --}}
                        <div x-show="detailData.status === 'menunggu'" class="mt-2 pt-2 border-t border-amber-100">
                            <p class="text-[11px] text-amber-600 font-medium text-center">Peminjaman Anda sedang
                                menunggu konfirmasi dari admin.</p>
                        </div>
                        {{-- Pesan untuk Agenda Akademik (Praktikum Menunggu) --}}
                        <div x-show="detailData.status === 'agenda_akademik'" class="mt-2 pt-2 border-t border-blue-100">
                            <p class="text-[11px] text-blue-700 font-medium text-center">Kegiatan akademik terjadwal sedang dalam tahap konfirmasi dan reservasi ruangan.</p>
                        </div>
                        {{-- Pesan untuk Praktikum Terjadwal --}}
                        <div x-show="detailData.status === 'praktikum'" class="mt-2 pt-2 border-t border-blue-100">
                            <p class="text-[11px] text-blue-700 font-medium text-center">Kegiatan Praktikum Laboratorium Terjadwal.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 border-t border-gray-100 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="closeDetailModal"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-[#0B266E] text-sm font-bold text-white hover:bg-[#091F5E] focus:outline-none transition-colors sm:w-auto sm:text-[13px] cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        {{-- =================== BOOKING MODAL =================== --}}
        <div x-show="showModal" x-cloak style="display: none;"
            class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6 text-left whitespace-normal"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">

            {{-- Backdrop --}}
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 transition-opacity" aria-hidden="true"
                @click="closeModal"></div>

            {{-- Modal Panel --}}
            <div x-show="showModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-full sm:translate-y-4 sm:scale-95"
                class="relative bg-white rounded-t-[24px] sm:rounded-t-[20px] rounded-b-none sm:rounded-b-[20px] border-0 sm:border border-gray-100 shadow-2xl w-full max-w-[500px] max-h-[95vh] sm:max-h-[90vh] flex flex-col p-0 overflow-visible">

                <div class="p-4 sm:p-6 flex flex-col flex-1 min-h-0">
                    {{-- Header --}}
                    <div class="-mx-4 sm:-mx-6 -mt-4 sm:-mt-6 mb-5 px-6 py-4 border-b border-[#0B266E]/10 flex items-center justify-between bg-[#0B266E]/[0.06] rounded-none sm:rounded-t-[20px]">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-[#0B266E] flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-[#1A1C1E] tracking-tight" id="modal-title">Form Booking Ruangan</h3>
                                <p class="text-[10px] text-[#0B266E] font-medium">Meminjam <span class="font-bold" x-text="bookingData.roomName"></span> pada <span class="font-bold" x-text="bookingData.waktu"></span></p>
                            </div>
                        </div>
                        <button type="button" @click="closeModal"
                            class="text-[#0B266E] hover:text-[#091F5E] hover:bg-[#0B266E]/10 transition-colors w-8 h-8 flex items-center justify-center shrink-0 rounded-lg cursor-pointer">
                            <span class="material-symbols-outlined" style="font-size:20px">close</span>
                        </button>
                    </div>

                    <form id="bookingForm" method="POST" action="{{ route('eoffice.peminjaman.user.booking.store') }}"
                        @submit="if(isSubmitDisabled) { $event.preventDefault(); return false; }"
                        enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0" autocomplete="off">
                        @csrf
                        <input type="hidden" name="ruangan_id" :value="selectedRoomId">
                        <input type="hidden" name="tanggal_pinjam" :value="selectedDate">

                        {{-- Scrollable Body --}}
                        <div class="space-y-4 overflow-y-auto overflow-x-hidden flex-1 bg-white whitespace-normal -mx-4 sm:-mx-6 px-4 sm:px-6 pb-2">
                            {{-- Waktu Peminjaman --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Jam Mulai <span class="text-red-500">*</span></label>
                                    <input type="time" name="jam_mulai" x-model="bookingData.jamMulai" required
                                        class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all text-gray-800">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Jam Selesai <span class="text-red-500">*</span></label>
                                    <input type="time" name="jam_selesai" x-model="bookingData.jamSelesai" required
                                        class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all text-gray-800">
                                </div>
                            </div>

                            <div x-show="isInvalidTimeRange" x-cloak
                                class="text-[11px] text-amber-800 bg-amber-50 border border-amber-200 px-3 py-2 rounded-xl font-medium">
                                Jam selesai tidak boleh mendahului atau sama dengan jam mulai.
                            </div>

                            <div x-show="!isInvalidTimeRange && isPastTimeToday" x-cloak
                                class="text-[11px] text-amber-800 bg-amber-50 border border-amber-200 px-3 py-2 rounded-xl font-medium">
                                Waktu mulai tidak boleh menggunakan jam yang telah berlalu hari ini.
                            </div>

                            {{-- Identitas Peminjam --}}
                            <div class="space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                                        <input type="text" class="w-full bg-slate-100/80 border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium text-gray-600 cursor-not-allowed outline-none"
                                            value="{{ $user->name ?? 'Student' }}" readonly>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">NIM / NIP</label>
                                        <input type="text" class="w-full bg-slate-100/80 border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium text-gray-600 cursor-not-allowed outline-none"
                                            value="{{ $nim ?? '00000' }}" readonly>
                                    </div>
                                </div>
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between ml-1">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            No. Telepon / WhatsApp <span class="text-red-500">*</span>
                                        </label>
                                        @if(!empty($phone))
                                            <a href="{{ route('profile.edit') }}" target="_blank" rel="noopener noreferrer"
                                                class="text-[10px] font-bold text-[#0B266E] hover:text-blue-700 hover:underline inline-flex items-center gap-1 transition-colors group"
                                                title="Ubah nomor kontak di profil akun Anda">
                                                <span>Ubah di Profil</span>
                                                <svg class="w-3 h-3 text-[#0B266E] group-hover:text-blue-700 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        @endif
                                    </div>

                                    @if(!empty($phone))
                                        <input type="text" name="nomor_telepon" value="{{ $phone }}" readonly required
                                            class="w-full bg-slate-100/80 border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium text-gray-600 cursor-not-allowed outline-none select-all"
                                            title="Nomor kontak tersambung dengan profil akun Anda">
                                    @else
                                        <input type="tel" name="nomor_telepon" value="{{ old('nomor_telepon') }}" required
                                            maxlength="15" minlength="10" inputmode="numeric" autocomplete="tel"
                                            placeholder="Contoh: 08123456789"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            class="w-full bg-white border @error('nomor_telepon') border-red-400 focus:ring-red-100 focus:border-red-500 @else border-slate-200 focus:ring-blue-100 focus:border-blue-400 @enderror rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 outline-none transition-all text-gray-800">
                                        <p class="text-[11px] text-slate-500 ml-1 flex items-center gap-1.5 leading-snug">
                                            <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>Nomor ini akan otomatis disimpan sebagai kontak profil akun Anda.</span>
                                        </p>
                                    @endif
                                    @error('nomor_telepon')
                                        <p class="text-[11px] text-rose-600 ml-1 font-semibold flex items-center gap-1.5 mt-1">
                                            <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span>{{ $message }}</span>
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Detail Kegiatan --}}
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Kegiatan <span class="text-red-500">*</span></label>
                                <input type="text" name="tujuan" placeholder="Misal: Rapat Kerja HIMASKOM" required maxlength="150"
                                    class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all text-gray-800">
                            </div>

                            {{-- Berkas Pendukung (Opsional) --}}
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">File Berkas <span class="text-slate-400 font-normal lowercase tracking-normal">(opsional)</span></label>
                                <div class="relative bg-white border border-slate-200 rounded-xl p-2 focus-within:ring-2 focus-within:ring-blue-100 focus-within:border-blue-400 transition-all">
                                    <input type="file" name="file_berkas" accept=".pdf"
                                        class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#0B266E]/10 file:text-[#0B266E] hover:file:bg-[#0B266E]/20 cursor-pointer transition-all outline-none">
                                </div>
                                <p class="text-[10px] text-gray-400 ml-1 leading-relaxed">Format <strong class="text-gray-600 font-semibold">PDF</strong> maksimal <strong class="text-gray-600 font-semibold">2MB</strong>. Biasanya diperlukan untuk persetujuan acara berskala besar atau formal.</p>
                                @error('file_berkas')
                                    <p class="text-[11px] text-rose-600 mt-2 font-bold flex items-center gap-1.5 bg-rose-50 p-2.5 rounded-xl border border-rose-100">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Ukuran maksimal 2MB dan wajib berformat PDF.
                                    </p>
                                @enderror
                            </div>

                            {{-- Card 5: Persetujuan S&K --}}
                            <div class="space-y-3 p-4 bg-blue-50/50 border border-blue-100 rounded-2xl">
                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <div class="flex items-center h-5 mt-0.5 shrink-0">
                                        <input type="checkbox" name="syarat_ketentuan" required
                                            class="w-4 h-4 border border-blue-300 rounded bg-white text-[#0B266E] focus:ring-[#0B266E] focus:ring-2 transition-all cursor-pointer shadow-sm"
                                            style="scroll-margin-bottom: 80px; scroll-margin-top: 120px;">
                                    </div>
                                    <span class="text-[11px] text-gray-600 leading-relaxed block">
                                        Saya setuju dan bersedia <strong class="text-gray-900 font-bold">merapikan kembali ruangan</strong> setelah digunakan serta siap <strong class="text-gray-900 font-bold">bertanggung jawab penuh mengganti kerusakan</strong> barang atau fasilitas akibat kelalaian selama peminjaman.
                                    </span>
                                </label>
                            </div>

                            <!-- Mobile Footer (Inside scrollable area so it doesn't follow keyboard) -->
                            <div class="sm:hidden mt-6 pt-4 pb-2 border-t border-slate-100 flex items-center justify-end gap-3">
                                <button type="button" @click="closeModal"
                                    class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-center cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit"
                                    :disabled="isSubmitDisabled"
                                    :class="isSubmitDisabled ? 'opacity-50 cursor-not-allowed bg-slate-400 shadow-none' : 'bg-[#0B266E] hover:bg-[#091F5E] shadow-lg shadow-[#0B266E]/20 cursor-pointer'"
                                    class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0B266E]/50 transition-all text-center flex items-center justify-center">
                                    Kirim Pengajuan
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Desktop Sticky Footer -->
                    <div class="hidden sm:flex mt-5 items-center justify-end gap-3 shrink-0">
                        <button type="button" @click="closeModal"
                            class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-center cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" form="bookingForm"
                            :disabled="isSubmitDisabled"
                            :class="isSubmitDisabled ? 'opacity-50 cursor-not-allowed bg-slate-400 shadow-none' : 'bg-[#0B266E] hover:bg-[#091F5E] shadow-lg shadow-[#0B266E]/20 cursor-pointer'"
                            class="px-6 py-2.5 text-sm font-semibold text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0B266E]/50 transition-all text-center flex items-center justify-center">
                            Kirim Pengajuan
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- Close Alpine Wrapper -->

    <script>
        // AJAX Polling setiap 30 detik untuk update Real-time
        document.addEventListener('DOMContentLoaded', () => {
            setInterval(() => {
                const kalenderDiv = document.querySelector('[x-data="bookingKalender()"]');
                if (window.Alpine && kalenderDiv) {
                    try {
                        const data = Alpine.$data(kalenderDiv);
                        // Jangan update UI jika user sedang berinteraksi dengan modal atau dragging
                        if (data && (data.showModal || data.showDetailModal || data.isDragging)) return;
                    } catch (e) {}
                }

                fetch(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        const newContainer = doc.getElementById('calendar-grid-wrapper');
                        const currentContainer = document.getElementById('calendar-grid-wrapper');

                        if (newContainer && currentContainer) {
                            // Cek apakah konten grid berubah, jika sama persis abaikan
                            if (newContainer.innerHTML.trim() === currentContainer.innerHTML.trim()) {
                                return;
                            }

                            // Simpan posisi scroll sebelum replace
                            let scrollWrapper = currentContainer.querySelector('#table-scroll-container');
                            let currentScroll = scrollWrapper ? scrollWrapper.scrollLeft : 0;

                            if (window.Alpine && typeof window.Alpine.destroyTree === 'function') {
                                window.Alpine.destroyTree(currentContainer);
                            }
                            delete currentContainer._x_marker;

                            currentContainer.innerHTML = newContainer.innerHTML;

                            // Kembalikan posisi scroll setelah replace
                            let newScrollWrapper = currentContainer.querySelector('#table-scroll-container');
                            if (newScrollWrapper) {
                                newScrollWrapper.scrollLeft = currentScroll;
                            }

                            // Inisialisasi ulang Alpine agar tombol dan event handler tetap aktif
                            if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                                window.Alpine.initTree(currentContainer);
                            }
                        }
                    })
                    .catch(err => console.error('Polling error:', err));
            }, 30000);
        });

        if (!document.getElementById('alpine-cloak-style')) {
            const style = document.createElement('style');
            style.id = 'alpine-cloak-style';
            style.innerHTML = '[x-cloak] { display: none !important; }';
            document.head.appendChild(style);
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('bookingKalender', () => ({
                showModal: false,
                selectedRoomId: '',
                selectedDate: '',
                hoverCol: null,
                
                // Absolute Drag properties
                isDragging: false,
                dragRoom: null,
                dragRoomName: '',
                dragDate: null,
                dragStartY: 0,
                dragCurrentY: 0,
                bukaJam: 0,
                totalDurasiJam: 0,
                
                get dragTop() {
                    return Math.min(this.dragStartY, this.dragCurrentY);
                },
                get dragHeight() {
                    return Math.max(Math.abs(this.dragCurrentY - this.dragStartY), 15); // Minimal 15 menit
                },
                get dragTimeText() {
                    let startMin = this.dragTop;
                    let endMin = this.dragTop + this.dragHeight;
                    
                    let sH = Math.floor(startMin / 60) + this.bukaJam;
                    let sM = Math.floor(startMin % 60);
                    let eH = Math.floor(endMin / 60) + this.bukaJam;
                    let eM = Math.floor(endMin % 60);
                    
                    return `${String(sH).padStart(2,'0')}:${String(sM).padStart(2,'0')} - ${String(eH).padStart(2,'0')}:${String(eM).padStart(2,'0')}`;
                },
                
                get isPastTimeToday() {
                    if (!this.selectedDate || !this.bookingData.jamMulai) return false;
                    let today = new Date();
                    let todayStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
                    if (this.selectedDate !== todayStr) return false;
                    let currentHM = String(today.getHours()).padStart(2, '0') + ':' + String(today.getMinutes()).padStart(2, '0');
                    return this.bookingData.jamMulai <= currentHM;
                },

                get isInvalidTimeRange() {
                    if (!this.bookingData.jamMulai || !this.bookingData.jamSelesai) return false;
                    return this.bookingData.jamSelesai <= this.bookingData.jamMulai;
                },

                get isSubmitDisabled() {
                    return this.isPastTimeToday || this.isInvalidTimeRange;
                },

                bookingData: {
                    roomName: '',
                    waktu: '',
                    jamMulai: '',
                    jamSelesai: ''
                },

                showDetailModal: false,
                detailData: { kegiatan: '', kelas: '', ruangan: '', tanggal: '', waktu: '', status: '', tujuan: '' },

                openDetailModal(kegiatan, kelas, ruangan, tanggal, waktu, status = '', tujuan = '') {
                    this.detailData = { kegiatan, kelas, ruangan, tanggal, waktu, status, tujuan };
                    this.showDetailModal = true;
                },
                closeDetailModal() { this.showDetailModal = false; },

                // Touchscreen Tap Properties & Methods
                touchStartX: 0,
                touchStartY: 0,
                touchMoved: false,

                handleTouchStart(e) {
                    if (e.touches && e.touches.length === 1) {
                        this.touchStartX = e.touches[0].clientX;
                        this.touchStartY = e.touches[0].clientY;
                        this.touchMoved = false;
                    }
                },

                handleTouchMove(e) {
                    if (e.touches && e.touches.length === 1) {
                        let dx = Math.abs(e.touches[0].clientX - this.touchStartX);
                        let dy = Math.abs(e.touches[0].clientY - this.touchStartY);
                        if (dx > 8 || dy > 8) {
                            this.touchMoved = true;
                        }
                    }
                },

                handleTouchEnd(e, roomId, roomName, date, bukaInt, totalHours, eventBounds = []) {
                    // Jika layar sedang di-scroll (horizontal/vertikal), abaikan
                    if (this.touchMoved) return;

                    // Jika menyentuh kotak event yang sudah ada, biarkan event box yang membuka modal detail
                    if (e.target && e.target.closest && e.target.closest('.cursor-pointer') && e.target !== e.currentTarget) {
                        return;
                    }

                    if (e.cancelable) {
                        e.preventDefault();
                    }

                    let rect = e.currentTarget.getBoundingClientRect();
                    let y = this.touchStartY - rect.top;

                    this.selectSingleTap(roomId, roomName, date, bukaInt, totalHours, y, eventBounds);
                },

                selectSingleTap(roomId, roomName, date, bukaInt, totalHours, y, eventBounds = []) {
                    let snapMin = Math.floor(y / 15) * 15;
                    if (snapMin < 0) snapMin = 0;
                    if (snapMin >= totalHours * 60) snapMin = (totalHours * 60) - 15;

                    // Cek batas waktu lampau hari ini
                    let today = new Date();
                    let todayYmd = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
                    let minAllowedY = 0;
                    if (date === todayYmd) {
                        minAllowedY = (today.getHours() * 60 + today.getMinutes()) - (bukaInt * 60);
                    }
                    if (snapMin < minAllowedY) {
                        if (typeof showCustomToast === 'function') {
                            showCustomToast('Slot waktu telah berlalu hari ini.');
                        }
                        return;
                    }

                    // Cek apakah klik berada di dalam event yang sudah ada
                    for (let bound of eventBounds) {
                        if (snapMin >= bound[0] && snapMin < bound[1]) {
                            return;
                        }
                    }

                    // Tentukan durasi default 1 jam (60 menit)
                    let startMin = snapMin;
                    let endMin = startMin + 60;

                    // Batasi jika menabrak event lain di bawahnya atau jam tutup
                    let maxAllowedY = totalHours * 60;
                    for (let bound of eventBounds) {
                        if (bound[0] >= startMin && bound[0] < maxAllowedY) {
                            maxAllowedY = bound[0];
                        }
                    }
                    if (endMin > maxAllowedY) {
                        endMin = maxAllowedY;
                    }
                    if (endMin <= startMin) {
                        return;
                    }

                    let sH = Math.floor(startMin / 60) + bukaInt;
                    let sM = Math.floor(startMin % 60);
                    let eH = Math.floor(endMin / 60) + bukaInt;
                    let eM = Math.floor(endMin % 60);

                    let startHStr = String(sH).padStart(2, '0') + ':' + String(sM).padStart(2, '0');
                    let endHStr = String(eH).padStart(2, '0') + ':' + String(eM).padStart(2, '0');

                    this.selectedRoomId = roomId;
                    this.selectedDate = date;
                    this.bookingData.roomName = roomName;

                    let d = new Date(date);
                    let dateStr = d.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

                    this.bookingData.waktu = dateStr;
                    this.bookingData.jamMulai = startHStr;
                    this.bookingData.jamSelesai = endHStr;

                    this.showModal = true;
                    document.body.style.overflow = 'hidden';
                },

                // Absolute Drag Methods
                startDragAbsolute(e, roomId, roomName, date, bukaInt, totalHours, eventBounds = []) {
                    if (e.button !== 0) return; // Hanya klik kiri
                    if (this.showModal || this.showDetailModal) return;
                    
                    let rect = e.currentTarget.getBoundingClientRect();
                    let y = e.clientY - rect.top;

                    // --- MENCEGAH KLIK DI MASA LALU (HARI INI) ---
                    let today = new Date();
                    let todayYmd = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
                    
                    let minAllowedY = 0;
                    if (date === todayYmd) {
                        minAllowedY = (today.getHours() * 60 + today.getMinutes()) - (bukaInt * 60);
                    }
                    
                    // Jika klik di atas batas waktu sekarang (area masa lalu), gagalkan
                    if (y < minAllowedY) {
                        return;
                    }
                    // ---------------------------------------------

                    this.isDragging = true;
                    this.dragRoom = roomId;
                    this.dragRoomName = roomName;
                    this.dragDate = date;
                    this.bukaJam = bukaInt;
                    this.totalDurasiJam = totalHours;
                    this.minAllowedY = minAllowedY; // Simpan untuk doDragAbsolute
                    this.eventBounds = eventBounds;
                    
                    this.dragStartY = Math.floor(y / 15) * 15; // Snap 15 menit
                    
                    // Pastikan snapping startY tidak mundur ke masa lalu
                    let snapMin = Math.ceil(minAllowedY / 15) * 15;
                    if (this.dragStartY < snapMin && minAllowedY > 0) {
                        this.dragStartY = snapMin;
                    }

                    // --- MENCEGAH KLIK DI DALAM EVENT YANG SUDAH ADA ---
                    for (let bound of this.eventBounds) {
                        if (this.dragStartY >= bound[0] && this.dragStartY < bound[1]) {
                            this.isDragging = false;
                            return;
                        }
                    }

                    // --- MENENTUKAN BATAS DRAG (ATAS & BAWAH) BERDASARKAN EVENT LAIN ---
                    this.maxAllowedY = totalHours * 60; // Default mentok bawah
                    this.minAllowedYEvent = 0; // Default mentok atas
                    
                    for (let bound of this.eventBounds) {
                        if (bound[0] >= this.dragStartY) {
                            if (bound[0] < this.maxAllowedY) {
                                this.maxAllowedY = bound[0];
                            }
                        }
                        if (bound[1] <= this.dragStartY) {
                            if (bound[1] > this.minAllowedYEvent) {
                                this.minAllowedYEvent = bound[1];
                            }
                        }
                    }
                    
                    // Gabungkan dengan batas masa lalu (time travel)
                    if (this.minAllowedY !== undefined && this.minAllowedY > this.minAllowedYEvent) {
                        this.minAllowedYEvent = this.minAllowedY;
                    }
                    
                    this.dragCurrentY = this.dragStartY + 60; // Default rentang klik = 1 Jam
                    // Jika drag 1 jam nabrak maxAllowedY, sesuaikan
                    if (this.dragCurrentY > this.maxAllowedY) {
                        this.dragCurrentY = this.maxAllowedY;
                    }
                },
                
                doDragAbsolute(e) {
                    if (!this.isDragging) return;
                    let rect = e.currentTarget.getBoundingClientRect();
                    let y = e.clientY - rect.top;
                    
                    let snappedY = Math.floor(y / 15) * 15;
                    
                    if (snappedY < 0) snappedY = 0;
                    if (snappedY > (this.totalDurasiJam * 60)) snappedY = this.totalDurasiJam * 60;
                    
                    // --- MENCEGAH TARIKAN KE MASA LALU ATAU EVENT LAIN ---
                    if (this.minAllowedYEvent !== undefined && snappedY < this.minAllowedYEvent) {
                        snappedY = this.minAllowedYEvent;
                    }
                    if (this.maxAllowedY !== undefined && snappedY > this.maxAllowedY) {
                        snappedY = this.maxAllowedY;
                    }
                    // -------------------------------------
                    
                    this.dragCurrentY = snappedY;
                },
                
                stopDragAbsolute() {
                    if (this.isDragging) {
                        let startMin = this.dragTop;
                        let endMin = this.dragTop + this.dragHeight;
                        
                        let sH = Math.floor(startMin / 60) + this.bukaJam;
                        let sM = Math.floor(startMin % 60);
                        let eH = Math.floor(endMin / 60) + this.bukaJam;
                        let eM = Math.floor(endMin % 60);
                        
                        let startHStr = String(sH).padStart(2,'0') + ':' + String(sM).padStart(2,'0');
                        let endHStr = String(eH).padStart(2,'0') + ':' + String(eM).padStart(2,'0');
                        
                        this.selectedRoomId = this.dragRoom;
                        this.selectedDate = this.dragDate;
                        this.bookingData.roomName = this.dragRoomName;
                        
                        let d = new Date(this.dragDate);
                        let dateStr = d.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                        
                        this.bookingData.waktu = dateStr;
                        this.bookingData.jamMulai = startHStr;
                        this.bookingData.jamSelesai = endHStr;
                        
                        this.showModal = true;
                        document.body.style.overflow = 'hidden';
                    }
                    this.isDragging = false;
                    this.dragRoom = null;
                },

                closeModal() {
                    this.showModal = false;
                    document.body.style.overflow = '';
                }
            }))
        })

        // Fungsi untuk menampilkan Custom Toast UI
        function showCustomToast(message) {
            // Hapus toast lama jika ada
            const oldToast = document.getElementById('client-toast');
            if (oldToast) oldToast.remove();

            const toast = document.createElement('div');
            toast.id = 'client-toast';
            // Gunakan class bawaan sistem agar desainnya seragam (mp-flash mp-flash-error)
            toast.className = 'mp-flash mp-flash-error';
            toast.style.position = 'fixed';
            toast.style.top = '24px';
            toast.style.left = '50%';
            toast.style.transform = 'translateX(-50%)';
            toast.style.zIndex = '99999';
            toast.style.justifyContent = 'space-between';
            toast.style.borderRadius = '8px';
            toast.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
            toast.style.minWidth = '320px';
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 300ms ease, top 300ms ease';

            toast.innerHTML = `
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span>${message}</span>
                </div>
                <button onclick="this.parentElement.style.opacity='0'; setTimeout(()=>this.parentElement.remove(), 300)" style="background:transparent; border:none; cursor:pointer; color:inherit; padding:0; display:flex; align-items:center; opacity:0.7;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            `;

            document.body.appendChild(toast);

            // Animasi masuk
            requestAnimationFrame(() => {
                toast.style.opacity = '1';
                toast.style.top = '32px';
            });

            // Hilang otomatis dalam 5 detik
            setTimeout(() => {
                if (document.body.contains(toast)) {
                    toast.style.opacity = '0';
                    toast.style.top = '24px';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 5000);
        }

        // Client-Side Validation untuk File Upload
        document.addEventListener('change', function (e) {
            if (e.target && e.target.name === 'file_berkas') {
                const file = e.target.files[0];
                if (file) {
                    // Validasi Ukuran (Maks 2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        showCustomToast('Ukuran file "' + file.name + '" terlalu besar (Maksimal 2 MB).');
                        e.target.value = ''; // Reset input seketika
                    }
                    // Validasi Ekstensi/MIME (Hanya PDF)
                    else if (file.type !== 'application/pdf') {
                        showCustomToast('Format file tidak didukung. Hanya file PDF yang diizinkan.');
                        e.target.value = ''; // Reset input seketika
                    }
                }
            }
        });
    </script>
</x-eoffice::manajemen-ruangan.layout>