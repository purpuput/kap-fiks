<?php

namespace App\Notifications;

use App\Models\Logbook;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LogbookReviewedNotification extends Notification
{
    use Queueable;

    public Logbook $logbook;
    public string $actionStatus; // 'approved' atau 'revision'
    public ?string $reviewerName;

    public function __construct(Logbook $logbook, string $actionStatus, ?string $reviewerName = null)
    {
        $this->logbook = $logbook;
        $this->actionStatus = $actionStatus;
        $this->reviewerName = $reviewerName ?? 'Pimpinan';
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $dateFormatted = date('d-m-Y', strtotime($this->logbook->date));
        $isApproved = $this->actionStatus === 'approved';
        $statusLabel = $isApproved ? 'Disetujui' : 'Memerlukan Revisi';

        $mail = (new MailMessage)
            ->subject("[KAP Eko Prasetyo] Status Logbook Tanggal {$dateFormatted} - {$statusLabel}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Logbook Harian Anda untuk tanggal **{$dateFormatted}** telah di-review oleh **{$this->reviewerName}**.")
            ->line("Status: **{$statusLabel}**");

        if (!$isApproved && !empty($this->logbook->note)) {
            $mail->line("Catatan Revisi dari Pimpinan: \"{$this->logbook->note}\"");
        }

        $url = route('logbooks.show', $this->logbook->id);

        return $mail
            ->action('Lihat Detail Logbook', $url)
            ->line('Terima kasih atas kerja keras dan dedikasi Anda.');
    }

    public function toArray($notifiable): array
    {
        $dateFormatted = date('d-m-Y', strtotime($this->logbook->date));
        $isApproved = $this->actionStatus === 'approved';

        return [
            'type' => 'logbook',
            'logbook_id' => $this->logbook->id,
            'title' => $isApproved ? 'Logbook Disetujui' : 'Revisi Logbook Required',
            'message' => $isApproved
                ? "Logbook Anda tanggal {$dateFormatted} telah disetujui oleh {$this->reviewerName}."
                : "Logbook Anda tanggal {$dateFormatted} perlu direvisi: " . ($this->logbook->note ?? 'Cek detail logbook.'),
            'status' => $this->actionStatus,
            'reviewer' => $this->reviewerName,
            'url' => route('logbooks.show', $this->logbook->id),
            'icon' => $isApproved ? 'check-circle' : 'exclamation-circle',
        ];
    }
}
