<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Logbook extends Model
{
    protected $fillable = [
        'user_id',
        'client_id',
        'team_role',
        'date',
        'audit_stage',
        'audit_area',
        'cumulative_progress',
        'main_task',
        'has_obstacle',
        'obstacle_type',
        'obstacle_description',
        'help_needed',
        'next_plan',
        'status',
        'note',
    ];

    /**
     * Cast tanggal
     */
    protected $casts = [
        'date' => 'date',
        'has_obstacle' => 'boolean',
    ];

    /**
     * Relasi user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
