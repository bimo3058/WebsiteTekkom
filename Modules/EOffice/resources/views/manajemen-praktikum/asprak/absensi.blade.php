<x-eoffice::manajemen-praktikum.layout
    pageTitle="{{ $praktikum ? $praktikum->nama : 'Belum Ada Praktikum' }} / Absensi & Nilai">
    @if(isset($praktikum) && $praktikum)
        <x-eoffice::manajemen-praktikum.asprak-header :praktikum="$praktikum" activeTab="absensi" />
    @endif

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

            .mp-assessment-field {
                position: relative !important;
                flex: 1 1 auto !important;
                min-width: 0 !important;
            }

            .mp-assessment-trigger {
                box-sizing: border-box !important;
                width: 100% !important;
                height: 46px !important;
                padding: 0 14px !important;
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
                gap: 12px !important;
                border: 1px solid #DFE1E7 !important;
                border-radius: 9px !important;
                background: #FFFFFF !important;
                color: #0D0D12 !important;
                font-size: 13px !important;
                line-height: 18px !important;
                text-align: left !important;
                cursor: pointer !important;
            }

            .mp-assessment-trigger.is-open {
                border-color: #0B266E !important;
                background: #F4F6FC !important;
                box-shadow: 0 0 0 1px #0B266E !important;
            }

            .mp-assessment-trigger > span {
                display: block !important;
                min-width: 0 !important;
                font-size: 13px !important;
                line-height: 18px !important;
            }

            .mp-assessment-trigger > svg {
                display: block !important;
                flex: 0 0 16px !important;
                margin-left: auto !important;
                transform: rotate(0deg) !important;
                transition: transform 0.2s ease !important;
            }

            .mp-assessment-trigger.is-open > svg {
                transform: rotate(180deg) !important;
            }

            .mp-assessment-menu {
                position: absolute !important;
                z-index: 30 !important;
                top: calc(100% + 6px) !important;
                left: 0 !important;
                right: 0 !important;
                padding: 6px !important;
                background: #FFFFFF !important;
                border: 1px solid #DFE1E7 !important;
                border-radius: 9px !important;
                box-shadow: 0 10px 30px rgba(15, 23, 42, 0.14) !important;
            }

            .mp-assessment-option {
                box-sizing: border-box !important;
                width: 100% !important;
                min-height: 38px !important;
                padding: 10px 12px !important;
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
                gap: 12px !important;
                border-radius: 7px !important;
                background: #FFFFFF !important;
                color: #353849 !important;
                font-size: 13px !important;
                line-height: 18px !important;
                text-align: left !important;
                cursor: pointer !important;
            }

            .mp-assessment-option.is-selected {
                background: #EEF1FA !important;
                color: #0B266E !important;
                font-weight: 600 !important;
            }

            .mp-assessment-option > span {
                display: block !important;
                font-size: 13px !important;
                line-height: 18px !important;
            }

            .mp-assessment-option > svg {
                display: block !important;
                flex: 0 0 15px !important;
                margin-left: auto !important;
            }

            .mp-assessment-add {
                box-sizing: border-box !important;
                width: 100% !important;
                height: 48px !important;
                margin-top: 10px !important;
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: center !important;
                gap: 8px !important;
                border: 1px dashed #D8DCE5 !important;
                border-radius: 9px !important;
                background: #FCFCFD !important;
                color: #0B266E !important;
                font-size: 13px !important;
                line-height: 18px !important;
                font-weight: 600 !important;
            }

            .mp-assessment-add > span:first-child {
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-size: 21px !important;
                line-height: 1 !important;
                font-weight: 400 !important;
            }
        </style>

        <div x-data="absensiManager()">

            {{-- Outer card wraps filter row + all accordions --}}
            <div
                style="background:#fff; border:1px solid #DFE1E7; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.04); margin-bottom:24px; overflow:hidden;">

                {{-- Filter Header Row --}}
                <div
                    style="padding:16px 20px; display:flex; flex-direction:row; flex-wrap:nowrap; align-items:center; justify-content:space-between; gap:16px; border-bottom:1px solid #DFE1E7;">

                    <div style="font-size: 15px; font-weight: 700; color: #0D0D12; white-space: nowrap; flex-shrink: 0;">
                        Absensi &amp; Nilai
                    </div>

                    <div
                        style="display: flex; flex-direction: row; flex-wrap: nowrap; align-items: center; gap: 10px; flex-shrink: 0;">

                        <div style="position: relative;">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round"
                                style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); pointer-events:none;">
                                <circle cx="11" cy="11" r="8" />
                                <line x1="21" y1="21" x2="16.65" y2="16.65" />
                            </svg>
                            <input type="text" x-model="globalSearch" placeholder="Cari nama / NIM..." class="mp-input"
                                style="padding-left: 32px; height: 36px; width: 210px; font-size: 13px;">
                        </div>

                        <div class="relative" x-data="{ openK: false }" @click.away="openK = false">
                            <div @click="openK = !openK"
                                style="height:36px; min-width:150px; font-size:13px; padding:0 12px; background:#fff; border:1px solid #DFE1E7; border-radius:8px; display:flex; align-items:center; justify-content:space-between; cursor:pointer; color:#353849;">
                                <span
                                    x-text="globalKelompok === '' ? 'Semua Kelompok' : 'Kelompok ' + globalKelompok"></span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#666D80"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    :style="openK ? 'transform:rotate(180deg)' : ''"
                                    style="transition:transform 0.2s; margin-left:8px;">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </div>
                            <div x-show="openK"
                                style="display:none; position:absolute; top:40px; left:0; width:180px; background:#fff; border:1px solid #DFE1E7; border-radius:8px; box-shadow:0 4px 6px rgba(0,0,0,0.05); z-index:50;">
                                <div style="padding:8px; border-bottom:1px solid #F3F4F6;">
                                    <input type="text" x-model="globalKelompok" placeholder="Cari kelompok..."
                                        style="width:100%; border:1px solid #DFE1E7; border-radius:6px; padding:6px 10px; font-size:12px; outline:none; box-sizing:border-box;">
                                </div>
                                <div style="max-height:200px; overflow-y:auto; padding:4px 0;">
                                    <div @click="globalKelompok = ''; openK = false;"
                                        class="hover:bg-gray-50 transition-colors"
                                        style="padding:8px 12px; font-size:12px; cursor:pointer; display:flex; justify-content:space-between; align-items:center;"
                                        :style="globalKelompok === '' ? 'background:#EEF2FF; color:#0B266E;' : 'color:#353849;'">
                                        <span style="font-size:13px; margin-left:8px; flex:1;">Semua Kelompok</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="relative" x-data="{ openS: false }" @click.away="openS = false">
                            <div @click="openS = !openS"
                                style="height:36px; min-width:130px; font-size:13px; padding:0 12px; background:#fff; border:1px solid #DFE1E7; border-radius:8px; display:flex; align-items:center; justify-content:space-between; cursor:pointer; color:#353849;">
                                <span x-text="globalShift === '' ? 'Semua Shift' : 'Shift ' + globalShift"></span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#666D80"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    :style="openS ? 'transform:rotate(180deg)' : ''"
                                    style="transition:transform 0.2s; margin-left:8px;">
                                    <polyline points="6 9 12 15 18 9" />
                                </svg>
                            </div>
                            <div x-show="openS"
                                style="display:none; position:absolute; top:40px; left:0; width:160px; background:#fff; border:1px solid #DFE1E7; border-radius:8px; box-shadow:0 4px 6px rgba(0,0,0,0.05); z-index:50;">
                                <div style="padding:8px; border-bottom:1px solid #F3F4F6;">
                                    <input type="text" x-model="globalShift" placeholder="Cari shift..."
                                        style="width:100%; border:1px solid #DFE1E7; border-radius:6px; padding:6px 10px; font-size:12px; outline:none; box-sizing:border-box;">
                                </div>
                                <div style="max-height:200px; overflow-y:auto; padding:4px 0;">
                                    <div @click="globalShift = ''; openS = false;"
                                        class="hover:bg-gray-50 transition-colors"
                                        style="padding:8px 12px; font-size:13px; cursor:pointer; display:flex; justify-content:space-between; align-items:center; gap:8px;"
                                        :style="globalShift === '' ? 'background:#EEF2FF; color:#0B266E;' : 'color:#353849;'">
                                        <span style="font-size:13px; flex:1; margin-left:8px;">Semua Shift</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Simpan Button --}}
                        <button type="button" @click="simpanSemua" :disabled="!isDirty || isSaving"
                            :class="isDirty && !isSaving ? 'bg-[#0B266E] text-[#fff] hover:bg-[#081b4d]' : 'bg-[#F9FAFB] text-[#0B266E] border-[#DFE1E7]'"
                            style="height: 36px; padding: 0 16px; border-radius: 8px; border: 1px solid transparent; box-shadow: 0 1px 2px rgba(0,0,0,0.05); display: inline-flex; align-items: center; justify-content: center; gap: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;">
                            <span x-text="isSaving ? 'Menyimpan...' : 'Simpan'"></span>
                        </button>

                        <a href="{{ route('eoffice.manprak.asprak.absensi.export-csv') }}"
                            style="height: 36px; padding: 0 14px; background: #fff; border: 1px solid #DFE1E7; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); display: inline-flex; align-items: center; gap: 6px; text-decoration: none; white-space: nowrap; font-size: 13px; font-weight: 600; color: #353849; cursor: pointer;"
                            onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#FFF'">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round">
                                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4" />
                                <polyline points="7 10 12 15 17 10" />
                                <line x1="12" y1="15" x2="12" y2="3" />
                            </svg>
                            Download CSV
                        </a>

                    </div>
                </div>

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

                                                                                                        const fields = ['tugas_pendahuluan', 'tugas_pengganti', 'laporan', 'responsi'];
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
                                                                                                        return {
                                                                                                            tugas_pendahuluan: 'Tugas Pendahuluan',
                                                                                                            tugas_pengganti: 'Praktikum',
                                                                                                            laporan: 'Laporan',
                                                                                                            responsi: 'Responsi'
                                                                                                        }[field] || 'Pilih kolom penilaian';
                                                                                                    },
                                                                                                    assessmentOptions() {
                                                                                                        return [
                                                                                                            { value: 'tugas_pendahuluan', label: 'Tugas Pendahuluan' },
                                                                                                            { value: 'tugas_pengganti', label: 'Praktikum' },
                                                                                                            { value: 'laporan', label: 'Laporan' },
                                                                                                            { value: 'responsi', label: 'Responsi' }
                                                                                                        ];
                                                                                                    },
                                                                                                    availableAssessmentOptions(currentIndex) {
                                                                                                        return this.assessmentOptions().filter(option =>
                                                                                                            this.bulkGradeFields[currentIndex] === option.value || !this.bulkGradeFields.includes(option.value)
                                                                                                        );
                                                                                                    },
                                                                                                    addAssessmentField() {
                                                                                                        if (this.bulkGradeFields.length >= 4) return;
                                                                                                        const nextOption = this.assessmentOptions().find(option => !this.bulkGradeFields.includes(option.value));
                                                                                                        if (nextOption) this.bulkGradeFields.push(nextOption.value);
                                                                                                    },
                                                                                                    removeAssessmentField(index) {
                                                                                                        this.bulkGradeFields.splice(index, 1);
                                                                                                        this.bulkSelectionError = '';
                                                                                                    },
                                                                                                    applyBulkValue() {
                                                                                                        this.bulkSelectionError = '';
                                                                                                        const validFields = ['tugas_pendahuluan', 'tugas_pengganti', 'laporan', 'responsi'];
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

                        <div x-show="expanded" style="display:none; border-top:1px solid #DFE1E7;" x-transition>
                            <div style="overflow-x:auto;">
                                <table class="mp-table" style="min-width:1100px; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background:#F9FAFB;">
                                            <th class="mp-th text-left" style="padding:12px 16px;width:50px;">NO</th>
                                            <th class="mp-th text-left" style="padding:12px 16px;width:280px;">NAMA MAHASISWA
                                            </th>
                                            <th class="mp-th text-left" style="padding:12px 16px;width:120px;">NIM</th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:100px;border-left:1px solid #DFE1E7;">KELOMPOK
                                            </th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:100px;border-right:1px solid #DFE1E7;">SHIFT</th>
                                            <th class="mp-th text-center" style="padding:12px 16px;width:240px;">Kehadiran</th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:95px;border-left:1px solid #DFE1E7;background:#EEF2FF;color:#4338CA;text-align: center;">
                                                Tugas Pendahuluan<br><span
                                                    style="font-size:11px;font-weight:600;">{{ $praktikum->bobot_tp ?? 10 }}%</span>
                                            </th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:95px;background:#F0FDF4;color:#15803D;text-align: center;">
                                                Praktikum<br><span
                                                    style="font-size:11px;font-weight:600;">{{ $praktikum->bobot_praktikum ?? 30 }}%</span>
                                            </th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:95px;background:#FEFCE8;color:#A16207;text-align: center;">
                                                Laporan<br><span
                                                    style="font-size:11px;font-weight:600;">{{ $praktikum->bobot_laporan ?? 30 }}%</span>
                                            </th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:95px;border-right:1px solid #DFE1E7;background:#FFF7ED;color:#C2410C;text-align: center;">
                                                Responsi<br><span
                                                    style="font-size:11px;font-weight:600;">{{ $praktikum->bobot_responsi ?? 30 }}%</span>
                                            </th>
                                            <th class="mp-th text-left" style="padding:12px 16px;">Keterangan</th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:80px;border-left:1px solid #DFE1E7;">Rata-rata
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(r, index) in paginatedRows" :key="modulId + '-' + r.dp_id">
                                            <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;">

                                                <td style="padding:12px 16px; font-size:13px; color:#353849;">
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
                                                    style="padding:12px 16px; font-size:15px; font-weight:700; color:#0D0D12; text-align:center; border-left:1px solid #DFE1E7; border-right:1px solid #DFE1E7; vertical-align:middle;">
                                                    <span x-text="r.kel"></span>
                                                </td>

                                                <td x-show="shfRowspan(r.id) > 0" :rowspan="shfRowspan(r.id)"
                                                    style="padding:12px 16px; font-size:15px; font-weight:700; color:#0D0D12; text-align:center; border-right:1px solid #DFE1E7; vertical-align:middle;">
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
                                                <td class="col-tugas mp-bulk-grade-cell" :class="{ 'is-selected': isCellSelected(r, 'tugas_pendahuluan') }"
                                                    @mousedown="startBulkSelection(r, 'tugas_pendahuluan')"
                                                    @mouseenter="updateBulkSelection(r, 'tugas_pendahuluan')"
                                                    style="padding:12px 16px; border-left:1px solid #DFE1E7; text-align:center; background:#EEF2FF; transition: background 0.2s;">
                                                    <input type="number" min="0" max="100" step="0.01"
                                                        x-model="r.tugas_pendahuluan" @input="markDirty()"
                                                        style="width:70px; padding:6px 8px; border:1px solid #DFE1E7; border-radius:6px; font-size:13px; text-align:center; background:#fff;">
                                                </td>
                                                <td class="col-prak mp-bulk-grade-cell" :class="{ 'is-selected': isCellSelected(r, 'tugas_pengganti') }"
                                                    @mousedown="startBulkSelection(r, 'tugas_pengganti')"
                                                    @mouseenter="updateBulkSelection(r, 'tugas_pengganti')"
                                                    style="padding:12px 16px; text-align:center; background:#F0FDF4; transition: background 0.2s;">
                                                    <input type="number" min="0" max="100" step="0.01"
                                                        x-model="r.tugas_pengganti" @input="markDirty()"
                                                        style="width:70px; padding:6px 8px; border:1px solid #DFE1E7; border-radius:6px; font-size:13px; text-align:center; background:#fff;">
                                                </td>
                                                <td class="col-lap mp-bulk-grade-cell" :class="{ 'is-selected': isCellSelected(r, 'laporan') }"
                                                    @mousedown="startBulkSelection(r, 'laporan')"
                                                    @mouseenter="updateBulkSelection(r, 'laporan')"
                                                    style="padding:12px 16px; text-align:center; background:#FEFCE8; transition: background 0.2s;">
                                                    <input type="number" min="0" max="100" step="0.01" x-model="r.laporan"
                                                        @input="markDirty()"
                                                        style="width:70px; padding:6px 8px; border:1px solid #DFE1E7; border-radius:6px; font-size:13px; text-align:center; background:#fff;">
                                                </td>
                                                <td class="col-resp mp-bulk-grade-cell" :class="{ 'is-selected': isCellSelected(r, 'responsi') }"
                                                    @mousedown="startBulkSelection(r, 'responsi')"
                                                    @mouseenter="updateBulkSelection(r, 'responsi')"
                                                    style="padding:12px 16px; border-right:1px solid #DFE1E7; text-align:center; background:#FFF7ED; transition: background 0.2s;">
                                                    <input type="number" min="0" max="100" step="0.01" x-model="r.responsi"
                                                        @input="markDirty()"
                                                        style="width:70px; padding:6px 8px; border:1px solid #DFE1E7; border-radius:6px; font-size:13px; text-align:center; background:#fff;">
                                                </td>

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
                                                                                                    let totalBobot = b_tp + b_prak + b_lap + b_resp;
                                                                                                    if (totalBobot === 0) return 0;
                                                                                                    let sum = (b_tp * (r.tugas_pendahuluan || 0)) + (b_prak * (r.tugas_pengganti || 0)) + (b_lap * (r.laporan || 0)) + (b_resp * (r.responsi || 0));
                                                                                                    return (sum / totalBobot).toFixed(0);
                                                                                                }
                                                                                            }">
                                                    <span x-text="rataRata"></span>
                                                </td>

                                            </tr>
                                        </template>
                                        <template x-if="visibleRows.length === 0">
                                            <tr>
                                                <td colspan="11"
                                                    style="padding:24px; text-align:center; font-size:13px; color:#666D80; background:#fff;">
                                                    Tidak ada data ditemukan.
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <div
                                style="display:flex; justify-content:space-between; align-items:center; width:100%; padding:12px 20px; border-top:1px solid #DFE1E7; box-sizing:border-box; flex-wrap:nowrap; gap:16px;">
                                {{-- Left: Per halaman dropdown + info teks --}}
                                <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                                    <div x-data="{ openP: false, options: [10, 20, 30] }" class="relative"
                                        @click.away="openP = false">
                                        <div class="flex items-center gap-2 cursor-pointer border rounded-[8px] px-3 py-1.5 transition-colors focus:outline-none"
                                            :class="openP ? 'border-[#0B266E] bg-[#EEF1FA] text-[#0B266E]' : 'border-[#DFE1E7] bg-white text-[#353849] hover:bg-[#F6F8FA]'"
                                            @click="openP = !openP">
                                            <span class="text-[12px] text-[#666D80]" :class="openP ? 'text-[#0B266E]' : ''">Per
                                                halaman</span>
                                            <div class="flex items-center gap-1 font-semibold text-[12px]">
                                                <span x-text="perPage"></span>
                                                <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                                    :class="{'rotate-180': openP, 'text-[#0B266E]': openP, 'text-[#666D80]': !openP}"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="6 9 12 15 18 9"></polyline>
                                                </svg>
                                            </div>
                                        </div>
                                        <div x-show="openP" @click.away="openP = false" style="display: none;"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="opacity-100 scale-100"
                                            x-transition:leave-end="opacity-0 scale-95"
                                            class="absolute bottom-full left-0 mb-2 z-10 w-full min-w-[80px] bg-white border border-[#DFE1E7] rounded-[10px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] py-2 px-1.5 flex flex-col">
                                            <template x-for="option in options" :key="option">
                                                <div @click="perPage = option; openP = false; page = 1;"
                                                    class="flex items-center justify-between px-3 py-2 rounded-[6px] cursor-pointer text-[12px] transition-colors mb-0.5 last:mb-0 no-underline"
                                                    :class="perPage == option ? 'bg-[#F6F8FA] text-[#0B266E] font-medium' : 'text-[#353849] hover:bg-[#F6F8FA]'">
                                                    <span x-text="option"></span>
                                                    <svg x-show="perPage == option"
                                                        class="w-3.5 h-3.5 flex-shrink-0 text-[#0B266E] ml-2"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                        <polyline points="20 6 9 17 4 12"></polyline>
                                                    </svg>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    <div style="font-size:12px; color:#666D80;">
                                        Menampilkan <span
                                            x-text="visibleRows.length === 0 ? 0 : ((page - 1) * perPage) + 1"></span> sampai
                                        <span x-text="Math.min(page * perPage, visibleRows.length)"></span> dari <span
                                            x-text="visibleRows.length"></span> data
                                    </div>
                                </div>

                                {{-- Right: standard pagination buttons --}}
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <button type="button" @click="if(page > 1) page--" :disabled="page === 1"
                                        style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; border:1px solid #DFE1E7; cursor:pointer;"
                                        :style="page === 1 ? 'background:#F8FAFC; color:#A4ABB8; cursor:not-allowed;' : 'background:white; color:#353849;'"
                                        :class="page === 1 ? '' : 'hover:bg-gray-50'">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="15 18 9 12 15 6"></polyline>
                                        </svg>
                                    </button>

                                    <span
                                        style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:#0B266E; color:white; font-size:13px; font-weight:600;"
                                        x-text="page"></span>

                                    <button type="button" @click="if(page < totalPages) page++"
                                        :disabled="page === totalPages || totalPages === 0"
                                        style="display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; border:1px solid #DFE1E7; cursor:pointer;"
                                        :style="page === totalPages || totalPages === 0 ? 'background:#F8FAFC; color:#A4ABB8; cursor:not-allowed;' : 'background:white; color:#353849;'"
                                        :class="page === totalPages || totalPages === 0 ? '' : 'hover:bg-gray-50'">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </button>
                                </div>
                            </div>

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
                                                                    style="appearance:none; width:46px; height:46px; flex:0 0 46px; display:flex; align-items:center; justify-content:center; border:1px solid #FECDD3; border-radius:9px; background:#FFF1F2; color:#E11D48; cursor:pointer;">
                                                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                                        <polyline points="3 6 5 6 21 6"></polyline>
                                                                        <path d="M19 6l-1 14H6L5 6m3 0V4h8v2"></path>
                                                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </template>
                                                    </div>
                                                    <template x-if="bulkGradeFields.length < 4">
                                                        <div role="button" class="mp-assessment-add" tabindex="0"
                                                            @click="addAssessmentField()"
                                                            @keydown.enter.prevent="addAssessmentField()"
                                                            @keydown.space.prevent="addAssessmentField()">
                                                            <span>+</span>
                                                            <span>Tambah Kolom Penilaian</span>
                                                        </div>
                                                    </template>
                                                    <div style="margin-top:6px; text-align:right; font-size:12px; color:#666D80;">Maksimal 4 kolom penilaian</div>
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
                                            <div style="padding:16px 22px; border-top:1px solid #DFE1E7; border-radius:0 0 14px 14px; display:flex; justify-content:flex-end; gap:10px; background:#F9FAFB;">
                                                <button type="button" class="mp-btn" @click="cancelBulkInput()"
                                                    style="height:38px; padding:0 16px; border:1px solid #DFE1E7; border-radius:8px; background:#fff; color:#353849; font-size:13px; font-weight:600;">Batal</button>
                                                <button type="submit" class="mp-btn"
                                                    style="height:38px; padding:0 16px; border:1px solid #0B266E; border-radius:8px; background:#0B266E; color:#fff; font-size:13px; font-weight:600;">Terapkan Nilai</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </div>
                @empty
                    <div style="padding:48px 24px; text-align:center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 12px; display:block;">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                        <div style="font-size:14px; font-weight:500; color:#353849;">Belum ada modul.</div>
                        <div style="font-size:13px; color:#666D80; margin-top:4px;">Anda belum di-assign modul apapun.</div>
                    </div>
                @endforelse

            </div>

            <script>
                document.addEventListener('alpine:init', () => {
                    Alpine.data('absensiManager', () => ({
                        globalSearch: '',
                        globalKelompok: '',
                        globalShift: '',
                        isDirty: false,
                        isSaving: false,
                        modulesDataRetrievers: {},

                        registerModul(modulId, getDataFn) {
                            this.modulesDataRetrievers[modulId] = getDataFn;
                        },

                        markDirty() {
                            this.isDirty = true;
                        },

                        async simpanSemua() {
                            if (!this.isDirty || this.isSaving) return;
                            this.isSaving = true;

                            let currentDate = new Date().toISOString().split('T')[0];
                            let promises = [];

                            for (const [modulId, getDataFn] of Object.entries(this.modulesDataRetrievers)) {
                                let rows = getDataFn();
                                let payload = {
                                    _token: '{{ csrf_token() }}',
                                    tanggal: currentDate,
                                    absensi: {},
                                    nilai: {}
                                };

                                rows.forEach(row => {
                                    if (row.status) {
                                        payload.absensi[row.dp_id] = {
                                            status: row.status,
                                            keterangan: row.keterangan || ''
                                        };
                                    }
                                    payload.nilai[row.dp_id] = {
                                        tugas_pendahuluan: row.tugas_pendahuluan || null,
                                        tugas_pengganti: row.tugas_pengganti || null,
                                        laporan: row.laporan || null,
                                        responsi: row.responsi || null
                                    };
                                });
                                // Only post if there is minimum 1 absen (not totally empty initially)
                                if (Object.keys(payload.absensi).length === 0) continue;

                                promises.push(
                                    fetch(`/eoffice/manprak/asprak/absensi/${modulId}`, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify(payload)
                                    }).then(res => {
                                        if (!res.ok) throw new Error("Failed to save modul " + modulId);
                                    })
                                );
                            }

                            try {
                                await Promise.all(promises);
                                this.isDirty = false;
                                window.location.reload();
                            } catch (e) {
                                alert("Terjadi kesalahan saat menyimpan data. Silakan coba lagi.");
                                console.error(e);
                                this.isSaving = false;
                            }
                        }
                    }))
                });
            </script>
        </div>{{-- /x-data --}}

    @endif
</x-eoffice::manajemen-praktikum.layout>
