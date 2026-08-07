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
            \App\Models\AuditReportFile::class,
            \App\Models\AuditReport::class,
            'client_id',
            'audit_report_id',
            'id',
            'id'
        )->whereHas('review', function ($q) {
            $q->where('status', 'approved');
        })->whereHas('auditReport', function ($q) {
            $q->where('status', 'disetujui');
        });
    }

    /*
    | Status Helpers & Descriptions
    */
    public static function approvalStatusLabels(): array
    {
        return [
            'pending' => 'Pending',
            'menunggu_persetujuan' => 'Pending',
            'disetujui' => 'Disetujui',
            'revisi' => 'Perlu Revisi',
            'ditolak' => 'Ditolak',
        ];
    }

    public static function approvalStatusDescriptions(): array
    {
        return [
            'pending' => 'Belum disetujui pimpinan',
            'menunggu_persetujuan' => 'Belum disetujui pimpinan',
            'disetujui' => 'Sudah disetujui pimpinan',
            'revisi' => 'Ada catatan perbaikan dari pimpinan',
            'ditolak' => 'Pengajuan ditolak pimpinan',
        ];
    }

    public static function contractStatusLabels(): array
    {
        return [
            'aktif' => 'Kontrak Aktif',
            'hampir_habis' => 'Hampir Habis',
            'selesai' => 'Kontrak Selesai',
        ];
    }

    public static function contractStatusDescriptions(): array
    {
        return [
            'aktif' => 'Kontrak audit masih berlangsung',
            'hampir_habis' => 'Masa kontrak audit segera berakhir',
            'selesai' => 'Masa kontrak audit telah berakhir',
        ];
    }

    public function getApprovalStatusLabelAttribute(): string
    {
        return self::approvalStatusLabels()[$this->approval_status] ?? ucfirst($this->approval_status);
    }

    public function getApprovalStatusDescriptionAttribute(): string
    {
        return self::approvalStatusDescriptions()[$this->approval_status] ?? '';
    }

    public function getContractStatusLabelAttribute(): string
    {
        return self::contractStatusLabels()[$this->contract_status] ?? ucfirst($this->contract_status);
    }

    public function getContractStatusDescriptionAttribute(): string
    {
        return self::contractStatusDescriptions()[$this->contract_status] ?? '';
    }
}
