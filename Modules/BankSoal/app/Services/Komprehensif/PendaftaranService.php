<?php

namespace Modules\BankSoal\Services\Komprehensif;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\BankSoal\Enums\KompreSessionStatus;
use Modules\BankSoal\Enums\PendaftaranStatus;
use Modules\BankSoal\Models\Komprehensif\JadwalUjian;
use Modules\BankSoal\Models\Komprehensif\KompreSession;
use Modules\BankSoal\Models\Komprehensif\PendaftarUjian;
use Modules\BankSoal\Models\Komprehensif\PeriodeUjian;

class PendaftaranService
{
    /**
     * Ambil periode ujian aktif saat ini.
     * Jika masih berstatus 'draft' dan tanggal mulai sudah tiba, auto-promote ke 'aktif'.
     */
    public function getActivePeriode(): ?PeriodeUjian
    {
        $periode = PeriodeUjian::currentlyActive()->latest()->first();

        if ($periode && $periode->status === 'draft') {
            $periode->update(['status' => 'aktif']);
            $periode->refresh();
        }

        return $periode;
    }

    /**
     * Cek apakah kuota pendaftaran pada periode sudah penuh.
     */
    public function isKuotaPenuh(PeriodeUjian $periode): bool
    {
        if ($periode->kuota_peserta === null) {
            return false;
        }

        return PendaftarUjian::where('periode_ujian_id', $periode->id)->count()
            >= $periode->kuota_peserta;
    }

    /**
     * Daftarkan mahasiswa ke periode ujian.
     * Menggunakan DB transaction + lockForUpdate untuk mencegah race condition pada kuota.
     *
     * @throws \RuntimeException jika kuota penuh
     */
    public function daftar(PeriodeUjian $periode, int $mahasiswaId, array $data): PendaftarUjian
    {
        return DB::transaction(function () use ($periode, $mahasiswaId, $data): PendaftarUjian {
            // Lock baris periode agar concurrent request antre satu per satu
            $lockedPeriode = PeriodeUjian::lockForUpdate()->findOrFail($periode->id);

            if ($this->isKuotaPenuh($lockedPeriode)) {
                throw new \RuntimeException('Maaf, kuota pendaftaran untuk periode ini sudah penuh.');
            }

            return PendaftarUjian::create([
                'periode_ujian_id'      => $lockedPeriode->id,
                'mahasiswa_id'          => $mahasiswaId,
                'nim'                   => $data['nim'],
                'nama_lengkap'          => $data['nama'],
                'kontak_wa'             => $data['kontak_wa'],
                'semester_aktif'        => $data['semester'],
                'target_wisuda'         => $data['target_wisuda'],
                'dosen_pembimbing_1_id' => $data['dosen_pembimbing_1_id'],
                'dosen_pembimbing_2_id' => $data['dosen_pembimbing_2_id'] ?? null,
                'status_pendaftaran'    => PendaftaranStatus::Pending->value,
            ]);
        });
    }

    /**
     * Auto-grade mahasiswa yang tidak mengerjakan ujian (no-show).
     * Dipanggil saat dashboard dimuat — membuat sesi dummy jika jadwal sudah lewat
     * dan tidak ada sesi CBT yang pernah dimulai.
     */
    public function autoGradeNoShow(JadwalUjian $jadwal, int $userId): ?KompreSession
    {
        if (!$jadwal->tanggal_ujian || !$jadwal->waktu_selesai) {
            return null;
        }

        $waktuSelesai = Carbon::parse(
            $jadwal->tanggal_ujian->format('Y-m-d') . ' ' . $jadwal->waktu_selesai
        );

        // Belum lewat waktu ujian
        if (now()->lt($waktuSelesai)) {
            return null;
        }

        // Sudah pernah punya sesi — tidak perlu auto-grade
        $sudahPunyaSesi = KompreSession::where('user_id', $userId)
            ->where('jadwal_id', $jadwal->id)
            ->exists();

        if ($sudahPunyaSesi) {
            return null;
        }

        $waktuMulai = Carbon::parse(
            $jadwal->tanggal_ujian->format('Y-m-d') . ' ' . $jadwal->waktu_mulai
        );

        return KompreSession::create([
            'user_id'     => $userId,
            'jadwal_id'   => $jadwal->id,
            'title'       => 'Tidak Mengerjakan',
            'status'      => KompreSessionStatus::Finished,
            'score'       => 0,
            'started_at'  => $waktuMulai,
            'finished_at' => $waktuSelesai,
        ]);
    }
}
