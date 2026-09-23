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

            $isMenunggu = $b->status === 'menunggu';

            $namaPengguna = explode(' ', trim($b->user->name ?? 'Mhs'))[0];
            $cleanTujuan = trim(str_ireplace('digunakan untuk', '', $b->tujuan ?? ''));
            $cleanTujuan = str_ireplace(' - Kelas ', '-', $cleanTujuan);
            $cleanTujuan = str_ireplace(' (Kelas ', '-', $cleanTujuan);
            $cleanTujuan = str_replace(')', '', $cleanTujuan);

            for ($h = $mulai; $h < $selesai; $h++) {
                $slotMap[$tgl][$b->ruangan_id][$h] = [
                    'id' => 'pm_' . $b->id,
                    'st' => 'event',
                    'pengguna' => $b->user->name ?? 'Mahasiswa',
                    'tujuan' => $b->tujuan,
                    'waktu' => substr($b->jam_mulai, 0, 5) . ' - ' . substr($b->jam_selesai, 0, 5),
                    'type' => 'Peminjaman',
                    'telepon' => $b->nomor_telepon ?? '-',
                    'label' => $isMenunggu ? 'Menunggu' : substr($b->user->name ?? 'Mhs', 0, 15),
                    'bg' => $isMenunggu ? '#FEF9C3' : '#EDE9FE',
                    'border' => $isMenunggu ? '#FBBF24' : '#C4B5FD',
                    'text' => $isMenunggu ? '#B45309' : '#5B21B6',
                    'cursor' => 'pointer'
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

            $tipeKategori = $j->kategori ?? '';
            if ($j->tipe_jadwal === 'rutin' || $tipeKategori === 'Jadwal Akademik (Kuliah)' || $tipeKategori === 'Pindah Kelas' || $tipeKategori === 'Pindah / Pengganti Kelas') {
                $bg = '#DBEAFE';
                $border = '#60A5FA';
                $text = '#1E40AF';
            } elseif ($tipeKategori === 'Ujian / Evaluasi (UTS/UAS)' || $tipeKategori === 'Lainnya...') {
                $bg = '#EDE9FE';
                $border = '#C4B5FD';
                $text = '#5B21B6';
            } else {
                $bg = '#FEE2E2';
                $border = '#F87171';
                $text = '#991B1B';
            }
            $eventName = $j->mata_kuliah ? trim($j->mata_kuliah . ' ' . $j->kelas) : ($j->keterangan ?: $j->kategori);
            $payload = [
                'id' => 'it_' . $j->id,
                'st' => 'event',
                'pengguna' => 'Admin Sistem',
                'tujuan' => $eventName,
                'waktu' => substr($j->jam_mulai, 0, 5) . ' - ' . substr($j->jam_selesai, 0, 5),
                'type' => $j->tipe_jadwal === 'rutin' ? 'Jadwal Akademik (Kuliah)' : ($j->kategori ?? 'Agenda Internal'),
                'telepon' => '-',
                'label' => $eventName,
                'bg' => $bg,
                'border' => $border,
                'text' => $text,
                'cursor' => 'pointer'
            ];

            if ($j->tipe_jadwal === 'spesifik') {
                $tgl = \Carbon\Carbon::parse($j->tanggal_spesifik)->format('Y-m-d');
                for ($h = $mulai; $h < $selesai; $h++) {
                    $slotMap[$tgl][$j->ruangan_id][$h] = $payload;
                }
            } else if ($j->tipe_jadwal === 'rutin') {
                foreach ($weekDays as $day) {
                    if ($day->dayOfWeekIso == $j->hari) {
                        $tgl = $day->format('Y-m-d');

                        // Temporal Boundary Check: Skip drawing if outside active boundaries
                        if (!empty($j->tgl_mulai_efektif) && $tgl < $j->tgl_mulai_efektif)
                            continue;
                        if (!empty($j->tgl_selesai_efektif) && $tgl > $j->tgl_selesai_efektif)
                            continue;

                        for ($h = $mulai; $h < $selesai; $h++) {
                            $slotMap[$tgl][$j->ruangan_id][$h] = $payload;
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

                    <div
                        class="flex items-center rounded-md border border-slate-200 bg-white overflow-visible shadow-sm">
                        <div x-data="{ 
                                open: false, 
                                selectedId: '{{ $selectedRoomId }}', 
                                selectedName: '{{ $selectedRoomId ? addslashes($allRuangansDaftar->firstWhere('id', $selectedRoomId)->nama ?? 'Semua Ruangan') : 'Semua Ruangan' }}',
                                selectItem(id, name) { 
                                    this.selectedId = id; 
                                    this.selectedName = name; 
                                    $refs.ruanganInput.value = id;
                                    document.getElementById('roomFilterForm').submit();
                                } 
                            }" class="relative w-[140px] sm:w-[180px]" @click.away="open = false">

                            <input type="hidden" name="ruangan_id" x-ref="ruanganInput" :value="selectedId">

                            <button type="button" @click="open = !open"
                                class="w-full flex items-center justify-between px-3 py-1.5 text-[13px] text-slate-900 font-bold bg-white hover:bg-slate-50 focus:outline-none transition-colors rounded-md cursor-pointer">
                                <span x-text="selectedName" class="truncate pr-2"></span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200 shrink-0"
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
                                class="absolute left-0 top-full mt-1 w-full min-w-[160px] bg-white border border-slate-200 rounded-md shadow-lg z-50 overflow-y-auto max-h-48"
                                style="display: none;">
                                <div class="py-1">
                                    <button type="button" @click="selectItem('', 'Semua Ruangan')"
                                        class="w-full text-left px-3 py-1.5 text-[13px] text-slate-700 hover:bg-slate-50 hover:text-[#0B266E] font-medium transition-colors cursor-pointer"
                                        :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedId == ''}">Semua
                                        Ruangan</button>
                                    @foreach($allRuangansDaftar as $r)
                                        <button type="button"
                                            @click="selectItem('{{ $r->id }}', '{{ addslashes($r->nama) }}')"
                                            class="w-full text-left px-3 py-1.5 text-[13px] text-slate-700 hover:bg-slate-50 hover:text-[#0B266E] font-medium transition-colors mt-0.5 cursor-pointer"
                                            :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': selectedId == '{{ $r->id }}'}">
                                            {{ $r->nama }}
                                        </button>
                                    @endforeach
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
                $cellMatrix = [];
                foreach ($weekDays as $day) {
                    foreach ($ruangans as $ruang) {
                        $dateStr = $day->format('Y-m-d');
                        $rId = $ruang->id;
                        $hourStatuses = [];
                        foreach ($jamList as $hIndex => $jam) {
                            $slotData = $slotMap[$dateStr][$rId][$jam] ?? ['st' => 'tersedia'];

                            $isPastDay = $day->copy()->startOfDay()->lt(\Carbon\Carbon::today());
                            $isPastHourToday = $day->isToday() && (int) $jam <= (int) now()->format('H');
                            $isPast = $isPastDay || $isPastHourToday;
                            $isHoliday = isset($holidays[$dateStr]);
                            $isWeekend = $day->isWeekend();
                            $isClosedWeekend = !$bukaAkhirPekan && $isWeekend;
                            $jamStr = str_pad($jam, 2, '0', STR_PAD_LEFT) . ':00';
                            $isOutOfHours = ($jamStr < $jamBuka) || ($jamStr >= $jamTutup);

                            if ($isClosedWeekend || $isOutOfHours) {
                                $slotData = ['st' => 'tutup', 'bg' => '#F3F4F6', 'border' => '#D1D5DB', 'text' => '#9CA3AF', 'label' => '', 'cursor' => 'not-allowed', 'id' => 'closed_' . $jam];
                            } elseif ($isHoliday) {
                                $slotData = ['st' => 'libur', 'bg' => '#FEE2E2', 'border' => '#F87171', 'text' => '#B91C1C', 'label' => 'Libur', 'cursor' => 'not-allowed', 'id' => 'holiday_' . $jam];
                            }

                            // Apply past overlay: preserve event ID so rowspan still merges correctly
                            if ($isPast) {
                                if ($slotData['st'] === 'tersedia') {
                                    // Empty past slot → grey, unique ID per hour prevents rowspan merge
                                    $slotData = ['st' => 'tutup', 'bg' => '#F3F4F6', 'border' => '#D1D5DB', 'text' => '#9CA3AF', 'label' => '', 'cursor' => 'not-allowed', 'id' => 'past_' . $jam];
                                } else {
                                    // Past events keep their identity but look faded
                                    $slotData['opacity'] = '0.55';
                                }
                            } else {
                                $slotData['opacity'] = $slotData['opacity'] ?? '1';
                            }

                            $hourStatuses[$jam] = $slotData;
                        }

                        // Lookahead pass: only merge 'event' blocks with the same event ID
                        $skipCount = 0;
                        foreach ($jamList as $hIndex => $jam) {
                            if ($skipCount > 0) {
                                $cellMatrix[$dateStr][$rId][$jam] = ['skip' => true];
                                $skipCount--;
                                continue;
                            }
                            $st = $hourStatuses[$jam];
                            $rowspan = 1;
                            // Merge for events and holidays
                            if (($st['st'] === 'event' && isset($st['id'])) || $st['st'] === 'libur') {
                                for ($k = $hIndex + 1; $k < count($jamList); $k++) {
                                    $nextSt = $hourStatuses[$jamList[$k]];
                                    if ($st['st'] === 'libur') {
                                        if ($nextSt['st'] !== 'libur')
                                            break;
                                    } else {
                                        if ($nextSt['st'] !== 'event' || ($nextSt['id'] ?? '') !== $st['id'])
                                            break;
                                    }
                                    $rowspan++;
                                }
                            }
                            $cellMatrix[$dateStr][$rId][$jam] = ['skip' => false, 'rowspan' => $rowspan, 'payload' => $st];
                            $skipCount = $rowspan - 1;
                        }
                    }
                }
                $minTWidth = 64 + (7 * $ruangans->count() * 75);
            @endphp
            {{-- Calendar Grid --}}
            <div id="calendar-grid-wrapper" class="mp-card overflow-hidden">
                <div style="overflow-x: auto; width: 100%;">
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
                                    <th colspan="{{ $ruangans->count() }}"
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
                                        @php
                                            $dateStr = $day->format('Y-m-d');
                                            $isHoliday = isset($holidays[$dateStr]);
                                        @endphp

                                        @if($isHoliday)
                                            @if($loop->parent->first)
                                                <td colspan="{{ $ruangans->count() }}" rowspan="{{ count($jamList) }}"
                                                    style="border: 1px solid #E5E7EB; padding: 4px; vertical-align: top; height: 1px;">
                                                    @php $isMultiRoom = $ruangans->count() > 1; @endphp
                                                    <div
                                                        style="display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:41px; height:100%; width:100%; padding: 0 4px; overflow:hidden; background:#FEE2E2; border:1px dashed #F87171; border-radius:6px; text-align:center; color:#B91C1C; font-size:{{ $isMultiRoom ? '14px' : '10px' }}; font-weight:{{ $isMultiRoom ? '800' : '700' }}; {{ $isMultiRoom ? 'letter-spacing: 0.5px;' : '' }}">
                                                        {{ ucwords(strtolower($holidays[$dateStr])) }}
                                                    </div>
                                                </td>
                                            @endif
                                            @continue
                                        @endif

                                        @foreach($ruangans as $ruang)
                                            @php
                                                $cData = $cellMatrix[$dateStr][$ruang->id][$jam] ?? ['skip' => false, 'rowspan' => 1];
                                            @endphp

                                            @if($cData['skip'])
                                                @continue
                                            @endif


                                            @php
                                                $payload = $cData['payload'] ?? [];
                                                $bg = $payload['bg'] ?? '#F3F4F6';
                                                $border = $payload['border'] ?? '#D1D5DB';
                                                $text = $payload['text'] ?? '#9CA3AF';
                                                $label = $payload['label'] ?? '';
                                                $st = $payload['st'] ?? 'tutup';
                                            @endphp
                                            <td rowspan="{{ $cData['rowspan'] }}"
                                                style="border: 1px solid #E5E7EB; padding: 4px; vertical-align: top; height: 1px;">
                                                @if($st === 'tersedia')
                                                    @php $hStr = str_pad($jam, 2, '0', STR_PAD_LEFT) . ':00';
                                                        $bgT = '#D1FAE5';
                                                    $borderT = '#6EE7B7'; @endphp
                                                    <button type="button"
                                                        @mousedown.prevent="startDrag('{{ $ruang->id }}', '{{ addslashes($ruang->nama) }}', '{{ $dateStr }}', '{{ $hStr }}')"
                                                        @mouseenter="enterDrag('{{ $ruang->id }}', '{{ $dateStr }}', '{{ $hStr }}')"
                                                        @mouseup="stopDrag()"
                                                        @mouseover="!isDragging && ($el.style.background = '#A7F3D0'); !isDragging && ($el.style.transform = 'scale(1.03)')"
                                                        @mouseout="!isDragging && ($el.style.background = '{{ $bgT }}'); !isDragging && ($el.style.transform = 'scale(1)')"
                                                        class="select-none"
                                                        :style="isDragging && dragStartPoint?.roomId === '{{ $ruang->id }}' && dragStartPoint?.dateStr === '{{ $dateStr }}' && dragSelection.includes('{{ $hStr }}') 
                                                                                                                                                                                                                                                                                                                                ? 'display:flex; align-items:center; justify-content:center; min-height:41px; height: 100%; width:100%; color:#059669; cursor:pointer; background: #6EE7B7; border: 1px solid #059669; border-radius:6px; transform: scale(1.05); z-index: 10; transition:all 0.15s; opacity: {{ $payload['opacity'] ?? '1' }};' 
                                                                                                                                                                                                                                                                                                                                : 'display:flex; align-items:center; justify-content:center; min-height:41px; height: 100%; width:100%; color:#059669; cursor:pointer; background: {{ $bgT }}; border:1px solid {{ $borderT }}; border-radius:6px; transition:all 0.15s; opacity: {{ $payload['opacity'] ?? '1' }};'"
                                                        title="Booking Cepat {{ $ruang->nama }} pukul {{ $hStr }}">
                                                    </button>
                                                @elseif($st === 'event')
                                                    <div @click.stop="window.dispatchEvent(new CustomEvent('open-event-modal', {
                                                                                                                                                                                                                                                                                                                                                                        detail: {
                                                                                                                                                                                                                                                                                                                                                                            title: '{{ addslashes($label) }}',
                                                                                                                                                                                                                                                                                                                                                                            pengguna: '{{ addslashes($payload["pengguna"] ?? "") }}',
                                                                                                                                                                                                                                                                                                                                                                            ruangan: '{{ addslashes($ruang->nama) }}',
                                                                                                                                                                                                                                                                                                                                                                            tujuan: '{{ addslashes($payload["tujuan"] ?? "") }}',
                                                                                                                                                                                                                                                                                                                                                                            tanggal: '{{ $day->translatedFormat('l, d M Y') }}',
                                                                                                                                                                                                                                                                                                                                                                            waktu: '{{ addslashes($payload["waktu"] ?? "") }}',
                                                                                                                                                                                                                                                                                                                                                                            type: '{{ addslashes($payload["type"] ?? "") }}',
                                                                                                                                                                                                                                                                                                                                                                            telepon: '{{ addslashes($payload["telepon"] ?? "-") }}'
                                                                                                                                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                                                                                                                                    }))"
                                                        style="display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:41px; height: 100%; width:100%; padding:4px; overflow:hidden;
                                                                                                                                                                                                                                                                                                                                                                           background:{{ $bg }}; border:1px dashed {{ $border }}; border-radius:6px;
                                                                                                                                                                                                                                                                                                                                                                           text-align:center; white-space:normal; word-break:break-word; line-height:1.25; max-width:100%;
                                                                                                                                                                                                                                                                                                                                                                           font-size:10px; font-weight:700; color:{{ $text }};
                                                                                                                                                                                                                                                                                                                                                                           cursor:pointer; opacity: {{ $payload['opacity'] ?? '1' }}; transition: transform 0.1s;"
                                                        onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'"
                                                        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none'">
                                                        {{ $label }}
                                                    </div>
                                                @else
                                                    <div
                                                        style="height: 100%; width:100%; background: #F9FAFB; border: 1px dashed #D1D5DB; border-radius: 6px;">
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
            <div x-show="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
                aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;" x-cloak>

                {{-- Backdrop --}}
                <div x-show="show" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 transition-opacity bg-slate-900/40 backdrop-blur-md" aria-hidden="true"
                    @click="show = false">
                </div>

                <div x-show="show" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative bg-white rounded-xl shadow-2xl w-full max-w-xl max-h-[90vh] flex flex-col overflow-hidden">

                    {{-- Header --}}
                    <div
                        class="bg-white px-6 py-5 border-b border-gray-100 flex-shrink-0 flex justify-between items-start">
                        <div>
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Input Peminjaman</h3>
                            <p class="text-sm text-gray-500 mt-1">Mengunci penjadwalan paksa untuk <span
                                    class="font-semibold text-primary-500" x-text="ruangan_nama"></span> pada <span
                                    class="font-semibold text-primary-500"
                                    x-text="tanggal + ' pukul ' + jam + ' WIB'"></span>.</p>
                        </div>
                        <button type="button" @click="show = false"
                            class="text-gray-400 hover:text-gray-500 rounded-md focus:outline-none">
                            <span class="sr-only">Close menu</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Scrollable Body --}}
                    <div class="px-6 py-5 overflow-y-auto flex-1 bg-white">
                        <form id="expressBookingForm" method="POST"
                            action="{{ route('eoffice.peminjaman.admin.kalender-global.express') }}">
                            @csrf
                            <input type="hidden" name="ruangan_id" x-model="ruangan_id">
                            <input type="hidden" name="tanggal" x-model="tanggal">
                            <input type="hidden" name="jam_mulai" x-model="jam">

                            <div class="space-y-5">
                                <div>
                                    <label
                                        class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-2">Pilih
                                        Mode Tindakan</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="border rounded-lg p-3 cursor-pointer transition-colors"
                                            :class="modeAction === 'internal' ? 'bg-emerald-50 border-emerald-500' : 'bg-white border-gray-200 hover:bg-gray-50'">
                                            <input type="radio" name="tipe_aksi" value="internal" x-model="modeAction"
                                                class="hidden">
                                            <div class="font-bold text-sm"
                                                :class="modeAction === 'internal' ? 'text-emerald-700' : 'text-gray-700'">
                                                Jadwal Internal</div>
                                            <div class="text-[10px] text-gray-500 mt-1">Blokir Kuliah / Maintenance
                                            </div>
                                        </label>
                                        <label class="border rounded-lg p-3 cursor-pointer transition-colors"
                                            :class="modeAction === 'dosen' ? 'bg-emerald-50 border-emerald-500' : 'bg-white border-gray-200 hover:bg-gray-50'">
                                            <input type="radio" name="tipe_aksi" value="dosen" x-model="modeAction"
                                                class="hidden">
                                            <div class="font-bold text-sm"
                                                :class="modeAction === 'dosen' ? 'text-emerald-700' : 'text-gray-700'">
                                                Peminjaman Manual</div>
                                            <div class="text-[10px] text-gray-500 mt-1">Peminjaman langsung disetujui
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Mulai
                                            Menit Ke</label>
                                        <input type="time"
                                            class="mp-input w-full bg-gray-50 text-gray-700 font-medium cursor-not-allowed"
                                            :value="jam" disabled>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Hingga
                                            Jam <span class="text-red-500">*</span></label>
                                        <input type="time" name="jam_selesai" x-model="jam_selesai"
                                            class="mp-input w-full bg-emerald-50 border-emerald-300 text-emerald-800 font-semibold focus:ring-emerald-500"
                                            required>
                                    </div>
                                </div>

                                <div x-show="modeAction === 'internal'" style="display:none;">
                                    <label
                                        class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Kategori
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

                                        <input type="hidden" name="kategori" :value="kategoriType"
                                            :required="modeAction === 'internal'">

                                        <button type="button" @click="open = !open"
                                            class="w-full flex items-center justify-between mp-input bg-white focus:outline-none transition-colors h-[42px] px-3">
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
                                            <div class="p-1">
                                                <button type="button" @click="selectItem('Pindah Kelas')"
                                                    class="w-full text-left px-3 py-2 text-[13px] font-medium rounded-md transition-colors"
                                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Pindah Kelas', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Pindah Kelas'}">Pindah
                                                    / Pengganti Kelas</button>
                                                <button type="button" @click="selectItem('Maintenance / Perbaikan')"
                                                    class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Maintenance / Perbaikan', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Maintenance / Perbaikan'}">Maintenance
                                                    / Perbaikan Ruangan</button>
                                                <button type="button" @click="selectItem('Sterilisasi Ruangan')"
                                                    class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Sterilisasi Ruangan', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Sterilisasi Ruangan'}">Sterilisasi
                                                    / Persiapan Ruangan</button>
                                                <button type="button" @click="selectItem('Penutupan Khusus')"
                                                    class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Penutupan Khusus', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Penutupan Khusus'}">Penutupan
                                                    Khusus / Libur Nasional</button>
                                                <button type="button" @click="selectItem('Ujian / Evaluasi')"
                                                    class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Ujian / Evaluasi', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Ujian / Evaluasi'}">Ujian
                                                    / Evaluasi (UTS/UAS)</button>
                                                <button type="button" @click="selectItem('Lainnya')"
                                                    class="w-full text-left px-3 py-2 mt-0.5 text-[13px] font-medium rounded-md transition-colors"
                                                    :class="{'bg-[#EFF6FF] text-[#0B266E] font-bold': kategoriType == 'Lainnya', 'text-gray-700 hover:bg-gray-50': kategoriType != 'Lainnya'}">Lainnya...</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Academic Metadata Panel (Dynamic) -->
                                <div x-show="kategoriType === 'Jadwal Akademik (Kuliah)' && modeAction === 'internal'"
                                    style="display:none;"
                                    class="bg-primary-50 border border-primary-100 p-4 rounded-lg space-y-4 mt-2">
                                    <label
                                        class="block text-[11px] uppercase tracking-wider font-bold text-primary-500 mb-1.5 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Metadata Akademik Tambahan <span
                                            class="text-primary-500 font-normal">(Opsional)</span>
                                    </label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block text-[11px] uppercase tracking-wider font-bold text-primary-500/70 mb-1.5">Mata
                                                Kuliah</label>
                                            <input type="text" name="mata_kuliah"
                                                :required="kategoriType === 'Jadwal Akademik (Kuliah)'"
                                                class="mp-input w-full" placeholder="Nama Lengkap Matkul">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[11px] uppercase tracking-wider font-bold text-primary-500/70 mb-1.5">Kode
                                                MK</label>
                                            <input type="text" name="kode_mk" class="mp-input w-full"
                                                placeholder="Contoh: TKK102">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-3 gap-4">
                                        <div>
                                            <label
                                                class="block text-[11px] uppercase tracking-wider font-bold text-primary-500/70 mb-1.5">Kelas</label>
                                            <input type="text" name="kelas" class="mp-input w-full"
                                                placeholder="Misal: A">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[11px] uppercase tracking-wider font-bold text-primary-500/70 mb-1.5">SKS</label>
                                            <input type="number" name="sks" class="mp-input w-full" placeholder="0-4">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[11px] uppercase tracking-wider font-bold text-primary-500/70 mb-1.5">Kuota</label>
                                            <input type="number" name="kuota" class="mp-input w-full"
                                                placeholder="Kuota">
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[11px] uppercase tracking-wider font-bold text-primary-500/70 mb-1.5">Nama
                                            Dosen Pengampu</label>
                                        <input type="text" name="pengampu" class="mp-input w-full"
                                            placeholder="Dosen Pengampu Mata Kuliah">
                                    </div>
                                </div>

                                <div x-show="modeAction === 'dosen'" class="relative">
                                    <label
                                        class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Nama,
                                        NIM, atau Email Target <span class="text-red-500">*</span></label>
                                    <!-- HIDDEN ACTUAL INPUT -->
                                    <input type="hidden" name="nim" x-model="nim">
                                    <!-- SEARCH INPUT -->
                                    <input type="text" x-model="searchQuery" @input.debounce.500ms="searchUsers"
                                        placeholder="Ketik nama atau email peminjam..." class="mp-input w-full"
                                        autocomplete="off" :required="modeAction === 'dosen'">

                                    <!-- LOADING SPINNER -->
                                    <div x-show="isSearching" class="absolute right-3 top-8 text-primary-500">
                                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                        </svg>
                                    </div>

                                    <!-- DROPDOWN SUGGESTIONS -->
                                    <ul x-show="suggestions.length > 0" @click.away="suggestions = []"
                                        class="absolute z-[100] w-full bg-white mt-1 border border-gray-200 rounded-md shadow-lg max-h-48 overflow-y-auto"
                                        style="display: none;">
                                        <template x-for="user in suggestions" :key="user.id">
                                            <li @click="selectUser(user)"
                                                class="px-4 py-2.5 hover:bg-primary-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                                <div class="font-bold text-sm text-gray-800" x-text="user.name"></div>
                                                <div
                                                    class="flex items-center gap-2 mt-0.5 text-[11px] font-medium text-gray-500">
                                                    <span x-text="user.external_id || 'N/A'"></span>
                                                    <span class="text-gray-300">•</span>
                                                    <span x-text="user.email"></span>
                                                </div>
                                            </li>
                                        </template>
                                    </ul>

                                    <p class="text-[10px] text-gray-400 mt-1">Sistem akan melakukan autorisasi instan,
                                        peminjaman ini akan dikunci dan langsung berstatus 'Disetujui'.</p>
                                </div>

                                <div>
                                    <label
                                        class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Nama
                                        Kegiatan <span class="text-red-500">*</span></label>
                                    <input type="text" name="keterangan" x-model="keterangan" class="mp-input w-full"
                                        placeholder="Misal: Kuliah Pengganti / Rapat Evaluasi..." required>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 flex-shrink-0">
                        <button type="button" @click="show = false" class="mp-btn secondary md">
                            Batal
                        </button>
                        <button type="submit" form="expressBookingForm" class="mp-btn primary md">
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

                startDrag(roomId, roomName, date, hourStart) {
                    this.isDragging = true;
                    this.dragStartPoint = { roomId, roomName, dateStr: date, hourStart };
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

                        // Dispatch event for Admin Modal
                        window.dispatchEvent(new CustomEvent('open-jalur-tol', {
                            detail: {
                                ruangan_id: this.dragStartPoint.roomId,
                                ruangan_nama: this.dragStartPoint.roomName,
                                tanggal: this.dragStartPoint.dateStr,
                                jam: startH,
                                jam_selesai: endHStr
                            }
                        }));
                    }
                    this.isDragging = false;
                    this.dragStartPoint = null;
                    this.dragSelection = [];
                }
            }))
        });
    </script>
</x-eoffice::manajemen-ruangan.layout>