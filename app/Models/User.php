<?php

namespace App\Models;

use App\Models\Client;
use App\Models\AuditReport;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi laporan audit
    public function auditReports()
    {
        return $this->belongsToMany(AuditReport::class);
    }

    // Relasi client auditor
    public function clients()
    {
        return $this->belongsToMany(
            Client::class,
            'client_auditors'
        )->withPivot('team_role')
            ->withTimestamps();
    }
}
