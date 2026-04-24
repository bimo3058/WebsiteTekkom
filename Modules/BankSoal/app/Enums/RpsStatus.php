<?php

namespace Modules\BankSoal\Enums;

/**
 * Status Workflow:
 * 1. DIAJUKAN -> Dosen submit dokumen RPS, menunggu review dari GPM
 * 2. REVISI -> GPM memberi masukan, Dosen perlu revisi
 * 3. DISETUJUI -> GPM approve dokumen, proses selesai
 */
enum RpsStatus: string
{
    case DIAJUKAN = 'diajukan';
    
    case REVISI = 'revisi';
    
    case DISETUJUI = 'disetujui';

    public function label(): string
    {
        return match ($this) {
            self::DIAJUKAN => 'Diajukan',
            self::REVISI => 'Revisi',
            self::DISETUJUI => 'Disetujui',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DIAJUKAN => 'badge-pending',    
            self::REVISI => 'badge-rejected',    
            self::DISETUJUI => 'badge-verified', 
        };
    }


    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
