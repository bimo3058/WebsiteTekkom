@props(['praktikum','role','embedded'=>false])
@if(\Illuminate\Support\Facades\Schema::hasTable('eo_prak_final_results'))
@php
    $studentView=$role==='mahasiswa';
    $moduleColumns=\Modules\EOffice\Models\Modul::where('praktikum_id',$praktikum->id)->orderBy('urutan')->orderBy('id')->get();
    try {
        $report=app(\Modules\EOffice\Services\ManajemenPraktikum\FinalGradeBook::class)->report($praktikum,null,$studentView);
    } catch(\Illuminate\Validation\ValidationException $exception) {
        $report=['rows'=>[],'errors'=>collect($exception->errors())->flatten()->all(),'weights'=>[],'published'=>false,'policy'=>null];
    }
    $locked=app(\Modules\EOffice\Services\ManajemenPraktikum\FinalAssessment::class)->locked($praktikum);
    $schemaReady=\Illuminate\Support\Facades\Schema::hasTable('eo_prak_extra_scores');
    $editable=in_array($role,['koor','dosen']) && !$locked && $praktikum->is_active && $schemaReady;
    $columns=$report['components']??[];
    $warnings=array_values(array_unique(array_merge($report['errors'],collect($report['rows'])->flatMap(fn($r)=>array_map(fn($e)=>$r['name'].': '.$e,array_filter(array_diff($r['errors'],$report['errors']),fn($e)=>!str_ends_with($e,'belum dinilai.'))))->all())));
    $display=fn($v)=>$v===null?'—':str_replace('.',',',(string)$v);
    $letters=['A','AB','B','BC','C','D','E'];
    $config=['rows'=>array_values($report['rows']),'components'=>$columns,'modules'=>$report['weights']['modules']??$praktikum->bobot_gabungan_modul,'ta'=>$report['weights']['ta']??$praktikum->bobot_gabungan_ta,'version'=>$praktikum->bobot_gabungan_version,'role'=>$role,'editable'=>$editable,'embedded'=>(bool)$embedded,'expanded'=>!$embedded,'rules'=>$report['policy']?->rules??[],'history'=>in_array($role,['koor','dosen']) ? app(\Modules\EOffice\Services\ManajemenPraktikum\ScoreHistory::class)->formatted('final',(string)$praktikum->id) : []];
