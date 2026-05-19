<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'created_by',
        'institution_type',
        'audit_type',
        'institution_name',
        'leader_name',
        'address',
        'city',
        'province',
        'postal_code',
        'phone',
        'email',
        'npwp',
        'contract_start',
        'contract_end',
        'contract_status',
        'status',
        'approval_status',
        'approved_by',
        'approved_at',
        'revision_note',
        'description',
    ];

    protected $casts = [
        'contract_start' => 'date',
        'contract_end' => 'date',
        'approved_at' => 'datetime',
    ];

    /*
    | Admin pembuat client
    */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    | Pimpinan approval
    */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /*
    | Tim auditor
    */
    public function auditors()
    {
        return $this->belongsToMany(User::class, 'client_auditors')
            ->withPivot('team_role')
            ->withTimestamps();
    }

    /*
    | Laporan audit
    */
    public function auditReports()
    {
        return $this->hasMany(AuditReport::class);
    }

    /*
    | Dokumen management
    */
    public function documentManagements()
    {
        return $this->hasManyThrough(
            \App\Models\DocumentManagement::class,
            \App\Models\AuditReport::class,
            'client_id',
            'audit_report_id',
            'id',
            'id'
        );
    }
}
