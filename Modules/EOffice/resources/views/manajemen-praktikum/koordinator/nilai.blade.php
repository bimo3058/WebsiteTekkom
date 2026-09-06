<x-eoffice::manajemen-praktikum.layout
    pageTitle="{{ $praktikum ? $praktikum->nama : 'Belum Ada Praktikum' }} / Absensi & Nilai Modul">
    @if(isset($praktikum) && $praktikum)
        <x-eoffice::manajemen-praktikum.koor-header :praktikum="$praktikum" />
    @endif

    @php
        /** @var \Modules\EOffice\Models\Praktikum|null $praktikum */
        /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Modul[] $allModuls */
        /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\Modul[] $moduls */
        /** @var \Illuminate\Database\Eloquent\Collection|\Modules\EOffice\Models\DaftarPraktikan[] $daftarPraktikan */
    @endphp

    @if(!$praktikum)
        <div class="mp-alert warning flex-shrink-0" style="margin-top:24px;">Anda belum memiliki praktikum aktif.</div>
    @else

        <div x-data="{ globalSearch: '', globalKelompok: '', globalShift: '' }">

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

                        <div class="relative" x-data="{ openK: false, searchK: '' }" @click.away="openK = false">
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
                                    <input type="text" x-model="searchK" placeholder="Cari kelompok..."
                                        style="width:100%; border:1px solid #DFE1E7; border-radius:6px; padding:6px 10px; font-size:12px; outline:none; box-sizing:border-box;">
                                </div>
                                <div style="max-height:200px; overflow-y:auto; padding:4px 0;">
                                    <div @click="globalKelompok = ''; openK = false; searchK = ''"
                                        class="hover:bg-gray-50 transition-colors"
                                        style="padding:8px 12px; font-size:12px; cursor:pointer; display:flex; justify-content:space-between; align-items:center;"
                                        :style="globalKelompok === '' ? 'background:#EEF2FF; color:#0B266E;' : 'color:#353849;'">
                                        <span style="font-size:13px; margin-left:8px; flex:1;">Semua Kelompok</span>
                                    </div>
                                    @php $kels = $daftarPraktikan->pluck('kelompok')->filter()->unique()->sort(); @endphp
                                    @foreach($kels as $k)
                                        <div x-show="String('{{ $k }}').toLowerCase().includes(searchK.toLowerCase())"
                                            @click="globalKelompok = '{{ $k }}'; openK = false; searchK = ''"
                                            class="hover:bg-gray-50 transition-colors"
                                            style="padding:8px 12px; font-size:11px; cursor:pointer; display:flex; justify-content:space-between; align-items:center;"
                                            :style="globalKelompok === '{{ $k }}' ? 'background:#EEF2FF; color:#0B266E;' : 'color:#353849;'">
                                            <span
                                                style="font-size:13px; flex:1; min-width:0; margin-left:8px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $k }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="relative" x-data="{ openS: false, searchS: '' }" @click.away="openS = false">
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
                                    <input type="text" x-model="searchS" placeholder="Cari shift..."
                                        style="width:100%; border:1px solid #DFE1E7; border-radius:6px; padding:6px 10px; font-size:12px; outline:none; box-sizing:border-box;">
                                </div>
                                <div style="max-height:200px; overflow-y:auto; padding:4px 0;">
                                    <div @click="globalShift = ''; openS = false; searchS = ''"
                                        class="hover:bg-gray-50 transition-colors"
                                        style="padding:8px 12px; font-size:13px; cursor:pointer; display:flex; justify-content:space-between; align-items:center; gap:8px;"
                                        :style="globalShift === '' ? 'background:#EEF2FF; color:#0B266E;' : 'color:#353849;'">

                                        <span style="font-size:13px; flex:1; margin-left:8px;">Semua Shift</span>
                                    </div>
                                    @php $shfs = $daftarPraktikan->pluck('shift')->filter()->unique()->sort(); @endphp
                                    @foreach($shfs as $s)
                                        <div x-show="String('{{ $s }}').toLowerCase().includes(searchS.toLowerCase())"
                                            @click="globalShift = '{{ $s }}'; openS = false; searchS = ''"
                                            class="hover:bg-gray-50 transition-colors"
                                            style="padding:8px 12px; font-size:11px; cursor:pointer; display:flex; justify-content:space-between; align-items:center;"
                                            :style="globalShift === '{{ $s }}' ? 'background:#EEF2FF; color:#0B266E;' : 'color:#353849;'">
                                            <span
                                                style="font-size:13px; margin-left:8px; flex:1; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $s }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('eoffice.manprak.koor.nilai.export-csv') }}"
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
                @forelse($allModuls as $modul)
                    <div x-data="{ 
                                                                                                                                                                                                                            expanded: false,
                                                                                                                                                                                                                            page: 1,
                                                                                                                                                                                                                            perPage: 10,
                                                                                                                                                                                                                            rows: [
                                                                                                                                                                                                                                @foreach($daftarPraktikan as $idx => $dp)
                                                                                                                                                                                                                                    { id: {{ $idx }}, name: @js(strtolower($dp->user?->name ?? '')), nim: @js(strtolower($dp->user?->student?->student_number ?? $dp->user?->email ?? '')), kel: @js($dp->kelompok ?? ''), shf: @js($dp->shift ?? '') }{{ $loop->last ? '' : ',' }}
                                                                                                                                                                                                                                @endforeach
                                                                                                                                                                                                                            ],
                                                                                                                                                                                                                            init() {
                                                                                                                                                                                                                                this.$watch('globalSearch', () => { this.page = 1; });
                                                                                                                                                                                                                                this.$watch('globalKelompok', () => { this.page = 1; });
                                                                                                                                                                                                                                this.$watch('globalShift', () => { this.page = 1; });
                                                                                                                                                                                                                                this.$watch('perPage', () => { this.page = 1; });
                                                                                                                                                                                                                            },
                                                                                                                                                                                                                            get visibleRows() {
                                                                                                                                                                                                                                return this.rows.filter(r => 
                                                                                                                                                                                                                                    (globalSearch === '' || r.name.includes(globalSearch.toLowerCase()) || r.nim.includes(globalSearch.toLowerCase())) &&
                                                                                                                                                                                                                                    (globalKelompok === '' || globalKelompok === r.kel) &&
                                                                                                                                                                                                                                    (globalShift === '' || globalShift === r.shf)
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
                                <table class="mp-table" style="min-width:1100px;">
                                    <thead>
                                        <tr style="background:#F9FAFB;">
                                            <th class="mp-th text-left" style="padding:12px 16px;width:50px;">NO</th>
                                            <th class="mp-th text-left" style="padding:12px 16px;width:250px;">NAMA MAHASISWA
                                            </th>
                                            <th class="mp-th text-left" style="padding:12px 16px;width:150px;">NIM</th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:100px;border-left:1px solid #DFE1E7;">KELOMPOK
                                            </th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:100px;border-right:1px solid #DFE1E7;">SHIFT</th>
                                            <th class="mp-th text-center" style="padding:12px 16px;width:100px;">Kehadiran</th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:100px;border-left:1px solid #DFE1E7;background:#EEF2FF;color:#4338CA;">
                                                Tugas Pendahuluan</th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:100px;background:#FEFCE8;color:#A16207;">Laporan
                                            </th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:100px;background:#FFF7ED;color:#C2410C;">
                                                Responsi</th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:100px;border-right:1px solid #DFE1E7;background:#F0FDF4;color:#15803D;">
                                                Tugas Pengganti
                                            </th>
                                            <th class="mp-th text-left" style="padding:12px 16px;">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($daftarPraktikan as $idx => $dp)
                                            @php
                                                $absensi = $dp->absensi->firstWhere('modul_id', $modul->id);
                                                $statusAbsen = $absensi?->status;
                                                $njMap = $nilaiJenisMap[$modul->id][$dp->id] ?? [];
                                            @endphp
                                            <tr class="mp-tr" style="border-bottom:1px solid #DFE1E7;"
                                                x-show="paginatedRows.some(r => r.id === {{ $idx }})">
                                                <td style="padding:12px 16px; color:#666D80; font-size:13px;">{{ $idx + 1 }}</td>
                                                <td style="padding:12px 16px;">
                                                    <div style="display:flex;align-items:center;gap:10px;">
                                                        <div class="mp-av sky"
                                                            style="width:28px;height:28px;font-size:11px;flex-shrink:0;">
                                                            {{ strtoupper(substr($dp->user?->name ?? 'M', 0, 2)) }}
                                                        </div>
                                                        <div>
                                                            <div style="font-weight:600;color:#0D0D12;font-size:13px;">
                                                                {{ $dp->user?->name ?? '-' }}
                                                            </div>
                                                            <div style="font-size:11.5px;color:#666D80;margin-top:2px;">
                                                                {{ $dp->user?->email ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="padding:12px 16px; color:#666D80; font-size:12px;font-weight:600;">
                                                    {{ $dp->user?->student?->student_number ?? $dp->user?->email ?? '-' }}
                                                </td>
                                                <td x-show="kelRowspan({{ $idx }}) > 0" :rowspan="kelRowspan({{ $idx }})"
                                                    style="padding:12px 16px;text-align:center;font-size:15px;font-weight:700;color:#0D0D12;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;vertical-align:middle;">
                                                    {{ $dp->kelompok ?? '—' }}
                                                </td>
                                                <td x-show="shfRowspan({{ $idx }}) > 0" :rowspan="shfRowspan({{ $idx }})"
                                                    style="padding:12px 16px;text-align:center;font-size:15px;font-weight:700;color:#0D0D12;border-right:1px solid #DFE1E7;vertical-align:middle;">
                                                    {{ $dp->shift ?? '—' }}
                                                </td>
                                                <td style="padding:12px 16px;text-align:center;">
                                                    @if($statusAbsen === 'hadir')
                                                        <span class="mp-badge success sm"
                                                            style="background:#ECFDF5; color:#10B981;">Hadir</span>
                                                    @elseif($statusAbsen === 'izin')
                                                        <span class="mp-badge sky sm"
                                                            style="background:#EFF6FF; color:#3B82F6;">Izin</span>
                                                    @elseif($statusAbsen === 'tidak_hadir')
                                                        <span class="mp-badge danger sm"
                                                            style="background:#FEF2F2; color:#EF4444;">Alpha</span>
                                                    @else
                                                        <span style="color:#A4ABB8; font-size:12px;">—</span>
                                                    @endif
                                                </td>
                                                {{-- Tugas Pendahuluan --}}
                                                <td
                                                    style="padding:12px 16px;text-align:center;background:#EAF0FA;font-weight:700;color:#4338CA;font-size:13px;">
                                                    {{ isset($njMap['tugas_pendahuluan']) ? number_format($njMap['tugas_pendahuluan'], 1) : '—' }}
                                                </td>
                                                {{-- Laporan --}}
                                                <td
                                                    style="padding:12px 16px;text-align:center;background:#FEF9C3;font-weight:700;color:#A16207;font-size:13px;">
                                                    {{ isset($njMap['laporan']) ? number_format($njMap['laporan'], 1) : '—' }}
                                                </td>
                                                {{-- Responsi --}}
                                                <td
                                                    style="padding:12px 16px;text-align:center;background:#FFF7ED;font-weight:700;color:#C2410C;font-size:13px;">
                                                    {{ isset($njMap['responsi']) ? number_format($njMap['responsi'], 1) : '—' }}
                                                </td>
                                                {{-- Tugas Pengganti --}}
                                                <td
                                                    style="padding:12px 16px;text-align:center;background:#F0FDF4;font-weight:700;color:#15803D;font-size:13px;">
                                                    {{ isset($njMap['tugas_pengganti']) ? number_format($njMap['tugas_pengganti'], 1) : '—' }}
                                                </td>
                                                {{-- Keterangan --}}
                                                <td style="padding:12px 16px;font-size:12px;color:#666D80;">
                                                    {{ $absensi?->keterangan ?? '—' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11"
                                                    style="padding:32px; text-align:center; color:#666D80; font-size:13px;">Belum
                                                    ada data praktikan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination Footer --}}
                            <div
                                :style="visibleRows.length > 0 ? 'display:flex; justify-content:space-between; align-items:center; width:100%; padding:12px 20px; border-top:1px solid #DFE1E7; box-sizing:border-box; flex-wrap:nowrap; gap:16px;' : 'display:none;'">
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
                                                <div @click="perPage = option; openP = false"
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
                                    <div style="font-size:13px; color:#666D80;">Menampilkan <span
                                            x-text="((page - 1) * perPage) + 1"></span>
                                        sampai <span x-text="Math.min(page * perPage, visibleRows.length)"></span> dari <span
                                            x-text="visibleRows.length"></span> data</div>
                                </div>

                                {{-- Right: Page nav buttons --}}
                                <div style="display:flex; gap:4px; flex-shrink:0;">
                                    <button @click="if(page > 1) page--" :disabled="page === 1"
                                        :style="page === 1 ? 'color:#D1D5DB; cursor:not-allowed; border:1px solid #F3F4F6; background:#FAFAFA;' : 'color:#666D80; cursor:pointer; border:1px solid #DFE1E7; background:#fff;'"
                                        class="hover:bg-gray-50 flex items-center justify-center transition-all"
                                        style="width:32px; height:32px; border-radius:6px; outline:none;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="15 18 9 12 15 6"></polyline>
                                        </svg>
                                    </button>
                                    <div style="width:32px; height:32px; background:#0B266E; color:#fff; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600;"
                                        x-text="page"></div>
                                    <button @click="if(page < totalPages) page++"
                                        :disabled="page === totalPages || totalPages === 0"
                                        :style="page === totalPages || totalPages === 0 ? 'color:#D1D5DB; cursor:not-allowed; border:1px solid #F3F4F6; background:#FAFAFA;' : 'color:#666D80; cursor:pointer; border:1px solid #DFE1E7; background:#fff;'"
                                        class="hover:bg-gray-50 flex items-center justify-center transition-all"
                                        style="width:32px; height:32px; border-radius:6px; outline:none;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9 18 15 12 9 6"></polyline>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                        </div>{{-- /x-show expanded --}}
                    </div>{{-- /accordion item --}}
                @empty
                    <div style="padding:48px; text-align:center;">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5"
                            stroke-linecap="round" style="margin:0 auto 12px;display:block;">
                            <path d="M9 11l3 3L22 4" />
                            <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
                        </svg>
                        <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada modul yang tersedia.</div>
                    </div>
                @endforelse

            </div>{{-- /outer card --}}

        </div>{{-- /x-data --}}

        {{-- Publikasi Nilai Section --}}
        @php
            $totalPraktikan = $daftarPraktikan->count();
            $koorCount = 0;
            $dosenCount = 0;
            foreach ($daftarPraktikan as $dp) {
                if ($dp->nilai?->disetujui_koor)
                    $koorCount++;
                if ($dp->nilai?->disetujui_dosen)
                    $dosenCount++;
            }
            $allKoorApproved = $totalPraktikan > 0 && $koorCount === $totalPraktikan;
            $allDosenApproved = $totalPraktikan > 0 && $dosenCount === $totalPraktikan;
        @endphp

        <div
            style="background:#fff; border:1px solid #DFE1E7; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.04); margin-bottom:48px; padding:20px 24px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:20px;">

            <div style="display:flex; flex-direction:column; gap:16px;">
                <div style="font-size:15px; font-weight:700; color:#0D0D12;">
                    Publikasi Nilai
                </div>
                <div style="display:flex; gap:24px;">

                    {{-- Dosen checkbox --}}
                    <label style="display:flex; align-items:center; gap:8px; cursor:default;">
                        <span
                            style="width:18px; height:18px; border-radius:4px; border:2px solid {{ $allDosenApproved ? 'transparent' : '#DFE1E7' }}; background:{{ $allDosenApproved ? '#0B266E' : '#fff' }}; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; transition:all .2s;">
                            @if($allDosenApproved)
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            @endif
                        </span>
                        <span style="font-size:13px; font-weight:600; color:#353849;">Dosen</span>
                    </label>

                    {{-- Koordinator checkbox --}}
                    <label style="display:flex; align-items:center; gap:8px; cursor:default;">
                        <span
                            style="width:18px; height:18px; border-radius:4px; border:2px solid {{ $allKoorApproved ? 'transparent' : '#DFE1E7' }}; background:{{ $allKoorApproved ? '#0B266E' : '#fff' }}; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0; transition:all .2s;">
                            @if($allKoorApproved)
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                            @endif
                        </span>
                        <span style="font-size:13px; font-weight:600; color:#353849;">Koordinator</span>
                    </label>

                </div>
                <p style="font-size:12px; color:#666D80; line-height:1.6; margin:0; max-width:600px;">
                    <strong style="color:#0D0D12;">Informasi:</strong> Nilai akan dipublikasikan ke mahasiswa setelah
                    disetujui oleh Dosen dan Koordinator.
                </p>
            </div>

            @if(!$allKoorApproved)
                <form method="POST" action="{{ route('eoffice.manprak.koor.nilai.approve') }}">
                    @csrf
                    <button type="submit" class="mp-btn primary md"
                        style="background:#6366F1; box-shadow:0 2px 6px rgba(99,102,241,.3);"
                        onmouseover="this.style.background='#4F46E5'" onmouseout="this.style.background='#6366F1'">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round">
                            <path d="M22 11.08V12a10 10 0 11-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        Simpan
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('eoffice.manprak.koor.nilai.approve') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="mp-btn neutral md"
                        style="background:#fff; border:1px solid #DFE1E7; color:#DC2626;"
                        onmouseover="this.style.background='#FEF2F2'" onmouseout="this.style.background='#fff'"
                        onclick="return confirm('Batalkan persetujuan nilai?')">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="15" y1="9" x2="9" y2="15" />
                            <line x1="9" y1="9" x2="15" y2="15" />
                        </svg>
                        Batalkan Publikasi
                    </button>
                </form>
            @endif

        </div>

    @endif

</x-eoffice::manajemen-praktikum.layout>