<?php

namespace App\Models;

use App\Models\User;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TokenUsage extends Model
{
    protected $table = 'token_usage';

    protected $fillable = [
        'user_id',
        'chat_message_id',
        'input_tokens',
        'output_tokens',
        'total_tokens',
        'model',
        'used_at',
        'date'
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'date' => 'date',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke ChatMessage
     */
    public function chatMessage(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'chat_message_id');
    }

    /**
     * Get today's total token usage for a user
     */
    public static function getTodayUsage(int $userId): int
    {
        return self::where('user_id', $userId)
            ->whereDate('date', today())
            ->sum('total_tokens') ?? 0;
    }

    /**
     * Get this month's total token usage for a user
     */
    public static function getMonthUsage(int $userId): int
    {
        return self::where('user_id', $userId)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('total_tokens') ?? 0;
    }

    /**
     * Get usage by role for today
     */
    public static function getTodayUsageByRole(string $role)
    {
        return self::whereHas('user', function ($query) use ($role) {
            $query->where('role', $role);
        })
            ->whereDate('date', today())
            ->selectRaw('user_id, SUM(total_tokens) as total')
            ->groupBy('user_id')
            ->with('user')
            ->get();
    }

    /**
     * Get average tokens per request
     */
    public static function getAverageTokens(int $userId): float
    {
        $avg = self::where('user_id', $userId)->avg('total_tokens');
        return round($avg ?? 0, 2);
    }
}
