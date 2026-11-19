<x-eoffice::manajemen-praktikum.layout :breadcrumbPraktikum="$praktikum ?? null"
    pageTitle="{{ $praktikum ? $praktikum->nama : 'Belum Ada Praktikum' }} / Absensi & Nilai Modul">
    @if(isset($praktikum) && $praktikum)

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

        <div style="row-gap:0!important" @final-assessment-change="finalAssessment=$event.detail.changed ? $event.detail.payload : null" @ta-weight-change="taWeightChanges[$event.detail.id]=$event.detail.changed" x-data="{
                                                                        finalAssessment:null,
                                                                        moduleWeightsLocked: {{ $praktikum->moduleWeightsLocked() ? 'true' : 'false' }},
                                                                        taWeightChanges: {}, finalWeights: null,
                                                                        globalSearch: '', 
                                                                        globalKelompok: '', 
                                                                        globalShift: '',
                                                                        extraWeights: {{ json_encode(collect($praktikum->komponen_modul ?? [])->pluck('bobot','id')->all()) }},
                                                                        bobot_tp: {{ $praktikum->bobot_tp ?? 10 }},
                                                                        bobot_praktikum: {{ $praktikum->bobot_praktikum ?? 30 }},
                                                                        bobot_laporan: {{ $praktikum->bobot_laporan ?? 30 }},
                                                                        bobot_responsi: {{ $praktikum->bobot_responsi ?? 30 }},
                                                                        get hasChanges() {
if (this.finalWeights !== null || this.finalAssessment !== null) return true;
return JSON.stringify(this.extraWeights) !== JSON.stringify({{ json_encode(collect($praktikum->komponen_modul ?? [])->pluck('bobot','id')->all()) }}) || Object.values(this.taWeightChanges).some(Boolean) || Number(this.bobot_tp) !== {{ $praktikum->bobot_tp ?? 10 }} ||
                                                                                   parseInt(this.bobot_praktikum) !== {{ $praktikum->bobot_praktikum ?? 30 }} ||
                                                                                   parseInt(this.bobot_laporan) !== {{ $praktikum->bobot_laporan ?? 30 }} ||
                                                                                   parseInt(this.bobot_responsi) !== {{ $praktikum->bobot_responsi ?? 30 }};
                                                                        }
                                                                    }">

            <x-eoffice::manajemen-praktikum.koor-header :praktikum="$praktikum">
                {{-- Filter Header Row --}}
                <div
                    style="background:white;border:1px solid #DFE1E7;border-radius:12px 12px 0 0;margin-top:15px;padding:16px 20px; display:flex; flex-direction:row; flex-wrap:nowrap; align-items:center; justify-content:space-between; gap:16px; border-bottom:1px solid #DFE1E7;"
                    >

                    <div style="font-size: 15px; font-weight: 700; color: #0D0D12; white-space: nowrap; flex-shrink: 0;">
                        Absensi &amp; Nilai
                    </div>

                    <div
                        style="display: flex; flex-direction: row; flex-wrap: nowrap; align-items: center; gap: 10px; flex-shrink: 0;">

                        <div style="position: relative;">
                            <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            <input type="text" x-model="globalSearch" placeholder="Cari nama / NIM..." class="mp-input"
                                style="padding-left: 32px; height: 36px; width: 210px; font-size: 13px;">
                        </div>

                        <x-eoffice::manajemen-praktikum.ui.grade-group-filter model="globalKelompok" label="Semua Kelompok" searchPlaceholder="Cari kelompok..." />
                        <x-eoffice::manajemen-praktikum.ui.grade-group-filter model="globalShift" label="Semua Shift" searchPlaceholder="Cari shift..." />

                        <form method="POST" action="{{ route('eoffice.manprak.koor.nilai.bobot.update', $praktikum->id) }}"
                            id="bobotForm" style="display:none;" x-data="{showWeightConfirm:false,weightConfirmed:false}" x-ref="moduleWeightForm" @submit="if(!hasChanges){$event.preventDefault();return;}if(!weightConfirmed){$event.preventDefault();showWeightConfirm=true}">
                            @include('eoffice::manajemen-praktikum.partials.module-weight-confirmation')
                            @php
                                $weightTa=\Modules\EOffice\Models\TugasAkhir::where('praktikum_id',$praktikum->id)->first();
                            @endphp
                            <input type="hidden" name="ta_version" value="{{ $weightTa?->version }}">
                            @foreach(['version','modules','ta'] as $field)
                            <input type="hidden" name="final_weights[{{ $field }}]" :disabled="!finalWeights" :value="finalWeights ? finalWeights.{{ $field }} : ''">
                            @endforeach
                            @csrf
                            <input type="hidden" name="final_payload" :disabled="!finalAssessment" :value="JSON.stringify(finalAssessment)">
                            <input type="hidden" name="bobot_tp" :value="bobot_tp">
                            <input type="hidden" name="module_version" value="{{ $praktikum->komponen_modul_version ?? 0 }}">
                            @foreach($praktikum->komponen_modul ?? [] as $extra)
                                <input type="hidden" name="extra_weights[{{ $extra['id'] }}]" :value="extraWeights.{{ $extra['id'] }}">
                            @endforeach
                            <input type="hidden" name="bobot_praktikum" :value="bobot_praktikum">
                            <input type="hidden" name="bobot_laporan" :value="bobot_laporan">
                            <input type="hidden" name="bobot_responsi" :value="bobot_responsi">
                        </form>

                        <button type="button" :disabled="!hasChanges" @click="if(hasChanges) document.getElementById('bobotForm').requestSubmit()"
                            :style="(hasChanges ? 'background:#0B266E; color:#fff; border: 1px solid #0B266E;' : 'background:#F4F6F8; color:#0B266E; border: 1px solid #DFE1E7;') + ' height: 36px; min-width: 100px; padding: 0 12px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: bold; transition: all 0.2s; cursor:' + (hasChanges ? 'pointer' : 'not-allowed') + ';pointer-events:auto;'">
                            Simpan
                        </button>

                        <x-eoffice::manajemen-praktikum.ui.nilai-csv-picker :praktikum="$praktikum" :moduls="$allModuls" role="koor" />

                    </div>
                </div>

            </x-eoffice::manajemen-praktikum.koor-header>

            {{-- Outer card wraps filter row + all accordions --}}
            <div
                style="background:#fff; border:1px solid #DFE1E7; border-top:0;border-radius:0 0 12px 12px!important; box-shadow:0 1px 3px rgba(0,0,0,0.04); margin-bottom:0; overflow:hidden;">

                {{-- Module Accordions --}}
                @forelse($allModuls as $modul)
                    <div style="row-gap:0!important" x-data="{ 
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
                        style="border-bottom:1px solid #DFE1E7!important;">
                        <div style="box-shadow:inset 0 -1px 0 #DFE1E7;padding:16px 20px; cursor:pointer; display:flex; justify-content:space-between; align-items:center;"
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

                        <x-eoffice::manajemen-praktikum.ui.grade-accordion>
                            <div style="overflow-x:auto;">
                                <table class="mp-table" style="min-width:1100px;">
                                    <thead>
                                        <tr style="background:#F9FAFB;">
                                            <th class="mp-th text-center" style="padding:12px 16px;width:50px;text-align:center;">NO</th>
                                            <th class="mp-th text-left" style="padding:12px 16px;width:250px;">NAMA MAHASISWA
                                            </th>
                                            <th class="mp-th text-left" style="padding:12px 16px;width:150px;">NIM</th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:100px;">KELOMPOK
                                            </th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:100px;">SHIFT</th>
                                            <th class="mp-th text-center" style="padding:12px 16px;width:100px;text-align:center;">KEHADIRAN</th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:120px;border-left:1px solid #DFE1E7;background:#EEF2FF;color:#4338CA;font-weight:600;">
                                                TUGAS PENDAHULUAN
                                                <div
                                                    style="margin-top:4px; display:flex; align-items:center; justify-content:center; gap:4px; font-size:11px;">
                                                    <input type="number" x-model="bobot_tp" :disabled="moduleWeightsLocked" min="0" max="100" step="1"
                                                        style="width:36px; height:23px; font-size:13px; text-align:center; border:1px solid #CBD5E1; border-radius:4px; padding:0; color:#4338CA;">
                                                    %
                                                </div>
                                            </th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:120px;background:#F0FDF4;color:#15803D;font-weight:600;">
                                                PRAKTIKUM
                                                <div
                                                    style="margin-top:4px; display:flex; align-items:center; justify-content:center; gap:4px; font-size:11px;">
                                                    <input type="number" x-model="bobot_praktikum" :disabled="moduleWeightsLocked" min="0" max="100" step="1"
                                                        style="width:36px; height:23px; font-size:13px; text-align:center; border:1px solid #CBD5E1; border-radius:4px; padding:0; color:#15803D;">
                                                    %
                                                </div>
                                            </th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:120px;background:#FEFCE8;color:#A16207;font-weight:600;">
                                                {{ mb_strtoupper($praktikum->laporanLabel()) }}
                                                <div
                                                    style="margin-top:4px; display:flex; align-items:center; justify-content:center; gap:4px; font-size:11px;">
                                                    <input type="number" x-model="bobot_laporan" :disabled="moduleWeightsLocked" min="0" max="100" step="1"
                                                        style="width:36px; height:23px; font-size:13px; text-align:center; border:1px solid #CBD5E1; border-radius:4px; padding:0; color:#A16207;">
                                                    %
                                                </div>
                                            </th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:120px;background:#FFF7ED;color:#C2410C;font-weight:600;">
                                                RESPONSI
                                                <div
                                                    style="margin-top:4px; display:flex; align-items:center; justify-content:center; gap:4px; font-size:11px;">
                                                    <input type="number" x-model="bobot_responsi" :disabled="moduleWeightsLocked" min="0" max="100" step="1"
                                                        style="width:36px; height:23px; font-size:13px; text-align:center; border:1px solid #CBD5E1; border-radius:4px; padding:0; color:#C2410C;">
                                                    %
                                                </div>
                                            </th>
                                            @include('eoffice::manajemen-praktikum.partials.module-extra-headers', ['extraMode'=>'koor'])
                                            <th class="mp-th text-left"
                                                style="padding:12px 16px;border-left:1px solid #DFE1E7;">KETERANGAN</th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;border-left:1px solid #DFE1E7;">RATA-RATA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($daftarPraktikan as $idx => $dp)
                                            @php
                                                $absensi = $dp->absensi->firstWhere('modul_id', $modul->id);
                                                $statusAbsen = $absensi?->status;
                                                $njMap = $nilaiJenisMap[$modul->id][$dp->id] ?? [];
                                            @endphp
                                            <tr class="mp-tr group" style="border-bottom:1px solid #DFE1E7;"
                                                x-show="paginatedRows.some(r => r.id === {{ $idx }})">
                                                <td class="group-hover:bg-gray-50 transition-all"
                                                    style="padding:12px 16px; color:#666D80; font-size:13px;text-align:center;font-weight:700;">{{ $idx + 1 }}</td>
                                                <td class="group-hover:bg-gray-50 transition-all" style="padding:12px 16px;">
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
                                                <td class="group-hover:bg-gray-50 transition-all"
                                                    style="padding:12px 16px; color:#0D0D12; font-size:12px;font-weight:600;">
                                                    {{ $dp->user?->student?->student_number ?? $dp->user?->email ?? '-' }}
                                                </td>
                                                <td x-show="kelRowspan({{ $idx }}) > 0" :rowspan="kelRowspan({{ $idx }})"
                                                    class="group-hover:bg-gray-50 transition-all"
                                                    style="padding:12px 16px;text-align:center;font-size:15px;font-weight:700;color:#0D0D12;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;vertical-align:middle;">
                                                    {{ $dp->kelompok ?? '—' }}
                                                </td>
                                                <td x-show="shfRowspan({{ $idx }}) > 0" :rowspan="shfRowspan({{ $idx }})"
                                                    class="group-hover:bg-gray-50 transition-all"
                                                    style="padding:12px 16px;text-align:center;font-size:15px;font-weight:700;color:#0D0D12;border-right:1px solid #DFE1E7;vertical-align:middle;">
                                                    {{ $dp->shift ?? '—' }}
                                                </td>
                                                <td class="group-hover:bg-gray-50 transition-all"
                                                    style="padding:12px 16px;text-align:center;">
                                                    @if($statusAbsen === 'hadir')
                                                        <span class="mp-badge success sm"
                                                            style="background:#ECFDF5; color:#10B981;">Hadir</span>
                                                    @elseif($statusAbsen === 'terlambat')
                                                        <span class="mp-badge warning sm"
                                                            style="background:#FFFBEB; color:#D97706;">Terlambat</span>
                                                    @elseif($statusAbsen === 'izin')
                                                        <span class="mp-badge sky sm"
                                                            style="background:#EFF6FF; color:#3B82F6;">Izin</span>
                                                    @elseif($statusAbsen === 'tidak_hadir')
                                                        <span class="mp-badge sky sm"
                                                            style="background:#EFF6FF; color:#3B82F6;">Tidak Hadir</span>
                                                    @elseif($statusAbsen === 'alpa')
                                                        <span class="mp-badge error sm"
                                                            style="background:#FEF2F2; color:#EF4444;">Alpa</span>
                                                    @else
                                                        <span style="color:#A4ABB8; font-size:12px;">—</span>
                                                    @endif
                                                </td>
                                                @php
                                                    $valTP = isset($njMap['tugas_pendahuluan']) ? floatval($njMap['tugas_pendahuluan']) : 0;
                                                    $valPrak = isset($njMap['tugas_pengganti']) ? floatval($njMap['tugas_pengganti']) : 0;
                                                    $valLap = isset($njMap['laporan']) ? floatval($njMap['laporan']) : 0;
                                                    $valResp = isset($njMap['responsi']) ? floatval($njMap['responsi']) : 0;
                                                @endphp
                                                {{-- Tugas Pendahuluan --}}
                                                <td class="mp-grade-history-cell group-hover:brightness-95 transition-all"
                                                    style="position:relative;padding-right:32px!important;padding:12px 16px;text-align:center;background:#EAF0FA;color:#0D0D12;font-size:13px;">
                                                    {{ isset($njMap['tugas_pendahuluan']) ? number_format($njMap['tugas_pendahuluan'], 0, ',', '') : '0' }}
                                                <x-eoffice::manajemen-praktikum.ui.module-score-history :module-id="$modul->id" :student-id="$dp->id" score-key="tugas_pendahuluan" score-label="Tugas Pendahuluan" :student-name="$dp->user?->name ?? 'Mahasiswa'" /></td>
                                                {{-- Praktikum (Tugas Pengganti) --}}
                                                <td class="mp-grade-history-cell group-hover:brightness-95 transition-all"
                                                    style="position:relative;padding-right:32px!important;padding:12px 16px;text-align:center;background:#F0FDF4;color:#0D0D12;font-size:13px;">
                                                    {{ isset($njMap['tugas_pengganti']) ? number_format($njMap['tugas_pengganti'], 0, ',', '') : '0' }}
                                                <x-eoffice::manajemen-praktikum.ui.module-score-history :module-id="$modul->id" :student-id="$dp->id" score-key="tugas_pengganti" score-label="Praktikum" :student-name="$dp->user?->name ?? 'Mahasiswa'" /></td>
                                                {{-- Laporan --}}
                                                <td class="mp-grade-history-cell group-hover:brightness-95 transition-all"
                                                    style="position:relative;padding-right:32px!important;padding:12px 16px;text-align:center;background:#FEF9C3;color:#0D0D12;font-size:13px;">
                                                    {{ isset($njMap['laporan']) ? number_format($njMap['laporan'], 0, ',', '') : '0' }}
                                                <x-eoffice::manajemen-praktikum.ui.module-score-history :module-id="$modul->id" :student-id="$dp->id" score-key="laporan" :score-label="$praktikum->laporanLabel()" :student-name="$dp->user?->name ?? 'Mahasiswa'" /></td>
                                                {{-- Responsi --}}
                                                <td class="mp-grade-history-cell group-hover:brightness-95 transition-all"
                                                    style="position:relative;padding-right:32px!important;padding:12px 16px;text-align:center;background:#FFF7ED;color:#0D0D12;font-size:13px;">
                                                    {{ isset($njMap['responsi']) ? number_format($njMap['responsi'], 0, ',', '') : '0' }}
                                                <x-eoffice::manajemen-praktikum.ui.module-score-history :module-id="$modul->id" :student-id="$dp->id" score-key="responsi" score-label="Responsi" :student-name="$dp->user?->name ?? 'Mahasiswa'" /></td>
                                            @include('eoffice::manajemen-praktikum.partials.module-extra-cells', ['extraMode'=>'koor'])
                                                {{-- Keterangan --}}
                                                <td class="group-hover:bg-gray-50 transition-all"
                                                    style="padding:12px 16px;font-size:12px;color:#666D80;border-left:1px solid #DFE1E7;text-align:{{ filled($absensi?->keterangan) ? 'left' : 'center' }};">
                                                    {{ filled($absensi?->keterangan) ? $absensi->keterangan : '—' }}
                                                </td>
                                                {{-- RATA-RATA --}}
                                                <td class="group-hover:bg-gray-50 transition-all"
                                                    style="padding:12px 16px;text-align:center;color:#0D0D12;font-size:13px;font-weight:500;border-left:1px solid #DFE1E7;"
                                                    x-data="{
                                                                                                                                                                                                                                        get rataRata() {
let totalBobot = Number(bobot_tp || 0) + Number(bobot_praktikum || 0) + Number(bobot_laporan || 0) + Number(bobot_responsi || 0) + Object.values(extraWeights).reduce((sum,w)=>sum+Number(w||0),0);
if (totalBobot !== 100) return '—';
                                                                                                                                                                                                                                            let sum = ({{$valTP}} * (bobot_tp || 0)) + ({{$valPrak}} * (bobot_praktikum || 0)) + ({{$valLap}} * (bobot_laporan || 0)) + ({{$valResp}} * (bobot_responsi || 0)); 
const extraScores = {{ json_encode(collect($njMap)->only(collect($praktikum->komponen_modul ?? [])->pluck('id')->all())->all()) }};
sum += Object.entries(extraWeights).reduce((sum,[key,w])=>sum+Number(extraScores[key]||0)*Number(w||0),0);
                                                                                                                                                                                                                                            return (sum / totalBobot).toFixed(2).replace('.',',');
                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                    }">
                                                    <span x-text="rataRata"></span>
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
                            <x-eoffice::manajemen-praktikum.ui.client-pagination :separator="true" />

                        </x-eoffice::manajemen-praktikum.ui.grade-accordion>
                    </div>{{-- /accordion item --}}
                @empty
                    <div style="padding:48px; text-align:center;">
                        <x-eoffice::manajemen-praktikum.ui.empty-icon name="medal" />
                        <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada modul yang tersedia.</div>
                    </div>
                @endforelse

                <x-eoffice::manajemen-praktikum.ui.tugas-akhir-card :praktikum="$praktikum" :students="$daftarPraktikan" role="koor" section="nilai" />
                <div @final-weight-change="finalWeights=$event.detail.changed ? $event.detail.values : null">
                    <x-eoffice::manajemen-praktikum.ui.final-grades :praktikum="$praktikum" role="koor" :embedded="true" />
                </div>
            </div>{{-- /outer card --}}

        </div>{{-- /x-data --}}


        {{-- Publikasi Nilai Section --}}
        @php
            $totalPraktikan = $daftarPraktikan->count();
            $publicationTa=\Modules\EOffice\Models\TugasAkhir::where('praktikum_id',$praktikum->id)->where('published',true)->first();
            $publicationGrades=$publicationTa ? \Modules\EOffice\Models\TaNilai::where('tugas_akhir_id',$publicationTa->id)->get() : collect();
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
            if($publicationTa){ $allKoorApproved=$allKoorApproved && $publicationGrades->count()===$totalPraktikan && $publicationGrades->every(fn($g)=>$g->disetujui_koor); $allDosenApproved=$allDosenApproved && $publicationGrades->count()===$totalPraktikan && $publicationGrades->every(fn($g)=>$g->disetujui_dosen); }
        @endphp

        <div
            style="background:#fff; border:1px solid #DFE1E7; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.04); margin-bottom:0; padding:20px 24px!important;padding-block:20px!important; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:20px;">

            <div style="display:flex; flex-direction:column; gap:12px;flex:1;min-width:240px;">
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
                <p style="font-size:12px; color:#666D80; line-height:1.6; margin:0;">
                    <strong style="color:#0D0D12;">Informasi:</strong> Nilai akan dipublikasikan ke mahasiswa setelah
                    disetujui oleh Dosen dan Koordinator.
                </p>
            </div>

            @if(!$allKoorApproved)
                <form method="POST" action="{{ route('eoffice.manprak.koor.nilai.approve') }}" x-data="{showPublicationConfirm:false,publicationConfirmed:false,publicationSubmitting:false}" x-ref="publicationForm" @submit="if(publicationSubmitting){$event.preventDefault();return;}if(!publicationConfirmed){$event.preventDefault();showPublicationConfirm=true}else{publicationSubmitting=true}">
                    <x-eoffice::manajemen-praktikum.ui.publication-confirmation role="koor" />
                    @csrf
                    @foreach($publicationGrades as $taGrade)<input type="hidden" name="ta_versions[{{ $taGrade->id }}]" value="{{ $taGrade->version }}">@endforeach
                    <button type="submit" :disabled="publicationSubmitting" class="mp-btn primary md"
                        style="background:#0B266E; color:#FFFFFF; padding:10px 16px; border-radius:8px; font-size:13px; font-weight:700; box-shadow:0 2px 6px rgba(11,38,110,.2);"
                        onmouseover="this.style.background='#091958'" onmouseout="this.style.background='#0B266E'">
                        Kirim Persetujuan
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('eoffice.manprak.koor.nilai.approve') }}" x-data="{showCancelConfirm:false,cancelConfirmed:false}" x-ref="cancelPublicationForm" @submit="if(!cancelConfirmed){$event.preventDefault();showCancelConfirm=true}">
                    <x-eoffice::manajemen-praktikum.ui.cancel-publication-confirmation />
                    @csrf
                    @foreach($publicationGrades as $taGrade)<input type="hidden" name="ta_versions[{{ $taGrade->id }}]" value="{{ $taGrade->version }}">@endforeach
                    @method('DELETE')
                    <button type="submit" class="mp-btn neutral md"
                        style="background:#FFF1F2; border:1px solid #FECDD3; color:#DF1C41;"
                        onmouseover="this.style.background='#FEF2F2'" onmouseout="this.style.background='#FFF1F2'">
                        Batalkan Publikasi
                    </button>
                </form>
            @endif

        </div>

    @endif

</x-eoffice::manajemen-praktikum.layout>