@endphp
<x-eoffice::manajemen-praktikum.ui.final-grades-script />
<section style="{{ $embedded?'border-top:1px solid #DFE1E7':'margin-top:16px;border:1px solid #DFE1E7;border-radius:12px' }};background:#fff;overflow:hidden"
    x-data="finalGradeManager(@js($config))" x-effect="const detail={changed:dirty,payload:payload};queueMicrotask(()=>$dispatch('final-assessment-change',detail))" @mouseup.window="finishSelection()">
    <div class="mp-grade-card-header" style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;padding:16px 20px">
        <button type="button" @click="expanded=!expanded" :aria-expanded="expanded" style="flex:1;min-width:220px;border:0;background:transparent;text-align:left;font-weight:400;padding:0;cursor:pointer">
            <span style="display:block;font-size:15px;font-weight:700;color:#0D0D12">Nilai Keseluruhan</span>
            <span style="display:block;margin-top:6px;font-size:13px;color:#666D80;line-height:1.5">Rekapitulasi nilai mahasiswa pada {{ $praktikum->nama }} {{ $praktikum->getRawOriginal('tahun_ajaran') }}.</span>
            <span style="display:flex;gap:12px;flex-wrap:wrap;margin-top:6px;font-size:12px;color:#666D80;font-weight:400">
                @foreach($letters as $letter)<span>{{ $letter }}: <span x-text="rows.filter(r=>letter(r)===@js($letter)).length">{{ collect($report['rows'])->where('letter',$letter)->count() }}</span></span>@endforeach
                <span x-show="rows.some(r=>!letter(r))">Belum ditentukan: <span x-text="rows.filter(r=>!letter(r)).length"></span></span>
            </span>
        </button>
        @if($role==='koor' && $schemaReady)<button x-show="expanded" x-cloak type="button" class="mp-btn primary sm" @click="addColumn()" :disabled="!editable || components.length>=3">Tambah Kolom</button>@endif
        @unless($studentView)<div x-show="expanded" x-cloak style="width:180px"><x-eoffice::manajemen-praktikum.ui.ta-select x-model="predicate" aria-label="Filter predikat nilai keseluruhan" :options="array_merge([''=>'Semua Predikat'],array_combine($letters,$letters),['pending'=>'Belum ditentukan'])" /></div>@endunless
        <button type="button" @click="expanded=!expanded" :aria-expanded="expanded" aria-label="Buka atau tutup Nilai Keseluruhan" style="border:0;background:transparent;padding:0;cursor:pointer;display:flex;align-items:center"><svg :style="expanded?'transform:rotate(180deg)':''" style="transition:transform .2s" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#666D80" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg></button>
    </div>
    <x-eoffice::manajemen-praktikum.ui.grade-accordion :separator="true">
        @foreach($warnings as $warning)<p role="status" style="margin:12px 20px;font-size:12px;color:#854D0E">{{ $warning }}</p>@endforeach
     <div style="overflow:auto"><table class="mp-table" style="width:100%;min-width:1100px;border-collapse:collapse;font-size:13px">
            <thead style="background:#F8F9FB;color:#666D80;font-size:12px"><tr>
                @foreach(['NO','NAMA MAHASISWA','NIM','KELOMPOK','SHIFT'] as $heading)<th class="mp-th" style="padding:12px 16px;text-align:{{ in_array($heading,['NAMA MAHASISWA','NIM'])?'left':'center' }}">{{ $heading }}</th>@endforeach
                @foreach($moduleColumns as $moduleColumn)<th class="mp-th" style="padding:12px 16px;text-align:center;white-space:nowrap">MODUL {{ $loop->iteration }}</th>@endforeach
                @foreach(['modules'=>['RATA-RATA MODUL','#EEF2FF','#4338CA'],'ta'=>['TUGAS AKHIR','#F0FDF4','#15803D']] as $key=>[$label,$bg,$fg])
                    <th class="mp-th" style="padding:12px 16px;text-align:center;background:{{ $bg }};color:{{ $fg }};white-space:nowrap"><div>{{ $label }}</div>
                        @if($role==='koor')<label style="display:inline-flex;align-items:center;gap:4px;margin-top:4px"><input aria-label="Bobot {{ $label }}" type="number" min="0" max="100" step="1" x-model.number="{{ $key }}" :disabled="!editable" style="width:44px;height:28px;border:1px solid #DFE1E7;border-radius:8px;text-align:center;font-size:12px;background:transparent"> %</label>
                        @else<div style="margin-top:6px">{{ $report['weights'][$key]??'—' }}%</div>@endif
                    </th>
                @endforeach
                <template x-for="(column, columnIndex) in components" :key="column.id"><th class="mp-th" style="padding:12px 16px;text-align:center;min-width:160px" :style="{background:['#FEFCE8','#FFF7ED','#EEF2FF'][columnIndex],color:['#A16207','#EA580C','#4338CA'][columnIndex]}">
                    @if($role==='koor')
                    <div style="display:flex;align-items:center;gap:6px"><input class="mp-input" style="width:140px;font-size:12px;font-weight:600" aria-label="Nama kolom penilaian tambahan" x-model="column.nama" :disabled="!editable" maxlength="100" placeholder="Contoh: Presentasi"><button type="button" @click="removeColumn(column.id)" :disabled="!editable" aria-label="Hapus kolom penilaian" style="border:0;background:none;color:#DF1C41;cursor:pointer"><x-eoffice::manajemen-praktikum.ui.icon name="trash" /></button></div>
                    <label style="display:inline-flex;align-items:center;gap:4px;margin-top:4px"><input type="number" aria-label="Bobot kolom tambahan" x-model.number="column.bobot" :disabled="!editable" min="1" max="100" step="1" style="width:44px;height:28px;border:1px solid #DFE1E7;border-radius:8px;text-align:center;font-size:12px;background:transparent"> %</label>
                    @else<div x-text="column.nama.toUpperCase()"></div><div style="margin-top:6px" x-text="column.bobot+'%'"></div>@endif
                </th></template>
                <th class="mp-th" style="text-align:center">TOTAL</th><th class="mp-th" style="text-align:center">PREDIKAT</th>
            </tr></thead>
            <tbody>
            @foreach($report['rows'] as $row)
                <tr class="mp-grade-row group" x-data="{get row(){return rows.find(r=>r.id===@js((string)$row['id']))}}" x-show="pageRows.some(r=>r.id===row.id)" style="border-top:1px solid #EFF1F5">
                    <td style="padding:12px 16px;color:#666D80;text-align:center;font-weight:700" x-text="visibleRows.findIndex(r=>r.id===row.id)+1"></td>
                    <td style="padding:12px 16px"><div style="display:flex;align-items:center;gap:10px"><div class="mp-av sky" style="width:28px;height:28px;font-size:11px;flex-shrink:0">{{ strtoupper(substr($row['name'],0,2)) }}</div><div><div style="font-size:13px;font-weight:600;color:#0D0D12">{{ $row['name'] }}</div>@if(!empty($row['email']))<div style="font-size:11.5px;color:#666D80;margin-top:2px">{{ $row['email'] }}</div>@endif</div></div></td>
                    <td style="padding:12px 16px;font-size:12px;font-weight:600;white-space:nowrap">{{ $row['nim'] }}</td>
                    @foreach(['kel'=>'group','shf'=>'shift'] as $type=>$field)<td x-show="span(row.id,@js($type))>0" :rowspan="span(row.id,@js($type))" style="padding:12px 16px;text-align:center;vertical-align:middle;font-size:15px;font-weight:700">{{ $row[$field]??'—' }}</td>@endforeach
                    @foreach($moduleColumns as $moduleColumn)<td style="padding:12px 16px;text-align:center;font-size:13px">{{ $display($row['module_values'][(string)$moduleColumn->getRawOriginal('id')]??null) }}</td>@endforeach
                    @foreach(['module'=>['#EEF2FF','#E0E7FF'],'ta'=>['#F0FDF4','#DCFCE7']] as $field=>[$bg,$hover])<td class="mp-grade-tint" style="--mp-grade-hover:{{ $hover }};padding:12px 16px;text-align:center;background:{{ $bg }}">{{ $display($row[$field]) }}</td>@endforeach
                    <template x-for="(column, columnIndex) in components" :key="column.id"><td class="mp-grade-history-cell mp-final-score-cell mp-grade-tint" :class="{'is-selected':isSelected(row,column.id)}" @mousedown="if(!$event.target.closest('input,button')){$event.preventDefault();beginSelection(row,column.id)}" @mouseenter="extendSelection(row,column.id)" style="position:relative;padding:12px 32px 12px 16px;text-align:center" :style="{background:['#FEFCE8','#FFF7ED','#EEF2FF'][columnIndex],'--mp-grade-hover':['#FEF9C3','#FFEDD5','#E0E7FF'][columnIndex]}">
                        @if(in_array($role,['koor','dosen']))
                        <x-eoffice::manajemen-praktikum.ui.grade-score-input model="row.extras[column.id]" label="Nilai kolom tambahan" x-bind:disabled="!editable" />
                        <x-eoffice::manajemen-praktikum.ui.grade-history client-entries="history[row.id]?.[column.id]||[]" client-score-label="column.nama" :label="'Riwayat Nilai Keseluruhan '.$row['name']" />
                        @else<span x-text="display(row.extras[column.id])"></span>@endif
                    </td></template>
                    <td style="padding:12px 16px;text-align:center;font-weight:600" x-text="display(total(row))"></td>
                    <td class="mp-grade-predicate" :style="predicateStyle(row)" style="padding:12px 16px;text-align:center;font-weight:700" x-text="letter(row)||'—'"></td>
                </tr>
            @endforeach
            <tr x-show="visibleRows.length===0" x-cloak><td :colspan="{{ 9+$moduleColumns->count() }}+components.length" style="padding:24px 16px;text-align:center;font-size:13px;color:#666D80">Data tidak ditemukan.</td></tr>
            </tbody>
        </table></div>
        <div style="padding:16px 20px;font-size:12px;color:#666D80;display:flex;gap:12px;justify-content:space-between;flex-wrap:wrap"><span :style="{color:weightTotal===100?'var(--c-success-100,#008B32)':'var(--c-error-100,#DF1C41)'}" style="font-weight:600" x-text="'Total bobot: '+weightTotal+'%'"></span><span x-show="dirty" x-cloak>Pratinjau perubahan. Klik Simpan untuk menyimpan nilai.</span></div>
        <x-eoffice::manajemen-praktikum.ui.client-pagination />
        @if(in_array($role,['koor','dosen']) && $schemaReady)@include('eoffice::manajemen-praktikum.partials.final-bulk-grades')@endif
    </x-eoffice::manajemen-praktikum.ui.grade-accordion>
</section>
@endif
