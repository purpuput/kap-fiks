<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientApprovalNotification extends Notification
{
    use Queueable;

    public Client $client;
    public string $actionStatus; // 'disetujui' atau 'revisi'
    public ?string $revisionNote;
    public ?string $reviewerName;

    public function __construct(Client $client, string $actionStatus, ?string $revisionNote = null, ?string $reviewerName = null)
    {
        $this->client = $client;
        $this->actionStatus = $actionStatus;
        $this->revisionNote = $revisionNote;
        $this->reviewerName = $reviewerName ?? 'Pimpinan';
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $clientName = $this->client->institution_name;

        if ($this->actionStatus === 'disetujui') {
            $statusLabel = 'Disetujui';
            $subject = "[KAP Eko Prasetyo] Persetujuan Data Klien {$clientName} - Disetujui";
            $line = "Pengajuan data klien **{$clientName}** telah disetujui oleh **{$this->reviewerName}**.";
        } elseif ($this->actionStatus === 'pending') {
            $statusLabel = 'Menunggu Persetujuan';
            $subject = "[KAP Eko Prasetyo] Pengajuan Klien Baru - {$clientName}";
            $line = "Data klien baru **{$clientName}** telah ditambahkan oleh **{$this->reviewerName}** dan memerlukan persetujuan.";
        } else {
            $statusLabel = 'Pembaruan Data';
            $subject = "[KAP Eko Prasetyo] Perubahan Data Klien - {$clientName}";
            $line = "Data klien **{$clientName}** telah diperbarui oleh **{$this->reviewerName}**.";
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Halo {$notifiable->name},")
            ->line($line)
            ->line("Status Klien: **{$statusLabel}**")
            ->action('Lihat Detail Klien', route('clients.show', $this->client->id))
            ->line('Terima kasih.');
    }

    public function toArray($notifiable): array
    {
        $clientName = $this->client->institution_name;

        if ($this->actionStatus === 'disetujui') {
            $title = 'Pengajuan Klien Disetujui';
            $message = "Data klien {$clientName} telah disetujui oleh {$this->reviewerName}.";
            $icon = 'check-badge';
        } elseif ($this->actionStatus === 'pending') {
            $title = 'Pengajuan Klien Baru';
            $message = "Admin {$this->reviewerName} menambahkan data klien {$clientName} yang memerlukan persetujuan.";
            $icon = 'exclamation-circle';
        } else {
            $title = 'Perubahan Data Klien';
            $message = "Data/tim auditor klien {$clientName} telah diperbarui oleh {$this->reviewerName}.";
            $icon = 'pencil';
        }

        return [
            'type' => 'client_approval',
            'client_id' => $this->client->id,
            'title' => $title,
            'message' => $message,
            'status' => $this->actionStatus,
            'reviewer' => $this->reviewerName,
            'url' => route('clients.show', $this->client->id),
            'icon' => $icon,
        ];
    }
}
