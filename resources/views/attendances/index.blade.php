{{-- resources/views/attendances/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Absensi
            <span class="text-red-600">
                {{ auth()->user()->name }}
            </span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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

                    @if(in_array(auth()->user()->role, ['auditor']))

                    @if($attendanceWindow['can_attend'])

                    <a href="{{ route('attendances.create') }}"
                        class="px-5 py-3 rounded-xl text-center text-white
                   font-semibold shadow transition
                   bg-blue-600 hover:bg-blue-700">

                        Absensi {{ $tipeAbsen }}

                    </a>

                    @else

                    <button
                        disabled
                        class="px-5 py-3 rounded-xl text-center text-white
                   font-semibold shadow bg-gray-400 cursor-not-allowed">

                        Di Luar Jam Absensi

                    </button>

                    @endif

                    @endif
                </div>

                {{-- Alert --}}
                @if ($message = Session::get('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 rounded-xl text-green-700">
                    {{ $message }}
                </div>
                @endif

                @if ($message = Session::get('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 rounded-xl text-red-700">
                    {{ $message }}
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
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl shadow-sm
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
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl shadow-sm
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

                    {{-- Tombol --}}
                    <div class="flex flex-wrap items-end gap-2">

                        <button
                            type="submit"
                            class="px-4 py-2 bg-gray-800 hover:bg-gray-900
                                   text-white rounded-xl transition shadow-sm">
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
                <div class="bg-white rounded-2xl shadow overflow-hidden border border-gray-100">

                    <div class="overflow-x-auto">

                        <table class="min-w-full text-sm text-left">

                            <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                                <tr>

                                    <th class="px-6 py-4">No.</th>

                                    @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                                    <th class="px-6 py-4">Auditor</th>
                                    @endif

                                    <th class="px-6 py-4">Tanggal</th>
                                    <th class="px-6 py-4">Masuk</th>
                                    <th class="px-6 py-4">Foto Masuk</th>
                                    <th class="px-6 py-4">Pulang</th>
                                    <th class="px-6 py-4">Foto Pulang</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4">Keterangan</th>

                                    @if(auth()->user()->role === 'admin')
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                    @endif

                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">

                                @forelse($attendances as $attendance)

                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        {{ $loop->iteration + ($attendances->currentPage() - 1) * $attendances->perPage() }}
                                    </td>
                                    @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                                    <td class="px-6 py-4 font-medium">
                                        {{ $attendance->user->name ?? '-' }}
                                    </td>
                                    @endif

                                    <td class="px-6 py-4">
                                        {{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}
                                    </td>

                                    <td class="px-6 py-4">
                                        {{ $attendance->check_in ?? '-' }}
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($attendance->photo_check_in)

                                        <a href="{{ asset('storage/' . $attendance->photo_check_in) }}"
                                            target="_blank">

                                            <img
                                                src="{{ asset('storage/' . $attendance->photo_check_in) }}"
                                                alt="Foto masuk"
                                                class="h-14 w-14 rounded-lg object-cover border">

                                        </a>

                                        @else
                                        -
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        {{ $attendance->check_out ?? '-' }}
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($attendance->photo_check_out)

                                        <a href="{{ asset('storage/' . $attendance->photo_check_out) }}"
                                            target="_blank">

                                            <img
                                                src="{{ asset('storage/' . $attendance->photo_check_out) }}"
                                                alt="Foto pulang"
                                                class="h-14 w-14 rounded-lg object-cover border">

                                        </a>

                                        @else
                                        -
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">

                                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                                            {{ $attendance->status === 'hadir' ? 'bg-green-100 text-green-700' :
                                               ($attendance->status === 'sakit' ? 'bg-yellow-100 text-yellow-700' :
                                               ($attendance->status === 'izin' ? 'bg-blue-100 text-blue-700' :
                                               'bg-red-100 text-red-700')) }}">

                                            {{ ucfirst($attendance->status) }}

                                        </span>

                                    </td>

                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $attendance->notes ?? '-' }}
                                    </td>

                                    @if(auth()->user()->role === 'admin')
                                    <td class="px-6 py-4 text-center">

                                        <div class="flex items-center justify-center gap-2">

                                            <a href="{{ route('attendances.edit', $attendance) }}"
                                                class="px-3 py-1 bg-blue-100 hover:bg-blue-200
                                                       text-blue-700 rounded-lg text-xs font-medium transition">
                                                Edit
                                            </a>

                                            <form action="{{ route('attendances.destroy', $attendance) }}"
                                                method="POST"
                                                onsubmit="return confirm('Hapus absensi ini?')">

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    class="px-3 py-1 bg-red-100 hover:bg-red-200
                                                           text-red-700 rounded-lg text-xs font-medium transition">
                                                    Hapus
                                                </button>

                                            </form>

                                        </div>

                                    </td>
                                    @endif

                                </tr>

                                @empty

                                <tr>

                                    <td colspan="{{ auth()->user()->role === 'admin' ? 9 : 8 }}"
                                        class="px-6 py-14 text-center">

                                        <div class="flex flex-col items-center">

                                            <h3 class="text-lg font-semibold text-gray-700">
                                                Data absensi tidak ditemukan
                                            </h3>

                                            <p class="text-sm text-gray-500 mt-1">
                                                Tidak ada data absensi sesuai filter yang dipilih.
                                            </p>

                                        </div>

                                    </td>

                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $attendances->withQueryString()->links() }}
                </div>

            </div>

        </div>
    </div>
</x-app-layout>