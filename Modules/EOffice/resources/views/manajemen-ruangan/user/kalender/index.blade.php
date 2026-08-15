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
            $selesai = (int) \Carbon\Carbon::parse($b->jam_selesai)->format('H');
            $tgl = is_string($b->tanggal_pinjam) ? $b->tanggal_pinjam : $b->tanggal_pinjam->format('Y-m-d');
            for ($h = $mulai; $h < $selesai; $h++) {
                $slotMap[$tgl][$b->ruangan_id][$h] = [
                    'status' => $b->status,
                    'tujuan' => $b->tujuan ?? ''
                ];
            }
        }

        // Parse and superimpose MrJadwalInternal events (Blocks entire slot)
        foreach ($internalSchedules as $j) {
            $mulai = (int) \Carbon\Carbon::parse($j->jam_mulai)->format('H');
            $selesai = (int) \Carbon\Carbon::parse($j->jam_selesai)->format('H');

            if ($j->tipe_jadwal === 'spesifik') {
                $tgl = \Carbon\Carbon::parse($j->tanggal_spesifik)->format('Y-m-d');
                for ($h = $mulai; $h < $selesai; $h++) {
                    $slotMap[$tgl][$j->ruangan_id][$h] = [
                        'status' => 'internal',
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
                                'status' => 'internal',
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

                    <select name="ruangan_id" onchange="document.getElementById('roomFilterForm').submit()"
                        class="mp-input py-1.5 px-3 text-[13px] font-medium bg-white border-gray-300 rounded-lg shadow-sm w-40">
                        <option value="">Semua Ruangan</option>
                        @foreach($allRuangansDaftar as $r)
                            <option value="{{ $r->id }}" {{ $selectedRoomId == $r->id ? 'selected' : '' }}>
                                {{ $r->nama }}
                            </option>
                        @endforeach
                    </select>
                </form>

                {{-- Mode Toggle --}}
                <div class="flex bg-gray-100 rounded-lg p-1 gap-1">
                    <a href="{{ request()->fullUrlWithQuery(['mode' => 'week', 'week_start' => $weekStart->format('Y-m-d')]) }}"
                        class="px-3 py-1.5 rounded-md text-[12px] font-semibold transition-all {{ $mode === 'week' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Mingguan
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['mode' => 'month', 'month' => $monthDate->format('Y-m')]) }}"
                        class="px-3 py-1.5 rounded-md text-[12px] font-semibold transition-all {{ $mode === 'month' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Bulanan
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine Wrapper Start --}}
    <div x-data="bookingKalender()" @mouseup.window="stopDrag()">

        {{-- =================== LEGEND =================== --}}
        <div class="flex items-center gap-4 mt-3 mb-5 text-[12px] font-medium text-gray-600">
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-emerald-400 inline-block"></span> Tersedia (klik untuk booking)
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-amber-400 inline-block"></span> Menunggu Persetujuan
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded-sm bg-red-400 inline-block"></span> Terpakai / Penuh
            </div>
        </div>

        {{-- =================== WEEKLY MODE =================== --}}
        @if($mode === 'week')
            {{-- Week Nav --}}
            <div class="flex items-center justify-between mb-4">
                @if($canGoBackWeek)
                    <a href="{{ request()->fullUrlWithQuery(['week_start' => $prevWeek]) }}"
                        class="inline-flex items-center gap-1.5 text-[13px] text-gray-600 hover:text-indigo-600 font-semibold px-3 py-2 rounded-lg hover:bg-indigo-50 transition-colors">
                        ← Minggu Lalu
                    </a>
                @else
                    <div class="px-3 py-2 w-[120px]"></div>
                @endif
                <div class="text-[15px] font-bold text-gray-800">
                    {{ $weekStart->translatedFormat('d M Y') }} — {{ $weekEnd->translatedFormat('d M Y') }}
                </div>
                <a href="{{ request()->fullUrlWithQuery(['week_start' => $nextWeek]) }}"
                    class="inline-flex items-center gap-1.5 text-[13px] text-gray-600 hover:text-indigo-600 font-semibold px-3 py-2 rounded-lg hover:bg-indigo-50 transition-colors">
                    Minggu Depan →
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
                            $slotData = $slotMap[$dateStr][$rId][$jam] ?? ['status' => 'tersedia', 'tujuan' => ''];
                            $slotStatus = $slotData['status'];
                            $tujuan = $slotData['tujuan'];

                            $isPastDay = $day->isPast() && !$day->isToday();
                            $isPastHourToday = $day->isToday() && $jam <= (int) now()->format('H');
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

                            $hourStatuses[$jam] = ['st' => $fKey, 'tujuan' => $tujuan];
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
                            if ($stObj['st'] !== 'tersedia' && $stObj['st'] !== 'menunggu') {
                                for ($k = $hIndex + 1; $k < count($jamList); $k++) {
                                    $nextStObj = $hourStatuses[$jamList[$k]];
                                    // if it's an event (internal/penuh), we must match the identical event string
                                    if ($nextStObj['st'] === $stObj['st']) {
                                        if (($stObj['st'] === 'internal' || $stObj['st'] === 'penuh') && $nextStObj['tujuan'] !== $stObj['tujuan']) {
                                            break;
                                        }
                                        $rowspan++;
                                    } else {
                                        break;
                                    }
                                }
                            }
                            $cellMatrix[$dateStr][$rId][$jam] = ['skip' => false, 'rowspan' => $rowspan];
                            $skipCount = $rowspan - 1;
                        }
                    }
                }
            @endphp

            @php $minTWidth = 64 + (7 * $ruangans->count() * 75); @endphp
            {{-- Calendar Grid --}}
            <div class="mp-card overflow-hidden">
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
                                        style="border: 1px solid #E5E7EB; padding: 10px 8px; text-align:center; color: #111827; font-weight: 700;
                                                                                                                                                                                                                        {{ $day->isToday() ? 'background: #EEF2FF; color: #4338CA;' : 'background: #F8F9FB;' }}">
                                        <div style="font-size:13px;">{{ $day->translatedFormat('D') }}</div>
                                        <div
                                            style="font-size:11px; font-weight:500; color: {{ $day->isToday() ? '#6366f1' : '#6B7280' }}; margin-top:2px;">
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
                                                $slotData = $slotMap[$dateStr][$ruang->id][$jam] ?? ['status' => 'tersedia', 'tujuan' => ''];
                                                $slotStatus = $slotData['status'];
                                                $rawTujuan = $slotData['tujuan'];

                                                // Membersihkan text agar pas (hapus "digunakan untuk")
                                                $cleanTujuan = trim(str_ireplace('digunakan untuk', '', $rawTujuan));

                                                // Dynamic truncation: Hapus teks "Kelas" agar singkatan Matkul dan Abjad Kelas menyatu
                                                $cleanTujuan = str_ireplace(' - Kelas ', '-', $cleanTujuan);
                                                $cleanTujuan = str_ireplace(' (Kelas ', '-', $cleanTujuan);
                                                $cleanTujuan = str_replace(')', '', $cleanTujuan);
                                                $words = explode(' ', $cleanTujuan);
                                                if (count($words) > 3) {
                                                    $cleanTujuan = implode(' ', array_slice($words, 0, 3)) . '..';
                                                }
                                                // Jika status Internal (Jadwal Kuliah), gunakan default jika kosong
                                                if ($slotStatus === 'internal' && empty($cleanTujuan)) {
                                                    $cleanTujuan = 'Jadwal Kuliah';
                                                }

                                                // Validasi Past, Holiday, dan Operasional
                                                $isPastDay = $day->isPast() && !$day->isToday();
                                                $isPastHourToday = $day->isToday() && $jam <= (int) now()->format('H');
                                                $isPast = $isPastDay || $isPastHourToday;

                                                $isHoliday = isset($holidays[$dateStr]);
                                                $isWeekend = $day->isWeekend();
                                                $isClosedWeekend = !$bukaAkhirPekan && $isWeekend;

                                                $minDate = \Carbon\Carbon::today()->addDays($batasHMinBooking);
                                                $isTooEarly = $day->copy()->startOfDay()->lt($minDate);

                                                $jamStr = str_pad($jam, 2, '0', STR_PAD_LEFT) . ':00';
                                                $isOutOfHours = ($jamStr < $jamBuka) || ($jamStr >= $jamTutup);

                                                if ($isPast || $isClosedWeekend || $isOutOfHours) {
                                                    $bg = '#F3F4F6';
                                                    $border = '#D1D5DB';
                                                    $label = ''; // Render as blank closed block
                                                    $cursor = 'not-allowed';
                                                    $href = null;
                                                } elseif ($slotStatus === 'disetujui') {
                                                    $bg = '#FEE2E2';
                                                    $border = '#F87171';
                                                    $label = strtoupper($cleanTujuan) ?: 'TERISI';
                                                    $cursor = 'not-allowed';
                                                    $href = null;
                                                } elseif ($slotStatus === 'internal') {
                                                    $bg = '#EDE9FE';
                                                    $border = '#C4B5FD';
                                                    $label = strtoupper($cleanTujuan);
                                                    $cursor = 'not-allowed';
                                                    $href = null;
                                                } elseif ($slotStatus === 'menunggu') {
                                                    $bg = '#FEF9C3';
                                                    $border = '#FBBF24';
                                                    $label = 'Menunggu';
                                                    $cursor = 'not-allowed';
                                                    $href = null;
                                                } elseif ($isHoliday) {
                                                    $bg = '#FEE2E2';
                                                    $border = '#F87171';
                                                    $label = 'Libur';
                                                    $cursor = 'not-allowed';
                                                    $href = null;
                                                } elseif ($isTooEarly) {
                                                    $bg = '#F3F4F6';
                                                    $border = '#FCA5A5';
                                                    $label = 'H-' . $batasHMinBooking;
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
                                                        :style="isDragging && dragStartPoint?.roomId === '{{ $ruang->id }}' && dragStartPoint?.dateStr === '{{ $dateStr }}' && dragSelection.includes('{{ $hStr }}') 
                                                                                                                                                                                                                                                                                                                                    ? 'display:flex; align-items:center; justify-content:center; min-height:34px; height: 100%; width:100%; font-size:9px; font-weight:700; color:#065F46; cursor:pointer; background: #6EE7B7; border: 1px solid #10B981; border-radius:5px; transform: scale(1.05); z-index: 10; transition:all 0.15s;' 
                                                                                                                                                                                                                                                                                                                                    : 'display:flex; align-items:center; justify-content:center; min-height:34px; height: 100%; width:100%; font-size:9px; font-weight:700; color:#065F46; cursor:pointer; background: {{ $bg }}; border:1px solid {{ $border }}; border-radius:5px; transition:all 0.15s;'"
                                                        title="Booking {{ $ruang->nama }} — {{ $day->translatedFormat('D, d M') }} pukul {{ $hStr }}">
                                                        ✓
                                                    </button>
                                                @else
                                                    @php
                                                        if ($isPast)
                                                            $tColor = '#9CA3AF';
                                                        elseif ($slotStatus === 'disetujui')
                                                            $tColor = '#B91C1C';
                                                        elseif ($slotStatus === 'internal')
                                                            $tColor = '#5B21B6';
                                                        elseif ($slotStatus === 'menunggu')
                                                            $tColor = '#B45309';
                                                        else
                                                            $tColor = '#374151';
                                                    @endphp
                                                    <div
                                                        style="display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:34px; height:100%; width:100%; padding: 4px; overflow:hidden;
                                                                                                                                                                                                                                                                                                                   background:{{ $bg }}; border:1px dashed {{ $border }}; border-radius:5px;
                                                                                                                                                                                                                                                                                                                   text-align:center; white-space:normal; word-break:break-word; line-height:1.25; max-width:100%;
                                                                                                                                                                                                                                                                                                                   font-size:9px; font-weight:800; color:{{ $tColor }};
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
                        class="inline-flex items-center gap-1.5 text-[13px] text-gray-600 hover:text-indigo-600 font-semibold px-3 py-2 rounded-lg hover:bg-indigo-50 transition-colors">
                        ← Bulan Lalu
                    </a>
                @else
                    <div class="px-3 py-2 w-[120px]"></div>
                @endif
                <div class="text-[15px] font-bold text-gray-800">
                    {{ $monthDate->translatedFormat('F Y') }}
                </div>
                <a href="{{ request()->fullUrlWithQuery(['month' => $nextMonth]) }}"
                    class="inline-flex items-center gap-1.5 text-[13px] text-gray-600 hover:text-indigo-600 font-semibold px-3 py-2 rounded-lg hover:bg-indigo-50 transition-colors">
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
                                                                                                                                                                                                                                                                                                          background: {{ $cellBg }}; border: {{ $isToday ? '2px solid #6366F1' : '1px solid #E5E7EB' }};
                                                                                                                                                                                                                                                                                                          transition: all 0.15s; {{ $isPast ? 'opacity:0.55;' : '' }}"
                                    onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'"
                                    onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none'">
                                    <div
                                        style="font-size:13px; font-weight:700; color: {{ $isToday ? '#4338CA' : ($isClosed ? '#9CA3AF' : '#111827') }};">
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
                    class="text-indigo-600 font-bold">Mingguan</span> di minggu tersebut secara detail.
            </div>
        @endif

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
                                <input type="text" name="nomor_telepon" class="mp-input w-full"
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
                                        class="mp-input w-full bg-emerald-50 border-emerald-300 text-emerald-800 font-semibold focus:ring-emerald-500"
                                        x-model="bookingData.jamSelesai" required>
                                    <p class="text-[11px] text-gray-400 mt-1">Estimasi slot: 1 jam.</p>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-[11px] uppercase tracking-wider font-bold text-gray-500 mb-1.5">Nama
                                    Kegiatan <span class="text-red-500">*</span></label>
                                <input type="text" name="tujuan" class="mp-input w-full"
                                    placeholder="Misal: Rapat Kerja HIMASKOM" required>
                            </div>

                            <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-lg">
                                <label
                                    class="block text-[11px] uppercase tracking-wider font-bold text-indigo-800 mb-1.5 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    File Berkas Proposal <span class="text-indigo-500 font-normal">(Opsional)</span>
                                </label>
                                <input type="file" name="file_berkas" accept=".pdf,.doc,.docx" class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-600 file:text-white
                                hover:file:bg-indigo-700
                                cursor-pointer transition-colors">
                                <p class="text-[10px] text-indigo-400 mt-2">Format PDF/Word maksimal 2MB. Hanya
                                    diperlukan untuk acara formal.</p>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Footer / Actions --}}
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-end gap-3 flex-shrink-0">
                    <button type="button" @click="closeModal"
                        class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-gray-200">
                        Batal
                    </button>
                    <button type="submit" form="bookingForm"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 border border-transparent rounded-lg shadow-sm hover:bg-indigo-700 transition-colors focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
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
    </script>
</x-eoffice::manajemen-ruangan.layout>