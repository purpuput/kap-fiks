<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentReview extends Model
{
    protected $fillable = [
        'audit_report_id',
        'audit_file_id',
        'reviewer_id',
        'status',
        'note',
        'reviewed_at',
        'is_viewed',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'is_viewed' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function auditReport()
    {
        return $this->belongsTo(AuditReport::class);
    }

    public function auditFile()
    {
        return $this->belongsTo(AuditReportFile::class, 'audit_file_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
