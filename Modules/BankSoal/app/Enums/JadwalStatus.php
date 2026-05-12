<?php

namespace Modules\BankSoal\Enums;

enum JadwalStatus: string
{
    case Menunggu = 'menunggu_jadwal';
    case Aktif    = 'aktif';
    case Selesai  = 'selesai';

    /** Label Indonesia untuk tampilan UI. */
    public function label(): string
    {
        return match($this) {
            self::Menunggu => 'Menunggu Jadwal',
            self::Aktif    => 'Aktif',
            self::Selesai  => 'Selesai',
        };
    }

    /** Warna badge untuk tampilan UI. */
    public function color(): string
    {
        return match($this) {
            self::Menunggu => 'amber',
            self::Aktif    => 'blue',
            self::Selesai  => 'slate',
        };
    }
}
