<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatSession;
use App\Models\ChatMessage;

class ChatbotController extends Controller
{
    /**
     * Tampilkan halaman chatbot
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Ambil semua sesi obrolan milik user ini, urutkan dari yang terbaru
        $sessions = ChatSession::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->get();
            
        // Jika ada parameter session_id, muat pesan dari sesi tersebut
        $currentSession = null;
        $messages = [];
        
        if ($request->has('session_id')) {
            $currentSession = ChatSession::where('id', $request->session_id)
                ->where('user_id', $user->id)
                ->firstOrFail();
                
            $messages = $currentSession->messages()->orderBy('created_at', 'asc')->get();
        }

        return view('chatbot.index', compact('sessions', 'currentSession', 'messages'));
    }

    /**
     * Kirim pesan ke Groq API dan kembalikan respons
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message'    => 'required|string|max:2000',
            'session_id' => 'nullable|exists:chat_sessions,id',
            'history'    => 'nullable|array',
        ]);

        $user = Auth::user();
        $apiKey  = config('services.groq.api_key');
        $baseUrl = config('services.groq.base_url');
        $model   = config('services.groq.model');
        
        // --- 1. TOKEN MANAGEMENT ---
        // Jika admin minta aturkan token, kita bisa batasi input/output token di sini.
        // Groq API memiliki limit max_tokens untuk output. Kita set 2048 agar jawaban bisa komprehensif.
        $maxTokens = 2048; 

        if (empty($apiKey) || $apiKey === 'your_groq_api_key_here') {
            return response()->json([
                'error' => 'GROQ_API_KEY belum dikonfigurasi. Silakan set di file .env'
            ], 500);
        }

        // --- 2. PENYIAPAN SESI ---
        $session_id = $request->session_id;
        if (!$session_id) {
            // Generate title dari pesan pertama (max 5 kata)
            $words = explode(' ', trim($request->message));
            $title = implode(' ', array_slice($words, 0, 5)) . (count($words) > 5 ? '...' : '');
            
            $session = ChatSession::create([
                'user_id' => $user->id,
                'title'   => $title ?: 'Percakapan Baru'
            ]);
            $session_id = $session->id;
        } else {
            $session = ChatSession::where('id', $session_id)->where('user_id', $user->id)->first();
            if (!$session) {
                return response()->json(['error' => 'Sesi tidak ditemukan.'], 404);
            }
            // Update updated_at
            $session->touch();
        }

        // --- 3. ANALISIS DATA SISTEM (Konteks AI) ---
        // Kita suntikkan data-data ringkasan ke dalam system prompt agar AI "tahu" konteks sistem saat ini.
        $totalReports = \App\Models\AuditReport::count();
        $pendingReports = \App\Models\AuditReport::where('status', 'pending')->count();
        $totalClients = \App\Models\Client::count();
        
        $clientsData = \App\Models\Client::get()->toArray();
        $reportsData = \App\Models\AuditReport::with('client:id,institution_name')->get()->toArray();
        $usersData = \App\Models\User::get()->toArray();
        $logbooksData = \App\Models\Logbook::with('user:id,name')->orderBy('date', 'desc')->take(50)->get()->toArray();
        $documentsData = \App\Models\Document::with('user:id,name')->get()->toArray();
        $documentManagementsData = \App\Models\DocumentManagement::with('auditReport:id,report_number')->get()->toArray();
        
        $clientsJson = json_encode($clientsData);
        $reportsJson = json_encode($reportsData);
        $usersJson = json_encode($usersData);
        $logbooksJson = json_encode($logbooksData);
        $documentsJson = json_encode($documentsData);
        $documentManagementsJson = json_encode($documentManagementsData);
        
        // System prompt sebagai AI Audit Assistant KAP
        $systemPrompt = <<<PROMPT
Kamu adalah AI Audit Assistant untuk Kantor Akuntan Publik (KAP). Kamu membantu auditor, pimpinan KAP, dan staf administrasi dalam proses audit dengan pengetahuan mendalam.

Konteks Sistem Saat Ini:
- Pengguna yang sedang bertanya: {$user->name} (Role: {$user->role})
- Total Laporan Audit di sistem: {$totalReports} (Pending: {$pendingReports})
- Total Klien KAP: {$totalClients}

Berikut adalah data lengkap yang ada di sistem (dalam format JSON) agar kamu bisa menjawab pertanyaan secara spesifik:

Data Klien:
{$clientsJson}

Data Laporan Audit:
{$reportsJson}

Data Pengguna (Auditor/Pimpinan/Admin):
{$usersJson}

Data Logbook (Aktivitas Terbaru):
{$logbooksJson}

Data Dokumen Umum (termasuk Dokumen Invoice dll):
{$documentsJson}

Data Manajemen Dokumen (Arsip Laporan):
{$documentManagementsJson}

Kamu dapat menggunakan data di atas jika pengguna bertanya tentang data klien, status laporan, nama pengguna, aktivitas logbook, manajemen dokumen, atau invoice. Analisis data tersebut untuk memberikan jawaban yang akurat, detail, dan sesuai dengan pertanyaan. Jika ditanya mengenai "semua data", berikan ringkasan yang komprehensif berdasarkan data JSON di atas. Jika ditanya mengenai "invoice" atau dokumen lainnya, silakan periksa di bagian Data Dokumen Umum atau Data Manajemen Dokumen.

Panduan menjawab:
- Gunakan bahasa Indonesia yang formal namun ramah dan mudah dipahami
- Berikan jawaban terstruktur (gunakan poin-poin atau tabel jika menjelaskan banyak data)
- Gunakan markdown formatting
PROMPT;

        // Bangun riwayat percakapan untuk API
        $apiMessages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Tambahkan history dari frontend atau database
        if ($request->has('history') && is_array($request->history)) {
            $history = array_slice($request->history, -10); // Ambil 10 terakhir agar token tidak bengkak
            foreach ($history as $msg) {
                $apiMessages[] = [
                    'role'    => $msg['role'],
                    'content' => $msg['content'],
                ];
            }
        } else {
            // Ambil dari DB jika tidak dipass dari frontend
            $dbHistory = $session->messages()->orderBy('created_at', 'asc')->take(10)->get();
            foreach ($dbHistory as $msg) {
                $apiMessages[] = ['role' => 'user', 'content' => $msg->message];
                $apiMessages[] = ['role' => 'assistant', 'content' => $msg->reply];
            }
        }

        // Tambahkan pesan terbaru
        $apiMessages[] = [
            'role'    => 'user',
            'content' => $request->message,
        ];

        // --- 4. KIRIM KE GROQ API ---
        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post("{$baseUrl}/chat/completions", [
                    'model'       => $model,
                    'messages'    => $apiMessages,
                    'max_tokens'  => $maxTokens,
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

            // --- 5. SIMPAN KE DATABASE ---
            ChatMessage::create([
                'chat_session_id' => $session_id,
                'message'         => $request->message,
                'reply'           => $reply,
            ]);

            return response()->json([
                'reply'      => $reply,
                'model'      => $model,
                'session_id' => $session_id,
                'title'      => $session->title
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus satu sesi percakapan
     */
    public function destroySession($id)
    {
        $session = ChatSession::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $session->delete();
        
        return redirect()->route('chatbot.index')->with('success', 'Percakapan berhasil dihapus.');
    }

    /**
     * Hapus semua riwayat percakapan user
     */
    public function clearHistory(Request $request)
    {
        ChatSession::where('user_id', Auth::id())->delete();
        return response()->json(['status' => 'cleared']);
    }
}
