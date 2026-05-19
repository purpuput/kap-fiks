<x-app-layout title="Dashboard Admin">
<div class="py-10">
<div class="max-w-7xl mx-auto px-4 space-y-6">

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl shadow p-5 flex flex-col gap-1 col-span-1">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center mb-1">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Total User</p>
            <p class="text-3xl font-extrabold text-gray-800">{{ $totalUsers }}</p>
            <p class="text-xs text-gray-400">{{ $totalAuditor }} auditor aktif</p>
        </div>
        <div class="bg-white rounded-2xl shadow p-5 flex flex-col gap-1">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center mb-1">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Total Klien</p>
            <p class="text-3xl font-extrabold text-gray-800">{{ $totalKlien }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow p-5 flex flex-col gap-1">
            <div class="w-10 h-10 rounded-xl bg-teal-100 flex items-center justify-center mb-1">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Laporan Audit</p>
            <p class="text-3xl font-extrabold text-gray-800">{{ $totalLaporan }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow p-5 flex flex-col gap-1">
            <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center mb-1">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
            </div>
            <p class="text-xs text-gray-400 uppercase tracking-wide">Dok. Terarsip</p>
            <p class="text-3xl font-extrabold text-gray-800">{{ $totalDokumen }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow p-5 col-span-2 lg:col-span-1">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Logbook Auditor</p>
                    <p class="text-3xl font-extrabold text-gray-800">{{ $logbookPending + $logbookApprove + $logbookRevisi }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            <div class="flex gap-1 mt-2 flex-wrap">
                <span class="text-[10px] bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full font-bold">{{ $logbookPending }} pending</span>
                <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold">{{ $logbookApprove }} approve</span>
                <span class="text-[10px] bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-bold">{{ $logbookRevisi }} revisi</span>
            </div>
        </div>
    </div>

    {{-- ROW 2: Status Chart + Jenis + Absensi --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        {{-- Status Laporan --}}
        <div class="bg-white rounded-2xl shadow p-6 md:col-span-2">
            <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wide">Status Laporan Audit</h3>
            @php $tot = max($totalLaporan, 1); @endphp
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-yellow-600 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-yellow-400 inline-block"></span> Pending Review
                        </span>
                        <span class="font-bold text-gray-700">{{ $laporanPending }} <span class="text-gray-400 font-normal">laporan</span></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="bg-yellow-400 h-3 rounded-full transition-all" style="width: {{ ($laporanPending/$tot)*100 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-green-600 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span> Disetujui
                        </span>
                        <span class="font-bold text-gray-700">{{ $laporanApprove }} <span class="text-gray-400 font-normal">laporan</span></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="bg-green-400 h-3 rounded-full transition-all" style="width: {{ ($laporanApprove/$tot)*100 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-red-500 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span> Perlu Revisi
                        </span>
                        <span class="font-bold text-gray-700">{{ $laporanRevisi }} <span class="text-gray-400 font-normal">laporan</span></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="bg-red-400 h-3 rounded-full transition-all" style="width: {{ ($laporanRevisi/$tot)*100 }}%"></div>
                    </div>
                </div>
                <div class="pt-2 border-t border-gray-100 flex gap-6 text-sm">
                    <div>
                        <p class="text-gray-400 text-xs">Audit Keuangan</p>
                        <p class="font-bold text-blue-700 text-lg">{{ $laporanKeuangan }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs">Audit Kepatuhan</p>
                        <p class="font-bold text-purple-700 text-lg">{{ $laporanKepatuhan }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs">Absensi Hari Ini</p>
                        <p class="font-bold text-teal-700 text-lg">{{ $absensiHariIni }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Links Admin --}}
        <div class="bg-white rounded-2xl shadow p-6 flex flex-col justify-between">
            <h3 class="text-sm font-bold text-gray-700 mb-4 uppercase tracking-wide">Manajemen Cepat</h3>
            <div class="space-y-2">
                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center justify-between p-3 bg-blue-50 hover:bg-blue-100 rounded-xl transition group">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span class="text-sm font-semibold text-blue-700">Manajemen User</span>
                    </div>
                    <svg class="w-4 h-4 text-blue-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('audit_reports.index') }}"
                    class="flex items-center justify-between p-3 bg-teal-50 hover:bg-teal-100 rounded-xl transition group">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm font-semibold text-teal-700">Laporan Audit</span>
                    </div>
                    <svg class="w-4 h-4 text-teal-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('document_managements.index') }}"
                    class="flex items-center justify-between p-3 bg-purple-50 hover:bg-purple-100 rounded-xl transition group">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        <span class="text-sm font-semibold text-purple-700">Manajemen Dokumen</span>
                    </div>
                    <svg class="w-4 h-4 text-purple-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
                <a href="{{ route('clients.index') }}"
                    class="flex items-center justify-between p-3 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition group">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-sm font-semibold text-indigo-700">Data Klien</span>
                    </div>
                    <svg class="w-4 h-4 text-indigo-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Laporan Terbaru --}}
    <div class="bg-white rounded-2xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Laporan Audit Terbaru</h3>
            <a href="{{ route('audit_reports.index') }}" class="text-blue-600 text-xs hover:underline font-semibold">Lihat semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left">No. Laporan</th>
                        <th class="px-4 py-3 text-left">Judul</th>
                        <th class="px-4 py-3 text-left">Klien</th>
                        <th class="px-4 py-3 text-left">Auditor</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($laporanTerbaru as $laporan)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $laporan->report_number }}</td>
                        <td class="px-4 py-3 font-semibold text-gray-800 max-w-xs truncate">{{ $laporan->title }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $laporan->client->institution_name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @foreach($laporan->client->auditors as $a)
                                <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $a->name }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($laporan->status === 'pending') <span class="bg-yellow-100 text-yellow-700 text-xs px-2.5 py-1 rounded-full font-medium">Pending</span>
                            @elseif($laporan->status === 'approve') <span class="bg-green-100 text-green-700 text-xs px-2.5 py-1 rounded-full font-medium">Approve</span>
                            @else <span class="bg-red-100 text-red-700 text-xs px-2.5 py-1 rounded-full font-medium">Revisi</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('audit_reports.show', $laporan) }}" class="text-blue-600 text-xs hover:underline font-semibold">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">Belum ada laporan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>
</x-app-layout>