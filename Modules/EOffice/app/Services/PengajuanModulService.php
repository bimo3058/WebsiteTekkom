<?php

namespace Modules\EOffice\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\EOffice\Models\Modul;
use Modules\EOffice\Models\PengajuanModul;
use Modules\EOffice\Models\Praktikum;

class PengajuanModulService
{
    public function buatDraft(Praktikum $praktikum, int $userId): PengajuanModul
    {
        $this->pastikanBelumBerjalan($praktikum);

        $aktif = $praktikum->pengajuanModul()
            ->whereIn('status', ['draft', 'diajukan', 'perlu_revisi'])
            ->latest('versi')
            ->first();

        if ($aktif) {
            return $aktif->load('items');
        }

        return DB::transaction(function () use ($praktikum, $userId) {
            $sumber = $praktikum->modul()->exists()
                ? $praktikum
                : $this->praktikumSebelumnya($praktikum);

            $pengajuan = PengajuanModul::create([
                'praktikum_id' => $praktikum->id,
                'praktikum_sumber_id' => $sumber?->id,
                'versi' => ((int) $praktikum->pengajuanModul()->max('versi')) + 1,
                'status' => 'draft',
                'diajukan_oleh' => $userId,
            ]);

            $modulSumber = $sumber
                ? $sumber->modul()->orderBy('urutan')->get()
                : collect();

            foreach ($modulSumber as $modul) {
                $pengajuan->items()->create([
                    'modul_sumber_id' => $modul->id,
                    'aksi' => 'tetap',
                    'nama_lama' => $modul->nama,
                    'urutan_lama' => $modul->urutan,
                    'nama_baru' => $modul->nama,
                    'urutan_baru' => $modul->urutan,
                ]);
            }

            return $pengajuan->load('items');
        });
    }

    public function susunUrutan(PengajuanModul $pengajuan, ?\Modules\EOffice\Models\PengajuanModulItem $dipindah = null, ?int $posisi = null): void
    {
        DB::transaction(function () use ($pengajuan, $dipindah, $posisi) {
            $locked = PengajuanModul::whereKey($pengajuan->id)->lockForUpdate()->firstOrFail();
            $this->pastikanBelumBerjalan($locked->praktikum);
            if (!$locked->dapatDiedit()) {
                throw ValidationException::withMessages(['pengajuan' => 'Pengajuan tidak dapat diubah.']);
            }
            $items = $locked->items()->where('aksi', '!=', 'hapus')->orderBy('id')->lockForUpdate()->get();
            if ($dipindah) {
                $target = $items->firstWhere('id', $dipindah->id);
                if (!$target) throw ValidationException::withMessages(['urutan' => 'Modul tidak dapat dipindahkan.']);
                if ($posisi < 1 || $posisi > $items->count()) {
                    throw ValidationException::withMessages(['urutan' => 'Urutan harus antara 1 dan ' . $items->count() . '.']);
                }
                $items = $items->reject(fn ($item) => $item->id === $target->id)->values();
                $items->splice($posisi - 1, 0, [$target]);
            }
            foreach ($items->values() as $index => $item) {
                $urutan = $index + 1;
                $aksi = $item->modul_sumber_id
                    ? (($item->nama_lama === $item->nama_baru && (int) $item->urutan_lama === $urutan) ? 'tetap' : 'ubah')
                    : 'tambah';
                $item->update(['urutan_baru' => $urutan, 'aksi' => $aksi]);
            }
        });
    }

