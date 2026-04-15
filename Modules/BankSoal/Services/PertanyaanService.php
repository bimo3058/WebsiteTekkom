<?php

namespace Modules\BankSoal\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\BankSoal\Models\Pertanyaan;
use Modules\BankSoal\Models\Jawaban;

class PertanyaanService
{
    /**
     * Listing soal dengan filter — untuk halaman bank soal admin/dosen.
     */
    public function list(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return Pertanyaan::with(['cpl', 'mataKuliah'])
            ->withCount('jawaban')
            ->when(isset($filters['mk_id']), fn($q) => $q->byMk((int) $filters['mk_id']))
            ->when(isset($filters['cpl_id']), fn($q) => $q->byCpl((int) $filters['cpl_id']))
            ->when(isset($filters['kesulitan']), fn($q) => $q->byKesulitan($filters['kesulitan']))
            ->when(isset($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->when(isset($filters['search']), fn($q) => $q->search($filters['search']))
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function findById(int $id): Pertanyaan
    {
        return Pertanyaan::with(['cpl', 'mataKuliah', 'jawaban'])->findOrFail($id);
    }

    /**
     * Buat soal beserta pilihan jawaban dalam satu transaksi.
     *
     * @param array $data     { soal, gambar?, bobot, cpl_id, mk_id, kesulitan }
     * @param array $jawaban  [{ opsi, deskripsi, gambar?, is_benar }, ...]
     */
    public function create(array $data, array $jawaban): Pertanyaan
    {
        return DB::transaction(function () use ($data, $jawaban) {
            $pertanyaan = Pertanyaan::create(array_merge($data, [
                'status' => Pertanyaan::STATUS_DRAFT,
            ]));

            if (($data['tipe_soal'] ?? 'pilihan_ganda') !== 'essay') {
                $this->syncJawaban($pertanyaan->id, $jawaban);
            }

            return $pertanyaan->load('jawaban');
        });
    }

    /**
     * Update soal dan jawaban — jawaban lama diganti seluruhnya.
     */
    public function update(int $id, array $data, array $jawaban): Pertanyaan
    {
        return DB::transaction(function () use ($id, $data, $jawaban) {
            $pertanyaan = Pertanyaan::findOrFail($id);

            // Soal yang sudah disetujui tidak boleh diedit
            if ($pertanyaan->isDisetujui()) {
                throw new \RuntimeException('Soal yang sudah disetujui tidak dapat diedit.');
            }

            $pertanyaan->update($data);

            if (($data['tipe_soal'] ?? 'pilihan_ganda') !== 'essay') {
                $this->syncJawaban($pertanyaan->id, $jawaban);
            } else {
                // Hapus jawaban sebelumnya jika tipe diubah menjadi essay
                Jawaban::where('soal_id', $pertanyaan->id)->delete();
            }

            return $pertanyaan->fresh('jawaban');
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $pertanyaan = Pertanyaan::findOrFail($id);

            if ($pertanyaan->isDisetujui()) {
                throw new \RuntimeException('Soal yang sudah disetujui tidak dapat dihapus.');
            }

            $pertanyaan->jawaban()->delete();
            $pertanyaan->delete();
        });
    }

    // -------------------------------------------------------------------------
    // Workflow status
    // -------------------------------------------------------------------------

    /**
     * Transisi status soal dengan validasi aturan workflow.
     */
    public function transisiStatus(int $id, string $newStatus): Pertanyaan
    {
        return DB::transaction(function () use ($id, $newStatus) {
            $pertanyaan = Pertanyaan::findOrFail($id);

            if (!$pertanyaan->canTransitionTo($newStatus)) {
                throw new \RuntimeException(
                    "Tidak bisa mengubah status dari '{$pertanyaan->status}' ke '{$newStatus}'."
                );
            }

            $pertanyaan->update(['status' => $newStatus]);
            return $pertanyaan->fresh();
        });
    }

    public function ajukan(int $id): Pertanyaan
    {
        return $this->transisiStatus($id, Pertanyaan::STATUS_DIAJUKAN);
    }

    public function setujui(int $id): Pertanyaan
    {
        return $this->transisiStatus($id, Pertanyaan::STATUS_DISETUJUI);
    }

    public function kembalikanRevisi(int $id): Pertanyaan
    {
        return $this->transisiStatus($id, Pertanyaan::STATUS_REVISI);
    }

    // -------------------------------------------------------------------------
    // Helper internal
    // -------------------------------------------------------------------------

    private function syncJawaban(int $pertanyaanId, array $jawaban): void
    {
        // Validasi: minimal satu jawaban benar
        $adaBenar = collect($jawaban)->contains(fn($j) => filter_var($j['is_benar'] ?? false, FILTER_VALIDATE_BOOLEAN));

        if (!$adaBenar) {
            throw new \RuntimeException('Minimal satu pilihan jawaban harus ditandai benar.');
        }

        Jawaban::where('soal_id', $pertanyaanId)->delete();

        $rows = array_map(fn($j) => array_merge($j, [
            'soal_id'    => $pertanyaanId,
            // Postgres membutuhkan literal boolean string 'true' / 'false' untuk raw insert
            'is_benar'   => filter_var($j['is_benar'], FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false',
            'created_at' => now(),
            'updated_at' => now(),
        ]), $jawaban);

        Jawaban::insert($rows);
    }
}