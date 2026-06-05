<x-app-layout title="Absensi">

    <div class="space-y-6">

        {{-- HEADER BAR WITH REALTIME CLOCK --}}
        <div class="flex items-center justify-between gap-4 bg-white border border-gray-200/80 rounded-xl p-3 shadow-sm">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-md font-semibold text-gray-500">Jam Digital:</span>
                <span id="clock" class="text-md font-bold text-blue-600 font-mono"></span>
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
        <div class="bg-gradient-to-r from-sky-900 to-sky-950 rounded-xl p-5 text-white shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-blue-100 text-sm uppercase font-bold tracking-wider mb-0.5">Absensi Hari Ini</p>
                    <h2 class="text-xl font-bold">{{ now()->translatedFormat('l, d F Y') }}</h2>
                    <p class="text-blue-100 text-sm mt-1">
                        @if(!$todayAttendance)
                            Belum melakukan absensi.
                        @elseif($todayAttendance->check_in && !$todayAttendance->check_out)
                            Sudah absen masuk. Jangan lupa absen pulang saat selesai bekerja.
                        @else
                            Absensi hari ini telah selesai dilakukan.
                        @endif
                    </p>
                </div>
                <div>
                    @if($isCompleted)
                    <div class="px-3.5 py-1.5 bg-white/10 border border-white/20 rounded-lg flex items-center gap-1.5 text-md text-white">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span class="font-bold"> Absensi Selesai</span>
                    </div>
                    @else
                    <button type="button" onclick="toggleAttendanceModal(true)"
                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-blue-700 rounded-lg text-md font-bold hover:bg-blue-50 transition shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Absen {{ $attendanceType }}
                    </button>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 pt-4 border-t border-white/10 text-md">
                <div class="bg-white/10 rounded-lg p-3">
                    <p class="text-blue-200 font-semibold mb-0.5">Jam Masuk</p>
                    <p class="text-md font-bold">{{ $todayAttendance->check_in ?? '--:--' }}</p>
                </div>
                <div class="bg-white/10 rounded-lg p-3">
                    <p class="text-blue-200 font-semibold mb-0.5">Jam Pulang</p>
                    <p class="text-md font-bold">{{ $todayAttendance->check_out ?? '--:--' }}</p>
                </div>
                <div class="bg-white/10 rounded-lg p-3">
                    <p class="text-blue-200 font-semibold mb-0.5">Status</p>
                    <p class="text-md font-bold capitalize">{{ $todayAttendance->status ?? 'Belum Absen' }}</p>
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
        <div class="bg-gradient-to-r from-stone-900 to-stone-950 rounded-xl p-5 text-white shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <div>
                    <p class="text-slate-400 text-md uppercase font-bold tracking-wider mb-0.5">Monitoring Absensi Hari Ini</p>
                    <h2 class="text-xl font-bold">{{ now()->translatedFormat('l, d F Y') }}</h2>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5 text-md">
                <div class="bg-white/5 border border-white/5 rounded-lg p-3">
                    <p class="text-slate-400 mb-0.5">Hadir</p>
                    <p class="text-xl font-bold text-emerald-400">{{ $hadirToday }}</p>
                </div>
                <div class="bg-white/5 border border-white/5 rounded-lg p-3">
                    <p class="text-slate-400 mb-0.5">Sakit</p>
                    <p class="text-xl font-bold text-amber-400">{{ $sakitToday }}</p>
                </div>
                <div class="bg-white/5 border border-white/5 rounded-lg p-3">
                    <p class="text-slate-400 mb-0.5">Izin</p>
                    <p class="text-xl font-bold text-blue-400">{{ $izinToday }}</p>
                </div>
                <div class="bg-white/5 border border-white/5 rounded-lg p-3">
                    <p class="text-slate-400 mb-0.5">Belum Absen</p>
                    <p class="text-xl font-bold text-rose-400">{{ $belumAbsen }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 pt-4 border-t border-white/10 text-md">
                {{-- Pegawai Hadir --}}
                <div class="bg-white/5 rounded-lg p-3.5 border border-white/5">
                    <h3 class="font-bold text-slate-300 mb-2.5 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Pegawai Hadir Hari Ini
                    </h3>
                    <div class="divide-y divide-white/5 max-h-40 overflow-y-auto pr-1">
                        @forelse($attendanceToday as $item)
                        <div class="flex items-center justify-between py-1.5">
                            <span class="font-medium text-slate-200">{{ $item->user->name }}</span>
                            <span class="text-md text-slate-400 font-mono">{{ $item->check_in ?? '-' }}</span>
                        </div>
                        @empty
                        <p class="text-slate-400 text-md py-1">Belum ada pegawai yang hadir.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Pegawai Belum Absen --}}
                <div class="bg-white/5 rounded-lg p-3.5 border border-white/5">
                    <h3 class="font-bold text-slate-300 mb-2.5 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Belum Absen
                    </h3>
                    <div class="divide-y divide-white/5 max-h-40 overflow-y-auto pr-1">
                        @forelse($notAttendance as $user)
                        <div class="flex items-center justify-between py-1.5">
                            <span class="font-medium text-slate-200">{{ $user->name }}</span>
                            <span class="text-md text-rose-300/80 bg-rose-500/10 px-1.5 py-0.5 rounded">Belum Absen</span>
                        </div>
                        @empty
                        <p class="text-slate-400 text-md py-1">Semua pegawai sudah absen.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- SEARCH, FILTER & EXPORT BAR --}}
        <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3 bg-white border border-gray-200/80 rounded-xl p-3 shadow-sm">
            <form id="filterForm" method="GET" action="{{ route('attendances.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
                <!-- Date Filter -->
                <input type="date" name="date" value="{{ $date ?? '' }}"
                    onchange="document.getElementById('filterForm').submit()"
                    class="bg-slate-50/50 border border-gray-200 rounded-lg px-2.5 py-1.5 text-md font-semibold text-gray-600 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">

                <!-- User Filter (Admin & Pimpinan) -->
                @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                <select name="user_id" onchange="document.getElementById('filterForm').submit()"
                    class="bg-slate-50/50 border border-gray-200 rounded-lg px-2.5 py-1.5 text-md font-semibold text-gray-600 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">Semua Pegawai</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ (string) $user_id === (string) $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                    @endforeach
                </select>
                @endif

                <!-- Reset -->
                @if($date || $user_id)
                <a href="{{ route('attendances.index') }}"
                    class="text-md font-bold text-gray-400 hover:text-gray-600 px-1 py-1.5 transition">
                    Reset
                </a>
                @endif
            </form>

            {{-- Export Buttons (Admin) --}}
            @if(auth()->user()->role === 'admin')
            <div class="flex items-center gap-2">
                <a href="{{ route('attendances.export.pdf', request()->query()) }}"
                    class="bg-red-50 hover:bg-red-100 text-red-700 px-3 py-1.5 rounded-lg text-md font-bold border border-red-200/30 transition shadow-sm flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export PDF
                </a>
            </div>
            @endif
        </div>

        {{-- TABLE SECTION --}}
        <div class="bg-white border border-gray-200/80 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto relative">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/60 text-gray-400 font-bold uppercase tracking-wider sticky top-0 z-10 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-center w-12">No</th>
                            @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                            <th class="px-4 py-3 text-left">Pegawai</th>
                            @endif
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Jam Masuk</th>
                            <th class="px-4 py-3 text-left">Jam Pulang</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">File</th>
                            @if(auth()->user()->role === 'admin')
                            <th class="px-4 py-3 text-center w-36">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($attendances as $attendance)
                        <tr class="hover:bg-slate-50/30 transition group">
                            {{-- No --}}
                            <td class="px-4 py-3 font-mono text-sm text-center text-gray-500 whitespace-nowrap">
                                {{ $loop->iteration + ($attendances->currentPage() - 1) * $attendances->perPage() }}
                            </td>
 
                            {{-- Pegawai --}}
                            @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                            <td class="px-4 py-3 whitespace-nowrap font-bold text-gray-800 text-md">
                                {{ $attendance->user->name ?? '-' }}
                            </td>
                            @endif

                            {{-- Tanggal --}}
                            <td class="px-4 py-3 text-gray-700 whitespace-nowrap text-sm">
                                {{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}
                            </td>

                            {{-- Jam Masuk --}}
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap text-sm">
                                {{ $attendance->check_in ?? '-' }}
                            </td>

                            {{-- Jam Pulang --}}
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap text-sm">
                                {{ $attendance->check_out ?? '-' }}
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3 text-center whitespace-nowrap text-sm">
                                <span class="text-sm px-2 py-0.5 rounded font-bold border 
                                    {{ $attendance->status === 'hadir' ? 'bg-emerald-50 text-emerald-700 border-emerald-200/50' : 
                                       ($attendance->status === 'sakit' ? 'bg-amber-50 text-amber-700 border-amber-200/50' : 
                                        ($attendance->status === 'izin' ? 'bg-blue-50 text-blue-700 border-blue-200/50' : 
                                         'bg-rose-50 text-rose-700 border-rose-200/50')) }}">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>



                            {{-- File --}}
                            <td class="px-4 py-3 text-center whitespace-nowrap text-sm">
                                @if($attendance->attachment)
                                <a href="{{ route('attendances.download', $attendance) }}"
                                    class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-bold">
                                    📄 Unduh Lampiran
                                </a>
                                @else
                                -
                                @endif
                            </td>
                            
                            {{-- Aksi --}}
                            @if(auth()->user()->role === 'admin')
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                <div class="flex items-center justify-center gap-3">
                                    {{-- Edit --}}
                                    <a href="{{ route('attendances.edit', $attendance) }}"
                                        class="text-blue-600 hover:text-blue-700 font-bold hover:underline transition">
                                        Edit
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('attendances.destroy', $attendance) }}" method="POST" class="inline"
                                        onsubmit="event.preventDefault(); confirmDelete(this, 'Data absensi pegawai ini akan dihapus secara permanen!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-rose-600 hover:text-rose-700 font-bold hover:underline transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'admin' ? 8 : (auth()->user()->role === 'pimpinan' ? 7 : 6) }}" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <h4 class="text-xs font-bold text-gray-800 mb-0.5">Data Tidak Ditemukan</h4>
                                    <p class="text-[11px] text-gray-400 text-center">Belum ada data absensi yang sesuai.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if($attendances->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 bg-slate-50/30 flex items-center justify-between">
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
    <div id="modalPreview" class="fixed inset-0 z-50 hidden bg-black/70 flex items-center justify-center p-4 lg:p-10 transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full h-full max-w-4xl max-h-[85vh] flex flex-col overflow-hidden relative">
            <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50">
                <h3 class="text-md font-bold text-gray-800" id="previewTitle">Preview Dokumen</h3>
                <button type="button" onclick="closeModalPreview()" class="text-gray-400 hover:text-gray-700 p-2 rounded-full hover:bg-gray-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="flex-1 bg-gray-100 overflow-auto flex items-center justify-center relative p-4" id="previewContainer">
                <iframe id="previewIframe" class="w-full h-full border-none hidden" src=""></iframe>
                <img id="previewImage" class="max-w-full max-h-full object-contain hidden" src="" alt="Preview Lampiran">
                <div id="previewLoading" class="absolute inset-0 flex items-center justify-center">
                    <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModalPreview(url, title) {
            const modal = document.getElementById('modalPreview');
            const iframe = document.getElementById('previewIframe');
            const previewImage = document.getElementById('previewImage');
            const loading = document.getElementById('previewLoading');

            document.getElementById('previewTitle').innerText = title || 'Preview Dokumen';

            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            iframe.classList.add('hidden');
            previewImage.classList.add('hidden');

            const isImage = /\.(jpg|jpeg|png|gif|webp)$/i.test(url);

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
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden transform transition-all flex flex-col">
            
            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50">
                <h3 class="text-md font-bold text-gray-800">
                    Form Absensi
                </h3>
                <button type="button" onclick="toggleAttendanceModal(false)" class="text-gray-400 hover:text-gray-700 p-2 rounded-full hover:bg-gray-200 transition">
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
                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl">
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
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Pilih Akun <span class="text-rose-500">*</span>
                            </label>
                            <select name="user_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
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
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tanggal
                            </label>
                            <input type="date" value="{{ now()->format('Y-m-d') }}" readonly class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-gray-100 text-sm text-gray-500">
                        </div>

                        {{-- Tipe absensi otomatis --}}
                        <div id="modal-tipe-absensi-container">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Tipe Absensi
                            </label>
                            <input type="text" value="Absen {{ $attendanceType }}" readonly class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-gray-100 font-semibold text-sm text-gray-700">
                            <input type="hidden" name="tipe" value="{{ $attendanceType }}">
                        </div>

                        {{-- Status --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Status <span class="text-rose-500">*</span>
                            </label>
                            <select name="status" id="modalStatusSelect" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="hadir">Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                            </select>
                        </div>

                        {{-- Lampiran Surat Keterangan / Izin --}}
                        <div id="modal-attachment-field" class="hidden">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Upload Surat Keterangan (Sakit / Izin) <span class="text-rose-500">*</span>
                            </label>
                            <input type="file" name="attachment" id="modalAttachmentInput" accept=".pdf" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <p class="text-xs text-gray-400 mt-1">Format file: PDF. Maksimal 2MB.</p>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t bg-gray-50 flex items-center justify-end gap-3">
                    <button type="button" onclick="toggleAttendanceModal(false)" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl text-sm font-bold transition">
                        Batal
                    </button>
                    <button type="submit" id="modalSubmitBtn" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold transition shadow-sm">
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
</x-app-layout>