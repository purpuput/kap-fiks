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
        'created_by'
    ];

    protected $casts = [
        'audit_date' => 'date',
    ];

    /**
     * Relasi ke client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relasi pembuat laporan
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi auditor
     */
    public function auditors()
    {
        return $this->belongsToMany(User::class);
    }

    public function files()
    {
        return $this->hasMany(AuditReportFile::class);
    }

    public function documents()
    {
        return $this->hasMany(DocumentManagement::class);
    }
}
