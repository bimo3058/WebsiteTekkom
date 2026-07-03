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

            for ($h = $mulai; $h < $selesai; $h++) {
                $slotMap[$tgl][$b->ruangan_id][$h] = [
                    'id' => 'pm_' . $b->id,
                    'st' => 'event',
                    'pengguna' => $b->user->name ?? 'Mahasiswa',
                    'tujuan' => $b->tujuan,
                    'waktu' => substr($b->jam_mulai, 0, 5) . ' - ' . substr($b->jam_selesai, 0, 5),
                    'type' => 'Peminjaman',
                    'telepon' => $b->nomor_telepon ?? '-',
                    'label' => strtoupper(substr($b->user->name ?? 'Mhs', 0, 15)),
                    'bg' => $isMenunggu ? '#FEF9C3' : '#DBEAFE',
                    'border' => $isMenunggu ? '#FBBF24' : '#60A5FA',
                    'text' => $isMenunggu ? '#B45309' : '#1E40AF',
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

            $bg = $j->tipe_jadwal === 'rutin' ? '#EDE9FE' : '#FEE2E2';
            $border = $j->tipe_jadwal === 'rutin' ? '#C4B5FD' : '#FCA5A5';
            $text = $j->tipe_jadwal === 'rutin' ? '#5B21B6' : '#991B1B';
            $payload = [
                'id' => 'it_' . $j->id,
                'st' => 'event',
                'pengguna' => 'Admin Sistem',
                'tujuan' => $j->keterangan,
                'waktu' => substr($j->jam_mulai, 0, 5) . ' - ' . substr($j->jam_selesai, 0, 5),
                'type' => $j->tipe_jadwal === 'rutin' ? 'Jadwal Mingguan' : 'Agenda Internal',
                'telepon' => '-',
                'label' => strtoupper(substr($j->kategori, 0, 15)),
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
                        if (!empty($j->tgl_mulai_efektif) && $tgl < $j->tgl_mulai_efektif) continue;
                        if (!empty($j->tgl_selesai_efektif) && $tgl > $j->tgl_selesai_efektif) continue;

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
                        📅 Mingguan
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['mode' => 'month', 'month' => $monthDate->format('Y-m')]) }}"
                        class="px-3 py-1.5 rounded-md text-[12px] font-semibold transition-all {{ $mode === 'month' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        🗓️ Bulanan
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine Wrapper Start --}}
    <div x-data="{}">

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
                            $slotData = $slotMap[$dateStr][$rId][$jam] ?? ['st' => 'tersedia'];

                            $isPastDay = $day->copy()->startOfDay()->lt(\Carbon\Carbon::today());
                            $isPastHourToday = $day->isToday() && (int)$jam <= (int) now()->format('H');
                            $isPast = $isPastDay || $isPastHourToday;
                            $isHoliday = isset($holidays[$dateStr]);
                            $isWeekend = $day->isWeekend();
                            $isClosedWeekend = !$bukaAkhirPekan && $isWeekend;
                            $jamStr = str_pad($jam, 2, '0', STR_PAD_LEFT) . ':00';
                            $isOutOfHours = ($jamStr < $jamBuka) || ($jamStr >= $jamTutup);

                            if ($isClosedWeekend || $isOutOfHours) {
                                $slotData = ['st' => 'tutup', 'bg' => '#F3F4F6', 'border' => '#D1D5DB', 'text' => '#9CA3AF', 'label' => '', 'cursor' => 'not-allowed', 'id' => 'closed_' . $jam];
                            } elseif ($isHoliday) {
                                $slotData = ['st' => 'libur', 'bg' => '#FEE2E2', 'border' => '#F87171', 'text' => '#9CA3AF', 'label' => 'Libur', 'cursor' => 'not-allowed', 'id' => 'holiday_' . $jam];
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
                            // Only merge for genuine event blocks — never merge tersedia/tutup/libur
                            if ($st['st'] === 'event' && isset($st['id'])) {
                                for ($k = $hIndex + 1; $k < count($jamList); $k++) {
                                    $nextSt = $hourStatuses[$jamList[$k]];
                                    if ($nextSt['st'] !== 'event') break;
                                    if (($nextSt['id'] ?? '') !== $st['id']) break;
                                    $rowspan++;
                                }
                            }
                            $cellMatrix[$dateStr][$rId][$jam] = ['skip' => false, 'rowspan' => $rowspan, 'payload' => $st];
                            $skipCount = $rowspan - 1;
                        }
                    }
                }
            @endphp

            {{-- Calendar Grid --}}
            <div class="mp-card overflow-hidden">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px; min-width: 900px;">
                        <thead>
                            {{-- Row 1: Day headers spanning all rooms --}}
                            <tr style="background: #F1F3F9;">
                                <th
                                    style="width: 64px; min-width:64px; border: 1px solid #E5E7EB; padding: 10px 8px; text-align:center; background:#F8F9FB; color: #4B5563; font-weight: 700; vertical-align:middle; position: sticky; left: 0; z-index: 20; border-right: 2px solid #D1D5DB;">
                                    Jam
                                </th>
                                @foreach($weekDays as $day)
                                    <th colspan="{{ $ruangans->count() }}"
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
                                                    <div @click.stop="window.dispatchEvent(new CustomEvent('open-jalur-tol', {
                                                                    detail: {
                                                                        ruangan_id: '{{ $ruang->id }}',
                                                                        ruangan_nama: '{{ addslashes($ruang->nama) }}',
                                                                        tanggal: '{{ $dateStr }}',
                                                                        jam: '{{ str_pad($jam, 2, "0", STR_PAD_LEFT).":00" }}'
                                                                    }
                                                                }))"
                                                        style="cursor:pointer; display:flex; align-items:center; justify-content:center; min-height:41px; height: 100%; width:100%; font-size:12px; stroke: #059669; color:#059669; background: #D1FAE5; border:1px solid #6EE7B7; border-radius:6px; opacity: {{ $payload['opacity'] ?? '1' }}; transition:all 0.1s;"
                                                        onmouseover="this.style.background='#A7F3D0'" onmouseout="this.style.background='#D1FAE5'">
                                                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M12 5v14M5 12h14"></path></svg>
                                                    </div>
                                                @elseif($st === 'event')
                                                    <div @click.stop="window.dispatchEvent(new CustomEvent('open-event-modal', {
                                                                                                    detail: {
                                                                                                        title: '{{ addslashes($label) }}',
                                                                                                        pengguna: '{{ addslashes($payload["pengguna"] ?? "") }}',
                                                                                                        ruangan: '{{ addslashes($ruang->nama) }}',
                                                                                                        tujuan: '{{ addslashes($payload["tujuan"] ?? "") }}',
                                                                                                        waktu: '{{ addslashes($payload["waktu"] ?? "") }}',
                                                                                                        type: '{{ addslashes($payload["type"] ?? "") }}',
                                                                                                        telepon: '{{ addslashes($payload["telepon"] ?? "-") }}'
                                                                                                    }
                                                                                                }))"
                                                        style="display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:41px; height: 100%; width:100%; padding:0 4px; overflow:hidden;
                                                                                                       background:{{ $bg }}; border:1px dashed {{ $border }}; border-radius:6px;
                                                                                                       font-size:10px; font-weight:700; color:{{ $text }};
                                                                                                       cursor:pointer; opacity: {{ $payload['opacity'] ?? '1' }}; transition: transform 0.1s;"
                                                        onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'"
                                                        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none'">
                                                        {{ $label }}
                                                    </div>
                                                @else
                                                    <div style="height: 100%; width:100%; background: #F9FAFB; border: 1px dashed #D1D5DB; border-radius: 6px;">
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


        <!-- Alpine.js Event Detail Modal -->
        <div x-data="{ open: false, title: '', pengguna: '', ruangan: '', tujuan: '', waktu: '', type: '', telepon: '' }"
            @open-event-modal.window="
            title = $event.detail.title;
            pengguna = $event.detail.pengguna;
            ruangan = $event.detail.ruangan;
            tujuan = $event.detail.tujuan;
            waktu = $event.detail.waktu;
            type = $event.detail.type;
            telepon = $event.detail.telepon;
            open = true;
         ">
            <div x-show="open" style="display: none;"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">
                <div x-show="open" x-transition.opacity
                    class="fixed inset-0 transition-opacity bg-gray-500/20 backdrop-blur-md"
                    @click="open = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="open" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl border border-gray-100 sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6 relative z-50">

                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 text-[11px] font-bold uppercase tracking-wider rounded-md" :class="{
                                  'bg-blue-100 text-blue-800': type === 'Peminjaman',
                                  'bg-red-100 text-red-800': type === 'Agenda Internal',
                                  'bg-purple-100 text-purple-800': type === 'Jadwal Mingguan'
                              }" x-text="type"></span>
                        </div>
                        <button @click="open = false"
                            class="text-gray-400 hover:text-gray-600 transition-colors z-[60] relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <h3 class="text-xl font-bold text-gray-900 font-['Inter_Tight'] leading-tight mb-5"
                        x-text="ruangan"></h3>

                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 p-1.5 bg-indigo-50 rounded text-indigo-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-widest">Waktu
                                    Terkunci</div>
                                <div class="text-[14px] font-bold text-gray-900" x-text="waktu"></div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 p-1.5 bg-indigo-50 rounded text-indigo-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-widest">
                                    Penanggung Jawab</div>
                                <div class="text-[14px] font-medium text-gray-800" x-text="pengguna"></div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3" x-show="telepon && telepon !== '-'">
                            <div class="mt-0.5 p-1.5 bg-indigo-50 rounded text-indigo-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-widest">
                                    Kontak PIC</div>
                                <div class="text-[14px] font-medium text-gray-800" x-text="telepon"></div>
                            </div>
                        </div>



                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 p-1.5 bg-indigo-50 rounded text-indigo-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="w-full">
                                <div class="text-[11px] font-semibold text-gray-500 uppercase tracking-widest">Agenda
                                    Utama</div>
                                <div class="text-[13px] text-gray-800 leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-100 mt-2 font-medium"
                                    x-text="tujuan"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button @click="show = false"
                            class="px-5 py-2.5 rounded-lg text-sm font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors">
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
                jam_selesai: ''
            }"  
            x-show="show" 
            @open-jalur-tol.window="
                show = true;
                ruangan_id = $event.detail.ruangan_id;
                ruangan_nama = $event.detail.ruangan_nama;
                tanggal = $event.detail.tanggal;
                jam = $event.detail.jam;
                jam_selesai = (parseInt(jam.split(':')[0]) + 1).toString().padStart(2, '0') + ':00';
            "
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity"
            style="display: none;"
            x-transition>
            
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all"
                @click.away="show = false">
                <div class="bg-indigo-600 p-5 text-white flex justify-between items-center relative overflow-hidden">
                    <div class="relative z-10">
                        <h2 class="text-lg font-bold">Fast-Track / Jalur Tol Booking</h2>
                        <p class="text-indigo-100 font-medium text-xs mt-1" x-text="ruangan_nama + ' - ' + tanggal + ' pukul ' + jam + ' WIB'"></p>
                    </div>
                    <button @click="show = false" class="text-indigo-100 hover:text-white transition-colors relative z-10 p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('eoffice.peminjaman.admin.kalender-global.express') }}">
                        @csrf
                        <input type="hidden" name="ruangan_id" x-model="ruangan_id">
                        <input type="hidden" name="tanggal" x-model="tanggal">
                        <input type="hidden" name="jam_mulai" x-model="jam">

                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-2 tracking-wide">Pilih Mode Tindakan</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="border rounded-lg p-3 cursor-pointer transition-colors" :class="modeAction === 'internal' ? 'bg-indigo-50 border-indigo-500' : 'bg-white border-gray-200 hover:bg-gray-50'">
                                    <input type="radio" name="tipe_aksi" value="internal" x-model="modeAction" class="hidden">
                                    <div class="font-bold text-sm" :class="modeAction === 'internal' ? 'text-indigo-700' : 'text-gray-700'">Jadwal Internal</div>
                                    <div class="text-[10px] text-gray-500 mt-1">Blokir Kuliah / Maintenance</div>
                                </label>
                                <label class="border rounded-lg p-3 cursor-pointer transition-colors" :class="modeAction === 'dosen' ? 'bg-emerald-50 border-emerald-500' : 'bg-white border-gray-200 hover:bg-gray-50'">
                                    <input type="radio" name="tipe_aksi" value="dosen" x-model="modeAction" class="hidden">
                                    <div class="font-bold text-sm" :class="modeAction === 'dosen' ? 'text-emerald-700' : 'text-gray-700'">Booking Ekspres</div>
                                    <div class="text-[10px] text-gray-500 mt-1">Suntik Peminjaman Disetujui</div>
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1 tracking-wide">Jam Selesai</label>
                            <input type="time" name="jam_selesai" x-model="jam_selesai" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-medium">
                        </div>

                        <div class="mb-4" x-show="modeAction === 'internal'" style="display:none;">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1 tracking-wide">Kategori Acara</label>
                            <select name="kategori" x-model="kategoriType" :required="modeAction === 'internal'" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="Jadwal Akademik (Kuliah)">Jadwal Akademik (Kuliah)</option>
                                <option value="Sidang / Ujian Akademik">Sidang / Ujian Akademik</option>
                                <option value="Rapat Internal Jurusan">Rapat Internal Jurusan</option>
                                <option value="Bimbingan Mahasiswa">Bimbingan Mahasiswa</option>
                                <option value="Maintenance / Perbaikan">Maintenance / Perbaikan</option>
                                <option value="Acara Kemahasiswaan">Acara Kemahasiswaan Khusus</option>
                            </select>
                        </div>

                        <!-- Academic Metadata Panel (Dynamic) -->
                        <div x-show="kategoriType === 'Jadwal Akademik (Kuliah)' && modeAction === 'internal'" style="display:none;" class="mb-4 p-4 bg-indigo-50/50 border border-indigo-100 rounded-xl space-y-4">
                            <h4 class="text-[12px] font-bold text-indigo-900 border-b border-indigo-100 pb-2 mb-2">Metadata Akademik Tambahan (Opsional)</h4>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">Mata Kuliah</label>
                                    <input type="text" name="mata_kuliah" :required="kategoriType === 'Jadwal Akademik (Kuliah)'" class="w-full border-gray-300 rounded-lg shadow-sm text-sm p-2" placeholder="Nama Kelas">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">Kode MK</label>
                                    <input type="text" name="kode_mk" class="w-full border-gray-300 rounded-lg shadow-sm text-sm p-2" placeholder="Contoh: TKK102">
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">Kelas</label>
                                    <input type="text" name="kelas" class="w-full border-gray-300 rounded-lg shadow-sm text-sm p-2" placeholder="Misal: A">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">SKS</label>
                                    <input type="number" name="sks" class="w-full border-gray-300 rounded-lg shadow-sm text-sm p-2" placeholder="SKS (0-4)">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">Kuota</label>
                                    <input type="number" name="kuota" class="w-full border-gray-300 rounded-lg shadow-sm text-sm p-2" placeholder="Kuota">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-700 uppercase mb-1">Nama Dosen Pengampu</label>
                                <input type="text" name="pengampu" class="w-full border-gray-300 rounded-lg shadow-sm text-sm p-2" placeholder="Dosen Pengampu Mata Kuliah">
                            </div>
                        </div>


                        <div class="mb-4" x-show="modeAction === 'dosen'">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1 tracking-wide">NIM / NIP Peminjam</label>
                            <input type="text" name="nim" x-model="nim" placeholder="Masukkan ID Pengguna Institusi..." class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <p class="text-[10px] text-gray-500 mt-1">Booking ini akan langsung dianggap sah dan berstatus 'disetujui'.</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1 tracking-wide">Tujuan / Agenda Utama</label>
                            <textarea name="keterangan" x-model="keterangan" rows="3" required placeholder="Jelaskan secara ringkas acara/kegiatan ini..." class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"></textarea>
                        </div>

                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" @click="show = false" class="px-4 py-2.5 rounded-lg text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">Batal</button>
                            <button type="submit" class="px-5 py-2.5 rounded-lg text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-sm">Eksekusi Jalur Tol</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
                </div>
            </div>
        </div>
</x-eoffice::manajemen-ruangan.layout>