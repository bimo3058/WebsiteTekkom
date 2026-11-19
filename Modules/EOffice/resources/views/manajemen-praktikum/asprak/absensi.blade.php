<x-eoffice::manajemen-praktikum.layout :breadcrumbPraktikum="$praktikum ?? null"
    pageTitle="{{ $praktikum ? $praktikum->nama : 'Belum Ada Praktikum' }} / Absensi & Nilai">
@if(!$praktikum)
        <div class="mp-alert warning flex-shrink-0" style="margin-top:24px;">Anda belum memiliki praktikum aktif.</div>
    @else

        <style>
            .mp-custom-radio {
                appearance: none;
                background-color: #fff;
                margin: 0;
                display: inline-block;
                width: 16px;
                height: 16px;
                border: 1.5px solid #A4ABB8;
                border-radius: 50%;
                position: relative;
                cursor: pointer;
            }

            .mp-custom-radio:checked {
                border-color: #293C79;
            }

            .mp-custom-radio:checked::after {
                content: "";
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 8px;
                height: 8px;
                border-radius: 50%;
                background-color: #293C79;
            }

            /* Table Row Hover Effects */
            tbody tr.mp-tr {
                transition: background 0.2s;
            }

            tbody tr.mp-tr:hover {
                background-color: #F9FAFB;
            }

            tbody tr.mp-tr:hover td.col-tugas {
                background-color: #E0E7FF !important;
            }

            tbody tr.mp-tr:hover td.col-prak {
                background-color: #DCFCE7 !important;
            }

            tbody tr.mp-tr:hover td.col-lap {
                background-color: #FEF9C3 !important;
            }

            tbody tr.mp-tr:hover td.col-resp {
                background-color: #FFEDD5 !important;
            }

            .mp-bulk-grade-cell {
                cursor: cell;
                user-select: none;
            }

            .mp-bulk-grade-cell.is-selected {
                position: relative;
                box-shadow: inset 0 0 0 2px var(--selection-color, #0B266E);
            }

            .mp-bulk-grade-cell.is-selected::after {
                content: "";
                position: absolute;
                inset: 3px;
                border: 1px solid var(--selection-color, #0B266E);
                border-radius: 5px;
                pointer-events: none;
            }

            .col-tugas.is-selected {
                --selection-color: #4338CA;
                background: #C7D2FE !important;
            }

            .col-prak.is-selected {
                --selection-color: #15803D;
                background: #BBF7D0 !important;
            }

            .col-lap.is-selected {
                --selection-color: #A16207;
                background: #FEF08A !important;
            }

            .col-resp.is-selected {
                --selection-color: #C2410C;
                background: #FED7AA !important;
            }

            .attendance-option {
                padding: 6px 8px;
                height: 34px;
                box-sizing: border-box;
                border: 1px solid transparent;
                border-radius: 7px;
                transition: background 0.15s, border-color 0.15s;
                user-select: none;
                line-height: 1;
            }

            .attendance-option .mp-custom-radio {
                flex: 0 0 16px;
                vertical-align: middle;
            }

            .attendance-option > span {
                display: inline-flex;
                align-items: center;
                min-height: 16px;
                line-height: 16px;
            }

            .attendance-option.is-bulk-selected {
                border-color: var(--attendance-selection-color, #0B266E);
                background: transparent;
                box-shadow: 0 0 0 1px var(--attendance-selection-color, #0B266E);
            }

        </style>
        <x-eoffice::manajemen-praktikum.ui.assessment-styles />

        <div x-data="absensiManager()" data-absensi-manager style="row-gap:0!important">
            <x-eoffice::manajemen-praktikum.asprak-header :praktikum="$praktikum" activeTab="absensi">
                @include('eoffice::manajemen-praktikum.asprak.absensi-toolbar')
            </x-eoffice::manajemen-praktikum.asprak-header>

            {{-- Outer card wraps filter row + all accordions --}}
            <div
                style="background:#fff; border:1px solid #DFE1E7; border-top:0;border-radius:0 0 12px 12px; box-shadow:0 1px 3px rgba(0,0,0,0.04); margin-bottom:24px; overflow:hidden;">

                {{-- Module Accordions --}}
                @forelse($modulDiampu as $modul)
                    <div x-data="{ 
                                                                                                    expanded: false,
                                                                                                    page: 1,
                                                                                                    perPage: 10,
                                                                                                    modulId: {{ $modul->id }},
                                                                                                    rows: [],
                                                                                                    isSelecting: false,
                                                                                                    selectionType: null,
                                                                                                    selectionAnchor: null,
                                                                                                    selectionAttendanceChoice: null,
                                                                                                    selectedCells: [],
                                                                                                    bulkModalOpen: false,
                                                                                                    bulkValue: '',
                                                                                                    bulkGradeFields: ['tugas_pendahuluan'],
                                                                                                    bulkSelectedNumbers: '',
                                                                                                    bulkSelectionError: '',
                                                                                                    init() {
                                                                                                        let tempRows = [
                                                                                                            @foreach($daftarPraktikan as $idx => $dp)
                                                                                                                @php
                                                                                                                    $absensi = $dp->absensi->firstWhere('modul_id', $modul->id);
                                                                                                                    $statusAbsen = $absensi?->status ?? '';
                                                                                                                    if (!in_array($statusAbsen, ['hadir', 'terlambat', 'tidak_hadir', 'alpa'])) {
                                                                                                                        $statusAbsen = '';
                                                                                                                    }
                                                                                                                    $ket = addslashes($absensi?->keterangan ?? '');
                                                                                                                    $njMap = $nilaiJenisMap[$modul->id][$dp->id] ?? [];
                                                                                                                    $nameRaw = addslashes($dp->user?->name ?? '');
                                                                                                                    $nimRaw = addslashes($dp->user?->student?->student_number ?? $dp->user?->email ?? '');
                                                                                                                    $emailRaw = addslashes($dp->user?->email ?? '');
                                                                                                                    $avatarUrl = $dp->user?->avatar_url ?? '';
                                                                                                                    $initialsRaw = addslashes(strtoupper(substr($dp->user?->name ?? 'MM', 0, 2)));
                                                                                                                @endphp
                                                                                                                { 
                                                                                                                    id: {{$idx}}, 
                                                                                                                    dp_id: '{{$dp->id}}', 
                                                                                                                    name: String.raw`{!! $nameRaw !!}`, 
                                                                                                                    nim: String.raw`{!! $nimRaw !!}`, 
                                                                                                                    email: String.raw`{!! $emailRaw !!}`,
                                                                                                                    avatar: '{{ $avatarUrl }}',
                                                                                                                    avatar_initials: String.raw`{!! $initialsRaw !!}`,
                                                                                                                    kel: '{{$dp->kelompok ?? "-"}}', 
                                                                                                                    shf: '{{$dp->shift ?? "-"}}',
                                                                                                                    status: '{{ $statusAbsen }}',
                                                                                                                    keterangan: String.raw`{!! $ket !!}`,
@foreach($praktikum->komponen_modul ?? [] as $extra)
{{ $extra['id'] }}: {{ json_encode($njMap[$extra['id']] ?? '') }},
@endforeach
                                                                                                                    tugas_pendahuluan: '{{ $njMap["tugas_pendahuluan"] ?? "" }}',
                                                                                                                    laporan: '{{ $njMap["laporan"] ?? "" }}',
                                                                                                                    responsi: '{{ $njMap["responsi"] ?? "" }}',
                                                                                                                    tugas_pengganti: '{{ $njMap["tugas_pengganti"] ?? "" }}'
                                                                                                                }{{ $loop->last ? '' : ',' }}
                                                                                                            @endforeach
                                                                                                        ];
                                                                                                        this.rows = tempRows;
                                                                                                        $watch('rows', (newVal) => {
                                                                                                            if (!this.expanded) return; 
                                                                                                        });
                                                                                                        this.registerModul(this.modulId, () => this.rows);

                                                                                                        this.$watch('globalSearch', () => { this.page = 1; });
                                                                                                        this.$watch('globalKelompok', () => { this.page = 1; });
                                                                                                        this.$watch('globalShift', () => { this.page = 1; });
                                                                                                        this.$watch('perPage', () => { this.page = 1; });
                                                                                                    },
                                                                                                    get visibleRows() {
                                                                                                        return this.rows.filter(r => 
                                                                                                            (globalSearch === '' || r.name.toLowerCase().includes(globalSearch.toLowerCase()) || r.nim.toLowerCase().includes(globalSearch.toLowerCase())) &&
                                                                                                            (globalKelompok === '' || String(r.kel).toLowerCase().includes(globalKelompok.toLowerCase())) &&
                                                                                                            (globalShift === '' || String(r.shf).toLowerCase().includes(globalShift.toLowerCase()))
                                                                                                        );
                                                                                                    },
                                                                                                    get paginatedRows() {
                                                                                                        let start = (this.page - 1) * this.perPage;
                                                                                                        return this.visibleRows.slice(start, start + parseInt(this.perPage));
                                                                                                    },
                                                                                                    get totalPages() {
                                                                                                        return Math.max(1, Math.ceil(this.visibleRows.length / this.perPage));
                                                                                                    },
                                                                                                    cellKey(row, field) {
                                                                                                        return `${row.dp_id}:${field}`;
                                                                                                    },
                                                                                                    isCellSelected(row, field) {
                                                                                                        const key = this.cellKey(row, field);
                                                                                                        return this.selectedCells.some(cell => cell.key === key);
                                                                                                    },
                                                                                                    startBulkSelection(row, field) {
                                                                                                        this.isSelecting = true;
                                                                                                        this.selectionType = 'grade';
                                                                                                        this.selectionAnchor = { row, field };
                                                                                                        this.updateBulkSelection(row, field);
                                                                                                    },
                                                                                                    updateBulkSelection(row, field) {
                                                                                                        if (!this.isSelecting || this.selectionType !== 'grade' || !this.selectionAnchor) return;

                                                                                                        const fields = {{ json_encode(array_keys($praktikum->moduleWeights())) }};
                                                                                                        const startRow = this.paginatedRows.findIndex(item => item.dp_id === this.selectionAnchor.row.dp_id);
                                                                                                        const endRow = this.paginatedRows.findIndex(item => item.dp_id === row.dp_id);
                                                                                                        const startColumn = fields.indexOf(this.selectionAnchor.field);
                                                                                                        const endColumn = fields.indexOf(field);

                                                                                                        if (startRow === -1 || endRow === -1 || startColumn === -1 || endColumn === -1) return;

                                                                                                        const rowFrom = Math.min(startRow, endRow);
                                                                                                        const rowTo = Math.max(startRow, endRow);
                                                                                                        const columnFrom = Math.min(startColumn, endColumn);
                                                                                                        const columnTo = Math.max(startColumn, endColumn);
                                                                                                        const cells = [];

                                                                                                        for (let rowIndex = rowFrom; rowIndex <= rowTo; rowIndex++) {
                                                                                                            for (let columnIndex = columnFrom; columnIndex <= columnTo; columnIndex++) {
                                                                                                                const selectedRow = this.paginatedRows[rowIndex];
                                                                                                                const selectedField = fields[columnIndex];
                                                                                                                cells.push({
                                                                                                                    key: this.cellKey(selectedRow, selectedField),
                                                                                                                    row: selectedRow,
                                                                                                                    field: selectedField
                                                                                                                });
                                                                                                            }
                                                                                                        }

                                                                                                        this.selectedCells = cells;
                                                                                                    },
                                                                                                    isAttendanceOptionSelected(row, status) {
                                                                                                        return this.selectionAttendanceChoice === status && this.isCellSelected(row, 'status');
                                                                                                    },
                                                                                                    startAttendanceSelection(row, status) {
                                                                                                        this.isSelecting = true;
                                                                                                        this.selectionType = 'attendance';
                                                                                                        this.selectionAttendanceChoice = status;
                                                                                                        this.selectionAnchor = { row, field: 'status', status };
                                                                                                        this.updateAttendanceSelection(row, status);
                                                                                                    },
                                                                                                    updateAttendanceSelection(row, status) {
                                                                                                        if (!this.isSelecting || this.selectionType !== 'attendance' || !this.selectionAnchor) return;
                                                                                                        if (status !== this.selectionAttendanceChoice) return;

                                                                                                        const startRow = this.paginatedRows.findIndex(item => item.dp_id === this.selectionAnchor.row.dp_id);
                                                                                                        const endRow = this.paginatedRows.findIndex(item => item.dp_id === row.dp_id);
                                                                                                        if (startRow === -1 || endRow === -1) return;

                                                                                                        const rowFrom = Math.min(startRow, endRow);
                                                                                                        const rowTo = Math.max(startRow, endRow);
                                                                                                        this.selectedCells = this.paginatedRows
                                                                                                            .slice(rowFrom, rowTo + 1)
                                                                                                            .map(selectedRow => ({
                                                                                                                key: this.cellKey(selectedRow, 'status'),
                                                                                                                row: selectedRow,
                                                                                                                field: 'status'
                                                                                                            }));
                                                                                                    },
                                                                                                    finishBulkSelection() {
                                                                                                        if (!this.isSelecting) return;
                                                                                                        const selectionAnchor = this.selectionAnchor;
                                                                                                        this.isSelecting = false;
                                                                                                        this.selectionAnchor = null;
                                                                                                        if (this.selectedCells.length > 1) {
                                                                                                            if (this.selectionType === 'attendance') {
                                                                                                                this.selectedCells.forEach(cell => { cell.row.status = this.selectionAttendanceChoice; });
                                                                                                                this.markDirty();
                                                                                                                this.selectedCells = [];
                                                                                                                this.selectionAttendanceChoice = null;
                                                                                                            } else {
                                                                                                                const selectedFields = [...new Set(this.selectedCells.map(cell => cell.field))];
                                                                                                                this.bulkGradeFields = selectedFields.length > 0
                                                                                                                    ? selectedFields
                                                                                                                    : [selectionAnchor?.field || 'tugas_pendahuluan'];
                                                                                                                const numbers = [...new Set(this.selectedCells
                                                                                                                    .map(cell => this.visibleRows.findIndex(row => row.dp_id === cell.row.dp_id) + 1)
                                                                                                                    .filter(number => number > 0))]
                                                                                                                    .sort((a, b) => a - b);
                                                                                                                this.bulkSelectedNumbers = this.formatSelectedNumbers(numbers);
                                                                                                                this.bulkValue = '';
                                                                                                                this.bulkSelectionError = '';
                                                                                                                this.bulkModalOpen = true;
                                                                                                                this.$nextTick(() => this.$refs.bulkValueInput?.focus());
                                                                                                            }
                                                                                                        } else {
                                                                                                            this.selectedCells = [];
                                                                                                            this.selectionAttendanceChoice = null;
                                                                                                        }
                                                                                                        this.selectionType = null;
                                                                                                    },
                                                                                                    cancelBulkInput() {
                                                                                                        this.bulkModalOpen = false;
                                                                                                        this.selectionType = null;
                                                                                                        this.selectionAnchor = null;
                                                                                                        this.selectionAttendanceChoice = null;
                                                                                                        this.selectedCells = [];
                                                                                                        this.bulkValue = '';
                                                                                                        this.bulkSelectedNumbers = '';
                                                                                                        this.bulkSelectionError = '';
                                                                                                    },
                                                                                                    formatSelectedNumbers(numbers) {
                                                                                                        if (numbers.length === 0) return '';
                                                                                                        const ranges = [];
                                                                                                        let start = numbers[0];
                                                                                                        let end = numbers[0];

                                                                                                        for (let index = 1; index <= numbers.length; index++) {
                                                                                                            if (numbers[index] === end + 1) {
                                                                                                                end = numbers[index];
                                                                                                                continue;
                                                                                                            }
                                                                                                            ranges.push(start === end ? `${start}` : `${start}\u2013${end}`);
                                                                                                            start = numbers[index];
                                                                                                            end = numbers[index];
                                                                                                        }

                                                                                                        return ranges.join(', ');
                                                                                                    },
                                                                                                    parseSelectedNumbers(value) {
                                                                                                        const selected = new Set();
                                                                                                        const parts = String(value || '').replace(/[\u2013\u2014]/g, '-').split(',').map(part => part.trim()).filter(Boolean);
                                                                                                        if (parts.length === 0) return null;

                                                                                                        for (const part of parts) {
                                                                                                            const rangeMatch = part.match(/^(\d+)\s*-\s*(\d+)$/);
                                                                                                            const singleMatch = part.match(/^\d+$/);
                                                                                                            if (!rangeMatch && !singleMatch) return null;

                                                                                                            const from = rangeMatch ? Number(rangeMatch[1]) : Number(part);
                                                                                                            const to = rangeMatch ? Number(rangeMatch[2]) : from;
                                                                                                            if (from < 1 || to < from || to > this.visibleRows.length) return null;
                                                                                                            for (let number = from; number <= to; number++) selected.add(number);
                                                                                                        }

                                                                                                        return [...selected].sort((a, b) => a - b);
                                                                                                    },
assessmentLabel(field) {
    return this.assessmentOptions().find(option=>option.value===field)?.label || 'Pilih kolom penilaian';
},
assessmentOptions() {
    return {{ json_encode(collect($praktikum->moduleTaskTypes())->map(fn($label,$key)=>['value'=>$key,'label'=>$key==='tugas_pengganti' ? 'Praktikum' : $label])->values()->all()) }};
},
                                                                                                    availableAssessmentOptions(currentIndex) {
                                                                                                        return this.assessmentOptions().filter(option =>
                                                                                                            this.bulkGradeFields[currentIndex] === option.value || !this.bulkGradeFields.includes(option.value)
                                                                                                        );
                                                                                                    },
                                                                                                    addAssessmentField() {
                                                                                                        if (this.bulkGradeFields.length >= this.assessmentOptions().length) return;
                                                                                                        const nextOption = this.assessmentOptions().find(option => !this.bulkGradeFields.includes(option.value));
                                                                                                        if (nextOption) this.bulkGradeFields.push(nextOption.value);
                                                                                                    },
                                                                                                    removeAssessmentField(index) {
                                                                                                        this.bulkGradeFields.splice(index, 1);
                                                                                                        this.bulkSelectionError = '';
                                                                                                    },
                                                                                                    applyBulkValue() {
                                                                                                        this.bulkSelectionError = '';
                                                                                                        const validFields = {{ json_encode(array_keys($praktikum->moduleWeights())) }};
                                                                                                        if (this.bulkGradeFields.length === 0 || this.bulkGradeFields.some(field => !validFields.includes(field))) {
                                                                                                            this.bulkSelectionError = 'Pilih minimal satu kolom penilaian yang valid.';
                                                                                                            return;
                                                                                                        }
                                                                                                        const numbers = this.parseSelectedNumbers(this.bulkSelectedNumbers);
                                                                                                        if (!numbers) {
                                                                                                            this.bulkSelectionError = `Masukkan nomor yang valid antara 1 sampai ${this.visibleRows.length}.`;
                                                                                                            return;
                                                                                                        }
                                                                                                        if (this.bulkValue === '' || this.bulkValue === null) return;
                                                                                                        const value = Number(this.bulkValue);
                                                                                                        if (!Number.isFinite(value) || value < 0 || value > 100) return;
                                                                                                        numbers.forEach(number => {
                                                                                                            this.bulkGradeFields.forEach(field => {
                                                                                                                this.visibleRows[number - 1][field] = value;
                                                                                                            });
                                                                                                        });
                                                                                                        this.markDirty();
                                                                                                        this.bulkModalOpen = false;
                                                                                                        this.selectedCells = [];
                                                                                                        this.bulkValue = '';
                                                                                                        this.bulkSelectedNumbers = '';
                                                                                                        this.bulkSelectionError = '';
                                                                                                    },
                                                                                                    kelRowspan(id) {
                                                                                                        let pr = this.paginatedRows;
                                                                                                        let vId = pr.findIndex(r => r.id === id);
                                                                                                        if (vId === -1) return 0;
                                                                                                        if (vId > 0 && pr[vId - 1].kel === pr[vId].kel) return 0;
                                                                                                        let count = 1;
                                                                                                        for (let i = vId + 1; i < pr.length; i++) {
                                                                                                            if (pr[i].kel === pr[vId].kel) count++; else break;
                                                                                                        }
                                                                                                        return count;
                                                                                                    },
                                                                                                    shfRowspan(id) {
                                                                                                        let pr = this.paginatedRows;
                                                                                                        let vId = pr.findIndex(r => r.id === id);
                                                                                                        if (vId === -1) return 0;
                                                                                                        if (vId > 0 && pr[vId - 1].shf === pr[vId].shf) return 0;
                                                                                                        let count = 1;
                                                                                                        for (let i = vId + 1; i < pr.length; i++) {
                                                                                                            if (pr[i].shf === pr[vId].shf) count++; else break;
                                                                                                        }
                                                                                                        return count;
                                                                                                    }
                                                                                                }"
                        @mouseup.window="finishBulkSelection()"
                        style="border-bottom:1px solid #DFE1E7;">
                        <div style="padding:16px 20px; cursor:pointer; display:flex; justify-content:space-between; align-items:center;"
                            @click="expanded = !expanded" onmouseover="this.style.background='#F9FAFB'"
                            onmouseout="this.style.background='transparent'">
                            <div>
                                <div style="font-size:15px; font-weight:700; color:#0D0D12;">{{ $modul->nama }}</div>
                                <div style="font-size:13px; color:#666D80; margin-top:3px;">Asisten:
                                    {{ $modul->asprak->pluck('user.name')->join(', ') ?: '-' }}
                                </div>
                            </div>
                            <svg :style="expanded ? 'transform:rotate(180deg)' : ''" style="transition:transform .2s;"
                                width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2"
                                stroke-linecap="round">
                                <polyline points="6 9 12 15 18 9" />
                            </svg>
                        </div>

                        <x-eoffice::manajemen-praktikum.ui.grade-accordion state="expanded" :separator="true">
                            <div style="overflow-x:auto;">
                                <table class="mp-table" style="min-width:1100px; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background:#F9FAFB;">
                                            <th class="mp-th text-left" style="padding:12px 16px;width:50px;">NO</th>
                                            <th class="mp-th text-left" style="padding:12px 16px;width:280px;">NAMA MAHASISWA
                                            </th>
                                            <th class="mp-th text-left" style="padding:12px 16px;width:120px;">NIM</th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:100px;">KELOMPOK
                                            </th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:100px;">SHIFT</th>
                                            <th class="mp-th text-center" style="padding:12px 16px;width:240px;text-align:center;">KEHADIRAN</th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:95px;border-left:1px solid #DFE1E7;background:#EEF2FF;color:#4338CA;text-align: center;">
                                                TUGAS PENDAHULUAN<br><span
                                                    style="font-size:11px;font-weight:600;">{{ $praktikum->bobot_tp ?? 10 }}%</span>
                                            </th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:95px;background:#F0FDF4;color:#15803D;text-align: center;">
                                                PRAKTIKUM<br><span
                                                    style="font-size:11px;font-weight:600;">{{ $praktikum->bobot_praktikum ?? 30 }}%</span>
                                            </th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:95px;background:#FEFCE8;color:#A16207;text-align: center;">
                                                {{ mb_strtoupper($praktikum->laporanLabel()) }}<br><span
                                                    style="font-size:11px;font-weight:600;">{{ $praktikum->bobot_laporan ?? 30 }}%</span>
                                            </th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:95px;border-right:1px solid #DFE1E7;background:#FFF7ED;color:#C2410C;text-align: center;">
                                                RESPONSI<br><span
                                                    style="font-size:11px;font-weight:600;">{{ $praktikum->bobot_responsi ?? 30 }}%</span>
                                            </th>
                                            @include('eoffice::manajemen-praktikum.partials.module-extra-headers', ['extraMode'=>'asprak'])
                                            <th class="mp-th text-left" style="padding:12px 16px;">KETERANGAN</th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:80px;border-left:1px solid #DFE1E7;">RATA-RATA
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(r, index) in paginatedRows" :key="modulId + '-' + r.dp_id">
                                            <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">

                                                <td style="padding:12px 16px; font-size:13px; color:#353849;font-weight:700;text-align:center;">
                                                    <span x-text="(page - 1) * perPage + index + 1"></span>
                                                </td>

                                                <td style="padding:12px 16px;">
                                                    <div style="display:flex; align-items:center; gap:12px;">
                                                        <template x-if="r.avatar">
                                                            <img :src="r.avatar" alt=""
                                                                style="width:32px;height:32px;object-fit:cover;border-radius:50%;flex-shrink:0;">
                                                        </template>
                                                        <template x-if="!r.avatar">
                                                            <div class="mp-av sky"
                                                                style="width:32px;height:32px;font-size:13px;display:flex;align-items:center;justify-content:center;border-radius:50%;flex-shrink:0;font-weight:700;"
                                                                x-text="r.avatar_initials"></div>
                                                        </template>
                                                        <div>
                                                            <div style="font-size:13px; font-weight:600; color:#0D0D12; text-transform:capitalize;"
                                                                x-text="r.name"></div>
                                                            <div style="font-size:12px; color:#666D80; margin-top:2px;"
                                                                x-text="r.email"></div>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td style="padding:12px 16px; font-size:12px; font-weight:600; color:#353849; text-transform:uppercase;"
                                                    x-text="r.nim"></td>

                                                <td x-show="kelRowspan(r.id) > 0" :rowspan="kelRowspan(r.id)"
                                                    style="padding:12px 16px; font-size:15px; font-weight:700; color:#0D0D12; text-align:center; vertical-align:middle;">
                                                    <span x-text="r.kel"></span>
                                                </td>

                                                <td x-show="shfRowspan(r.id) > 0" :rowspan="shfRowspan(r.id)"
                                                    style="padding:12px 16px; font-size:15px; font-weight:700; color:#0D0D12; text-align:center; vertical-align:middle;">
                                                    <span x-text="r.shf"></span>
                                                </td>

                                                {{-- Kehadiran --}}
                                                <td style="padding:12px 16px; font-size:13px; text-align:center;">
                                                    <div
                                                        style="display:flex; flex-direction:row; justify-content:center; align-items:center; gap:12px; white-space:nowrap;">
                                                        <label class="attendance-option"
                                                            :class="{ 'is-bulk-selected': isAttendanceOptionSelected(r, 'hadir') }"
                                                            :style="isAttendanceOptionSelected(r, 'hadir') ? '--attendance-selection-color:#070E2A;' : ''"
                                                            @mousedown="startAttendanceSelection(r, 'hadir')"
                                                            @mouseenter="updateAttendanceSelection(r, 'hadir')"
                                                            style="display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
                                                            <input type="radio" value="hadir" x-model="r.status"
                                                                @click="r.status === 'hadir' ? (r.status = null, markDirty()) : markDirty()"
                                                                :name="'status_' + modulId + '_' + r.dp_id"
                                                                class="mp-custom-radio">
                                                            <span
                                                                style="font-weight:500; font-size:13px; color:#353849;">Hadir</span>
                                                        </label>
                                                        <label class="attendance-option"
                                                            :class="{ 'is-bulk-selected': isAttendanceOptionSelected(r, 'terlambat') }"
                                                            :style="isAttendanceOptionSelected(r, 'terlambat') ? '--attendance-selection-color:#070E2A;' : ''"
                                                            @mousedown="startAttendanceSelection(r, 'terlambat')"
                                                            @mouseenter="updateAttendanceSelection(r, 'terlambat')"
                                                            style="display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
                                                            <input type="radio" value="terlambat" x-model="r.status"
                                                                @click="r.status === 'terlambat' ? (r.status = null, markDirty()) : markDirty()"
                                                                :name="'status_' + modulId + '_' + r.dp_id"
                                                                class="mp-custom-radio">
                                                            <span
                                                                style="font-weight:500; font-size:13px; color:#353849;">Terlambat</span>
                                                        </label>
                                                        <label class="attendance-option"
                                                            :class="{ 'is-bulk-selected': isAttendanceOptionSelected(r, 'tidak_hadir') }"
                                                            :style="isAttendanceOptionSelected(r, 'tidak_hadir') ? '--attendance-selection-color:#070E2A;' : ''"
                                                            @mousedown="startAttendanceSelection(r, 'tidak_hadir')"
                                                            @mouseenter="updateAttendanceSelection(r, 'tidak_hadir')"
                                                            style="display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
                                                            <input type="radio" value="tidak_hadir" x-model="r.status"
                                                                @click="r.status === 'tidak_hadir' ? (r.status = null, markDirty()) : markDirty()"
                                                                :name="'status_' + modulId + '_' + r.dp_id"
                                                                class="mp-custom-radio">
                                                            <span
                                                                style="font-weight:500; font-size:13px; color:#353849;">Tidak Hadir</span>
                                                        </label>
                                                        <label class="attendance-option"
                                                            :class="{ 'is-bulk-selected': isAttendanceOptionSelected(r, 'alpa') }"
                                                            :style="isAttendanceOptionSelected(r, 'alpa') ? '--attendance-selection-color:#070E2A;' : ''"
                                                            @mousedown="startAttendanceSelection(r, 'alpa')"
                                                            @mouseenter="updateAttendanceSelection(r, 'alpa')"
                                                            style="display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
                                                            <input type="radio" value="alpa" x-model="r.status"
                                                                @click="r.status === 'alpa' ? (r.status = null, markDirty()) : markDirty()"
                                                                :name="'status_' + modulId + '_' + r.dp_id"
                                                                class="mp-custom-radio">
                                                            <span
                                                                style="font-weight:500; font-size:13px; color:#353849;">Alpa</span>
                                                        </label>
                                                    </div>
                                                </td>

                                                {{-- Nilai inputs --}}
                                                <td class="mp-grade-history-cell col-tugas mp-bulk-grade-cell" :class="{ 'is-selected': isCellSelected(r, 'tugas_pendahuluan') }"
                                                    @mousedown="startBulkSelection(r, 'tugas_pendahuluan')"
                                                    @mouseenter="updateBulkSelection(r, 'tugas_pendahuluan')"
                                                    style="position:relative;padding-right:32px!important;padding:12px 16px; border-left:1px solid #DFE1E7; text-align:center; background:#EEF2FF; transition: background 0.2s;">
                                                    <x-eoffice::manajemen-praktikum.ui.grade-score-input :integer="true" model="r.tugas_pendahuluan" label="Nilai tugas pendahuluan" :disabled="!$praktikum->is_active" />
                                                <x-eoffice::manajemen-praktikum.ui.module-score-history :module-id="$modul->id" client-student="r.dp_id" score-key="tugas_pendahuluan" score-label="Tugas Pendahuluan" /></td>
                                                <td class="mp-grade-history-cell col-prak mp-bulk-grade-cell" :class="{ 'is-selected': isCellSelected(r, 'tugas_pengganti') }"
                                                    @mousedown="startBulkSelection(r, 'tugas_pengganti')"
                                                    @mouseenter="updateBulkSelection(r, 'tugas_pengganti')"
                                                    style="position:relative;padding-right:32px!important;padding:12px 16px; text-align:center; background:#F0FDF4; transition: background 0.2s;">
                                                    <x-eoffice::manajemen-praktikum.ui.grade-score-input :integer="true" model="r.tugas_pengganti" label="Nilai tugas pengganti" :disabled="!$praktikum->is_active" />
                                                <x-eoffice::manajemen-praktikum.ui.module-score-history :module-id="$modul->id" client-student="r.dp_id" score-key="tugas_pengganti" score-label="Praktikum" /></td>
                                                <td class="mp-grade-history-cell col-lap mp-bulk-grade-cell" :class="{ 'is-selected': isCellSelected(r, 'laporan') }"
                                                    @mousedown="startBulkSelection(r, 'laporan')"
                                                    @mouseenter="updateBulkSelection(r, 'laporan')"
                                                    style="position:relative;padding-right:32px!important;padding:12px 16px; text-align:center; background:#FEFCE8; transition: background 0.2s;">
                                                    <x-eoffice::manajemen-praktikum.ui.grade-score-input :integer="true" model="r.laporan" label="Nilai laporan" :disabled="!$praktikum->is_active" />
                                                <x-eoffice::manajemen-praktikum.ui.module-score-history :module-id="$modul->id" client-student="r.dp_id" score-key="laporan" :score-label="$praktikum->laporanLabel()" /></td>
                                                <td class="mp-grade-history-cell col-resp mp-bulk-grade-cell" :class="{ 'is-selected': isCellSelected(r, 'responsi') }"
                                                    @mousedown="startBulkSelection(r, 'responsi')"
                                                    @mouseenter="updateBulkSelection(r, 'responsi')"
                                                    style="position:relative;padding-right:32px!important;padding:12px 16px; border-right:1px solid #DFE1E7; text-align:center; background:#FFF7ED; transition: background 0.2s;">
                                                    <x-eoffice::manajemen-praktikum.ui.grade-score-input :integer="true" model="r.responsi" label="Nilai responsi" :disabled="!$praktikum->is_active" />
                                                <x-eoffice::manajemen-praktikum.ui.module-score-history :module-id="$modul->id" client-student="r.dp_id" score-key="responsi" score-label="Responsi" /></td>

                                                @include('eoffice::manajemen-praktikum.partials.module-extra-cells', ['extraMode'=>'asprak'])
                                                <td style="padding:12px 16px;">
                                                    <input type="text" x-model="r.keterangan" @input="markDirty()"
                                                        placeholder="Opsional..."
                                                        style="width:100px; padding:6px 8px; border:1px solid #DFE1E7; border-radius:6px; font-size:13px; background:#fff;">
                                                </td>

                                                <td class="group-hover:bg-gray-50 transition-all"
                                                    style="padding:12px 16px;text-align:center;color:#0D0D12;font-size:13px;font-weight:600;border-left:1px solid #DFE1E7;"
                                                    x-data="{
                                                                                                get rataRata() {
                                                                                                    let b_tp = {{ $praktikum->bobot_tp ?? 10 }};
                                                                                                    let b_prak = {{ $praktikum->bobot_praktikum ?? 30 }};
                                                                                                    let b_lap = {{ $praktikum->bobot_laporan ?? 30 }};
                                                                                                    let b_resp = {{ $praktikum->bobot_responsi ?? 30 }};
const extraWeights = {{ json_encode(collect($praktikum->komponen_modul ?? [])->pluck('bobot','id')->all()) }};
let totalBobot = b_tp + b_prak + b_lap + b_resp + Object.values(extraWeights).reduce((sum,w)=>sum+Number(w||0),0);
if (totalBobot !== 100) return '—';
                                                                                                    let sum = (b_tp * (r.tugas_pendahuluan || 0)) + (b_prak * (r.tugas_pengganti || 0)) + (b_lap * (r.laporan || 0)) + (b_resp * (r.responsi || 0));
sum += Object.entries(extraWeights).reduce((sum,[key,w])=>sum+Number(r[key]||0)*Number(w||0),0);
                                                                                                    return (sum / totalBobot).toFixed(2).replace('.',',');
                                                                                                }
                                                                                            }">
                                                    <span x-text="rataRata"></span>
                                                </td>

                                            </tr>
                                        </template>
                                        <template x-if="visibleRows.length === 0">
                                            <tr>
                                                <td colspan="{{ 11 + count($praktikum->komponen_modul ?? []) }}"
                                                    style="padding:24px; text-align:center; font-size:13px; color:#666D80; background:#fff;">
                                                    Data tidak ditemukan.
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <x-eoffice::manajemen-praktikum.ui.client-pagination />

                            <template x-teleport="body">
                                <div x-show="bulkModalOpen" x-cloak
                                    @keydown.escape.window="cancelBulkInput()"
                                    @click.self="cancelBulkInput()"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    style="position:fixed; inset:0; z-index:1000; display:block; background:rgba(15,23,42,0.55);">
                                    <div
                                        style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:calc(100% - 40px); max-width:480px; background:#fff; border:1px solid #DFE1E7; border-radius:14px; box-shadow:0 20px 50px rgba(15,23,42,0.22); overflow:visible;">
                                        <div style="padding:20px 22px; border-bottom:1px solid #DFE1E7; display:flex; justify-content:space-between; align-items:flex-start; gap:16px;">
                                            <div>
                                                <div style="font-size:18px; font-weight:700; color:#0D0D12;">Isi Nilai Terpilih</div>
                                                <div style="font-size:13px; color:#666D80; margin-top:4px;">
                                                    Atur kolom penilaian, nomor mahasiswa, dan nilai yang akan diterapkan.
                                                </div>
                                            </div>
                                        </div>
                                        <form @submit.prevent="applyBulkValue()">
                                            <div style="padding:22px; display:flex; flex-direction:column; gap:18px;">
                                                <div>
                                                    <label style="display:block; font-size:13px; font-weight:600; color:#353849; margin-bottom:8px;">Kolom Penilaian <span style="color:#E11D48;">*</span></label>
                                                    <div style="display:flex; flex-direction:column; gap:8px;">
                                                        <template x-for="(field, fieldIndex) in bulkGradeFields" :key="fieldIndex">
                                                            <div x-data="{ assessmentOpen: false }" style="position:relative; display:flex; align-items:flex-start; gap:10px;">
                                                                <div class="mp-assessment-field">
                                                                    <div role="button" tabindex="0" class="mp-assessment-trigger"
                                                                        :class="{ 'is-open': assessmentOpen }"
                                                                        @click="assessmentOpen = !assessmentOpen"
                                                                        @keydown.enter.prevent="assessmentOpen = !assessmentOpen"
                                                                        @keydown.space.prevent="assessmentOpen = !assessmentOpen"
                                                                        >
                                                                        <span x-text="assessmentLabel(field)"></span>
                                                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                            >
                                                                            <polyline points="6 9 12 15 18 9"></polyline>
                                                                        </svg>
                                                                    </div>
                                                                    <div x-show="assessmentOpen" x-cloak class="mp-assessment-menu"
                                                                        @click.outside="assessmentOpen = false"
                                                                        x-transition:enter="transition ease-out duration-150"
                                                                        x-transition:enter-start="opacity-0 -translate-y-1"
                                                                        x-transition:enter-end="opacity-100 translate-y-0">
                                                                        <template x-for="option in availableAssessmentOptions(fieldIndex)" :key="option.value">
                                                                            <div role="button" tabindex="0" class="mp-assessment-option"
                                                                                :class="{ 'is-selected': field === option.value }"
                                                                                @click="bulkGradeFields[fieldIndex] = option.value; assessmentOpen = false"
                                                                                @keydown.enter.prevent="bulkGradeFields[fieldIndex] = option.value; assessmentOpen = false"
                                                                                >
                                                                                <span x-text="option.label"></span>
                                                                                <template x-if="field === option.value">
                                                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0B266E" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                                                    </svg>
                                                                                </template>
                                                                            </div>
                                                                        </template>
                                                                    </div>
                                                                </div>
                                                                <button type="button" @click="removeAssessmentField(fieldIndex)" aria-label="Hapus kolom penilaian"
                                                                    style="appearance:none; width:46px; height:46px; flex:0 0 46px; display:flex; align-items:center; justify-content:center; border:1px solid #FECDD3; border-radius:9px; background:#FFF1F2; color:#E11D48; cursor:pointer;" class="mp-delete-button" title="Hapus"><x-eoffice::manajemen-praktikum.ui.icon name="trash" :size="14" /></button>
                                                            </div>
                                                        </template>
                                                    </div>
                                                    <template x-if="bulkGradeFields.length < assessmentOptions().length">
                                                        <div role="button" class="mp-assessment-add" tabindex="0"
                                                            @click="addAssessmentField()"
                                                            @keydown.enter.prevent="addAssessmentField()"
                                                            @keydown.space.prevent="addAssessmentField()">
                                                            <span>+</span>
                                                            <span>Tambah Kolom Penilaian</span>
                                                        </div>
                                                    </template>
                                                    <div style="margin-top:6px; text-align:right; font-size:12px; color:#666D80;">Maksimal {{ count($praktikum->moduleWeights()) }} kolom penilaian</div>
                                                    <div x-show="bulkGradeFields.length === 0" style="margin-top:7px; font-size:12px; color:#BE123C;">Tambahkan minimal satu kolom penilaian.</div>
                                                    </div>

                                                <div>
                                                    <label style="display:block; font-size:13px; font-weight:600; color:#353849; margin-bottom:8px;">No Dipilih <span style="color:#E11D48;">*</span></label>
                                                    <input type="text" x-model="bulkSelectedNumbers" @input="bulkSelectionError = ''"
                                                        required placeholder="Contoh: 1-10 atau 1-5, 8, 10-12"
                                                        class="mp-input" style="width:100%; height:44px; font-size:13px;">
                                                    <div style="font-size:12px; color:#666D80; margin-top:7px;">Gunakan tanda hubung untuk rentang dan koma untuk nomor terpisah.</div>
                                                </div>

                                                <div>
                                                <label style="display:block; font-size:13px; font-weight:600; color:#353849; margin-bottom:8px;">Nilai <span style="color:#E11D48;">*</span></label>
                                                <input x-ref="bulkValueInput" type="number" x-model="bulkValue"
                                                    min="0" max="100" step="0.01" required placeholder="Masukkan nilai 0-100"
                                                    class="mp-input" style="width:100%; height:44px; font-size:13px;">
                                                <div style="font-size:12px; color:#666D80; margin-top:7px;">Gunakan angka antara 0 sampai 100.</div>
                                                </div>

                                                <div x-show="bulkSelectionError" x-text="bulkSelectionError"
                                                    style="padding:10px 12px; border:1px solid #FECDD3; border-radius:8px; background:#FFF1F2; color:#BE123C; font-size:12px;"></div>
                                            </div>
                                            <div class="mp-modal-footer" style="border-radius:0 0 14px 14px;">
                                                <button type="button" class="mp-btn secondary" @click="cancelBulkInput()"
                                                    style="height:38px; padding:0 16px; border:1px solid #DFE1E7; border-radius:8px; background:#fff; color:#353849; font-size:13px; font-weight:600;">Batal</button>
                                                <button type="submit" class="mp-btn"
                                                    style="height:38px; padding:0 16px; border:1px solid #0B266E; border-radius:8px; background:#0B266E; color:#fff; font-size:13px; font-weight:600;">Terapkan Nilai</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </template>

                        </x-eoffice::manajemen-praktikum.ui.grade-accordion>
                    </div>
                @empty
                    <div style="padding:48px 24px; text-align:center;">
                        <x-eoffice::manajemen-praktikum.ui.empty-icon name="medal" />
                        <div style="font-size:14px; font-weight:500; color:#353849;">Belum ada modul.</div>
                        <div style="font-size:13px; color:#666D80; margin-top:4px;">Anda belum di-assign modul apapun.</div>
                    </div>
                @endforelse
                <x-eoffice::manajemen-praktikum.ui.tugas-akhir-card :praktikum="$praktikum" :students="$daftarPraktikan" role="asprak" section="nilai" />
                <x-eoffice::manajemen-praktikum.ui.final-grades :praktikum="$praktikum" role="asprak" :embedded="true" />
            </div>
            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('absensiManager', () => ({
                        globalSearch: '',
                        globalKelompok: '',
                        globalShift: '',
                        moduleInitial: {},extraSavers:{},
                        snapshot(rows){return JSON.stringify(rows,(_,v)=>typeof v==='number'||(typeof v==='string' && /^\d+(\.\d*)?$/.test(v))?String(Number(v)):v)},
                        get isDirty(){return Object.entries(this.modulesDataRetrievers).some(([id,getRows])=>this.snapshot(getRows())!==this.moduleInitial[id]) || Object.values(this.extraSavers).some(s=>s.dirty())},
                        registerExtraSave(id,dirty,save){this.extraSavers[id]={dirty,save}},
                        isSaving: false,
                        modulesDataRetrievers: {},

                        registerModul(modulId, getDataFn) {
                            this.modulesDataRetrievers[modulId] = getDataFn;
                            this.moduleInitial[modulId]=this.snapshot(getDataFn());
                        },

                        markDirty() {
                            // Dirty state is derived from current values, including reverted edits.
                        },

                        async simpanSemua() {
                            if (!this.isDirty || this.isSaving) return;
                            const invalid=(this.$el.closest('[data-absensi-manager]')||this.$root).querySelector('.mp-grade-score:invalid');if(invalid){invalid.reportValidity();invalid.focus();return;}
                            this.isSaving=true;
                            try {
                                const jobs=[];
                                const fields=@js(array_keys($praktikum->moduleTaskTypes()));
                                for(const [id,getRows] of Object.entries(this.modulesDataRetrievers)){
                                    const rows=getRows();if(this.snapshot(rows)===this.moduleInitial[id])continue;
                                    const baseline=JSON.parse(this.moduleInitial[id]);
                                    const payload={tanggal:new Date().toLocaleDateString('en-CA'),absensi:{},nilai:{}};
                                    for(const row of rows){
                                        const old=baseline.find(r=>r.dp_id===row.dp_id);
                                        if(this.snapshot([row.status||'',row.keterangan||''])!==this.snapshot([old.status||'',old.keterangan||'']))payload.absensi[row.dp_id]={status:row.status||null,keterangan:row.keterangan||''};
                                        const values={};for(const key of fields)if(this.snapshot([row[key]])!==this.snapshot([old[key]]))values[key]=row[key]===''||row[key]==null?null:row[key];
                                        if(Object.keys(values).length)payload.nilai[row.dp_id]=values;
                                    }
                                    if(!Object.keys(payload.absensi).length && !Object.keys(payload.nilai).length)continue;
                                    jobs.push(async()=>{const res=await fetch(`{{ url('/eoffice/manprak/asprak/absensi') }}/${id}`,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify(payload)});if(!res.ok){const data=await res.json().catch(()=>({}));throw new Error(Object.values(data.errors||{}).flat().join(' ')||data.message||'Gagal menyimpan nilai modul.')}this.moduleInitial[id]=this.snapshot(rows)});
                                }
                                jobs.push(...Object.values(this.extraSavers).filter(s=>s.dirty()).map(s=>()=>s.save()));
                                for(const save of jobs)await save();
                                window.location.reload();
                            } catch(error){alert(error.message||'Gagal menyimpan data.');this.isSaving=false;}
                        }
                    }))
                });
            </script>
        </div>{{-- /x-data --}}

    @endif


</x-eoffice::manajemen-praktikum.layout>
