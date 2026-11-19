<section class="mp-card ta-card {{ ($embedded ?? false) ? 'ta-embedded' : '' }}" @if($embedded ?? false) style="border:0;border-radius:0;box-shadow:none;margin:0" @endif x-data="{page:1,perPage:10,count:{{ $praktikan->count() }},get totalPages(){return Math.max(1,Math.ceil(this.count/this.perPage))},get visibleRows(){return Array.from({length:this.count})}}">
    @if(!$ta->validWeights())<div role="alert" class="mp-alert warning" style="margin:12px 20px">Bobot belum lengkap atau totalnya belum 100%. Rata-rata belum dapat dihitung.</div>@endif
    @if(!($embedded ?? false))
    <div class="ta-heading">Absensi &amp; Nilai — Tugas Akhir</div>
    <div class="ta-body"><p class="ta-muted">Absensi dicatat satu kali per mahasiswa. Parameter dikelompokkan per tahap. Total bobot seluruh parameter 100%. Nilai awal dan revisi tersimpan terpisah; hasil revisi mengikuti kebijakan tahap.</p></div>
    @endif
    @if(($embedded ?? false))
        <style>
            .ta-embedded .ta-body{padding:16px 20px;display:grid;gap:12px}.ta-embedded .ta-muted{font-size:12px;color:#666D80}.ta-embedded .ta-tag{font-size:12px;color:#0B266E}.ta-embedded .ta-btn{border:1px solid #DFE1E7;border-radius:8px;background:#0B266E;color:white;padding:8px 14px;font-size:13px;cursor:pointer}.ta-embedded input[type=number]{width:52px;border:1px solid #DFE1E7;border-radius:4px;padding:4px;font-size:12px}.ta-embedded .ta-link{color:#0B266E}.ta-embedded .ta-history{font-size:12px;padding:8px 0}
        </style>
        @if($role === 'koor' && count($ta->komponen))<form id="ta-inline-weights" method="POST" action="{{ route($base.'weights',$routeArgs) }}" style="padding:12px 20px;text-align:right">@csrf<input type="hidden" name="version" value="{{ $ta->version }}"><button class="mp-btn primary sm" type="submit">Simpan Bobot</button></form>@endif
    @endif
    @if($role === 'koor' && count($ta->komponen) && !($embedded ?? false))
        <form method="POST" action="{{ route($base.'weights', $routeArgs) }}" class="ta-form">@csrf<input type="hidden" name="version" value="{{ $ta->version }}"><div class="ta-body"><h3 style="font-size:14px;font-weight:700">Bobot Penilaian Tugas Akhir</h3>
            @foreach($ta->tahap as $weightStage)
                @if(count($ta->componentsFor($weightStage['id'])))<strong style="font-size:13px">{{ $weightStage['nama'] }}</strong>@endif
                @foreach($ta->componentsFor($weightStage['id']) as $c)<label>{{ $c['nama'] }} (%)<input type="number" name="bobot[{{ $c['id'] }}]" value="{{ old('bobot.'.$c['id'], $c['bobot'] > 0 ? $c['bobot'] : '') }}" required min="1" max="100" step="1" placeholder="1–100"></label>@endforeach
            @endforeach
            <p class="ta-muted">Bobot setiap parameter harus lebih dari 0% dan maksimal 100%. Total harus 100%. Perubahan bobot memerlukan persetujuan nilai ulang.</p>
        </div><div class="mp-modal-footer"><button type="submit" class="ta-btn">Simpan Bobot</button></div></form>
    @endif
    <div style="overflow-x:auto">
        <table class="mp-table" style="min-width:1100px">
            <thead><tr><th class="mp-th">Mahasiswa</th>
                @foreach(['Hadir','Terlambat','Tidak Hadir','Alpa'] as $label)<th class="mp-th" style="text-align:center">{{ $label }}</th>@endforeach
                <th class="mp-th">Keterangan</th>
                @foreach($ta->komponen as $component)<th class="mp-th" style="min-width:120px;background:#EFF6FF"><span style="display:block;color:#666D80;font-size:11px">{{ collect($ta->tahap)->firstWhere('id',$component['tahap_id'] ?? $ta->tahap[0]['id'])['nama'] }}</span>{{ $component['nama'] }} @if(($embedded ?? false) && $role === 'koor')<div style="margin-top:4px"><input form="ta-inline-weights" type="number" name="bobot[{{ $component['id'] }}]" value="{{ $component['bobot'] > 0 ? $component['bobot'] : '' }}" min="1" max="100" step="1" required aria-label="Bobot {{ $component['nama'] }}"> %</div>@else ({{ $component['bobot'] }}%) @endif</th>@endforeach
                <th class="mp-th">Rata-rata</th><th class="mp-th">{{ $role === 'asprak' ? 'Aksi' : 'Publikasi Nilai' }}</th>
            </tr></thead>
            <tbody>
                @forelse($praktikan as $student)
                    @php
                        $grade = $grades->get($student->id);
                        $presence = $attendance->get($student->id);
                        $gradeForm = 'ta-grade-'.$student->id;
                        $attendanceForm = 'ta-attendance-'.$student->id;
                        $scores = collect($ta->komponen)->mapWithKeys(fn($c) => [$c['id'] => $grade?->skor[$c['id']] ?? ''])->all();
                        $weights = collect($ta->komponen)->mapWithKeys(fn($c) => [$c['id'] => (float)$c['bobot']])->all();
                    @endphp
                    <tr class="mp-tr ta-form" x-show="{{ $loop->index }} >= (page-1)*perPage && {{ $loop->index }} < page*perPage"
                        x-data="{average:{{ json_encode($ta->average($grade?->skor ?? [])) }}}">
                        <td style="padding:14px 16px;min-width:180px;font-size:13px;font-weight:600">{{ $student->user?->name ?? 'Mahasiswa' }}
                            @if($role !== 'mahasiswa' && $grade)<details style="font-weight:400;margin-top:8px"><summary class="ta-link" style="cursor:pointer">Riwayat Nilai</summary>
                                @foreach($grade->riwayat as $entry)<div class="ta-history"><strong>{{ $entry['oleh'] }}</strong> · {{ \Illuminate\Support\Carbon::parse($entry['waktu'])->format('d M Y, H:i') }}
                                    @if(($entry['jenis'] ?? 'penilaian') === 'penilaian')
                                        @foreach($ta->komponen as $component)<div>{{ $component['nama'] }}: {{ $entry['sebelum'][$component['id']] ?? '-' }} → {{ $entry['sesudah'][$component['id']] ?? '-' }}</div>@endforeach
                                    @else <div>Menunggu penilaian ulang setelah pengumpulan/perbaikan.</div> @endif
                                </div>@endforeach
                            </details>@endif
                            @if($role !== 'mahasiswa' && $presence)<details style="font-weight:400;margin-top:8px"><summary class="ta-link" style="cursor:pointer">Riwayat Absensi</summary>@foreach($presence->riwayat as $entry)<div class="ta-history">{{ $entry['oleh'] }} · {{ $entry['waktu'] }}<div>{{ str_replace('_',' ',$entry['sesudah']['status']) }} · {{ $entry['sesudah']['keterangan'] ?? '-' }}</div></div>@endforeach</details>@endif
                        </td>
                        @foreach(['hadir','terlambat','tidak_hadir','alpa'] as $status)
                            <td style="padding:14px 16px;text-align:center">
                                @if($role === 'asprak')<input type="radio" name="status" value="{{ $status }}" form="{{ $attendanceForm }}" @checked($presence?->status === $status) required aria-label="{{ str_replace('_',' ',$status) }} — {{ $student->user?->name }}">
                                @else {{ $presence?->status === $status ? '✓' : '—' }} @endif
                            </td>
                        @endforeach
                        <td style="padding:14px 16px;min-width:180px;font-size:13px">
                            @if($role === 'asprak')<textarea name="keterangan" form="{{ $attendanceForm }}" maxlength="500" rows="1" placeholder="Maks. 500 karakter" aria-label="Keterangan absensi">{{ $presence?->keterangan }}</textarea>@else {{ $presence?->keterangan ?? '—' }} @endif
                        </td>
                        @foreach($ta->komponen as $component)
                            <td style="padding:14px 16px;font-size:13px;background:#F8FAFF">
                                @if($role === 'asprak')<label>Awal<input type="number" name="skor[{{ $component['id'] }}]" form="{{ $gradeForm }}" value="{{ $grade?->initialScores()[$component['id']] ?? '' }}" min="0" max="100" step="0.01" aria-label="{{ $component['nama'] }} — {{ $student->user?->name }}"></label>
                                    @php($componentSubmission = $submissions->first(fn($s) => $s->praktikan_id === $student->id && $s->tahap_id === ($component['tahap_id'] ?? $ta->tahap[0]['id'])))
                                    @if($componentSubmission && $componentSubmission->entries()->count() > 1)<label style="margin-top:6px">Revisi<input type="number" name="revisi[{{ $component['id'] }}]" form="{{ $gradeForm }}" value="{{ $grade?->revisionScores()[$component['id']] ?? '' }}" min="0" max="100" step="0.01"></label>@endif
                                    <span class="ta-muted">Hasil: {{ $grade?->skor[$component['id']] ?? '—' }}</span>
                                    @if(isset($grade?->skor[$component['id']]))<label style="margin-top:8px;font-size:11px;font-weight:400"><input type="checkbox" name="kosongkan[{{ $component['id'] }}]" value="1" form="{{ $gradeForm }}" style="width:auto"> Kosongkan nilai</label>@endif
                                @else {{ $grade?->skor[$component['id']] ?? '—' }} @endif
                            </td>
                        @endforeach
                        <td style="padding:14px 16px;font-size:13px;font-weight:700" x-text="average===null ? '—' : Number(average).toFixed(2)"></td>
                        <td style="padding:14px 16px;min-width:180px">
                            @if($role === 'asprak')
                                <form id="{{ $attendanceForm }}" method="POST" action="{{ route($base.'attendance',$routeArgs) }}">@csrf<input type="hidden" name="praktikan_id" value="{{ $student->id }}"><input type="hidden" name="version" value="{{ $presence?->version ?? 0 }}"><button class="ta-btn secondary" type="submit">Simpan Absensi</button></form>
                                <form id="{{ $gradeForm }}" method="POST" action="{{ route($base.'grade',$routeArgs) }}" style="margin-top:8px">@csrf<input type="hidden" name="praktikan_id" value="{{ $student->id }}"><input type="hidden" name="version" value="{{ $grade?->version ?? 0 }}"><button class="ta-btn" type="submit">Simpan Nilai</button></form>
                            @else <span class="ta-tag">{{ $grade ? ($grade->disetujui_koor && $grade->disetujui_dosen ? 'Dipublikasikan' : 'Menunggu Persetujuan') : 'Belum Dipublikasikan' }}</span> @endif
                            @if($role !== 'mahasiswa' && $grade)<p class="ta-muted" style="margin-top:8px">Koordinator: {{ $grade->disetujui_koor ? 'Disetujui' : 'Belum' }}<br>Dosen: {{ $grade->disetujui_dosen ? 'Disetujui' : 'Belum' }}</p>@endif
                        </td>
                    </tr>
                @empty <tr><td colspan="{{ count($ta->komponen)+8 }}" style="padding:40px;text-align:center;font-size:13px">Belum ada praktikan.</td></tr> @endforelse
            </tbody>
        </table>
    </div>
    <x-eoffice::manajemen-praktikum.ui.client-pagination :options="[5,10,20]" />

</section>
