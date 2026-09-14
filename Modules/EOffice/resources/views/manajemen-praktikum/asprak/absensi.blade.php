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
                                                        <label
                                                            style="display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
                                                            <input type="radio" value="hadir" x-model="r.status"
                                                                @click="r.status === 'hadir' ? (r.status = null, markDirty()) : markDirty()"
                                                                :name="'status_' + modulId + '_' + r.dp_id"
                                                                class="mp-custom-radio">
                                                            <span
                                                                style="font-weight:500; font-size:13px; color:#353849;">Hadir</span>
                                                        </label>
                                                        <label
                                                            style="display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
                                                            <input type="radio" value="terlambat" x-model="r.status"
                                                                @click="r.status === 'terlambat' ? (r.status = null, markDirty()) : markDirty()"
                                                                :name="'status_' + modulId + '_' + r.dp_id"
                                                                class="mp-custom-radio">
                                                            <span
                                                                style="font-weight:500; font-size:13px; color:#353849;">Terlambat</span>
                                                        </label>
                                                        <label
                                                            style="display:inline-flex; align-items:center; gap:6px; cursor:pointer;">
                                                            <input type="radio" value="tidak_hadir" x-model="r.status"
                                                                @click="r.status === 'tidak_hadir' ? (r.status = null, markDirty()) : markDirty()"
                                                                :name="'status_' + modulId + '_' + r.dp_id"
                                                                class="mp-custom-radio">
                                                            <span
                                                                style="font-weight:500; font-size:13px; color:#353849;">Tidak Hadir</span>
                                                        </label>
                                                        <label
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
                                                <td class="col-tugas"
                                                    style="padding:12px 16px; border-left:1px solid #DFE1E7; text-align:center; background:#EEF2FF; transition: background 0.2s;">
                                                    <input type="number" min="0" max="100" step="0.01"
                                                        x-model="r.tugas_pendahuluan" @input="markDirty()"
                                                        style="width:70px; padding:6px 8px; border:1px solid #DFE1E7; border-radius:6px; font-size:13px; text-align:center; background:#fff;">
                                                </td>
                                                <td class="col-prak"
                                                    style="padding:12px 16px; text-align:center; background:#F0FDF4; transition: background 0.2s;">
                                                    <input type="number" min="0" max="100" step="0.01"
                                                        x-model="r.tugas_pengganti" @input="markDirty()"
                                                        style="width:70px; padding:6px 8px; border:1px solid #DFE1E7; border-radius:6px; font-size:13px; text-align:center; background:#fff;">
                                                </td>
                                                <td class="col-lap"
                                                    style="padding:12px 16px; text-align:center; background:#FEFCE8; transition: background 0.2s;">
                                                    <input type="number" min="0" max="100" step="0.01" x-model="r.laporan"
                                                        @input="markDirty()"
                                                        style="width:70px; padding:6px 8px; border:1px solid #DFE1E7; border-radius:6px; font-size:13px; text-align:center; background:#fff;">
                                                </td>
                                                <td class="col-resp"
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
                                        praktikum: row.praktikum || null,
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