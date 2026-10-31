<?php

namespace Modules\ManajemenMahasiswa\Support;

/**
 * Jenis dokumen kegiatan yang dipilih lewat dropdown di form Rencana Proker,
 * Pelaksanaan, dan Laporan & Arsip (S-56). Disimpan di mk_repo_mulmed.jenis_dokumen.
 *
 * Rencana hanya memuat dokumen pra-acara; Notulensi dan LPJ baru ada setelah acara,
 * jadi hanya ditawarkan di Pelaksanaan & Arsip. Proposal, RAB, Notulensi, dan LPJ
 * cukup satu per kegiatan; surat undangan bisa banyak (satu per dosen/tamu) begitu
 * pula surat peminjaman (Recreation 2025: 7 undangan, 2 peminjaman).
 */
class JenisDokumenKegiatan
{
    /** Urutan ini juga urutan pengelompokan di halaman detail. */
    public const LABEL = [
        'proposal'   => 'Proposal',
        'rab'        => 'RAB',
        'undangan'   => 'Surat Undangan',
        'peminjaman' => 'Surat Peminjaman',
        'notulensi'  => 'Notulensi',
        'lpj'        => 'LPJ',
        'lainnya'    => 'Lainnya',
    ];

    public const PASCA_ACARA = ['notulensi', 'lpj'];

    public const MAKS_SATU = ['proposal', 'rab', 'notulensi', 'lpj'];

    /** Dokumen lama (sebelum S-56) belum punya jenis. */
    public const LABEL_KOSONG = 'Belum dikelompokkan';

    /**
     * @return array<string, string> kode => label yang boleh dipilih di tahap ini
     */
    public static function pilihan(bool $tahapRencana): array
    {
        return $tahapRencana
            ? array_diff_key(self::LABEL, array_flip(self::PASCA_ACARA))
            : self::LABEL;
    }

    public static function label(?string $kode): string
    {
        return self::LABEL[$kode] ?? self::LABEL_KOSONG;
    }
}
