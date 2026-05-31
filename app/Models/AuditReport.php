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

    /*
    |--------------------------------------------------------------------------
    | Arsip dokumen
    |--------------------------------------------------------------------------
    */
    public function documentManagements()
    {
        return $this->hasMany(
            AuditReportFile::class
        )->where('status', 'lengkap');
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
}
