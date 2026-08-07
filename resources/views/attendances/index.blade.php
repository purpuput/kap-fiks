<x-app-layout title="Absensi">

    <div class="space-y-6 animate-fade-in-up">

        {{-- HEADER BAR WITH REALTIME CLOCK --}}
        <div class="flex items-center justify-between gap-4 bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
            <div class="flex items-center gap-2.5">
                <span class="w-3.5 h-3.5 rounded-full bg-emerald-500 animate-pulse border-4 border-emerald-500/20"></span>
                <span class="text-sm font-semibold text-slate-500">Jam Digital:</span>
                <span id="clock" class="text-sm font-extrabold text-blue-600 font-mono tracking-tight bg-blue-50/50 border border-blue-100 rounded-lg px-2.5 py-1"></span>
            </div>
        </div>

        {{-- PEGAWAI DASHBOARD (AUDITOR & ADMIN) --}}
        @if(in_array(auth()->user()->role, ['auditor', 'admin']))
        @php
            $todayAttendance = \App\Models\Attendance::where('user_id', auth()->id())
                ->where('date', now()->toDateString())
                ->first();

            $attendanceType = 'Masuk';
            if ($todayAttendance && $todayAttendance->check_in) {
                $attendanceType = 'Pulang';
            }

            $isCompleted = $todayAttendance && $todayAttendance->check_in && $todayAttendance->check_out;
        @endphp
        <div class="bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 rounded-3xl p-6 md:p-8 text-white shadow-xl shadow-indigo-950/10 border border-indigo-900/35 relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-white/5 via-transparent to-transparent"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div>
                    <span class="bg-indigo-500/20 text-indigo-200 text-[10px] px-3.5 py-1 rounded-full font-semibold tracking-wide uppercase border border-indigo-500/30">Presensi Presisi</span>
                    <h2 class="text-2xl font-black tracking-tight mt-3">{{ now()->translatedFormat('l, d F Y') }}</h2>
                    <p class="text-indigo-200/90 text-sm mt-2 max-w-xl">
                        @if(!$todayAttendance)
                            Belum melakukan absensi hari ini. Silakan lakukan absensi masuk untuk memulai pekerjaan.
                        @elseif($todayAttendance->check_in && !$todayAttendance->check_out)
                            Sudah absen masuk. Jangan lupa melakukan absensi pulang saat jam kerja selesai.
                        @else
                            Presensi hari ini telah selesai dilakukan. Terima kasih atas kontribusi Anda.
                        @endif
                    </p>
                </div>
                <div class="flex-shrink-0">
                    @if($isCompleted)
                    <div class="px-5 py-3 bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl flex items-center gap-2 text-sm text-white font-bold">
                        <div class="w-6 h-6 rounded-lg bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        Absensi Selesai
                    </div>
                    @else
                    <button type="button" onclick="toggleAttendanceModal(true)"
                       class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl text-sm font-bold hover:shadow-lg hover:shadow-blue-500/25 transition duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Absen {{ $attendanceType }}
                    </button>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-6 pt-6 border-t border-white/10 text-sm relative z-10">
                <div class="bg-white/5 backdrop-blur-md rounded-2xl p-4 border border-white/10">
                    <p class="text-indigo-300 text-xs font-semibold uppercase tracking-wider mb-1">Jam Masuk</p>
                    <p class="text-lg font-bold text-white">{{ $todayAttendance->check_in ?? '--:--' }}</p>
                </div>
                <div class="bg-white/5 backdrop-blur-md rounded-2xl p-4 border border-white/10">
                    <p class="text-indigo-300 text-xs font-semibold uppercase tracking-wider mb-1">Jam Pulang</p>
                    <p class="text-lg font-bold text-white">{{ $todayAttendance->check_out ?? '--:--' }}</p>
                </div>
                <div class="bg-white/5 backdrop-blur-md rounded-2xl p-4 border border-white/10">
                    <p class="text-indigo-300 text-xs font-semibold uppercase tracking-wider mb-1">Status</p>
                    <p class="text-lg font-bold capitalize text-white">{{ $todayAttendance->status ?? 'Belum Absen' }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- ADMIN/PIMPINAN MONITORING DASHBOARD --}}
        @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
        @php
            $totalAuditor = \App\Models\User::whereIn('role', ['auditor', 'admin'])->count();
            $hadirToday = \App\Models\Attendance::whereDate('date', now())
                ->where('status', 'hadir')
                ->whereHas('user', function($q) {
                    $q->whereIn('role', ['auditor', 'admin']);
                })->count();

            $sakitToday = \App\Models\Attendance::whereDate('date', now())
                ->where('status', 'sakit')
                ->whereHas('user', function($q) {
                    $q->whereIn('role', ['auditor', 'admin']);
                })->count();

            $izinToday = \App\Models\Attendance::whereDate('date', now())
                ->where('status', 'izin')
                ->whereHas('user', function($q) {
                    $q->whereIn('role', ['auditor', 'admin']);
                })->count();

            $belumAbsen = $totalAuditor - ($hadirToday + $sakitToday + $izinToday);

            $attendanceToday = \App\Models\Attendance::with('user')
                ->whereDate('date', now())
                ->where('status', 'hadir')
                ->whereHas('user', function($q) {
                    $q->whereIn('role', ['auditor', 'admin']);
                })->get();

            $notAttendance = \App\Models\User::whereIn('role', ['auditor', 'admin'])
                ->whereNotIn('id', function ($query) {
                    $query->select('user_id')
                        ->from('attendances')
                        ->whereDate('date', now());
                })->get();
        @endphp
        <div class="bg-gradient-to-br from-slate-900 via-stone-900 to-slate-950 rounded-3xl p-6 md:p-8 text-white shadow-xl shadow-stone-950/15 border border-stone-800/80 relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-white/5 via-transparent to-transparent"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <span class="bg-stone-500/20 text-stone-300 text-[10px] px-3.5 py-1 rounded-full font-semibold tracking-wide uppercase border border-stone-500/30">Monitoring Panel</span>
                    <h2 class="text-2xl font-black tracking-tight mt-3">{{ now()->translatedFormat('l, d F Y') }}</h2>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-sm relative z-10">
                <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4">
                    <p class="text-stone-400 text-xs font-semibold uppercase tracking-wider mb-1">Hadir</p>
                    <p class="text-2xl font-black text-emerald-400">{{ $hadirToday }}</p>
                </div>
                <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4">
                    <p class="text-stone-400 text-xs font-semibold uppercase tracking-wider mb-1">Sakit</p>
                    <p class="text-2xl font-black text-amber-400">{{ $sakitToday }}</p>
                </div>
                <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4">
                    <p class="text-stone-400 text-xs font-semibold uppercase tracking-wider mb-1">Izin</p>
                    <p class="text-2xl font-black text-blue-400">{{ $izinToday }}</p>
                </div>
                <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4">
                    <p class="text-stone-400 text-xs font-semibold uppercase tracking-wider mb-1">Belum Absen</p>
                    <p class="text-2xl font-black text-rose-400">{{ $belumAbsen }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-6 border-t border-white/10 text-sm relative z-10">
                {{-- Pegawai Hadir --}}
                <div class="bg-white/5 backdrop-blur-md rounded-2xl p-5 border border-white/10">
                    <h3 class="font-bold text-slate-200 mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Pegawai Hadir Hari Ini
                    </h3>
                    <div class="divide-y divide-white/5 max-h-48 overflow-y-auto pr-1 scrollbar-thin">
                        @forelse($attendanceToday as $item)
                        <div class="flex items-center justify-between py-2.5">
                            <span class="font-semibold text-slate-200">{{ $item->user->name }}</span>
                            <span class="text-xs text-slate-400 font-mono bg-white/5 border border-white/10 px-2 py-0.5 rounded">{{ $item->check_in ?? '-' }}</span>
                        </div>
                        @empty
                        <p class="text-slate-400 text-sm py-2">Belum ada pegawai yang hadir.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Pegawai Belum Absen --}}
                <div class="bg-white/5 backdrop-blur-md rounded-2xl p-5 border border-white/10">
                    <h3 class="font-bold text-slate-200 mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-400"></span> Belum Absen
                    </h3>
                    <div class="divide-y divide-white/5 max-h-48 overflow-y-auto pr-1 scrollbar-thin">
                        @forelse($notAttendance as $user)
                        <div class="flex items-center justify-between py-2.5">
                            <span class="font-semibold text-slate-200">{{ $user->name }}</span>
                            <span class="text-[10px] font-bold text-rose-300 bg-rose-500/10 px-2.5 py-1 rounded-full border border-rose-500/25">Belum Absen</span>
                        </div>
                        @empty
                        <p class="text-slate-400 text-sm py-2">Semua pegawai sudah absen.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- SEARCH, FILTER & EXPORT BAR --}}
        <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4 bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
            <form id="filterForm" method="GET" action="{{ route('attendances.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
                <!-- User Multi-Select Filter (Admin & Pimpinan) -->
                @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                <div class="flex items-center gap-1.5 relative">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider hidden sm:inline">Pegawai:</span>
                    <div class="relative inline-block text-left">
                        <button type="button" onclick="toggleUserDropdown('filterUserDropdownMenu')" id="filterUserDropdownBtn"
                            class="bg-slate-50/50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100/70 focus:outline-none transition-all flex items-center justify-between gap-2 min-w-[170px] cursor-pointer">
                            <span id="filterUserDropdownLabel" class="truncate max-w-[140px]">
                                @if(empty($user_ids))
                                    Semua Pegawai
                                @elseif(count($user_ids) === 1)
                                    {{ $users->where('id', $user_ids[0])->first()->name ?? '1 Pegawai' }}
                                @else
                                    {{ count($user_ids) }} Pegawai Dipilih
                                @endif
                            </span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div id="filterUserDropdownMenu" class="absolute left-0 mt-2 w-64 bg-white border border-slate-200 rounded-2xl shadow-xl z-30 hidden p-3 space-y-2">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100 text-xs font-bold text-slate-500">
                                <span>Pilih Pegawai</span>
                                <div class="flex gap-2">
                                    <button type="button" onclick="toggleCheckAllUsers('filterUserDropdownMenu', true)" class="text-blue-600 hover:underline">Semua</button>
                                    <span class="text-slate-300">|</span>
                                    <button type="button" onclick="toggleCheckAllUsers('filterUserDropdownMenu', false)" class="text-rose-600 hover:underline">Reset</button>
                                </div>
                            </div>
                            <div class="max-h-48 overflow-y-auto space-y-1 scrollbar-thin pr-1">
                                @foreach($users as $u)
                                <label class="flex items-center gap-2.5 p-1.5 rounded-lg hover:bg-slate-50 cursor-pointer text-sm font-medium text-slate-700">
                                    <input type="checkbox" name="user_ids[]" value="{{ $u->id }}" 
                                        {{ in_array($u->id, $user_ids ?? []) ? 'checked' : '' }}
                                        onchange="updateUserDropdownText('filterUserDropdownMenu', 'filterUserDropdownLabel')"
                                        class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 user-cb">
                                    <span>{{ $u->name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Periode Tanggal Filter -->
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider hidden sm:inline">Periode:</span>
                    <input type="date" name="start_date" value="{{ $start_date ?? '' }}" placeholder="Tanggal Mulai" title="Tanggal Mulai"
                        class="bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer">
                    <span class="text-xs font-bold text-slate-400">s/d</span>
                    <input type="date" name="end_date" value="{{ $end_date ?? '' }}" placeholder="Tanggal Selesai" title="Tanggal Selesai"
                        class="bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer">
                </div>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm px-4 py-2 rounded-xl transition flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Filter
                </button>

                <!-- Reset -->
                @if($date || $start_date || $end_date || !empty($user_ids))
                <a href="{{ route('attendances.index') }}"
                    class="text-sm font-bold text-slate-400 hover:text-rose-600 px-2 py-2 transition flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Reset
                </a>
                @endif
            </form>

            {{-- Export Action Buttons --}}
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" onclick="toggleExportModal(true)"
                    class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-xl text-sm font-bold transition shadow-sm flex items-center gap-2 hover:shadow">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export Evaluasi
                </button>
            </div>
        </div>

        {{-- SUMMARY PER PEGAWAI CARD (Hanya tampil saat filter sedang aktif) --}}
        @if((!empty($user_ids) || $start_date || $end_date || $date) && isset($userSummary) && $userSummary->count() > 0 && in_array(auth()->user()->role, ['admin', 'pimpinan']))
        <div class="bg-white border border-slate-100 rounded-2xl p-5 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                        📊
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Ringkasan Evaluasi Kehadiran Per Pegawai</h3>
                        <p class="text-xs text-slate-400">Total kehadiran, sakit, izin, dan alpha berdasarkan filter aktif</p>
                    </div>
                </div>
                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-lg">
                    {{ $userSummary->count() }} Pegawai
                </span>
            </div>

            <div class="overflow-x-auto border border-slate-100 rounded-xl">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100 text-[10px]">
                        <tr>
                            <th class="px-4 py-3 text-center w-10">No</th>
                            <th class="px-4 py-3">Nama Pegawai</th>
                            <th class="px-4 py-3 text-center">Role</th>
                            <th class="px-4 py-3 text-center">Total Hari</th>
                            <th class="px-4 py-3 text-center text-emerald-600">Hadir</th>
                            <th class="px-4 py-3 text-center text-amber-600">Sakit</th>
                            <th class="px-4 py-3 text-center text-blue-600">Izin</th>
                            <th class="px-4 py-3 text-center text-rose-600">Alpha</th>
                            <th class="px-4 py-3 text-center">% Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($userSummary as $idx => $sum)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-4 py-2.5 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                            <td class="px-4 py-2.5 font-bold text-slate-800">{{ $sum->name }}</td>
                            <td class="px-4 py-2.5 text-center text-slate-500">{{ ucfirst($sum->role) }}</td>
                            <td class="px-4 py-2.5 text-center font-semibold text-slate-700">{{ $sum->total }} Hari</td>
                            <td class="px-4 py-2.5 text-center font-bold text-emerald-600 bg-emerald-50/40">{{ $sum->hadir }}</td>
                            <td class="px-4 py-2.5 text-center font-bold text-amber-600 bg-amber-50/40">{{ $sum->sakit }}</td>
                            <td class="px-4 py-2.5 text-center font-bold text-blue-600 bg-blue-50/40">{{ $sum->izin }}</td>
                            <td class="px-4 py-2.5 text-center font-bold text-rose-600 bg-rose-50/40">{{ $sum->alpha }}</td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="px-2 py-0.5 rounded-md font-bold text-[11px] {{ $sum->percentage >= 85 ? 'bg-emerald-100 text-emerald-700' : ($sum->percentage >= 70 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                    {{ $sum->percentage }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- TABLE SECTION --}}
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto relative">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/60 text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-4 text-center w-12">No</th>
                            @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                            <th class="px-5 py-4 text-left">Pegawai</th>
                            @endif
                            <th class="px-5 py-4 text-left">Tanggal</th>
                            <th class="px-5 py-4 text-left">Jam Masuk</th>
                            <th class="px-5 py-4 text-left">Jam Pulang</th>
                            <th class="px-5 py-4 text-center">Status</th>
                            <th class="px-5 py-4 text-center">File</th>
                            @if(auth()->user()->role === 'admin')
                            <th class="px-5 py-4 text-center w-36">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($attendances as $attendance)
                        <tr class="hover:bg-slate-50/40 transition duration-150">
                            {{-- No --}}
                            <td class="px-5 py-4 font-mono text-xs text-center text-slate-400 whitespace-nowrap">
                                {{ $loop->iteration + ($attendances->currentPage() - 1) * $attendances->perPage() }}
                            </td>
 
                            {{-- Pegawai --}}
                            @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                            <td class="px-5 py-4 whitespace-nowrap font-bold text-slate-800 text-sm">
                                {{ $attendance->user->name ?? '-' }}
                            </td>
                            @endif

                            {{-- Tanggal --}}
                            <td class="px-5 py-4 text-slate-650 whitespace-nowrap text-sm">
                                {{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}
                            </td>

                            {{-- Jam Masuk --}}
                            <td class="px-5 py-4 text-slate-500 whitespace-nowrap text-sm font-mono">
                                {{ $attendance->check_in ?? '-' }}
                            </td>

                            {{-- Jam Pulang --}}
                            <td class="px-5 py-4 text-slate-500 whitespace-nowrap text-sm font-mono">
                                {{ $attendance->check_out ?? '-' }}
                            </td>

                            {{-- Status --}}
                            <td class="px-5 py-4 text-center whitespace-nowrap">
                                <span class="text-xs px-2.5 py-0.5 rounded-full font-bold border 
                                    {{ $attendance->status === 'hadir' ? 'bg-emerald-50 text-emerald-700 border-emerald-200/50' : 
                                       ($attendance->status === 'sakit' ? 'bg-amber-50 text-amber-700 border-amber-200/50' : 
                                        ($attendance->status === 'izin' ? 'bg-blue-50 text-blue-700 border-blue-200/50' : 
                                         'bg-rose-50 text-rose-700 border-rose-200/50')) }}">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>

                            {{-- File --}}
                            <td class="px-5 py-4 text-center whitespace-nowrap text-xs">
                                @if($attendance->attachment)
                                @php
                                    $ext = pathinfo($attendance->attachment, PATHINFO_EXTENSION);
                                    $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                @endphp
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button"
                                        onclick="openModalPreview('{{ route('attendances.preview', $attendance) }}', 'Lampiran Absensi - {{ $attendance->user->name ?? '' }}', {{ $isImage ? 'true' : 'false' }})"
                                        class="inline-flex items-center text-blue-600 hover:text-blue-800 font-bold hover:underline">
                                        Lihat
                                    </button>
                                    <span class="text-slate-200">|</span>
                                    <a href="{{ route('attendances.download', $attendance) }}"
                                        class="inline-flex items-center text-slate-500 hover:text-slate-800 font-bold hover:underline">
                                        Unduh
                                    </a>
                                </div>
                                @else
                                -
                                @endif
                            </td>
                            
                            {{-- Aksi --}}
                            @if(auth()->user()->role === 'admin')
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-3">
                                    {{-- Edit --}}
                                    <a href="{{ route('attendances.edit', $attendance) }}"
                                        class="text-blue-600 hover:text-blue-800 font-extrabold transition text-xs">
                                        Edit
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('attendances.destroy', $attendance) }}" method="POST" class="inline"
                                        onsubmit="event.preventDefault(); confirmDelete(this, 'Data absensi pegawai ini akan dihapus secara permanen!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-rose-600 hover:text-rose-800 font-extrabold transition text-xs">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'admin' ? 8 : (auth()->user()->role === 'pimpinan' ? 7 : 6) }}" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <h4 class="text-sm font-bold text-slate-800 mb-1">Data Tidak Ditemukan</h4>
                                    <p class="text-xs text-slate-400 text-center">Belum ada data absensi yang sesuai.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if($attendances->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/30 flex items-center justify-between">
                {{ $attendances->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- CLOCK REALTIME SCRIPT --}}
    <script>
        function updateClock() {
            const now = new Date();
            const time = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('clock').innerHTML = time + ' WIB';
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>

    {{-- MODAL PREVIEW FILE --}}
    <div id="modalPreview" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 lg:p-10 transition-opacity">
        <div class="bg-white border border-slate-100 rounded-2xl shadow-xl w-full h-full max-w-4xl max-h-[85vh] flex flex-col overflow-hidden relative">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-sm font-bold text-slate-800" id="previewTitle">Preview Dokumen</h3>
                <button type="button" onclick="closeModalPreview()" class="text-slate-400 hover:text-slate-705 p-2 rounded-xl hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="flex-1 bg-slate-50/50 overflow-auto flex items-center justify-center relative p-4" id="previewContainer">
                <iframe id="previewIframe" class="w-full h-full border-none rounded-xl hidden" src=""></iframe>
                <img id="previewImage" class="max-w-full max-h-full object-contain rounded-xl hidden" src="" alt="Preview Lampiran">
                <div id="previewLoading" class="absolute inset-0 flex items-center justify-center">
                    <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModalPreview(url, title, isImage) {
            const modal = document.getElementById('modalPreview');
            const iframe = document.getElementById('previewIframe');
            const previewImage = document.getElementById('previewImage');
            const loading = document.getElementById('previewLoading');

            document.getElementById('previewTitle').innerText = title || 'Preview Dokumen';

            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            iframe.classList.add('hidden');
            previewImage.classList.add('hidden');

            if (isImage) {
                previewImage.src = url;
                previewImage.onload = function() {
                    loading.classList.add('hidden');
                    previewImage.classList.remove('hidden');
                };
            } else {
                iframe.src = url;
                iframe.onload = function() {
                    loading.classList.add('hidden');
                    iframe.classList.remove('hidden');
                };
            }
        }

        function closeModalPreview() {
            const modal = document.getElementById('modalPreview');
            const iframe = document.getElementById('previewIframe');
            const previewImage = document.getElementById('previewImage');

            modal.classList.add('hidden');
            iframe.src = '';
            previewImage.src = '';
        }
    </script>

    @if(in_array(auth()->user()->role, ['auditor', 'admin']))
    <script>
        // Attendance Modal Toggle
        function toggleAttendanceModal(show) {
            const modal = document.getElementById('modalAttendance');
            if (show) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.getElementById('modalStatusSelect');
            const attachmentField = document.getElementById('modal-attachment-field');
            const attachmentInput = document.getElementById('modalAttachmentInput');
            const tipeContainer = document.getElementById('modal-tipe-absensi-container');
            const submitBtn = document.getElementById('modalSubmitBtn');
            const defaultTipe = "{{ $attendanceType }}";

            function toggleStatusFields() {
                const status = statusSelect.value;
                if (status === 'sakit' || status === 'izin') {
                    attachmentField.classList.remove('hidden');
                    attachmentInput.required = true;
                    if (tipeContainer) {
                        tipeContainer.classList.add('hidden');
                    }
                    if (submitBtn) {
                        submitBtn.innerText = 'Simpan Absensi';
                    }
                } else {
                    attachmentField.classList.add('hidden');
                    attachmentInput.required = false;
                    if (tipeContainer) {
                        tipeContainer.classList.remove('hidden');
                    }
                    if (submitBtn) {
                        submitBtn.innerText = 'Simpan ' + defaultTipe;
                    }
                }
            }

            if (statusSelect) {
                statusSelect.addEventListener('change', toggleStatusFields);
                toggleStatusFields();
            }
        });
    </script>

    {{-- MODAL ADD ATTENDANCE --}}
    <div id="modalAttendance" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
        <div class="bg-white border border-slate-100 rounded-3xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all flex flex-col">
            
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-md font-bold text-slate-800">
                    Form Absensi
                </h3>
                <button type="button" onclick="toggleAttendanceModal(false)" class="text-slate-400 hover:text-slate-705 p-2 rounded-xl hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            {{-- Body --}}
            <form action="{{ route('attendances.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 flex flex-col">
                @csrf
                <div class="p-6 space-y-4 overflow-y-auto max-h-[70vh]">
                    
                    {{-- Error validation alerts --}}
                    @if ($errors->any())
                    <div class="p-4 bg-rose-50 border border-rose-200/50 rounded-2xl">
                        <ul class="list-disc list-inside text-rose-700 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 gap-4">
                        {{-- Pilihan auditor (Hanya admin) --}}
                        @if(auth()->user()->role === 'admin')
                        <div>
                            <label class="block text-sm font-semibold text-slate-650 mb-2">
                                Pilih Akun <span class="text-rose-500">*</span>
                            </label>
                            <select name="user_id" class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm focus:outline-none transition-all cursor-pointer">
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $user->id === auth()->id() ? 'selected' : '' }}>
                                    {{ $user->name }} {{ $user->id === auth()->id() ? '(Saya)' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        {{-- Tanggal --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-650 mb-2">
                                Tanggal
                            </label>
                            <input type="date" value="{{ now()->format('Y-m-d') }}" readonly class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-100/70 text-sm text-slate-400 cursor-not-allowed">
                        </div>

                        {{-- Tipe absensi otomatis --}}
                        <div id="modal-tipe-absensi-container">
                            <label class="block text-sm font-semibold text-slate-650 mb-2">
                                Tipe Absensi
                            </label>
                            <input type="text" value="Absen {{ $attendanceType }}" readonly class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-100/70 font-semibold text-sm text-slate-600 cursor-not-allowed">
                            <input type="hidden" name="tipe" value="{{ $attendanceType }}">
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-650 mb-2">
                                Status <span class="text-rose-500">*</span>
                            </label>
                            <select name="status" id="modalStatusSelect" class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm focus:outline-none transition-all cursor-pointer">
                                <option value="hadir">Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                            </select>
                        </div>

                        {{-- Lampiran Surat Keterangan / Izin --}}
                        <div id="modal-attachment-field" class="hidden">
                            <label class="block text-sm font-semibold text-slate-650 mb-2">
                                Upload Surat Keterangan (Sakit / Izin) <span class="text-rose-500">*</span>
                            </label>
                            <input type="file" name="attachment" id="modalAttachmentInput" accept=".pdf" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm focus:outline-none transition-all bg-slate-50/50">
                            <p class="text-xs text-slate-400 mt-1.5 font-medium">Format file: PDF. Maksimal 2MB.</p>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-end gap-3">
                    <button type="button" onclick="toggleAttendanceModal(false)" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition">
                        Batal
                    </button>
                    <button type="submit" id="modalSubmitBtn" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-sm font-bold transition shadow-sm shadow-blue-500/10 hover:shadow-md">
                        Simpan {{ $attendanceType }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($errors->any() || request('show_modal'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toggleAttendanceModal(true);
        });
    </script>
    @endif
    @endif

    {{-- MODAL EXPORT EVALUASI ABSENSI --}}
    <div id="modalExport" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
        <div class="bg-white border border-slate-100 rounded-3xl shadow-xl w-full max-w-md overflow-hidden transform transition-all flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-md font-bold text-slate-800">
                        Export Evaluasi Absensi
                    </h3>
                </div>
                <button type="button" onclick="toggleExportModal(false)" class="text-slate-400 hover:text-slate-700 p-2 rounded-xl hover:bg-slate-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="exportModalForm" method="GET" action="{{ route('attendances.export.pdf') }}" class="p-6 space-y-4">
                @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                <div class="relative">
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Pilih Pegawai (Bisa Lebih Dari Satu)
                    </label>
                    <div class="relative">
                        <button type="button" onclick="toggleUserDropdown('exportUserDropdownMenu')" id="exportUserDropdownBtn"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100/70 focus:outline-none transition-all flex items-center justify-between gap-2 cursor-pointer">
                            <span id="exportUserDropdownLabel" class="truncate max-w-[280px]">
                                @if(empty($user_ids))
                                    Semua Pegawai
                                @elseif(count($user_ids) === 1)
                                    {{ $users->where('id', $user_ids[0])->first()->name ?? '1 Pegawai' }}
                                @else
                                    {{ count($user_ids) }} Pegawai Dipilih
                                @endif
                            </span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div id="exportUserDropdownMenu" class="absolute left-0 right-0 mt-2 bg-white border border-slate-200 rounded-2xl shadow-xl z-30 hidden p-3 space-y-2">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100 text-xs font-bold text-slate-500">
                                <span>Pilih Pegawai</span>
                                <div class="flex gap-2">
                                    <button type="button" onclick="toggleCheckAllUsers('exportUserDropdownMenu', true)" class="text-blue-600 hover:underline">Semua</button>
                                    <span class="text-slate-300">|</span>
                                    <button type="button" onclick="toggleCheckAllUsers('exportUserDropdownMenu', false)" class="text-rose-600 hover:underline">Reset</button>
                                </div>
                            </div>
                            <div class="max-h-48 overflow-y-auto space-y-1 scrollbar-thin pr-1">
                                @foreach($users as $u)
                                <label class="flex items-center gap-2.5 p-1.5 rounded-lg hover:bg-slate-50 cursor-pointer text-sm font-medium text-slate-700">
                                    <input type="checkbox" name="user_ids[]" value="{{ $u->id }}" 
                                        {{ in_array($u->id, $user_ids ?? []) ? 'checked' : '' }}
                                        onchange="updateUserDropdownText('exportUserDropdownMenu', 'exportUserDropdownLabel')"
                                        class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 user-cb">
                                    <span>{{ $u->name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Tanggal Mulai
                        </label>
                        <input type="date" name="start_date" id="exportStartDate" value="{{ $start_date ?? '' }}"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm focus:outline-none transition-all cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Tanggal Selesai
                        </label>
                        <input type="date" name="end_date" id="exportEndDate" value="{{ $end_date ?? '' }}"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm focus:outline-none transition-all cursor-pointer">
                    </div>
                </div>

                {{-- Preset Periode Cepat --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                        Pilih Cepat Periode
                    </label>
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <button type="button" onclick="setExportPeriod('this_month')" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg transition text-center">
                            Bulan Ini
                        </button>
                        <button type="button" onclick="setExportPeriod('last_30')" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg transition text-center">
                            30 Hari Terakhir
                        </button>
                        <button type="button" onclick="setExportPeriod('clear')" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold rounded-lg transition text-center">
                            Reset Periode
                        </button>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="submitExportForm('{{ route('attendances.export.pdf') }}')"
                        class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-bold transition shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export PDF
                    </button>

                    <button type="button" onclick="submitExportForm('{{ route('attendances.export.excel') }}')"
                        class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold transition shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Excel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleExportModal(show) {
            const modal = document.getElementById('modalExport');
            if (show) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }
        }

        function setExportPeriod(type) {
            const startInput = document.getElementById('exportStartDate');
            const endInput = document.getElementById('exportEndDate');
            const today = new Date();

            if (type === 'this_month') {
                const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
                startInput.value = firstDay.toISOString().split('T')[0];
                endInput.value = today.toISOString().split('T')[0];
            } else if (type === 'last_30') {
                const pastDate = new Date();
                pastDate.setDate(today.getDate() - 30);
                startInput.value = pastDate.toISOString().split('T')[0];
                endInput.value = today.toISOString().split('T')[0];
            } else if (type === 'clear') {
                startInput.value = '';
                endInput.value = '';
            }
        }

        function submitExportForm(actionUrl) {
            const form = document.getElementById('exportModalForm');
            form.action = actionUrl;
            form.submit();
            toggleExportModal(false);
        }

        function toggleUserDropdown(menuId) {
            const menu = document.getElementById(menuId);
            if (!menu) return;
            menu.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            ['filterUserDropdownMenu', 'exportUserDropdownMenu'].forEach(menuId => {
                const menu = document.getElementById(menuId);
                const btnId = menuId === 'filterUserDropdownMenu' ? 'filterUserDropdownBtn' : 'exportUserDropdownBtn';
                const btn = document.getElementById(btnId);
                if (menu && !menu.classList.contains('hidden')) {
                    if (!menu.contains(e.target) && btn && !btn.contains(e.target)) {
                        menu.classList.add('hidden');
                    }
                }
            });
        });

        function toggleCheckAllUsers(menuId, check) {
            const menu = document.getElementById(menuId);
            if (!menu) return;
            const checkboxes = menu.querySelectorAll('.user-cb');
            checkboxes.forEach(cb => cb.checked = check);
            
            const labelId = menuId === 'filterUserDropdownMenu' ? 'filterUserDropdownLabel' : 'exportUserDropdownLabel';
            updateUserDropdownText(menuId, labelId);
        }

        function updateUserDropdownText(menuId, labelId) {
            const menu = document.getElementById(menuId);
            const label = document.getElementById(labelId);
            if (!menu || !label) return;

            const checked = menu.querySelectorAll('.user-cb:checked');
            if (checked.length === 0) {
                label.innerText = 'Semua Pegawai';
            } else if (checked.length === 1) {
                const parentLabel = checked[0].closest('label');
                label.innerText = parentLabel ? parentLabel.textContent.trim() : '1 Pegawai';
            } else {
                label.innerText = checked.length + ' Pegawai Dipilih';
            }
        }
    </script>
</x-app-layout>