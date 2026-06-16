<x-app-layout title="Dashboard Pimpinan">
    <div class="py-8 animate-fade-in-up">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- WELCOME HERO BANNER --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 rounded-3xl p-6 md:p-8 text-white shadow-xl shadow-indigo-950/10 border border-indigo-900/35">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-white/10 via-transparent to-transparent"></div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <span class="bg-indigo-500/20 text-indigo-200 text-xs px-3.5 py-1 rounded-full font-semibold tracking-wide uppercase border border-indigo-500/30">Pimpinan Portal</span>
                        <h1 class="text-2xl md:text-4xl font-extrabold tracking-tight mt-3">Selamat Datang, {{ auth()->user()->name }}!</h1>
                        <p class="text-indigo-200/90 text-sm md:text-base mt-2 max-w-2xl">Pantau seluruh proses kinerja auditor, tinjau dokumen laporan audit, berikan persetujuan akhir, serta evaluasi presensi kerja harian.</p>
                    </div>
                    <div class="bg-white/5 backdrop-blur-md rounded-2xl px-5 py-4 flex items-center gap-4 border border-white/10 flex-shrink-0">
                        <div class="w-12 h-12 rounded-xl bg-indigo-500/20 flex items-center justify-center border border-indigo-500/30">
                            <svg class="w-6 h-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-indigo-300 font-medium">Hari ini</p>
                            <p class="text-base font-bold text-white mt-0.5">{{ today()->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ALERT: LAPORAN MENUNGGU REVIEW --}}
            @if($laporanMenunggu->count() > 0)
                <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-5 md:p-6 shadow-sm">
                    <div class="flex items-center gap-3.5 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center border border-amber-200">
                            <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-amber-800 text-sm">{{ $laporanMenunggu->count() }} Laporan Menunggu Persetujuan Anda</h4>
                            <p class="text-xs text-amber-600/90">Laporan audit memerlukan verifikasi akhir dan tanda tangan digital Anda untuk diselesaikan.</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @foreach($laporanMenunggu->take(3) as $laporan)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between bg-white border border-amber-100 rounded-xl p-4 gap-3 shadow-sm hover:border-amber-300 transition duration-300">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="inline-flex text-[9px] font-bold font-mono bg-amber-50 text-amber-700 px-2 py-0.5 rounded border border-amber-200/50">{{ $laporan->report_number }}</span>
                                        <span class="text-[10px] text-slate-400 font-semibold">{{ $laporan->audit_date->format('d M Y') }}</span>
                                    </div>
                                    <p class="font-bold text-slate-800 text-sm truncate">{{ $laporan->title }}</p>
                                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                                        Klien: {{ $laporan->client->institution_name ?? '-' }}
                                        @if($laporan->client->auditors->count())
                                            · Oleh: {{ $laporan->client->auditors->pluck('name')->join(', ') }}
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <a href="{{ route('audit_reports.show', $laporan) }}"
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-4 py-2 rounded-xl font-bold transition shadow-sm shadow-indigo-600/10">
                                        Tinjau Detail
                                    </a>
                                </div>
                            </div>
                        @endforeach
                        @if($laporanMenunggu->count() > 3)
                            <a href="{{ route('audit_reports.index', ['status' => 'review']) }}"
                                class="block text-center text-amber-700 text-xs font-bold hover:underline mt-2">
                                + {{ $laporanMenunggu->count() - 3 }} Laporan Menunggu Lainnya →
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-center gap-3.5 shadow-sm">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center border border-emerald-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-emerald-700 text-xs font-bold">Semua laporan audit telah ditinjau. Tidak ada dokumen yang tertunda.</p>
                </div>
            @endif

            {{-- STAT CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
                {{-- Total Laporan --}}
                <div class="bg-white border border-slate-100 border-b-4 border-b-blue-600 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Laporan</span>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-black text-slate-800 tracking-tight">{{ $totalLaporan }}</p>
                        <div class="flex gap-1.5 mt-2 flex-wrap text-[9px] font-bold">
                            <span class="bg-yellow-50 text-yellow-700 px-1.5 py-0.5 rounded">Review: {{ $laporanPending }}</span>
                            <span class="bg-green-50 text-green-700 px-1.5 py-0.5 rounded">Disetujui: {{ $laporanApprove }}</span>
                        </div>
                    </div>
                </div>

                {{-- Dokumen Terarsip --}}
                <div class="bg-white border border-slate-100 border-b-4 border-b-purple-650 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Dok. Terarsip</span>
                        <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-black text-slate-800 tracking-tight">{{ $totalDokumen }}</p>
                        <p class="text-[10px] text-slate-400 mt-2 font-medium">Dari {{ $laporanApprove }} laporan selesai</p>
                    </div>
                </div>

                {{-- Data Klien --}}
                <div class="bg-white border border-slate-100 border-b-4 border-b-indigo-650 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Data Klien</span>
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-black text-slate-800 tracking-tight">{{ $totalKlien }}</p>
                        <p class="text-[10px] text-slate-400 mt-2 font-medium">Klien aktif terdaftar</p>
                    </div>
                </div>

                {{-- Logbook Auditor --}}
                <div class="bg-white border border-slate-100 border-b-4 border-b-orange-500 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Logbook</span>
                        <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center border border-orange-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col justify-between h-full">
                        <p class="text-3xl font-black text-slate-800 tracking-tight">{{ $logbookPending + $logbookApprove + $logbookRevisi }}</p>
                        <a href="{{ route('logbooks.index') }}" class="text-[10px] text-indigo-650 hover:text-indigo-850 mt-2 font-bold flex items-center justify-between transition group">
                            Tinjau Logbook 
                            <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Absensi Hari Ini --}}
                <div class="bg-white border border-slate-100 border-b-4 border-b-teal-600 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-5 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Absensi Hari Ini</span>
                        <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center border border-teal-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-black text-slate-800 tracking-tight">{{ $absensiHariIni }}</p>
                        <p class="text-[10px] text-slate-400 mt-2 font-medium">Auditor telah absen</p>
                    </div>
                </div>
            </div>

            {{-- SECONDARY COLUMNS: CHARTS & NEWEST ARCHIVES --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Jenis Audit & Persetujuan Card --}}
                <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="mb-5">
                            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Distribusi Jenis Audit</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Statistik pengerjaan jenis audit keuangan dan kepatuhan</p>
                        </div>
                        @php $tot = max($totalLaporan, 1); @endphp
                        <div class="space-y-4 mb-4">
                            <div>
                                <div class="flex justify-between text-xs font-bold mb-1">
                                    <span class="text-blue-600">Audit Keuangan</span>
                                    <span class="text-slate-700">{{ $laporanKeuangan }} <span class="text-slate-400 font-normal">lap.</span></span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2.5">
                                    <div class="bg-blue-500 h-2.5 rounded-full" style="width: {{ ($laporanKeuangan/$tot)*100 }}%"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between text-xs font-bold mb-1">
                                    <span class="text-purple-600">Audit Kepatuhan</span>
                                    <span class="text-slate-700">{{ $laporanKepatuhan }} <span class="text-slate-400 font-normal">lap.</span></span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2.5">
                                    <div class="bg-purple-500 h-2.5 rounded-full" style="width: {{ ($laporanKepatuhan/$tot)*100 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pt-4 border-t border-slate-100">
                        <p class="text-xs text-slate-400 mb-2 font-bold uppercase tracking-wider">Tingkat Penyelesaian Laporan</p>
                        @php $approveRate = $totalLaporan > 0 ? round(($laporanApprove/$totalLaporan)*100) : 0; @endphp
                        <div class="flex items-center gap-3">
                            <div class="flex-1 bg-slate-100 rounded-full h-4 overflow-hidden">
                                <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-4 rounded-full transition-all duration-700"
                                    style="width: {{ $approveRate }}%">
                                </div>
                            </div>
                            <span class="text-base font-extrabold text-emerald-600 w-12 text-right">{{ $approveRate }}%</span>
                        </div>
                    </div>
                </div>

                {{-- Dokumen Terarsip Terbaru --}}
                <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Dokumen Terarsip Terbaru</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Daftar berkas laporan audit yang baru saja disetujui</p>
                        </div>
                        <a href="{{ route('document_managements.index') }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold">Lihat Semua →</a>
                    </div>
                    @if($dokumenTerbaru->isEmpty())
                        <div class="py-10 text-center bg-slate-50 border border-dashed border-slate-200 rounded-2xl">
                            <svg class="w-10 h-10 text-slate-300 mx-auto mb-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <p class="text-slate-400 text-xs font-semibold">Belum ada dokumen yang terarsip.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($dokumenTerbaru as $dok)
                                <div class="flex items-center gap-4 p-3 bg-slate-50 hover:bg-slate-100/50 border border-slate-100 hover:border-slate-200 rounded-2xl transition duration-300">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-slate-800 truncate">{{ $dok->document_name }}</p>
                                        <p class="text-[10px] text-slate-400 font-semibold mt-0.5">{{ $dok->auditReport?->client?->institution_name ?? '-' }} · {{ $dok->approved_at?->format('d M Y') }}</p>
                                    </div>
                                    <a href="{{ route('document_managements.download', $dok) }}"
                                        class="w-8 h-8 rounded-full bg-white hover:bg-indigo-50 border border-slate-100 flex items-center justify-center text-slate-500 hover:text-indigo-600 transition shadow-sm flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
