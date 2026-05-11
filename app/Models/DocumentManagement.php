<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentManagement extends Model
{
    protected $table = 'document_managements';

    protected $fillable = [
        'audit_report_id',
        'document_name',
        'document_type',
        'file',
        'archived_by',
        'archived_at'
    ];

    protected $casts = [
        'archived_at' => 'datetime'
    ];

    public function auditReport()
    {
        return $this->belongsTo(AuditReport::class);
    }

    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }
}
