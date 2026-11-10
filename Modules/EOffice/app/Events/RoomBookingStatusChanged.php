<?php

namespace Modules\EOffice\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\EOffice\Models\Peminjaman;

class RoomBookingStatusChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Peminjaman $peminjaman;
    public string $newStatus;
    public ?string $previousStatus;
    public ?string $alasan;

    /**
     * Create a new event instance.
     *
     * @param Peminjaman $peminjaman
     * @param string $newStatus ('disetujui', 'ditolak', 'dibatalkan', dll)
     * @param string|null $previousStatus
     * @param string|null $alasan
     */
    public function __construct(Peminjaman $peminjaman, string $newStatus, ?string $previousStatus = null, ?string $alasan = null)
    {
        $this->peminjaman = $peminjaman;
        $this->newStatus = $newStatus;
        $this->previousStatus = $previousStatus;
        $this->alasan = $alasan;
    }
}

