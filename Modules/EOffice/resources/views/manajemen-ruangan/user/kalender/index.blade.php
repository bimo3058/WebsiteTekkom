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
                <p class="mp-page-sub">Lihat ketersediaan seluruh ruangan dan langsung booking slot yang kosong.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                {{-- Room Filter --}}
                <form id="roomFilterForm" method="GET" action="{{ route('eoffice.peminjaman.user.kalender') }}"
                    class="flex items-center">
                    <input type="hidden" name="mode" value="{{ $mode }}">
                    @if($mode === 'week') <input type="hidden" name="week_start"
                    value="{{ $weekStart->format('Y-m-d') }}"> @endif
                    @if($mode === 'month') <input type="hidden" name="month" value="{{ $monthDate->format('Y-m') }}">
                    @endif

                    <div x-data="{
                            open: false,
                            selectedCat: '{{ $selectedKategori }}',
                            selectedRoomId: '{{ $selectedRoomId }}',
                            selectedRoomName: '{{ $selectedRoomId ? addslashes($allRuangansDaftar->firstWhere('id', $selectedRoomId)->nama ?? 'Semua ' . $selectedKategori) : 'Semua ' . $selectedKategori }}',
                            rooms: {{ $allRuangansDaftar->map(fn($r) => ['id' => $r->id, 'nama' => $r->nama, 'kategori' => $r->kategori])->toJson() }},
                            categories: {{ $kategoriList->toJson() }},
                            selectCat(cat) {
                                this.selectedCat = cat;
                                this.selectedRoomId = '';
                                this.selectedRoomName = 'Semua ' + cat;
                                $refs.ruanganInput.value = '';
                                $refs.kategoriInput.value = cat;
                                document.getElementById('roomFilterForm').submit();
                            },
                            selectRoom(id, name, cat) {
                                this.selectedCat = cat;
                                this.selectedRoomId = id;
                                this.selectedRoomName = name;
                                $refs.ruanganInput.value = id;
                                $refs.kategoriInput.value = cat;
                                document.getElementById('roomFilterForm').submit();
                            }
                        }" class="relative w-56 sm:w-64" @click.away="open = false">

                        <input type="hidden" name="ruangan_id" x-ref="ruanganInput" :value="selectedRoomId">
                        <input type="hidden" name="kategori" x-ref="kategoriInput" :value="selectedCat">

                        <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between py-1.5 px-3 text-[13px] font-medium bg-white border border-gray-300 rounded-lg shadow-sm hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0B266E]/20 transition-all cursor-pointer">
                            <span x-text="selectedRoomId ? selectedRoomName : 'Kategori: ' + selectedCat"
                                class="truncate pr-2 text-gray-800"></span>
                            <svg class="w-4 h-4 text-gray-500 transition-transform duration-200 shrink-0"
                                :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 top-full mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-[0_8px_30px_rgb(0,0,0,0.12)] z-50 max-h-60 overflow-y-auto"
                            style="display: none;">
                            <div class="p-1.5">
                                <template x-for="cat in categories" :key="cat">
                                    <div class="mb-1">
                                        <!-- Category Header -->
                                        <button type="button" @click="selectCat(cat)"
                                            class="w-full text-left px-2 py-1.5 rounded-md text-[13px] font-bold transition-colors cursor-pointer flex items-center gap-2"
                                            :class="{'bg-[#EFF6FF] text-[#0B266E]': selectedCat === cat && !selectedRoomId, 'text-gray-800 hover:bg-gray-50': !(selectedCat === cat && !selectedRoomId)}">
                                            <span x-text="getIcon(cat)"></span>
                                            <span x-text="'Kategori: ' + cat"></span>
                                        </button>
                                        <!-- Rooms in Category -->
                                        <div class="pl-6 border-l border-gray-100 ml-3 my-0.5 space-y-0.5">
                                            <template x-for="r in rooms.filter(room => room.kategori === cat)"
                                                :key="r.id">
                                                <button type="button" @click="selectRoom(r.id, r.nama, cat)"
                                                    class="w-full text-left px-2 py-1.5 rounded-md text-[12px] font-medium transition-colors cursor-pointer"
                                                    :class="{'bg-[#0B266E] text-white': selectedRoomId == r.id, 'text-gray-600 hover:bg-gray-50': selectedRoomId != r.id}">
                                                    - <span x-text="r.nama"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </template>
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
    <div x-data="bookingKalender()" @mouseup.window="stopDrag()">

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
                        class="flex items-center gap-1 md:gap-2 text-[12px] md:text-[15px] font-bold text-[#0B266E] hover:bg-[#EFF6FF] px-1 md:px-4 py-1.5 rounded-lg transition-colors cursor-pointer border border-transparent hover:border-[#0B266E]/20 text-center">
                        {{ $weekStart->translatedFormat('d M') }} — {{ $weekEnd->translatedFormat('d M Y') }}
                        <svg class="w-3.5 h-3.5 md:w-4 md:h-4 text-[#0B266E] transition-transform duration-200 shrink-0"
                            :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                                    $isOwn = ($ev['user_id'] ?? null) === auth()->id();
                                                    $tujuan = $ev['tujuan'];
                                                    
                                                    if ($status === 'disetujui') {
                                                        $bg = 'rgba(237, 233, 254, 0.9)'; $border = '#C4B5FD'; $label = 'Terpakai'; $tColor = '#5B21B6';
                                                        $onClick = "openDetailModal('Terpakai', '-', '".addslashes($ruang->nama)."', '".$day->translatedFormat('l, d M Y')."', '{$ev['jam_mulai']} - {$ev['jam_selesai']}', 'terpakai', '')";
                                                    } elseif ($status === 'internal') {
                                                        if ($type === 'Jadwal Akademik (Kuliah)' || $type === 'Pindah Kelas' || $type === 'Pindah / Pengganti Kelas') {
                                                            $bg = 'rgba(219, 234, 254, 0.9)'; $border = '#60A5FA'; $tColor = '#1E40AF';
                                                        } elseif ($type === 'Ujian / Evaluasi (UTS/UAS)' || $type === 'Lainnya...') {
                                                            $bg = 'rgba(237, 233, 254, 0.9)'; $border = '#C4B5FD'; $tColor = '#5B21B6';
                                                        } else {
                                                            $bg = 'rgba(254, 226, 226, 0.9)'; $border = '#F87171'; $tColor = '#991B1B';
                                                        }
                                                        $cleanTujuan = trim(str_ireplace(['digunakan untuk', ' - Kelas ', ' (Kelas ', ')'], ['', '-', '-', ''], $tujuan));
                                                        $label = $cleanTujuan ?: 'Jadwal Kuliah';
                                                        $matkul = $label; $kelas = '-';
                                                        if (strpos($label, '-') !== false) {
                                                            $parts = explode('-', $label);
                                                            $kelas = trim(array_pop($parts));
                                                            $matkul = trim(implode('-', $parts));
                                                        }
                                                        $onClick = "openDetailModal('".addslashes($matkul)."', '".addslashes($kelas)."', '".addslashes($ruang->nama)."', '".$day->translatedFormat('l, d M Y')."', '{$ev['jam_mulai']} - {$ev['jam_selesai']}', 'internal', '')";
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
                        class="absolute left-1/2 -translate-x-1/2 top-full mt-2 w-[280px] bg-white border border-gray-200 rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] z-50 p-4"
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
            class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center p-0 pt-10 sm:p-6" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">

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
                class="relative bg-white rounded-t-[24px] sm:rounded-t-[20px] rounded-b-none sm:rounded-b-[20px] shadow-2xl w-full max-w-xl max-h-[95vh] sm:max-h-[90vh] flex flex-col overflow-hidden">

                {{-- Header --}}
                <div class="px-4 sm:px-6 py-4 border-b border-[#0B266E]/10 flex items-center justify-between bg-[#0B266E]/[0.06] flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="bg-[#0B266E] p-2.5 rounded-xl shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-[#1A1C1E]" id="modal-title">Form Booking Ruangan</h3>
                            <p class="text-xs text-[#0B266E] mt-0.5">Meminjam <span class="font-bold" x-text="bookingData.roomName"></span> pada <span class="font-bold" x-text="bookingData.waktu"></span></p>
                        </div>
                    </div>
                    <button type="button" @click="closeModal"
                        class="text-[#0B266E] hover:text-[#091F5E] hover:bg-[#0B266E]/10 transition-colors p-1.5 rounded-lg shrink-0 cursor-pointer">
                        <span class="sr-only">Close menu</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Scrollable Body --}}
                <div class="px-4 sm:px-6 md:px-8 pt-3 pb-4 overflow-y-auto flex-1 bg-white">
                    <form id="bookingForm" method="POST" action="{{ route('eoffice.peminjaman.user.booking.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="ruangan_id" :value="selectedRoomId">
                        <input type="hidden" name="tanggal_pinjam" :value="selectedDate">
                        
                        <div class="grid grid-cols-2 gap-5 mt-0">
                            <div>
                                <label class="block text-[12px] font-semibold text-gray-700 mb-1.5">Jam Mulai <span class="text-red-500">*</span></label>
                                <input type="time" name="jam_mulai" x-model="bookingData.jamMulai" required class="mp-input text-[13px] font-bold w-full">
                            </div>
                            <div>
                                <label class="block text-[12px] font-semibold text-gray-700 mb-1.5">Jam Selesai <span class="text-red-500">*</span></label>
                                <input type="time" name="jam_selesai" x-model="bookingData.jamSelesai" required class="mp-input text-[13px] font-bold w-full">
                            </div>
                        </div>


                        <div class="space-y-3 mt-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[12px] font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                                    <input type="text" class="mp-input w-full bg-gray-50/80 text-gray-600 font-medium border-gray-200"
                                        value="{{ $user->name ?? 'Student' }}" readonly>
                                </div>
                                <div>
                                    <label class="block text-[12px] font-semibold text-gray-700 mb-1.5">NIM / NIP</label>
                                    <input type="text" class="mp-input w-full bg-gray-50/80 text-gray-600 font-medium border-gray-200"
                                        value="{{ $nim ?? '00000' }}" readonly>
                                </div>
                            </div>

                            <div>
                                <label class="block text-[12px] font-semibold text-gray-700 mb-1.5">No. Telepon / WhatsApp <span class="text-red-500">*</span></label>
                                <input type="text" name="nomor_telepon" class="mp-input w-full cursor-text"
                                    value="{{ $phone ?? '' }}" required maxlength="20" placeholder="Contoh: 08123456789">
                            </div>

                            <div>
                                <label class="block text-[12px] font-semibold text-gray-700 mb-1.5">Nama Kegiatan <span class="text-red-500">*</span></label>
                                <input type="text" name="tujuan" class="mp-input w-full cursor-text"
                                    placeholder="Misal: Rapat Kerja HIMASKOM" required maxlength="150">
                            </div>

                            <div class="bg-gray-50 border border-gray-200 p-4 md:p-5 rounded-[12px]">
                                <label class="block text-[13px] font-bold text-gray-800 mb-2 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    File Berkas Proposal <span class="text-gray-500 font-normal ml-1">(Opsional)</span>
                                </label>
                                <input type="file" name="file_berkas" accept=".pdf" class="block w-full text-[13px] text-slate-500
                                    file:mr-4 file:py-2.5 file:px-4
                                    file:rounded-xl file:border-0
                                    file:text-[12px] file:font-semibold
                                    file:bg-blue-50 file:text-[#0B266E]
                                    hover:file:bg-blue-100
                                    border border-slate-200 rounded-xl p-1
                                    cursor-pointer transition-all outline-none hover:border-[#0B266E]/40">
                                <p class="text-[11px] text-gray-500 mt-2.5 leading-relaxed">Format <strong>PDF</strong> maksimal <strong>2MB</strong>. Biasanya diperlukan untuk persetujuan acara berskala besar atau formal.</p>
                                @error('file_berkas')
                                    <p class="text-[11px] text-rose-600 mt-2 font-bold flex items-center gap-1.5 bg-rose-50 p-2 rounded-md border border-rose-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Ukuran maksimal 2MB dan wajib berformat PDF.
                                    </p>
                                @enderror
                            </div>

                            {{-- Persetujuan S&K --}}
                            <div class="pt-1">
                                <label class="flex items-start gap-3.5 cursor-pointer group bg-blue-50/50 p-3.5 md:p-4 rounded-[12px] border border-blue-100/50 hover:bg-blue-50 transition-colors">
                                    <div class="flex items-center h-5 mt-0.5 shrink-0">
                                        <input type="checkbox" name="syarat_ketentuan" required
                                            class="w-4 h-4 border border-blue-300 rounded bg-white text-[#0B266E] focus:ring-[#0B266E] focus:ring-2 transition-all cursor-pointer shadow-sm"
                                            style="scroll-margin-bottom: 80px; scroll-margin-top: 120px;">
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[12px] text-gray-600 leading-relaxed block">
                                            Saya setuju dan bersedia <strong class="text-gray-900 font-bold">merapikan kembali ruangan</strong> setelah digunakan serta siap <strong class="text-gray-900 font-bold">bertanggung jawab penuh mengganti kerusakan</strong> barang atau fasilitas akibat kelalaian selama peminjaman.
                                        </span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Footer / Actions --}}
                <div class="bg-white px-4 sm:px-6 py-4 border-t border-gray-100 flex gap-3 flex-shrink-0 mt-auto pb-6 sm:pb-4">
                    <button type="button" @click="closeModal"
                        class="flex-1 px-4 py-3 sm:py-2.5 border border-slate-200 text-slate-600 font-semibold rounded-xl hover:bg-slate-50 transition-all text-sm cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" form="bookingForm"
                        class="flex-1 px-4 py-3 sm:py-2.5 bg-[#0B266E] hover:bg-[#091F5E] text-white font-semibold rounded-xl transition text-sm flex items-center justify-center gap-2 cursor-pointer shadow-sm">
                        Kirim Pengajuan
                    </button>
                </div>
            </div>
        </div>

    </div> <!-- Close Alpine Wrapper -->

    <script src="//unpkg.com/alpinejs" defer></script>
    <script>
        // AJAX Polling setiap 30 detik untuk update Real-time
        document.addEventListener('DOMContentLoaded', () => {
            setInterval(() => {
                const kalenderDiv = document.querySelector('[x-data="bookingKalender()"]');
                if (kalenderDiv && kalenderDiv.__x) {
                    const data = Alpine.$data(kalenderDiv);
                    // Jangan update UI jika user sedang berinteraksi dengan modal atau dragging
                    if (data && (data.showModal || data.showDetailModal || data.isDragging)) return;
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
                            let scrollWrapper = currentContainer.querySelector('#table-scroll-container');
                            let currentScroll = scrollWrapper ? scrollWrapper.scrollLeft : 0;

                            currentContainer.innerHTML = newContainer.innerHTML;

                            // Kembalikan posisi scroll setelah replace
                            let newScrollWrapper = currentContainer.querySelector('#table-scroll-container');
                            if (newScrollWrapper) {
                                newScrollWrapper.scrollLeft = currentScroll;
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