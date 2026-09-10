<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BlindReviewCompletedAdminNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected int $roundId,
        protected string $mataKuliahNama,
        protected string $creatorName,
        protected bool $autoApproved = false
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $prefix = $this->autoApproved
            ? 'Auto-approve (batas waktu tercapai)'
            : 'Blind review selesai';

        return [
            'type' => 'blind_review_completed',
            'round_id' => $this->roundId,
            'mata_kuliah' => $this->mataKuliahNama,
            'creator' => $this->creatorName,
            'auto_approved' => $this->autoApproved,
            'message' => "{$prefix}: soal mata kuliah {$this->mataKuliahNama} dari {$this->creatorName} siap dicetak.",
            'url' => route('banksoal.soal.dosen.index'),
        ];
    }
}
