{{-- resources/views/attendances/index.blade.php --}}

<x-app-layout title="Absensi">

    <div class="py-10">

        <div class="max-w-7xl mx-auto px-4">

            {{-- Main Card --}}
            <div class="bg-white shadow rounded-2xl p-6">

                @php
                $tipeAbsen = $attendanceWindow['type'];
                @endphp

                {{-- Header --}}
                <div class="flex flex-col gap-4 md:flex-row md:justify-between md:items-center mb-6">

                    <div>

                        <h2 class="text-2xl font-bold text-gray-800">
                            Riwayat Absensi
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Jam WIB:
                            Masuk {{ $attendanceWindow['check_in_start'] }} - {{ $attendanceWindow['check_in_end'] }},
                            Pulang {{ $attendanceWindow['check_out_start'] }} - {{ $attendanceWindow['check_out_end'] }}
                        </p>

                    </div>

                    {{-- Tombol Absensi --}}
                    @if(auth()->user()->role === 'auditor')

                    <div class="flex flex-col items-end gap-2">

                        @if($attendanceWindow['can_attend'])

                        <a href="{{ route('attendances.create') }}"
                            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl
                                   bg-blue-600 hover:bg-blue-700
                                   text-white font-semibold shadow-sm transition">

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

                            Absensi {{ $tipeAbsen }}

                        </a>

                        @else

                        <button
                            disabled
                            class="inline-flex items-center gap-2 px-5 py-3 rounded-xl
                                   bg-gray-400 cursor-not-allowed
                                   text-white font-semibold shadow-sm">

                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="w-5 h-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor">

                                <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M18.364 5.636L5.636 18.364" />

                            </svg>

                            Di Luar Jam Absensi

                        </button>

                        @endif

                        <p class="text-xs text-gray-500 text-right max-w-xs">
                            Absensi tersedia pukul
                            {{ $attendanceWindow['check_in_start'] }}-{{ $attendanceWindow['check_in_end'] }}
                            dan
                            {{ $attendanceWindow['check_out_start'] }}-{{ $attendanceWindow['check_out_end'] }} WIB
                        </p>

                    </div>

                    @endif

                </div>

                {{-- Alert --}}
                @if(session('success'))

                <div class="mb-5 p-4 rounded-xl border border-green-200 bg-green-50 text-green-700">
                    {{ session('success') }}
                </div>

                @endif

                @if(session('error'))

                <div class="mb-5 p-4 rounded-xl border border-red-200 bg-red-50 text-red-700">
                    {{ session('error') }}
                </div>

                @endif

                {{-- Filter --}}
                <form method="GET"
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
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                            <option value="">Semua Auditor</option>

                            @foreach($users as $user)

                            <option value="{{ $user->id }}"
                                {{ (string) $user_id === (string) $user->id ? 'selected' : '' }}>

                                {{ $user->name }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                    @endif

                    {{-- Button --}}
                    <div class="flex flex-wrap items-end gap-2">

                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700
                                   text-white rounded-xl transition text-sm font-semibold">

                            Filter

                        </button>

                        <a href="{{ route('attendances.index') }}"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300
                                   text-gray-700 rounded-xl transition shadow-sm">

                            Reset

                        </a>

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

                    </div>

                </form>

                {{-- Table --}}
                <div class="border border-gray-200 rounded-2xl overflow-hidden">

                    <div class="overflow-x-auto">

                        <table class="min-w-full text-sm">

                            {{-- Table Head --}}
                            <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">

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
                                        Masuk
                                    </th>

                                    <th class="px-6 py-4 text-center">
                                        Foto Masuk
                                    </th>

                                    <th class="px-6 py-4 text-left">
                                        Pulang
                                    </th>

                                    <th class="px-6 py-4 text-center">
                                        Foto Pulang
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

                            {{-- Table Body --}}
                            <tbody class="divide-y divide-gray-100 bg-white">

                                @forelse($attendances as $attendance)

                                <tr class="hover:bg-gray-50 transition">

                                    {{-- No --}}
                                    <td class="px-6 py-4 text-center text-gray-500 font-medium">

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

                                    {{-- Foto Masuk --}}
                                    <td class="px-6 py-4 text-center">

                                        @if($attendance->photo_check_in)

                                        <a href="{{ asset('storage/' . $attendance->photo_check_in) }}"
                                            target="_blank">

                                            <img
                                                src="{{ asset('storage/' . $attendance->photo_check_in) }}"
                                                alt="Foto masuk"
                                                class="h-14 w-14 mx-auto rounded-xl object-cover
                                                       border border-gray-200 hover:scale-105 transition">

                                        </a>

                                        @else

                                        <span class="text-gray-400">-</span>

                                        @endif

                                    </td>

                                    {{-- Pulang --}}
                                    <td class="px-6 py-4 text-gray-700">

                                        {{ $attendance->check_out ?? '-' }}

                                    </td>

                                    {{-- Foto Pulang --}}
                                    <td class="px-6 py-4 text-center">

                                        @if($attendance->photo_check_out)

                                        <a href="{{ asset('storage/' . $attendance->photo_check_out) }}"
                                            target="_blank">

                                            <img
                                                src="{{ asset('storage/' . $attendance->photo_check_out) }}"
                                                alt="Foto pulang"
                                                class="h-14 w-14 mx-auto rounded-xl object-cover
                                                       border border-gray-200 hover:scale-105 transition">

                                        </a>

                                        @else

                                        <span class="text-gray-400">-</span>

                                        @endif

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

                                    {{-- Aksi --}}
                                    @if(auth()->user()->role === 'admin')

                                    <td class="px-6 py-4 text-center">

                                        <div class="flex justify-center gap-2">

                                            <a href="{{ route('attendances.edit', $attendance) }}"
                                                class="px-3 py-1 rounded-lg text-xs font-medium
                                                       bg-blue-100 hover:bg-blue-200
                                                       text-blue-700 transition">

                                                Edit

                                            </a>

                                            <form
                                                action="{{ route('attendances.destroy', $attendance) }}"
                                                method="POST"
                                                onsubmit="return confirm('Hapus absensi ini?')">

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    class="px-3 py-1 rounded-lg text-xs font-medium
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

                                <tr>

                                    <td colspan="{{ auth()->user()->role === 'admin' ? 10 : (auth()->user()->role === 'pimpinan' ? 9 : 8) }}"
                                        class="px-6 py-14 text-center">

                                        <div class="flex flex-col items-center">

                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="w-16 h-16 text-gray-300 mb-3"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor">

                                                <path stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M9.172 9.172a4 4 0 015.656 5.656M15 15l6 6m-6-6a6 6 0 10-8.485-8.485A6 6 0 0015 15z" />

                                            </svg>

                                            <h3 class="text-lg font-semibold text-gray-700">
                                                Data absensi tidak ditemukan
                                            </h3>

                                            <p class="text-sm text-gray-500 mt-1">
                                                Tidak ada data absensi sesuai filter yang dipilih.
                                            </p>

                                            <a href="{{ route('attendances.index') }}"
                                                class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700
                                                       text-white rounded-xl transition">

                                                Reset Filter

                                            </a>

                                        </div>

                                    </td>

                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

                {{-- Pagination --}}
                <div class="mt-5">
                    {{ $attendances->withQueryString()->links() }}
                </div>

            </div>

        </div>

    </div>

</x-app-layout>