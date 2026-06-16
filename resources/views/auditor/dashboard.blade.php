<x-app-layout title="Dashboard Auditor">
    <div class="py-8 animate-fade-in-up">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- WELCOME HERO BANNER --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-emerald-950 via-emerald-900 to-slate-900 rounded-3xl p-6 md:p-8 text-white shadow-xl shadow-emerald-900/10 border border-emerald-700/20">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-white/10 via-transparent to-transparent"></div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <span class="bg-emerald-500/20 text-emerald-200 text-xs px-3.5 py-1 rounded-full font-semibold tracking-wide uppercase border border-emerald-500/30">Auditor Portal</span>
                        <h1 class="text-2xl md:text-4xl font-extrabold tracking-tight mt-3">Semangat Bekerja, {{ auth()->user()->name }}!</h1>
                        <p class="text-emerald-200/90 text-sm md:text-base mt-2 max-w-2xl">Pantau berkas laporan, isi kehadiran harian, perbarui logbook aktivitas, dan tindak lanjuti revisi dari pimpinan KAP.</p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-md rounded-2xl px-5 py-4 flex items-center gap-4 border border-white/10 flex-shrink-0">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/20 flex items-center justify-center border border-emerald-500/30">
                            <svg class="w-6 h-6 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-emerald-300 font-medium">Hari ini</p>
                            <p class="text-base font-bold text-white mt-0.5">{{ today()->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ALERT REVISI --}}
            @if($laporanRevisi->count() > 0)
                <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 flex items-start gap-4 shadow-sm">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0 border border-rose-200">
                        <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-extrabold text-rose-800 text-sm">Ada {{ $laporanRevisi->count() }} Laporan Butuh Tindakan Segera!</h4>
                        <p class="text-xs text-rose-600 mt-1">Laporan berikut ditolak oleh pimpinan dan memerlukan revisi/perbaikan dokumen.</p>
                        <div class="mt-4 space-y-2">
                            @foreach($laporanRevisi as $rev)
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white border border-rose-100 rounded-xl p-3.5 gap-3 shadow-sm hover:border-rose-300 transition duration-300">
                                    <div>
                                        <span class="inline-flex text-[10px] font-bold font-mono bg-rose-50 text-rose-700 px-2 py-0.5 rounded border border-rose-100">{{ $rev->report_number }}</span>
                                        <span class="text-xs font-bold text-slate-700 ml-2">{{ $rev->title }}</span>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Klien: {{ $rev->client->institution_name ?? '-' }}</p>
                                    </div>
                                    <a href="{{ route('audit_reports.edit', $rev) }}"
                                        class="bg-rose-600 hover:bg-rose-700 text-white text-xs px-5 py-2 rounded-xl font-bold transition duration-300 shadow-sm shadow-rose-600/10 text-center">
                                        Perbaiki Dokumen
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- DAILY ATTENDANCE & LOGBOOK STATUS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                {{-- Absensi Hari Ini --}}
                <div class="bg-white border border-slate-100 border-b-4 border-b-blue-600 dark:border-slate-800 rounded-2xl shadow-sm p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-700">Absensi Hari Ini</h3>
                                    <p class="text-xs text-slate-400 mt-0.5">Catatan presensi masuk & pulang</p>
                                </div>
                            </div>
                        </div>

                        @if($absensiHariIni)
                            <div class="grid grid-cols-2 gap-4 mt-2 bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-slate-400">Jam Masuk</p>
                                    <p class="text-sm font-bold text-slate-700 mt-1">{{ $absensiHariIni->check_in ?? '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] uppercase font-bold text-slate-400">Jam Pulang</p>
                                    <p class="text-sm font-bold text-slate-700 mt-1">{{ $absensiHariIni->check_out ?? '-' }}</p>
                                </div>
                            </div>
                        @else
                            <div class="mt-2 bg-slate-50 border border-slate-100 rounded-xl p-4 text-center">
                                <span class="text-xs text-slate-400 block mb-3">Anda belum mengisi absensi kehadiran hari ini.</span>
                            </div>
                        @endif
                    </div>
                    @if(!$absensiHariIni)
                        <div class="mt-4">
                            <a href="{{ route('attendances.create') }}" class="w-full text-center block bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-700 text-white text-xs px-4 py-2.5 rounded-xl font-bold transition duration-300 shadow-sm shadow-blue-600/10">
                                Absen Sekarang
                            </a>
                        </div>
                    @elseif(!$absensiHariIni->check_out)
                        <div class="mt-4">
                            <a href="{{ route('attendances.edit', $absensiHariIni) }}" class="w-full text-center block bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-xs px-4 py-2.5 rounded-xl font-bold transition duration-300 shadow-sm shadow-amber-500/10">
                                Absen Pulang
                            </a>
                        </div>
                    @else
                        <div class="mt-4 text-center">
                            <span class="inline-flex items-center bg-emerald-50 text-emerald-700 text-xs px-3 py-1.5 rounded-full font-bold border border-emerald-200 w-full justify-center">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2 animate-pulse"></span> Kehadiran Terpenuhi
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Logbook Hari Ini --}}
                <div class="bg-white border border-slate-100 border-b-4 border-b-orange-500 dark:border-slate-800 rounded-2xl shadow-sm p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-700">Logbook Hari Ini</h3>
                                    <p class="text-xs text-slate-400 mt-0.5">Catatan aktivitas pekerjaan harian</p>
                                </div>
                            </div>
                        </div>

                        @if($logbookHariIni)
                            <div class="mt-2 bg-slate-50 border border-slate-100 rounded-xl p-4">
                                <div class="flex justify-between items-start">
                                    <span class="text-[10px] uppercase font-bold text-slate-400">Pekerjaan</span>
                                    @if($logbookHariIni->status === 'approved')
                                        <span class="text-[9px] bg-emerald-50 text-emerald-700 font-bold px-1.5 py-0.5 rounded border border-emerald-200">Disetujui</span>
                                    @elseif($logbookHariIni->status === 'revision')
                                        <span class="text-[9px] bg-rose-50 text-rose-700 font-bold px-1.5 py-0.5 rounded border border-rose-200">Revisi</span>
                                    @else
                                        <span class="text-[9px] bg-amber-50 text-amber-700 font-bold px-1.5 py-0.5 rounded border border-amber-200">Review</span>
                                    @endif
                                </div>
                                <p class="text-xs font-semibold text-slate-700 mt-1 truncate">{{ $logbookHariIni->main_task ?? $logbookHariIni->activity }}</p>
                                @if($logbookHariIni->status === 'revision' && $logbookHariIni->note)
                                    <p class="text-[10px] text-rose-600 bg-rose-50 border border-rose-100 rounded-lg p-2 mt-2 font-semibold">Catatan: {{ $logbookHariIni->note }}</p>
                                @endif
                            </div>
                        @else
                            <div class="mt-2 bg-slate-50 border border-slate-100 rounded-xl p-4 text-center">
                                <span class="text-xs text-slate-400 block mb-3">Progress kerja hari ini belum dilaporkan.</span>
                            </div>
                        @endif
                    </div>
                    @if(!$logbookHariIni)
                        <div class="mt-4">
                            <a href="{{ route('logbooks.create') }}" class="w-full text-center block bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-xs px-4 py-2.5 rounded-xl font-bold transition duration-300 shadow-sm shadow-orange-500/10">
                                Isi Logbook Sekarang
                            </a>
                        </div>
                    @else
                        <div class="mt-4">
                            <a href="{{ route('logbooks.index') }}" class="w-full text-center block bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs px-4 py-2.5 rounded-xl font-bold transition duration-305">
                                Riwayat Logbook
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Ringkasan Laporan --}}
                <div class="bg-white border border-slate-100 border-b-4 border-b-teal-600 dark:border-slate-800 rounded-2xl shadow-sm p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-700">Ringkasan Laporan</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Status dokumen laporan saya</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3 text-center mt-2">
                            <div class="bg-amber-50/50 border border-amber-100 rounded-xl p-3.5">
                                <p class="text-2xl font-black text-amber-650">{{ $myPending }}</p>
                                <p class="text-[10px] text-amber-600 font-bold uppercase tracking-wider mt-1">Review</p>
                            </div>
                            <div class="bg-emerald-50/50 border border-emerald-100 rounded-xl p-3.5">
                                <p class="text-2xl font-black text-emerald-650">{{ $myApprove }}</p>
                                <p class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider mt-1">Selesai</p>
                            </div>
                            <div class="bg-rose-50/50 border border-rose-100 rounded-xl p-3.5">
                                <p class="text-2xl font-black text-rose-550">{{ $myRevisi }}</p>
                                <p class="text-[10px] text-rose-500 font-bold uppercase tracking-wider mt-1">Revisi</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('audit_reports.index') }}" class="w-full text-center block bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 text-white text-xs px-4 py-2.5 rounded-xl font-bold transition duration-300 shadow-sm shadow-teal-600/10">
                            Semua Laporan
                        </a>
                    </div>
                </div>

                {{-- Tugas Saya (Dokumen) --}}
                <div class="bg-white border border-slate-100 border-b-4 border-b-indigo-650 dark:border-slate-800 rounded-2xl shadow-sm p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-650 flex items-center justify-center border border-indigo-100/50">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-700">Tugas Saya (Dokumen)</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Status dokumen review</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2.5 text-center mt-2">
                            <div class="bg-rose-50/50 border border-rose-100 rounded-xl p-2.5">
                                <p class="text-lg font-black text-rose-550">{{ $tugasRevisi }}</p>
                                <p class="text-[9px] text-rose-600 font-bold uppercase tracking-wider mt-1">Revisi</p>
                            </div>
                            <div class="bg-amber-50/50 border border-amber-100 rounded-xl p-2.5">
                                <p class="text-lg font-black text-amber-550">{{ $tugasDalamReview }}</p>
                                <p class="text-[9px] text-amber-600 font-bold uppercase tracking-wider mt-1">Review</p>
                            </div>
                            <div class="bg-emerald-50/50 border border-emerald-100 rounded-xl p-2.5">
                                <p class="text-lg font-black text-emerald-550">{{ $tugasDisetujui }}</p>
                                <p class="text-[9px] text-emerald-600 font-bold uppercase tracking-wider mt-1">Selesai</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('audit_reports.index') }}" class="w-full text-center block bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-xs px-4 py-2.5 rounded-xl font-bold transition duration-300 shadow-sm shadow-indigo-600/10">
                            Kelola Dokumen
                        </a>
                    </div>
                </div>

            </div>

            {{-- AKSI CEPAT --}}
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Navigasi Aktivitas</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <a href="{{ route('audit_reports.index') }}"
                        class="bg-white border border-slate-100 hover:border-blue-200 rounded-2xl p-5 flex flex-col items-center gap-3 transition duration-300 text-center shadow-sm hover:shadow-md group">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform border border-blue-100/50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Kelola Laporan</span>
                    </a>
                    <a href="{{ route('logbooks.create') }}"
                        class="bg-white border border-slate-100 hover:border-orange-200 rounded-2xl p-5 flex flex-col items-center gap-3 transition duration-300 text-center shadow-sm hover:shadow-md group">
                        <div class="w-12 h-12 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center group-hover:scale-110 transition-transform border border-orange-100/50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Tambah Logbook</span>
                    </a>
                    <a href="{{ route('attendances.create') }}"
                        class="bg-white border border-slate-100 hover:border-teal-200 rounded-2xl p-5 flex flex-col items-center gap-3 transition duration-300 text-center shadow-sm hover:shadow-md group">
                        <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center group-hover:scale-110 transition-transform border border-teal-100/50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-700">Presensi Kehadiran</span>
                    </a>
                    <a href="{{ route('clients.index') }}"
                        class="bg-white border border-slate-100 hover:border-indigo-200 rounded-2xl p-5 flex flex-col items-center gap-3 transition duration-300 text-center shadow-sm hover:shadow-md group">
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform border border-indigo-100/50">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>   
                        </div>
                        <span class="text-xs font-bold text-slate-700">Daftar Klien</span>
                    </a>
                </div>
            </div>

            {{-- LAPORAN TERBARU SAYA --}}
            <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Daftar Laporan Saya</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Daftar laporan audit yang terakhir kali Anda ajukan</p>
                    </div>
                    <a href="{{ route('audit_reports.index') }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold">Lihat Semua →</a>
                </div>
                @if($laporanTerbaru->isEmpty())
                    <div class="py-12 text-center bg-slate-50 border border-dashed border-slate-200 rounded-2xl">
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-slate-400 text-xs font-medium">Belum ada dokumen laporan audit yang tercatat.</p>
                        <a href="{{ route('audit_reports.create') }}" class="mt-3 inline-flex bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-4 py-2 rounded-xl font-bold transition duration-300">
                            Buat Laporan Pertama
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($laporanTerbaru as $laporan)
                            <div class="flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100/50 border border-slate-100 hover:border-slate-200 rounded-2xl transition duration-300">
                                <div class="flex-1 min-w-0 mr-4">
                                    <div class="flex items-center gap-2 mb-1.5">
                                        <span class="inline-flex text-[9px] font-bold font-mono bg-slate-100 text-slate-600 px-2 py-0.5 rounded border border-slate-200/50">{{ $laporan->report_number }}</span>
                                        <span class="text-[10px] text-slate-400 font-semibold">{{ $laporan->audit_date->format('d M Y') }}</span>
                                    </div>
                                    <p class="font-bold text-slate-800 text-sm truncate">{{ $laporan->title }}</p>
                                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">Klien: {{ $laporan->client->institution_name ?? '-' }}</p>
                                </div>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    @if($laporan->status === 'review')
                                        <span class="bg-amber-50 text-amber-700 font-bold text-[10px] px-2.5 py-1 rounded-full border border-amber-200">Review</span>
                                    @elseif($laporan->status === 'disetujui')
                                        <span class="bg-emerald-50 text-emerald-700 font-bold text-[10px] px-2.5 py-1 rounded-full border border-emerald-200">Selesai</span>
                                    @else
                                        <span class="bg-rose-50 text-rose-700 font-bold text-[10px] px-2.5 py-1 rounded-full border border-rose-200">Revisi</span>
                                    @endif
                                    <a href="{{ route('audit_reports.show', $laporan) }}" class="w-8 h-8 rounded-full bg-white hover:bg-indigo-50 border border-slate-100 flex items-center justify-center text-slate-500 hover:text-indigo-600 transition shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
