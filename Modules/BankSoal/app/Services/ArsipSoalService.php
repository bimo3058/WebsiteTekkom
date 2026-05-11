<?php

namespace Modules\BankSoal\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\BankSoal\Models\ArsipSoal;
use Modules\BankSoal\Models\PenarikanSoal;
use Modules\BankSoal\Models\PenarikanSoalArchived;

class ArsipSoalService
{
    public function savePenarikanSoal(int $dosenId, int $mkId, array $data): PenarikanSoal
    {
        return DB::transaction(function () use ($dosenId, $mkId, $data) {
            $soalList = $this->normalizeSoalList($data['soal_list'] ?? []);

            return PenarikanSoal::create([
                'dosen_id' => $dosenId,
                'mk_id' => $mkId,
                'nama_ekstraksi' => $data['nama_ekstraksi'] ?? 'Ekstraksi Soal',
                'tipe_ujian' => $data['tipe_ujian'] ?? 'lainnya',
                'metode_ujian' => $data['metode_ujian'] ?? 'online',
                'status_cetak' => $data['status_cetak'] ?? null,
                'tahun_akademik' => $data['tahun_akademik'] ?? date('Y') . '/' . (date('Y') + 1),
                'semester' => $data['semester'] ?? 'Genap',
                'tanggal_ujian' => $data['tanggal_ujian'] ?? null,
                'soal_data' => json_encode($soalList),
                'jumlah_soal' => count($soalList),
                'total_bobot' => $this->calculateTotalBobot($soalList),
                'pdf_file_path' => $data['pdf_file_path'] ?? null,
                'pdf_file_hash' => $data['pdf_file_hash'] ?? null,
                'status' => 'pending',
                'deskripsi' => $data['deskripsi'] ?? null,
                'catatan_internal' => $data['catatan_internal'] ?? null,
            ]);
        });
    }

    public function createFromEkstraksi(int $dosenId, int $mkId, array $data, bool $directArchive = false)
    {
        return DB::transaction(function () use ($dosenId, $mkId, $data, $directArchive) {
            $penarikan = $this->savePenarikanSoal($dosenId, $mkId, $data);

            if (! $directArchive) {
                return $penarikan;
            }

            $arsip = ArsipSoal::create([
                'mk_id' => $mkId,
                'dosen_id' => $dosenId,
                'tahun_akademik' => $penarikan->tahun_akademik,
                'semester' => $penarikan->semester,
                'nama_arsip' => $data['nama_arsip'] ?? $penarikan->nama_ekstraksi,
                'tipe_ujian' => $penarikan->tipe_ujian,
                'tanggal_ujian' => $penarikan->tanggal_ujian,
                'jumlah_soal' => $penarikan->jumlah_soal,
                'total_bobot' => $penarikan->total_bobot,
                'soal_data' => $penarikan->soal_data,
                'pdf_file_path' => $penarikan->pdf_file_path,
                'pdf_file_hash' => $penarikan->pdf_file_hash,
                'status' => 'final',
                'deskripsi' => $data['deskripsi'] ?? null,
                'catatan_internal' => $data['catatan_internal'] ?? null,
            ]);

            PenarikanSoalArchived::create([
                'penarikan_id' => $penarikan->id,
                'arsip_id' => $arsip->id,
                'archived_at' => now(),
                'archived_by' => $dosenId,
                'catatan_konversi' => $data['catatan_konversi'] ?? null,
            ]);

            $penarikan->update(['status' => 'archived']);

            return $arsip;
        });
    }

    public function convertPenarikanToArsip(int $penarikanId, int $dosenId, array $additionalData = []): ArsipSoal
    {
        return DB::transaction(function () use ($penarikanId, $dosenId, $additionalData) {
            $penarikan = PenarikanSoal::query()
                ->where('id', $penarikanId)
                ->where('dosen_id', $dosenId)
                ->where('status', 'pending')
                ->firstOrFail();

            $arsip = ArsipSoal::create([
                'mk_id' => $penarikan->mk_id,
                'dosen_id' => $dosenId,
                'tahun_akademik' => $penarikan->tahun_akademik,
                'semester' => $penarikan->semester,
                'nama_arsip' => $additionalData['nama_arsip'] ?? $penarikan->nama_ekstraksi,
                'tipe_ujian' => $penarikan->tipe_ujian,
                'tanggal_ujian' => $penarikan->tanggal_ujian,
                'jumlah_soal' => $penarikan->jumlah_soal,
                'total_bobot' => $penarikan->total_bobot,
                'soal_data' => $penarikan->soal_data,
                'pdf_file_path' => $penarikan->pdf_file_path,
                'pdf_file_hash' => $penarikan->pdf_file_hash,
                'status' => 'final',
                'deskripsi' => $additionalData['deskripsi'] ?? $penarikan->deskripsi,
                'catatan_internal' => $additionalData['catatan_internal'] ?? $penarikan->catatan_internal,
            ]);

            PenarikanSoalArchived::create([
                'penarikan_id' => $penarikan->id,
                'arsip_id' => $arsip->id,
                'archived_at' => now(),
                'archived_by' => $dosenId,
                'catatan_konversi' => $additionalData['catatan_konversi'] ?? null,
            ]);

            $penarikan->update(['status' => 'archived']);

            return $arsip;
        });
    }

    public function getArsipGroupedByTahunSemester(int $dosenId)
    {
        return ArsipSoal::query()
            ->with('mataKuliah')
            ->byDosen($dosenId)
            ->where('status', 'final')
            ->orderByDesc('tahun_akademik')
            ->orderByDesc('semester')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(fn ($item) => $item->tahun_akademik . '|' . $item->semester)
            ->map(fn ($group) => $group->groupBy('mk_id'));
    }

    public function getPenarikanPending(int $dosenId)
    {
        return PenarikanSoal::query()
            ->with('mataKuliah')
            ->byDosen($dosenId)
            ->pending()
            ->orderByDesc('created_at')
            ->paginate(10);
    }

    public function getPenarikanById(int $penarikanId, int $dosenId): PenarikanSoal
    {
        return PenarikanSoal::query()
            ->where('id', $penarikanId)
            ->where('dosen_id', $dosenId)
            ->firstOrFail();
    }

    public function getArsipById(int $arsipId, int $dosenId): ArsipSoal
    {
        return ArsipSoal::query()
            ->where('id', $arsipId)
            ->where('dosen_id', $dosenId)
            ->firstOrFail();
    }

    public function discardPenarikan(int $penarikanId, int $dosenId): void
    {
        $penarikan = $this->getPenarikanById($penarikanId, $dosenId);
        $penarikan->update(['status' => 'discarded']);
    }

    private function normalizeSoalList(array $soalList): array
    {
        return array_values(array_map(function ($soal, $index) {
            if (! is_array($soal)) {
                $soal = ['soal' => (string) $soal];
            }

            return [
                'nomor' => $soal['nomor'] ?? $index + 1,
                'id' => $soal['id'] ?? null,
                'soal' => $soal['soal'] ?? '',
                'cpl' => $soal['cpl'] ?? null,
                'cpmk' => $soal['cpmk'] ?? null,
                'tipe_soal' => $soal['tipe_soal'] ?? null,
                'bobot' => (int) ($soal['bobot'] ?? 1),
            ];
        }, $soalList, array_keys($soalList)));
    }

    private function calculateTotalBobot(array $soalList): float
    {
        return (float) collect($soalList)->sum(fn ($soal) => (int) ($soal['bobot'] ?? 1));
    }
}