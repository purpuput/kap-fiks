<x-app-layout title="Dashboard Pimpinan">
<div class="py-10">
<div class="max-w-7xl mx-auto px-4 space-y-6">

    {{-- ALERT: LAPORAN MENUNGGU REVIEW --}}
    @if($laporanMenunggu->count() > 0)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 rounded-xl p-4">
        <div class="flex items-center gap-2 mb-3">
            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
            </svg>
            <p class="font-semibold text-yellow-800 text-sm">
                {{ $laporanMenunggu->count() }} laporan menunggu persetujuan Anda
            </p>
        </div>
        <div class="space-y-2">
            @foreach($laporanMenunggu->take(3) as $laporan)
            <div class="flex items-center justify-between bg-white border border-yellow-100 rounded-xl px-4 py-3">
                <div class="flex-1 min-w-0 mr-4">
                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $laporan->title }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $laporan->report_number }} · {{ $laporan->client->institution_name ?? '-' }}
                        @if($laporan->client->auditors->count())
                        · oleh {{ $laporan->client->auditors->pluck('name')->join(', ') }}
                        @endif
                    </p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <form action="{{ route('audit_reports.approve', $laporan->id) }}" method="POST">
                        @csrf @method('PUT')
                        <button onclick="return confirm('Approve laporan ini?')"
                            class="bg-green-600 hover:bg-green-700 text-white text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                            ✓ Approve
                        </button>
                    </form>
                    <a href="{{ route('audit_reports.show', $laporan) }}"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                        Detail
                    </a>
                </div>
            </div>
            @endforeach
            @if($laporanMenunggu->count() > 3)
            <a href="{{ route('audit_reports.index', ['status' => 'review']) }}"
                class="block text-center text-yellow-700 text-xs font-semibold hover:underline mt-1">
                + {{ $laporanMenunggu->count() - 3 }} laporan lainnya →
            </a>
            @endif
        </div>
    </div>
    @else
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        <p class="text-green-700 text-sm font-medium">Semua laporan sudah ditinjau. Tidak ada yang menunggu persetujuan.</p>
    </div>
    @endif

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl shadow p-5 col-span-2 lg:col-span-1">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Total Laporan</p>
                    <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ $totalLaporan }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="flex gap-2 mt-3 flex-wrap">
                <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">{{ $laporanPending }} review</span>
                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">{{ $laporanApprove }} disetujui</span>
                <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">{{ $laporanRevisi }} revisi</span>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Dok. Terarsip</p>
                    <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ $totalDokumen }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-3">Dari {{ $laporanApprove }} laporan disetujui</p>
        </div>
        <div class="bg-white rounded-2xl shadow p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Data Klien</p>
                    <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ $totalKlien }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow p-5 col-span-2 lg:col-span-1">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Logbook Auditor</p>
                    <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ $logbookPending + $logbookApprove + $logbookRevisi }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            <div class="flex gap-1 mt-3 flex-wrap">
                <span class="text-[10px] bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-bold">{{ $logbookPending }} pending</span>
                <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold">{{ $logbookApprove }} approve</span>
                <span class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-bold">{{ $logbookRevisi }} revisi</span>
            </div>
            <a href="{{ route('logbooks.index') }}" class="text-[10px] text-orange-600 hover:underline mt-2 block font-semibold text-right">Tinjau →</a>
        </div>
        <div class="bg-white rounded-2xl shadow p-5 col-span-2 lg:col-span-1">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Absensi Hari Ini</p>
                    <p class="text-3xl font-extrabold text-gray-800 mt-1">{{ $absensiHariIni }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-teal-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW BAWAH --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Jenis Audit + Progress --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4">Distribusi Jenis Audit</h3>
            @php $tot = max($totalLaporan, 1); @endphp
            <div class="space-y-4 mb-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-blue-600">Audit Keuangan</span>
                        <span class="font-bold text-gray-700">{{ $laporanKeuangan }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="bg-blue-500 h-3 rounded-full" style="width: {{ ($laporanKeuangan/$tot)*100 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-purple-600">Audit Kepatuhan</span>
                        <span class="font-bold text-gray-700">{{ $laporanKepatuhan }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="bg-purple-500 h-3 rounded-full" style="width: {{ ($laporanKepatuhan/$tot)*100 }}%"></div>
                    </div>
                </div>
            </div>
            <div class="pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-400 mb-3 font-semibold uppercase tracking-wide">Tingkat Persetujuan</p>
                @php $approveRate = $totalLaporan > 0 ? round(($laporanApprove/$totalLaporan)*100) : 0; @endphp
                <div class="flex items-center gap-3">
                    <div class="flex-1 bg-gray-100 rounded-full h-4">
                        <div class="bg-gradient-to-r from-green-400 to-green-600 h-4 rounded-full flex items-center justify-end pr-2 transition-all"
                            style="width: {{ $approveRate }}%">
                        </div>
                    </div>
                    <span class="text-lg font-extrabold text-green-600 w-14 text-right">{{ $approveRate }}%</span>
                </div>
            </div>
        </div>

        {{-- Dokumen Arsip Terbaru --}}
        <div class="bg-white rounded-2xl shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Dokumen Terarsip Terbaru</h3>
                <a href="{{ route('document_managements.index') }}" class="text-blue-600 text-xs hover:underline font-semibold">Semua →</a>
            </div>
            @if($dokumenTerbaru->isEmpty())
            <p class="text-gray-400 text-sm text-center py-6">Belum ada dokumen terarsip</p>
            @else
            <div class="space-y-2">
                @foreach($dokumenTerbaru as $dok)
                <div class="flex items-center gap-3 p-2.5 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-800 truncate">{{ $dok->document_name }}</p>
                        <p class="text-xs text-gray-400">{{ $dok->auditReport?->client?->institution_name ?? '-' }} · {{ $dok->archived_at?->format('d M Y') }}</p>
                    </div>
                    <a href="{{ route('document_managements.download', $dok) }}"
                        class="text-indigo-600 hover:text-indigo-800 flex-shrink-0">
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
