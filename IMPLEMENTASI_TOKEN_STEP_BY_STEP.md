# 🚀 PANDUAN IMPLEMENTASI TOKEN MANAGEMENT - STEP BY STEP

## 📋 Overview Singkat

Untuk setup token management untuk 20 orang, berikut ringkasan yang perlu dilakukan:

### **Distribusi Token untuk 20 Orang:**

```
3 Admin (IDs: 1-3)
├─ Daily: 50,000 tokens
├─ Monthly: 500,000 tokens
└─ Total: 150,000/bulan

5 Pimpinan (IDs: 4-8)
├─ Daily: 30,000 tokens
├─ Monthly: 300,000 tokens
└─ Total: 100,000/bulan

12 Auditor (IDs: 9-20)
├─ Senior (2 orang): 15,000/hari, 150,000/bulan
├─ Mid-Level (6 orang): 10,000/hari, 100,000/bulan
├─ Junior (4 orang): 8,000/hari, 80,000/bulan
└─ Total: 100,000/bulan

════════════════════════════════════════
GRAND TOTAL = 350,000 tokens/bulan
```

---

## ✅ CHECKLIST IMPLEMENTASI

### **Phase 1: Database & Models**

- [ ] Jalankan migration untuk token tables:

```bash
php artisan make:migration create_token_usage_table
php artisan make:migration create_token_quotas_table
```

- [ ] Copy file migration dari `TOKEN_MANAGEMENT_GUIDE.md`

- [ ] Create models:

```bash
# Sudah tersedia di:
# - app/Models/TokenUsage.php
# - app/Models/TokenQuota.php
```

- [ ] Jalankan migration:

```bash
php artisan migrate
```

---

### **Phase 2: Utilities & Config**

- [ ] Copy file `config/tokens.php` (sudah dibuat)

- [ ] Copy file `app/Utils/TokenCounter.php` (sudah dibuat)

- [ ] Test TokenCounter:

```php
use App\Utils\TokenCounter;

// Test estimate tokens
echo TokenCounter::estimate("Berapa laporan yang pending?");  // ~6 tokens

// Test debug breakdown
$breakdown = TokenCounter::getDebugBreakdown($systemPrompt, $apiMessages);
dd($breakdown);
```

---

### **Phase 3: Setup Token Quotas untuk 20 Users**

- [ ] Copy file `database/seeders/TokenQuotaSeeder.php` (sudah dibuat)

- [ ] Update `database/seeders/DatabaseSeeder.php`:

```php
public function run(): void
{
    $this->call([
        // Seeders lainnya...
        TokenQuotaSeeder::class,  // Tambahkan ini
    ]);
}
```

- [ ] Jalankan seeder:

```bash
php artisan db:seed --class=TokenQuotaSeeder
```

- [ ] Verify data sudah masuk:

```bash
php artisan tinker
```

```php
TokenQuota::all();  // Check semua quota
TokenQuota::where('user_id', 1)->first();  // Check user 1
```

---

### **Phase 4: Update ChatbotController**

**Option A: Update Existing Method (Recommended)**

Edit file: `app/Http/Controllers/ChatbotController.php`

Di method `chat()`, tambahkan di awal (setelah `$user = Auth::user()`):

```php
// TAMBAHKAN KODE INI setelah $user = Auth::user();

// Get atau create token quota
$quota = TokenQuota::getOrCreateQuota($user->id, $user->role);

// Estimate tokens
$estimatedInputTokens = TokenCounter::estimate($request->message);
$maxTokens = config('tokens.groq.max_tokens_per_request', 1500);
$estimatedOutputTokens = TokenCounter::estimateOutput($maxTokens);
$estimatedTotalTokens = $estimatedInputTokens + $estimatedOutputTokens;

// Cek quota
if (!$quota->canUseTokens($estimatedTotalTokens)) {
    $todayUsage = TokenUsage::getTodayUsage($user->id);
    $monthUsage = TokenUsage::getMonthUsage($user->id);

    return response()->json([
        'error' => 'Quota token telah habis!',
        'details' => [
            'daily_limit' => $quota->daily_limit,
            'daily_used' => $todayUsage,
            'monthly_limit' => $quota->monthly_limit,
            'monthly_used' => $monthUsage,
        ]
    ], 429);
}
```

Kemudian setelah mendapat response dari Groq API, tambahkan:

```php
// TAMBAHKAN SETELAH mendapat response dari Groq API
// (sebelum ChatMessage::create)

$actualInputTokens = $data['usage']['prompt_tokens'] ?? $estimatedInputTokens;
$actualOutputTokens = $data['usage']['completion_tokens'] ?? $estimatedOutputTokens;
$actualTotalTokens = $actualInputTokens + $actualOutputTokens;

// Simpan message
$chatMessage = ChatMessage::create([
    'chat_session_id' => $session_id,
    'message' => $request->message,
    'reply' => $reply,
]);

// Log token usage
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

// Update quota
$quota->addUsage($actualTotalTokens);
```

Juga update return response untuk include token info:

```php
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
```

**Option B: Full Implementation Reference**

Lihat file: `INTEGRATION_TOKEN_EXAMPLE.php` untuk full implementation contoh.

---

### **Phase 5: Add Imports di ChatbotController**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\TokenQuota;        // ← TAMBAHKAN
use App\Models\TokenUsage;        // ← TAMBAHKAN
use App\Utils\TokenCounter;       // ← TAMBAHKAN
```

---

## 🧪 TESTING TOKEN MANAGEMENT

### **Test 1: Verify Quota Creation**

```bash
php artisan tinker
```

```php
// Check quota untuk user 1 (should be admin)
$quota = TokenQuota::where('user_id', 1)->first();
echo "Daily: " . $quota->daily_limit;      // 50000
echo "Monthly: " . $quota->monthly_limit;  // 500000