    public function ajukan(PengajuanModul $pengajuan, ?string $catatan): void
    {
        $this->pastikanBelumBerjalan($pengajuan->praktikum);

        if (! $pengajuan->dapatDiedit()) {
            throw ValidationException::withMessages(['pengajuan' => 'Pengajuan ini tidak dapat dikirim kembali.']);
        }

        $items = $pengajuan->items()->where('aksi', '!=', 'hapus')->get();
        if ($items->isEmpty()) {
            throw ValidationException::withMessages(['pengajuan' => 'Minimal satu modul harus dipertahankan atau ditambahkan.']);
        }

        $urutan = $items->pluck('urutan_baru')->map(fn ($value) => (int) $value);
        $urutanTerurut = $urutan->sort()->values()->all();
        if ($urutanTerurut !== range(1, $items->count())) {
            throw ValidationException::withMessages(['pengajuan' => 'Urutan modul harus unik dan berurutan mulai dari angka 1.']);
        }

        $pengajuan->update([
            'status' => 'diajukan',
            'catatan_pengajuan' => $catatan,
            'catatan_reviewer' => null,
            'diajukan_at' => now(),
            'direview_oleh' => null,
            'direview_at' => null,
        ]);
    }

    public function hambatanPenghapusan(PengajuanModul $pengajuan): array
    {
        if ($pengajuan->status !== 'diajukan' || $pengajuan->praktikum_sumber_id !== $pengajuan->praktikum_id) return [];

        $pesan = [];
        foreach ($pengajuan->items()->where('aksi', 'hapus')->get() as $item) {
            $modul = Modul::where('praktikum_id', $pengajuan->praktikum_id)->find($item->modul_sumber_id);
            if (!$modul) continue;
            if ($modul->modulAsprak()->exists()) {
                $pesan[] = "Modul \"{$modul->nama}\" masih memiliki asisten. Hapus atau pindahkan penugasan melalui Pembagian Modul ke Asisten, lalu minta dosen menyetujui kembali.";
            } elseif ($modul->materi()->exists() || $modul->tugas()->exists() || $modul->absensi()->exists()) {
                $pesan[] = "Modul \"{$modul->nama}\" memiliki materi, tugas, atau absensi dan tidak dapat dihapus. Tarik pengajuan, pertahankan modul tersebut, lalu ajukan kembali.";
            }
        }
        return $pesan;
    }

    public function setujui(PengajuanModul $pengajuan, int $dosenId, ?string $catatan): void
    {
        DB::transaction(function () use ($pengajuan, $dosenId, $catatan) {
            $pengajuan = PengajuanModul::query()->lockForUpdate()->findOrFail($pengajuan->id);
            $praktikum = Praktikum::query()->lockForUpdate()->findOrFail($pengajuan->praktikum_id);

            $this->pastikanBelumBerjalan($praktikum);
            if ($pengajuan->status !== 'diajukan') {
                throw ValidationException::withMessages(['pengajuan' => 'Pengajuan sudah diputuskan oleh dosen lain.']);
            }

            $items = $pengajuan->items()->orderBy('urutan_baru')->get();
            foreach ($items as $item) {
                if ($item->aksi === 'hapus') {
                    if ($pengajuan->praktikum_sumber_id !== $praktikum->id || ! $item->modul_sumber_id) {
                        continue;
                    }

                    $modul = Modul::where('praktikum_id', $praktikum->id)->find($item->modul_sumber_id);
                    if (! $modul) {
                        continue;
                    }

                    if ($modul->modulAsprak()->exists()) {
                        throw ValidationException::withMessages([
                            'pengajuan' => "Pengajuan belum dapat disetujui. Modul \"{$modul->nama}\" masih memiliki asisten. Koordinator perlu menghapus atau memindahkan penugasan asisten melalui Pembagian Modul ke Asisten, lalu dosen dapat menyetujui kembali. Seluruh perubahan belum diterapkan.",
                        ]);
                    }

                    if ($modul->materi()->exists() || $modul->tugas()->exists() || $modul->absensi()->exists()) {
                        throw ValidationException::withMessages([
                            'pengajuan' => "Pengajuan belum dapat disetujui. Modul \"{$modul->nama}\" memiliki materi, tugas, atau absensi sehingga tidak dapat dihapus. Koordinator perlu merevisi pengajuan dengan mempertahankan modul tersebut. Seluruh perubahan belum diterapkan.",
                        ]);
                    }

                    $modul->delete();
                    continue;
                }

                if ($pengajuan->praktikum_sumber_id === $praktikum->id && $item->modul_sumber_id) {
                    Modul::where('praktikum_id', $praktikum->id)
                        ->whereKey($item->modul_sumber_id)
                        ->update([
                            'nama' => $item->nama_baru,
                            'urutan' => $item->urutan_baru,
                            'pengajuan_modul_id' => $pengajuan->id,
                        ]);
                    continue;
                }

                Modul::create([
                    'praktikum_id' => $praktikum->id,
                    'modul_sumber_id' => $item->modul_sumber_id,
                    'pengajuan_modul_id' => $pengajuan->id,
                    'nama' => $item->nama_baru,
                    'urutan' => $item->urutan_baru,
                ]);
            }

            $pengajuan->update([
                'status' => 'disetujui',
                'direview_oleh' => $dosenId,
                'catatan_reviewer' => $catatan,
                'direview_at' => now(),
            ]);

            $praktikum->update(['status_pelaksanaan' => 'siap_berjalan']);
        });
    }

