<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class AuditReportFile extends Model
{
    protected $fillable = [

        'audit_report_id',

        'document_name',

        'document_type',

        'file',

        'version',

        'is_active',

        'uploaded_by',

        // approval
        'status',

        'revision_note',

        'approved_by',

        'approved_at',
    ];

    protected $casts = [

        'approved_at' => 'datetime',

        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIP
    |--------------------------------------------------------------------------
    */

    // relasi ke laporan audit
    public function auditReport()
    {
        return $this->belongsTo(AuditReport::class);
    }

    // relasi uploader
    public function uploader()
    {
        return $this->belongsTo(
            User::class,
            'uploaded_by'
        );
    }

    // relasi approver
    public function approvedBy()
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }

    public function review()
    {
        return $this->hasOne(DocumentReview::class, 'audit_file_id');
    }
}
