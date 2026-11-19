@php
    $base='eoffice.manprak.mahasiswa.tugas-akhir.';
    $routeArgs=['praktikum'=>$praktikum->id];
    $submission=$submissions->first(fn($item)=>$item->praktikan_id===$student->id && $item->tahap_id===$stage['id']);
    $cancelledSubmission=$submission?->status==='dibatalkan';
    $previousEntry=$cancelledSubmission ? collect($submission->riwayat)->last(fn($entry)=>($entry['jenis']??null)==='pengumpulan') : null;
    $studentGrade=$stageGrades->firstWhere('praktikan_id',$student->id);
    $hasStageGrade=collect($ta->componentsFor($stage['id']))->contains(fn($c)=>isset($studentGrade?->skor[$c['id']]) || isset($studentGrade?->initialScores()[$c['id']]) || isset($studentGrade?->revisionScores()[$c['id']]));
    $canUnsubmit=$submission && $submission->status==='dikumpulkan' && $submission->entries()->count()===1 && !$hasStageGrade && !empty($stage['deadline']) && now()->lte($stage['deadline']) && (empty($stage['dibuka_pada']) || now()->gte($stage['dibuka_pada']));
    $initialLate=(!$submission || $cancelledSubmission) && ($stage['pengumpulan']??true) && !empty($stage['deadline']) && now()->gt($stage['deadline']);
    $revisionOpen=($stage['revisi_aktif']??false) && !empty($stage['revisi_deadline']) && now()->lte($stage['revisi_deadline']);
    $canSubmit=(!$submission || $cancelledSubmission) ? (!$initialLate || $revisionOpen) : $revisionOpen;
    $canSubmit=$canSubmit && (empty($stage['dibuka_pada']) || now()->gte($stage['dibuka_pada']));
    $canSubmit=$canSubmit && (bool)$praktikum->is_active;
    $canUnsubmit=$canUnsubmit && (bool)$praktikum->is_active;
    $entries=$submission?->entries() ?? collect();
    $displayEntries=collect($submission?->riwayat??[])->filter(fn($entry)=>in_array($entry['jenis']??null,['pengumpulan','perbaikan']));
