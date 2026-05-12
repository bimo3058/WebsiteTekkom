<?php

namespace Modules\BankSoal\Enums;

enum PendaftaranStatus: string
{
    case Pending  = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    /** Label Indonesia untuk tampilan UI. */
    public function label(): string
    {
        return match($this) {
            self::Pending  => 'Menunggu Review',
            self::Approved => 'Disetujui',
            self::Rejected => 'Ditolak',
        };
    }

    /** Warna badge untuk tampilan UI. */
    public function color(): string
    {
        return match($this) {
            self::Pending  => 'amber',
            self::Approved => 'green',
            self::Rejected => 'red',
        };
    }
}
