@props(['praktikum'])
@php
    $locked=$praktikum->moduleComponentsLocked();
    $weightsLocked=$praktikum->moduleWeightsLocked();
    $formLocked=$locked || $weightsLocked;
    $extras=collect($praktikum->komponen_modul ?? [])->keyBy('id');
@endphp
@once
<style>
.mp-module-columns{overflow:hidden}
.mp-module-columns .mp-column-name{width:100%;min-width:0;min-height:36px;height:36px;font-size:13px;font-weight:600}
.mp-module-columns .mp-column-name[readonly]{padding:0;border:0;background:transparent;color:var(--c-fg);box-shadow:none}
.mp-module-columns .mp-column-label{display:block;color:var(--c-fg);font-size:13px;font-weight:600;line-height:20px;white-space:normal}
.mp-module-columns .mp-column-weight{width:48px;min-width:48px;min-height:32px;height:32px;padding:4px;font-size:13px;text-align:center;border-color:var(--c-border);border-radius:8px}
.mp-module-columns fieldset:disabled .mp-column-weight{background:var(--c-bg);color:var(--c-fg-muted);cursor:not-allowed}
.mp-module-columns .mp-column-weight-group{display:flex;align-items:center;justify-content:center;gap:8px}
.mp-module-columns .mp-column-weight-group>span{color:var(--c-fg-muted);font-size:12px;line-height:20px}
.mp-module-columns .mp-column-weight-heading{display:inline-block;width:48px;flex-shrink:0;text-align:center}
.mp-module-columns .mp-column-weight-unit-placeholder{visibility:hidden}
.mp-module-columns .mp-table th{padding:12px 16px!important;white-space:nowrap}
.mp-module-columns .mp-table td{padding:14px 16px!important;font-size:13px;border-bottom:0;vertical-align:middle}
.mp-module-columns .mp-table .mp-column-no{text-align:center;font-size:12px;font-weight:700;color:var(--c-fg-muted)}
.mp-module-columns .mp-table .mp-tr{border-bottom:0}
.mp-module-columns .mp-table tbody tr+tr td{border-top:1px solid var(--c-border)}
.mp-module-columns .mp-column-lock-notices{padding:12px 20px;display:flex;flex-direction:column;gap:8px}
.mp-module-columns .mp-column-lock-note{margin:0;color:var(--c-fg-muted);font-size:12px;line-height:20px}
.mp-module-columns .mp-column-footer{padding:16px 20px;display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:center;gap:16px;border-top:1px solid var(--c-border)}
.mp-module-columns .mp-column-footer-copy{display:flex;flex-direction:column;gap:7px;min-width:0}
.mp-module-columns .mp-column-help{margin:0;color:var(--c-fg-muted);font-size:12px;line-height:20px}
.mp-module-columns .mp-column-footer-summary{display:flex;align-items:center;gap:12px;flex-wrap:wrap;min-width:0}
.mp-module-columns .mp-weight-summary,.mp-module-columns .mp-weight-summary strong{font-size:12px;line-height:20px;font-weight:600}
.mp-module-columns .mp-weight-summary{white-space:nowrap}
.mp-module-columns .mp-column-save{margin-left:auto;flex-shrink:0}
@media(max-width:640px){.mp-module-columns .mp-column-footer{gap:12px}.mp-module-columns .mp-column-footer-summary{gap:8px}}
</style>
@endonce
<div class="mp-card mp-module-columns">
    <div style="padding:16px 20px;border-bottom:1px solid var(--c-border)"><h3 class="mp-card-title">Kolom Penilaian Modul</h3><p class="mp-column-description" style="margin:6px 0 0;font-size:12px;color:var(--c-fg-muted)">{{ $locked ? 'Susunan kolom dikunci setelah tugas dibuka, dikumpulkan, atau dinilai.' : 'Berlaku untuk seluruh modul, kecuali tugas akhir. Maksimal dua kolom tambahan.' }}</p></div>
    <form method="POST" action="{{ route('eoffice.manprak.koor.komponen-modul.update') }}" x-data="{showWeightConfirm:false,weightConfirmed:false,visible:{{ json_encode([1=>filled(old('tambahan.1.nama',$extras->get('tambahan_1')['nama'] ?? null)),2=>filled(old('tambahan.2.nama',$extras->get('tambahan_2')['nama'] ?? null))]) }},total:100,refreshTotal(){this.total=[...$refs.moduleWeightForm.querySelectorAll('input[type=number]')].reduce((sum,input)=>sum+Number(input.value||0),0)}}" x-init="refreshTotal()" @input="refreshTotal()" x-ref="moduleWeightForm" @if($locked) @submit="if(!weightConfirmed){$event.preventDefault();showWeightConfirm=true}" @endif>
        @csrf
        @include('eoffice::manajemen-praktikum.partials.module-weight-confirmation')
        <input type="hidden" name="version" value="{{ old('version',$praktikum->komponen_modul_version ?? 0) }}">
        @if($weightsLocked)
        <div class="mp-column-lock-notices" role="status">
            @if($weightsLocked)<p class="mp-column-lock-note">Bobot dikunci. Batalkan pengajuan atau publikasi nilai pada halaman Absensi &amp; Nilai sebelum melakukan perubahan.</p>@endif
        </div>
        @endif
        <fieldset @disabled($formLocked) style="border:0;padding:0;margin:0;min-width:0">
        <div style="overflow-x:auto">
        <table class="mp-table" style="width:100%;min-width:560px">
            <thead><tr><th class="mp-th" style="width:80px;text-align:center">NO</th><th class="mp-th">NAMA KOLOM PENILAIAN</th><th class="mp-th" style="width:120px;text-align:center"><span class="mp-column-weight-group"><span class="mp-column-weight-heading">BOBOT</span><span class="mp-column-weight-unit-placeholder" aria-hidden="true">%</span></span></th><th class="mp-th" style="width:100px;text-align:center">AKSI</th></tr></thead>
            <tbody>
            @foreach(['bobot_tp'=>'Tugas Pendahuluan','bobot_praktikum'=>'Praktikum (Tugas Pengganti)','bobot_laporan'=>$praktikum->laporanLabel(),'bobot_responsi'=>'Responsi'] as $key=>$name)
                <tr class="mp-tr">
                    <td class="mp-column-no">{{ $loop->iteration }}</td>
                    <td><span class="mp-column-label" title="Kolom tetap tidak dapat diubah.">{{ $name }}</span></td>
                    <td><div class="mp-column-weight-group"><input class="mp-input mp-column-weight" type="number" name="{{ $key }}" value="{{ old($key,$praktikum->$key ?? ($key==='bobot_tp' ? 10 : 30)) }}" min="0" max="100" step="1" required aria-label="Bobot {{ $name }}"><span>%</span></div></td>
                    <td style="text-align:center;color:#A4ABB8"><span title="Kolom tetap tidak dapat dihapus.">—</span></td>
                </tr>
            @endforeach
            @foreach([1,2] as $slot)
                <tr class="mp-tr" x-show="visible[{{ $slot }}]" x-cloak>
                    <td class="mp-column-no" x-text="4 + Object.keys(visible).filter(key=>Number(key)<={{ $slot }} &amp;&amp; visible[key]).length"></td>
                    <td><input class="mp-input mp-column-name" name="tambahan[{{ $slot }}][nama]" value="{{ old('tambahan.'.$slot.'.nama',$extras->get('tambahan_'.$slot)['nama'] ?? '') }}" maxlength="60" placeholder="Contoh: Form Data" @readonly($locked) :required="visible[{{ $slot }}]" aria-label="Nama kolom tambahan {{ $slot }}" x-ref="extraName{{ $slot }}"></td>
                    <td><div class="mp-column-weight-group"><input class="mp-input mp-column-weight" type="number" name="tambahan[{{ $slot }}][bobot]" value="{{ old('tambahan.'.$slot.'.bobot',$extras->get('tambahan_'.$slot)['bobot'] ?? '') }}" min="1" max="100" step="1" :required="visible[{{ $slot }}]" aria-label="Bobot kolom tambahan {{ $slot }}"><span>%</span></div></td>
                    <td style="text-align:center"><button type="button" class="mp-btn error sm" @disabled($locked) title="{{ $locked ? 'Susunan kolom penilaian dikunci.' : 'Hapus kolom penilaian' }}" @click="visible[{{ $slot }}]=false;$el.closest('tr').querySelectorAll('input').forEach(input=>input.value='');refreshTotal()">Hapus</button></td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        </fieldset>
        <div class="mp-column-footer">
            <div class="mp-column-footer-copy">
            <p class="mp-column-help">Empat kolom tetap tidak dapat diubah atau dihapus. Total bobot harus 100%. Kolom tambahan dapat diatur sebelum tugas pertama dibuka.</p>
                <div class="mp-column-footer-summary">@unless($locked)<button type="button" class="mp-btn primary md" @disabled($weightsLocked) x-show="Object.values(visible).filter(Boolean).length<2" @click="const slot=!visible[1] ? 1 : 2;visible[slot]=true;$nextTick(()=>$refs['extraName'+slot].focus())">Tambah Kolom</button>@endunless <span class="mp-weight-summary" :style="total===100 ? 'color:var(--c-success-200)' : 'color:var(--c-error-100)'">Total bobot: <strong x-text="total"></strong>%</span></div>
            </div>
            <button class="mp-btn primary md mp-column-save" type="submit" @disabled($formLocked)>Simpan</button>
        </div>
    </form>
</div>
