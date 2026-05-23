<?php

/**
 * IMPLEMENTASI INTEGRASI TOKEN MANAGEMENT KE CHATBOTCONTROLLER
 * 
 * File ini menunjukkan cara mengintegrasikan token checking dan tracking
 * ke dalam ChatbotController::chat() method
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\TokenQuota;
use App\Models\TokenUsage;
use App\Utils\TokenCounter;

class ChatbotControllerWithTokenManagement extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message'    => 'required|string|max:2000',
            'session_id' => 'nullable|exists:chat_sessions,id',
            'history'    => 'nullable|array',
        ]);

        $user = Auth::user();

        // ============================================================
        // STEP 1: VALIDASI TOKEN QUOTA SEBELUM PROCESSING
        // ============================================================

        $quota = TokenQuota::getOrCreateQuota($user->id, $user->role);

        // Estimasi tokens yang akan digunakan
        $estimatedInputTokens = TokenCounter::estimate($request->message);
        $maxTokens = (int) (config('tokens.groq.max_tokens_per_request') ?? 1500);
        $estimatedOutputTokens = TokenCounter::estimateOutput($maxTokens);
        $estimatedTotalTokens = $estimatedInputTokens + $estimatedOutputTokens;

        // Cek apakah masih punya quota
        if (!$quota->canUseTokens($estimatedTotalTokens)) {
            $todayUsage = TokenUsage::getTodayUsage($user->id);
            $monthUsage = TokenUsage::getMonthUsage($user->id);

            return response()->json([
                'error' => 'Quota token telah habis!',
                'details' => [
                    'daily_limit' => $quota->daily_limit,
                    'daily_used' => $todayUsage,
                    'daily_remaining' => max(0, $quota->daily_limit - $todayUsage),
                    'monthly_limit' => $quota->monthly_limit,
                    'monthly_used' => $monthUsage,
                    'monthly_remaining' => max(0, $quota->monthly_limit - $monthUsage),
                    'tokens_requested' => $estimatedTotalTokens,
                    'reset_time' => $quota->reset_date ?? 'tomorrow at 00:00',
                ]
            ], 429); // 429 Too Many Requests
        }

        // ============================================================
        // STEP 2: SETUP SESSION (kode existing)
        // ============================================================

        $session_id = $request->session_id;
        if (!$session_id) {
            $words = explode(' ', trim($request->message));
            $title = implode(' ', array_slice($words, 0, 5)) . (count($words) > 5 ? '...' : '');

            $session = ChatSession::create([
                'user_id' => $user->id,
                'title' => $title ?: 'Percakapan Baru'
            ]);
            $session_id = $session->id;
        } else {
            $session = ChatSession::where('id', $session_id)
                ->where('user_id', $user->id)
                ->first();
            if (!$session) {
                return response()->json(['error' => 'Sesi tidak ditemukan.'], 404);
            }
            $session->touch();
        }

        // ============================================================
        // STEP 3: LOAD DATA SISTEM (kode existing)
        // ============================================================

        try {
            // ... kode existing untuk load data ...
            // (AuditReport, Client, Logbook, dll)

            $totalReports = 0; // Placeholder
            $systemPrompt = "Anda adalah AI Assistant KAP..."; // Placeholder

        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat data sistem: ' . $e->getMessage()], 500);
        }

        // ============================================================
        // STEP 4: BANGUN MESSAGE ARRAY & ESTIMATE TOKENS LEBIH AKURAT
        // ============================================================

        $apiMessages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Ambil history
        if ($request->has('history') && is_array($request->history)) {
            $history = array_slice($request->history, -10);
            foreach ($history as $msg) {
                $apiMessages[] = [
                    'role' => $msg['role'] ?? 'user',
                    'content' => $msg['content'] ?? '',
                ];
            }
        }

        $apiMessages[] = [
            'role' => 'user',
            'content' => $request->message,
        ];

        // Re-estimate dengan message yang sebenarnya
        $actualEstimatedInputTokens = TokenCounter::estimateFullRequest($systemPrompt, $apiMessages);
        $actualEstimatedTotalTokens = $actualEstimatedInputTokens + $estimatedOutputTokens;

        // Double-check quota dengan estimasi yang lebih akurat
        if (!$quota->canUseTokens($actualEstimatedTotalTokens)) {
            return response()->json([
                'error' => 'Quota token tidak cukup untuk request ini',
                'details' => [
                    'estimated_tokens' => $actualEstimatedTotalTokens,
                    'remaining_daily' => $quota->getRemainingDaily(),
                    'remaining_monthly' => $quota->getRemainingMonthly(),
                ]
            ], 429);
        }

        // ============================================================
        // STEP 5: LOG TOKEN ESTIMATION (untuk debugging)
        // ============================================================

        \Log::debug('Token Estimation', [
            'user_id' => $user->id,
            'session_id' => $session_id,
            'estimated_input_tokens' => $actualEstimatedInputTokens,
            'estimated_output_tokens' => $estimatedOutputTokens,
            'estimated_total' => $actualEstimatedTotalTokens,
            'breakdown' => TokenCounter::getDebugBreakdown($systemPrompt, $apiMessages, $maxTokens),
        ]);

        // ============================================================
        // STEP 6: KIRIM KE GROQ API
        // ============================================================

        try {
            if (empty($apiMessages) || count($apiMessages) < 2) {
                return response()->json(['error' => 'Pesan tidak valid.'], 400);
            }

            $apiKey = config('services.groq.api_key');
            $baseUrl = config('services.groq.base_url');
            $model = config('services.groq.model');

            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post("{$baseUrl}/chat/completions", [
                    'model' => $model,
                    'messages' => $apiMessages,
                    'max_tokens' => (int) $maxTokens,
                    'temperature' => 0.7,
                    'stream' => false,
                ]);

            if ($response->failed()) {
                $errorBody = $response->json();
                $errorMsg = $errorBody['error']['message'] ?? 'API Error';
                \Log::error('Groq API Error', ['status' => $response->status(), 'error' => $errorMsg]);
                return response()->json(['error' => $errorMsg], $response->status());
            }

            $data = $response->json();
            if (!isset($data['choices']) || empty($data['choices'])) {
                return response()->json(['error' => 'Respons dari AI tidak valid.'], 500);
            }

            $reply = $data['choices'][0]['message']['content'];

            // ============================================================
            // STEP 7: CATAT TOKEN USAGE YANG SEBENARNYA
            // ============================================================

            // Groq API mengembalikan actual token usage
            $actualInputTokens = $data['usage']['prompt_tokens'] ?? $actualEstimatedInputTokens;
            $actualOutputTokens = $data['usage']['completion_tokens'] ?? $estimatedOutputTokens;
            $actualTotalTokens = $actualInputTokens + $actualOutputTokens;

            // Simpan pesan terlebih dahulu
            $chatMessage = ChatMessage::create([
                'chat_session_id' => $session_id,
                'message' => $request->message,
                'reply' => $reply,
            ]);

            // Simpan token usage
            TokenUsage::create([
                'user_id' => $user->id,
                'chat_message_id' => $chatMessage->id,
                'input_tokens' => $actualInputTokens,
                'output_tokens' => $actualOutputTokens,
                'total_tokens' => $actualTotalTokens,
                'model' => $model,
                'used_at' => now(),
                'date' => today(),
            ]);

            // Update quota counters
            $quota->addUsage($actualTotalTokens);

            // ============================================================
            // STEP 8: LOG SUCCESS & RETURN RESPONSE
            // ============================================================

            \Log::info('Chatbot Success', [
                'user_id' => $user->id,
                'session_id' => $session_id,
                'input_tokens' => $actualInputTokens,
                'output_tokens' => $actualOutputTokens,
                'total_tokens' => $actualTotalTokens,
                'remaining_daily' => $quota->getRemainingDaily(),
            ]);

            return response()->json([
                'reply' => $reply,
                'model' => $model,
                'session_id' => $session_id,
                'title' => $session->title,
                'token_usage' => [
                    'input_tokens' => $actualInputTokens,
                    'output_tokens' => $actualOutputTokens,
                    'total_tokens' => $actualTotalTokens,
                    'daily_remaining' => $quota->getRemainingDaily(),
                    'monthly_remaining' => $quota->getRemainingMonthly(),
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Chatbot Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => $user->id
            ]);

            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
