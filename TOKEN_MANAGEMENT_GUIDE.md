# 📊 Panduan Manajemen Token untuk Multiple Users

## 🎯 Konsep Dasar

### **Apa itu Token?**

Token adalah unit terkecil yang diproses oleh API Groq. Satu kata ≈ 1-2 tokens.

**Contoh:**

- "Berapa laporan pending?" = ~4 tokens
- Response panjang 1000 kata = ~1500 tokens

### **Groq API Token Limits**

```
Per Permintaan (Request):
- Input: ~128K tokens max
- Output (max_tokens): 1500 tokens saat ini

Per Bulan (tergantung pricing plan):
- Free tier: ~500 requests/bulan
- Pro tier: Unlimited (berbayar)
```

---

## 🛠️ Solusi 1: Token Tracking per User (Database)

### **Step 1: Buat Migration untuk Token Usage Table**

```php
// database/migrations/2026_05_23_create_token_usage_table.php
php artisan make:migration create_token_usage_table
```

Isi migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('token_usage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('chat_message_id')->constrained()->onDelete('cascade');

            // Token counts
            $table->integer('input_tokens');      // Token dari prompt + history
            $table->integer('output_tokens');     // Token dari response
            $table->integer('total_tokens');      // Total input + output

            // Metadata
            $table->string('model')->default('mixtral-8x7b-32768');
            $table->timestamp('used_at');

            // Untuk tracking quota
            $table->date('date');
            $table->timestamps();

            $table->index('user_id');
            $table->index('date');
        });

        // Tabel untuk quota settings per user/role
        Schema::create('token_quotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('role')->nullable(); // auditor, pimpinan, admin

            // Quota settings
            $table->integer('daily_limit');      // Token per hari
            $table->integer('monthly_limit');    // Token per bulan
            $table->integer('used_today');       // Token sudah dipakai hari ini
            $table->integer('used_this_month');  // Token sudah dipakai bulan ini

            $table->date('reset_date')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('token_quotas');
        Schema::dropIfExists('token_usage');
    }
};
```

### **Step 2: Buat Model**

```php
// app/Models/TokenUsage.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokenUsage extends Model
{
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chatMessage()
    {
        return $this->belongsTo(ChatMessage::class);
    }

    /**
     * Get today's token usage for a user
     */
    public static function getTodayUsage($userId)
    {
        return self::where('user_id', $userId)
            ->whereDate('date', today())
            ->sum('total_tokens');
    }

    /**
     * Get this month's token usage for a user
     */
    public static function getMonthUsage($userId)
    {
        return self::where('user_id', $userId)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('total_tokens');
    }
}
```

```php
// app/Models/TokenQuota.php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokenQuota extends Model
{
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create quota for user
     */
    public static function getOrCreateQuota($userId, $role = null)
    {
        $quota = self::where('user_id', $userId)->first();

        if (!$quota) {
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
     * Get default daily limit berdasarkan role
     */
    private static function getDefaultDailyLimit($role)
    {
        return match($role) {
            'admin' => 50000,      // Admin: banyak
            'pimpinan' => 30000,   // Pimpinan: sedang
            'auditor' => 10000,    // Auditor: terbatas
            default => 10000,
        };
    }

    /**
     * Get default monthly limit berdasarkan role
     */
    private static function getDefaultMonthlyLimit($role)
    {
        return match($role) {
            'admin' => 500000,
            'pimpinan' => 300000,
            'auditor' => 100000,
            default => 100000,
        };
    }

    /**
     * Check apakah user masih bisa mengirim pesan
     */
    public function canUseTokens($tokensNeeded)
    {
        return $this->used_today + $tokensNeeded <= $this->daily_limit
            && $this->used_this_month + $tokensNeeded <= $this->monthly_limit;
    }
}
```

---

## 📝 Solusi 2: Estimate Token Sebelum Kirim ke API

### **Install Library untuk Token Counting**

```bash
composer require openai-php/client
```

### **Buat Helper Class untuk Token Counting**

```php
// app/Utils/TokenCounter.php
<?php
namespace App\Utils;

class TokenCounter
{
    /**
     * Estimate tokens (sederhana - 1 kata ≈ 1.3 tokens)
     * Untuk accuracy lebih tinggi, gunakan cl100k_base tokenizer
     */
    public static function estimate($text)
    {
        if (empty($text)) {
            return 0;
        }

        // Rough estimation: 1 kata ≈ 1.3 tokens
        $words = count(explode(' ', $text));
        return (int) ceil($words * 1.3);
    }

    /**
     * Estimate tokens untuk array of messages
     */
    public static function estimateMessages($messages)
    {
        $total = 0;

        foreach ($messages as $msg) {
            $total += self::estimate($msg['content'] ?? '');
            // Tambah overhead untuk role dan formatting
            $total += 4;
        }

        return $total;
    }

    /**
     * Hitung tokens lebih akurat dengan OpenAI tokenizer
     * (Opsional - memerlukan dependency)
     */
    public static function countAccurate($text)
    {
        try {
            // Jika menggunakan OpenAI library
            // $client = OpenAI::client();
            // Gunakan encoding cl100k_base untuk Groq compatibility

            // Fallback ke estimation jika tidak bisa
            return self::estimate($text);
        } catch (\Exception $e) {
            return self::estimate($text);
        }
    }
}
```

---

## 🔄 Solusi 3: Update ChatbotController dengan Token Management

```php
// Di dalam ChatbotController::chat() method

// Setelah validasi input
$user = Auth::user();
$quota = TokenQuota::getOrCreateQuota($user->id, $user->role);

// Estimate tokens yang akan digunakan
$estimatedInputTokens = TokenCounter::estimateMessages($apiMessages);
$estimatedOutputTokens = 1500; // max_tokens
$estimatedTotalTokens = $estimatedInputTokens + $estimatedOutputTokens;

// Validasi quota sebelum mengirim
if (!$quota->canUseTokens($estimatedTotalTokens)) {
    $todayUsed = TokenUsage::getTodayUsage($user->id);
    $monthUsed = TokenUsage::getMonthUsage($user->id);

    return response()->json([
        'error' => 'Quota token telah habis!',
        'details' => [
            'daily_limit' => $quota->daily_limit,
            'daily_used' => $todayUsed,
            'monthly_limit' => $quota->monthly_limit,
            'monthly_used' => $monthUsed,
            'tokens_needed' => $estimatedTotalTokens
        ]
    ], 429); // 429 Too Many Requests
}

// Lanjutkan dengan API call...
$response = Http::withToken($apiKey)->post(/* ... */);

// PENTING: Groq API response biasanya include token usage info
$data = $response->json();

// Catat penggunaan token
if (isset($data['usage'])) {
    $actualInputTokens = $data['usage']['prompt_tokens'] ?? $estimatedInputTokens;
    $actualOutputTokens = $data['usage']['completion_tokens'] ?? 0;

    TokenUsage::create([
        'user_id' => $user->id,
        'chat_message_id' => $chatMessage->id,
        'input_tokens' => $actualInputTokens,
        'output_tokens' => $actualOutputTokens,
        'total_tokens' => $actualInputTokens + $actualOutputTokens,
        'model' => $model,
        'used_at' => now(),
        'date' => today(),
    ]);

    // Update quota
    $quota->used_today += $actualInputTokens + $actualOutputTokens;
    $quota->used_this_month += $actualInputTokens + $actualOutputTokens;
    $quota->save();
}
```

---

## 📊 Konfigurasi Token Limit untuk 20 Orang

### **Opsi 1: Berbasis Role**

```php
// config/tokens.php
return [
    'quotas' => [
        'admin' => [
            'daily' => 50000,      // 50K tokens/hari
            'monthly' => 500000,   // 500K tokens/bulan
        ],
        'pimpinan' => [
            'daily' => 30000,      // 30K tokens/hari
            'monthly' => 300000,   // 300K tokens/bulan
        ],
        'auditor' => [
            'daily' => 10000,      // 10K tokens/hari
            'monthly' => 100000,   // 100K tokens/bulan
        ],
    ],
];
```

### **Opsi 2: Berbasis User Individual**

```php
// Setup di seeder atau config
TokenQuota::create([
    'user_id' => 1,  // John (Auditor Lead)
    'role' => 'auditor',
    'daily_limit' => 15000,    // Lebih tinggi karena lead
    'monthly_limit' => 150000,
]);

TokenQuota::create([
    'user_id' => 2,  // Jane (Junior Auditor)
    'role' => 'auditor',
    'daily_limit' => 8000,     // Lebih rendah
    'monthly_limit' => 80000,
]);
```

---

## 💡 Dashboard untuk Monitor Token Usage

### **Buat Controller**

```php
// app/Http/Controllers/TokenDashboardController.php
<?php
namespace App\Http\Controllers;

use App\Models\TokenUsage;
use App\Models\TokenQuota;
use Illuminate\Support\Facades\Auth;

class TokenDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $quota = TokenQuota::getOrCreateQuota($user->id, $user->role);

        // Statistik penggunaan
        $todayUsage = TokenUsage::getTodayUsage($user->id);
        $monthUsage = TokenUsage::getMonthUsage($user->id);

        // History penggunaan
        $usage_history = TokenUsage::where('user_id', $user->id)
            ->with('chatMessage')
            ->orderBy('used_at', 'desc')
            ->take(20)
            ->get();

        // Team usage (untuk admin/pimpinan)
        $team_usage = [];
        if (in_array($user->role, ['admin', 'pimpinan'])) {
            $team_usage = TokenUsage::selectRaw('user_id, SUM(total_tokens) as total')
                ->with('user')
                ->whereDate('date', today())
                ->groupBy('user_id')
                ->orderByDesc('total')
                ->get();
        }

        return view('dashboard.tokens', compact(
            'quota',
            'todayUsage',
            'monthUsage',
            'usage_history',
            'team_usage'
        ));
    }

    /**
     * Admin: Ubah quota user tertentu
     */
    public function updateQuota($userId)
    {
        request()->validate([
            'daily_limit' => 'required|integer|min:1000',
            'monthly_limit' => 'required|integer|min:10000',
        ]);

        $quota = TokenQuota::findOrFail($userId);
        $quota->update(request()->only(['daily_limit', 'monthly_limit']));

        return redirect()->back()->with('success', 'Quota updated');
    }
}
```

---

## ⚙️ Skenario Penggunaan 20 Orang

### **Distribusi Token (Total: 300K tokens/bulan)**

```
3 Admin                 → 50K tokens/bulan masing-masing = 150K
5 Pimpinan             → 20K tokens/bulan masing-masing = 100K
12 Auditor             → 8-10K tokens/bulan masing-masing = 100K
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
TOTAL = 350K tokens/bulan
```

### **Daily Distribution (untuk 20 orang)**

```
Maksimal request per hari = 350K ÷ 30 hari ≈ 11.6K tokens

