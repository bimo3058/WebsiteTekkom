<?php

namespace Modules\ManajemenMahasiswa\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Modules\ManajemenMahasiswa\Models\Kegiatan;
use Modules\ManajemenMahasiswa\Models\RiwayatKegiatan;

/**
 * Centralised kegiatan-merge logic for CV generation.
 *
 * Combines three data sources (RiwayatKegiatan, Ketua Pelaksana, Panitia)
 * with deduplication, and returns a flat collection of arrays suitable
 * for CV display.
 *
 * Both the CvBuilderController and the directory controllers' generateCv
 * methods should consume this service to avoid logic duplication.
 */
class KegiatanCvService
{
    /**
     * Get merged kegiatan for a user (approved riwayat + completed ketua + completed panitia).
     *
     * Only kegiatan with status = 'selesai' are included (matching directory behaviour).
     *
     * @return Collection<int, array{nama: string, peran: string, tanggal: string|null, is_sync: bool}>
     */
    public function getMergedKegiatan(User $user): Collection
    {
        $student = $user->student;

        if (!$student) {
            return collect();
        }

        $studentId = $student->id;

        // 1. Riwayat manual (approved only)
        $riwayat = RiwayatKegiatan::with('kegiatan')
            ->where('student_id', $studentId)
            ->where('verification_status', 'approved')
            ->get();

        // 2. Kegiatan as Ketua Pelaksana — only STATUS_SELESAI
        $kegiatanAsKetua = Kegiatan::where('ketua_pelaksana_id', $studentId)
            ->where('status', Kegiatan::STATUS_SELESAI)
            ->get();

        // 3. Kegiatan as Panitia — only STATUS_SELESAI
        $kegiatanAsPanitia = Kegiatan::whereHas('panitia', fn($q) => $q->where('students.id', $studentId))
            ->where('status', Kegiatan::STATUS_SELESAI)
            ->with(['panitia' => fn($q) => $q->where('students.id', $studentId)])
            ->get();

        // 4. Dedup: track kegiatan_ids already covered
        $existingKegiatanIds = $riwayat->pluck('kegiatan_id')->filter()->toArray();

        $result = collect();

        // ── From approved riwayat ─────────────────────────────────────────
        foreach ($riwayat as $rw) {
            $result->push([
                'nama'    => $rw->nama_kegiatan,
                'peran'   => $rw->peran_label,
                'tanggal' => $rw->tanggal_display,
                'is_sync' => true,
            ]);
        }

        // ── From ketua pelaksana (not already in riwayat) ─────────────────
        foreach ($kegiatanAsKetua as $kg) {
            if (!in_array($kg->id, $existingKegiatanIds)) {
                $result->push([
                    'nama'    => $kg->judul,
                    'peran'   => 'Ketua Pelaksana',
                    'tanggal' => $kg->tanggal_mulai,
                    'is_sync' => true,
                ]);
                $existingKegiatanIds[] = $kg->id;
            }
        }

        // ── From panitia (not already in riwayat or ketua) ────────────────
        foreach ($kegiatanAsPanitia as $kg) {
            if (!in_array($kg->id, $existingKegiatanIds)) {
                $peran = 'Panitia';
                $panitiaCurrent = $kg->panitia->first();
                if ($panitiaCurrent && !empty($panitiaCurrent->pivot?->peran)) {
                    $peran = ucfirst($panitiaCurrent->pivot->peran);
                }
                $result->push([
                    'nama'    => $kg->judul,
                    'peran'   => $peran,
                    'tanggal' => $kg->tanggal_mulai,
                    'is_sync' => true,
                ]);
                $existingKegiatanIds[] = $kg->id;
            }
        }

        return $result;
    }
}