@endphp
@once
<style>
    .mp-student-stage-section{border-top:1px dashed #DFE1E7;margin-top:16px;padding-top:16px;font-size:13px;color:#374151;line-height:1.5}
    .mp-student-stage-heading{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:12px}
    .mp-student-stage-heading h3{font-size:14px;font-weight:700;color:#353849;margin:0}
    .mp-student-stage-time{font-size:12px;color:#666D80}
    .mp-student-stage-file{display:flex;align-items:center;gap:8px;padding:10px 12px;border:1px solid #DFE1E7;border-radius:8px;background:#F8F9FB;color:#0B266E;text-decoration:none;font-size:13px;overflow-wrap:anywhere}
    .mp-student-stage-form{display:grid;gap:16px;margin-top:16px}
    .mp-student-stage-form label{display:block;font-size:13px;font-weight:600;color:#353849;margin-bottom:8px}
    .mp-student-stage-form .ta-upload{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:12px 20px;border:1px dashed #DFE1E7;border-radius:8px;background:#fff;color:#0B266E;font-size:14px;cursor:pointer}
    .mp-student-stage-form .ta-upload-hint{display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;font-size:11px;color:#818898;margin-top:8px}
    .mp-student-stage-form .ta-file{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 12px;background:#F8F9FB;border:1px solid #DFE1E7;border-radius:8px;margin-top:12px;font-size:13px}
    .mp-student-stage-form .ta-file-remove{border:0;background:transparent;color:#DF1C41;cursor:pointer}
</style>
@endonce
@if(!($stage['pengumpulan']??true))
    <p class="mp-student-stage-section">Tahap ini tidak memerlukan pengumpulan.</p>
@else
    @foreach($displayEntries as $entry)
        <section class="mp-student-stage-section">
            <div class="mp-student-stage-heading"><h3>{{ $entry['jenis']==='perbaikan' ? 'Revisi Tugas' : (($entry['kategori']??'awal')==='revisi' ? 'Pengumpulan Revisi' : 'Pengumpulan Tugas') }}</h3><span class="mp-student-stage-time">{{ $entry['jenis']==='perbaikan' ? 'Diberikan pada: ' : (!empty($entry['dibatalkan_pada']) ? 'Dibatalkan pada: ' : 'Dikumpulkan pada: ') }}{{ \Illuminate\Support\Carbon::parse($entry['dibatalkan_pada']??$entry['waktu'])->format('d/m/Y, H:i') }}</span></div>
            @if($entry['jenis']==='perbaikan' && !empty($entry['catatan']))<p style="white-space:pre-wrap">{{ $entry['catatan'] }}</p>@endif
            <div style="display:grid;gap:8px">
                @foreach($entry['files']??[] as $file)<a class="mp-student-stage-file" target="_blank" rel="noopener noreferrer" href="{{ route($base.'download',$routeArgs+['path'=>$file['path']]) }}"><x-eoffice::manajemen-praktikum.ui.icon name="file" /><span>{{ $file['nama'] }}</span></a>@endforeach
                @if(!empty($entry['link']))<a class="mp-student-stage-file" target="_blank" rel="noopener noreferrer" href="{{ $entry['link'] }}">{{ $entry['link'] }}</a>@endif
            </div>
            @if(!empty($entry['deadline']))<p class="mp-student-stage-time">Tenggat perbaikan: {{ \Illuminate\Support\Carbon::parse($entry['deadline'])->format('d/m/Y, H:i') }}</p>@endif
        </section>
    @endforeach
    @if($canUnsubmit)@include('eoffice::manajemen-praktikum.tugas-akhir.unsubmit-confirmation')@endif
    @if($canSubmit)
        <section class="mp-student-stage-section">
            <div class="mp-student-stage-heading"><h3>{{ $entries->isNotEmpty() && !$cancelledSubmission ? 'Pengumpulan Revisi' : 'Pengumpulan Tugas' }}</h3><span class="mp-badge neutral sm">{{ $cancelledSubmission ? 'Dibatalkan' : 'Belum Dikumpulkan' }}</span></div>
            @if($cancelledSubmission)<p style="color:#666D80">Pengumpulan dibatalkan dan belum dianggap masuk. Perbaiki berkas atau tautan, lalu kumpulkan kembali.</p>@endif
            @if($initialLate)<p style="color:#666D80">Pengumpulan pertama dicatat terlambat dan dinilai sebagai nilai awal, bukan revisi.</p>@endif
            <form class="mp-student-stage-form" method="POST" enctype="multipart/form-data" action="{{ route($base.'submit',$routeArgs) }}">
                @csrf<input type="hidden" name="tahap_id" value="{{ $stage['id'] }}">
                @if($cancelledSubmission && count($previousEntry['files']??[]))<label style="display:flex;align-items:center;gap:8px;font-weight:400"><input type="hidden" name="reuse_files" value="0"><input type="checkbox" name="reuse_files" value="1" @checked(old('reuse_files',1))> Gunakan berkas pengumpulan sebelumnya</label>@endif
                <div><label>Berkas Lampiran</label><x-eoffice::manajemen-praktikum.ui.ta-upload name="files[]" /></div>
                <div><label>Tautan <span style="font-weight:400;color:#666D80">(opsional apabila mengunggah berkas)</span></label><input class="mp-input" style="width:100%;font-size:13px" type="url" name="link" value="{{ old('link',$previousEntry['link']??'') }}" maxlength="2000" placeholder="https://"></div>
                <div style="display:flex;justify-content:flex-end"><button type="submit" class="mp-btn primary md">{{ $entries->isNotEmpty() && !$cancelledSubmission ? 'Kumpulkan Revisi' : 'Kumpulkan' }}</button></div>
            </form>
        </section>
    @elseif(!$praktikum->is_active)
        <p class="mp-student-stage-section">Praktikum nonaktif. Tugas dan berkas pengumpulan hanya dapat dibaca.</p>
    @elseif(!$submission || $cancelledSubmission || $submission->status==='perlu_perbaikan')
        <p class="mp-student-stage-section">{{ !empty($stage['dibuka_pada']) && now()->lt($stage['dibuka_pada']) ? 'Pengumpulan dibuka pada '.\Illuminate\Support\Carbon::parse($stage['dibuka_pada'])->format('d/m/Y, H:i').'.' : 'Pengumpulan ditutup karena tenggat sudah lewat atau periode revisi tidak aktif.' }}</p>
    @endif
@endif
