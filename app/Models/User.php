<?php

namespace App\Models;

use App\Models\Client;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [

        'name',

        'email',

        'password',

        'role',
    ];

    protected $hidden = [

        'password',

        'remember_token',
    ];

    protected function casts(): array
    {
        return [

            'email_verified_at' => 'datetime',

            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi client auditor
    |--------------------------------------------------------------------------
    */
    public function clients()
    {
        return $this->belongsToMany(

            Client::class,

            'client_auditors'
        )
            ->withPivot('team_role')
            ->withTimestamps();
    }

    /*
    |--------------------------------------------------------------------------
    | Laporan audit
    |--------------------------------------------------------------------------
    */
    public function auditReports()
    {
        return $this->hasMany(

            AuditReport::class,

            'created_by'
        );
    }
}
