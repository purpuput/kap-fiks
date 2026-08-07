<?php

namespace App\Notifications;

use App\Models\AuditReport;
use App\Models\AuditReportFile;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuditReportReviewedNotification extends Notification
{
    use Queueable;

    public AuditReport $auditReport;
    public ?AuditReportFile $file;
    public string $actionStatus; // 'approved', 'revision', 'resubmitted'
    public ?string $note;
    public ?string $reviewerName;

    public function __construct(
        AuditReport $auditReport,
        string $actionStatus,
        ?AuditReportFile $file = null,
        ?string $note = null,
        ?string $reviewerName = null
    ) {
        $this->auditReport = $auditReport;
        $this->actionStatus = $actionStatus;
        $this->file = $file;
        $this->note = $note;
        $this->reviewerName = $reviewerName ?? 'Pimpinan';
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $reportNumber = $this->auditReport->report_number;
        $clientName = $this->auditReport->client ? $this->auditReport->client->institution_name : '';

        if ($this->actionStatus === 'created') {
            $subject = "[KAP Eko Prasetyo] Laporan Audit Baru Dibuat - {$reportNumber}";
            $mail = (new MailMessage)
                ->subject($subject)
                ->greeting("Halo {$notifiable->name},")
                ->line("Laporan Audit baru **{$reportNumber}** untuk klien **{$clientName}** telah dibuat oleh **{$this->reviewerName}** dan siap untuk di-review.");
        } elseif ($this->actionStatus === 'updated') {
            $docInfo = $this->file ? " ({$this->file->document_name})" : '';
            $subject = "[KAP Eko Prasetyo] Pembaruan Dokumen Laporan Audit - {$reportNumber}";
            $mail = (new MailMessage)
                ->subject($subject)
                ->greeting("Halo {$notifiable->name},")
                ->line("Dokumen pada Laporan Audit **{$reportNumber}** ({$clientName}) telah diunggah/diperbarui{$docInfo} oleh **{$this->reviewerName}**.");
        } elseif ($this->actionStatus === 'resubmitted') {
            $subject = "[KAP Eko Prasetyo] Laporan Audit {$reportNumber} Dikirim Ulang oleh Auditor";
            $mail = (new MailMessage)
                ->subject($subject)
                ->greeting("Halo {$notifiable->name},")
                ->line("Laporan Audit **{$reportNumber}** ({$clientName}) telah diperbaiki dan dikirim ulang oleh Auditor untuk di-review kembali.");
        } else {
            $isApproved = $this->actionStatus === 'approved';
            $statusLabel = $isApproved ? 'Disetujui' : 'Memerlukan Revisi';
            $docInfo = $this->file ? " Dokumen ({$this->file->document_name})" : '';

            $subject = "[KAP Eko Prasetyo] Review Laporan Audit {$reportNumber}{$docInfo} - {$statusLabel}";
            $mail = (new MailMessage)
                ->subject($subject)
                ->greeting("Halo {$notifiable->name},")
                ->line("Review Pimpinan pada Laporan Audit **{$reportNumber}** ({$clientName}) telah diperbarui oleh **{$this->reviewerName}**.");

            if ($this->file) {
                $mail->line("Nama Dokumen: **{$this->file->document_name}**");
            }

            $mail->line("Status Review: **{$statusLabel}**");

            if (!$isApproved && !empty($this->note)) {
                $mail->line("Catatan Revisi: \"{$this->note}\"");
            }
        }

        $url = route('audit_reports.show', $this->auditReport->id);

        return $mail
            ->action('Lihat Laporan Audit', $url)
            ->line('Terima kasih atas perhatian dan kerja samanya.');
    }

    public function toArray($notifiable): array
    {
        $reportNumber = $this->auditReport->report_number;
        $clientName = $this->auditReport->client ? $this->auditReport->client->institution_name : '';

        if ($this->actionStatus === 'created') {
            $title = 'Laporan Audit Baru Dibuat';
            $message = "Laporan Audit baru {$reportNumber} ({$clientName}) telah dibuat oleh {$this->reviewerName}.";
            $icon = 'document-plus';
        } elseif ($this->actionStatus === 'updated') {
            $docName = $this->file ? $this->file->document_name : 'Dokumen Laporan';
            $title = 'Pembaruan Dokumen Laporan';
            $message = "{$docName} pada Laporan Audit {$reportNumber} ({$clientName}) telah diperbarui oleh {$this->reviewerName}.";
            $icon = 'pencil-alt';
        } elseif ($this->actionStatus === 'resubmitted') {
            $title = 'Laporan Audit Dikirim Ulang';
            $message = "Laporan Audit {$reportNumber} ({$clientName}) telah diperbaiki dan dikirim ulang untuk di-review.";
            $icon = 'refresh';
        } else {
            $isApproved = $this->actionStatus === 'approved';
            $docName = $this->file ? $this->file->document_name : 'Laporan Audit';

            $title = $isApproved ? "Dokumen Disetujui Pimpinan" : "Revisi Dokumen Required";
            $message = $isApproved
                ? "{$docName} pada Laporan Audit {$reportNumber} telah disetujui."
                : "{$docName} pada Laporan Audit {$reportNumber} memerlukan revisi: " . ($this->note ?? 'Cek catatan.');
            $icon = $isApproved ? 'check-circle' : 'exclamation-circle';
        }

        return [
            'type' => 'audit_report',
            'audit_report_id' => $this->auditReport->id,
            'audit_file_id' => $this->file ? $this->file->id : null,
            'title' => $title,
            'message' => $message,
            'status' => $this->actionStatus,
            'reviewer' => $this->reviewerName,
            'url' => route('audit_reports.show', $this->auditReport->id),
            'icon' => $icon,
        ];
    }
}
