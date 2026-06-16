<x-app-layout title="Dashboard Admin">
    <div class="py-8 animate-fade-in-up">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- WELCOME HERO BANNER --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-indigo-950 via-indigo-900 to-slate-900 rounded-3xl p-6 md:p-8 text-white shadow-xl shadow-indigo-900/10 border border-indigo-700/20">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-white/10 via-transparent to-transparent"></div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <span class="bg-indigo-500/20 text-indigo-200 text-xs px-3.5 py-1 rounded-full font-semibold tracking-wide uppercase border border-indigo-500/30">Administrator Portal</span>
                        <h1 class="text-2xl md:text-4xl font-extrabold tracking-tight mt-3">Selamat Datang Kembali, {{ auth()->user()->name }}!</h1>
                        <p class="text-indigo-200/90 text-sm md:text-base mt-2 max-w-2xl">Kelola seluruh aktivitas audit, data klien, laporan keuangan, logbook, dan presensi auditor secara mudah dan terintegrasi dari satu portal admin.</p>
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

            {{-- STAT CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
                {{-- Total User --}}
                <div class="bg-white border border-slate-100 border-b-4 border-b-blue-600 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-6 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total User</span>
                        <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-black text-slate-800 tracking-tight">{{ $totalUsers }}</p>
                        <p class="text-xs text-slate-500 mt-2 flex items-center gap-1.5 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> {{ $totalAuditor }} Auditor Aktif
                        </p>
                    </div>
                </div>

                {{-- Total Klien --}}
                <div class="bg-white border border-slate-100 border-b-4 border-b-indigo-600 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-6 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Klien</span>
                        <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-black text-slate-800 tracking-tight">{{ $totalKlien }}</p>
                        <p class="text-xs text-slate-500 mt-2 font-semibold">Klien KAP terdaftar</p>
                    </div>
                </div>

                {{-- Laporan Audit --}}
                <div class="bg-white border border-slate-100 border-b-4 border-b-emerald-600 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-6 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Laporan Audit</span>
                        <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-black text-slate-800 tracking-tight">{{ $totalLaporan }}</p>
                        <p class="text-xs text-slate-500 mt-2 font-semibold">Berkas laporan dibuat</p>
                    </div>
                </div>

                {{-- Dokumen Terarsip --}}
                <div class="bg-white border border-slate-100 border-b-4 border-b-purple-600 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-6 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Dok. Terarsip</span>
                        <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-black text-slate-800 tracking-tight">{{ $totalDokumen }}</p>
                        <p class="text-xs text-slate-500 mt-2 font-semibold">Dari laporan disetujui</p>
                    </div>
                </div>

                {{-- Logbook Auditor --}}
                <div class="bg-white border border-slate-100 border-b-4 border-b-orange-500 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 p-6 flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Logbook</span>
                        <div class="w-11 h-11 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center border border-orange-100/50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-3xl font-black text-slate-800 tracking-tight">{{ $logbookPending + $logbookApprove + $logbookRevisi }}</p>
                        <div class="flex gap-1.5 mt-2 flex-wrap">
                            <span class="text-[9px] bg-yellow-50 text-yellow-700 px-2.5 py-0.5 rounded-md font-extrabold">{{ $logbookPending }} pending</span>
                            <span class="text-[9px] bg-green-50 text-green-700 px-2.5 py-0.5 rounded-md font-extrabold">{{ $logbookApprove }} approve</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STATUS PRIBADI ADMIN --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Absensi Hari Ini --}}
                <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 hover:shadow-md transition duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-700">Absensi Saya Hari Ini</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Catatan presensi masuk dan pulang</p>
                            </div>
                        </div>
                        <div>
                            @if($absensiSayaHariIni)
                                @if($absensiSayaHariIni->check_in && $absensiSayaHariIni->check_out)
                                    <span class="inline-flex items-center bg-emerald-50 text-emerald-700 text-xs px-3 py-1 rounded-full font-bold border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span> Lengkap
                                    </span>
                                @elseif($absensiSayaHariIni->check_in)
                                    <span class="inline-flex items-center bg-amber-50 text-amber-700 text-xs px-3 py-1 rounded-full font-bold border border-amber-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span> Belum Pulang
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center bg-rose-50 text-rose-700 text-xs px-3 py-1 rounded-full font-bold border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span> Belum Absen
                                </span>
                            @endif
                        </div>
                    </div>
                    @if($absensiSayaHariIni)
                        <div class="grid grid-cols-2 gap-4 mt-2 bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <div>
                                <p class="text-[10px] uppercase font-bold text-slate-400">Jam Masuk</p>
                                <p class="text-sm font-bold text-slate-700 mt-1">{{ $absensiSayaHariIni->check_in ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-slate-400">Jam Pulang</p>
                                <p class="text-sm font-bold text-slate-700 mt-1">{{ $absensiSayaHariIni->check_out ?? '-' }}</p>
                            </div>
                        </div>
                    @else
                        <div class="mt-4 flex items-center justify-between bg-slate-50 border border-slate-100 rounded-xl p-4">
                            <span class="text-xs text-slate-400">Anda belum mencatat presensi kehadiran hari ini.</span>
                            <a href="{{ route('attendances.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-4 py-2 rounded-xl font-bold transition duration-300 shadow-sm shadow-indigo-600/10">
                                Absen Sekarang
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Logbook Hari Ini --}}
                <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 hover:shadow-md transition duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-700">Logbook Saya Hari Ini</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Catatan aktivitas pekerjaan harian</p>
                            </div>
                        </div>
                        <div>
                            @if($logbookSayaHariIni)
                                @if($logbookSayaHariIni->status === 'approved')
                                    <span class="inline-flex items-center bg-emerald-50 text-emerald-700 text-xs px-3 py-1 rounded-full font-bold border border-emerald-200">
                                        ✓ Disetujui
                                    </span>
                                @elseif($logbookSayaHariIni->status === 'revision')
                                    <span class="inline-flex items-center bg-rose-50 text-rose-700 text-xs px-3 py-1 rounded-full font-bold border border-rose-200">
                                        ↩ Revisi
                                    </span>
                                @else
                                    <span class="inline-flex items-center bg-amber-50 text-amber-700 text-xs px-3 py-1 rounded-full font-bold border border-amber-200">
                                        ⏳ Review
                                    </span>
                                @endif
                            @else
                                <span class="inline-flex items-center bg-slate-50 text-slate-500 text-xs px-3 py-1 rounded-full font-bold border border-slate-200">
                                    Belum Diisi
                                </span>
                            @endif
                        </div>
                    </div>
                    @if($logbookSayaHariIni)
                        <div class="mt-2 bg-slate-50 border border-slate-100 rounded-xl p-4">
                            <p class="text-[10px] uppercase font-bold text-slate-400">Pekerjaan Utama</p>
                            <p class="text-xs font-semibold text-slate-700 mt-1 truncate">{{ $logbookSayaHariIni->main_task ?? $logbookSayaHariIni->activity }}</p>
                            @if($logbookSayaHariIni->status === 'revision' && $logbookSayaHariIni->note)
                                <p class="text-xs text-rose-600 bg-rose-50 border border-rose-100 rounded-lg p-2 mt-2 font-medium">Catatan: {{ $logbookSayaHariIni->note }}</p>
                            @endif
                        </div>
                    @else
                        <div class="mt-4 flex items-center justify-between bg-slate-50 border border-slate-100 rounded-xl p-4">
                            <span class="text-xs text-slate-400">Catat progress harian Anda hari ini.</span>
                            <a href="{{ route('logbooks.create') }}" class="bg-orange-500 hover:bg-orange-600 text-white text-xs px-4 py-2 rounded-xl font-bold transition duration-300 shadow-sm shadow-orange-500/10">
                                Isi Logbook
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- SECONDARY COLUMNS: STATUS LAPORAN & QUICK LINKS --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Status Laporan Card --}}
                <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 md:col-span-2">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Status Laporan Audit</h3>
                            <p class="text-xs text-slate-400 mt-0.5">Persentase status persetujuan laporan audit</p>
                        </div>
                        <span class="bg-slate-50 text-slate-600 text-xs px-3 py-1 rounded-full font-bold border border-slate-100">
                            Total: {{ $totalLaporan }}
                        </span>
                    </div>

                    @php $tot = max($totalLaporan, 1); @endphp
                    <div class="space-y-5">
                        {{-- Pending Review --}}
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1.5">
                                <span class="text-amber-600 flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400 border border-amber-300"></span> Pending Review
                                </span>
                                <span class="text-slate-700">{{ $laporanPending }} <span class="text-slate-400 font-normal">laporan ({{ round(($laporanPending/$tot)*100) }}%)</span></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-3">
                                <div class="bg-amber-400 h-3 rounded-full transition-all duration-500" style="width: {{ ($laporanPending/$tot)*100 }}%"></div>
                            </div>
                        </div>

                        {{-- Disetujui --}}
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1.5">
                                <span class="text-emerald-600 flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 border border-emerald-300"></span> Disetujui
                                </span>
                                <span class="text-slate-700">{{ $laporanApprove }} <span class="text-slate-400 font-normal">laporan ({{ round(($laporanApprove/$tot)*100) }}%)</span></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-3">
                                <div class="bg-emerald-500 h-3 rounded-full transition-all duration-500" style="width: {{ ($laporanApprove/$tot)*100 }}%"></div>
                            </div>
                        </div>

                        {{-- Perlu Revisi --}}
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1.5">
                                <span class="text-rose-600 flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-400 border border-rose-300"></span> Perlu Revisi
                                </span>
                                <span class="text-slate-700">{{ $laporanRevisi }} <span class="text-slate-400 font-normal">laporan ({{ round(($laporanRevisi/$tot)*100) }}%)</span></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-3">
                                <div class="bg-rose-500 h-3 rounded-full transition-all duration-500" style="width: {{ ($laporanRevisi/$tot)*100 }}%"></div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 grid grid-cols-3 gap-4 text-center">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 hover:bg-slate-100/50 transition">
                                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Audit Keuangan</p>
                                <p class="font-extrabold text-blue-700 text-xl mt-1">{{ $laporanKeuangan }}</p>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 hover:bg-slate-100/50 transition">
                                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Audit Kepatuhan</p>
                                <p class="font-extrabold text-purple-700 text-xl mt-1">{{ $laporanKepatuhan }}</p>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 hover:bg-slate-100/50 transition">
                                <p class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Absen Hari Ini</p>
                                <p class="font-extrabold text-teal-700 text-xl mt-1">{{ $absensiHariIni }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Links Card --}}
                <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <h3 class="text-sm font-bold text-slate-700 mb-1 uppercase tracking-wider">Akses Cepat</h3>
                        <p class="text-xs text-slate-400 mb-4">Navigasi pintasan administratif</p>
                        <div class="space-y-2.5">
                            <a href="{{ route('admin.users.index') }}"
                                class="flex items-center justify-between p-3.5 bg-blue-50/50 hover:bg-blue-50 border border-blue-100/20 hover:border-blue-100 rounded-xl transition duration-300 group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                    <span class="text-xs font-bold text-blue-800">Manajemen User</span>
                                </div>
                                <svg class="w-4 h-4 text-blue-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                            <a href="{{ route('audit_reports.index') }}"
                                class="flex items-center justify-between p-3.5 bg-teal-50/50 hover:bg-teal-50 border border-teal-100/20 hover:border-teal-100 rounded-xl transition duration-300 group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <span class="text-xs font-bold text-teal-800">Laporan Audit</span>
                                </div>
                                <svg class="w-4 h-4 text-teal-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                            <a href="{{ route('document_managements.index') }}"
                                class="flex items-center justify-between p-3.5 bg-purple-50/50 hover:bg-purple-50 border border-purple-100/20 hover:border-purple-100 rounded-xl transition duration-300 group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                    </div>
                                    <span class="text-xs font-bold text-purple-800">Manajemen Dokumen</span>
                                </div>
                                <svg class="w-4 h-4 text-purple-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                            <a href="{{ route('clients.index') }}"
                                class="flex items-center justify-between p-3.5 bg-indigo-50/50 hover:bg-indigo-50 border border-indigo-100/20 hover:border-indigo-100 rounded-xl transition duration-300 group">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <span class="text-xs font-bold text-indigo-800">Data Klien</span>
                                </div>
                                <svg class="w-4 h-4 text-indigo-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LAPORAN TERBARU & AUDITOR WORKLOAD --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Laporan Audit Terbaru --}}
                <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6 lg:col-span-2">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-md font-bold text-slate-700 uppercase tracking-wider">Laporan Audit Terbaru</h3>
                            <p class="text-sm text-slate-400 mt-0.5">Daftar laporan audit yang terakhir kali diperbarui</p>
                        </div>
                        <a href="{{ route('audit_reports.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-bold">Lihat Semua →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 text-slate-500 text-sm uppercase font-bold tracking-wider">
                                <tr>
                                    <th class="px-4 py-3 text-left rounded-l-xl">No. Laporan</th>
                                    <th class="px-4 py-3 text-left">Judul</th>
                                    <th class="px-4 py-3 text-left">Klien</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-center rounded-r-xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($laporanTerbaru as $laporan)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="px-4 py-3.5 font-mono text-sm text-slate-500 font-bold">{{ $laporan->report_number }}</td>
                                        <td class="px-4 py-3.5 font-bold text-slate-800 text-md max-w-xs truncate">{{ $laporan->title }}</td>
                                        <td class="px-4 py-3.5 text-sm text-slate-500 font-medium">{{ $laporan->client->institution_name ?? '-' }}</td>
                                        <td class="px-4 py-3.5">
                                            @if($laporan->status === 'review')
                                                <span class="inline-flex items-center bg-amber-50 text-amber-700 text-sm px-2.5 py-1 rounded-full font-bold border border-amber-200">
                                                    Review
                                                </span>
                                            @elseif($laporan->status === 'disetujui')
                                                <span class="inline-flex items-center bg-emerald-50 text-emerald-700 text-sm px-2.5 py-1 rounded-full font-bold border border-emerald-200">
                                                    Disetujui
                                                </span>
                                            @else
                                                <span class="inline-flex items-center bg-rose-50 text-rose-700 text-sm px-2.5 py-1 rounded-full font-bold border border-rose-200">
                                                    Revisi
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5 text-center">
                                            <a href="{{ route('audit_reports.show', $laporan) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-bold">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-slate-400 text-xs font-medium">Belum ada laporan audit</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Auditor Workload List --}}
                <div class="bg-white border border-slate-100 rounded-2xl shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-1">Beban Kerja Auditor</h3>
                    <p class="text-xs text-slate-400 mb-5">Jumlah laporan audit yang ditangani oleh tiap auditor</p>
                    <div class="space-y-4">
                        @forelse($auditors as $auditor)
                            <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center text-sm">
                                        {{ strtoupper(substr($auditor->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-700">{{ $auditor->name }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium">{{ $auditor->email }}</p>
                                    </div>
                                </div>
                                <span class="bg-indigo-50 border border-indigo-100 text-indigo-700 font-extrabold text-xs px-2.5 py-1 rounded-lg">
                                    {{ $auditor->audit_reports_count }} Lap.
                                </span>
                            </div>
                        @empty
                            <p class="text-center text-slate-400 text-xs py-6 font-medium">Belum ada auditor terdaftar</p>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>