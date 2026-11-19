@props(['praktikum','moduls','role'])
@php
    $csvTa=\Modules\EOffice\Models\TugasAkhir::where('praktikum_id',$praktikum->id)->where('published',true)->first();
    $csvUrl=match($role){'koor'=>route('eoffice.manprak.koor.nilai.export-csv'),'asprak'=>route('eoffice.manprak.asprak.absensi.export-csv'),'mahasiswa'=>route('eoffice.manprak.mahasiswa.nilai.export-csv'),default=>route('eoffice.manprak.dosen.nilai.export-csv',$praktikum->id)};
    $csvChoices=$moduls->map(fn($m)=>(string)$m->getRawOriginal('id'))->values()->all();
    if($csvTa) $csvChoices[]='tugas_akhir';
    $csvFinal=\Illuminate\Support\Facades\Schema::hasTable('eo_prak_final_results');
    if($csvFinal) $csvChoices[]='nilai_akhir';
@endphp
<div x-data="{{ json_encode(['open'=>false,'selected'=>$csvChoices,'choices'=>$csvChoices]) }}">
    <button type="button" class="mp-btn neutral md" @click="open=true" style="height:36px;font-size:13px;padding:0 14px;border:1px solid #DFE1E7!important;border-radius:8px;background:white;box-shadow:0 1px 2px rgba(0,0,0,.05)"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><path d="M12 15V3"/></svg>Ekspor CSV</button>
    <template x-teleport="body"><div x-show="open" x-cloak x-transition.opacity.duration.200ms @keydown.escape.window="open=false" @click.self="open=false" style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999">
        <form method="GET" action="{{ $csvUrl }}" role="dialog" aria-modal="true" aria-label="Ekspor CSV" style="position:fixed;left:50%;top:50%;transform:translate(-50%,-50%);width:480px;max-width:calc(100% - 40px);border-radius:14px;background:white;overflow:hidden;margin:0">
            @if($role==='mahasiswa')<input type="hidden" name="praktikum_id" value="{{ $praktikum->id }}">@endif
            <div style="padding:20px 22px;border-bottom:1px solid #DFE1E7;font-size:16px;font-weight:700">Ekspor CSV</div>
            <div class="mp-csv-options" style="padding:12px 22px;max-height:55vh;overflow:auto"><p style="font-size:13px;color:#666D80;margin:0;line-height:1.5">Pilih rekap nilai yang ingin diekspor.</p>
                <label style="display:flex;align-items:center;gap:10px;padding:6px 22px 12px;font-size:13px;line-height:20px;margin:0 -22px 6px;border-bottom:1px solid #DFE1E7;cursor:pointer;font-weight:600"><input type="checkbox" checked :checked="choices.length>0 && selected.length===choices.length" x-effect="$el.indeterminate=selected.length>0 && selected.length<choices.length" @change="selected=$el.checked ? [...choices] : []" style="width:18px;height:18px;margin:0;accent-color:#0B266E;flex-shrink:0" aria-label="Pilih Semua"><span x-text="selected.length + ' dari ' + choices.length + ' dipilih'"></span></label>
                @foreach($moduls as $csvModul)<label style="display:flex;align-items:center;gap:10px;padding:6px 0;font-size:13px;line-height:20px;margin:0;cursor:pointer"><input type="checkbox" name="cards[]" value="{{ $csvModul->getRawOriginal('id') }}" x-model="selected" checked style="width:18px;height:18px;margin:0;accent-color:#0B266E;flex-shrink:0">{{ $csvModul->nama }}</label>@endforeach
                @if($csvTa)<label style="display:flex;align-items:center;gap:10px;padding:6px 0;font-size:13px;line-height:20px;margin:0;cursor:pointer"><input type="checkbox" name="cards[]" value="tugas_akhir" x-model="selected" checked style="width:18px;height:18px;margin:0;accent-color:#0B266E;flex-shrink:0">Tugas Akhir</label>@endif
                @if($csvFinal)<label style="display:flex;align-items:center;gap:10px;padding:6px 0;font-size:13px;line-height:20px;margin:0;cursor:pointer"><input type="checkbox" name="cards[]" value="nilai_akhir" x-model="selected" checked style="width:18px;height:18px;margin:0;accent-color:#0B266E;flex-shrink:0">Nilai Keseluruhan</label>@endif
                <p x-show="selected.length===0" style="font-size:12px;color:#DF1C41;margin:6px 0 0">Pilih minimal satu.</p>
            </div>
            <div style="padding:16px 22px;background:#FAFBFC;border-top:1px solid #DFE1E7;display:flex;justify-content:flex-end;gap:10px"><button type="button" class="mp-btn secondary md" @click="open=false">Batal</button><button type="submit" class="mp-btn primary md" :disabled="selected.length===0">Ekspor</button></div>
        </form>
    </div></template>
</div>
@once
<style>
.mp-csv-options input[type=checkbox]{appearance:none;border:2px solid #DFE1E7;border-radius:4px;background:white;cursor:pointer}
.mp-csv-options input[type=checkbox]:checked{border-color:#0B266E;background:#0B266E url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='20 6 9 17 4 12'/%3E%3C/svg%3E") center/14px no-repeat}
.mp-csv-options input[type=checkbox]:indeterminate{border-color:#0B266E;background:#0B266E url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' stroke='white' stroke-width='3'%3E%3Cpath d='M5 12h14'/%3E%3C/svg%3E") center/14px no-repeat}
.mp-csv-options input[type=checkbox]:focus-visible{outline:2px solid #0B266E;outline-offset:3px}
</style>
@endonce
