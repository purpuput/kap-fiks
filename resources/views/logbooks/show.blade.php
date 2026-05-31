<x-app-layout title="Detail Logbook">

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4">

            {{-- HEADER --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Detail Logbook</h2>
                    <p class="text-sm text-gray-500 mt-1">Laporan harian aktivitas audit</p>
                </div>
                <a href="{{ route('logbooks.index') }}"
                    class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-xl text-sm font-semibold hover:bg-gray-50 transition">
                    Kembali
                </a>
            </div>

            {{-- CONTENT --}}
            <div class="bg-white border border-gray-200/80 rounded-xl shadow-sm-sm border border-gray-100 overflow-hidden">
                
                {{-- STATUS BADGE --}}
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-600">Status Logbook</span>
                    @if($logbook->status === 'pending')
                    <span class="bg-yellow-100 text-yellow-700 text-xs px-3 py-1.5 rounded-full font-bold uppercase tracking-wider">Pending</span>
                    @elseif($logbook->status === 'approved')
                    <span class="bg-green-100 text-green-700 text-xs px-3 py-1.5 rounded-full font-bold uppercase tracking-wider">Approved</span>
                    @elseif($logbook->status === 'revision')
                    <span class="bg-red-100 text-red-700 text-xs px-3 py-1.5 rounded-full font-bold uppercase tracking-wider">Revisi</span>
                    @endif
                </div>

                {{-- REVISI ALERT --}}
                @if($logbook->status === 'revision' && $logbook->note)
                <div class="bg-red-50 p-4 border-b border-red-100">
                    <h4 class="text-sm font-bold text-red-800 mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Catatan Revisi
                    </h4>
                    <p class="text-sm text-red-700">{{ $logbook->note }}</p>
                </div>
                @endif

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                    
                    {{-- INFO UMUM --}}
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Nama Pegawai</p>
                        <p class="text-sm text-gray-900 font-medium">{{ $logbook->user->name }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Tanggal</p>
                        <p class="text-sm text-gray-900 font-medium">{{ $logbook->date->format('d M Y') }}</p>
                    </div>

                    @if($logbook->user->role === 'auditor')
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Klien</p>
                        <p class="text-sm text-gray-900 font-medium">{{ optional($logbook->client)->institution_name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Peran dalam Tim</p>
                        <p class="text-sm text-gray-900 font-medium capitalize">{{ $logbook->team_role }}</p>
                    </div>

                    {{-- DETAIL AUDIT --}}
                    <div class="md:col-span-2 pt-4 border-t border-gray-100"></div>

                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Tahapan Audit</p>
                        <span class="inline-flex bg-blue-50 text-blue-700 text-xs px-2.5 py-1 rounded-md font-medium border border-blue-100">
                            {{ $logbook->audit_stage }}
                        </span>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Area Audit</p>
                        <span class="inline-flex bg-purple-50 text-purple-700 text-xs px-2.5 py-1 rounded-md font-medium border border-purple-100">
                            {{ $logbook->audit_area }}
                        </span>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-2">Progress Kumulatif Penugasan</p>
                        <div class="flex items-center gap-3">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $logbook->cumulative_progress }}%"></div>
                            </div>
                            <span class="text-xs font-bold text-gray-600 min-w-[3rem]">{{ $logbook->cumulative_progress }}%</span>
                        </div>
                    </div>
                    @endif

                    <div class="md:col-span-2">
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-2">Pekerjaan Utama Hari Ini</p>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $logbook->main_task }}</p>
                        </div>
                    </div>

                    {{-- KENDALA --}}
                    <div class="md:col-span-2 pt-4 border-t border-gray-100"></div>

                    <div class="md:col-span-2">
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-2">Status Kendala</p>
                        @if($logbook->has_obstacle)
                            <span class="inline-flex bg-red-50 text-red-700 text-xs px-2.5 py-1 rounded-md font-medium border border-red-100 mb-4">
                                Ya, terdapat kendala
                            </span>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-red-50/50 p-4 rounded-xl border border-red-100/50">
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold mb-1">Jenis Kendala</p>
                                    <p class="text-sm text-gray-900 font-medium">{{ $logbook->obstacle_type }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 font-semibold mb-1">Bantuan yang Dibutuhkan</p>
                                    <p class="text-sm text-gray-900 font-medium">{{ $logbook->help_needed }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-xs text-gray-500 font-semibold mb-1">Penjelasan Singkat</p>
                                    <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $logbook->obstacle_description }}</p>
                                </div>
                            </div>
                        @else
                            <span class="inline-flex bg-green-50 text-green-700 text-xs px-2.5 py-1 rounded-md font-medium border border-green-100">
                                Tidak ada kendala
                            </span>
                        @endif
                    </div>

                    {{-- NEXT PLAN --}}
                    <div class="md:col-span-2 pt-4 border-t border-gray-100"></div>

                    <div class="md:col-span-2">
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-2">Rencana Pekerjaan Besok</p>
                        <div class="bg-blue-50/30 p-4 rounded-xl border border-blue-100">
                            <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $logbook->next_plan }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
