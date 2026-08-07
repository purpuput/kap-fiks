<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditReport extends Model
{
    protected $fillable = [

        'client_id',

        'report_number',

        'title',

        'audit_type',

        'audit_date',

        'findings',

        'recommendations',

        'status',

        'revision_note',

        'created_by',

        'reviewed_by',

        'reviewed_at',
    ];

    protected $casts = [

        'audit_date' => 'date',

        'reviewed_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relasi client
    |--------------------------------------------------------------------------
    */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Auditor pembuat laporan
    |--------------------------------------------------------------------------
    */
    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Pimpinan reviewer
    |--------------------------------------------------------------------------
    */
    public function reviewer()
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | File laporan audit
    |--------------------------------------------------------------------------
    */
    public function files()
    {
        return $this->hasMany(
            AuditReportFile::class
        );
    }

    public function documentReviews()
    {
        return $this->hasMany(
            DocumentReview::class,
            'audit_report_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Arsip dokumen
    |--------------------------------------------------------------------------
    */
    public function documentManagements()
    {
        return $this->hasMany(
            AuditReportFile::class
        )->whereHas('review', function ($q) {
            $q->where('status', 'approved');
        })->whereHas('auditReport', function ($q) {
            $q->where('status', 'disetujui');
        });
    }

    public function updateStatusFromReviews($isResubmit = false)
    {
        $totalFilesCount = $this->files()->count();
        $reviews = $this->documentReviews()->get();
        $revisionCount = $reviews->where('status', 'revision')->count();
        $approvedCount = $reviews->where('status', 'approved')->count();

        if ($revisionCount > 0) {
            $this->status = 'revisi';
        } elseif ($approvedCount === $totalFilesCount && $totalFilesCount > 0) {
            $this->status = 'disetujui';
            if (!$this->reviewed_at) {
                $this->reviewed_at = now();
            }
        } else {
            $this->status = 'review';
        }

        $this->save();
    }

    /*
    |--------------------------------------------------------------------------
    | Status helper
    |--------------------------------------------------------------------------
    */

    // Review
    public function isReview(): bool
    {
        return $this->status === 'review';
    }

    // Revisi
    public function isRevision(): bool
    {
        return $this->status === 'revisi';
    }

    // Approved
    public function isApproved(): bool
    {
        return $this->status === 'disetujui';
    }

    public static function statusLabels(): array
    {
        return [
            'draft' => 'Draft',
            'review' => 'Dalam Review',
            'revisi' => 'Perlu Revisi',
            'disetujui' => 'Disetujui',
        ];
    }

    public static function statusDescriptions(): array
    {
        return [
            'draft' => 'Belum diajukan',
            'review' => 'Sedang diperiksa auditor/reviewer',
            'revisi' => 'Ada temuan/catatan yang harus diperbaiki',
            'disetujui' => 'Sudah lolos review dan final',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusDescriptionAttribute(): string
    {
        return self::statusDescriptions()[$this->status] ?? '';
    }
}
