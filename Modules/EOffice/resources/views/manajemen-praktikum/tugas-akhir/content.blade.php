    @php
        $base = 'eoffice.manprak.'.$role.'.tugas-akhir.';
        $routeArgs = ['praktikum' => $praktikum->id];
        $stages = collect($ta?->tahap ?? [['nama'=>'', 'ketentuan'=>'', 'deadline'=>'']])->map(function ($stage) use ($ta) {
            $stage['_key'] = $stage['id'] ?? (string) \Illuminate\Support\Str::uuid();
            $stage['pengumpulan'] = (bool)($stage['pengumpulan'] ?? true);
            $stage['parameter'] = isset($stage['id']) ? ($ta?->componentsFor($stage['id']) ?? []) : [];
            $stage['revisi_aktif'] = (bool)($stage['revisi_aktif'] ?? false);
            $stage['revisi_metode'] = $stage['revisi_metode'] ?? 'rata_rata';
            $stage['revisi_deadline'] = $stage['revisi_deadline'] ?? '';
            $stage['revisi_bobot_awal'] = $stage['revisi_bobot_awal'] ?? 50;
            return $stage;
        })->all();
        $stageGrades = $ta ? \Modules\EOffice\Models\TaNilai::where('tugas_akhir_id', $ta->id)->get() : collect();
        $taWeightsLocked=$stageGrades->contains(fn($grade)=>$grade->disetujui_koor || $grade->disetujui_dosen);
        $usedStages = $ta ? \Modules\EOffice\Models\TaPengumpulan::where('tugas_akhir_id', $ta->id)->pluck('tahap_id') : collect();
        $editor = ['tahap' => collect(old('tahap', $stages))->map(function ($stage) use ($ta, $stageGrades, $usedStages) {
            $stageComponents = isset($stage['id']) ? ($ta?->componentsFor($stage['id']) ?? []) : [];
            $gradedIds = collect($stageComponents)->pluck('id')->filter(fn($id) => $stageGrades->contains(fn($grade) => isset($grade->skor[$id]) || isset($grade->initialScores()[$id]) || isset($grade->revisionScores()[$id])));
            $stage['_deleteBlocked'] = $gradedIds->isNotEmpty() || $usedStages->contains($stage['id'] ?? null);
            $stage['_structureLocked'] = $stage['_deleteBlocked'];
            $stage['_hasSubmissions'] = $usedStages->contains($stage['id'] ?? null);
            $stage['_key'] = $stage['_key'] ?? $stage['id'] ?? (string) \Illuminate\Support\Str::uuid();
            $stage['parameter'] = $stage['parameter'] ?? [];
            $stage['parameter'] = collect($stage['parameter'])->map(function ($parameter) use ($gradedIds,$stage) {
                $parameter['_deleteBlocked'] = $stage['_structureLocked'] || $gradedIds->contains($parameter['id'] ?? null);
                return $parameter;
            })->all();
            if (!count($stage['parameter'])) $stage['parameter'] = [['nama'=>'']];
            $stage['dibuka_pada'] = $stage['dibuka_pada'] ?? '';
            $stage['pengumpulan'] = (bool)($stage['pengumpulan'] ?? true);
            $stage['revisi_aktif'] = (bool)($stage['revisi_aktif'] ?? false);
            return $stage;
        })->all()];
    @endphp
    <style>
        .ta-body{padding:20px;display:flex;flex-direction:column;gap:15px}.ta-heading{padding:16px 20px;border-bottom:1px solid #DFE1E7;background:#FAFBFC;font-size:15px;font-weight:700}
        .ta-form label{display:block;font-size:13px;font-weight:600;margin-bottom:6px}.ta-form input:not([type=file]),.ta-form textarea{width:100%;border:1px solid #DFE1E7;border-radius:8px;padding:10px 12px;font-size:13px;background:white}.ta-form textarea{min-height:40px;max-height:180px;resize:vertical}
        .ta-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;font-size:13px!important;font-weight:600;padding:10px 16px!important;border-radius:8px;background:#0B266E;color:white;border:1px solid #0B266E;cursor:pointer;white-space:nowrap}.ta-btn.secondary{background:white;color:#353849;border-color:#DFE1E7}.ta-btn.danger{background:#FFF1F2;color:#DF1C41;border-color:#FECDD3}
        .ta-muted{font-size:13px;color:#666D80}.ta-note{padding:16px;border:1px solid #DFE1E7;background:#F9FAFB;border-radius:10px;color:#475569;font-size:13px;white-space:pre-wrap;overflow-wrap:anywhere}
        .ta-tag{display:inline-flex;padding:4px 10px;border-radius:20px;background:#EFF6FF;color:#0B266E;font-size:12px;font-weight:700}.ta-row{display:grid;grid-template-columns:1fr 160px auto;gap:12px;align-items:end}.ta-card{overflow:hidden;flex-shrink:0}.ta-history{border-top:1px solid #DFE1E7;padding:14px 0;font-size:13px}.ta-link{color:#0B266E;text-decoration:underline;overflow-wrap:anywhere}
        @media(max-width:700px){.ta-row{grid-template-columns:1fr}.ta-body{padding:16px}}
    </style>
    <style>
        .mp-app .ta-card-toggle{padding:16px 24px;line-height:1.5}.mp-app .ta-card-toggle strong{margin:0 0 4px;font-size:16px;line-height:24px}.mp-app .ta-card-toggle span span{font-size:13px;color:#666D80;line-height:18px;margin:0 0 4px}.mp-app .ta-card-toggle span span:last-child{margin-bottom:0}.mp-app .ta-editor .ta-body{padding:24px;gap:20px}.mp-app .ta-stage-body{padding:20px;gap:20px}.mp-app .ta-editor textarea{min-height:42px;height:42px;line-height:20px;max-height:900px}.mp-app .ta-upload{height:46px;padding:10px 14px;font-size:13px}.mp-app .ta-file{padding:8px 12px;min-height:36px;margin-top:4px}.mp-app .ta-file-remove{border:0;background:none;color:#DF1C41;cursor:pointer;padding:0;font-size:18px;line-height:18px}.mp-app .ta-divider{padding-top:20px}.mp-app .ta-stage-actions{display:flex;justify-content:flex-end;padding-top:16px;border-top:1px solid #DFE1E7}.mp-app .ta-add-row{display:flex;justify-content:flex-end;margin-top:10px}.mp-app .ta-editor .ta-add-button{background:#0B266E;color:white;border:1px solid #0B266E;box-shadow:0 2px 4px rgba(11,38,110,.15)}
    </style>
    <style>
        .ta-card-toggle{display:flex;align-items:center;justify-content:space-between;gap:20px;width:100%;padding:24px 30px;border:0;background:white;text-align:left;cursor:pointer;color:#666D80}.ta-card-toggle strong{display:block;font-size:16px;color:#111827;margin-bottom:6px}.ta-card-toggle span span{display:block;font-size:13px;margin-top:6px}.ta-editor .ta-body{padding:28px;gap:24px}.ta-editor label{font-weight:500;color:#111827}.ta-editor textarea{min-height:90px}.ta-editor input::placeholder,.ta-editor textarea::placeholder{color:#818898}.ta-editor .ta-muted{font-size:13px;color:#666D80;margin-top:8px;line-height:1.5}.ta-required{color:#DF1C41}.ta-divider{border-top:1px dashed #DFE1E7;padding-top:24px}.ta-divider h3{font-size:14px;font-weight:700}.ta-stage{border:1px solid #DFE1E7;border-radius:12px;background:white}.ta-stage-heading{display:flex;align-items:center;gap:16px;padding:16px 20px;background:#FAFBFC;border-radius:12px 12px 0 0}.ta-stage-heading>button:first-child{display:flex;align-items:center;justify-content:space-between;flex:1;border:0;background:none;font-size:14px;text-align:left;color:#353849;cursor:pointer}.ta-stage-body{padding:24px;display:grid;gap:24px;border-top:1px solid #DFE1E7}.ta-two-columns{display:grid;grid-template-columns:1fr 1fr;gap:20px}.ta-parameter{display:flex;gap:12px;margin-top:12px;align-items:center}.ta-select{display:flex;align-items:center;justify-content:space-between;gap:12px;width:100%;min-height:42px;border:1px solid #DFE1E7;border-radius:8px;background:white;padding:10px 12px;text-align:left;font-size:13px;color:#353849;cursor:pointer}.ta-options{position:absolute;top:calc(100% + 5px);left:0;right:0;z-index:40;background:white;border:1px solid #DFE1E7;border-radius:8px;padding:5px;box-shadow:0 6px 18px rgba(0,0,0,.1)}.ta-options button{display:block;width:100%;padding:10px 12px;border:0;border-radius:6px;background:white;text-align:left;font-size:13px;cursor:pointer}.ta-options button:hover,.ta-options button.selected{background:#EEF1FA;color:#0B266E}.ta-upload{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:16px;border:1px dashed #DFE1E7;background:white;border-radius:8px;color:#0B266E;font-size:13px;cursor:pointer}.ta-upload-hint{display:flex;justify-content:space-between;gap:12px;font-size:12px;color:#818898;margin:10px 0}.ta-file{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 12px;background:#F8F9FB;border:1px solid #DFE1E7;border-radius:6px;margin-top:6px;font-size:13px;overflow-wrap:anywhere}.ta-editor .mp-btn{font-size:13px!important;padding:10px 16px!important;white-space:nowrap}
        @media(max-width:700px){.ta-two-columns{grid-template-columns:1fr}.ta-editor .ta-body,.ta-stage-body{padding:16px}.ta-card-toggle{padding:20px}.ta-upload-hint{flex-wrap:wrap}}
    </style>
    @if($errors->any()) <div class="mp-flash mp-flash-error" role="alert">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div> @endif
    <style>.ta-date-range{display:grid;gap:20px}.ta-editor .ta-upload{max-height:46px}.ta-editor .ta-add-row .mp-btn{font-size:13px}.ta-editor .ta-stage-actions{margin-top:0}</style>
    <style>
        .mp-app .ta-editor label{font-size:12px;font-weight:700;color:var(--c-fg)}
        .mp-app .ta-editor label .ta-muted{font-weight:400}
        .ta-editor .ta-stage-heading{border-radius:11px}
        .ta-editor .ta-stage-heading.is-open{border-radius:11px 11px 0 0}
        .ta-editor .ta-stage-body{border-radius:0 0 11px 11px}
        .ta-stage-expand{display:grid;grid-template-rows:0fr;opacity:0;transition:grid-template-rows .24s ease,opacity .2s ease}
        .ta-stage-expand.is-open{grid-template-rows:1fr;opacity:1}
        .ta-stage-clip{min-height:0;overflow:hidden}
        .ta-stage-expand.is-open:not(.is-animating)>.ta-stage-clip{overflow:visible}
        .mp-app .ta-editor .ta-parameter{gap:0;border:1px solid var(--c-border);border-radius:8px;background:white;overflow:hidden}
        .mp-app .ta-editor .ta-parameter:focus-within{border-color:var(--c-primary);box-shadow:0 0 0 3px rgba(11,38,110,.08)}
        .mp-app .ta-editor .ta-parameter input:not([type=hidden]){flex:1;min-width:0;border:0;border-radius:0;background:transparent;box-shadow:none}
        .mp-app .ta-editor .ta-icon-button{width:36px;min-width:36px;height:36px;margin-right:4px;padding:0!important;flex-shrink:0;border:0;background:transparent;color:var(--c-error-100);box-shadow:none}
        .mp-app .ta-editor .ta-icon-button:hover{background:transparent;box-shadow:none}
        .mp-app .ta-editor .ta-file-remove{display:inline-flex;align-items:center;justify-content:center;min-width:28px;min-height:28px;flex-shrink:0}
        .mp-app .ta-editor .ta-upload-hint{margin-bottom:5px}
        .mp-app .ta-editor .ta-editor-section-heading>.ta-muted{margin:6px 0 0;line-height:18px}
        @media(prefers-reduced-motion:reduce){.ta-stage-expand{transition:none}.ta-stage-heading svg{transition:none!important}}
    </style>

    @if($role === 'koor' && $section === 'modul')
        @include('eoffice::manajemen-praktikum.tugas-akhir.editor')
    @endif
    @if(!$ta || (!$ta->published && $role !== 'koor'))
        @unless($role === 'koor' && $section === 'modul')
        <div class="mp-card ta-body" style="text-align:center;padding:48px"><x-eoffice::manajemen-praktikum.ui.empty-icon name="book" /><h3>Belum Ada Tugas Akhir</h3><p class="ta-muted">Ketentuan tugas akhir belum dipublikasikan oleh koordinator.</p></div>
        @endunless
    @elseif($ta->published)
        @if($section === 'modul' && $role !== 'koor')
        <section class="mp-card ta-card"><h2 class="ta-heading">Tugas Akhir</h2><div class="ta-body"><div class="ta-note">{{ $ta->ketentuan }}</div>
            @foreach($ta->lampiran ?? [] as $attachment)<a class="ta-link" target="_blank" rel="noopener noreferrer" href="{{ route($base.'download', $routeArgs + ['path' => $attachment['path']]) }}">{{ $attachment['nama'] }}</a>@endforeach
            <p class="ta-muted">Setiap tahap menerima file dan/atau link. Maksimal 3 file, masing-masing 5 MB. Pastikan link dapat diakses asisten.</p>
            @foreach($ta->komponen as $component)<div style="font-size:13px">{{ $component['nama'] }} — <strong>{{ $component['bobot'] }}%</strong></div>@endforeach
        </div></section>

        @endif
        @if($section === 'tugas')
        @foreach($ta->tahap as $stage)
            @continue(($selectedStage ?? null) && $selectedStage !== $stage['id'])
            @if($role === 'mahasiswa')
                <section class="mp-card" style="overflow:hidden">
                    <div style="padding:16px 24px;border-bottom:1px solid #DFE1E7"><h2 style="font-size:16px;font-weight:700;margin:0">{{ $stage['nama'] }}</h2><p style="font-size:12px;color:#666D80;margin:4px 0 0">{{ ($stage['pengumpulan']??true) && !empty($stage['deadline']) ? 'Tenggat: '.\Illuminate\Support\Carbon::parse($stage['deadline'])->format('d/m/Y, H:i') : 'Tanpa pengumpulan' }}</p></div>
                    <div style="padding:16px 24px;background:#FAFBFC">
                        <div style="font-size:13px;color:#374151;white-space:pre-wrap;margin-bottom:16px">{{ $stage['ketentuan'] }}</div>
                        @include('eoffice::manajemen-praktikum.tugas-akhir.attachments',['files'=>$stage['lampiran']??[]])
                        @if($stage['revisi_aktif']??false)@include('eoffice::manajemen-praktikum.tugas-akhir.revision-summary')@endif
                        @foreach($praktikan as $student)@include('eoffice::manajemen-praktikum.tugas-akhir.student-submission')@endforeach
                    </div>
                </section>
            @else
            <section class="mp-card ta-card"><h2 class="ta-heading">{{ $stage['nama'] }} <span class="ta-tag">{{ ($stage['pengumpulan'] ?? true) && !empty($stage['deadline']) ? \Illuminate\Support\Carbon::parse($stage['deadline'])->format('d M Y, H:i') : 'Tanpa pengumpulan' }}</span></h2><div class="ta-body"><div class="ta-note">{{ $stage['ketentuan'] }}</div>
            @if($stage['revisi_aktif'] ?? false)<p class="ta-muted">Revisi hingga {{ \Illuminate\Support\Carbon::parse($stage['revisi_deadline'])->format('d M Y, H:i') }}. Perhitungan: {{ str_replace('_',' ', $stage['revisi_metode']) }}{{ $stage['revisi_metode'] === 'berbobot' ? ' (bobot awal '.$stage['revisi_bobot_awal'].'%)' : '' }}. Jika tidak mengumpulkan revisi, nilai awal tetap digunakan.</p>@endif
            @foreach($stage['lampiran'] ?? [] as $attachment)<a class="ta-link" target="_blank" rel="noopener noreferrer" href="{{ route($base.'download', $routeArgs + ['path' => $attachment['path']]) }}">{{ $attachment['nama'] }}</a>@endforeach
            @forelse($praktikan as $student)
                @php
                    $submission = $submissions->first(fn($item) => $item->praktikan_id === $student->id && $item->tahap_id === $stage['id']);
                    $cancelledSubmission=$submission?->status==='dibatalkan';
                    $previousEntry=$cancelledSubmission ? collect($submission->riwayat)->last(fn($entry)=>($entry['jenis']??null)==='pengumpulan') : null;
                    $studentGrade=$stageGrades->firstWhere('praktikan_id',$student->id);
                    $hasStageGrade=collect($ta->componentsFor($stage['id']))->contains(fn($c)=>isset($studentGrade?->skor[$c['id']]) || isset($studentGrade?->initialScores()[$c['id']]) || isset($studentGrade?->revisionScores()[$c['id']]));
                    $canUnsubmit=$submission && $submission->status==='dikumpulkan' && $submission->entries()->count()===1 && !$hasStageGrade && !empty($stage['deadline']) && now()->lte($stage['deadline']) && (empty($stage['dibuka_pada']) || now()->gte($stage['dibuka_pada']));
                    $initialLate = (!$submission || $cancelledSubmission) && ($stage['pengumpulan'] ?? true) && !empty($stage['deadline']) && now()->gt($stage['deadline']);
                    $revisionOpen = ($stage['revisi_aktif'] ?? false) && !empty($stage['revisi_deadline']) && now()->lte($stage['revisi_deadline']);
                    $canSubmit = (!$submission || $cancelledSubmission)
                        ? (!$initialLate || $revisionOpen)
                        : $revisionOpen;
                    $canSubmit = $canSubmit && (empty($stage['dibuka_pada']) || now()->gte($stage['dibuka_pada']));
                @endphp
                <details style="border:1px solid #DFE1E7;border-radius:10px;padding:16px" @if($role === 'mahasiswa') open @endif>
                    <summary style="cursor:pointer;font-size:13px;font-weight:600">{{ $student->user?->name ?? 'Mahasiswa' }} <span class="ta-tag">{{ !($stage['pengumpulan'] ?? true) ? 'Tanpa pengumpulan' : ($submission && !$cancelledSubmission ? ($submission->status === 'perlu_perbaikan' ? 'Perlu Perbaikan' : 'Dikumpulkan') : 'Belum Dikumpulkan') }}</span></summary>
                    @if($submission)
                        @foreach($submission->riwayat as $entry)
                            <div class="ta-history"><div class="ta-muted">{{ $entry['oleh'] }} · {{ \Illuminate\Support\Carbon::parse($entry['waktu'])->format('d M Y, H:i') }} · {{ $entry['jenis'] === 'pembatalan' ? 'Pengumpulan dibatalkan' : ($entry['jenis'] === 'perbaikan' ? 'Permintaan perbaikan' : (['terlambat'=>'Pengumpulan pertama — Terlambat', 'revisi'=>'Pengumpulan revisi', 'awal'=>'Pengumpulan awal'][$entry['kategori'] ?? 'awal'] ?? 'Pengumpulan')) }}</div>
                                @foreach($entry['files'] ?? [] as $file)<div><a class="ta-link" target="_blank" rel="noopener noreferrer" href="{{ route($base.'download', $routeArgs + ['path' => $file['path']]) }}">{{ $file['nama'] }}</a></div>@endforeach
                                @if(!empty($entry['link']))<div><a class="ta-link" href="{{ $entry['link'] }}" target="_blank" rel="noopener noreferrer">{{ $entry['link'] }}</a></div>@endif
                                @if(($entry['jenis'] ?? null) === 'perbaikan' && !empty($entry['catatan']))<p style="white-space:pre-wrap">{{ $entry['catatan'] }}</p>@endif
                                @if(!empty($entry['deadline']))<p class="ta-muted">Tenggat perbaikan: {{ \Illuminate\Support\Carbon::parse($entry['deadline'])->format('d M Y, H:i') }}</p>@endif
                            </div>
                        @endforeach
                    @endif
                    @if($role === 'mahasiswa' && $canUnsubmit)
                        @include('eoffice::manajemen-praktikum.tugas-akhir.unsubmit-confirmation')
                    @endif
                    @if($role === 'mahasiswa' && ($stage['pengumpulan'] ?? true) && $canSubmit)
                        @if($cancelledSubmission)<p class="mp-alert warning">Pengumpulan dibatalkan dan belum dianggap masuk. Perbaiki berkas atau tautan, lalu kumpulkan kembali.</p>@endif
                        @if($initialLate)<p class="ta-muted" style="margin-top:12px">Pengumpulan pertama dicatat terlambat dan dinilai sebagai nilai awal, bukan revisi. Tenggat: {{ \Illuminate\Support\Carbon::parse($stage['revisi_deadline'])->format('d M Y, H:i') }}.</p>@endif
                        <form class="ta-form ta-body" method="POST" enctype="multipart/form-data" action="{{ route($base.'submit', $routeArgs) }}">@csrf<input type="hidden" name="tahap_id" value="{{ $stage['id'] }}">
                            @if($cancelledSubmission && count($previousEntry['files'] ?? []))<label style="font-weight:400"><input type="hidden" name="reuse_files" value="0"><input type="checkbox" name="reuse_files" value="1" @checked(old('reuse_files',1)) style="width:18px!important;height:18px!important;accent-color:#0B266E"> Gunakan berkas pengumpulan sebelumnya</label>@endif
                            <div><label>File (maksimal 3 file, 5 MB/file)</label><input type="file" name="files[]" multiple></div><div><label>Link (opsional apabila mengunggah file)</label><input type="url" name="link" value="{{ old('link',$previousEntry['link'] ?? '') }}" maxlength="2000" placeholder="https://"></div><div><button type="submit" class="ta-btn">Kumpulkan</button></div>
                        </form>
                    @elseif($role === 'mahasiswa' && ($stage['pengumpulan'] ?? true) && (!$submission || $cancelledSubmission || $submission->status === 'perlu_perbaikan'))
                        <p class="ta-muted" style="margin-top:12px">{{ !empty($stage['dibuka_pada']) && now()->lt($stage['dibuka_pada']) ? 'Pengumpulan dibuka pada '.\Illuminate\Support\Carbon::parse($stage['dibuka_pada'])->format('d M Y, H:i').'.' : 'Pengumpulan ditutup karena tenggat sudah lewat atau periode revisi tidak aktif.' }}</p>
                    @endif
                </details>
            @empty <p class="ta-muted">Belum ada praktikan yang terdaftar.</p> @endforelse
            </div></section>
            @endif
        @endforeach

        @endif
        @if($section === 'nilai')
            @include('eoffice::manajemen-praktikum.tugas-akhir.penilaian')
        @endif
    @endif
