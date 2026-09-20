<?php

namespace Modules\BankSoal\Enums;

/**
 * Status Workflow:
 * 0. DRAFT     -> Progress tersimpan sementara di bs_rps_draft (belum diajukan)
 * 1. DIAJUKAN  -> Dosen submit, menunggu review GPM
 * 2. REVISI    -> GPM minta revisi
 * 3. DISETUJUI -> GPM setujui
 */
enum RpsStatus: string
{
    case DRAFT     = 'draft';
    case DIAJUKAN  = 'diajukan';
    case REVISI    = 'revisi';
    case DISETUJUI = 'disetujui';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT     => 'Draft',
            self::DIAJUKAN  => 'Diajukan',
            self::REVISI    => 'Revisi',
            self::DISETUJUI => 'Disetujui',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT     => 'badge-secondary',
            self::DIAJUKAN  => 'badge-pending',
            self::REVISI    => 'badge-rejected',
            self::DISETUJUI => 'badge-verified',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
