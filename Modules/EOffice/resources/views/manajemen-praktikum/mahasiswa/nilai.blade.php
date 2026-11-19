<x-eoffice::manajemen-praktikum.layout :breadcrumbPraktikum="$praktikum ?? null"
    :pageTitle="'Absensi & Nilai'">
    @if(isset($praktikum) && $praktikum)
        <x-eoffice::manajemen-praktikum.mhs-header :praktikum="$praktikum" />
    @endif

@if($praktikumList->isEmpty())

{{-- Belum terdaftar di praktikum apapun --}}
<div class="mp-card flex-shrink-0" style="padding:48px;text-align:center;">
    <div style="width:56px;height:56px;border-radius:14px;background:#F4F6F8;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#A4ABB8" stroke-width="1.5" stroke-linecap="round">
            <rect x="3" y="3" width="18" height="18" rx="3"/><path d="M9 9h6M9 12h6M9 15h4"/>
        </svg>
    </div>
    <div style="font-size:15px;font-weight:600;color:#0D0D12;margin-bottom:6px;">Belum Terdaftar di Praktikum</div>
    <div style="font-size:13px;color:#666D80;max-width:280px;margin:0 auto;line-height:1.6;">
        Anda belum terdaftar di praktikum manapun. Daftarkan diri melalui menu Pendaftaran.
    </div>

</div>

@else

@if(!$praktikum)
<div class="mp-alert info flex-shrink-0">Silakan pilih praktikum terlebih dahulu.</div>
@elseif(!$isPublished)

{{-- Nilai belum dipublikasikan --}}
<div class="mp-card flex-shrink-0 mp-unpublished-grades" style="padding:48px 24px;min-height:280px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;">
    <div style="width:56px;height:56px;border-radius:14px;background:rgba(211,156,61,0.1);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#D39C3D" stroke-width="1.5" stroke-linecap="round">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
    </div>
    <div style="font-size:15px;font-weight:600;color:#0D0D12;margin-bottom:6px;">Nilai Belum Dipublikasikan</div>
    <div style="font-size:13px;color:#666D80;max-width:340px;margin:0 auto;line-height:1.6;">
        Nilai untuk <strong>{{ $praktikum->nama }}</strong> belum dipublikasikan. Nilai akan tampil setelah disetujui oleh Koordinator dan Dosen.
    </div>
</div>

@else

