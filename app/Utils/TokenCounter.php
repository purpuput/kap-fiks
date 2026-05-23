<?php

namespace App\Utils;

/**
 * Helper class untuk menghitung dan mengestimasi token usage
 * Token adalah unit terkecil yang diproses oleh API Groq
 * 
 * Estimasi kasar: 1 kata ≈ 1.3 tokens
 * Untuk estimasi lebih akurat, gunakan tokenizer dari OpenAI
 */
class TokenCounter
{
    /**
     * Estimasi tokens dari text (sederhana)
     * Metode: count words × 1.3
     */
    public static function estimate(string $text): int
    {
        if (empty(trim($text))) {
            return 0;
        }

        // Split text menjadi words
        $words = preg_split('/\s+/', trim($text));
        $wordCount = count(array_filter($words)); // Filter empty strings

        // Estimasi: 1 kata ≈ 1.3 tokens
        // Kurangi sedikit untuk JSON formatting
        $estimate = (int) ceil($wordCount * 1.3);

        return max(1, $estimate); // Minimal 1 token
    }

    /**
     * Estimasi tokens dari array of messages
     * Format messages: [['role' => 'user', 'content' => '...'], ...]
     */
    public static function estimateMessages(array $messages): int
    {
        $total = 0;

        foreach ($messages as $msg) {
            if (!isset($msg['content'])) {
                continue;
            }

            // Hitung tokens untuk content
            $total += self::estimate($msg['content']);

            // Overhead untuk role dan formatting (~4 tokens per message)
            $total += 4;
        }

        // Overhead untuk struktur JSON (~10 tokens)
        $total += 10;

        return max(1, $total);
    }

    /**
     * Estimasi tokens untuk system prompt
     */
    public static function estimateSystemPrompt(string $prompt): int
    {
        $tokens = self::estimate($prompt);

        // System prompt biasanya lebih "expensive"
        // Tambah 10% untuk overhead
        return (int) ceil($tokens * 1.1);
    }

    /**
     * Hitung estimasi untuk full request (system + messages)
     */
    public static function estimateFullRequest(string $systemPrompt, array $messages): int
    {
        $systemTokens = self::estimateSystemPrompt($systemPrompt);
        $messagesTokens = self::estimateMessages($messages);

        return $systemTokens + $messagesTokens;
    }

    /**
     * Estimasi output tokens berdasarkan max_tokens yang ditetapkan
     * (Groq biasanya return kurang dari max_tokens)
     */
    public static function estimateOutput(int $maxTokens): int
    {
        // Rata-rata output sekitar 60-80% dari max_tokens
        return (int) ceil($maxTokens * 0.7);
    }

    /**
     * Get human-readable token info
     */
    public static function getReadableInfo(int $tokens): string
    {
        $words = (int) ($tokens / 1.3);
        $chars = $words * 5; // Rata-rata 5 char per kata

        if ($words > 1000) {
            return number_format($words) . " words (~" . number_format($tokens) . " tokens)";
        }

        return "$words words (~$tokens tokens)";
    }

    /**
     * Validasi bahwa message tidak terlalu besar
     */
    public static function validateMessageSize(string $message, int $maxTokens = 2000): bool
    {
        $tokens = self::estimate($message);
        return $tokens <= $maxTokens;
    }

    /**
     * Get validation error jika message terlalu besar
     */
    public static function getValidationError(string $message, int $maxTokens = 2000): ?string
    {
        $tokens = self::estimate($message);

        if ($tokens > $maxTokens) {
            return "Pesan terlalu panjang! ({$tokens} tokens, max: {$maxTokens} tokens)";
        }

        return null;
    }

    /**
     * Estimate tokens untuk JSON data yang sudah di-encode
     */
    public static function estimateJsonData(array $data): int
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        return self::estimate($json);
    }

    /**
     * Get token price estimation (untuk reference)
     * Berdasarkan Groq pricing (bisa berbeda per plan)
     */
    public static function estimatePrice(int $inputTokens, int $outputTokens, string $model = 'mixtral-8x7b-32768'): float
    {
        // Groq pricing example (per 1M tokens)
        $pricing = [
            'mixtral-8x7b-32768' => [
                'input' => 0.24,   // $0.24 per 1M input tokens
                'output' => 0.24,  // $0.24 per 1M output tokens
            ],
            'llama-3-70b-versatile' => [
                'input' => 0.59,
                'output' => 0.79,
            ],
        ];

        $rates = $pricing[$model] ?? $pricing['mixtral-8x7b-32768'];

        $inputCost = ($inputTokens / 1000000) * $rates['input'];
        $outputCost = ($outputTokens / 1000000) * $rates['output'];

        return round($inputCost + $outputCost, 6);
    }

    /**
     * Get formatted price string
     */
    public static function formatPrice(int $inputTokens, int $outputTokens, string $model = 'mixtral-8x7b-32768'): string
    {
        $price = self::estimatePrice($inputTokens, $outputTokens, $model);

        if ($price < 0.001) {
            return "< $0.001";
        }

        return "$" . number_format($price, 6);
    }

    /**
     * Debug: Breakdown token usage
     */
    public static function getDebugBreakdown(string $systemPrompt, array $messages, int $maxTokens = 1500): array
    {
        return [
            'system_prompt_tokens' => self::estimateSystemPrompt($systemPrompt),
            'messages_tokens' => self::estimateMessages($messages),
            'estimated_input_total' => self::estimateFullRequest($systemPrompt, $messages),
            'estimated_output_tokens' => self::estimateOutput($maxTokens),
            'estimated_total_tokens' => self::estimateFullRequest($systemPrompt, $messages) + self::estimateOutput($maxTokens),
            'readable_input' => self::getReadableInfo(self::estimateFullRequest($systemPrompt, $messages)),
            'readable_output' => self::getReadableInfo(self::estimateOutput($maxTokens)),
        ];
    }
}
