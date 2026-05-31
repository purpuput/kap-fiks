<x-app-layout title="Logbook Pegawai">

    <div class="space-y-6">

        {{-- DASHBOARD PEGAWAI (AUDITOR & ADMIN) --}}
        @if(in_array(auth()->user()->role, ['auditor', 'admin']))
        @php
            $todayLogbook = \App\Models\Logbook::where('user_id', auth()->id())
                ->whereDate('date', now())
                ->first();
        @endphp
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl p-5 text-white shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-blue-100 text-[10px] uppercase font-bold tracking-wider mb-0.5">Logbook Hari Ini</p>
                    <h2 class="text-xl font-bold">{{ now()->translatedFormat('l, d F Y') }}</h2>
                </div>
                <div>
                    @if(!$todayLogbook)
                    <a href="{{ route('logbooks.create') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-blue-700 rounded-lg text-xs font-bold hover:bg-blue-50 transition shadow-sm">
                        Isi Logbook Hari Ini
                    </a>
                    @else
                    <div class="px-3.5 py-1.5 bg-white/10 border border-white/20 rounded-lg flex items-center gap-1.5 text-xs text-white">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span class="font-bold">Sudah Mengisi Logbook</span>
                    </div>
                    @endif
                </div>
            </div>

            @if($todayLogbook)
            <div class="mt-4 pt-4 border-t border-white/10 grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div>
                    <p class="text-blue-200 font-semibold mb-0.5">Tugas Utama</p>
                    <p class="font-medium truncate text-sm" title="{{ $todayLogbook->main_task }}">{{ $todayLogbook->main_task }}</p>
                </div>
                <div>
                    <p class="text-blue-200 font-semibold mb-0.5">Progress & Tahapan</p>
                    <p class="font-medium text-sm">{{ $todayLogbook->cumulative_progress }}% — {{ $todayLogbook->audit_stage }}</p>
                </div>
                <div>
                    <p class="text-blue-200 font-semibold mb-1">Status</p>
                    <div class="inline-flex items-center gap-1.5 bg-white/10 px-2 py-0.5 rounded text-[11px] font-bold">
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
        <div class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-xl p-5 text-white shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <div>
                    <p class="text-slate-400 text-[10px] uppercase font-bold tracking-wider mb-0.5">Monitoring Logbook Hari Ini</p>
                    <h2 class="text-xl font-bold">{{ now()->translatedFormat('l, d F Y') }}</h2>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5 text-xs">
                <div class="bg-white/5 border border-white/5 rounded-lg p-3">
                    <p class="text-slate-400 mb-0.5">Total Pegawai</p>
                    <p class="text-xl font-bold">{{ $totalAuditor }}</p>
                </div>
                <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-lg p-3">
                    <p class="text-emerald-400 mb-0.5">Sudah Mengisi</p>
                    <p class="text-xl font-bold text-emerald-400">{{ $filledCount }}</p>
                </div>
                <div class="bg-rose-500/10 border border-rose-500/20 rounded-lg p-3">
                    <p class="text-rose-400 mb-0.5">Belum Mengisi</p>
                    <p class="text-xl font-bold text-rose-400">{{ $notFilledCount }}</p>
                </div>
                <div class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-3">
                    <p class="text-amber-400 mb-0.5">Ada Kendala</p>
                    <p class="text-xl font-bold text-amber-400">{{ $obstacleCount }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-white/10 text-xs">
                <div>
                    <p class="font-bold text-emerald-400 mb-2 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Sudah Mengisi
                    </p>
                    <div class="flex flex-wrap gap-1">
                        @forelse($auditorFilled as $a)
                            <span class="px-2 py-0.5 bg-white/5 border border-white/10 rounded text-[10px] text-slate-300">{{ $a->name }}</span>
                        @empty
                            <span class="text-[10px] text-slate-400">Belum ada data</span>
                        @endforelse
                    </div>
                </div>
                <div>
                    <p class="font-bold text-rose-400 mb-2 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Belum Mengisi
                    </p>
                    <div class="flex flex-wrap gap-1">
                        @forelse($auditorNotFilled as $a)
                            <span class="px-2 py-0.5 bg-white/5 border border-white/10 rounded text-[10px] text-slate-300">{{ $a->name }}</span>
                        @empty
                            <span class="text-[10px] text-slate-400">Semua sudah mengisi</span>
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
                                <span class="px-2 py-0.5 bg-amber-500/10 text-amber-300 border border-amber-500/20 rounded text-[10px] font-semibold cursor-help whitespace-nowrap">
                                    {{ $l->user->name }} ({{ $l->obstacle_type }})
                                </span>
                                {{-- Tooltip --}}
                                <div class="hidden group-hover/obs:block absolute z-20 left-0 mt-1 w-64 bg-slate-900 text-slate-100 border border-slate-700/60 rounded-lg p-2.5 shadow-lg text-[10px] leading-relaxed">
                                    <p class="font-bold border-b border-white/10 pb-1 mb-1 text-amber-400">Detail Kendala ({{ $l->obstacle_type }}):</p>
                                    <p class="text-slate-200 italic font-medium">"{{ $l->obstacle_description }}"</p>
                                </div>
                            </div>
                        @empty
                            <span class="text-[10px] text-slate-400">Tidak ada kendala</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- SEARCH & FILTER ROW --}}
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 bg-white border border-gray-200/80 rounded-xl p-3 shadow-sm">
            <form id="filterFormLogbook" method="GET" action="{{ route('logbooks.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
                <!-- Date -->
                <input type="date" name="date" value="{{ request('date') }}"
                    onchange="document.getElementById('filterFormLogbook').submit()"
                    class="bg-slate-50/50 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-gray-600 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">

                <!-- Auditor Filter (Admin & Pimpinan) -->
                @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                <select name="user_id" onchange="document.getElementById('filterFormLogbook').submit()"
                    class="bg-slate-50/50 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-gray-600 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
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
                    class="text-xs font-bold text-gray-400 hover:text-gray-600 px-1 py-1.5 transition">
                    Reset
                </a>
                @endif
            </form>
        </div>

        {{-- TABLE SECTION --}}
        <div class="bg-white border border-gray-200/80 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto relative">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50/60 text-gray-400 font-bold uppercase tracking-wider sticky top-0 z-10 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left w-12">No</th>
                            @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                            <th class="px-4 py-3 text-left">Pegawai</th>
                            @endif
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Klien</th>
                            <th class="px-4 py-3 text-left">Pekerjaan Utama</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Catatan Revisi</th>
                            <th class="px-4 py-3 text-center w-40">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logbooks as $logbook)
                        <tr class="hover:bg-slate-50/30 transition group">
                            {{-- No --}}
                            <td class="px-4 py-3 font-mono text-xs text-gray-500 whitespace-nowrap">
                                {{ $logbooks->firstItem() + $loop->index }}
                            </td>

                            {{-- Pegawai --}}
                            @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                            <td class="px-4 py-3 whitespace-nowrap font-bold text-gray-800 text-[13px]">
                                {{ optional($logbook->user)->name ?? '-' }}
                            </td>
                            @endif

                            {{-- Tanggal --}}
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                {{ $logbook->date->format('d/m/Y') }}
                            </td>

                            {{-- Klien --}}
                            <td class="px-4 py-3 text-gray-700 font-semibold whitespace-nowrap">
                                {{ optional($logbook->client)->institution_name ?? '-' }}
                            </td>

                            {{-- Pekerjaan Utama --}}
                            <td class="px-4 py-3 text-gray-600 max-w-xs">
                                <div class="flex items-center gap-1.5">
                                    <p class="truncate" title="{{ $logbook->main_task }}">{{ $logbook->main_task }}</p>
                                    @if($logbook->has_obstacle)
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200/50 text-[9px] font-bold cursor-help" 
                                          title="Kendala: {{ $logbook->obstacle_type }} — {{ $logbook->obstacle_description }}">
                                        ⚠️ Kendala
                                    </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($logbook->status === 'pending')
                                <span class="bg-amber-50 text-amber-700 text-[11px] px-2 py-0.5 rounded border border-amber-200/50 font-bold">Pending</span>
                                @elseif($logbook->status === 'approved')
                                <span class="bg-emerald-50 text-emerald-700 text-[11px] px-2 py-0.5 rounded border border-emerald-200/50 font-bold">Approved</span>
                                @elseif($logbook->status === 'revision')
                                <span class="bg-rose-50 text-rose-700 text-[11px] px-2 py-0.5 rounded border border-rose-200/50 font-bold">Revisi</span>
                                @endif
                            </td>

                            {{-- Catatan --}}
                            <td class="px-4 py-3">
                                @if($logbook->note)
                                <div class="bg-rose-50/50 border border-rose-100 rounded-lg px-2.5 py-1 text-[11px] text-rose-700 max-w-xs">
                                    {{ $logbook->note }}
                                </div>
                                @else
                                <span class="text-gray-300 font-semibold">—</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center justify-center gap-3">
                                        {{-- Detail --}}
                                        <a href="{{ route('logbooks.show', $logbook) }}"
                                            class="text-slate-500 hover:text-slate-800 font-bold hover:underline transition">Detail</a>

                                        {{-- ADMIN --}}
                                        @if(auth()->user()->role === 'admin')
                                            <a href="{{ route('logbooks.edit', $logbook) }}"
                                                class="text-blue-600 hover:text-blue-700 font-bold hover:underline transition">Edit</a>
                                            <form action="{{ route('logbooks.destroy', $logbook) }}" method="POST" class="inline"
                                                onsubmit="event.preventDefault(); confirmDelete(this, 'Logbook yang dihapus tidak dapat dikembalikan!')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-700 font-bold hover:underline transition">Hapus</button>
                                            </form>

                                        {{-- PIMPINAN --}}
                                        @elseif(auth()->user()->role === 'pimpinan')
                                            @if($logbook->status === 'pending')
                                                <form action="{{ route('logbooks.approve', $logbook) }}" method="POST" class="inline">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="text-emerald-600 hover:text-emerald-700 font-bold hover:underline transition">Setujui</button>
                                                </form>
                                                <button type="button"
                                                    onclick="document.getElementById('revision-{{ $logbook->id }}').classList.toggle('hidden')"
                                                    class="text-amber-600 hover:text-amber-700 font-bold hover:underline transition">
                                                    Revisi
                                                </button>
                                            @elseif($logbook->status === 'revision')
                                                <button type="button"
                                                    onclick="document.getElementById('edit-revision-{{ $logbook->id }}').classList.toggle('hidden')"
                                                    class="text-blue-600 hover:text-blue-700 font-bold hover:underline transition">
                                                    Edit Revisi
                                                </button>
                                            @else
                                                <span class="text-emerald-600 font-bold">✓ Selesai</span>
                                            @endif

                                        {{-- AUDITOR --}}
                                        @else
                                            @if($logbook->status === 'pending' && !$logbook->created_at->addDay()->isPast())
                                                <a href="{{ route('logbooks.edit', $logbook) }}"
                                                    class="text-blue-600 hover:text-blue-700 font-bold hover:underline transition">Edit</a>
                                            @endif
                                        @endif
                                    </div>

                                    {{-- Inline forms for revision notes --}}
                                    @if(auth()->user()->role === 'pimpinan')
                                        <div id="revision-{{ $logbook->id }}" class="hidden mt-2 border-t border-gray-100 pt-2">
                                            <form action="{{ route('logbooks.revision', $logbook) }}" method="POST" class="space-y-1.5">
                                                @csrf @method('PATCH')
                                                <textarea name="note" rows="2" required placeholder="Tulis catatan revisi..."
                                                    class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-amber-500 focus:border-amber-500 focus:outline-none resize-none"></textarea>
                                                <button type="submit"
                                                    class="w-full bg-amber-600 hover:bg-amber-700 text-white text-xs py-1 rounded-lg font-bold transition">
                                                    Kirim Revisi
                                                </button>
                                            </form>
                                        </div>
                                        <div id="edit-revision-{{ $logbook->id }}" class="hidden mt-2 border-t border-gray-100 pt-2">
                                            <form action="{{ route('logbooks.revision', $logbook) }}" method="POST" class="space-y-1.5">
                                                @csrf @method('PATCH')
                                                <textarea name="note" rows="2" required
                                                    class="w-full border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-1 focus:ring-blue-500 focus:border-blue-500 focus:outline-none resize-none">{{ $logbook->note }}</textarea>
                                                <button type="submit"
                                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs py-1 rounded-lg font-bold transition">
                                                    Perbarui
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ in_array(auth()->user()->role, ['admin', 'pimpinan']) ? 8 : 7 }}" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <h4 class="text-xs font-bold text-gray-800 mb-0.5">Logbook Tidak Ditemukan</h4>
                                    <p class="text-[11px] text-gray-400 text-center">Belum ada data logbook yang sesuai.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if($logbooks->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 bg-slate-50/30 flex items-center justify-between">
                {{ $logbooks->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>

</x-app-layout>