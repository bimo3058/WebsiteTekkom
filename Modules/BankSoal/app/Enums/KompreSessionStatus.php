<?php

namespace Modules\BankSoal\Enums;

enum KompreSessionStatus: string
{
    case Ongoing  = 'ongoing';
    case Finished = 'finished';

    /** Label Indonesia untuk tampilan UI. */
    public function label(): string
    {
        return match($this) {
            self::Ongoing  => 'Sedang Berjalan',
            self::Finished => 'Selesai',
        };
    }

    /** Warna badge untuk tampilan UI. */
    public function color(): string
    {
        return match($this) {
            self::Ongoing  => 'emerald',
            self::Finished => 'slate',
        };
    }
}
