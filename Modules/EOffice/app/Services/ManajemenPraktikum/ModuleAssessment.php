<?php

namespace Modules\EOffice\Services\ManajemenPraktikum;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\{Praktikum,Modul,Tugas};

class ModuleAssessment
{
    public function save(Praktikum $praktikum, array $data): void
    {
        DB::transaction(function () use ($praktikum,$data) {
            $current=Praktikum::whereKey($praktikum->id)->lockForUpdate()->firstOrFail();
            $this->check((int)$data['version'] === (int)$current->komponen_modul_version, 'Pengaturan telah berubah. Muat ulang halaman sebelum menyimpan.');
            $extras=[];
            $this->check(count($data['tambahan'] ?? [])<=2 && !array_diff(array_keys($data['tambahan'] ?? []),[1,2]), 'Maksimal dua komponen tambahan.');
            foreach ($data['tambahan'] ?? [] as $slot=>$component) {
                if (blank($component['nama'] ?? null)) {
                    $this->check(blank($component['bobot'] ?? null), 'Nama komponen wajib diisi.');
                    continue;
                }
                $this->check(isset($component['bobot']), 'Lengkapi bobot seluruh komponen dengan total 100%.');
                $this->check(filter_var($component['bobot'],FILTER_VALIDATE_INT)!==false && $component['bobot']>=1 && $component['bobot']<=100, 'Bobot komponen tambahan harus berupa bilangan bulat antara 1 dan 100%.');
                $extras[]=['id'=>'tambahan_'.$slot,'nama'=>trim($component['nama']),'bobot'=>(int)$component['bobot']];
            }
            // Fixed aliases may overlap; additional names must not duplicate any alias or each other.
            $extraNames=array_map(fn ($c)=>mb_strtolower($c['nama']),$extras);
            $fixed=array_map(fn ($name)=>mb_strtolower($name),array_merge(array_values(Tugas::JENIS),['Praktikum',$current->laporanLabel()]));
            $this->check(count(array_unique($extraNames))===count($extraNames) && !array_intersect($extraNames,$fixed), 'Nama komponen tidak boleh sama.');
            $oldStructure=collect($current->komponen_modul ?? [])->map(fn ($c)=>[$c['id'],$c['nama']])->all();
            $newStructure=collect($extras)->map(fn ($c)=>[$c['id'],$c['nama']])->all();
            $locked=$current->moduleComponentsLocked();
            $this->check(!$locked || $oldStructure===$newStructure, 'Susunan komponen dikunci karena tugas telah dibuka atau sudah memiliki pengumpulan maupun nilai.');
            $this->check(!$current->moduleWeightsLocked(), 'Batalkan pengajuan atau publikasi nilai sebelum mengubah bobot.');
            $current->fill(collect($data)->only(['bobot_tp','bobot_praktikum','bobot_laporan','bobot_responsi'])->all());
            $current->komponen_modul=$extras;
            $this->check($current->moduleWeightsValid(), 'Lengkapi bobot seluruh komponen dengan total 100%.');
            // Never discard a prepared task when removing or renaming its component.
            $removed=array_diff(array_column(json_decode($current->getRawOriginal('komponen_modul') ?? '[]',true) ?? [],'id'),array_column($extras,'id'));
            $moduleIds=Modul::where('praktikum_id',$current->id)->pluck('id');
            $this->check(!Tugas::whereIn('modul_id',$moduleIds)->whereIn('jenis_tugas',$removed)->exists(), 'Hapus rancangan tugas terkait sebelum menghapus komponen.');
            if ($locked && !$current->komponen_modul_locked_at) $current->komponen_modul_locked_at=now();
            $current->komponen_modul_version++;
            $current->save();
        });
    }

    private function check(bool $valid,string $message): void
    {
        if (!$valid) throw ValidationException::withMessages(['komponen_modul'=>$message]);
    }
}
