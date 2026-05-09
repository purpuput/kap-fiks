<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Logbook extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'activity',
        'status',
        'note',
    ];

    /**
     * Cast tanggal
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Relasi user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
