<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = ['name', 'company_name', 'email', 'phone', 'address', 'city', 'province', 'postal_code', 'description', 'status'];

    public function logbooks(): HasMany
    {
        return $this->hasMany(Logbook::class);
    }

    public function auditReports(): HasMany
    {
        return $this->hasMany(AuditReport::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
