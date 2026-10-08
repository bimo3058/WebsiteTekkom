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
                'telepon' => $b->nomor_telepon ?? '-',
                'jam_mulai' => substr($b->jam_mulai, 0, 5),
                'jam_selesai' => substr($b->jam_selesai, 0, 5)
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
                        'pengguna' => '-',
                        'telepon' => '-',
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
                                'pengguna' => '-',
                                'telepon' => '-',
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
        
// Month grid
        $calendarDays = [];
        $firstDayOfWeek = (int) $monthStart->format('N'); // 1=Mon ... 7=Sun
        for ($i = 1; $i < $firstDayOfWeek; $i++)
            $calendarDays[] = null; // Pad empty cells
        $curDate = $monthStart->copy();
        while ($curDate->lte($monthEnd)) {
            $calendarDays[] = $curDate->copy();
            $curDate->addDay();
        }

        $prevWeek = $weekStart->copy()->subWeek()->format('Y-m-d');
        $nextWeek = $weekStart->copy()->addWeek()->format('Y-m-d');
        $prevMonth = $monthDate->copy()->subMonth()->format('Y-m');
        $nextMonth = $monthDate->copy()->addMonth()->format('Y-m');

        // Time travel restrictions
        $now = \Carbon\Carbon::now();
        $currentWeekStart = $now->copy()->startOfWeek(\Carbon\Carbon::MONDAY)->format('Y-m-d');
        $currentMonthStart = $now->copy()->startOfMonth()->format('Y-m');

        $canGoBackWeek = $weekStart->format('Y-m-d') > $currentWeekStart;
        $canGoBackMonth = $monthDate->format('Y-m') > $currentMonthStart;
    @endphp

    {{-- =================== PAGE HEADER =================== --}}
    <div class="mp-page-header">
        <div class="flex flex-col md:flex-row md:items-center justify-between w-full gap-4">
            <div>
                <h1 class="mp-page-title">Kalender Ruangan</h1>
                <p class="mp-page-sub">Pantau ketersediaan dan agenda ruang secara komprehensif.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                {{-- Room Filter --}}
                <form id="roomFilterForm" method="GET"
                    action="{{ route('eoffice.peminjaman.admin.kalender-global.index') }}" class="flex items-center">
                    <input type="hidden" name="mode" value="{{ $mode }}">
                    @if($mode === 'week') <input type="hidden" name="week_start"
                    value="{{ $weekStart->format('Y-m-d') }}"> @endif
                    @if($mode === 'month') <input type="hidden" name="month" value="{{ $monthDate->format('Y-m') }}">
                    @endif

                    <div x-data="{
                            openCat: false,
                            openRoom: false,
                            selectedCat: '{{ request()->get('kategori', 'Semua Kategori') }}',
                            selectedRoomId: '{{ $selectedRoomId }}',
                            selectedRoomName: '{{ $selectedRoomId ? addslashes($allRuangansDaftar->firstWhere('id', $selectedRoomId)->nama ?? 'Semua Ruangan') : 'Semua Ruangan' }}',
                            rooms: {{ $allRuangansDaftar->map(fn($r) => ['id'=>$r->id, 'nama'=>$r->nama, 'kategori'=>$r->kategori])->toJson() }},
                            categories: {{ $kategoriList->toJson() }},
                            get filteredRooms() {
                                if (this.selectedCat === 'Semua Kategori') {
                                    return this.rooms;
                                }
                                return this.rooms.filter(r => r.kategori === this.selectedCat);
                            },
                            selectCat(cat) {
                                this.selectedCat = cat;
                                this.openCat = false;
                                // Automatically submit the form to update calendar to show all rooms in this category
                                this.selectedRoomId = '';
                                this.selectedRoomName = 'Semua Ruangan';
                                $refs.ruanganInput.value = '';
                                $refs.kategoriInput.value = cat;
                                document.getElementById('roomFilterForm').submit();
                            },
                            selectRoom(id, name) {
                                this.selectedRoomId = id;
                                this.selectedRoomName = name;
                                $refs.ruanganInput.value = id;
                                $refs.kategoriInput.value = this.selectedCat;
                                document.getElementById('roomFilterForm').submit();
                            },
                            init() {
                                if (this.selectedRoomId !== '') {
                                    let currentRoom = this.rooms.find(r => r.id == this.selectedRoomId);
                                    if (currentRoom && currentRoom.kategori) {
                                        this.selectedCat = currentRoom.kategori;
                                        $refs.kategoriInput.value = currentRoom.kategori;
                                    }
                                }
                            }
                        }" 
                        class="flex flex-col sm:flex-row items-center gap-2">
                        
                        <input type="hidden" name="ruangan_id" x-ref="ruanganInput" :value="selectedRoomId">
                        <input type="hidden" name="kategori" x-ref="kategoriInput" :value="selectedCat">
                        
                        <!-- Dropdown Kategori (Filter 1) -->
                        <div class="relative w-full sm:w-[150px]" @click.away="openCat = false">
                            <button type="button" @click="openCat = !openCat"
                                class="w-full flex items-center justify-between px-3 py-1.5 text-[13px] text-slate-900 font-bold bg-white border border-slate-200 rounded-md hover:bg-slate-50 focus:outline-none shadow-sm transition-colors cursor-pointer">
                                <span x-text="selectedCat" class="truncate pr-2"></span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': openCat}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="openCat" x-cloak x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute left-0 top-full mt-1 w-full min-w-[150px] bg-white border border-slate-200 rounded-md shadow-lg z-50 overflow-y-auto max-h-48"
                                style="display: none;">
                                <div class="py-1">
                                    <button type="button" @click="selectCat('Semua Kategori')"
                                        class="w-full text-left px-3 py-1.5 text-[13px] text-slate-700 hover:bg-slate-50 hover:text-[#0B266E] font-medium transition-colors cursor-pointer"
                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedCat === 'Semua Kategori'}">Semua Kategori</button>
                                    <template x-for="cat in categories" :key="cat">
                                        <button type="button" @click="selectCat(cat)"
                                            class="w-full text-left px-3 py-1.5 text-[13px] text-slate-700 hover:bg-slate-50 hover:text-[#0B266E] font-medium transition-colors cursor-pointer"
                                            :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedCat === cat}"
                                            x-text="cat"></button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown Ruangan (Filter 2) -->
                        <div class="relative w-full sm:w-[170px]" @click.away="openRoom = false">
                            <button type="button" @click="openRoom = !openRoom"
                                class="w-full flex items-center justify-between px-3 py-1.5 text-[13px] text-slate-900 font-bold bg-white border border-slate-200 rounded-md hover:bg-slate-50 focus:outline-none shadow-sm transition-colors cursor-pointer">
                                <span x-text="selectedRoomName" class="truncate pr-2"></span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0" :class="{'rotate-180': openRoom}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="openRoom" x-cloak x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute left-0 top-full mt-1 w-full min-w-[170px] bg-white border border-slate-200 rounded-md shadow-lg z-50 overflow-y-auto max-h-48"
                                style="display: none;">
                                <div class="py-1">
                                    <button type="button" @click="selectRoom('', 'Semua Ruangan')"
                                        class="w-full text-left px-3 py-1.5 text-[13px] text-slate-700 hover:bg-slate-50 hover:text-[#0B266E] font-medium transition-colors cursor-pointer"
                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedRoomId === ''}">Semua Ruangan</button>
                                    <template x-for="r in filteredRooms" :key="r.id">
                                        <button type="button" @click="selectRoom(r.id, r.nama)"
                                            class="w-full text-left px-3 py-1.5 text-[13px] text-slate-700 hover:bg-slate-50 hover:text-[#0B266E] font-medium transition-colors cursor-pointer"
                                            :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedRoomId == r.id}"
                                            x-text="r.nama"></button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Navigation Actions --}}
                <div class="flex items-center gap-2">
                    {{-- Hari Ini Button --}}
                    @php
                        if ($mode === 'week') {
                            $todayDate = \Carbon\Carbon::now()->format('Y-m-d');
                            $isTodayView = $todayDate === $weekStart->format('Y-m-d');
                            $todayUrl = request()->fullUrlWithQuery(['week_start' => $todayDate]);
                        } else {
                            $todayMonth = \Carbon\Carbon::now()->format('Y-m');
                            $isTodayView = $todayMonth === $monthDate->format('Y-m');
                            $todayUrl = request()->fullUrlWithQuery(['month' => $todayMonth]);
                        }
                    @endphp
                    <a href="{{ $todayUrl }}"
                        class="flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-[12px] font-bold transition-all border {{ $isTodayView ? 'bg-gray-50 text-gray-400 border-gray-200 cursor-default' : 'bg-white text-[#0B266E] border-gray-200 hover:bg-[#EFF6FF] hover:border-[#0B266E]/30 shadow-sm' }}"
                        {{ $isTodayView ? 'onclick="return false;"' : '' }} title="Kembali ke Hari Ini">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Hari Ini
                    </a>

                    {{-- Mode Toggle --}}
                    <div class="flex bg-gray-100 rounded-lg p-1 gap-1">
                        <a href="{{ request()->fullUrlWithQuery(['mode' => 'week', 'week_start' => $weekStart->format('Y-m-d')]) }}"
                            class="px-3 py-1.5 rounded-md text-[12px] font-semibold transition-all {{ $mode === 'week' ? 'bg-white text-[#0B266E] shadow-sm' : 'text-gray-500 hover:text-[#0B266E]' }}">
                            Mingguan
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['mode' => 'month', 'month' => $monthDate->format('Y-m')]) }}"
                            class="px-3 py-1.5 rounded-md text-[12px] font-semibold transition-all {{ $mode === 'month' ? 'bg-white text-[#0B266E] shadow-sm' : 'text-gray-500 hover:text-[#0B266E]' }}">
                            Bulanan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine Wrapper Start --}}
    <div x-data="bookingKalender()" @mouseup.window="stopDrag()" class="w-full min-w-0 max-w-full">

        {{-- =================== LEGEND =================== --}}
        <div class="flex flex-wrap items-center gap-4 mt-3 mb-5 text-[12px] font-medium text-gray-600">
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-emerald-400 inline-block"></span> Tersedia
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-amber-400 inline-block"></span> Menunggu Persetujuan
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-purple-400 inline-block"></span> Disetujui (Peminjaman)
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-blue-400 inline-block"></span> Jadwal Kuliah
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-red-400 inline-block"></span> Tutup
            </div>
        </div>

        {{-- =================== WEEKLY MODE =================== --}}
        @if($mode === 'week')
            {{-- Week Nav --}}
            <div class="flex items-center justify-between mb-4 mt-2">
                @if($canGoBackWeek)
                    <a href="{{ request()->fullUrlWithQuery(['week_start' => $prevWeek]) }}"
                        class="inline-flex items-center justify-center gap-1.5 text-[13px] font-semibold px-3 md:px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg shadow-sm hover:bg-gray-50 hover:text-[#0B266E] hover:border-gray-300 transition-all min-w-[36px] md:min-w-[130px]">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="hidden md:inline">Minggu Lalu</span>
                    </a>
                @else
                    <div class="min-w-[36px] md:min-w-[130px]"></div>
                @endif

                {{-- Date Picker Dropdown (Weekly) --}}
                <div x-data="{ open: false }" class="relative flex-1 flex justify-center">
                    <button @click="open = !open" type="button"
                        class="flex items-center gap-1 md:gap-2 text-[12px] md:text-[15px] font-bold text-[#0B266E] hover:bg-gray-100 px-1 md:px-3 py-1.5 rounded-lg transition-colors cursor-pointer text-center">
                        {{ $weekStart->translatedFormat('d M') }} — {{ $weekEnd->translatedFormat('d M Y') }}
                        <svg class="w-3.5 h-3.5 md:w-4 md:h-4 text-gray-500 transition-transform duration-200 shrink-0" :class="{'rotate-180': open}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                        x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute left-1/2 -translate-x-1/2 top-full mt-2 w-[200px] md:w-[220px] bg-white border border-gray-200 rounded-xl shadow-lg z-50 p-3 md:p-4"
                        style="display: none;">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-2 text-center">Pindah ke
                            Tanggal</p>
                        <form method="GET" action="{{ url()->current() }}" class="flex flex-col gap-2">
                            <input type="hidden" name="mode" value="week">
                            @if(request('ruangan_id'))
                                <input type="hidden" name="ruangan_id" value="{{ request('ruangan_id') }}">
                            @endif
                            <input type="date" name="week_start" value="{{ $weekStart->format('Y-m-d') }}"
                                class="w-full text-[13px] border-gray-300 rounded-md shadow-sm focus:ring-[#0B266E] focus:border-[#0B266E] cursor-pointer">
                            <button type="submit"
                                class="w-full bg-[#0B266E] text-white text-[12px] font-bold py-1.5 rounded-md hover:bg-[#091F5E] transition-colors cursor-pointer">Pergi</button>
                        </form>
                    </div>
                </div>

                <a href="{{ request()->fullUrlWithQuery(['week_start' => $nextWeek]) }}"
                    class="inline-flex items-center justify-center gap-1.5 text-[13px] font-semibold px-3 md:px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg shadow-sm hover:bg-gray-50 hover:text-[#0B266E] hover:border-gray-300 transition-all min-w-[36px] md:min-w-[130px]">
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
                                    <th colspan="{{ $ruangans->count() }}" style="border: 1px solid #E5E7EB; padding: 10px 8px; text-align:center; font-weight: 700; color: #0B266E; {{ $day->isToday() ? 'background: #EFF6FF;' : 'background: #F8F9FB;' }}">
                                        <div style="font-size:13px;">{{ $day->translatedFormat('D') }}</div>
                                        <div style="font-size:11px; font-weight:500; color: #0B266E; margin-top:2px;">{{ $day->format('d/m') }}</div>
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
                                            $bgCell = '#FFFFFF';
                                            
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
                                                @mousedown.prevent="startDragAbsolute($event, '{{ $ruang->id }}', '{{ addslashes($ruang->nama) }}', '{{ $dateStr }}', {{ $bukaInt }}, {{ $totalHours }}, {{ $eventBoundsJson }})"
                                                @mousemove.prevent="doDragAbsolute($event)"
                                                @mouseup.prevent="stopDragAbsolute()"
                                                @mouseenter="hoverCol = '{{ $dateStr }}_{{ $ruang->id }}'"
                                                @mouseleave="hoverCol = null; if(isDragging) stopDragAbsolute()"
                                                :style="hoverCol === '{{ $dateStr }}_{{ $ruang->id }}' && !isDragging ? 'background: #F8FAFC;' : ''"
                                                class="cursor-crosshair relative"
                                        >
                                            <div style="position: relative; width: 100%; height: 100%; min-height: {{ $gridHeight }}px;">
                                            {{-- Garis Grid per Jam --}}
                                            @foreach($jamList as $index => $jam)
                                                <div style="position: absolute; top: {{ $index * $pxPerHour }}px; width: 100%; height: {{ $pxPerHour }}px; border-top: 1px dashed #E5E7EB; box-sizing: border-box; pointer-events: none;"></div>
                                            @endforeach

                                            {{-- Indikator Hari Libur di background --}}
                                            @if($isHoliday)
                                                <div style="position: absolute; inset: 0; background: rgba(254, 226, 226, 0.4); z-index: 1; pointer-events: none; display:flex; align-items:center; justify-content:center;">
                                                    <span style="transform: rotate(-90deg); color: #EF4444; font-weight: bold; font-size: 14px; opacity: 0.5; white-space: nowrap;">
                                                        {{ ucwords(strtolower($holidays[$dateStr])) }}
                                                    </span>
                                                </div>
                                            @endif

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
                                                    $isMenunggu = $status === 'menunggu';
                                                    $tujuan = $ev['tujuan'];
                                                    $pengguna = $ev['pengguna'] ?? '-';
                                                    $telepon = $ev['telepon'] ?? '-';
                                                    
                                                    $bg = '#EDE9FE';
                                                    $border = '#C4B5FD';
                                                    $text = '#5B21B6';
                                                    $eventType = 'Peminjaman';
                                                    
                                                    if ($status === 'internal') {
                                                        $eventType = 'Jadwal Internal';
                                                        if ($type === 'Jadwal Akademik (Kuliah)' || $type === 'Pindah Kelas' || $type === 'Pindah / Pengganti Kelas') {
                                                            $bg = '#DBEAFE'; $border = '#60A5FA'; $text = '#1E40AF';
                                                        } elseif ($type === 'Ujian / Evaluasi (UTS/UAS)' || $type === 'Lainnya...') {
                                                            $bg = '#EDE9FE'; $border = '#C4B5FD'; $text = '#5B21B6';
                                                        } else {
                                                            $bg = '#FEE2E2'; $border = '#F87171'; $text = '#991B1B';
                                                        }
                                                        $label = trim(str_ireplace(['digunakan untuk', ' - Kelas ', ' (Kelas ', ')'], ['', '-', '-', ''], $tujuan));
                                                    } else {
                                                        if ($isMenunggu) {
                                                            $bg = '#FEF9C3'; $border = '#FBBF24'; $text = '#B45309';
                                                            $label = 'Menunggu';
                                                        } else {
                                                            $label = substr($pengguna, 0, 15);
                                                        }
                                                    }
                                                    
                                                    $waktuStr = "{$ev['jam_mulai']} - {$ev['jam_selesai']}";
                                                    
                                                    $onClick = "window.dispatchEvent(new CustomEvent('open-event-modal', {
                                                        detail: {
                                                            title: '".addslashes($label)."',
                                                            pengguna: '".addslashes($pengguna)."',
                                                            ruangan: '".addslashes($ruang->nama)."',
                                                            tujuan: '".addslashes($tujuan)."',
                                                            tanggal: '".$day->translatedFormat('l, d M Y')."',
                                                            waktu: '".addslashes($waktuStr)."',
                                                            type: '".addslashes($eventType)."',
                                                            telepon: '".addslashes($telepon)."'
                                                        }
                                                    }))";
                                                @endphp
                                                <div @mousedown.stop @click.stop="{!! $onClick !!}"
                                                    class="absolute left-0.5 right-0.5 rounded shadow-sm overflow-hidden flex flex-col justify-center items-center px-1 py-0.5 cursor-pointer hover:shadow-md transition-all z-10"
                                                    style="top: {{ $top }}px; height: {{ $height }}px; background: {{ $bg }}; border: 1px solid {{ $border }}; backdrop-filter: blur(2px);"
                                                    @mouseover="$el.style.transform='translateY(-2px)'" @mouseout="$el.style.transform='translateY(0)'">
                                                    <span style="font-size: 9px; font-weight: 800; color: {{ $text }}; text-align: center; line-height: 1.1; word-break: break-word;">{{ $label }}</span>
                                                    @if($height >= 30)
                                                    <span style="font-size: 8px; font-weight: 600; color: {{ $text }}; opacity: 0.8; margin-top: 1px;">{{ $waktuStr }}</span>
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




            {{-- =================== MONTHLY MODE =================== --}}
        @else
            {{-- Month Nav --}}
            <div class="flex items-center justify-between mb-4">
                @if($canGoBackMonth)
                    <a href="{{ request()->fullUrlWithQuery(['month' => $prevMonth]) }}"
                        class="inline-flex items-center justify-center gap-1.5 text-[13px] font-semibold px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg shadow-sm hover:bg-gray-50 hover:text-[#0B266E] hover:border-gray-300 transition-all min-w-[130px]">
                        ← Bulan Lalu
                    </a>
                @else
                    <div class="px-4 py-2 min-w-[130px]"></div>
                @endif

                {{-- Month/Year Picker Dropdown (Monthly) --}}
                <div x-data="{ open: false, selectedYear: {{ $monthDate->format('Y') }} }" class="relative">
                    <button @click="open = !open" type="button"
                        class="flex items-center gap-2 text-[15px] font-bold text-[#0B266E] hover:bg-[#EFF6FF] px-4 py-1.5 rounded-lg transition-colors cursor-pointer border border-transparent hover:border-[#0B266E]/20">
                        {{ $monthDate->translatedFormat('F Y') }}
                        <svg class="w-4 h-4 text-[#0B266E] transition-transform duration-200" :class="{'rotate-180': open}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-cloak
                        x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute left-1/2 -translate-x-1/2 top-full mt-2 w-[280px] bg-white border border-gray-200 rounded-xl shadow-lg z-50 p-4"
                        style="display: none;">

                        <!-- Year selector -->
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                            <button type="button" @click="selectedYear--"
                                class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-500 hover:text-[#0B266E] transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <span class="font-bold text-[16px] text-[#0B266E]" x-text="selectedYear"></span>
                            <button type="button" @click="selectedYear++"
                                class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-500 hover:text-[#0B266E] transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>

                        <!-- Months grid -->
                        <div class="grid grid-cols-3 gap-2">
                            @php
                                $months = [
                                    '01' => 'Jan',
                                    '02' => 'Feb',
                                    '03' => 'Mar',
                                    '04' => 'Apr',
                                    '05' => 'Mei',
                                    '06' => 'Jun',
                                    '07' => 'Jul',
                                    '08' => 'Agu',
                                    '09' => 'Sep',
                                    '10' => 'Okt',
                                    '11' => 'Nov',
                                    '12' => 'Des'
                                ];
                                $currentMonthNum = $monthDate->format('m');
                                $currentYearNum = $monthDate->format('Y');
                            @endphp

                            @foreach($months as $num => $name)
                                <button type="button" @click="
                                                            let url = new URL(window.location.href);
                                                            url.searchParams.set('mode', 'month');
                                                            url.searchParams.set('month', selectedYear + '-{{ $num }}');
                                                            window.location.href = url.href;
                                                        "
                                    class="py-2 text-center text-[13px] rounded-lg transition-colors cursor-pointer" :class="{
                                                            'bg-[#0B266E] text-white font-bold shadow-md': selectedYear == {{ $currentYearNum }} && '{{ $num }}' == '{{ $currentMonthNum }}',
                                                            'text-gray-600 hover:bg-[#EFF6FF] hover:text-[#0B266E] hover:font-bold': !(selectedYear == {{ $currentYearNum }} && '{{ $num }}' == '{{ $currentMonthNum }}')
                                                        }">
                                    {{ $name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <a href="{{ request()->fullUrlWithQuery(['month' => $nextMonth]) }}"
                    class="inline-flex items-center justify-center gap-1.5 text-[13px] font-semibold px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg shadow-sm hover:bg-gray-50 hover:text-[#0B266E] hover:border-gray-300 transition-all min-w-[130px]">
                    Bulan Depan →
                </a>
            </div>

            <div class="mp-card overflow-hidden">
                <div class="mp-card-body" style="padding: 20px;">
                    <p class="text-[12px] text-gray-500 mb-4"><strong>Heatmap Aktivitas:</strong> Semakin gelap warnanya,
                        semakin banyak peminjaman di tanggal tersebut.</p>

                    {{-- Day-of-week header --}}
                    <div style="display:grid; grid-template-columns:repeat(7, 1fr); gap: 4px; margin-bottom: 4px;">
                        @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $dayLabel)
                            <div style="text-align:center; font-size:11px; font-weight:700; color:#6B7280; padding: 6px 0;">
                                {{ $dayLabel }}
                            </div>
                        @endforeach
                    </div>

                    {{-- Calendar Cells --}}
                    <div style="display:grid; grid-template-columns:repeat(7, 1fr); gap: 4px;">
                        @foreach($calendarDays as $cell)
                            @if($cell === null)
                                <div></div>
                            @else
                                @php
                                    $dateKey = $cell->format('Y-m-d');
                                    $count = $monthBookings[$dateKey] ?? 0;
                                    $isToday = $cell->isToday();
                                    $isPast = $cell->isPast() && !$isToday;

                                    $isHoliday = isset($holidays[$dateKey]);
                                    $isClosedWeekend = !$bukaAkhirPekan && $cell->isWeekend();
                                    $isClosed = $isHoliday || $isClosedWeekend;

                                    // Heatmap color by booking density
                                    if ($isClosed) {
                                        $cellBg = '#F3F4F6'; // Gray out
                                        $countClr = '#9CA3AF';
                                    } elseif ($count == 0) {
                                        $cellBg = '#F9FAFB';
                                        $countClr = '#9CA3AF';
                                    } elseif ($count <= 2) {
                                        $cellBg = '#D1FAE5';
                                        $countClr = '#059669';
                                    } elseif ($count <= 5) {
                                        $cellBg = '#FEF9C3';
                                        $countClr = '#B45309';
                                    } else {
                                        $cellBg = '#FEE2E2';
                                        $countClr = '#DC2626';
                                    }

                                    $weekLink = request()->fullUrlWithQuery(['mode' => 'week', 'week_start' => $cell->copy()->startOfWeek(\Carbon\Carbon::MONDAY)->format('Y-m-d')]);
                                @endphp
                                <a href="{{ $weekLink }}"
                                    title="{{ $cell->translatedFormat('d F Y') }}{{ $isHoliday ? ' (Libur: ' . $holidays[$dateKey] . ')' : '' }}"
                                    style="display:block; text-align:center; padding: 10px 6px; border-radius:8px; text-decoration:none;
                                                                                                                                                                                                                                                                                                                                              background: {{ $cellBg }}; border: {{ $isToday ? '2px solid #0B266E' : '1px solid #E5E7EB' }};
                                                                                                                                                                                                                                                                                                                                              transition: all 0.15s; {{ $isPast ? 'opacity:0.55;' : '' }}"
                                    onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'"
                                    onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none'">
                                    <div
                                        style="font-size:13px; font-weight:700; color: {{ $isToday ? '#0B266E' : ($isClosed ? '#9CA3AF' : '#111827') }};">
                                        {{ $cell->format('d') }}
                                    </div>
                                    @if($isHoliday)
                                        <div style="font-size:10px; font-weight:600; color:#DC2626; margin-top:3px;">
                                            Libur
                                        </div>
                                    @elseif($isClosedWeekend)
                                        <div style="font-size:10px; font-weight:600; color:#9CA3AF; margin-top:3px;">
                                            Tutup
                                        </div>
                                    @elseif($count > 0)
                                        <div style="font-size:10px; font-weight:600; color:{{ $countClr }}; margin-top:3px;">
                                            {{ $count }} booking
                                        </div>
                                    @endif
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-4 text-[12px] text-gray-500 font-medium">
                💡 <strong>Tips:</strong> Klik tanggal manapun untuk beralih ke tampilan <span
                    class="text-[#0B266E] font-bold">Mingguan</span> di minggu tersebut secara detail.
            </div>
        @endif


        <!-- Alpine.js Event Detail Modal -->
        <div x-data="{ open: false, title: '', pengguna: '', ruangan: '', tujuan: '', tanggal: '', waktu: '', type: '', telepon: '' }"
            @open-event-modal.window="
            title = $event.detail.title;
            pengguna = $event.detail.pengguna;
            ruangan = $event.detail.ruangan;
            tujuan = $event.detail.tujuan;
            tanggal = $event.detail.tanggal;
            waktu = $event.detail.waktu;
            type = $event.detail.type;
            telepon = $event.detail.telepon;
            open = true;
         ">
            <div x-show="open" style="display: none;"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">
                <div x-show="open" x-transition.opacity
                    class="fixed inset-0 transition-opacity bg-gray-500/20 backdrop-blur-md" @click="open = false">
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="open" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100 z-50">
                    
                    <div class="bg-white px-5 pt-5 pb-6 sm:p-6 sm:pb-5">
                        <div class="text-[#0B266E] font-extrabold text-[15px] mb-1 text-center w-full pb-3 border-b border-gray-100" x-text="tujuan || title"></div>
                        
                        <div class="w-full mt-3 space-y-2">
                            <div class="flex justify-between items-center text-[12px]" x-show="type">
                                <span class="text-gray-500 font-medium">Jenis Kegiatan</span>
                                <span class="text-gray-800 font-bold" x-text="type"></span>
                            </div>
                            <div class="flex justify-between items-center text-[12px]">
                                <span class="text-gray-500 font-medium">Ruangan</span>
                                <span class="text-gray-800 font-bold" x-text="ruangan"></span>
                            </div>
                            <div class="flex justify-between items-center text-[12px]">
                                <span class="text-gray-500 font-medium">Tanggal</span>
                                <span class="text-gray-800 font-bold" x-text="tanggal"></span>
                            </div>
                            <div class="flex justify-between items-center text-[12px]">
                                <span class="text-gray-500 font-medium">Waktu</span>
                                <span class="text-gray-800 font-bold" x-text="waktu"></span>
                            </div>
                            <div class="flex justify-between items-center text-[12px]">
                                <span class="text-gray-500 font-medium">Penanggung Jawab</span>
                                <span class="text-gray-800 font-bold" x-text="pengguna"></span>
                            </div>
                            <div class="flex justify-between items-center text-[12px]" x-show="telepon && telepon !== '-'">
                                <span class="text-gray-500 font-medium">Kontak</span>
                                <span class="text-gray-800 font-bold" x-text="telepon"></span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-100 sm:flex sm:flex-row-reverse">
                        <button type="button" @click="open = false" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-[#0B266E] text-sm font-bold text-white hover:bg-[#091F5E] focus:outline-none transition-colors sm:w-auto sm:text-[13px] cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- =================== JALUR TOL MODAL (Alpine) =================== --}}
        <div x-data="{
                show: false,
                ruangan_id: '',
                ruangan_nama: '',
                tanggal: '',
                jam: '',
                modeAction: 'internal',
                kategoriType: 'Maintenance / Perbaikan',
                keterangan: '',
                nim: '',
                jam_selesai: '',
                searchQuery: '',
                isSearching: false,
                suggestions: [],
                searchUsers() {
                    if (this.searchQuery.length < 2) {
                        this.suggestions = [];
                        return;
                    }
                    this.isSearching = true;
                    fetch(`{{ route('eoffice.peminjaman.admin.kalender-global.search-users') }}?q=${encodeURIComponent(this.searchQuery)}`)
                        .then(res => res.json())
                        .then(data => {
                            this.suggestions = data;
                            this.isSearching = false;
                        }).catch(() => { this.isSearching = false; });
                },
                selectUser(user) {
                    this.nim = user.external_id || user.email;
                    this.searchQuery = user.name + ' (' + this.nim + ')';
                    this.suggestions = [];
                }
            }" @open-jalur-tol.window="
                show = true;
                ruangan_id = $event.detail.ruangan_id;
                ruangan_nama = $event.detail.ruangan_nama;
                tanggal = $event.detail.tanggal;
                jam = $event.detail.jam;
                jam_selesai = $event.detail.jam_selesai || (parseInt(jam.split(':')[0]) + 1).toString().padStart(2, '0') + ':00';
                searchQuery = '';
                nim = '';
                suggestions = [];
            " x-init="$watch('searchQuery', value => { 
                if(nim && !value.includes(nim)) nim = ''; 
            })">
            <div x-show="show" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6 text-left whitespace-normal"
                aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;" x-cloak>

                {{-- Backdrop --}}
                <div x-show="show" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/60 transition-opacity" aria-hidden="true"
                    @click="show = false">
                </div>

                <div x-show="show" x-transition:enter="ease-out duration-300"
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
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-[#1A1C1E] tracking-tight" id="modal-title">Input Peminjaman</h3>
                                    <p class="text-[10px] text-[#0B266E] font-medium">Mengunci penjadwalan paksa untuk <span class="font-bold" x-text="ruangan_nama"></span> pada <span class="font-bold" x-text="tanggal + ' pukul ' + jam + ' WIB'"></span></p>
                                </div>
                            </div>
                            <button type="button" @click="show = false"
                                class="text-[#0B266E] hover:text-[#091F5E] hover:bg-[#0B266E]/10 transition-colors w-8 h-8 flex items-center justify-center shrink-0 rounded-lg cursor-pointer">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <form id="expressBookingForm" method="POST" action="{{ route('eoffice.peminjaman.admin.kalender-global.express') }}" class="flex flex-col flex-1 min-h-0" autocomplete="off">
                            @csrf
                            <input type="hidden" name="ruangan_id" x-model="ruangan_id">
                            <input type="hidden" name="tanggal" x-model="tanggal">
                            <input type="hidden" name="jam_mulai" x-model="jam">

                            {{-- Scrollable Body --}}
                            <div class="space-y-4 overflow-y-auto overflow-x-hidden flex-1 bg-white whitespace-normal -mx-4 sm:-mx-6 px-4 sm:px-6 pb-2">
                                <div class="space-y-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[10px] font-black text-[#0B266E] uppercase tracking-widest">Mode Tindakan</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="border rounded-xl p-3 cursor-pointer transition-colors"
                                            :class="modeAction === 'internal' ? 'bg-blue-50 border-blue-400 ring-2 ring-blue-100' : 'bg-white border-slate-200 hover:bg-slate-50'">
                                            <input type="radio" name="tipe_aksi" value="internal" x-model="modeAction"
                                                class="hidden">
                                            <div class="font-bold text-sm"
                                                :class="modeAction === 'internal' ? 'text-blue-800' : 'text-gray-700'">
                                                Jadwal Internal</div>
                                            <div class="text-[10px] text-gray-500 mt-1">Blokir Kuliah / Maintenance
                                            </div>
                                        </label>
                                        <label class="border rounded-xl p-3 cursor-pointer transition-colors"
                                            :class="modeAction === 'dosen' ? 'bg-emerald-50 border-emerald-400 ring-2 ring-emerald-100' : 'bg-white border-slate-200 hover:bg-slate-50'">
                                            <input type="radio" name="tipe_aksi" value="dosen" x-model="modeAction"
                                                class="hidden">
                                            <div class="font-bold text-sm"
                                                :class="modeAction === 'dosen' ? 'text-emerald-800' : 'text-gray-700'">
                                                Peminjaman Manual</div>
                                            <div class="text-[10px] text-gray-500 mt-1">Peminjaman langsung disetujui
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="space-y-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[10px] font-black text-[#0B266E] uppercase tracking-widest">Waktu & Tanggal</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-1.5">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Jam Mulai</label>
                                            <input type="time" x-model="jam" required
                                                class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all text-gray-800">
                                        </div>
                                        <div class="space-y-1.5">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Jam Selesai
                                                <span class="text-red-500">*</span></label>
                                            <input type="time" name="jam_selesai" x-model="jam_selesai"
                                                class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all text-gray-800"
                                                required>
                                        </div>
                                    </div>
                                </div>

                                <div x-show="modeAction === 'internal'" style="display:none;" class="space-y-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[10px] font-black text-[#0B266E] uppercase tracking-widest">Kategori & Detail</span>
                                    </div>
                                    <div class="space-y-1.5 relative z-50">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kategori
                                            <span class="text-red-500">*</span></label>
                                        <div x-data="{ 
                                                open: false,
                                                get selectedName() {
                                                    const map = {
                                                        'Pindah Kelas': 'Pindah / Pengganti Kelas',
                                                        'Maintenance / Perbaikan': 'Maintenance / Perbaikan Ruangan',
                                                        'Sterilisasi Ruangan': 'Sterilisasi / Persiapan Ruangan',
                                                        'Penutupan Khusus': 'Penutupan Khusus / Libur Nasional',
                                                        'Ujian / Evaluasi': 'Ujian / Evaluasi (UTS/UAS)',
                                                        'Lainnya': 'Lainnya...'
                                                    };
                                                    return map[kategoriType] || 'Pilih Kategori...';
                                                },
                                                selectItem(val) { 
                                                    kategoriType = val;
                                                    this.open = false; 
                                                } 
                                            }" class="relative w-full" :class="{'z-50': open, 'z-[1]': !open}"
                                            @click.away="open = false">

                                            <input type="hidden" name="kategori" :value="kategoriType" :required="modeAction === 'internal'">

                                            <button type="button" @click="open = !open"
                                                class="w-full flex items-center justify-between bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all h-[42px]">
                                                <span x-text="selectedName" class="truncate"
                                                    :class="{'text-gray-400': !kategoriType, 'text-gray-800': kategoriType}"></span>
                                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0"
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
                                                class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg z-[60] overflow-y-auto max-h-48"
                                                style="display: none;">
                                                <div class="p-1 flex flex-col">
                                                    <button type="button" @click="selectItem('Pindah Kelas')" class="w-full text-left px-3 py-2 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Pindah Kelas', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Pindah Kelas'}">Pindah / Pengganti Kelas</button>
                                                    <button type="button" @click="selectItem('Maintenance / Perbaikan')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Maintenance / Perbaikan', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Maintenance / Perbaikan'}">Maintenance / Perbaikan Ruangan</button>
                                                    <button type="button" @click="selectItem('Sterilisasi Ruangan')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Sterilisasi Ruangan', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Sterilisasi Ruangan'}">Sterilisasi / Persiapan Ruangan</button>
                                                    <button type="button" @click="selectItem('Penutupan Khusus')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Penutupan Khusus', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Penutupan Khusus'}">Penutupan Khusus / Libur Nasional</button>
                                                    <button type="button" @click="selectItem('Ujian / Evaluasi')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Ujian / Evaluasi', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Ujian / Evaluasi'}">Ujian / Evaluasi (UTS/UAS)</button>
                                                    <button type="button" @click="selectItem('Lainnya')" class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors" :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Lainnya', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Lainnya'}">Lainnya...</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Academic Metadata Panel (Dynamic) -->
                                <div x-show="kategoriType === 'Jadwal Akademik (Kuliah)' && modeAction === 'internal'"
                                    style="display:none;"
                                    class="bg-blue-50/50 border border-blue-100 p-4 rounded-2xl space-y-4">
                                    <label class="block text-[10px] uppercase tracking-widest font-black text-blue-800 mb-1.5 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Metadata Akademik Tambahan <span class="text-blue-500 font-medium normal-case tracking-normal">(Opsional)</span>
                                    </label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-1.5">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mata Kuliah</label>
                                            <input type="text" name="mata_kuliah" :required="kategoriType === 'Jadwal Akademik (Kuliah)'"
                                                class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all" placeholder="Nama Lengkap Matkul">
                                        </div>
                                        <div class="space-y-1.5">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kode MK</label>
                                            <input type="text" name="kode_mk" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all" placeholder="Contoh: TKK102">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-3 gap-4">
                                        <div class="space-y-1.5">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kelas</label>
                                            <input type="text" name="kelas" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all" placeholder="Misal: A">
                                        </div>
                                        <div class="space-y-1.5">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">SKS</label>
                                            <input type="number" name="sks" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all" placeholder="0-4">
                                        </div>
                                        <div class="space-y-1.5">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Kuota</label>
                                            <input type="number" name="kuota" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all" placeholder="Kuota">
                                        </div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Dosen Pengampu</label>
                                        <input type="text" name="pengampu" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all" placeholder="Dosen Pengampu Mata Kuliah">
                                    </div>
                                </div>

                                <div x-show="modeAction === 'dosen'" class="space-y-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl" style="display:none;">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[10px] font-black text-[#0B266E] uppercase tracking-widest">Target Peminjam</span>
                                    </div>
                                    <div class="space-y-1.5 relative">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama atau Email Target <span class="text-red-500">*</span></label>
                                        <!-- HIDDEN ACTUAL INPUT -->
                                        <input type="hidden" name="nim" x-model="nim">
                                        <!-- SEARCH INPUT -->
                                        <input type="text" x-model="searchQuery" @input.debounce.500ms="searchUsers"
                                            placeholder="Ketik nama atau email peminjam..." class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all"
                                            autocomplete="off" :required="modeAction === 'dosen'">

                                        <!-- LOADING SPINNER -->
                                        <div x-show="isSearching" class="absolute right-3 top-8 text-primary-500">
                                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                        </div>

                                        <!-- DROPDOWN SUGGESTIONS -->
                                        <ul x-show="suggestions.length > 0" @click.away="suggestions = []"
                                            class="absolute z-[100] w-full bg-white mt-1 border border-gray-200 rounded-md shadow-lg max-h-48 overflow-y-auto"
                                            style="display: none;">
                                            <template x-for="user in suggestions" :key="user.id">
                                                <li @click="selectUser(user)" class="px-4 py-2.5 hover:bg-primary-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                                    <div class="font-bold text-sm text-gray-800" x-text="user.name"></div>
                                                    <div class="flex items-center gap-2 mt-0.5 text-[11px] font-medium text-gray-500">
                                                        <span x-text="user.external_id || 'N/A'"></span>
                                                        <span class="text-gray-300">&bull;</span>
                                                        <span x-text="user.email"></span>
                                                    </div>
                                                </li>
                                            </template>
                                        </ul>

                                        <p class="text-[10px] text-gray-400 mt-1">Sistem akan melakukan autorisasi instan, peminjaman ini akan dikunci dan langsung berstatus 'Disetujui'.</p>
                                    </div>
                                </div>

                                <div class="space-y-3 p-4 bg-slate-50 border border-slate-100 rounded-2xl">
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama Kegiatan <span class="text-red-500">*</span></label>
                                        <input type="text" name="keterangan" x-model="keterangan" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2.5 text-xs font-medium focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all"
                                            placeholder="Misal: Kuliah Pengganti / Rapat Evaluasi..." required>
                                    </div>
                                </div>

                                <!-- Mobile Footer (Inside scrollable area so it doesn't follow keyboard) -->
                                <div class="sm:hidden mt-6 pt-4 pb-2 border-t border-slate-100 flex items-center justify-end gap-3">
                                    <button type="button" @click="show = false"
                                    class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-center cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="px-6 py-2.5 text-sm font-semibold text-white bg-[#0B266E] rounded-xl hover:bg-[#091F5E] focus:outline-none focus:ring-2 focus:ring-[#0B266E]/50 shadow-lg shadow-[#0B266E]/20 transition-all text-center flex items-center justify-center cursor-pointer">
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Desktop Sticky Footer -->
                    <div class="hidden sm:flex mt-5 items-center justify-end gap-3 shrink-0">
                        <button type="button" @click="show = false"
                            class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all text-center cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" form="expressBookingForm"
                            class="px-6 py-2.5 text-sm font-semibold text-white bg-[#0B266E] rounded-xl hover:bg-[#091F5E] focus:outline-none focus:ring-2 focus:ring-[#0B266E]/50 shadow-lg shadow-[#0B266E]/20 transition-all text-center flex items-center justify-center cursor-pointer">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        // AJAX Polling setiap 30 detik untuk update Real-time
        document.addEventListener('DOMContentLoaded', () => {
            setInterval(() => {
                const kalenderDiv = document.querySelector('[x-data="bookingKalender()"]');
                if (kalenderDiv && kalenderDiv.__x) {
                    const data = Alpine.$data(kalenderDiv);
                    // Jangan update UI jika admin sedang berinteraksi dengan modal atau dragging
                    if (data && (data.showModal || data.showDetailModal || data.showBlockModal || data.isDragging)) return;
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
                        // Simpan posisi scroll sebelum replace
                        let scrollWrapper = currentContainer.querySelector('div[style*="overflow-x"]');
                        let currentScroll = scrollWrapper ? scrollWrapper.scrollLeft : 0;

                        currentContainer.innerHTML = newContainer.innerHTML;

                        // Kembalikan posisi scroll setelah replace
                        let newScrollWrapper = currentContainer.querySelector('div[style*="overflow-x"]');
                        if (newScrollWrapper) {
                            newScrollWrapper.scrollLeft = currentScroll;
                        }
                    }
                })
                .catch(err => console.error('Polling error:', err));
            }, 30000);
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('bookingKalender', () => ({
                isDragging: false,
                dragStartPoint: null,
                dragSelection: [],
                hoverCol: null,
                
                // Absolute Drag properties
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

                // Absolute Drag Methods
                startDragAbsolute(e, roomId, roomName, date, bukaInt, totalHours, eventBounds = []) {
                    if (e.button !== 0) return; // Hanya klik kiri
                    
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
                        
                        // Dispatch event for Admin Modal
                        window.dispatchEvent(new CustomEvent('open-jalur-tol', {
                            detail: {
                                ruangan_id: this.dragRoom,
                                ruangan_nama: this.dragRoomName,
                                tanggal: this.dragDate,
                                jam: startHStr,
                                jam_selesai: endHStr
                            }
                        }));
                    }
                    this.isDragging = false;
                    this.dragRoom = null;
                }
            }));
        });
    </script>
</x-eoffice::manajemen-ruangan.layout>