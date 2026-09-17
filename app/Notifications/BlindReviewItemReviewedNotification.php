<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BlindReviewItemReviewedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected int $roundId,
        protected string $mataKuliahNama,
        protected string $reviewerName,
        protected string $status,
        protected ?string $catatan
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $statusLabel = $this->status === 'approved' ? 'disetujui' : 'ditolak';

        return [
            'type' => 'blind_review_item_reviewed',
            'round_id' => $this->roundId,
            'mata_kuliah' => $this->mataKuliahNama,
            'reviewer' => $this->reviewerName,
            'status' => $this->status,
            'catatan' => $this->catatan,
            'message' => "{$this->reviewerName} telah {$statusLabel} butir soal pada blind review mata kuliah {$this->mataKuliahNama}."
                . ($this->catatan ? " Catatan: {$this->catatan}" : ''),
            'url' => route('banksoal.soal.dosen.index'),
        ];
    }
}
