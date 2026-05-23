<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TokenQuota extends Model
{
    protected $table = 'token_quotas';

    protected $fillable = [
        'user_id',
        'role',
        'daily_limit',
        'monthly_limit',
        'used_today',
        'used_this_month',
        'reset_date'
    ];

    protected $casts = [
        'reset_date' => 'date',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create quota for user dengan default values
     */
    public static function getOrCreateQuota($userId, $role = null): self
    {
        $quota = self::where('user_id', $userId)->first();

        if (!$quota) {
            $user = User::find($userId);
            $role = $role ?? $user?->role ?? 'auditor';

            $quota = self::create([
                'user_id' => $userId,
                'role' => $role,
                'daily_limit' => self::getDefaultDailyLimit($role),
                'monthly_limit' => self::getDefaultMonthlyLimit($role),
                'used_today' => 0,
                'used_this_month' => 0,
            ]);
        }

        return $quota;
    }

    /**
     * Get default daily limit based on role
     */
    public static function getDefaultDailyLimit(?string $role): int
    {
        return match ($role) {
            'admin' => 50000,      // Admin: sangat tinggi
            'pimpinan' => 30000,   // Pimpinan: tinggi
            'auditor' => 10000,    // Auditor: standard
            default => 10000,
        };
    }

    /**
     * Get default monthly limit based on role
     */
    public static function getDefaultMonthlyLimit(?string $role): int
    {
        return match ($role) {
            'admin' => 500000,     // Admin: 500K
            'pimpinan' => 300000,  // Pimpinan: 300K
            'auditor' => 100000,   // Auditor: 100K
            default => 100000,
        };
    }

    /**
     * Check if user dapat menggunakan tokens tertentu
     */
    public function canUseTokens($tokensNeeded): bool
    {
        $todayUsage = TokenUsage::getTodayUsage($this->user_id);
        $monthUsage = TokenUsage::getMonthUsage($this->user_id);

        return ($todayUsage + $tokensNeeded) <= $this->daily_limit
            && ($monthUsage + $tokensNeeded) <= $this->monthly_limit;
    }

    /**
     * Get remaining tokens untuk hari ini
     */
    public function getRemainingDaily(): int
    {
        $todayUsage = TokenUsage::getTodayUsage($this->user_id);
        return max(0, $this->daily_limit - $todayUsage);
    }

    /**
     * Get remaining tokens untuk bulan ini
     */
    public function getRemainingMonthly(): int
    {
        $monthUsage = TokenUsage::getMonthUsage($this->user_id);
        return max(0, $this->monthly_limit - $monthUsage);
    }

    /**
     * Get percentage of daily usage
     */
    public function getDailyUsagePercent(): float
    {
        $todayUsage = TokenUsage::getTodayUsage($this->user_id);
        return round(($todayUsage / $this->daily_limit) * 100, 2);
    }

    /**
     * Get percentage of monthly usage
     */
    public function getMonthlyUsagePercent(): float
    {
        $monthUsage = TokenUsage::getMonthUsage($this->user_id);
        return round(($monthUsage / $this->monthly_limit) * 100, 2);
    }

    /**
     * Add tokens to usage
     */
    public function addUsage($tokens): void
    {
        $this->used_today += $tokens;
        $this->used_this_month += $tokens;
        $this->save();
    }

    /**
     * Reset daily usage (biasanya dijalankan di cron job)
     */
    public function resetDaily(): void
    {
        $this->used_today = 0;
        $this->reset_date = now();
        $this->save();
    }

    /**
     * Reset monthly usage (biasanya di awal bulan)
     */
    public function resetMonthly(): void
    {
        $this->used_today = 0;
        $this->used_this_month = 0;
        $this->reset_date = now();
        $this->save();
    }

    /**
     * Get readable status
     */
    public function getStatus(): string
    {
        $dailyPercent = $this->getDailyUsagePercent();

        if ($dailyPercent >= 100) {
            return 'over_limit';
        } elseif ($dailyPercent >= 80) {
            return 'warning';
        } elseif ($dailyPercent >= 50) {
            return 'normal';
        } else {
            return 'healthy';
        }
    }
}