// Check semua admin
$admins = TokenQuota::where('role', 'admin')->get();
echo "Admin count: " . $admins->count();   // 3
```

### **Test 2: Token Counting**

```php
use App\Utils\TokenCounter;

// Estimate simple message
$tokens = TokenCounter::estimate("Berapa laporan yang pending?");
echo "Tokens: $tokens";  // Should be ~6-8

// Test dengan message panjang
$longText = "Lorem ipsum dolor sit amet..."; // 1000+ words
$tokens = TokenCounter::estimate($longText);
echo "Tokens: $tokens";  // Should proportional to word count

// Test messages array
$messages = [
    ['role' => 'system', 'content' => $systemPrompt],
    ['role' => 'user', 'content' => 'Halo'],
];
$totalTokens = TokenCounter::estimateMessages($messages);
echo "Total: $totalTokens";
```

### **Test 3: Simulate API Call & Track Usage**

```php
// Simulate creating token usage record
$chatMessage = ChatMessage::find(1);  // Get existing message
$user = User::find(1);

TokenUsage::create([
    'user_id' => $user->id,
    'chat_message_id' => $chatMessage->id,
    'input_tokens' => 256,
    'output_tokens' => 512,
    'total_tokens' => 768,
    'model' => 'mixtral-8x7b-32768',
    'used_at' => now(),
    'date' => today(),
]);

// Check usage
$todayUsage = TokenUsage::getTodayUsage($user->id);
echo "Today used: $todayUsage tokens";  // 768

// Check remaining quota
$quota = TokenQuota::find($user->id);
echo "Daily remaining: " . $quota->getRemainingDaily();
```

### **Test 4: Test Over-Quota Error**

```php
// Jika user sudah pakai semua quota hari ini
$quota = TokenQuota::where('user_id', 10)->first();  // Auditor
echo $quota->daily_limit;  // 10000

// Simulate usage
TokenUsage::create([
    'user_id' => 10,
    'input_tokens' => 8000,
    'output_tokens' => 2000,
    'total_tokens' => 10000,
    'date' => today(),
]);

// Try to check if can use more tokens
$canUse = $quota->canUseTokens(1000);
echo $canUse ? "Can use" : "Cannot use";  // Should be "Cannot use"
```

---

## 📊 MONITORING & DASHBOARD

### **Simple Dashboard Controller**

```php
// Create file: app/Http/Controllers/TokenDashboardController.php

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

        return view('dashboard.tokens', [
            'quota' => $quota,
            'todayUsage' => TokenUsage::getTodayUsage($user->id),
            'monthUsage' => TokenUsage::getMonthUsage($user->id),
            'dailyPercent' => $quota->getDailyUsagePercent(),
            'monthlyPercent' => $quota->getMonthlyUsagePercent(),
            'remaining_daily' => $quota->getRemainingDaily(),
            'remaining_monthly' => $quota->getRemainingMonthly(),
        ]);
    }
}
```

---

## 🔧 TROUBLESHOOTING

### **Error: "Class TokenQuota not found"**

```
→ Pastikan file app/Models/TokenQuota.php sudah ada
→ Run: composer dump-autoload
```

### **Error: "Table token_quotas doesn't exist"**

```
→ Run migration: php artisan migrate
```

### **Error: "User has no quota"**

```
→ TokenQuota akan auto-create saat pertama kali
→ Atau manual seed: TokenQuotaSeeder::class
```

### **Token Usage Not Recorded**

```
→ Check apakah ChatMessage berhasil dibuat
→ Pastikan TokenUsage create() dipanggil setelah API success
→ Check logs: storage/logs/laravel.log
```

---

## 📈 MONITORING SCRIPT

Jalankan command untuk monitoring:

```bash
# Check token usage untuk semua users hari ini
php artisan tinker

# Di dalam tinker:
TokenUsage::whereDate('date', today())->selectRaw('user_id, SUM(total_tokens) as total')
    ->with('user:id,name')
    ->groupBy('user_id')
    ->orderByDesc('total')
    ->get();

# Check siapa yang over quota
TokenQuota::all()->filter(fn($q) => $q->getDailyUsagePercent() >= 100);

# Reset daily quota (jalankan di scheduler)
TokenQuota::all()->each(fn($q) => $q->resetDaily());
```

---

## 🎯 NEXT STEPS

Setelah implementasi token management:

1. **Setup Dashboard UI** - Buat blade template untuk show token usage
2. **Setup Scheduler** - Auto-reset quota daily/monthly
3. **Setup Alerts** - Notify user saat mendekati limit
4. **Setup Reports** - Generate token usage reports per user/role
5. **Setup Analytics** - Track trend penggunaan token

---

## 📞 REFERENCE FILES

Semua file yang dibutuhkan sudah dibuat:

- `TOKEN_MANAGEMENT_GUIDE.md` - Panduan lengkap konsep
- `token/config.php` - Konfigurasi
- `app/Models/TokenUsage.php` - Model
- `app/Models/TokenQuota.php` - Model
- `app/Utils/TokenCounter.php` - Helper
- `database/seeders/TokenQuotaSeeder.php` - Seeder untuk 20 users
- `INTEGRATION_TOKEN_EXAMPLE.php` - Contoh implementasi di Controller

---

**Last Updated: 23 May 2026**
