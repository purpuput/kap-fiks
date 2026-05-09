<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    // System prompt sebagai AI Audit Assistant KAP
    private string $systemPrompt = <<<PROMPT
Kamu adalah AI Audit Assistant untuk Kantor Akuntan Publik (KAP). Kamu membantu auditor, pimpinan KAP, dan staf administrasi dalam proses audit dengan pengetahuan mendalam tentang:

1. **Standar Audit**: ISA (International Standards on Auditing), SPAP (Standar Profesional Akuntan Publik), SA (Standar Audit) yang berlaku di Indonesia
2. **Standar Akuntansi**: PSAK (Pernyataan Standar Akuntansi Keuangan), IFRS, SAK ETAP
3. **Prosedur Audit**: Perencanaan audit, prosedur substantif, uji pengendalian, sampling audit, dokumentasi audit
4. **Risiko Audit**: Identifikasi risiko material, inherent risk, control risk, detection risk
5. **Laporan Audit**: Opini audit (WTP, WDP, Disclaimer, Adverse), penyusunan laporan
6. **Kepatuhan**: Regulasi OJK, BPK, BPKP, PMK, peraturan perpajakan Indonesia
7. **Etika Profesi**: Kode etik akuntan publik, independensi, objektivitas

Panduan menjawab:
- Gunakan bahasa Indonesia yang formal namun mudah dipahami
- Berikan jawaban yang terstruktur dengan poin-poin jelas
- Jika menyebut standar, sebutkan nomor standar yang relevan
- Berikan contoh praktis jika diperlukan
- Jika pertanyaan di luar bidang audit/akuntansi, arahkan kembali ke topik audit
- Gunakan emoji yang relevan untuk memperjelas poin penting (✅ ⚠️ 📋 🔍 dll)
PROMPT;

    /**
     * Tampilkan halaman chatbot
     */
    public function index()
    {
        return view('chatbot.index');
    }

    /**
     * Kirim pesan ke Groq API dan kembalikan respons
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message'  => 'required|string|max:2000',
            'history'  => 'nullable|array',
            'history.*.role'    => 'required|in:user,assistant',
            'history.*.content' => 'required|string',
        ]);

        $apiKey  = config('services.groq.api_key');
        $baseUrl = config('services.groq.base_url');
        $model   = config('services.groq.model');

        if (empty($apiKey) || $apiKey === 'your_groq_api_key_here') {
            return response()->json([
                'error' => 'GROQ_API_KEY belum dikonfigurasi. Silakan set di file .env'
            ], 500);
        }

        // Bangun riwayat percakapan
        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt],
        ];

        // Tambahkan history dari frontend (max 10 pesan terakhir untuk efisiensi token)
        if ($request->has('history') && is_array($request->history)) {
            $history = array_slice($request->history, -10);
            foreach ($history as $msg) {
                $messages[] = [
                    'role'    => $msg['role'],
                    'content' => $msg['content'],
                ];
            }
        }

        // Tambahkan pesan terbaru user
        $messages[] = [
            'role'    => 'user',
            'content' => $request->message,
        ];

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post("{$baseUrl}/chat/completions", [
                    'model'       => $model,
                    'messages'    => $messages,
                    'max_tokens'  => 1024,
                    'temperature' => 0.7,
                    'stream'      => false,
                ]);

            if ($response->failed()) {
                $errorBody = $response->json();
                $errorMsg  = $errorBody['error']['message'] ?? 'Gagal menghubungi Groq API.';
                return response()->json(['error' => $errorMsg], $response->status());
            }

            $data  = $response->json();
            $reply = $data['choices'][0]['message']['content'] ?? 'Maaf, tidak ada respons dari AI.';

            return response()->json([
                'reply' => $reply,
                'model' => $model,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset riwayat percakapan (session-based, handled by frontend)
     */
    public function clearHistory(Request $request)
    {
        return response()->json(['status' => 'cleared']);
    }
}
