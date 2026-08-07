# MASTER PROMPT: GENERATOR DATA FLOW DIAGRAM (DFD) SKRIPSI (TERMASUK AI CHATBOT)

Gunakan prompt di bawah ini untuk meminta AI (seperti ChatGPT, Claude, atau Gemini) membuatkan **Data Flow Diagram (DFD)** yang akurat, terstruktur, dan sesuai standar metodologi penelitian/skripsi (Gane & Sarson / Yourdon & DeMarco), lengkap dengan fitur **AI Chatbot Service**.

---

## 📋 SALIN (COPY) PROMPT DI BAWAH INI:

```text
Bertindaklah sebagai System Analyst Senior dan Pakar Pemodelan Sistem Informasi. 

Saya membutuhkan rancangan Data Flow Diagram (DFD) yang akurat, logis, dan terstruktur untuk penulisan Bab 3 / Bab 4 Skripsi saya mengenai "Sistem Informasi Manajemen Audit, Logbook Kegiatan, dan AI Chatbot Asisten pada Kantor Akuntan Publik (KAP)".

Tolong buatkan dokumen DFD lengkap dengan standar Gane & Sarson / Yourdon & DeMarco yang mencakup poin-poin berikut:

1. IDENTIFIKASI ELEMEN SISTEM:
   - Entitas Eksternal (Actors & Services): 
     * Admin (Manajemen user, instansi klien, tim auditor, draft laporan)
     * Auditor (Presensi harian, logbook kegiatan, upload revisi laporan, chat AI)
     * Pimpinan / Partner (Approval klien, review & catatan laporan, review logbook, statistik)
     * Groq AI Service (API Eksternal penyedia LLM pemroses pertanyaan & konteks)
   - Data Store (Penyimpanan Data): 
     * D1: Data Pengguna (Users)
     * D2: Data Klien & Tim Audit (Clients & Client Auditors)
     * D3: Laporan Audit & Berkas (Audit Reports & Files)
     * D4: Review & Catatan Revisi (Audit Report Reviews)
     * D5: Absensi & Logbook Kegiatan (Attendances & Logbooks)
     * D6: Notifikasi System (Notifications)
     * D7: Sesi & Pesan Chatbot (Chat Sessions & Messages)

2. DIAGRAM KONTEKS (CONTEXT DIAGRAM / DFD LEVEL 0 TOP):
   - Jelaskan hubungan interaksi entitas Admin, Auditor, Pimpinan, dan Groq AI Service dengan Sistem Informasi Audit KAP secara keseluruhan.
   - Sertakan kode diagram Mermaid.js agar dapat divisualisasikan dengan jelas.

3. DFD LEVEL 0 (OVERVIEW DIAGRAM):
   - Pecah sistem menjadi 6 proses utama:
     1.0 Otentikasi & Kelola Pengguna
     2.0 Manajemen Klien & Penugasan Tim Audit
     3.0 Pengelolaan & Review Laporan Audit (Draft -> Dalam Review -> Perlu Revisi -> Disetujui)
     4.0 Pengelolaan Presensi & Logbook Kegiatan Auditor
     5.0 Layanan Notifikasi & Dashboard Rekapitulasi
     6.0 Layanan Asisten Pintar (AI Chatbot AURA)
   - Sertakan alur data (data flow) yang menghubungkan entitas eksternal, proses utama, dan Data Store (D1 s/d D7).
   - Sertakan kode diagram Mermaid.js.

4. DFD LEVEL 1 (DEKOMPOSISI PROSES DETIL):
   - Buatkan DFD Level 1 khusus untuk Sub-Proses 3.0 (Alur Pemeriksaan Laporan Audit dari Upload Draft, Review Pimpinan, Catatan Perbaikan Auditor, hingga Approval Final).
   - Buatkan DFD Level 1 khusus untuk Sub-Proses 4.0 (Presensi Check-in/Check-out & Pengisian Logbook Harian Auditor).
   - Buatkan DFD Level 1 khusus untuk Sub-Proses 6.0 (Alur AI Chatbot AURA: Inisialisasi Sesi -> Query Konteks Database D1-D5 -> API Request ke Groq LLM -> Response & Saving Chat D7).
   - Sertakan kode diagram Mermaid.js untuk masing-masing DFD Level 1.

5. KAMUS DATA (DATA DICTIONARY):
   - Buatkan struktur Kamus Data formal untuk Data Flow utama (Input & Output termasuk Chatbot Prompt & Response).
   - Buatkan Kamus Data untuk Data Store (D1 sampai D7) beserta atribut kunci penyimpanannya.

6. FORMAT OUTPUT:
   - Format jawaban dalam Markdown yang rapi dan profesional.
   - Gunakan diagram berbasis Mermaid.js agar mudah dicopy atau dirender di Obsidian, GitHub, atau Mermaid Live Editor.
   - Bebas dari kesalahan logika seperti sinkhole, black hole, atau pemanggilan data store langsung antar data store tanpa melalui proses.

Silakan susun dokumen DFD tersebut secara lengkap dan mendalam sekarang.
```

---

## 💡 Petunjuk Cara Penggunaan Prompt Ini:

1. Salin seluruh teks di dalam kotak blok ````text ... ```` di atas.
2. Tempelkan (*paste*) pada model AI (ChatGPT / Claude / Gemini).
3. Kode **Mermaid.js** yang dihasilkan oleh AI dapat langsung Anda buka di [Mermaid Live Editor](https://mermaid.live) untuk di-export menjadi gambar High Resolution (PNG/SVG) untuk dimasukkan ke dalam dokumen Laporan Skripsi Anda.
