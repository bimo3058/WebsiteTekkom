<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BlindReviewAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected int $itemId,
        protected string $mataKuliahNama,
        protected string $creatorName
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'blind_review_assigned',
            'item_id' => $this->itemId,
            'mata_kuliah' => $this->mataKuliahNama,
            'creator' => $this->creatorName,
            'message' => "Anda ditugaskan untuk blind review soal pada mata kuliah {$this->mataKuliahNama}.",
            'url' => route('banksoal.soal.dosen.blind-review.index'),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tugas Blind Review Baru')
            ->line("Anda ditugaskan untuk blind review soal pada mata kuliah {$this->mataKuliahNama}.")
            ->action('Buka Inbox Blind Review', route('banksoal.soal.dosen.blind-review.index'));
    }
}
