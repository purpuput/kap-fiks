<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'institution_name',
        'leader_name',
        'address',
        'city',
        'province',
        'postal_code',
        'phone',
        'email',
        'npwp',
        'status',
        'approval_status',
        'approved_by',
        'approved_at',
        'revision_note',
        'description',
    ];

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditReports()
    {
        return $this->hasMany(AuditReport::class);
    }

    public function documentManagements()
    {
        return $this->hasManyThrough(
            \App\Models\DocumentManagement::class,
            \App\Models\AuditReport::class,
            'client_id',      // foreign key di audit_reports
            'audit_report_id', // foreign key di document_managements
            'id',
            'id'
        );
    }
}
