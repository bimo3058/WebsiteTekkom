<x-eoffice::manajemen-praktikum.layout :breadcrumbPraktikum="$praktikum" pageTitle="Pengumpulan Tugas">
    @php
        $stage = collect($ta->tahap)->firstWhere('id', $selectedStage);
        $stageComponents = $ta->componentsFor($selectedStage);
        $needsSubmission = (bool)($stage['pengumpulan'] ?? true);
        $stageSubmissions = $submissions->where('tahap_id', $selectedStage)->keyBy('praktikan_id');
        $activeSubmissions=$stageSubmissions->reject(fn($submission)=>$submission->status==='dibatalkan');
        $total = $praktikan->count();
        $praktikan = $praktikan->sortBy(fn($student) => sprintf('%08d-%08d-%s', (int)$student->shift, (int)$student->kelompok, $student->user?->student?->student_number ?? $student->user?->name))->values();
        // Grades are shared across all stages, not separate grades for this submission.
        $graded = $praktikan->filter(function ($student) use ($grades, $stageComponents) {
            $grade = $grades->get($student->id);
            return $grade && count($stageComponents) && collect($stageComponents)->every(fn($c) => array_key_exists($c['id'], $grade->skor));
        })->count();
        $base = 'eoffice.manprak.'.$role.'.tugas-akhir.';
        $gradeUrl = route($base.'nilai', ['praktikum'=>$praktikum->id]);
        $componentColors = [['#EEF2FF','#4F46E5'],['#F0FDF4','#008B32'],['#FFFBEB','#B45309'],['#FFF7ED','#EA580C']];
        $hasRevisionColumns=(bool)($stage['revisi_aktif'] ?? false) || $stageSubmissions->contains(fn($s)=>$s->entries()->count()>1);
        $statistics = [
            ['Sudah Dikumpul', $activeSubmissions->count(), 'check'],
            ['Menunggu Penilaian', max(0, ($needsSubmission ? $activeSubmissions->count() : $total) - $graded), 'clock'],
            ['Sudah Dinilai', $graded, 'file'],
            [$needsSubmission ? 'Belum Mengumpulkan' : 'Tanpa Pengumpulan', $needsSubmission ? $total - $activeSubmissions->count() : $total, 'minus'],
        ];
    @endphp
    <style>
        .ta-collection-doc {display:flex;align-items:center;gap:6px;max-width:190px;overflow:hidden;border:1px solid #DFE1E7;border-radius:6px;padding:6px 10px;margin:0 0 4px;font-size:11px;font-weight:500;color:#353849;text-decoration:none;background:white}
        .ta-collection-doc span {overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
        .ta-collection-tag {font-size:10px!important;padding:2px 6px!important;white-space:nowrap}
        .ta-collection-table th,.ta-collection-table td {padding:12px 16px;vertical-align:middle}
        .ta-collection-table th {white-space:nowrap}
        .ta-collection-table td {font-size:13px}
        .ta-collection-inputs{display:grid;grid-template-columns:64px;gap:8px;align-items:start;justify-content:center;min-height:58px}.ta-collection-inputs.has-revision{grid-template-columns:64px 64px minmax(60px,1fr)}.ta-collection-inputs label>span,.ta-collection-inputs>div>span:first-child{display:block;min-height:16px;line-height:16px;margin-bottom:4px;font-size:11px;color:#666D80;font-weight:400}
        .ta-collection-score {display:block;margin:4px auto 0;width:64px;border:1px solid #DFE1E7;border-radius:8px;padding:4px 6px;font-size:12px;text-align:center;background:white;color:#111827}
        .ta-collection-table {border-collapse:collapse;text-align:left}
        .ta-collection-table thead {background:#F9FAFB;border-bottom:1px solid #DFE1E7}
        .ta-collection-table tbody tr {border-bottom:1px solid #DFE1E7}
        .ta-collection-table tbody tr:last-child {border-bottom:0}
        .ta-collection-table th {font-size:12px;font-weight:600;color:#666D80}
        .ta-collection-table td.ta-collection-group {text-align:center;border-left:1px solid #DFE1E7;border-right:1px solid #DFE1E7;font-weight:700;font-size:15px!important}
    </style>
    <x-slot name="header"><div class="mp-page-header" style="margin:0">
        <div><h1 class="mp-page-title">{{ $stage['nama'] }}</h1><p class="mp-page-sub">Tugas Akhir — {{ $praktikum->nama }}</p></div>
        <a class="mp-btn secondary md" style="text-decoration:none" href="{{ route('eoffice.manprak.'.$role.'.tugas.index', ['praktikum_id'=>$praktikum->id]) }}">Kembali</a>
    </div></x-slot>
    @if(!$ta->validWeights())<div role="alert" class="mp-alert warning" style="margin-bottom:15px">Bobot seluruh tugas akhir belum lengkap atau totalnya belum 100%. Rata-rata tahap belum dapat dihitung.</div>@endif
    <div class="mp-stats-grid cols-4" style="margin-bottom:20px">
        @foreach($statistics as [$label, $count, $icon])
            <div class="mp-stat" style="border:1px solid var(--c-border,#DFE1E7);border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.04)">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px"><span class="mp-stat-icon {{ ['green','yellow','sky',''][$loop->index] }}" @if($loop->index===3) style="background:#F3F4F6;color:#666D80" @endif><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">@if($icon === 'check')<path d="m7 12 3 3 7-7"/><circle cx="12" cy="12" r="10"/>@elseif($icon === 'clock')<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>@elseif($icon === 'file')<path d="M14 2H5v20h14V7zM14 2v5h5M8 12h8M8 16h8"/>@else<circle cx="12" cy="12" r="10"/><path d="m5 5 14 14"/>@endif</svg></span><span style="font-size:14px">{{ $label }}</span></div>
                <div style="font-size:32px;font-weight:700">{{ $count }}</div><p style="font-size:13px;color:#666D80">dari {{ $total }} mahasiswa</p>
            </div>
        @endforeach
    </div>
    <section class="mp-card" style="overflow:hidden;border:1px solid #DFE1E7;border-radius:12px" x-data="{
        page:1,perPage:10,globalSearch:'',globalKelompok:'',globalShift:'',changed:{},submitting:false,
        rows:{{ $praktikan->map(fn($student,$index)=>['id'=>$index,'name'=>mb_strtolower($student->user?->name ?? ''),'nim'=>(string)($student->user?->student?->student_number ?? ''),'kel'=>(string)($student->kelompok ?? ''),'shf'=>(string)($student->shift ?? '')])->values()->toJson() }},
        get hasChanges(){return Object.values(this.changed).some(Boolean)},
        get visibleRows(){return this.rows.filter(r=>(this.globalSearch==='' || r.name.includes(this.globalSearch.toLowerCase()) || r.nim.includes(this.globalSearch)) &amp;&amp; (this.globalKelompok==='' || r.kel===this.globalKelompok) &amp;&amp; (this.globalShift==='' || r.shf===this.globalShift))},
        get pageRows(){return this.visibleRows.slice((this.page-1)*this.perPage,this.page*this.perPage)},
        get totalPages(){return Math.max(1,Math.ceil(this.visibleRows.length/this.perPage))},
        span(index,type){const rows=this.pageRows;const i=rows.findIndex(r=>r.id===index);if(i<0)return 0;const same=(a,b)=>a.shf===b.shf &amp;&amp; (type==='shf'||a.kel===b.kel);if(i>0 &amp;&amp; same(rows[i-1],rows[i]))return 0;let size=1;while(i+size<rows.length &amp;&amp; same(rows[i],rows[i+size]))size++;return size}
    }" x-init="$watch('globalSearch',()=>page=1);$watch('globalKelompok',()=>page=1);$watch('globalShift',()=>page=1)">
        <div class="mp-card-header" style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;border-bottom:1px solid #DFE1E7">
            <div><h2 class="mp-card-title">{{ $needsSubmission ? 'Pengumpulan Tugas' : 'Penilaian Tahap' }}</h2><p style="font-size:12px;color:#666D80;margin-top:6px">Nilai tahap tersinkron dengan Absensi &amp; Nilai. Metode revisi mengikuti pengaturan koordinator.</p></div>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-left:auto">
                <div style="position:relative"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg><input type="search" x-model="globalSearch" placeholder="Cari nama / NIM..." aria-label="Cari nama atau NIM" class="mp-input" style="height:36px;width:210px;padding-left:32px;font-size:13px"></div>
                <x-eoffice::manajemen-praktikum.ui.grade-group-filter model="globalKelompok" label="Semua Kelompok" searchPlaceholder="Cari kelompok..." />
                <x-eoffice::manajemen-praktikum.ui.grade-group-filter model="globalShift" label="Semua Shift" searchPlaceholder="Cari shift..." />
                @if($role==='asprak' && count($stageComponents))
                <form id="taStageGrades" method="POST" action="{{ route($base.'grade-batch',['praktikum'=>$praktikum->id]) }}" @submit="if(!hasChanges || submitting){$event.preventDefault()}else{submitting=true}">
                    @csrf<input type="hidden" name="tahap_id" value="{{ $selectedStage }}">
                    <button type="submit" class="mp-btn primary md" disabled :disabled="!hasChanges || submitting || {{ !$praktikum->is_active?'true':'false' }}" style="height:36px;min-width:100px;padding:0 12px;border-radius:8px;font-size:13px;font-weight:700" :style="{cursor:hasChanges ? 'pointer' : 'not-allowed',background:hasChanges ? '#0B266E' : '#F4F6F8',color:hasChanges ? '#fff' : '#0B266E',border:hasChanges ? '1px solid #0B266E' : '1px solid #DFE1E7',pointerEvents:'auto'}">Simpan</button>
                </form>
                @endif
            </div>
        </div>
        <div style="overflow-x:auto">
            <table class="mp-table ta-collection-table" style="min-width:1500px;width:100%">
                <thead><tr><th class="mp-th" style="text-align:center;width:50px">NO</th><th class="mp-th" style="width:250px">NAMA MAHASISWA</th><th class="mp-th" style="width:150px">NIM</th><th class="mp-th" style="text-align:center">KELOMPOK</th><th class="mp-th" style="text-align:center">SHIFT</th><th class="mp-th">DOKUMEN</th><th class="mp-th">WAKTU PENGUMPULAN</th>@foreach($stageComponents as $column)<th class="mp-th" style="text-align:center;background:{{ $componentColors[$loop->index % count($componentColors)][0] }};color:{{ $componentColors[$loop->index % count($componentColors)][1] }}"><div>{{ mb_strtoupper($column['nama']) }}</div><div style="font-size:11px;font-weight:600;margin-top:4px">{{ $column['bobot'] }}%</div></th>@endforeach<th class="mp-th" style="text-align:center">RATA-RATA</th><th class="mp-th">STATUS</th><th class="mp-th">DOKUMEN REVISI</th><th class="mp-th">WAKTU PENGUMPULAN REVISI</th></tr></thead>
                <tbody>
                    @forelse($praktikan as $student)
                        @php
                            $submission = $stageSubmissions->get($student->id);
                            $entries = $submission?->entries() ?? collect();
                            $original = $entries->first();
                            $revision = $entries->count() > 1 ? $entries->last() : null;
                            $grade = $grades->get($student->id);

                            $initial=collect($stageComponents)->mapWithKeys(fn($c)=>[$c['id']=>(string)($grade?->initialScores()[$c['id']] ?? '')])->all();
                            $revised=$revision ? collect($stageComponents)->mapWithKeys(fn($c)=>[$c['id']=>(string)($grade?->revisionScores()[$c['id']] ?? '')])->all() : [];

                        @endphp
                        <tr x-show="pageRows.some(r=>r.id==={{ $loop->index }})" x-data="{
                            stageWeights:{{ json_encode(collect($stageComponents)->pluck('bobot','id')->all()) }},validWeights:{{ $ta->validWeights()?'true':'false' }},policy:{{ json_encode($stage['revisi_metode'] ?? 'rata_rata') }},initialWeight:{{ $stage['revisi_bobot_awal'] ?? 50 }},
                            result(key){const a=Number(this.scores[key]||0),value=this.revisions[key];if(value==null||value==='')return a;const b=Number(value);if(this.policy==='ganti')return b;if(this.policy==='tertinggi')return Math.max(a,b);if(this.policy==='berbobot')return (a*this.initialWeight+b*(100-this.initialWeight))/100;return (a+b)/2},
                            get average(){const total=Object.values(this.stageWeights).reduce((sum,w)=>sum+Number(w),0);return this.validWeights &amp;&amp; total ? Object.entries(this.stageWeights).reduce((sum,[key,w])=>sum+this.result(key)*Number(w),0)/total : null},
                            scores:{{ json_encode(old('students.'.$student->id.'.skor',$initial)) }},initial:{{ json_encode($initial) }},
                            revisions:{{ json_encode(old('students.'.$student->id.'.revisi',$revised)) }},initialRevisions:{{ json_encode($revised) }},
                            different(values,original){return Object.keys(original).some(k=>{const a=values[k]??'',b=original[k]??'';return (a==='')!==(b==='') || (a!=='' &amp;&amp; Number(a)!==Number(b))})},
                            get dirty(){return this.different(this.scores,this.initial) || this.different(this.revisions,this.initialRevisions)}
                        }" x-effect="changed[{{ json_encode((string)$student->id) }}]=dirty">
                            <td style="text-align:center;font-size:13px;font-weight:700;color:#666D80">{{ $loop->iteration }}
                                @if($role==='asprak' && count($stageComponents))<input type="hidden" form="taStageGrades" name="students[{{ $student->id }}][version]" value="{{ $grade?->version ?? 0 }}" :disabled="!dirty">@endif
                            </td>
                            <td><div style="display:flex;align-items:center;gap:10px;min-width:220px"><span class="mp-av sky" style="width:28px;height:28px;flex-shrink:0;font-size:11px">{{ mb_strtoupper(mb_substr($student->user?->name ?? 'M',0,2)) }}</span><div><div style="font-size:13px;font-weight:600;color:#0D0D12">{{ $student->user?->name ?? 'Mahasiswa' }}</div><div style="font-size:11.5px;color:#666D80;margin-top:2px">{{ $student->user?->email }}</div></div></div></td>
                            <td style="font-size:12px;font-weight:600;color:#0D0D12;white-space:nowrap">{{ $student->user?->student?->student_number ?? '-' }}</td>
                            <td class="ta-collection-group" x-show="span({{ $loop->index }},'kel')>0" :rowspan="span({{ $loop->index }},'kel')">{{ $student->kelompok ?? '-' }}</td>
                            <td class="ta-collection-group" x-show="span({{ $loop->index }},'shf')>0" :rowspan="span({{ $loop->index }},'shf')">{{ $student->shift ?? '-' }}</td>
                            <td>@include('eoffice::manajemen-praktikum.tugas-akhir.submission-documents', ['entry'=>$original])</td>
                            <td style="white-space:nowrap;color:#0F6E56;font-weight:600;font-size:12px">{{ $original ? \Illuminate\Support\Carbon::parse($original['waktu'])->translatedFormat('d M Y, H:i').' WIB' : '-' }}</td>
                            @foreach($stageComponents as $column)
                                <td style="text-align:center;vertical-align:top;background:{{ $componentColors[$loop->index % count($componentColors)][0] }}">@if($role==='asprak')
                                    <div class="ta-collection-inputs {{ $hasRevisionColumns?'has-revision':'' }}">
                                        <label><span>{{ $hasRevisionColumns?'Awal':'' }}</span><x-eoffice::manajemen-praktikum.ui.grade-score-input :model="'scores['.json_encode($column['id']).']'" :label="$column['nama'].' — '.$student->user?->name" :disabled="!$praktikum->is_active" form="taStageGrades" x-bind:required="dirty" /></label>
                                        @if($hasRevisionColumns)
                                            <label><span>Revisi</span>@if($revision)<x-eoffice::manajemen-praktikum.ui.grade-score-input :model="'revisions['.json_encode($column['id']).']'" :label="'Revisi '.$column['nama'].' — '.$student->user?->name" :disabled="!$praktikum->is_active" form="taStageGrades" />@else <x-eoffice::manajemen-praktikum.ui.grade-score-input :unavailable="true" label="Belum mengumpulkan revisi" /> @endif</label>
                                            <div title="{{ ['ganti'=>'Nilai revisi','tertinggi'=>'Nilai tertinggi','rata_rata'=>'Rata-rata awal dan revisi','berbobot'=>'Berbobot'][$stage['revisi_metode'] ?? 'rata_rata'] ?? 'Rata-rata awal dan revisi' }}"><span>Hasil</span><span style="font-size:12px;font-weight:600;line-height:32px" x-text="Number.isFinite(result({{ json_encode($column['id']) }})) ? result({{ json_encode($column['id']) }}).toFixed(2).replace('.',',') : '—'"></span></div>
                                        @endif
                                    </div>
                                    <input type="hidden" form="taStageGrades" name="students[{{ $student->id }}][skor][{{ $column['id'] }}]" :value="scores[{{ json_encode($column['id']) }}]" :disabled="!dirty">
                                    @if($revision)<input type="hidden" form="taStageGrades" name="students[{{ $student->id }}][revisi][{{ $column['id'] }}]" :value="revisions[{{ json_encode($column['id']) }}]" :disabled="!dirty">@endif
                                @else {{ str_replace('.',',',(string)($grade?->skor[$column['id']] ?? '-')) }} @endif</td>
                            @endforeach
                            <td style="text-align:center"><div x-text="average===null || !Number.isFinite(Number(average)) ? '—' : Number(average).toFixed(2).replace('.',',')" style="font-size:13px;font-weight:600;min-width:45px"></div></td>
                            <td><span class="mp-badge sm ta-collection-tag {{ !$submission || $submission->status==='dibatalkan' ? 'neutral' : ($submission->status==='perlu_perbaikan' ? 'error' : ($grade && collect($stageComponents)->every(fn($c)=>isset($grade->skor[$c['id']])) ? 'success' : 'warning')) }}"><span class="dot"></span>{{ !$needsSubmission ? 'Tanpa Pengumpulan' : (!$submission || $submission->status==='dibatalkan' ? 'Belum Kumpul' : ($submission->status==='perlu_perbaikan' ? ($submission->deadline_revisi && now()->gt($submission->deadline_revisi) ? 'Revisi Berakhir' : 'Perlu Perbaikan') : ($grade && collect($stageComponents)->every(fn($c)=>isset($grade->skor[$c['id']])) ? 'ACC' : 'Menunggu'))) }}</span></td>
                            <td>@include('eoffice::manajemen-praktikum.tugas-akhir.submission-documents', ['entry'=>$revision, 'emptyLabel'=>'-'])</td>
                            <td style="white-space:nowrap;color:#0F6E56;font-weight:600;font-size:12px">{{ $revision ? \Illuminate\Support\Carbon::parse($revision['waktu'])->translatedFormat('d M Y, H:i').' WIB' : '-' }}</td>
                        </tr>
                    @empty<tr><td colspan="{{ 11 + count($stageComponents) }}" style="text-align:center;padding:40px;color:#666D80">Belum ada praktikan yang terdaftar.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        <p x-show="visibleRows.length===0" style="padding:16px 20px;font-size:13px;color:#666D80">Data tidak ditemukan.</p>
        <x-eoffice::manajemen-praktikum.ui.client-pagination :options="[10,20,30]" />
    </section>
</x-eoffice::manajemen-praktikum.layout>