{{-- Nilai sudah dipublikasikan — tampilkan per modul --}}
<div x-data="{ 
                                                                    globalSearch: '', 
                                                                    globalKelompok: '', 
                                                                    globalShift: '',
                                                                        extraWeights: {{ json_encode(collect($praktikum->komponen_modul ?? [])->pluck('bobot','id')->all()) }},
                                                                    bobot_tp: {{ $praktikum->bobot_tp ?? 10 }},
                                                                    bobot_praktikum: {{ $praktikum->bobot_praktikum ?? 30 }},
                                                                    bobot_laporan: {{ $praktikum->bobot_laporan ?? 30 }},
                                                                    bobot_responsi: {{ $praktikum->bobot_responsi ?? 30 }},
                                                                    get hasChanges() {
                                                                        return parseInt(this.bobot_tp) !== {{ $praktikum->bobot_tp ?? 10 }} ||
                                                                               parseInt(this.bobot_praktikum) !== {{ $praktikum->bobot_praktikum ?? 30 }} ||
                                                                               parseInt(this.bobot_laporan) !== {{ $praktikum->bobot_laporan ?? 30 }} ||
                                                                               parseInt(this.bobot_responsi) !== {{ $praktikum->bobot_responsi ?? 30 }};
                                                                    }
                                                                }">

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

                        <x-eoffice::manajemen-praktikum.ui.grade-group-filter model="globalKelompok" label="Semua Kelompok" searchPlaceholder="Cari kelompok..." />
                        <x-eoffice::manajemen-praktikum.ui.grade-group-filter model="globalShift" label="Semua Shift" searchPlaceholder="Cari shift..." />
                        <x-eoffice::manajemen-praktikum.ui.nilai-csv-picker :praktikum="$praktikum" :moduls="$moduls" role="mahasiswa" />

                    </div>
                </div>

                {{-- Module Accordions --}}
                @forelse($moduls as $modul)
                    <div x-data="{ 
                                                                                                                                                                                                                                                                                                                                            expanded: false,
                                                                                                                                                                                                                                                                                                                                            page: 1,
                                                                                                                                                                                                                                                                                                                                            perPage: 10,
                                                                                                                                                                                                                                                                                                                                            rows: [
                                                                                                                                                                                                                                                                                                                                                @foreach($daftarPraktikan as $idx => $dp_item)
                                                                                                                                                                                                                                                                                                                                                    { id: {{ $idx }}, name: @js(strtolower($dp_item->user?->name ?? '')), nim: @js(strtolower($dp_item->user?->student?->student_number ?? $dp_item->user?->email ?? '')), kel: @js($dp_item->kelompok ?? ''), shf: @js($dp_item->shift ?? '') }{{ $loop->last ? '' : ',' }}
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

                        <x-eoffice::manajemen-praktikum.ui.grade-accordion state="expanded">
                            <div style="overflow-x:auto;">
                                <table class="mp-table" style="min-width:1200px;">
                                    <thead>
                                        <tr style="background:#F9FAFB;">
                                            <th class="mp-th text-left" style="padding:12px 16px;width:50px;text-align:center;">NO</th>
                                            <th class="mp-th text-left" style="padding:12px 16px;width:250px;">NAMA MAHASISWA
                                            </th>
                                            <th class="mp-th text-left" style="padding:12px 16px;width:150px;">NIM</th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:100px;text-align:center;">KELOMPOK
                                            </th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:100px;text-align:center;">SHIFT</th>
                                            <th class="mp-th text-center" style="padding:12px 16px;width:100px;text-align:center;">KEHADIRAN</th>
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
                                                {{ $praktikum->laporanLabel() }}<br><span
                                                    style="font-size:11px;font-weight:600;">{{ $praktikum->bobot_laporan ?? 30 }}%</span>
                                            </th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;width:95px;border-right:1px solid #DFE1E7;background:#FFF7ED;color:#C2410C;text-align: center;">
                                                Responsi<br><span
                                                    style="font-size:11px;font-weight:600;">{{ $praktikum->bobot_responsi ?? 30 }}%</span>
                                            </th>
                                            @include('eoffice::manajemen-praktikum.partials.module-extra-headers', ['extraMode'=>'mahasiswa'])
                                            <th class="mp-th text-left"
                                                style="padding:12px 16px;border-left:1px solid #DFE1E7;">KETERANGAN</th>
                                            <th class="mp-th text-center"
                                                style="padding:12px 16px;border-left:1px solid #DFE1E7;">RATA-RATA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($daftarPraktikan as $idx => $dp_item)
                                            @php
                                                $absensi = $dp_item->absensi->firstWhere('modul_id', $modul->id);
                                                $statusAbsen = $absensi?->status;
                                                $njMap = $nilaiJenisMap[$modul->id][$dp_item->id] ?? [];
                                            @endphp
                                            <tr class="mp-tr group" style="border-bottom:1px solid #DFE1E7;"
                                                x-show="paginatedRows.some(r => r.id === {{ $idx }})">
                                                <td class="group-hover:bg-gray-50 transition-all"
                                                    style="padding:12px 16px; color:#666D80; font-size:13px;text-align:center;font-weight:700;">{{ $idx + 1 }}</td>
                                                <td class="group-hover:bg-gray-50 transition-all" style="padding:12px 16px;">
                                                    <div style="display:flex;align-items:center;gap:10px;">
                                                        <div class="mp-av sky"
                                                            style="width:28px;height:28px;font-size:11px;flex-shrink:0;">
                                                            {{ strtoupper(substr($dp_item->user?->name ?? 'M', 0, 2)) }}
                                                        </div>
                                                        <div>
                                                            <div style="font-weight:600;color:#0D0D12;font-size:13px;">
                                                                {{ $dp_item->user?->name ?? '-' }}
                                                            </div>
                                                            <div style="font-size:11.5px;color:#666D80;margin-top:2px;">
                                                                {{ $dp_item->user?->email ?? '-' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="group-hover:bg-gray-50 transition-all"
                                                    style="padding:12px 16px; color:#0D0D12; font-size:12px;font-weight:600;">
                                                    {{ $dp_item->user?->student?->student_number ?? $dp_item->user?->email ?? '-' }}
                                                </td>
                                                <td x-show="kelRowspan({{ $idx }}) > 0" :rowspan="kelRowspan({{ $idx }})"
                                                    class="group-hover:bg-gray-50 transition-all"
                                                    style="padding:12px 16px;text-align:center;font-size:15px;font-weight:700;color:#0D0D12;vertical-align:middle;">
                                                    {{ $dp_item->kelompok ?? '—' }}
                                                </td>
                                                <td x-show="shfRowspan({{ $idx }}) > 0" :rowspan="shfRowspan({{ $idx }})"
                                                    class="group-hover:bg-gray-50 transition-all"
                                                    style="padding:12px 16px;text-align:center;font-size:15px;font-weight:700;color:#0D0D12;vertical-align:middle;">
                                                    {{ $dp_item->shift ?? '—' }}
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
                                                <td class="group-hover:brightness-95 transition-all"
                                                    style="padding:12px 16px;text-align:center;background:#EAF0FA;color:#0D0D12;font-size:13px;">
                                                    {{ isset($njMap['tugas_pendahuluan']) ? number_format($njMap['tugas_pendahuluan'], 0, ',', '') : '0' }}
                                                </td>
                                                {{-- Praktikum (Tugas Pengganti) --}}
                                                <td class="group-hover:brightness-95 transition-all"
                                                    style="padding:12px 16px;text-align:center;background:#F0FDF4;color:#0D0D12;font-size:13px;">
                                                    {{ isset($njMap['tugas_pengganti']) ? number_format($njMap['tugas_pengganti'], 0, ',', '') : '0' }}
                                                </td>
                                                {{-- Laporan --}}
                                                <td class="group-hover:brightness-95 transition-all"
                                                    style="padding:12px 16px;text-align:center;background:#FEF9C3;color:#0D0D12;font-size:13px;">
                                                    {{ isset($njMap['laporan']) ? number_format($njMap['laporan'], 0, ',', '') : '0' }}
                                                </td>
                                                {{-- Responsi --}}
                                                <td class="group-hover:brightness-95 transition-all"
                                                    style="padding:12px 16px;text-align:center;background:#FFF7ED;color:#0D0D12;font-size:13px;">
                                                    {{ isset($njMap['responsi']) ? number_format($njMap['responsi'], 0, ',', '') : '0' }}
                                                </td>
                                            @include('eoffice::manajemen-praktikum.partials.module-extra-cells', ['extraMode'=>'mahasiswa'])
                                                {{-- Keterangan --}}
                                                <td class="group-hover:bg-gray-50 transition-all"
                                                    style="padding:12px 16px;font-size:12px;color:#666D80;border-left:1px solid #DFE1E7;">
                                                    {{ $absensi?->keterangan ?? '—' }}
                                                </td>
                                                {{-- RATA-RATA --}}
                                                <td class="group-hover:bg-gray-50 transition-all"
                                                    style="padding:12px 16px;text-align:center;color:#0D0D12;font-size:13px;font-weight:600;border-left:1px solid #DFE1E7;"
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
                            <x-eoffice::manajemen-praktikum.ui.client-pagination />

                        </x-eoffice::manajemen-praktikum.ui.grade-accordion>{{-- /x-show expanded --}}
                    </div>{{-- /accordion item --}}
                @empty
                    <div style="padding:48px; text-align:center;">
                        <x-eoffice::manajemen-praktikum.ui.empty-icon name="medal" />
                        <div style="font-size:13px;font-weight:500;color:#666D80;">Belum ada modul yang tersedia.</div>
                    </div>
                @endforelse

                <x-eoffice::manajemen-praktikum.ui.tugas-akhir-card :praktikum="$praktikum" role="mahasiswa" section="nilai" />
                <x-eoffice::manajemen-praktikum.ui.final-grades :praktikum="$praktikum" role="mahasiswa" embedded="true" />
            </div>{{-- /outer card --}}

        </div>{{-- /x-data --}}

        
@endif
@endif
</x-eoffice::manajemen-praktikum.layout>
