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

        // Group weekly bookings by [date][ruangan_id] => list of slots
        $slotMap = [];
        foreach ($bookingsRaw as $b) {
            $mulai = (int) \Carbon\Carbon::parse($b->jam_mulai)->format('H');
            $selesaiCarbon = \Carbon\Carbon::parse($b->jam_selesai);
            $selesai = (int) $selesaiCarbon->format('H');
            if ($selesaiCarbon->format('i') > 0 || $selesaiCarbon->format('s') > 0) {
                $selesai += 1;
            }
            $tgl = is_string($b->tanggal_pinjam) ? $b->tanggal_pinjam : $b->tanggal_pinjam->format('Y-m-d');
            for ($h = $mulai; $h < $selesai; $h++) {
                $slotMap[$tgl][$b->ruangan_id][$h] = [
                    'id' => 'pm_' . $b->id,
                    'status' => $b->status,
                    'tujuan' => $b->tujuan ?? '',
                    'pengguna' => $b->user->name ?? 'Mahasiswa',
                    'user_id' => $b->user_id,
                ];
            }
        }

        // Parse and superimpose MrJadwalInternal events (Blocks entire slot)
        foreach ($internalSchedules as $j) {
            $mulai = (int) \Carbon\Carbon::parse($j->jam_mulai)->format('H');
            $selesaiCarbon = \Carbon\Carbon::parse($j->jam_selesai);
            $selesai = (int) $selesaiCarbon->format('H');
            if ($selesaiCarbon->format('i') > 0 || $selesaiCarbon->format('s') > 0) {
                $selesai += 1;
            }

            if ($j->tipe_jadwal === 'spesifik') {
                $tgl = \Carbon\Carbon::parse($j->tanggal_spesifik)->format('Y-m-d');
                for ($h = $mulai; $h < $selesai; $h++) {
                    $slotMap[$tgl][$j->ruangan_id][$h] = [
                        'id' => 'it_' . $j->id,
                        'status' => 'internal',
                        'type' => $j->kategori ?? 'Agenda Internal',
                        'tujuan' => $j->keterangan ?? ''
                    ];
                }
            } else if ($j->tipe_jadwal === 'rutin') {
                foreach ($weekDays as $day) {
                    if ($day->dayOfWeekIso == $j->hari) {
                        $tgl = $day->format('Y-m-d');

                        if (!empty($j->tgl_mulai_efektif) && $tgl < $j->tgl_mulai_efektif)
                            continue;
                        if (!empty($j->tgl_selesai_efektif) && $tgl > $j->tgl_selesai_efektif)
                            continue;

                        for ($h = $mulai; $h < $selesai; $h++) {
                            $slotMap[$tgl][$j->ruangan_id][$h] = [
                                'id' => 'it_' . $j->id,
                                'status' => 'internal',
                                'type' => $j->kategori ?? 'Jadwal Akademik (Kuliah)',
                                'tujuan' => $j->keterangan ?? ''
                            ];
                        }
                    }
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
                $cellMatrix = [];
                foreach ($weekDays as $day) {
                    foreach ($ruangans as $ruang) {
                        $dateStr = $day->format('Y-m-d');
                        $rId = $ruang->id;
                        $hourStatuses = [];
                        foreach ($jamList as $hIndex => $jam) {
                            $slotData = $slotMap[$dateStr][$rId][$jam] ?? ['status' => 'tersedia', 'tujuan' => '', 'id' => null, 'pengguna' => ''];
                            $slotStatus = $slotData['status'];
                            $tujuan = $slotData['tujuan'];
                            $pengguna = $slotData['pengguna'] ?? '';
                            $type = $slotData['type'] ?? '';
                            $id = $slotData['id'] ?? null;

                            $isPastDay = $day->isPast() && !$day->isToday();
                            $isPastHourToday = $day->isToday() && ($jam + 1) <= (int) now()->format('H');
                            $isPast = $isPastDay || $isPastHourToday;
                            $isHoliday = isset($holidays[$dateStr]);
                            $isWeekend = $day->isWeekend();
                            $isClosedWeekend = !$bukaAkhirPekan && $isWeekend;
                            $minDate = \Carbon\Carbon::today()->addDays($batasHMinBooking);
                            $isTooEarly = $day->copy()->startOfDay()->lt($minDate);
                            $jamStr = str_pad($jam, 2, '0', STR_PAD_LEFT) . ':00';
                            $isOutOfHours = ($jamStr < $jamBuka) || ($jamStr >= $jamTutup);

                            if ($isPast || $isClosedWeekend || $isOutOfHours)
                                $fKey = 'tutup';
                            elseif ($slotStatus === 'disetujui')
                                $fKey = 'penuh';
                            elseif ($slotStatus === 'internal')
                                $fKey = 'internal';
                            elseif ($slotStatus === 'menunggu')
                                $fKey = 'menunggu';
                            elseif ($isHoliday)
                                $fKey = 'libur';
                            elseif ($isTooEarly)
                                $fKey = 'too_early';
                            else
                                $fKey = 'tersedia';

                            $hourStatuses[$jam] = ['st' => $fKey, 'tujuan' => $tujuan, 'id' => $id, 'pengguna' => $pengguna, 'type' => $type];
                        }
                        $skipCount = 0;
                        foreach ($jamList as $hIndex => $jam) {
                            if ($skipCount > 0) {
                                $cellMatrix[$dateStr][$rId][$jam] = ['skip' => true];
                                $skipCount--;
                                continue;
                            }
                            $stObj = $hourStatuses[$jam];
                            $rowspan = 1;
                            if ($stObj['st'] === 'penuh' || $stObj['st'] === 'internal' || $stObj['st'] === 'menunggu') {
                                for ($k = $hIndex + 1; $k < count($jamList); $k++) {
                                    $nextStObj = $hourStatuses[$jamList[$k]];
                                    if ($nextStObj['st'] === $stObj['st'] && $nextStObj['id'] === $stObj['id']) {
                                        $rowspan++;
                                    } else {
                                        break;
                                    }
                                }
                            } elseif (in_array($stObj['st'], ['tutup', 'libur', 'too_early'])) {
                                for ($k = $hIndex + 1; $k < count($jamList); $k++) {
                                    $nextStObj = $hourStatuses[$jamList[$k]];
                                    if ($nextStObj['st'] === $stObj['st']) {
                                        $rowspan++;
                                    } else {
                                        break;
                                    }
                                }
                            }
                            $cellMatrix[$dateStr][$rId][$jam] = [
                                'skip' => false,
                                'rowspan' => $rowspan,
                                'type' => $stObj['type'] ?? ''
                            ];
                            $skipCount = $rowspan - 1;
                        }
                    }
                }
            @endphp

            @php $minTWidth = 64 + (7 * $ruangans->count() * 75); @endphp
            {{-- Calendar Grid --}}
            <div id="calendar-grid-wrapper" class="mp-card overflow-hidden">
                <div id="table-scroll-container" style="overflow-x: auto;">
                    <table
                        style="width: 100%; table-layout: fixed; border-collapse: collapse; font-size: 12px; min-width: {{ max(900, $minTWidth) }}px;">
                        <thead>
                            {{-- Row 1: Day headers spanning all rooms --}}
                            <tr style="background: #F1F3F9;">
                                <th
                                    style="width: 64px; min-width:64px; border: 1px solid #E5E7EB; padding: 10px 8px; text-align:center; background:#F8F9FB; color: #4B5563; font-weight: 700; vertical-align:middle; position: sticky; left: 0; z-index: 20; border-right: 2px solid #D1D5DB;">
                                    Jam
                                </th>
                                @foreach($weekDays as $day)
                                    <th colspan="{{ $ruangans->count() }}" {{ $day->isToday() ? 'id=col-today' : '' }}
                                        style="border: 1px solid #E5E7EB; padding: 10px 8px; text-align:center; font-weight: 700; color: #0B266E;
                                                                                                            {{ $day->isToday() ? 'background: #EFF6FF;' : 'background: #F8F9FB;' }}">
                                        <div style="font-size:13px;">{{ $day->translatedFormat('D') }}</div>
                                        <div style="font-size:11px; font-weight:500; color: #0B266E; margin-top:2px;">
                                            {{ $day->format('d/m') }}
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                            {{-- Row 2: Room sub-headers per day --}}
                            <tr style="background: #FAFAFA;">
                                <th
                                    style="border: 1px solid #E5E7EB; background: #FAFAFA; position: sticky; left: 0; z-index: 20; border-right: 2px solid #D1D5DB;">
                                </th>
                                @foreach($weekDays as $day)
                                    @foreach($ruangans as $ruang)
                                        <th
                                            style="border: 1px solid #E5E7EB; padding: 6px 4px; text-align:center; font-size:10px; font-weight:700; color:#6B7280; min-width: 72px;">
                                            {{ $ruang->nama }}
                                        </th>
                                    @endforeach
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jamList as $jam)
                                <tr style="{{ $loop->odd ? 'background:#FFFFFF;' : 'background:#FAFAFA;' }}">
                                    {{-- Time label --}}
                                    <td
                                        style="border: 1px solid #E5E7EB; padding: 6px 8px; text-align:center; font-weight:700; font-size:11px; color:#374151; background:#F8F9FB; white-space:nowrap; position: sticky; left: 0; z-index: 10; border-right: 2px solid #D1D5DB;">
                                        {{ str_pad($jam, 2, '0', STR_PAD_LEFT) }}.00
                                    </td>
                                    {{-- Cells per day per room --}}
                                    @foreach($weekDays as $day)
                                        @foreach($ruangans as $ruang)
                                            @php
                                                $dateStr = $day->format('Y-m-d');
                                                $cData = $cellMatrix[$dateStr][$ruang->id][$jam] ?? ['skip' => false, 'rowspan' => 1];
                                            @endphp

                                            @if($cData['skip'])
                                                @continue
                                            @endif

                                            @php
                                                $slotData = $slotMap[$dateStr][$ruang->id][$jam] ?? ['status' => 'tersedia', 'tujuan' => '', 'id' => null, 'pengguna' => ''];
                                                $slotStatus = $slotData['status'];
                                                $rawTujuan = $slotData['tujuan'];
                                                $pengguna = $slotData['pengguna'] ?? '';

                                                // Membersihkan text agar pas (hapus "digunakan untuk")
                                                $cleanTujuan = trim(str_ireplace('digunakan untuk', '', $rawTujuan));

                                                // Dynamic truncation: Hapus teks "Kelas" agar singkatan Matkul dan Abjad Kelas menyatu
                                                $cleanTujuan = str_ireplace(' - Kelas ', '-', $cleanTujuan);
                                                $cleanTujuan = str_ireplace(' (Kelas ', '-', $cleanTujuan);
                                                $cleanTujuan = str_replace(')', '', $cleanTujuan);

                                                // Jika status Internal (Jadwal Kuliah), gunakan default jika kosong
                                                if ($slotStatus === 'internal' && empty($cleanTujuan)) {
                                                    $cleanTujuan = 'Jadwal Kuliah';
                                                }

                                                // Validasi Past, Holiday, dan Operasional
                                                $isPastDay = $day->isPast() && !$day->isToday();
                                                $isPastHourToday = $day->isToday() && ($jam + 1) <= (int) now()->format('H');
                                                $isPast = $isPastDay || $isPastHourToday;

                                                $isHoliday = isset($holidays[$dateStr]);
                                                $isWeekend = $day->isWeekend();
                                                $isClosedWeekend = !$bukaAkhirPekan && $isWeekend;

                                                $minDate = \Carbon\Carbon::today()->addDays($batasHMinBooking);
                                                $isTooEarly = $day->copy()->startOfDay()->lt($minDate);

                                                $jamStr = str_pad($jam, 2, '0', STR_PAD_LEFT) . ':00';
                                                $isOutOfHours = ($jamStr < $jamBuka) || ($jamStr >= $jamTutup);

                                                $onClick = null;
                                                $isOwnBooking = ($slotData['user_id'] ?? null) === auth()->id();
                                                if ($isPast || $isClosedWeekend || $isOutOfHours) {
                                                    $bg = '#F3F4F6';
                                                    $border = '#D1D5DB';
                                                    $label = ''; // Render as blank closed block
                                                    $cursor = 'not-allowed';
                                                    $href = null;
                                                } elseif ($slotStatus === 'disetujui') {
                                                    // Disetujui: ungu, info minimal (Ruangan + Waktu)
                                                    $bg = '#EDE9FE';
                                                    $border = '#C4B5FD';
                                                    $label = 'Terpakai';
                                                    $cursor = 'pointer';
                                                    $href = null;
                                                    $roomName = addslashes($ruang->nama);
                                                    $jamDisplay = str_pad($jam, 2, '0', STR_PAD_LEFT) . ':00 - ' . str_pad($jam + $cData['rowspan'], 2, '0', STR_PAD_LEFT) . ':00';
                                                    $tanggalDisplay = $day->translatedFormat('l, d M Y');
                                                    $onClick = "openDetailModal('Terpakai', '-', '{$roomName}', '{$tanggalDisplay}', '{$jamDisplay}', 'terpakai', '')";
                                                } elseif ($slotStatus === 'internal') {
                                                    $tipeKategori = $cData['type'] ?? '';
                                                    if ($tipeKategori === 'Jadwal Akademik (Kuliah)' || $tipeKategori === 'Pindah Kelas' || $tipeKategori === 'Pindah / Pengganti Kelas') {
                                                        $bg = '#DBEAFE';
                                                        $border = '#60A5FA';
                                                    } elseif ($tipeKategori === 'Ujian / Evaluasi (UTS/UAS)' || $tipeKategori === 'Lainnya...') {
                                                        $bg = '#EDE9FE';
                                                        $border = '#C4B5FD';
                                                    } else {
                                                        $bg = '#FEE2E2';
                                                        $border = '#F87171';
                                                    }
                                                    $label = $cleanTujuan;
                                                    $cursor = 'pointer';
                                                    $href = null;
                                                    $roomName = addslashes($ruang->nama);
                                                    $jamDisplay = str_pad($jam, 2, '0', STR_PAD_LEFT) . ':00 - ' . str_pad($jam + $cData['rowspan'], 2, '0', STR_PAD_LEFT) . ':00';
                                                    $tanggalDisplay = $day->translatedFormat('l, d M Y');

                                                    if ($tipeKategori === 'Jadwal Akademik (Kuliah)' || $tipeKategori === 'Pindah Kelas' || $tipeKategori === 'Pindah / Pengganti Kelas') {
                                                        // Parse Kelas if present (e.g. "Sistem Basis Data-A")
                                                        $matkul = $label;
                                                        $kelas = '-';
                                                        if (strpos($label, '-') !== false) {
                                                            $parts = explode('-', $label);
                                                            $kelas = trim(array_pop($parts));
                                                            $matkul = trim(implode('-', $parts));
                                                        }
                                                        $onClick = "openDetailModal('" . addslashes($matkul) . "', '" . addslashes($kelas) . "', '{$roomName}', '{$tanggalDisplay}', '{$jamDisplay}', 'internal', '')";
                                                    } else {
                                                        $onClick = "openDetailModal('" . addslashes($label) . "', '-', '{$roomName}', '{$tanggalDisplay}', '{$jamDisplay}', 'internal', '')";
                                                    }
                                                } elseif ($slotStatus === 'menunggu' && $isOwnBooking) {
                                                    // Booking milik user sendiri: kuning, detail lengkap
                                                    $bg = '#FEF9C3';
                                                    $border = '#FBBF24';
                                                    $label = 'Menunggu';
                                                    $cursor = 'pointer';
                                                    $href = null;
                                                    $roomName = addslashes($ruang->nama);
                                                    $jamDisplay = str_pad($jam, 2, '0', STR_PAD_LEFT) . ':00 - ' . str_pad($jam + $cData['rowspan'], 2, '0', STR_PAD_LEFT) . ':00';
                                                    $tanggalDisplay = $day->translatedFormat('l, d M Y');
                                                    $tujuanOwn = addslashes($cleanTujuan);
                                                    $onClick = "openDetailModal('Menunggu Konfirmasi', '-', '{$roomName}', '{$tanggalDisplay}', '{$jamDisplay}', 'menunggu', '{$tujuanOwn}')";
                                                } elseif ($slotStatus === 'menunggu') {
                                                    // Booking milik user lain: tampil ungu, info minimal
                                                    $bg = '#EDE9FE';
                                                    $border = '#C4B5FD';
                                                    $label = 'Terpakai';
                                                    $cursor = 'pointer';
                                                    $href = null;
                                                    $roomName = addslashes($ruang->nama);
                                                    $jamDisplay = str_pad($jam, 2, '0', STR_PAD_LEFT) . ':00 - ' . str_pad($jam + $cData['rowspan'], 2, '0', STR_PAD_LEFT) . ':00';
                                                    $tanggalDisplay = $day->translatedFormat('l, d M Y');
                                                    $onClick = "openDetailModal('Terpakai', '-', '{$roomName}', '{$tanggalDisplay}', '{$jamDisplay}', 'terpakai', '')";
                                                } elseif ($isHoliday) {
                                                    $bg = '#FEE2E2';
                                                    $border = '#F87171';
                                                    $label = 'Libur';
                                                    $cursor = 'not-allowed';
                                                    $href = null;
                                                } elseif ($isTooEarly) {
                                                    $bg = '#F3F4F6';
                                                    $border = '#FCA5A5';
                                                    $label = 'Pinjam H-' . $batasHMinBooking;
                                                    $cursor = 'not-allowed';
                                                    $href = null;
                                                } else {
                                                    $bg = '#D1FAE5';
                                                    $border = '#34D399';
                                                    $label = 'Tersedia';
                                                    $cursor = 'pointer';
                                                    $href = route('eoffice.peminjaman.user.booking') . "?ruangan={$ruang->id}&tanggal={$dateStr}&jam=" . str_pad($jam, 2, '0', STR_PAD_LEFT) . ":00";
                                                }
                                            @endphp
                                            <td rowspan="{{ $cData['rowspan'] }}"
                                                style="border: 1px solid #E5E7EB; padding: 3px; height: 1px;">
                                                @if($href)
                                                    @php $hStr = str_pad($jam, 2, '0', STR_PAD_LEFT) . ':00'; @endphp
                                                    <button type="button"
                                                        @mousedown.prevent="startDrag('{{ $ruang->id }}', '{{ $ruang->nama }}', '{{ $dateStr }}', '{{ $hStr }}')"
                                                        @mouseenter="enterDrag('{{ $ruang->id }}', '{{ $dateStr }}', '{{ $hStr }}')"
                                                        @mouseup="stopDrag()"
                                                        @mouseover="!isDragging && ($el.style.background = '#A7F3D0'); !isDragging && ($el.style.transform = 'scale(1.03)')"
                                                        @mouseout="!isDragging && ($el.style.background = '{{ $bg }}'); !isDragging && ($el.style.transform = 'scale(1)')"
                                                        class="select-none"
                                                        style="display:flex; align-items:center; justify-content:center; min-height:34px; height: 100%; width:100%; font-size:9px; font-weight:700; color:#065F46; cursor:pointer; background: {{ $bg }}; border:1px solid {{ $border }}; border-radius:5px; transition:all 0.15s;"
                                                        :style="isDragging && dragStartPoint?.roomId === '{{ $ruang->id }}' && dragStartPoint?.dateStr === '{{ $dateStr }}' && dragSelection.includes('{{ $hStr }}') ? 'display:flex; align-items:center; justify-content:center; min-height:34px; height: 100%; width:100%; font-size:9px; font-weight:700; color:#065F46; cursor:pointer; background: #6EE7B7; border: 1px solid #10B981; border-radius:5px; transform: scale(1.05); z-index: 10; transition:all 0.15s;' : 'display:flex; align-items:center; justify-content:center; min-height:34px; height: 100%; width:100%; font-size:9px; font-weight:700; color:#065F46; cursor:pointer; background: {{ $bg }}; border:1px solid {{ $border }}; border-radius:5px; transition:all 0.15s;'"
                                                        title="Booking {{ $ruang->nama }} — {{ $day->translatedFormat('D, d M') }} pukul {{ $hStr }}">
                                                    </button>
                                                @else
                                                    @php
                                                        if ($isPast)
                                                            $tColor = '#9CA3AF';
                                                        elseif ($slotStatus === 'disetujui')
                                                            $tColor = '#5B21B6';
                                                        elseif ($slotStatus === 'internal') {
                                                            if ($tipeKategori === 'Jadwal Akademik (Kuliah)' || $tipeKategori === 'Pindah Kelas' || $tipeKategori === 'Pindah / Pengganti Kelas') {
                                                                $tColor = '#1E40AF';
                                                            } elseif ($tipeKategori === 'Ujian / Evaluasi (UTS/UAS)' || $tipeKategori === 'Lainnya...') {
                                                                $tColor = '#5B21B6';
                                                            } else {
                                                                $tColor = '#991B1B';
                                                            }
                                                        } elseif ($slotStatus === 'menunggu')
                                                            $tColor = '#B45309';
                                                        else
                                                            $tColor = '#374151';
                                                    @endphp
                                                    <div @if($onClick) @click="{{ $onClick }}"
                                                        @mouseover="$el.style.transform='scale(1.03)'; $el.style.boxShadow='0 4px 6px rgba(0,0,0,0.05)'"
                                                    @mouseout="$el.style.transform='scale(1)'; $el.style.boxShadow='none'" @endif
                                                        style="display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:34px; height:100%; width:100%; padding: 4px; overflow:hidden;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   background:{{ $bg }}; border:1px dashed {{ $border }}; border-radius:5px;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   text-align:center; white-space:normal; word-break:break-word; line-height:1.25; max-width:100%;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   font-size:9px; font-weight:800; color:{{ $tColor }}; transition: all 0.15s;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   cursor:{{ $cursor }}; opacity: {{ $isPast ? '0.5' : '1' }};">
                                                        {{ $label }}
                                                    </div>
                                                @endif
                                            </td>
                                        @endforeach
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 text-[12px] text-gray-500 font-medium">
                💡 <strong>Tips:</strong> Klik kotak <span class="text-emerald-600 font-bold">hijau</span> untuk langsung
                booking slot tersebut. Tanggal & jam otomatis terisi!
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
                class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" aria-hidden="true"
                @click="closeDetailModal"></div>

            {{-- Modal Panel --}}
            <div x-show="showDetailModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm flex flex-col overflow-hidden">

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
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">

            {{-- Backdrop --}}
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" aria-hidden="true"
                @click="closeModal"></div>

            {{-- Modal Panel --}}
            <div x-show="showModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative bg-white rounded-xl shadow-2xl w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden">

                {{-- Header --}}
                <div class="bg-white px-6 py-5 border-b border-gray-100 flex-shrink-0 flex justify-between items-start">
                    <div>
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Form Booking Ruangan</h3>
                        <p class="text-sm text-gray-500 mt-1">Lengkapi data berikut untuk meminjam <span
                                class="font-semibold text-emerald-600" x-text="bookingData.roomName"></span> pada <span
                                class="font-semibold text-emerald-600" x-text="bookingData.waktu"></span>.</p>
                    </div>
                    <button type="button" @click="closeModal"
                        class="text-gray-400 hover:text-gray-500 rounded-md focus:outline-none cursor-pointer">
                        <span class="sr-only">Close menu</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Scrollable Body --}}
                <div class="px-6 py-5 overflow-y-auto flex-1 bg-white">
                    <form id="bookingForm" method="POST" action="{{ route('eoffice.peminjaman.user.booking.store') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="ruangan_id" :value="selectedRoomId">
                        <input type="hidden" name="tanggal_pinjam" :value="selectedDate">
                        <input type="hidden" name="jam_mulai" :value="bookingData.jamMulai">
                        <input type="hidden" name="jam_selesai" :value="bookingData.jamSelesai">

                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Nama
                                        Lengkap</label>
                                    <input type="text" class="mp-input w-full bg-gray-50 text-gray-700 font-medium"
                                        value="{{ $user->name ?? 'Student' }}" readonly>
                                </div>
                                <div>
                                    <label
                                        class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">NIM
                                        / NIP</label>
                                    <input type="text" class="mp-input w-full bg-gray-50 text-gray-700 font-medium"
                                        value="{{ $nim ?? '00000' }}" readonly>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">No.
                                    Telepon / WhatsApp <span class="text-red-500">*</span></label>
                                <input type="text" name="nomor_telepon" class="mp-input w-full cursor-text"
                                    value="{{ $phone ?? '' }}" required placeholder="Contoh: 08123456789">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Jam
                                        Mulai</label>
                                    <input type="time"
                                        class="mp-input w-full bg-gray-50 text-gray-700 font-medium cursor-not-allowed"
                                        disabled x-model="bookingData.jamMulai">
                                </div>
                                <div>
                                    <label
                                        class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Hingga
                                        <span class="text-red-500">*</span></label>
                                    <input type="time"
                                        class="mp-input w-full bg-emerald-50 border-emerald-300 text-emerald-800 font-semibold focus:ring-emerald-500 cursor-text"
                                        x-model="bookingData.jamSelesai" required>
                                    <p class="text-[11px] text-gray-400 mt-1">Estimasi slot: 1 jam.</p>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Nama
                                    Kegiatan <span class="text-red-500">*</span></label>
                                <input type="text" name="tujuan" class="mp-input w-full cursor-text"
                                    placeholder="Misal: Rapat Kerja HIMASKOM" required>
                            </div>

                            <div class="bg-blue-50/30 border border-gray-200 p-4 rounded-lg">
                                <label
                                    class="block text-[11px] uppercase tracking-wider font-bold text-[#0B266E] mb-1.5 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    File Berkas Proposal <span class="text-gray-500 font-normal">(Opsional)</span>
                                </label>
                                <input type="file" name="file_berkas" accept=".pdf" class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-[#415086] file:text-white
                                hover:file:bg-[#2e3b66]
                                cursor-pointer transition-colors">
                                <p class="text-[10px] text-gray-500 mt-2">Format PDF maksimal 2MB. Hanya
                                    diperlukan untuk acara formal.</p>
                                @error('file_berkas')
                                    <p class="text-[11px] text-[#DF1C41] mt-1.5 font-bold flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Ukuran maksimal 2MB dan wajib berformat PDF.
                                    </p>
                                @enderror
                            </div>

                            {{-- Persetujuan S&K --}}
                            <div class="mt-2 pt-3 border-t border-gray-100 pb-2">
                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <div class="flex items-center h-5 mt-0.5">
                                        <input type="checkbox" name="syarat_ketentuan" required
                                            class="w-4 h-4 border border-gray-300 rounded bg-white text-[#0B266E] focus:ring-[#0B266E] focus:ring-2 transition-all cursor-pointer shadow-sm"
                                            style="scroll-margin-bottom: 80px; scroll-margin-top: 120px;">
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[12px] text-gray-500 leading-relaxed block">
                                            Saya bersedia <strong class="text-gray-700">merapikan kembali
                                                ruangan</strong> setelah digunakan dan siap <strong
                                                class="text-gray-700">bertanggung jawab penuh mengganti
                                                kerusakan</strong> barang atau fasilitas akibat kelalaian selama masa
                                            peminjaman.
                                        </span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Footer / Actions --}}
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 flex-shrink-0">
                    <button type="button" @click="closeModal"
                        class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-gray-200 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" form="bookingForm"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-[#0B266E] border border-transparent rounded-lg shadow-sm hover:bg-[#071946] transition-colors focus:ring-2 focus:ring-[#0B266E] focus:ring-offset-2 cursor-pointer">
                        Kirim Pengajuan
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
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
                isDragging: false,
                dragStartPoint: null,
                dragSelection: [],
                bookingData: {
                    roomName: '',
                    waktu: '',
                    jamMulai: '',
                    jamSelesai: ''
                },

                showDetailModal: false,
                detailData: {
                    kegiatan: '',
                    kelas: '',
                    ruangan: '',
                    tanggal: '',
                    waktu: '',
                    status: '',
                    tujuan: ''
                },

                openDetailModal(kegiatan, kelas, ruangan, tanggal, waktu, status = '', tujuan = '') {
                    this.detailData = { kegiatan, kelas, ruangan, tanggal, waktu, status, tujuan };
                    this.showDetailModal = true;
                },

                closeDetailModal() {
                    this.showDetailModal = false;
                },

                startDrag(roomId, roomName, date, hourStart) {
                    this.isDragging = true;
                    this.dragStartPoint = { roomId, dateStr: date, hourStart };
                    this.bookingData.roomName = roomName;
                    this.dragSelection = [hourStart];
                },

                enterDrag(roomId, date, hourStart) {
                    if (!this.isDragging) return;
                    if (this.dragStartPoint.roomId !== roomId || this.dragStartPoint.dateStr !== date) return;

                    let sh = parseInt(this.dragStartPoint.hourStart.substring(0, 2));
                    let eh = parseInt(hourStart.substring(0, 2));
                    let minH = Math.min(sh, eh);
                    let maxH = Math.max(sh, eh);

                    let newSel = [];
                    for (let i = minH; i <= maxH; i++) {
                        newSel.push(i.toString().padStart(2, '0') + ':00');
                    }
                    this.dragSelection = newSel;
                },

                stopDrag() {
                    if (this.isDragging && this.dragSelection.length > 0) {
                        let sorted = this.dragSelection.map(h => parseInt(h.substring(0, 2))).sort((a, b) => a - b);
                        let startH = sorted[0].toString().padStart(2, '0') + ':00';
                        let endHStr = (sorted[sorted.length - 1] + 1).toString().padStart(2, '0') + ':00';

                        this.selectedRoomId = this.dragStartPoint.roomId;
                        this.selectedDate = this.dragStartPoint.dateStr;

                        let d = new Date(this.dragStartPoint.dateStr);
                        let dateStr = d.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

                        this.bookingData.waktu = dateStr + ' Pukul ' + startH + ' - ' + endHStr;
                        this.bookingData.jamMulai = startH;
                        this.bookingData.jamSelesai = endHStr;

                        this.showModal = true;
                    }
                    this.isDragging = false;
                    this.dragStartPoint = null;
                    this.dragSelection = [];
                },

                openBookingModal(roomId, roomName, date, hourStart) {
                    this.selectedRoomId = roomId;
                    this.selectedDate = date;
                    this.bookingData.roomName = roomName;

                    let hr = parseInt(hourStart.substring(0, 2));
                    let hourEnd = (hr + 1).toString().padStart(2, '0') + ':00';

                    // Format date nicely
                    let d = new Date(date);
                    let dateStr = d.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

                    this.bookingData.waktu = dateStr + ' Pukul ' + hourStart + ' - ' + hourEnd;
                    this.bookingData.jamMulai = hourStart;
                    this.bookingData.jamSelesai = hourEnd;

                    this.showModal = true;
                    // Prevent background scrolling
                    document.body.style.overflow = 'hidden';
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
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
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