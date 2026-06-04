<x-app-layout title="Dashboard">

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4">

            {{-- HEADER --}}
            <div class="mb-8">
                <h2 class="text-md font-bold text-gray-800">Dashboard</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Selamat datang, <span class="font-semibold text-blue-600">{{ auth()->user()->name }}</span>
                    — {{ ucfirst(auth()->user()->role) }}
                </p>
            </div>

            {{-- STATS CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

                @if(in_array(auth()->user()->role, ['admin', 'auditor', 'pimpinan']))
                <div class="bg-white rounded-2xl shadow p-5 flex items-center gap-4 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 12a5 5 0 1110 0 5 5 0 01-10 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 uppercase tracking-wide">Laporan Audit</p>
                        <p class="text-md font-bold text-gray-800">{{ \App\Models\AuditReport::count() }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow p-5 flex items-center gap-4 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 uppercase tracking-wide">Data Klien</p>
                        <p class="text-md font-bold text-gray-800">{{ \App\Models\Client::count() }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow p-5 flex items-center gap-4 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 uppercase tracking-wide">Manajemen Dokumen</p>
                        <p class="text-md font-bold text-gray-800">{{ \App\Models\AuditReportFile::where('status', 'lengkap')->count() }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow p-5 flex items-center gap-4 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400 uppercase tracking-wide">Logbook</p>
                        <p class="text-md font-bold text-gray-800">{{ \App\Models\Logbook::count() }}</p>
                    </div>
                </div>
                @endif

            </div>

            {{-- STATUS LAPORAN --}}
            @if(in_array(auth()->user()->role, ['admin', 'auditor', 'pimpinan']))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Laporan per Status --}}
                <div class="bg-white rounded-2xl shadow p-6">
                    <h3 class="text-md font-bold text-gray-700 mb-4">Status Laporan Audit</h3>
                    <div class="space-y-3">
                        @php
                            $pending = \App\Models\AuditReport::where('status','review')->count();
                            $approve = \App\Models\AuditReport::where('status','disetujui')->count();
                            $revisi  = \App\Models\AuditReport::where('status','revisi')->count();
                            $total   = max($pending + $approve + $revisi, 1);
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-yellow-600 font-medium">Pending Review</span>
                                <span class="font-bold">{{ $pending }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ ($pending/$total)*100 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-green-600 font-medium">Disetujui</span>
                                <span class="font-bold">{{ $approve }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-green-400 h-2 rounded-full" style="width: {{ ($approve/$total)*100 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-red-500 font-medium">Perlu Revisi</span>
                                <span class="font-bold">{{ $revisi }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-red-400 h-2 rounded-full" style="width: {{ ($revisi/$total)*100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Akses Cepat --}}
                <div class="bg-white rounded-2xl shadow p-6">
                    <h3 class="text-md font-bold text-gray-700 mb-4">Akses Cepat</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('audit_reports.index') }}"
                            class="flex items-center gap-3 p-3 bg-blue-50 hover:bg-blue-100 rounded-xl transition">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7 12a5 5 0 1110 0 5 5 0 01-10 0z"/>
                            </svg>
                            <span class="text-sm font-semibold text-blue-700">Laporan Audit</span>
                        </a>
                        <a href="{{ route('document_managements.index') }}"
                            class="flex items-center gap-3 p-3 bg-purple-50 hover:bg-purple-100 rounded-xl transition">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                            </svg>
                            <span class="text-sm font-semibold text-purple-700">Manajemen Dok.</span>
                        </a>
                        <a href="{{ route('clients.index') }}"
                            class="flex items-center gap-3 p-3 bg-green-50 hover:bg-green-100 rounded-xl transition">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-sm font-semibold text-green-700">Data Klien</span>
                        </a>
                        <a href="{{ route('logbooks.index') }}"
                            class="flex items-center gap-3 p-3 bg-yellow-50 hover:bg-yellow-100 rounded-xl transition">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <span class="text-sm font-semibold text-yellow-700">Logbook</span>
                        </a>
                    </div>
                </div>

            </div>
            @endif

        </div>
    </div>

</x-app-layout>