Pagi (08:00-12:00)    : 4K tokens
Siang (12:00-16:00)   : 4K tokens
Sore (16:00-18:00)    : 3.6K tokens
```

---

## 🚀 Best Practices untuk Token Management

### **1. Optimize Prompt Size**

```php
// ❌ JANGAN: Kirim semua data
$systemPrompt = json_encode([
    'all_audit_reports' => 10000 records,  // Terlalu besar!
    'all_clients' => 5000 records,
]);

// ✅ LAKUKAN: Ambil yang relevan saja
$recentReports = AuditReport::latest()->take(10)->get();  // Hanya 10 terbaru
$relevantClients = Client::where('status', 'aktif')->take(5)->get();
```

### **2. Cache System Prompt**

```php
// Cache prompt selama 1 jam untuk mengurangi token
$systemPrompt = Cache::remember('chatbot_system_prompt', 3600, function () {
    // Generate prompt...
    return $prompt;
});
```

### **3. Compress Data untuk Context**

```php
// Ganti JSON yang verbose dengan format ringkas
// ❌ VERBOSE
'laporan_audit' => [
    'id' => 1,
    'nomor_laporan' => 'LAP-001',
    'judul' => 'Audit Laporan Keuangan PT ABC Tahun 2024',
    // ...
]

// ✅ RINGKAS
'laporan' => [
    'LAP-001' => 'Audit Keuangan PT ABC [Pending]',
]
```

### **4. Batching untuk Reduce Overhead**

```php
// Alih-alih kirim 1 prompt = 1 API call
// Bisa batch beberapa pertanyaan (jika UI mendukung)
```

---

## 🔔 Alert & Monitoring

### **Buat Notifikasi untuk Over-Usage**

```php
// app/Jobs/CheckTokenQuotaJob.php
<?php
namespace App\Jobs;

use App\Models\TokenQuota;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckTokenQuotaJob implements ShouldQueue
{
    use Queueable;

    public function handle()
    {
        // Cek user yang sudah pakai >80% quota
        $quotas = TokenQuota::all();

        foreach ($quotas as $quota) {
            $usage_percent = ($quota->used_today / $quota->daily_limit) * 100;

            if ($usage_percent >= 80) {
                // Kirim notifikasi/email
                $user = $quota->user;
                // Notify::send($user, "Token quota mencapai {$usage_percent}%");
            }
        }
    }
}
```

---

## 📋 Checklist Setup Token Management

- [ ] Buat migration untuk token_usage dan token_quotas tables
- [ ] Buat models TokenUsage dan TokenQuota
- [ ] Buat TokenCounter helper class
- [ ] Update ChatbotController dengan token checking
- [ ] Setup konfigurasi di config/tokens.php
- [ ] Buat TokenDashboardController untuk monitoring
- [ ] Seed default quotas untuk 20 users
- [ ] Setup job untuk notifikasi over-usage
- [ ] Test dengan berbagai skenario

---

**Terakhir diperbarui:** 23 Mei 2026
