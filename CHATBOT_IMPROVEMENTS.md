# Dokumentasi Perbaikan Chatbot KAP

## 📋 Ringkasan Perubahan

ChatbotController telah ditingkatkan dengan **system prompt yang lebih komprehensif** dan **akses data yang lebih baik** untuk memberikan respons AI yang lebih akurat dan berguna.

---

## ✨ Fitur Utama yang Ditingkatkan

### 1. **System Prompt yang Lebih Detail & Terstruktur** 🤖

Sebelumnya, system prompt hanya memberikan informasi dasar. Sekarang mencakup:

- ✅ **Panduan lengkap** untuk setiap tipe pertanyaan
- ✅ **Instruksi role-based** (auditor, pimpinan, admin)
- ✅ **Format respons yang jelas** dengan struktur yang rapi
- ✅ **Batasan dan aturan penting** yang harus dipatuhi AI
- ✅ **Penjelasan data** yang tersedia dalam sistem

### 2. **Akses Data yang Lebih Komprehensif** 📊

Sistem sekarang mengambil dan menyajikan data lebih lengkap:

| Data          | Jumlah Sebelumnya | Jumlah Sekarang | Apa yang Ditambahkan                |
| ------------- | ----------------- | --------------- | ----------------------------------- |
| Laporan Audit | 5                 | 10              | Status in_progress, pembuat laporan |
| Klien         | 5                 | 10              | Kontak person, telepon              |
| Logbook       | 10                | 15              | Tipe kendala, nama lengkap          |
| Kendala       | 5                 | 8               | Deskripsi lebih detail              |
| Pengguna      | -                 | 20              | Daftar lengkap tim                  |
| Status Report | -                 | Summary         | Perhitungan status audit            |

### 3. **Konteks Data yang Lebih Kaya** 📈

Setiap data sekarang disajikan dengan:

- **Field yang relevan** dan terlokalisasi (bahasa Indonesia)
- **Relasi data** (klien ke laporan, user ke logbook, dll)
- **Format JSON yang terstruktur** untuk kemudahan parsing
- **Statistik ringkasan** untuk quick insights

### 4. **Manajemen Riwayat Percakapan yang Lebih Baik** 💬

- **Dari:** 5 pesan terakhir
- **Menjadi:** 10 pesan terakhir (lebih baik untuk konteks)
- **Plus:** Validasi input yang lebih ketat
- **Plus:** Penanganan pesan kosong yang lebih baik

### 5. **Token Management Optimal** ⚡

- **Max tokens:** Ditingkatkan dari 1024 menjadi 1500
- **Alasan:** Memungkinkan respons yang lebih detail dan berkualitas
- **Keseimbangan:** Tetap efisien dalam penggunaan API

### 6. **Logging & Error Handling yang Lebih Baik** 🔍

Logging yang lebih detail untuk debugging:

```php
// Log request dengan konteks
\Log::debug('Chatbot API Request', [
    'user_id' => $user->id,
    'session_id' => $session_id,
    'message_count' => count($apiMessages),
    'user_message' => $request->message
]);

// Log error dengan informasi lengkap
\Log::error('Groq API Error', [
    'status' => $response->status(),
    'error' => $errorMsg,
    'user_id' => $user->id,
    'session_id' => $session_id
]);

// Log success
\Log::info('Chatbot Response Success', [
    'user_id' => $user->id,
    'session_id' => $session_id,
    'reply_length' => strlen($reply)
]);
```

---

## 🎯 Tipe Pertanyaan yang Sekarang Lebih Baik Ditangani

### 1. **Pertanyaan Laporan Audit**

```
"Berapa banyak laporan yang masih pending?"
"Siapa yang membuat laporan nomor LAP-001?"
"Klien mana yang belum selesai auditnya?"
```

### 2. **Pertanyaan Klien**

```
"Siapa saja klien KAP kami?"
"Bagaimana cara menghubungi klien XYZ?"
"Alamat lengkap klien ABC adalah?"
```

### 3. **Pertanyaan Aktivitas & Logbook**

```
"Apa saja yang dilakukan auditor hari ini?"
"Siapa yang memiliki kendala dalam audit?"
"Kegiatan apa yang sedang dijalankan Tim A?"
```

### 4. **Pertanyaan Absensi & Kehadiran**

```
"Siapa yang belum hadir hari ini?"
"Daftar kehadiran karyawan hari ini?"
"Jam masuk siapa saja hari ini?"
```

### 5. **Pertanyaan Analitik**

```
"Berapa tingkat penyelesaian audit saat ini?"
"Siapa auditor yang paling produktif?"
"Berapa banyak laporan yang pending/in_progress/completed?"
```

### 6. **Pertanyaan Kendala & Solusi**

```
"Apa saja kendala yang dihadapi tim?"
"Siapa yang butuh bantuan?"
"Bagaimana solusi untuk kendala ini?"
```

