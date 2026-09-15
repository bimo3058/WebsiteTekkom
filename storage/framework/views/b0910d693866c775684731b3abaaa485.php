<?php if (isset($component)) { $__componentOriginal544c4f22773da773738fd88478b17597 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal544c4f22773da773738fd88478b17597 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'eoffice::components.manajemen-ruangan.layout','data' => ['pageTitle' => 'Kalender Ruangan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('eoffice::manajemen-ruangan.layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pageTitle' => 'Kalender Ruangan']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php
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
            $eventName = $j->mata_kuliah ? trim($j->mata_kuliah . ' ' . $j->kelas) : ($j->keterangan ?: $j->kategori);
            $payload = [
                'id' => 'it_' . $j->id,
                'st' => 'event',
                'pengguna' => 'Admin Sistem',
                'tujuan' => $eventName,
                'waktu' => substr($j->jam_mulai, 0, 5) . ' - ' . substr($j->jam_selesai, 0, 5),
                'type' => $j->tipe_jadwal === 'rutin' ? 'Jadwal Mingguan' : 'Agenda Internal',
                'telepon' => '-',
                'label' => strtoupper(substr($eventName, 0, 25)),
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
    ?>

    
    <div class="mp-page-header">
        <div class="flex flex-col md:flex-row md:items-center justify-between w-full gap-4">
            <div>
                <h1 class="mp-page-title">Kalender Ruangan</h1>
                <p class="mp-page-sub">Pantau ketersediaan dan agenda ruang secara komprehensif.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                
                <form id="roomFilterForm" method="GET"
                    action="<?php echo e(route('eoffice.peminjaman.admin.kalender-global.index')); ?>" class="flex items-center">
                    <input type="hidden" name="mode" value="<?php echo e($mode); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'week'): ?> <input type="hidden" name="week_start"
                    value="<?php echo e($weekStart->format('Y-m-d')); ?>"> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'month'): ?> <input type="hidden" name="month" value="<?php echo e($monthDate->format('Y-m')); ?>">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <select name="ruangan_id" onchange="document.getElementById('roomFilterForm').submit()"
                        class="mp-input py-1.5 px-3 text-[13px] font-medium bg-white border-gray-300 rounded-lg shadow-sm w-40">
                        <option value="">Semua Ruangan</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $allRuangansDaftar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($r->id); ?>" <?php echo e($selectedRoomId == $r->id ? 'selected' : ''); ?>>
                                <?php echo e($r->nama); ?>

                            </option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </form>

                
                <div class="flex bg-gray-100 rounded-lg p-1 gap-1">
                    <a href="<?php echo e(request()->fullUrlWithQuery(['mode' => 'week', 'week_start' => $weekStart->format('Y-m-d')])); ?>"
                        class="px-3 py-1.5 rounded-md text-[12px] font-semibold transition-all <?php echo e($mode === 'week' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'); ?>">
                        Mingguan
                    </a>
                    <a href="<?php echo e(request()->fullUrlWithQuery(['mode' => 'month', 'month' => $monthDate->format('Y-m')])); ?>"
                        class="px-3 py-1.5 rounded-md text-[12px] font-semibold transition-all <?php echo e($mode === 'month' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-700'); ?>">
                        Bulanan
                    </a>
                </div>
            </div>
        </div>
    </div>

    
    <div x-data="bookingKalender()" @mouseup.window="stopDrag()" class="w-full min-w-0 max-w-full">

        
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

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mode === 'week'): ?>
            
            <div class="flex items-center justify-between mb-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canGoBackWeek): ?>
                    <a href="<?php echo e(request()->fullUrlWithQuery(['week_start' => $prevWeek])); ?>"
                        class="inline-flex items-center gap-1.5 text-[13px] text-gray-600 hover:text-indigo-600 font-semibold px-3 py-2 rounded-lg hover:bg-indigo-50 transition-colors">
                        ← Minggu Lalu
                    </a>
                <?php else: ?>
                    <div class="px-3 py-2 w-[120px]"></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="text-[15px] font-bold text-gray-800">
                    <?php echo e($weekStart->translatedFormat('d M Y')); ?> — <?php echo e($weekEnd->translatedFormat('d M Y')); ?>

                </div>
                <a href="<?php echo e(request()->fullUrlWithQuery(['week_start' => $nextWeek])); ?>"
                    class="inline-flex items-center gap-1.5 text-[13px] text-gray-600 hover:text-indigo-600 font-semibold px-3 py-2 rounded-lg hover:bg-indigo-50 transition-colors">
                    Minggu Depan →
                </a>
            </div>

            <?php
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
                                    if ($nextSt['st'] !== 'event')
                                        break;
                                    if (($nextSt['id'] ?? '') !== $st['id'])
                                        break;
                                    $rowspan++;
                                }
                            }
                            $cellMatrix[$dateStr][$rId][$jam] = ['skip' => false, 'rowspan' => $rowspan, 'payload' => $st];
                            $skipCount = $rowspan - 1;
                        }
                    }
                }
                $minTWidth = 64 + (7 * $ruangans->count() * 75);
            ?>
            
            <div class="mp-card overflow-hidden">
                <div style="overflow-x: auto; width: 100%;">
                    <table
                        style="width: 100%; table-layout: fixed; border-collapse: collapse; font-size: 12px; min-width: <?php echo e(max(900, $minTWidth)); ?>px;">
                        <thead>
                            
                            <tr style="background: #F1F3F9;">
                                <th
                                    style="width: 64px; min-width:64px; border: 1px solid #E5E7EB; padding: 10px 8px; text-align:center; background:#F8F9FB; color: #4B5563; font-weight: 700; vertical-align:middle; position: sticky; left: 0; z-index: 20; border-right: 2px solid #D1D5DB;">
                                    Jam
                                </th>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $weekDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <th colspan="<?php echo e($ruangans->count()); ?>"
                                        style="border: 1px solid #E5E7EB; padding: 10px 8px; text-align:center; color: #111827; font-weight: 700;
                                                                                                                                                                                                <?php echo e($day->isToday() ? 'background: #EEF2FF; color: #4338CA;' : 'background: #F8F9FB;'); ?>">
                                        <div style="font-size:13px;"><?php echo e($day->translatedFormat('D')); ?></div>
                                        <div
                                            style="font-size:11px; font-weight:500; color: <?php echo e($day->isToday() ? '#6366f1' : '#6B7280'); ?>; margin-top:2px;">
                                            <?php echo e($day->format('d/m')); ?>

                                        </div>
                                    </th>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </tr>
                            
                            <tr style="background: #FAFAFA;">
                                <th
                                    style="border: 1px solid #E5E7EB; background: #FAFAFA; position: sticky; left: 0; z-index: 20; border-right: 2px solid #D1D5DB;">
                                </th>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $weekDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $ruangans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ruang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <th
                                            style="border: 1px solid #E5E7EB; padding: 6px 4px; text-align:center; font-size:10px; font-weight:700; color:#6B7280; min-width: 72px;">
                                            <?php echo e($ruang->nama); ?>

                                        </th>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $jamList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jam): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr style="<?php echo e($loop->odd ? 'background:#FFFFFF;' : 'background:#FAFAFA;'); ?>">
                                    
                                    <td
                                        style="border: 1px solid #E5E7EB; padding: 6px 8px; text-align:center; font-weight:700; font-size:11px; color:#374151; background:#F8F9FB; white-space:nowrap; position: sticky; left: 0; z-index: 10; border-right: 2px solid #D1D5DB;">
                                        <?php echo e(str_pad($jam, 2, '0', STR_PAD_LEFT)); ?>.00
                                    </td>
                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $weekDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $ruangans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ruang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <?php
                                                $dateStr = $day->format('Y-m-d');
                                                $cData = $cellMatrix[$dateStr][$ruang->id][$jam] ?? ['skip' => false, 'rowspan' => 1];
                                            ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cData['skip']): ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php continue; ?>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


                                            <?php
                                                $payload = $cData['payload'] ?? [];
                                                $bg = $payload['bg'] ?? '#F3F4F6';
                                                $border = $payload['border'] ?? '#D1D5DB';
                                                $text = $payload['text'] ?? '#9CA3AF';
                                                $label = $payload['label'] ?? '';
                                                $st = $payload['st'] ?? 'tutup';
                                            ?>
                                            <td rowspan="<?php echo e($cData['rowspan']); ?>"
                                                style="border: 1px solid #E5E7EB; padding: 4px; vertical-align: top; height: 1px;">
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($st === 'tersedia'): ?>
                                                    <?php $hStr = str_pad($jam, 2, '0', STR_PAD_LEFT) . ':00';
                                                        $bgT = '#D1FAE5';
                                                    $borderT = '#6EE7B7'; ?>
                                                    <button type="button"
                                                        @mousedown.prevent="startDrag('<?php echo e($ruang->id); ?>', '<?php echo e(addslashes($ruang->nama)); ?>', '<?php echo e($dateStr); ?>', '<?php echo e($hStr); ?>')"
                                                        @mouseenter="enterDrag('<?php echo e($ruang->id); ?>', '<?php echo e($dateStr); ?>', '<?php echo e($hStr); ?>')"
                                                        @mouseup="stopDrag()"
                                                        @mouseover="!isDragging && ($el.style.background = '#A7F3D0'); !isDragging && ($el.style.transform = 'scale(1.03)')"
                                                        @mouseout="!isDragging && ($el.style.background = '<?php echo e($bgT); ?>'); !isDragging && ($el.style.transform = 'scale(1)')"
                                                        class="select-none"
                                                        :style="isDragging && dragStartPoint?.roomId === '<?php echo e($ruang->id); ?>' && dragStartPoint?.dateStr === '<?php echo e($dateStr); ?>' && dragSelection.includes('<?php echo e($hStr); ?>') 
                                                                                                                                                                                                                            ? 'display:flex; align-items:center; justify-content:center; min-height:41px; height: 100%; width:100%; color:#059669; cursor:pointer; background: #6EE7B7; border: 1px solid #059669; border-radius:6px; transform: scale(1.05); z-index: 10; transition:all 0.15s; opacity: <?php echo e($payload['opacity'] ?? '1'); ?>;' 
                                                                                                                                                                                                                            : 'display:flex; align-items:center; justify-content:center; min-height:41px; height: 100%; width:100%; color:#059669; cursor:pointer; background: <?php echo e($bgT); ?>; border:1px solid <?php echo e($borderT); ?>; border-radius:6px; transition:all 0.15s; opacity: <?php echo e($payload['opacity'] ?? '1'); ?>;'"
                                                        title="Booking Jalur Tol <?php echo e($ruang->nama); ?> — pukul <?php echo e($hStr); ?>">
                                                        <span
                                                            x-show="isDragging && dragStartPoint?.roomId === '<?php echo e($ruang->id); ?>' && dragStartPoint?.dateStr === '<?php echo e($dateStr); ?>' && dragSelection.includes('<?php echo e($hStr); ?>')"
                                                            style="display:none; font-size:12px; font-weight:700;">✓</span>
                                                    </button>
                                                <?php elseif($st === 'event'): ?>
                                                    <div @click.stop="window.dispatchEvent(new CustomEvent('open-event-modal', {
                                                                                                                                                                                                                                                                    detail: {
                                                                                                                                                                                                                                                                        title: '<?php echo e(addslashes($label)); ?>',
                                                                                                                                                                                                                                                                        pengguna: '<?php echo e(addslashes($payload["pengguna"] ?? "")); ?>',
                                                                                                                                                                                                                                                                        ruangan: '<?php echo e(addslashes($ruang->nama)); ?>',
                                                                                                                                                                                                                                                                        tujuan: '<?php echo e(addslashes($payload["tujuan"] ?? "")); ?>',
                                                                                                                                                                                                                                                                        waktu: '<?php echo e(addslashes($payload["waktu"] ?? "")); ?>',
                                                                                                                                                                                                                                                                        type: '<?php echo e(addslashes($payload["type"] ?? "")); ?>',
                                                                                                                                                                                                                                                                        telepon: '<?php echo e(addslashes($payload["telepon"] ?? "-")); ?>'
                                                                                                                                                                                                                                                                    }
                                                                                                                                                                                                                                                                }))"
                                                        style="display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:41px; height: 100%; width:100%; padding:0 4px; overflow:hidden;
                                                                                                                                                                                                                                                                       background:<?php echo e($bg); ?>; border:1px dashed <?php echo e($border); ?>; border-radius:6px;
                                                                                                                                                                                                                                                                       font-size:10px; font-weight:700; color:<?php echo e($text); ?>;
                                                                                                                                                                                                                                                                       cursor:pointer; opacity: <?php echo e($payload['opacity'] ?? '1'); ?>; transition: transform 0.1s;"
                                                        onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'"
                                                        onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none'">
                                                        <?php echo e($label); ?>

                                                    </div>
                                                <?php else: ?>
                                                    <div
                                                        style="height: 100%; width:100%; background: #F9FAFB; border: 1px dashed #D1D5DB; border-radius: 6px;">
                                                    </div>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </td>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>



            
        <?php else: ?>
            
            <div class="flex items-center justify-between mb-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canGoBackMonth): ?>
                    <a href="<?php echo e(request()->fullUrlWithQuery(['month' => $prevMonth])); ?>"
                        class="inline-flex items-center gap-1.5 text-[13px] text-gray-600 hover:text-indigo-600 font-semibold px-3 py-2 rounded-lg hover:bg-indigo-50 transition-colors">
                        ← Bulan Lalu
                    </a>
                <?php else: ?>
                    <div class="px-3 py-2 w-[120px]"></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="text-[15px] font-bold text-gray-800">
                    <?php echo e($monthDate->translatedFormat('F Y')); ?>

                </div>
                <a href="<?php echo e(request()->fullUrlWithQuery(['month' => $nextMonth])); ?>"
                    class="inline-flex items-center gap-1.5 text-[13px] text-gray-600 hover:text-indigo-600 font-semibold px-3 py-2 rounded-lg hover:bg-indigo-50 transition-colors">
                    Bulan Depan →
                </a>
            </div>

            <div class="mp-card overflow-hidden">
                <div class="mp-card-body" style="padding: 20px;">
                    <p class="text-[12px] text-gray-500 mb-4"><strong>Heatmap Aktivitas:</strong> Semakin gelap warnanya,
                        semakin banyak peminjaman di tanggal tersebut.</p>

                    
                    <div style="display:grid; grid-template-columns:repeat(7, 1fr); gap: 4px; margin-bottom: 4px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div style="text-align:center; font-size:11px; font-weight:700; color:#6B7280; padding: 6px 0;">
                                <?php echo e($dayLabel); ?>

                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>

                    
                    <div style="display:grid; grid-template-columns:repeat(7, 1fr); gap: 4px;">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $calendarDays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cell): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cell === null): ?>
                                <div></div>
                            <?php else: ?>
                                <?php
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
                                ?>
                                <a href="<?php echo e($weekLink); ?>"
                                    title="<?php echo e($cell->translatedFormat('d F Y')); ?><?php echo e($isHoliday ? ' (Libur: ' . $holidays[$dateKey] . ')' : ''); ?>"
                                    style="display:block; text-align:center; padding: 10px 6px; border-radius:8px; text-decoration:none;
                                                                                                                                                                                                                                                                                  background: <?php echo e($cellBg); ?>; border: <?php echo e($isToday ? '2px solid #6366F1' : '1px solid #E5E7EB'); ?>;
                                                                                                                                                                                                                                                                                  transition: all 0.15s; <?php echo e($isPast ? 'opacity:0.55;' : ''); ?>"
                                    onmouseover="this.style.transform='scale(1.05)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'"
                                    onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='none'">
                                    <div
                                        style="font-size:13px; font-weight:700; color: <?php echo e($isToday ? '#4338CA' : ($isClosed ? '#9CA3AF' : '#111827')); ?>;">
                                        <?php echo e($cell->format('d')); ?>

                                    </div>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isHoliday): ?>
                                        <div style="font-size:10px; font-weight:600; color:#DC2626; margin-top:3px;">
                                            Libur
                                        </div>
                                    <?php elseif($isClosedWeekend): ?>
                                        <div style="font-size:10px; font-weight:600; color:#9CA3AF; margin-top:3px;">
                                            Tutup
                                        </div>
                                    <?php elseif($count > 0): ?>
                                        <div style="font-size:10px; font-weight:600; color:<?php echo e($countClr); ?>; margin-top:3px;">
                                            <?php echo e($count); ?> booking
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-[12px] text-gray-500 font-medium">
                💡 <strong>Tips:</strong> Klik tanggal manapun untuk beralih ke tampilan <span
                    class="text-indigo-600 font-bold">Mingguan</span> di minggu tersebut secara detail.
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


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
                    class="fixed inset-0 transition-opacity bg-gray-500/20 backdrop-blur-md" @click="open = false">
                </div>
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
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                    </path>
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
                    fetch(`<?php echo e(route('eoffice.peminjaman.admin.kalender-global.search-users')); ?>?q=${encodeURIComponent(this.searchQuery)}`)
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

                    
                    <div
                        class="bg-white px-6 py-5 border-b border-gray-100 flex-shrink-0 flex justify-between items-start">
                        <div>
                            <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">Input Peminjaman</h3>
                            <p class="text-sm text-gray-500 mt-1">Mengunci penjadwalan paksa untuk <span
                                    class="font-semibold text-indigo-600" x-text="ruangan_nama"></span> pada <span
                                    class="font-semibold text-indigo-600"
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

                    
                    <div class="px-6 py-5 overflow-y-auto flex-1 bg-white">
                        <form id="expressBookingForm" method="POST"
                            action="<?php echo e(route('eoffice.peminjaman.admin.kalender-global.express')); ?>">
                            <?php echo csrf_field(); ?>
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
                                            :class="modeAction === 'internal' ? 'bg-indigo-50 border-indigo-500' : 'bg-white border-gray-200 hover:bg-gray-50'">
                                            <input type="radio" name="tipe_aksi" value="internal" x-model="modeAction"
                                                class="hidden">
                                            <div class="font-bold text-sm"
                                                :class="modeAction === 'internal' ? 'text-indigo-700' : 'text-gray-700'">
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
                                    <select name="kategori" x-model="kategoriType" :required="modeAction === 'internal'"
                                        class="mp-input w-full">
                                        <option value="Event / Kegiatan">Event / Kegiatan Mahasiswa</option>
                                        <option value="Rapat Internal">Rapat Internal Dosen</option>
                                        <option value="Ujian / Evaluasi">Ujian / Evaluasi (UTS/UAS)</option>
                                        <option value="Maintenance / Perbaikan">Maintenance / Perbaikan</option>
                                        <option value="Lainnya">Lainnya...</option>
                                    </select>
                                </div>

                                <!-- Academic Metadata Panel (Dynamic) -->
                                <div x-show="kategoriType === 'Jadwal Akademik (Kuliah)' && modeAction === 'internal'"
                                    style="display:none;"
                                    class="bg-indigo-50 border border-indigo-100 p-4 rounded-lg space-y-4 mt-2">
                                    <label
                                        class="block text-[11px] uppercase tracking-wider font-bold text-indigo-800 mb-1.5 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Metadata Akademik Tambahan <span
                                            class="text-indigo-500 font-normal">(Opsional)</span>
                                    </label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block text-[11px] uppercase tracking-wider font-bold text-indigo-700/70 mb-1.5">Mata
                                                Kuliah</label>
                                            <input type="text" name="mata_kuliah"
                                                :required="kategoriType === 'Jadwal Akademik (Kuliah)'"
                                                class="mp-input w-full" placeholder="Nama Lengkap Matkul">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[11px] uppercase tracking-wider font-bold text-indigo-700/70 mb-1.5">Kode
                                                MK</label>
                                            <input type="text" name="kode_mk" class="mp-input w-full"
                                                placeholder="Contoh: TKK102">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-3 gap-4">
                                        <div>
                                            <label
                                                class="block text-[11px] uppercase tracking-wider font-bold text-indigo-700/70 mb-1.5">Kelas</label>
                                            <input type="text" name="kelas" class="mp-input w-full"
                                                placeholder="Misal: A">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[11px] uppercase tracking-wider font-bold text-indigo-700/70 mb-1.5">SKS</label>
                                            <input type="number" name="sks" class="mp-input w-full" placeholder="0-4">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[11px] uppercase tracking-wider font-bold text-indigo-700/70 mb-1.5">Kuota</label>
                                            <input type="number" name="kuota" class="mp-input w-full"
                                                placeholder="Kuota">
                                        </div>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[11px] uppercase tracking-wider font-bold text-indigo-700/70 mb-1.5">Nama
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
                                    <div x-show="isSearching" class="absolute right-3 top-8 text-indigo-500">
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
                                                class="px-4 py-2.5 hover:bg-indigo-50 cursor-pointer border-b border-gray-100 last:border-b-0">
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
                        <button type="button" @click="show = false"
                            class="px-5 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-gray-200">
                            Batal
                        </button>
                        <button type="submit" form="expressBookingForm"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 border border-transparent rounded-lg shadow-sm hover:bg-indigo-700 transition-colors focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Simpan
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

    <script>
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
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal544c4f22773da773738fd88478b17597)): ?>
<?php $attributes = $__attributesOriginal544c4f22773da773738fd88478b17597; ?>
<?php unset($__attributesOriginal544c4f22773da773738fd88478b17597); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal544c4f22773da773738fd88478b17597)): ?>
<?php $component = $__componentOriginal544c4f22773da773738fd88478b17597; ?>
<?php unset($__componentOriginal544c4f22773da773738fd88478b17597); ?>
<?php endif; ?><?php /**PATH C:\WebsiteTekkom - Copy\Modules\EOffice\resources\views\manajemen-ruangan\admin\kalender\index.blade.php ENDPATH**/ ?>