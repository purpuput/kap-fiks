<x-app-layout title="Detail Logbook">

    <div class="py-6 animate-fade-in-up">
        <div class="max-w-4xl mx-auto px-4">

            {{-- HEADER --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Detail Logbook</h2>
                    <p class="text-xs text-slate-400 mt-1 font-semibold">Laporan harian aktivitas audit</p>
                </div>
                <a href="{{ route('logbooks.index') }}"
                    class="bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-xl text-xs font-bold hover:bg-slate-50 transition">
                    Kembali
                </a>
            </div>

            {{-- CONTENT --}}
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
                
                {{-- STATUS BADGE --}}
                <div class="bg-slate-50/50 px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-650 uppercase tracking-wider">Status Logbook</span>
                    @if($logbook->status === 'pending')
                    <span class="bg-amber-50 text-amber-700 border border-amber-250/20 text-xs px-3 py-1 rounded-full font-bold">Pending</span>
                    @elseif($logbook->status === 'approved')
                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-250/20 text-xs px-3 py-1 rounded-full font-bold">Approved</span>
                    @elseif($logbook->status === 'revision')
                    <span class="bg-rose-50 text-rose-700 border border-rose-250/20 text-xs px-3 py-1 rounded-full font-bold">Revisi</span>
                    @endif
                </div>

                {{-- REVISI ALERT --}}
                @if($logbook->status === 'revision' && $logbook->note)
                <div class="bg-rose-50/50 p-5 border-b border-rose-100/50">
                    <h4 class="text-xs font-bold text-rose-800 mb-1 flex items-center gap-2 uppercase tracking-wider">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Catatan Revisi
                    </h4>
                    <p class="text-sm text-rose-700 font-medium">{{ $logbook->note }}</p>
                </div>
                @endif

                <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                    
                    {{-- INFO UMUM --}}
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wider font-bold mb-1.5">Nama Pegawai</p>
                        <p class="text-sm text-slate-800 font-semibold">{{ $logbook->user->name }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wider font-bold mb-1.5">Tanggal</p>
                        <p class="text-sm text-slate-800 font-semibold">{{ $logbook->date->format('d M Y') }}</p>
                    </div>

                    @if($logbook->user->role === 'auditor')
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wider font-bold mb-1.5">Klien</p>
                        <p class="text-sm text-slate-800 font-semibold">{{ optional($logbook->client)->institution_name ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wider font-bold mb-1.5">Peran dalam Tim</p>
                        <p class="text-sm text-slate-800 font-semibold capitalize">{{ $logbook->team_role }}</p>
                    </div>

                    {{-- DETAIL AUDIT --}}
                    <div class="md:col-span-2 pt-4 border-t border-slate-100"></div>

                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wider font-bold mb-1.5">Tahapan Audit</p>
                        <span class="inline-flex bg-blue-50/50 text-blue-700 text-xs px-2.5 py-1 rounded-lg font-bold border border-blue-100/30">
                            {{ $logbook->audit_stage }}
                        </span>
                    </div>

                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wider font-bold mb-1.5">Area Audit</p>
                        <span class="inline-flex bg-purple-50/50 text-purple-700 text-xs px-2.5 py-1 rounded-lg font-bold border border-purple-100/30">
                            {{ $logbook->audit_area }}
                        </span>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-xs text-slate-400 uppercase tracking-wider font-bold mb-2">Progress Kumulatif Penugasan</p>
                        <div class="flex items-center gap-3">
                            <div class="w-full bg-slate-100 rounded-full h-2.5">
                                <div class="bg-gradient-to-r from-blue-500 to-indigo-650 h-2.5 rounded-full" style="width: {{ $logbook->cumulative_progress }}%"></div>
                            </div>
                            <span class="text-xs font-bold text-slate-700 min-w-[3rem]">{{ $logbook->cumulative_progress }}%</span>
                        </div>
                    </div>
                    @endif

                    <div class="md:col-span-2">
                        <p class="text-xs text-slate-400 uppercase tracking-wider font-bold mb-2">Pekerjaan Utama Hari Ini</p>
                        <div class="bg-slate-50/30 p-4 rounded-2xl border border-slate-100/50">
                            <p class="text-sm text-slate-700 font-medium leading-relaxed whitespace-pre-wrap">{{ $logbook->main_task }}</p>
                        </div>
                    </div>

                    {{-- KENDALA --}}
                    <div class="md:col-span-2 pt-4 border-t border-slate-100"></div>

                    <div class="md:col-span-2">
                        <p class="text-xs text-slate-400 uppercase tracking-wider font-bold mb-2">Status Kendala</p>
                        @if($logbook->has_obstacle)
                            <span class="inline-flex bg-rose-50/50 text-rose-700 text-xs px-2.5 py-1 rounded-lg font-bold border border-rose-100/40 mb-4">
                                Ya, terdapat kendala
                            </span>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-rose-50/20 p-5 rounded-2xl border border-rose-100/30">
                                <div>
                                    <p class="text-xs text-slate-400 font-bold mb-1">Jenis Kendala</p>
                                    <p class="text-sm text-slate-800 font-semibold">{{ $logbook->obstacle_type }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold mb-1">Bantuan yang Dibutuhkan</p>
                                    <p class="text-sm text-slate-800 font-semibold">{{ $logbook->help_needed }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-xs text-slate-400 font-bold mb-1">Penjelasan Singkat</p>
                                    <p class="text-sm text-slate-800 font-medium leading-relaxed whitespace-pre-wrap">{{ $logbook->obstacle_description }}</p>
                                </div>
                            </div>
                        @else
                            <span class="inline-flex bg-emerald-50/50 text-emerald-700 text-xs px-2.5 py-1 rounded-lg font-bold border border-emerald-100/40">
                                Tidak ada kendala
                            </span>
                        @endif
                    </div>

                    {{-- NEXT PLAN --}}
                    <div class="md:col-span-2 pt-4 border-t border-slate-100"></div>

                    <div class="md:col-span-2">
                        <p class="text-xs text-slate-400 uppercase tracking-wider font-bold mb-2">Rencana Pekerjaan Besok</p>
                        <div class="bg-blue-50/20 p-4 rounded-2xl border border-blue-150/10">
                            <p class="text-sm text-slate-700 font-medium leading-relaxed whitespace-pre-wrap">{{ $logbook->next_plan }}</p>
                        </div>
                    </div>

                </div>
            </div>

            @if(auth()->user()->role === 'pimpinan')
                <div class="mt-6 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2 uppercase tracking-wider">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Persetujuan Pimpinan
                    </h3>

                    @if($logbook->status === 'pending')
                        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-4">
                            <form action="{{ route('logbooks.approve', $logbook) }}" method="POST" class="flex-1">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-teal-650 hover:from-emerald-700 hover:to-teal-700 text-white font-bold py-3 px-4 rounded-xl text-xs uppercase tracking-wider transition shadow-sm shadow-emerald-500/10 hover:shadow-md flex items-center justify-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Setujui Logbook
                                </button>
                            </form>
                            
                            <button type="button"
                                onclick="document.getElementById('revision-panel').classList.toggle('hidden')"
                                class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-4 rounded-xl text-xs uppercase tracking-wider transition shadow-sm flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Minta Revisi
                            </button>
                        </div>

                        {{-- Revision Form Panel --}}
                        <div id="revision-panel" class="hidden mt-4 border-t border-slate-100 pt-4">
                            <form action="{{ route('logbooks.revision', $logbook) }}" method="POST" class="space-y-3">
                                @csrf @method('PATCH')
                                <div>
                                    <label class="block text-xs font-bold text-slate-655 uppercase tracking-wider mb-2">Catatan Revisi</label>
                                    <textarea name="note" rows="3" required placeholder="Tuliskan catatan revisi/perbaikan yang harus dilakukan oleh pegawai..."
                                        class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 focus:outline-none resize-y bg-slate-50/50 text-slate-700"></textarea>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit"
                                        class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold py-3 px-6 rounded-xl transition shadow-sm uppercase tracking-wider">
                                        Kirim Permintaan Revisi
                                    </button>
                                </div>
                            </form>
                        </div>

                    @elseif($logbook->status === 'revision')
                        <div class="bg-amber-50/50 border border-amber-100/50 rounded-2xl p-4 mb-4">
                            <p class="text-sm text-amber-850 font-bold mb-1">Status: Meminta Revisi</p>
                            <p class="text-xs text-amber-700 font-medium">Logbook ini sedang ditandai untuk revisi. Anda dapat memperbarui catatan revisi di bawah jika diperlukan.</p>
                        </div>

                        <form id="update-revision-form" action="{{ route('logbooks.revision', $logbook) }}" method="POST" class="space-y-3">
                            @csrf @method('PATCH')
                            <div>
                                <label class="block text-xs font-bold text-slate-650 uppercase tracking-wider mb-2">Edit Catatan Revisi</label>
                                <textarea name="note" rows="3" required
                                    class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none resize-y bg-slate-50/50 text-slate-700">{{ $logbook->note }}</textarea>
                            </div>
                        </form>

                        <div class="flex justify-between items-center gap-4 mt-3">
                            <form action="{{ route('logbooks.approve', $logbook) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="bg-gradient-to-r from-emerald-600 to-teal-650 hover:from-emerald-700 hover:to-teal-700 text-white text-xs font-bold py-3 px-6 rounded-xl transition shadow-sm flex items-center gap-1.5 uppercase tracking-wider">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    Setujui Sekarang
                                </button>
                            </form>
                            
                            <button type="submit" form="update-revision-form"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-3 px-6 rounded-xl transition shadow-sm uppercase tracking-wider">
                                Perbarui Catatan Revisi
                            </button>
                        </div>
                    @else
                        <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-bold text-emerald-800">Logbook ini telah disetujui (Approved).</span>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>

</x-app-layout>
