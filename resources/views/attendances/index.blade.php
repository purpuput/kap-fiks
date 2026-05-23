{{-- resources/views/attendances/index.blade.php --}}

<x-app-layout title="Absensi">

    <div class="py-10">

        <div class="max-w-7xl mx-auto px-4">

            {{-- ===================================================== --}}
            {{-- HEADER --}}
            {{-- ===================================================== --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

                <div>

                    <h2 class="text-3xl font-bold text-gray-800">
                        Absensi Auditor
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Monitoring dan riwayat absensi auditor.
                    </p>

                </div>

                {{-- User info --}}
                <div class="text-left md:text-right">

                    {{-- Jam realtime --}}
                    <div class="flex items-center gap-1 text-sm text-gray-600">

                        <span class="font-medium">
                            Jam:
                        </span>

                        <span
                            id="clock"
                            class="text-blue-600 font-semibold">
                        </span>

                    </div>

                </div>

            </div>

            {{-- ===================================================== --}}
            {{-- ALERT --}}
            {{-- ===================================================== --}}
            @if(session('success'))

            <div class="mb-6 p-4 rounded-2xl border border-green-200 bg-green-50 text-green-700">

                {{ session('success') }}

            </div>

            @endif

            @if(session('error'))

            <div class="mb-6 p-4 rounded-2xl border border-red-200 bg-red-50 text-red-700">

                {{ session('error') }}

            </div>

            @endif

            {{-- ===================================================== --}}
            {{-- DASHBOARD AUDITOR --}}
            {{-- ===================================================== --}}
            @if(auth()->user()->role === 'auditor')

            @php

            // Absensi hari ini
            $todayAttendance = \App\Models\Attendance::where('user_id', auth()->id())
            ->where('date', now()->toDateString())
            ->first();

            // Default tipe
            $attendanceType = 'Masuk';

            // Jika sudah masuk
            if ($todayAttendance && $todayAttendance->check_in) {
            $attendanceType = 'Pulang';
            }

            // Jika selesai
            $isCompleted =
            $todayAttendance &&
            $todayAttendance->check_in &&
            $todayAttendance->check_out;

            @endphp

            <div class="mb-8">

                <div class="bg-gradient-to-r from-blue-600 to-blue-700
                            rounded-3xl p-6 text-white shadow-lg">

                    {{-- Header --}}
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

                        <div>

                            <p class="text-blue-100 text-sm">
                                Absensi Hari Ini
                            </p>

                            <h2 class="text-3xl font-bold mt-1">

                                {{ now()->translatedFormat('l, d F Y') }}

                            </h2>

                            <p class="text-blue-100 mt-2 text-sm">

                                @if(!$todayAttendance)

                                Belum melakukan absensi.

                                @elseif($todayAttendance->check_in && !$todayAttendance->check_out)

                                Sudah absen masuk.

                                @else

                                Absensi hari ini selesai.

                                @endif

                            </p>

                        </div>

                        {{-- Tombol --}}
                        <div>

                            @if($isCompleted)

                            <button
                                disabled
                                class="px-6 py-3 rounded-2xl bg-white/20
                                       text-white font-semibold cursor-not-allowed">

                                ✓ Absensi Selesai

                            </button>

                            @else

                            <a href="{{ route('attendances.create') }}"
                                class="inline-flex items-center gap-2 px-6 py-3
                                       bg-white text-blue-700 rounded-2xl
                                       font-bold hover:bg-blue-50 transition">

                                {{-- Icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor">

                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4v16m8-8H4" />

                                </svg>

                                Tambahkan Absen {{ $attendanceType }}

                            </a>

                            @endif

                        </div>

                    </div>

                    {{-- Statistik --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">

                        {{-- Masuk --}}
                        <div class="bg-white/10 rounded-2xl p-4">

                            <p class="text-sm text-blue-100">
                                Jam Masuk
                            </p>

                            <h3 class="text-2xl font-bold mt-1">

                                {{ $todayAttendance->check_in ?? '--:--' }}

                            </h3>

                        </div>

                        {{-- Pulang --}}
                        <div class="bg-white/10 rounded-2xl p-4">

                            <p class="text-sm text-blue-100">
                                Jam Pulang
                            </p>

                            <h3 class="text-2xl font-bold mt-1">

                                {{ $todayAttendance->check_out ?? '--:--' }}

                            </h3>

                        </div>

                        {{-- Status --}}
                        <div class="bg-white/10 rounded-2xl p-4">

                            <p class="text-sm text-blue-100">
                                Status
                            </p>

                            <h3 class="text-2xl font-bold mt-1 capitalize">

                                {{ $todayAttendance->status ?? 'Belum Absen' }}

                            </h3>

                        </div>

                    </div>

                </div>

            </div>

            @endif

            {{-- ADMIN --}}
            @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))

            @php

            // Total auditor
            $totalAuditor = \App\Models\User::where('role', 'auditor')->count();

            // Hadir hari ini
            $hadirToday = \App\Models\Attendance::whereDate('date', now())
            ->where('status', 'hadir')
            ->count();

            // Sakit
            $sakitToday = \App\Models\Attendance::whereDate('date', now())
            ->where('status', 'sakit')
            ->count();

            // Izin
            $izinToday = \App\Models\Attendance::whereDate('date', now())
            ->where('status', 'izin')
            ->count();

            // Belum absen
            $belumAbsen = $totalAuditor - ($hadirToday + $sakitToday + $izinToday);

            // Auditor hadir hari ini
            $attendanceToday = \App\Models\Attendance::with('user')
            ->whereDate('date', now())
            ->where('status', 'hadir')
            ->get();

            // Auditor yang belum absen
            $notAttendance = \App\Models\User::where('role', 'auditor')
            ->whereNotIn('id', function ($query) {

            $query->select('user_id')
            ->from('attendances')
            ->whereDate('date', now());

            })
            ->get();

            @endphp

            <div class="mb-8">

                <div class="bg-gradient-to-r from-blue-800 to-blue-900
                            rounded-3xl p-6 text-white shadow-lg">

                    {{-- Header --}}
                    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                        <div>

                            <p class="text-slate-300 text-sm">
                                Monitoring Hari Ini
                            </p>

                            <h2 class="text-3xl font-bold mt-1">

                                {{ now()->translatedFormat('l, d F Y') }}

                            </h2>

                        </div>

                        @if(auth()->user()->role === 'admin')
                        <div>
                            <a href="{{ route('attendances.create') }}"
                                class="inline-flex items-center gap-2 px-6 py-3
                                       bg-white text-blue-800 rounded-2xl
                                       font-bold hover:bg-blue-50 transition">
                                Tambahkan Absen Admin
                            </a>
                        </div>
                        @endif

                    </div>

                    {{-- Statistik --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                        {{-- Hadir --}}
                        <div class="bg-white/10 rounded-2xl p-5">

                            <p class="text-sm text-slate-300">
                                Hadir
                            </p>

                            <h3 class="text-3xl font-bold mt-2">

                                {{ $hadirToday }}

                            </h3>

                        </div>

                        {{-- Sakit --}}
                        <div class="bg-white/10 rounded-2xl p-5">

                            <p class="text-sm text-slate-300">
                                Sakit
                            </p>

                            <h3 class="text-3xl font-bold mt-2">

                                {{ $sakitToday }}

                            </h3>

                        </div>

                        {{-- Izin --}}
                        <div class="bg-white/10 rounded-2xl p-5">

                            <p class="text-sm text-slate-300">
                                Izin
                            </p>

                            <h3 class="text-3xl font-bold mt-2">

                                {{ $izinToday }}

                            </h3>

                        </div>

                        {{-- Belum --}}
                        <div class="bg-white/10 rounded-2xl p-5">

                            <p class="text-sm text-slate-300">
                                Belum Absen
                            </p>

                            <h3 class="text-3xl font-bold mt-2">

                                {{ $belumAbsen }}

                            </h3>

                        </div>

                    </div>

                    {{-- List absensi --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mt-6">

                        {{-- Sudah absen --}}
                        <div class="bg-white/10 rounded-2xl p-5">

                            <h3 class="font-semibold text-lg mb-4">
                                Auditor Hadir Hari Ini
                            </h3>

                            @forelse($attendanceToday as $item)

                            <div class="flex items-center justify-between py-2 border-b border-white/10">

                                <div>

                                    <p class="font-medium">

                                        {{ $item->user->name }}

                                    </p>

                                    <p class="text-xs text-slate-300">

                                        {{ $item->check_in ?? '-' }}

                                    </p>

                                </div>

                                <span class="text-green-300 text-sm">
                                    Hadir
                                </span>

                            </div>

                            @empty

                            <p class="text-slate-300 text-sm">
                                Belum ada auditor yang hadir.
                            </p>

                            @endforelse

                        </div>

                        {{-- Belum absen --}}
                        <div class="bg-white/10 rounded-2xl p-5">

                            <h3 class="font-semibold text-lg mb-4">
                                Belum Absen
                            </h3>

                            @forelse($notAttendance as $user)

                            <div class="flex items-center justify-between py-2 border-b border-white/10">

                                <p class="font-medium">

                                    {{ $user->name }}

                                </p>

                                <span class="text-yellow-300 text-sm">
                                    Belum Absen
                                </span>

                            </div>

                            @empty

                            <p class="text-slate-300 text-sm">
                                Semua auditor sudah absen.
                            </p>

                            @endforelse

                        </div>

                    </div>

                </div>

            </div>

            @endif

            {{-- CARD RIWAYAT --}}
            <div class="bg-white border border-gray-100 shadow rounded-3xl p-6">

                {{-- Header --}}
                <div class="mb-5">

                    <h2 class="text-2xl font-bold text-gray-800">
                        Riwayat Absensi
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Menampilkan riwayat absensi auditor.
                    </p>

                </div>

                {{-- FILTER --}}
                <form
                    id="filterForm"
                    method="GET"
                    class="mb-6 flex flex-wrap items-end gap-3">

                    {{-- Tanggal --}}
                    <div class="w-full md:w-52">

                        <label class="block text-sm font-semibold mb-2">
                            Tanggal
                        </label>

                        <input
                            type="date"
                            name="date"
                            value="{{ $date ?? '' }}"
                            onchange="document.getElementById('filterForm').submit()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                    </div>

                    {{-- Auditor --}}
                    @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))

                    <div class="w-full md:w-60">

                        <label class="block text-sm font-semibold mb-2">
                            Auditor
                        </label>

                        <select
                            name="user_id"
                            onchange="document.getElementById('filterForm').submit()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                            <option value="">
                                Semua Auditor
                            </option>

                            @foreach($users as $user)

                            <option value="{{ $user->id }}"
                                {{ (string) $user_id === (string) $user->id ? 'selected' : '' }}>

                                {{ $user->name }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                    @endif

                    {{-- Reset --}}
                    <a href="{{ route('attendances.index') }}"
                        class="px-4 py-2 bg-yellow-200 hover:bg-yellow-300
                               text-yellow-700 font-semibold border border-yellow-300
                               rounded-xl transition shadow-sm">

                        Reset

                    </a>

                    {{-- Export --}}
                    @if(auth()->user()->role === 'admin')

                    <a href="{{ route('attendances.export.pdf', request()->query()) }}"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700
                               text-white rounded-xl transition shadow-sm">

                        Export PDF

                    </a>

                    <a href="{{ route('attendances.export.excel', request()->query()) }}"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700
                               text-white rounded-xl transition shadow-sm">

                        Export Excel

                    </a>

                    @endif

                </form>

                {{-- ===================================================== --}}
                {{-- TABLE --}}
                {{-- ===================================================== --}}
                <div class="border border-gray-200 rounded-3xl overflow-hidden">

                    <div class="overflow-x-auto">

                        <table class="min-w-full text-sm">

                            {{-- Head --}}
                            <thead class="bg-gray-50 text-bold uppercase text-l ">

                                <tr>

                                    <th class="px-6 py-4 text-center">
                                        No
                                    </th>

                                    @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))

                                    <th class="px-6 py-4 text-left">
                                        Auditor
                                    </th>

                                    @endif

                                    <th class="px-6 py-4 text-left">
                                        Tanggal
                                    </th>

                                    <th class="px-6 py-4 text-left">
                                        Jam Masuk
                                    </th>

                                    <th class="px-6 py-4 text-left">
                                        Jam Pulang
                                    </th>

                                    <th class="px-6 py-4 text-center">
                                        Status
                                    </th>

                                    <th class="px-6 py-4 text-left">
                                        Keterangan
                                    </th>

                                    @if(auth()->user()->role === 'admin')

                                    <th class="px-6 py-4 text-center">
                                        Aksi
                                    </th>

                                    @endif

                                </tr>

                            </thead>

                            {{-- Body --}}
                            <tbody class="divide-y divide-gray-100 bg-white">

                                @forelse($attendances as $attendance)

                                <tr class="hover:bg-gray-50 transition">

                                    {{-- No --}}
                                    <td class="px-6 py-4 text-center text-gray-500">

                                        {{ $loop->iteration + ($attendances->currentPage() - 1) * $attendances->perPage() }}

                                    </td>

                                    {{-- Auditor --}}
                                    @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))

                                    <td class="px-6 py-4 font-medium text-gray-700">

                                        {{ $attendance->user->name ?? '-' }}

                                    </td>

                                    @endif

                                    {{-- Tanggal --}}
                                    <td class="px-6 py-4 text-gray-700">

                                        {{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}

                                    </td>

                                    {{-- Masuk --}}
                                    <td class="px-6 py-4 text-gray-700">

                                        {{ $attendance->check_in ?? '-' }}

                                    </td>

                                    {{-- Pulang --}}
                                    <td class="px-6 py-4 text-gray-700">

                                        {{ $attendance->check_out ?? '-' }}

                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4 text-center">

                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $attendance->status === 'hadir'
                                                ? 'bg-green-100 text-green-700'
                                                : ($attendance->status === 'sakit'
                                                ? 'bg-yellow-100 text-yellow-700'
                                                : ($attendance->status === 'izin'
                                                ? 'bg-blue-100 text-blue-700'
                                                : 'bg-red-100 text-red-700')) }}">

                                            {{ ucfirst($attendance->status) }}

                                        </span>

                                    </td>

                                    {{-- Keterangan --}}
                                    <td class="px-6 py-4 text-gray-600">

                                        {{ $attendance->notes ?? '-' }}

                                    </td>

                                    {{-- Hapus --}}
                                    @if(auth()->user()->role === 'admin')

                                    {{-- Aksi --}}
                                    <td class="px-6 py-4 text-center">

                                        <div class="flex items-center justify-center gap-2">

                                            {{-- Tombol edit --}}
                                            <a
                                                href="{{ route('attendances.edit', $attendance) }}"
                                                class="inline-flex items-center px-3 py-1.5
                                                rounded-lg text-xs font-semibold
                                                bg-blue-100 hover:bg-blue-200
                                                text-blue-700 transition">

                                                Edit

                                            </a>

                                            {{-- Tombol hapus --}}
                                            <form
                                                action="{{ route('attendances.destroy', $attendance) }}"
                                                method="POST"
                                                onsubmit="return confirm('Hapus data ini?')">

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center px-3 py-1.5
                                                    rounded-lg text-xs font-semibold
                                                    bg-red-100 hover:bg-red-200
                                                    text-red-700 transition">

                                                    Hapus

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                    @endif

                                </tr>

                                @empty

                                {{-- Data kosong --}}
                                <tr>

                                    <td colspan="{{ auth()->user()->role === 'admin' ? 8 : (auth()->user()->role === 'pimpinan' ? 7 : 6) }}"
                                        class="px-6 py-14 text-center">

                                        <div class="flex flex-col items-center">

                                            {{-- Icon --}}
                                            <div class="w-20 h-20 rounded-full bg-gray-100
                                        flex items-center justify-center mb-4">

                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="w-10 h-10 text-gray-400"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor">

                                                    <path stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M9 12h6m-6 4h6M7 4h7l5 5v11a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z" />

                                                </svg>

                                            </div>

                                            {{-- Judul --}}
                                            <h3 class="text-lg font-semibold text-gray-700">
                                                Data tidak ditemukan
                                            </h3>

                                            {{-- Deskripsi --}}
                                            <p class="text-sm text-gray-500 mt-1">
                                                Tidak ada data absensi yang tersedia.
                                            </p>

                                        </div>

                                    </td>

                                </tr>>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

                {{-- Pagination --}}
                <div class="mt-6">

                    {{ $attendances->withQueryString()->links() }}

                </div>

            </div>

        </div>

    </div>

    {{-- ===================================================== --}}
    {{-- CLOCK --}}
    {{-- ===================================================== --}}
    <script>
        // Update jam realtime
        function updateClock() {

            const now = new Date();

            const time = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });

            document.getElementById('clock').innerHTML = time + ' WIB';
        }

        // Update tiap detik
        setInterval(updateClock, 1000);

        // Jalankan pertama kali
        updateClock();
    </script>

</x-app-layout>