---

## 📱 Struktur Response yang Diharapkan

Chatbot sekarang dirancang untuk memberikan respons dengan struktur:

```
1. Ringkasan singkat (1-2 baris)
2. Detail dengan bullet points atau tabel
3. Rekomendasi atau insight jika relevan
4. Penawaran bantuan lebih lanjut
```

**Contoh:**

> Berdasarkan data sistem saat ini, terdapat **3 laporan yang masih pending**.
>
> **Laporan Pending:**
>
> - LAP-001: Audit Keuangan PT ABC (Dibuat oleh: John Doe)
> - LAP-002: Audit Internal PT XYZ (Dibuat oleh: Jane Smith)
> - LAP-003: Audit Operasional PT 123 (Dibuat oleh: Mike Johnson)
>
> **Rekomendasi:** Prioritaskan LAP-001 karena sudah 5 hari pending. Apakah ada kendala yang menghambat penyelesaian?

---

## 🔧 Konfigurasi yang Diperlukan

Pastikan file `.env` memiliki konfigurasi:

```env
GROQ_API_KEY=your_groq_api_key_here
GROQ_BASE_URL=https://api.groq.com/openai/v1
GROQ_MODEL=mixtral-8x7b-32768  # atau model lainnya
```

---

## 📝 Catatan Teknis

### Data Yang Diambil dari Database

1. **AuditReport** - 10 laporan terbaru dengan relasi klien dan creator
2. **Client** - 10 klien dengan informasi:
    - `id`, `institution_name`, `institution_type` (pemerintahan/swasta)
    - `leader_name` (nama pimpinan klien)
    - `address`, `city`, `phone`, `status` (aktif/nonaktif)
3. **Logbook** - 15 aktivitas terbaru dengan user dan status kendala
4. **Attendance** - Absensi hari ini dengan jam masuk/keluar
5. **User** - 20 anggota tim dengan role dan email
6. **Obstacle Logbooks** - 8 kendala terbaru yang butuh solusi

### Perbaikan Bug (25 Mei 2026)

**Error yang Diperbaiki:**

```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'contact_person'
```

**Solusi:**

- ❌ Sebelumnya menggunakan field: `contact_person`, `address`, `phone`
- ✅ Sekarang menggunakan field yang ada di database: `institution_name`, `institution_type`, `leader_name`, `address`, `city`, `phone`, `status`

**Field yang Tersedia di Table `clients`:**

```
- id
- created_by (user yang membuat data)
- institution_type (Pemerintahan/Swasta)
- audit_type (keuangan/kepatuhan)
- institution_name (nama instansi)
- leader_name (nama pimpinan - BUKAN contact_person!)
- address (alamat)
- city (kota)
- province (provinsi)
- postal_code (kode pos)
- phone (telepon)
- email (email)
- npwp (NPWP)
- contract_start, contract_end (tanggal kontrak)
- contract_status (status kontrak)
- status (status klien)
- approval_status (status approval pimpinan)
```

### Format Data yang Digunakan

Semua data di-convert ke **JSON dengan flag:**

```php
JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
```

Ini memastikan karakter Indonesia (ü, á, é, dll) ditampilkan dengan benar dalam konteks AI.

---

## ✅ Testing Recommendations

Untuk memastikan chatbot bekerja optimal:

1. **Test pertanyaan dasar:**
    - "Berapa total laporan audit?"
    - "Siapa saja klien kami?"
    - "Siapa yang hadir hari ini?"

2. **Test pertanyaan kompleks:**
    - "Analisis produktivitas tim bulan ini"
    - "Apa kendala utama yang dihadapi dan solusinya?"

3. **Test role-based:**
    - Login sebagai auditor, pimpinan, admin
    - Pastikan response disesuaikan dengan role

4. **Monitor logs:**
    - Cek `storage/logs/laravel.log` untuk debugging
    - Perhatikan error handling dan performance

---

## 🚀 Peningkatan Ke Depan

Saran untuk perbaikan selanjutnya:

- [ ] Tambahkan filter berdasarkan tanggal range (audit dari X sampai Y)
- [ ] Implementasi caching untuk data yang jarang berubah
- [ ] Tambah analitik prediktif (estimasi tanggal selesai audit)
- [ ] Integrasi dengan calendar untuk timeline management
- [ ] Export report dari chatbot (PDF, Excel)
- [ ] Fine-tuning model dengan contoh respons yang baik (few-shot learning)

---

## 📞 Dukungan

Jika ada pertanyaan atau masalah:

1. Cek log di `storage/logs/laravel.log`
2. Pastikan API key Groq valid
3. Verifikasi relasi model di `app/Models/`
4. Test koneksi database dengan `php artisan tinker`

---

**Terakhir diperbarui:** 23 Mei 2026
