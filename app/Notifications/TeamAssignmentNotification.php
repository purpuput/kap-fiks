<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamAssignmentNotification extends Notification
{
    use Queueable;

    public Client $client;
    public string $teamRole; // 'ketua' atau 'anggota' / 'ketua tim' / 'anggota tim'
    public ?string $assignerName;

    public function __construct(Client $client, string $teamRole, ?string $assignerName = null)
    {
        $this->client = $client;
        $this->teamRole = str_contains(strtolower($teamRole), 'ketua') ? 'Ketua Tim' : 'Anggota Tim';
        $this->assignerName = $assignerName ?? 'Admin';
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $clientName = $this->client->institution_name;

        return (new MailMessage)
            ->subject("[KAP Eko Prasetyo] Penugasan Tim Audit Klien - {$clientName}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Anda telah ditugaskan oleh **{$this->assignerName}** sebagai **{$this->teamRole}** untuk perikatan audit klien:")
            ->line("**Nama Klien / Institusi**: {$clientName}")
            ->line("**Jenis Audit**: " . ucfirst($this->client->audit_type))
            ->line("**Alamat**: {$this->client->address}, {$this->client->city}")
            ->action('Lihat Detail Klien', route('clients.show', $this->client->id))
            ->line('Selamat bertugas dan jaga selalu profesionalisme.');
    }

    public function toArray($notifiable): array
    {
        $clientName = $this->client->institution_name;

        return [
            'type' => 'team_assignment',
            'client_id' => $this->client->id,
            'title' => 'Penugasan Tim Audit Baru',
            'message' => "Anda telah ditugaskan sebagai {$this->teamRole} untuk klien {$clientName}.",
            'status' => 'assigned',
            'url' => route('clients.show', $this->client->id),
            'icon' => 'user-group',
        ];
    }
}
