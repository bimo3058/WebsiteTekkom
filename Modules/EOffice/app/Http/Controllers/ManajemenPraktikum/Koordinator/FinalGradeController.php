<?php

namespace Modules\EOffice\Http\Controllers\ManajemenPraktikum\Koordinator;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\Praktikum;
use Modules\EOffice\Models\TaNilai;
use Modules\EOffice\Models\TugasAkhir;

class FinalGradeController extends Controller
{
    public function weights(Request $request)
    {
        $praktikum = DashboardController::resolvePraktikum() ?? abort(404);
        $data = $request->validate(['version' => 'required|integer|min:0', 'modules' => 'required|integer|min:0|max:100', 'ta' => 'required|integer|min:0|max:100']);
        $this->saveWeights($praktikum, $data);

        return back()->with('success', 'Bobot nilai keseluruhan berhasil disimpan.');
    }

    public function saveWeights(Praktikum $praktikum, array $data): void
    {
        if ((int) $data['modules'] + (int) $data['ta'] + array_sum(array_column($praktikum->final_components??[],'bobot')) !== 100) {
            throw ValidationException::withMessages(['bobot_gabungan' => 'Total bobot gabungan harus 100%.']);
        }
        DB::transaction(function () use ($praktikum, $data) {
            $current = Praktikum::whereKey($praktikum->id)->lockForUpdate()->firstOrFail();
            if (! $current->is_active) {
                throw ValidationException::withMessages(['bobot_gabungan' => 'Praktikum telah diarsipkan.']);
            }
            if ((int) $current->bobot_gabungan_version !== (int) $data['version']) {
                throw ValidationException::withMessages(['bobot_gabungan' => 'Pengaturan telah berubah. Muat ulang halaman.']);
            }
            $ta = TugasAkhir::where('praktikum_id', $current->id)->first();
            if ($current->moduleWeightsLocked() || ($ta && TaNilai::where('tugas_akhir_id', $ta->id)->where(fn ($q) => $q->where('disetujui_koor', true)->orWhere('disetujui_dosen', true))->exists())) {
                throw ValidationException::withMessages(['bobot_gabungan' => 'Batalkan persetujuan atau publikasi nilai sebelum mengubah bobot gabungan.']);
            }
            $current->update(['bobot_gabungan_modul' => (int) $data['modules'], 'bobot_gabungan_ta' => (int) $data['ta'], 'bobot_gabungan_version' => $current->bobot_gabungan_version + 1]);
            DB::table('eo_prak_grade_batches')->where('praktikum_id', $current->id)->where('status', 'pending')->update(['status' => 'withdrawn']);
        });

    }
}
