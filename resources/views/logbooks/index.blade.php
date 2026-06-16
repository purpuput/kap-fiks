<x-app-layout title="Logbook Pegawai">

    <div class="py-6 animate-fade-in-up">

        <div class="max-w-7xl mx-auto px-4 space-y-6">

            {{-- HEADER --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <div>

                    <h1 class="text-xl font-bold text-slate-800">
                        Logbook Pegawai
                    </h1>

                    <p class="text-xs text-slate-400 mt-1 font-semibold">
                        Kelola dan pantau aktivitas harian kerja tim audit
                    </p>

                </div>

            </div>

            {{-- DASHBOARD PEGAWAI (AUDITOR & ADMIN) --}}
            @if(in_array(auth()->user()->role, ['auditor', 'admin']))
            @php
                $todayLogbook = \App\Models\Logbook::where('user_id', auth()->id())
                    ->whereDate('date', now())
                    ->first();
            @endphp
            <div class="bg-gradient-to-r from-blue-600 to-indigo-650 rounded-3xl p-6 text-white shadow-sm shadow-blue-500/10">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <p class="text-blue-100 text-[10px] uppercase font-bold tracking-wider mb-0.5">Logbook Hari Ini</p>
                        <h2 class="text-lg font-bold">{{ now()->translatedFormat('l, d F Y') }}</h2>
                    </div>
                    <div>
                        @if(!$todayLogbook)
                        <a href="{{ route('logbooks.create') }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-blue-700 rounded-xl text-xs font-bold hover:bg-blue-50 transition shadow-sm">
                           + Isi Logbook Hari Ini
                        </a>
                        @else
                        <div class="px-3.5 py-1.5 bg-white/10 border border-white/20 rounded-xl flex items-center gap-1.5 text-xs text-white">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span class="font-bold">Sudah Mengisi Logbook</span>
                        </div>
                        @endif
                    </div>
                </div>

                @if($todayLogbook)
                <div class="mt-4 pt-4 border-t border-white/10 grid grid-cols-1 md:grid-cols-3 gap-4 text-xs font-semibold">
                    <div>
                        <p class="text-blue-200 font-bold mb-0.5">Tugas Utama</p>
                        <p class="font-medium truncate text-xs" title="{{ $todayLogbook->main_task }}">{{ $todayLogbook->main_task }}</p>
                    </div>
                    <div>
                        <p class="text-blue-200 font-bold mb-0.5">Progress & Tahapan</p>
                        <p class="font-medium text-xs">{{ $todayLogbook->cumulative_progress }}% — {{ $todayLogbook->audit_stage }}</p>
                    </div>
                    <div>
                        <p class="text-blue-200 font-bold mb-1">Status</p>
                        <div class="inline-flex items-center gap-1.5 bg-white/10 px-2.5 py-0.5 rounded-full text-xs font-bold border border-white/10">
                            @if($todayLogbook->status === 'pending')
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-400"></span> Pending
                            @elseif($todayLogbook->status === 'approved')
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Approved
                            @elseif($todayLogbook->status === 'revision')
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Revisi
                            @endif
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif

            {{-- DASHBOARD MONITORING (ADMIN & PIMPINAN) --}}
            @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
            @php
                $totalAuditor = \App\Models\User::whereIn('role', ['auditor', 'admin'])->count();
                $logbooksToday = \App\Models\Logbook::whereDate('date', now())->get();
                
                $auditorFilledIds = $logbooksToday->pluck('user_id')->unique()->toArray();
                                                  
                $filledCount = count($auditorFilledIds);
                $notFilledCount = $totalAuditor - $filledCount;
                $obstacleCount = $logbooksToday->where('has_obstacle', 1)->count();

                $auditorFilled = \App\Models\User::whereIn('role', ['auditor', 'admin'])->whereIn('id', $auditorFilledIds)->get();
                $auditorNotFilled = \App\Models\User::whereIn('role', ['auditor', 'admin'])->whereNotIn('id', $auditorFilledIds)->get();

                $logbooksWithObstacles = $logbooksToday->where('has_obstacle', 1);
            @endphp
            <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-3xl p-6 text-white shadow-sm">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                    <div>
                        <p class="text-slate-400 text-[10px] uppercase font-bold tracking-wider mb-0.5">Monitoring Logbook Hari Ini</p>
                        <h2 class="text-lg font-bold">{{ now()->translatedFormat('l, d F Y') }}</h2>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5 text-xs font-semibold">
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                        <p class="text-[10px] text-slate-400 uppercase tracking-wider mb-1">Total Pegawai</p>
                        <p class="text-lg font-bold">{{ $totalAuditor }}</p>
                    </div>
                    <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-4">
                        <p class="text-[10px] text-emerald-400 uppercase tracking-wider mb-1">Sudah Mengisi</p>
                        <p class="text-lg font-bold text-emerald-400">{{ $filledCount }}</p>
                    </div>
                    <div class="bg-rose-500/10 border border-rose-500/20 rounded-2xl p-4">
                        <p class="text-[10px] text-rose-400 uppercase tracking-wider mb-1">Belum Mengisi</p>
                        <p class="text-lg font-bold text-rose-400">{{ $notFilledCount }}</p>
                    </div>
                    <div class="bg-amber-500/10 border border-amber-500/20 rounded-2xl p-4">
                        <p class="text-[10px] text-amber-400 uppercase tracking-wider mb-1">Ada Kendala</p>
                        <p class="text-lg font-bold text-amber-400">{{ $obstacleCount }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-white/10 text-xs">
                    <div>
                        <p class="font-bold text-emerald-400 mb-2 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Sudah Mengisi
                        </p>
                        <div class="flex flex-wrap gap-1">
                            @forelse($auditorFilled as $a)
                                <span class="px-2.5 py-0.5 bg-white/5 border border-white/10 rounded-lg text-xs text-slate-300 font-semibold">{{ $a->name }}</span>
                            @empty
                                <span class="text-xs text-slate-400 font-semibold">Belum ada data</span>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <p class="font-bold text-rose-400 mb-2 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Belum Mengisi
                        </p>
                        <div class="flex flex-wrap gap-1">
                            @forelse($auditorNotFilled as $a)
                                <span class="px-2.5 py-0.5 bg-white/5 border border-white/10 rounded-lg text-xs text-slate-300 font-semibold">{{ $a->name }}</span>
                            @empty
                                <span class="text-xs text-slate-400 font-semibold">Semua sudah mengisi</span>
                            @endforelse
                        </div>
                    </div>
                    <div>
                        <p class="font-bold text-amber-400 mb-2 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Ada Kendala
                        </p>
                        <div class="flex flex-wrap gap-1.5">
                            @forelse($logbooksWithObstacles as $l)
                                <div class="group/obs relative inline-block">
                                    <span class="px-2.5 py-0.5 bg-amber-500/10 text-amber-300 border border-amber-500/20 rounded-lg text-xs font-semibold cursor-help whitespace-nowrap">
                                        {{ $l->user->name }} ({{ $l->obstacle_type }})
                                    </span>
                                    {{-- Tooltip --}}
                                    <div class="hidden group-hover/obs:block absolute z-20 left-0 mt-1.5 w-64 bg-slate-950 text-slate-100 border border-slate-800 rounded-2xl p-3 shadow-xl text-xs leading-relaxed">
                                        <p class="font-bold border-b border-white/10 pb-1.5 mb-1.5 text-amber-400">Detail Kendala ({{ $l->obstacle_type }}):</p>
                                        <p class="text-slate-200 italic font-semibold">"{{ $l->obstacle_description }}"</p>
                                    </div>
                                </div>
                            @empty
                                <span class="text-xs text-slate-400 font-semibold">Tidak ada kendala</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- SEARCH & FILTER ROW --}}
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 bg-white border border-slate-100 rounded-3xl p-3.5 shadow-sm">
                <form id="filterFormLogbook" method="GET" action="{{ route('logbooks.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
                    <!-- Date -->
                    <input type="date" name="date" value="{{ request('date') }}"
                        onchange="document.getElementById('filterFormLogbook').submit()"
                        class="bg-slate-50/50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-semibold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">

                    <!-- Auditor Filter (Admin & Pimpinan) -->
                    @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                    <select name="user_id" onchange="document.getElementById('filterFormLogbook').submit()"
                        class="bg-slate-50/50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-semibold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                        <option value="">Semua Pegawai</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                    @endif

                    <!-- Reset -->
                    @if(request('date') || request('user_id'))
                    <a href="{{ route('logbooks.index') }}"
                        class="text-xs font-bold text-slate-400 hover:text-slate-600 px-1 py-2 transition">
                        Reset
                    </a>
                    @endif
                </form>
            </div>

            {{-- TABLE SECTION --}}
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto relative">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50/70 text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-50">
                            <tr>
                                <th class="px-6 py-4 text-left w-12">No</th>
                                @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                                <th class="px-6 py-4 text-left">Pegawai</th>
                                @endif
                                <th class="px-6 py-4 text-left">Tanggal</th>
                                <th class="px-6 py-4 text-left">Klien</th>
                                <th class="px-6 py-4 text-left">Pekerjaan Utama</th>
                                <th class="px-6 py-4 text-left">Status</th>
                                <th class="px-6 py-4 text-left">Catatan Revisi</th>
                                <th class="px-6 py-4 text-center w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($logbooks as $logbook)
                            <tr class="hover:bg-slate-50/30 transition group">
                                {{-- No --}}
                                <td class="px-6 py-4 font-mono text-xs text-slate-400 whitespace-nowrap">
                                    {{ $logbooks->firstItem() + $loop->index }}
                                </td>

                                {{-- Pegawai --}}
                                @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-800 text-xs">
                                    {{ optional($logbook->user)->name ?? '-' }}
                                </td>
                                @endif

                                {{-- Tanggal --}}
                                <td class="px-6 py-4 text-slate-500 text-xs whitespace-nowrap">
                                    {{ $logbook->date->format('d/m/Y') }}
                                </td>

                                {{-- Klien --}}
                                <td class="px-6 py-4 text-slate-700 font-bold text-xs whitespace-nowrap">
                                    {{ optional($logbook->client)->institution_name ?? '-' }}
                                </td>

                                {{-- Pekerjaan Utama --}}
                                <td class="px-6 py-4 text-slate-500 text-xs max-w-xs">
                                    <div class="flex items-center gap-1.5">
                                        <p class="truncate" title="{{ $logbook->main_task }}">{{ $logbook->main_task }}</p>
                                        @if($logbook->has_obstacle)
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-250/20 text-[10px] font-bold cursor-help" 
                                              title="Kendala: {{ $logbook->obstacle_type }} — {{ $logbook->obstacle_description }}">
                                            ⚠️ Kendala
                                        </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($logbook->status === 'pending')
                                    <span class="bg-amber-50 text-amber-700 border border-amber-250/20 text-xs px-2.5 py-0.5 rounded-full font-bold">Pending</span>
                                    @elseif($logbook->status === 'approved')
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-250/20 text-xs px-2.5 py-0.5 rounded-full font-bold">Approved</span>
                                    @elseif($logbook->status === 'revision')
                                    <span class="bg-rose-50 text-rose-700 border border-rose-250/20 text-xs px-2.5 py-0.5 rounded-full font-bold">Revisi</span>
                                    @endif
                                </td>

                                {{-- Catatan --}}
                                <td class="px-6 py-4">
                                    @if($logbook->note)
                                    <div class="bg-rose-50/50 border border-rose-100 rounded-xl px-2.5 py-1 text-xs text-rose-700 max-w-xs font-semibold">
                                        {{ $logbook->note }}
                                    </div>
                                    @else
                                    <span class="text-slate-300 font-semibold">—</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        {{-- Detail --}}
                                        <a href="{{ route('logbooks.show', $logbook) }}"
                                            class="text-slate-500 hover:text-slate-800 text-xs font-bold transition hover:underline">Detail</a>

                                        {{-- ADMIN --}}
                                        @if(auth()->user()->role === 'admin')
                                            <a href="{{ route('logbooks.edit', $logbook) }}"
                                                class="text-blue-600 hover:text-blue-700 text-xs font-bold transition hover:underline">Edit</a>
                                            <form action="{{ route('logbooks.destroy', $logbook) }}" method="POST" class="inline"
                                                onsubmit="event.preventDefault(); confirmDelete(this, 'Logbook yang dihapus tidak dapat dikembalikan!')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-750 text-xs font-bold transition hover:underline">Hapus</button>
                                            </form>

                                        {{-- AUDITOR --}}
                                        @elseif(auth()->user()->role === 'auditor')
                                            @if($logbook->status === 'pending' && !$logbook->created_at->addDay()->isPast())
                                                <a href="{{ route('logbooks.edit', $logbook) }}"
                                                    class="text-blue-600 hover:text-blue-700 text-xs font-bold transition hover:underline">Edit</a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ in_array(auth()->user()->role, ['admin', 'pimpinan']) ? 8 : 7 }}" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                        <h4 class="text-xs font-bold text-slate-800 mb-0.5">Logbook Tidak Ditemukan</h4>
                                        <p class="text-[10px] text-slate-400 text-center">Belum ada data logbook yang sesuai.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                @if($logbooks->hasPages())
                <div class="px-6 py-4 border-t border-slate-50 bg-slate-50/30 flex items-center justify-between">
                    {{ $logbooks->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>