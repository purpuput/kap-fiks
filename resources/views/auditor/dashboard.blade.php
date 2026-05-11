<x-app-layout title="Dashboard Auditor">
<div class="py-10">
<div class="max-w-7xl mx-auto px-4 space-y-6">

    {{-- ALERT REVISI --}}
    @if($laporanRevisi->count() > 0)
    <div class="bg-red-50 border-l-4 border-red-400 rounded-xl p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <div class="flex-1">
            <p class="font-semibold text-red-700 text-sm">{{ $laporanRevisi->count() }} laporan perlu diperbaiki!</p>
            <div class="mt-2 space-y-1">
                @foreach($laporanRevisi as $rev)
                <div class="flex items-center justify-between bg-white border border-red-100 rounded-lg px-3 py-2">
                    <div>
                        <span class="text-xs font-semibold text-gray-700">{{ $rev->report_number }}</span>
                        <span class="text-xs text-gray-400 ml-2">— {{ $rev->client->institution_name ?? '-' }}</span>
                    </div>
                    <a href="{{ route('audit_reports.edit', $rev) }}"
                        class="text-xs bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg font-semibold transition">
                        Perbaiki
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- STATUS HARI INI --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- Absensi Hari Ini --}}
        <div class="bg-white rounded-2xl shadow p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm font-bold text-gray-700">Absensi Hari Ini</p>
            </div>
            @if($absensiHariIni)
                @if($absensiHariIni->check_in && $absensiHariIni->check_out)
                <span class="bg-green-100 text-green-700 text-xs px-3 py-1.5 rounded-full font-semibold">✓ Lengkap</span>
                <p class="text-xs text-gray-400 mt-2">Masuk: {{ $absensiHariIni->check_in }} · Pulang: {{ $absensiHariIni->check_out }}</p>
                @elseif($absensiHariIni->check_in)
                <span class="bg-yellow-100 text-yellow-700 text-xs px-3 py-1.5 rounded-full font-semibold">⏳ Belum Pulang</span>
                <p class="text-xs text-gray-400 mt-2">Masuk: {{ $absensiHariIni->check_in }}</p>
                @endif
            @else
                <span class="bg-red-100 text-red-600 text-xs px-3 py-1.5 rounded-full font-semibold">✗ Belum Absen</span>
                <div class="mt-3">
                    <a href="{{ route('attendances.create') }}"
                        class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-4 py-1.5 rounded-lg font-semibold transition inline-block">
                        Absen Sekarang
                    </a>
                </div>
            @endif
        </div>

        {{-- Logbook Hari Ini --}}
        <div class="bg-white rounded-2xl shadow p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <p class="text-sm font-bold text-gray-700">Logbook Hari Ini</p>
            </div>
            @if($logbookHariIni)
                @if($logbookHariIni->status === 'approved')
                <span class="bg-green-100 text-green-700 text-xs px-3 py-1.5 rounded-full font-semibold">✓ Disetujui</span>
                @elseif($logbookHariIni->status === 'revision')
                <span class="bg-red-100 text-red-700 text-xs px-3 py-1.5 rounded-full font-semibold">↩ Perlu Revisi</span>
                <p class="text-xs text-red-500 mt-1 truncate">{{ $logbookHariIni->note }}</p>
                @else
                <span class="bg-yellow-100 text-yellow-700 text-xs px-3 py-1.5 rounded-full font-semibold">⏳ Menunggu</span>
                @endif
                <p class="text-xs text-gray-400 mt-2 truncate">{{ Str::limit($logbookHariIni->activity, 60) }}</p>
            @else
                <span class="bg-gray-100 text-gray-500 text-xs px-3 py-1.5 rounded-full font-semibold">Belum diisi</span>
                <div class="mt-3">
                    <a href="{{ route('logbooks.create') }}"
                        class="text-xs bg-orange-500 hover:bg-orange-600 text-white px-4 py-1.5 rounded-lg font-semibold transition inline-block">
                        Isi Logbook
                    </a>
                </div>
            @endif
        </div>

        {{-- Progress Laporan --}}
        <div class="bg-white rounded-2xl shadow p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-teal-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-sm font-bold text-gray-700">Laporan Saya</p>
            </div>
            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="bg-yellow-50 rounded-xl p-2">
                    <p class="text-xl font-extrabold text-yellow-600">{{ $myPending }}</p>
                    <p class="text-xs text-gray-400">Pending</p>
                </div>
                <div class="bg-green-50 rounded-xl p-2">
                    <p class="text-xl font-extrabold text-green-600">{{ $myApprove }}</p>
                    <p class="text-xs text-gray-400">Approve</p>
                </div>
                <div class="bg-red-50 rounded-xl p-2">
                    <p class="text-xl font-extrabold text-red-500">{{ $myRevisi }}</p>
                    <p class="text-xs text-gray-400">Revisi</p>
                </div>
            </div>
        </div>
    </div>

    {{-- AKSI CEPAT --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('audit_reports.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white rounded-2xl p-4 flex flex-col items-center gap-2 transition text-center group">
            <svg class="w-7 h-7 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span class="text-xs font-semibold">Buat Laporan</span>
        </a>
        <a href="{{ route('logbooks.create') }}"
            class="bg-orange-500 hover:bg-orange-600 text-white rounded-2xl p-4 flex flex-col items-center gap-2 transition text-center group">
            <svg class="w-7 h-7 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            <span class="text-xs font-semibold">Tambah Logbook</span>
        </a>
        <a href="{{ route('attendances.create') }}"
            class="bg-teal-600 hover:bg-teal-700 text-white rounded-2xl p-4 flex flex-col items-center gap-2 transition text-center group">
            <svg class="w-7 h-7 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-xs font-semibold">Absensi</span>
        </a>
        <a href="{{ route('clients.create') }}"
            class="bg-indigo-500 hover:bg-indigo-600 text-white rounded-2xl p-4 flex flex-col items-center gap-2 transition text-center group">
            <svg class="w-7 h-7 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            <span class="text-xs font-semibold">Tambah Klien</span>
        </a>
    </div>

    {{-- LAPORAN TERBARU SAYA --}}
    <div class="bg-white rounded-2xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Laporan Saya</h3>
            <a href="{{ route('audit_reports.index') }}" class="text-blue-600 text-xs hover:underline font-semibold">Lihat semua →</a>
        </div>
        @if($laporanTerbaru->isEmpty())
        <div class="py-8 text-center">
            <svg class="w-12 h-12 text-gray-200 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <p class="text-gray-400 text-sm">Belum ada laporan</p>
            <a href="{{ route('audit_reports.create') }}" class="mt-2 inline-block text-xs text-blue-600 hover:underline font-semibold">Buat laporan pertama →</a>
        </div>
        @else
        <div class="space-y-3">
            @foreach($laporanTerbaru as $laporan)
            <div class="flex items-center justify-between p-3 bg-gray-50 hover:bg-gray-100 rounded-xl transition">
                <div class="flex-1 min-w-0 mr-4">
                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $laporan->title }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $laporan->report_number }} · {{ $laporan->client->institution_name ?? '-' }} · {{ $laporan->audit_date->format('d M Y') }}</p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    @if($laporan->status === 'pending') <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full font-medium">Pending</span>
                    @elseif($laporan->status === 'approve') <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-medium">Approve</span>
                    @else <span class="bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full font-medium">Revisi</span>
                    @endif
                    <a href="{{ route('audit_reports.show', $laporan) }}" class="text-blue-600 hover:text-blue-800">
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
