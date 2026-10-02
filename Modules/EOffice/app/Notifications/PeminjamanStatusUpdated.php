<?php

namespace Modules\EOffice\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\EOffice\Models\Peminjaman;

class PeminjamanStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $peminjaman;
    protected $statusMessage;
    protected $isCancelByAdmin;

    /**
     * Create a new notification instance.
     *
     * @param Peminjaman $peminjaman
     * @param bool $isCancelByAdmin 
     */
    public function __construct(Peminjaman $peminjaman, $isCancelByAdmin = false)
    {
        $this->peminjaman = $peminjaman;
        $this->isCancelByAdmin = $isCancelByAdmin;

        if ($this->isCancelByAdmin) {
            $this->statusMessage = 'dibatalkan oleh admin';
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
        $ruanganNama = $this->peminjaman->ruangan ? $this->peminjaman->ruangan->nama : 'Ruangan';
        $tanggal = \Carbon\Carbon::parse($this->peminjaman->tanggal_pinjam)->format('d M Y');

        $title = "Peminjaman Ruangan";
        $message = "Pengajuan peminjaman {$ruanganNama} tanggal {$tanggal} {$this->statusMessage}.";

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
