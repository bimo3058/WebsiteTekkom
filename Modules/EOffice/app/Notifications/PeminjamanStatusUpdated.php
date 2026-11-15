<?php

namespace Modules\EOffice\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Modules\EOffice\Models\Peminjaman;

class PeminjamanStatusUpdated extends Notification
{
    use Queueable;

    protected $peminjaman;
    protected $statusMessage;
    protected $isCancelByAdmin;
    protected $customMessage;

    /**
     * Create a new notification instance.
     *
     * @param Peminjaman $peminjaman
     * @param bool $isCancelByAdmin 
     * @param string|null $customMessage
     */
    public function __construct(Peminjaman $peminjaman, $isCancelByAdmin = false, ?string $customMessage = null)
    {
        $this->peminjaman = $peminjaman;
        $this->isCancelByAdmin = $isCancelByAdmin;
        $this->customMessage = $customMessage;

        if ($this->isCancelByAdmin) {
            $this->statusMessage = 'yang sebelumnya disetujui, kini terpaksa dibatalkan oleh admin';
        } elseif ($this->peminjaman->status == 'disetujui') {
            $this->statusMessage = 'telah disetujui';
        } elseif ($this->peminjaman->status == 'ditolak') {
            $this->statusMessage = 'telah ditolak';
        } else {
            $this->statusMessage = 'diperbarui';
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database']; // Only save to database for now
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        if (!$this->peminjaman->relationLoaded('ruangan')) {
            $this->peminjaman->loadMissing('ruangan');
        }

        $ruanganNama = $this->peminjaman->ruangan ? $this->peminjaman->ruangan->nama : 'Ruangan';
        $tanggal = \Carbon\Carbon::parse($this->peminjaman->tanggal_pinjam)->translatedFormat('d M Y');
        $jamMulai = \Carbon\Carbon::parse($this->peminjaman->jam_mulai)->format('H:i');
        $jamSelesai = $this->peminjaman->jam_selesai ? \Carbon\Carbon::parse($this->peminjaman->jam_selesai)->format('H:i') : null;
        $waktu = $jamSelesai ? "{$jamMulai} - {$jamSelesai} WIB" : "{$jamMulai} WIB";

        $title = "Peminjaman Ruangan";
        $message = $this->customMessage ?: "Pengajuan peminjaman {$ruanganNama} pada pukul {$waktu} tanggal {$tanggal} {$this->statusMessage}.";

        return [
            'peminjaman_id' => $this->peminjaman->id,
            'status' => $this->isCancelByAdmin ? 'dibatalkan' : $this->peminjaman->status,
            'title' => $title,
            'message' => $message,
            'alasan' => $this->peminjaman->alasan_penolakan,
            'url' => route('eoffice.peminjaman.user.riwayat'),
        ];
    }
}
