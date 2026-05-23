<?php

/**
 * Token Management Configuration
 * 
 * File ini mengatur semua setting untuk token management system
 */

return [

    // ============================================================
    // TOKEN QUOTA DEFAULT BERDASARKAN ROLE
    // ============================================================
    'quotas' => [
        'admin' => [
            'daily' => 50000,       // 50K tokens per hari
            'monthly' => 500000,    // 500K tokens per bulan
            'description' => 'Administrator dengan akses penuh'
        ],

        'pimpinan' => [
            'daily' => 30000,       // 30K tokens per hari
            'monthly' => 300000,    // 300K tokens per bulan
            'description' => 'Pimpinan/Manager'
        ],

        'auditor' => [
            'daily' => 10000,       // 10K tokens per hari
            'monthly' => 100000,    // 100K tokens per bulan
            'description' => 'Auditor staff'
        ],
    ],

    // ============================================================
    // TOKEN ESTIMATION SETTINGS
    // ============================================================
    'estimation' => [
        'words_per_token' => 1.3,      // Rata-rata kata per token
        'message_overhead' => 4,       // Overhead per message (tokens)
        'system_prompt_overhead' => 1.1,  // Multiplier untuk system prompt
        'json_overhead' => 10,         // Overhead untuk JSON formatting
    ],

    // ============================================================
    // GROQ API SETTINGS
    // ============================================================
    'groq' => [
        'max_tokens_per_request' => 1500,      // Max output tokens
        'max_input_tokens' => 128000,          // Max input tokens
        'estimated_output_percent' => 0.7,     // Output biasanya 70% dari max
    ],

    // ============================================================
    // ALERT & WARNING THRESHOLDS
    // ============================================================
    'alerts' => [
        'warning_threshold_percent' => 70,   // Warn at 70% usage
        'danger_threshold_percent' => 90,    // Danger at 90% usage
        'over_limit_threshold_percent' => 100,
    ],

    // ============================================================
    // PRICING (untuk reference/billing)
    // ============================================================
    'pricing' => [
        'mixtral-8x7b-32768' => [
            'input_per_1m_tokens' => 0.24,     // $0.24 per 1M tokens
            'output_per_1m_tokens' => 0.24,    // $0.24 per 1M tokens
        ],
        'llama-3-70b-versatile' => [
            'input_per_1m_tokens' => 0.59,
            'output_per_1m_tokens' => 0.79,
        ],
    ],

    // ============================================================
    // LOGGING & MONITORING
    // ============================================================
    'logging' => [
        'enabled' => true,
        'log_all_requests' => false,      // Log semua request (bisa berat)
        'log_errors_only' => true,        // Log hanya errors
        'retention_days' => 90,           // Simpan logs 90 hari
    ],

    // ============================================================
    // QUOTA RESET SETTINGS
    // ============================================================
    'reset' => [
        'daily_reset_time' => '00:00',   // Jam reset daily (format 24h)
        'monthly_reset_day' => 1,         // Hari reset monthly (1-31)
        'monthly_reset_time' => '00:00',
    ],

    // ============================================================
    // RATE LIMITING
    // ============================================================
    'rate_limiting' => [
        'enabled' => true,
        'max_requests_per_minute' => 10,   // Max 10 requests per menit per user
        'max_requests_per_hour' => 100,    // Max 100 requests per jam per user
    ],

    // ============================================================
    // CACHE SETTINGS
    // ============================================================
    'cache' => [
        'system_prompt_ttl' => 3600,       // Cache system prompt 1 jam
        'quota_check_ttl' => 60,           // Cache quota check 1 menit
        'user_stats_ttl' => 300,           // Cache user stats 5 menit
    ],
];
