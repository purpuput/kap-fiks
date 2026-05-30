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

        // --- 1. TOKEN MANAGEMENT (OPTIMIZED) ---
        // Groq API memiliki token limit. Atur untuk keseimbangan antara context dan response quality
        $maxTokens = 1500; // Tingkat optimal untuk response yang berkualitas dengan context yang baik

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

        // --- 3. ANALISIS DATA SISTEM (Konteks AI) - ENHANCED ---
        // Ambil data ringkasan dan detail untuk mendukung berbagai tipe query
        try {
            // === STATISTIK UMUM ===
            $totalReports = \App\Models\AuditReport::query()->count('*');
            $pendingReports = \App\Models\AuditReport::query()->where('status', 'pending')->count('*');
            $completedReports = \App\Models\AuditReport::query()->where('status', 'completed')->count('*');
            $inProgressReports = \App\Models\AuditReport::query()->where('status', 'in_progress')->count('*');
            $totalClients = \App\Models\Client::query()->count('*');
            $totalUsers = \App\Models\User::query()->count('*');

            // === LAPORAN AUDIT ===
            // Data lengkap dengan detail yang lebih komprehensif
            $auditReports = \App\Models\AuditReport::query()->select(['id', 'report_number', 'title', 'audit_type', 'status', 'audit_date', 'created_by'])
                ->with('client:id,institution_name')
                ->with('creator:id,name')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function ($report) {
                    return [
                        'id' => $report->id,
                        'nomor_laporan' => $report->report_number,
                        'judul' => $report->title,
                        'tipe_audit' => $report->audit_type,
                        'status' => $report->status,
                        'tanggal_audit' => $report->audit_date,
                        'klien' => $report->client?->institution_name ?? 'N/A',
                        'dibuat_oleh' => $report->creator?->name ?? 'N/A'
                    ];
                })
                ->toArray() ?? [];

            // === KLIEN ===
            $allClients = \App\Models\Client::query()->select(['id', 'institution_name', 'institution_type', 'leader_name', 'address', 'city', 'phone', 'status'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get()
                ->map(function ($client) {
                    return [
                        'id' => $client->id,
                        'nama_institusi' => $client->institution_name,
                        'jenis_institusi' => $client->institution_type,
                        'pimpinan' => $client->leader_name,
                        'alamat' => $client->address,
                        'kota' => $client->city,
                        'telepon' => $client->phone,
                        'status' => $client->status
                    ];
                })
                ->toArray() ?? [];

            // === LOGBOOK & KEGIATAN ===
            $recentLogbooks = \App\Models\Logbook::query()->select(['id', 'main_task', 'date', 'user_id', 'has_obstacle', 'obstacle_type'])
                ->with('user:id,name')
                ->orderBy('date', 'desc')
                ->take(15)
                ->get()
                ->map(function ($logbook) {
                    return [
                        'id' => $logbook->id,
                        'kegiatan_utama' => $logbook->main_task,
                        'tanggal' => $logbook->date,
                        'karyawan' => $logbook->user?->name ?? 'N/A',
                        'ada_kendala' => $logbook->has_obstacle ? 'Ya' : 'Tidak',
                        'tipe_kendala' => $logbook->obstacle_type ?? '-'
                    ];
                })
                ->toArray() ?? [];

            // === ABSENSI ===
            $todayAttendances = \App\Models\Attendance::query()->with('user:id,name')
                ->whereDate('date', today())
                ->get(['id', 'user_id', 'check_in', 'check_out', 'status'])
                ->map(function ($att) {
                    return [
                        'karyawan' => $att->user?->name ?? 'N/A',
                        'jam_masuk' => $att->check_in,
                        'jam_keluar' => $att->check_out,
                        'status' => $att->status
                    ];
                })
                ->toArray() ?? [];

            // === KENDALA YANG MEMBUTUHKAN SOLUSI ===
            $obstacleLogbooks = \App\Models\Logbook::query()->with('user:id,name')
                ->where('has_obstacle', 1)
                ->orderBy('date', 'desc')
                ->take(8)
                ->get(['id', 'user_id', 'main_task', 'obstacle_type', 'obstacle_description', 'help_needed', 'date'])
                ->map(function ($logbook) {
                    return [
                        'tanggal' => $logbook->date,
                        'karyawan' => $logbook->user?->name ?? 'N/A',
                        'kegiatan' => $logbook->main_task,
                        'jenis_kendala' => $logbook->obstacle_type,
                        'deskripsi' => $logbook->obstacle_description,
                        'butuh_bantuan' => $logbook->help_needed ? 'Ya' : 'Tidak'
                    ];
                })
                ->toArray() ?? [];

            // === PENGGUNA & PERAN ===
            $teamMembers = \App\Models\User::query()->select(['id', 'name', 'email', 'role'])
                ->orderBy('name', 'asc')
                ->take(20)
                ->get()
                ->toArray();

            // === LAPORAN AUDIT BERDASARKAN STATUS ===
            $reportsByStatus = [
                'pending' => $pendingReports,
                'in_progress' => $inProgressReports,
                'completed' => $completedReports
            ];

            // === JSON ENCODE SEMUA DATA ===
            $reportsJson = json_encode($auditReports, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $clientsJson = json_encode($allClients, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $logbooksJson = json_encode($recentLogbooks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $attendancesJson = json_encode($todayAttendances, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $obstaclesJson = json_encode($obstacleLogbooks, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $teamJson = json_encode($teamMembers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $statusJson = json_encode($reportsByStatus, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memuat data sistem: ' . $e->getMessage()], 500);
        }

        // System prompt sebagai AURA AI (Audit Reporting Asisten) KAP (ENHANCED & COMPREHENSIVE)
        $systemPrompt = <<<PROMPT
🤖 **AURA AI (Audit Reporting Asisten) - KANTOR AKUNTAN PUBLIK (KAP)**

Anda adalah AURA AI (Audit Reporting Asisten), asisten cerdas yang membantu tim audit KAP dalam mengelola proses audit, logbook kegiatan, absensi, dan komunikasi dengan klien. Anda memiliki akses ke database sistem yang mencakup semua laporan audit, data klien, kegiatan tim, dan kendala yang dihadapi.

═══════════════════════════════════════════════════════════════════════════════════
📊 INFORMASI PENGGUNA & KONTEKS
═══════════════════════════════════════════════════════════════════════════════════
- Pengguna: {$user->name}
- Role: {$user->role} (auditor | pimpinan | admin)
- Total Laporan Audit: {$totalReports} (Pending: {$pendingReports}, In Progress: {$inProgressReports}, Selesai: {$completedReports})
- Total Klien: {$totalClients}
- Total Anggota Tim: {$totalUsers}

═══════════════════════════════════════════════════════════════════════════════════
📋 DATA YANG TERSEDIA DALAM SISTEM
═══════════════════════════════════════════════════════════════════════════════════

1️⃣ **LAPORAN AUDIT** (10 Laporan Terbaru):
{$reportsJson}

Data laporan audit mencakup: nomor laporan, judul, tipe audit, status (pending/in_progress/completed), tanggal audit, klien terkait, dan pembuat laporan.

2️⃣ **DAFTAR KLIEN** (10 Klien):
{$clientsJson}

Informasi klien mencakup: nama institusi, jenis institusi (pemerintahan/swasta), nama pimpinan, alamat, kota, telepon, dan status (aktif/nonaktif).

3️⃣ **KEGIATAN & LOGBOOK** (15 Aktivitas Terbaru):
{$logbooksJson}

Logbook mencatat kegiatan harian setiap auditor termasuk: deskripsi kegiatan, tanggal, nama auditor, dan ada tidaknya kendala.

4️⃣ **ABSENSI HARI INI**:
{$attendancesJson}

Melacak kehadiran tim dengan: jam masuk, jam keluar, dan status (hadir/tidak hadir/izin/sakit).

5️⃣ **KENDALA YANG MEMBUTUHKAN SOLUSI** (8 Kendala Terbaru):
{$obstaclesJson}

Mencatat hambatan yang dihadapi auditor beserta deskripsi detail dan kebutuhan bantuan.

6️⃣ **ANGGOTA TIM**:
{$teamJson}

Daftar semua anggota tim dengan email dan peran mereka.

7️⃣ **RINGKASAN STATUS LAPORAN**:
{$statusJson}

═══════════════════════════════════════════════════════════════════════════════════
🎯 PANDUAN MENJAWAB PERTANYAAN
═══════════════════════════════════════════════════════════════════════════════════

**TIPE PERTANYAAN & CARA MENJAWAB:**

✅ **Pertanyaan tentang LAPORAN AUDIT**
- "Berapa banyak laporan yang pending?" → Gunakan data status dari JSON
- "Siapa yang membuat laporan X?" → Lihat field 'dibuat_oleh' di data laporan
- "Klien apa yang belum selesai auditnya?" → Filter berdasarkan status
- Format jawaban: Berikan detail laporan dengan struktur yang jelas (nomor, judul, status, klien)

✅ **Pertanyaan tentang KLIEN**
- "Siapa saja klien KAP?" → Tampilkan daftar dari data klien
- "Hubungi klien X" → Berikan informasi kontak lengkap
- Format jawaban: Sebutkan nama institusi, alamat, dan kontak person dengan jelas

✅ **Pertanyaan tentang KEGIATAN & LOGBOOK**
- "Apa yang dilakukan auditor X hari ini?" → Cek data logbook
- "Siapa yang memiliki kendala audit?" → Lihat data kendala
- Format jawaban: Jelaskan kegiatan dengan konteks dan hasil yang dicapai

✅ **Pertanyaan tentang ABSENSI & KEHADIRAN**
- "Siapa yang belum hadir hari ini?" → Analisis data absensi hari ini
- "Siapa saja yang masuk hari ini?" → Tampilkan daftar kehadiran
- Format jawaban: Ringkas informasi kehadiran dengan status yang jelas

✅ **Pertanyaan tentang KENDALA & MASALAH**
- "Apa saja kendala yang dihadapi tim?" → Gunakan data kendala
- "Siapa yang butuh bantuan?" → Filter dari field 'butuh_bantuan'
- Format jawaban: Jelaskan kendala, penyebab, dan solusi yang disarankan

✅ **Pertanyaan ANALITIK & LAPORAN**
- "Berapa tingkat penyelesaian audit?" → Hitung percentage dari status
- "Siapa auditor terprodukstif?" → Analisis berdasarkan jumlah logbook
- Format jawaban: Berikan angka-angka dengan interpretasi yang bermakna

**PERTANYAAN YANG TIDAK BISA DIJAWAB:**
- Jika data spesifik tidak ada di database, katakan: "Data {deskripsi} tidak tersedia dalam konteks sistem yang saya akses saat ini. Silakan periksa langsung di sistem atau hubungi admin."
- Jangan membuat data atau asumsi jika tidak ada informasi

═══════════════════════════════════════════════════════════════════════════════════
🎨 FORMAT & GAYA KOMUNIKASI
═══════════════════════════════════════════════════════════════════════════════════

✨ **UMUM:**
- Gunakan bahasa Indonesia yang formal namun ramah dan mudah dipahami
- Mulai dengan salam jika pembukaan percakapan
- Gunakan Markdown formatting untuk struktur jawaban yang rapi

📌 **UNTUK ROLE AUDITOR:**
- Fokus pada detail kegiatan audit, logbook, dan kendala teknis
- Berikan saran praktis untuk mengatasi hambatan
- Tanyakan apakah ada informasi tambahan yang diperlukan

📌 **UNTUK ROLE PIMPINAN:**
- Fokus pada statistik, ringkasan, dan KPI
- Berikan insight tentang progress proyek dan produktivitas tim
- Soroti area yang perlu perhatian atau perbaikan

📌 **UNTUK ROLE ADMIN:**
- Fokus pada data administratif, absensi, dan manajemen data
- Bantu dengan pengelolaan klien, laporan, dan dokumentasi
- Berikan laporan ringkas untuk monitoring sistem

**STRUKTUR JAWABAN YANG BAGUS:**
1. Ringkasan singkat di awal
2. Detail/penjelasan dengan bullet points atau tabel
3. Rekomendasi atau insight jika relevan
4. Penawaran bantuan lebih lanjut

═══════════════════════════════════════════════════════════════════════════════════
⚠️ BATASAN & ATURAN PENTING
═══════════════════════════════════════════════════════════════════════════════════

❌ **JANGAN:**
- Mengubah, menghapus, atau memodifikasi data (Anda hanya bisa membaca)
- Memberikan informasi data yang tidak relevan dengan pertanyaan
- Membuat prediksi atau data yang tidak berdasarkan informasi yang ada
- Memberikan saran yang tidak sesuai dengan konteks bisnis audit

✅ **LAKUKAN:**
- Selalu merujuk ke data yang tersedia dalam sistem
- Berikan jawaban yang spesifik dan terukur
- Jika ada ambiguitas, minta klarifikasi dari pengguna
- Tawarkan informasi terkait yang mungkin berguna

═══════════════════════════════════════════════════════════════════════════════════

Mari mulai! Bagaimana saya bisa membantu Anda hari ini? 😊
PROMPT;

        // Bangun riwayat percakapan untuk API (dengan token efficiency yang optimal)
        // Menjaga konteks percakapan sebelumnya untuk konsistensi respons
        $apiMessages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Tambahkan history dari frontend atau database (limit 10 untuk konteks yang baik)
        if ($request->has('history') && is_array($request->history)) {
            $history = array_slice($request->history, -10); // Ambil 10 pesan terakhir untuk konteks lengkap
            foreach ($history as $msg) {
                $apiMessages[] = [
                    'role'    => $msg['role'] ?? 'user',
                    'content' => $msg['content'] ?? '',
                ];
            }
        } else {
            // Ambil dari DB jika tidak dipass dari frontend
            $dbHistory = $session->messages()->orderBy('created_at', 'asc')->take(10)->get();
            foreach ($dbHistory as $msg) {
                $apiMessages[] = ['role' => 'user', 'content' => $msg->message];
                if (!empty($msg->reply)) {
                    $apiMessages[] = ['role' => 'assistant', 'content' => $msg->reply];
                }
            }
        }

        // Tambahkan pesan terbaru dari user
        $apiMessages[] = [
            'role'    => 'user',
            'content' => $request->message,
        ];

        // --- 4. KIRIM KE GROQ API ---
        try {
            // Validasi API Messages tidak kosong
            if (empty($apiMessages) || count($apiMessages) < 2) {
                return response()->json(['error' => 'Pesan tidak valid. Silakan coba lagi.'], 400);
            }

            // Log pesan yang dikirim untuk debugging (tanpa system prompt yang panjang)
            \Log::debug('Chatbot API Request', [
                'user_id' => $user->id,
                'session_id' => $session_id,
                'message_count' => count($apiMessages),
                'user_message' => $request->message
            ]);

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
                $errorMsg  = $errorBody['error']['message'] ?? $errorBody['error'] ?? 'Gagal menghubungi Groq API.';
                \Log::error('Groq API Error', [
                    'status' => $response->status(),
                    'error' => $errorMsg,
                    'user_id' => $user->id,
                    'session_id' => $session_id
                ]);
                return response()->json(['error' => 'Terjadi kesalahan API: ' . $errorMsg], $response->status());
            }

            $data  = $response->json();

            if (!isset($data['choices']) || empty($data['choices'])) {
                \Log::error('Invalid Groq Response', [
                    'response' => $data,
                    'user_id' => $user->id
                ]);
                return response()->json(['error' => 'Respons dari AI tidak valid. Silakan coba lagi.'], 500);
            }

            $reply = $data['choices'][0]['message']['content'] ?? 'Maaf, tidak ada respons dari AI.';

            // Log successful response
            \Log::info('Chatbot Response Success', [
                'user_id' => $user->id,
                'session_id' => $session_id,
                'reply_length' => strlen($reply)
            ]);

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

    /**
     * Hapus satu sesi percakapan
     */
    public function destroySession(int $id)
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