    public function kembalikan(PengajuanModul $pengajuan, int $dosenId, string $catatan, bool $tolak = false): void
    {
        DB::transaction(function () use ($pengajuan, $dosenId, $catatan, $tolak) {
            $pengajuan = PengajuanModul::query()->lockForUpdate()->findOrFail($pengajuan->id);
            if ($pengajuan->status !== 'diajukan') {
                throw ValidationException::withMessages(['pengajuan' => 'Pengajuan sudah diputuskan oleh dosen lain.']);
            }

            $pengajuan->update([
                'status' => $tolak ? 'ditolak' : 'perlu_revisi',
                'direview_oleh' => $dosenId,
                'catatan_reviewer' => $catatan,
                'direview_at' => now(),
            ]);
        });
    }

    public function batalkan(PengajuanModul $pengajuan): void
    {
        DB::transaction(function () use ($pengajuan) {
            $pengajuan = PengajuanModul::query()->lockForUpdate()->findOrFail($pengajuan->id);
            if (! in_array($pengajuan->status, ['draft', 'diajukan', 'perlu_revisi'], true)) {
                throw ValidationException::withMessages(['pengajuan' => 'Pengajuan yang sudah diputuskan tidak dapat dibatalkan.']);
            }

            $pengajuan->update(['status' => 'dibatalkan']);
        });
    }

    private function praktikumSebelumnya(Praktikum $praktikum): ?Praktikum
    {
        if (! $praktikum->matkul_id) {
            return null;
        }

        return Praktikum::query()
            ->where('matkul_id', $praktikum->matkul_id)
            ->where('id', '!=', $praktikum->id)
            ->whereHas('modul')
            ->where(function ($query) use ($praktikum) {
                $query->where('tahun_ajaran', '<', $praktikum->tahun_ajaran);
                if (strtolower((string) $praktikum->semester) === 'genap') {
                    $query->orWhere(function ($sameYear) use ($praktikum) {
                        $sameYear->where('tahun_ajaran', $praktikum->tahun_ajaran)
                            ->whereRaw('LOWER(semester) = ?', ['ganjil']);
                    });
                }
            })
            ->orderByDesc('tahun_ajaran')
            ->orderByRaw("CASE WHEN LOWER(semester) = 'genap' THEN 2 ELSE 1 END DESC")
            ->first();
    }

    private function pastikanBelumBerjalan(Praktikum $praktikum): void
    {
        if ($praktikum->started_at || $praktikum->status_pelaksanaan === 'berjalan') {
            throw ValidationException::withMessages([
                'pengajuan' => 'Struktur modul sudah dikunci karena praktikum telah berjalan.',
            ]);
        }
    }
}
