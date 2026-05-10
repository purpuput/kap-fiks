<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Logbook Auditor
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg p-6">

                <!-- Header -->
                <div class="flex flex-col gap-4 md:flex-row md:justify-between md:items-center mb-4">

                    <div>
                        <h2 class="text-xl font-semibold">
                            Daftar Logbook
                        </h2>

                        <p class="text-sm text-gray-600 mt-1">
                            Monitoring aktivitas dan approval logbook auditor.
                        </p>
                    </div>

                </div>

                <!-- Success -->
                @if ($message = Session::get('success'))

                <div class="mb-4 p-4 bg-green-100 border border-green-400 rounded text-green-700">
                    {{ $message }}
                </div>

                @endif

                <!-- Filter -->
                <form method="GET"
                    class="mb-6 flex flex-wrap items-end gap-3">

                    {{-- Filter tanggal --}}
                    <div class="w-full md:w-52">

                        <label class="block text-sm font-semibold mb-2">
                            Tanggal
                        </label>

                        <input
                            type="date"
                            name="date"
                            value="{{ request('date') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl shadow-sm
                            focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                    </div>

                    {{-- Filter auditor --}}
                    @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))

                    <div class="w-full md:w-60">

                        <label class="block text-sm font-semibold mb-2">
                            Auditor
                        </label>

                        <select
                            name="user_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl shadow-sm
                                focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                            <option value="">
                                Semua Auditor
                            </option>

                            @foreach($users as $user)

                            <option value="{{ $user->id }}"
                                {{ request('user_id') == $user->id ? 'selected' : '' }}>

                                {{ $user->name }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                    @endif

                    <!-- Tombol -->
                    <div class="flex flex-wrap items-end gap-2">

                        <button
                            type="submit"
                            class="px-4 py-2 bg-gray-800 hover:bg-gray-900
                            text-white rounded-xl transition shadow-sm">

                            Filter

                        </button>

                        <a href="{{ route('logbooks.index') }}"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300
                            text-gray-700 rounded-xl transition shadow-sm">

                            Reset

                        </a>

                        {{-- Tambah hanya auditor --}}
                        @if(auth()->user()->role === 'auditor')

                        <a href="{{ route('logbooks.create') }}"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700
                                text-white rounded-xl transition shadow-sm">

                            + Tambah

                        </a>

                        @endif

                    </div>

                </form>

                <!-- Table -->
                <div class="bg-white rounded-2xl shadow overflow-hidden">

                    <div class="overflow-x-auto">

                        <table class="min-w-full text-sm text-left">

                            <thead class="bg-gray-100 text-gray-700 uppercase text-xs">

                                <tr>

                                    <th class="px-6 py-4">No</th>

                                    @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                                    <th class="px-6 py-4">Auditor</th>
                                    @endif

                                    <th class="px-6 py-4">Tanggal</th>
                                    <th class="px-6 py-4">Aktivitas</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4">Catatan Revisi</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>

                                </tr>

                            </thead>

                            <tbody class="divide-y divide-gray-100">

                                @forelse($logbooks as $logbook)

                                <tr class="hover:bg-gray-50 transition">

                                    <!-- No -->
                                    <td class="px-6 py-4">
                                        {{ $logbooks->firstItem() + $loop->index }}
                                    </td>

                                    <!-- Auditor -->
                                    @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))

                                    <td class="px-6 py-4 font-medium">
                                        {{ optional($logbook->user)->name ?? '-' }}
                                    </td>

                                    @endif

                                    <!-- Tanggal -->
                                    <td class="px-6 py-4">
                                        {{ $logbook->date->format('d M Y') }}
                                    </td>

                                    <!-- Aktivitas -->
                                    <td class="px-6 py-4 text-gray-700">
                                        {{ $logbook->activity }}
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4">

                                        @if($logbook->status === 'pending')

                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                            Pending
                                        </span>

                                        @elseif($logbook->status === 'approved')

                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                            Approved
                                        </span>

                                        @elseif($logbook->status === 'revision')

                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                            Revision
                                        </span>

                                        @endif

                                    </td>

                                    <!-- Catatan -->
                                    <td class="px-6 py-4 text-gray-600">

                                        @if($logbook->note)

                                        <div class="bg-red-50 border border-red-100 rounded-lg p-3 text-sm text-red-700">
                                            {{ $logbook->note }}
                                        </div>

                                        @else

                                        -

                                        @endif

                                    </td>

                                    <!-- Aksi -->
                                    <td class="px-6 py-4 text-center">

                                        {{-- ADMIN --}}
                                        @if(auth()->user()->role === 'admin')

                                        <div class="flex items-center justify-center gap-2">

                                            <a href="{{ route('logbooks.edit', $logbook) }}"
                                                class="px-3 py-1 bg-blue-100 hover:bg-blue-200
                                                text-blue-700 rounded-lg text-xs font-medium transition">

                                                Edit

                                            </a>

                                            <form action="{{ route('logbooks.destroy', $logbook) }}"
                                                method="POST"
                                                onsubmit="return confirm('Hapus logbook ini?')">

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    class="px-3 py-1 bg-red-100 hover:bg-red-200
                    text-red-700 rounded-lg text-xs font-medium transition">

                                                    Hapus

                                                </button>

                                            </form>

                                        </div>

                                        {{-- PIMPINAN --}}
                                        @elseif(auth()->user()->role === 'pimpinan')

                                        {{-- STATUS PENDING --}}
                                        @if($logbook->status === 'pending')

                                        <div class="flex items-center justify-center gap-2">

                                            {{-- Approve --}}
                                            <form action="{{ route('logbooks.approve', $logbook) }}"
                                                method="POST">

                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    class="px-3 py-1 bg-green-100 hover:bg-green-200
                        text-green-700 rounded-lg text-xs font-medium transition">

                                                    Approve

                                                </button>

                                            </form>

                                            {{-- Revisi --}}
                                            <button
                                                type="button"
                                                onclick="document.getElementById('revision-{{ $logbook->id }}').classList.toggle('hidden')"
                                                class="px-3 py-1 bg-yellow-100 hover:bg-yellow-200
                    text-yellow-700 rounded-lg text-xs font-medium transition">

                                                Revisi

                                            </button>

                                        </div>

                                        <!-- Form Revisi -->
                                        <div id="revision-{{ $logbook->id }}"
                                            class="hidden mt-3">

                                            <form action="{{ route('logbooks.revision', $logbook) }}"
                                                method="POST">

                                                @csrf
                                                @method('PATCH')

                                                <textarea
                                                    name="note"
                                                    rows="3"
                                                    required
                                                    placeholder="Masukkan catatan revisi..."
                                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>

                                                <button type="submit"
                                                    class="mt-2 px-3 py-1 bg-red-600 hover:bg-red-700
                        text-white rounded-lg text-xs transition">

                                                    Kirim Revisi

                                                </button>

                                            </form>

                                        </div>

                                        {{-- STATUS REVISION --}}
                                        @elseif($logbook->status === 'revision')

                                        <div class="flex items-center justify-center">

                                            {{-- Edit Revisi --}}
                                            <button
                                                type="button"
                                                onclick="document.getElementById('edit-revision-{{ $logbook->id }}').classList.toggle('hidden')"
                                                class="px-3 py-1 bg-blue-100 hover:bg-blue-200
                    text-blue-700 rounded-lg text-xs font-medium transition">

                                                Edit Revisi

                                            </button>

                                        </div>

                                        <!-- Form Edit Revisi -->
                                        <div id="edit-revision-{{ $logbook->id }}"
                                            class="hidden mt-3">

                                            <form action="{{ route('logbooks.revision', $logbook) }}"
                                                method="POST">

                                                @csrf
                                                @method('PATCH')

                                                <textarea
                                                    name="note"
                                                    rows="3"
                                                    required
                                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ $logbook->note }}</textarea>

                                                <button type="submit"
                                                    class="mt-2 px-3 py-1 bg-blue-600 hover:bg-blue-700
                        text-white rounded-lg text-xs transition">

                                                    Update Revisi

                                                </button>

                                            </form>

                                        </div>

                                        {{-- STATUS APPROVED --}}
                                        @else

                                        <span class="text-gray-400 text-sm">
                                            Selesai
                                        </span>

                                        @endif

                                        {{-- AUDITOR --}}
                                        @else

                                        @if(
                                        $logbook->status === 'pending' &&
                                        !$logbook->created_at->addDay()->isPast()
                                        )

                                        <div class="flex items-center justify-center">

                                            <a href="{{ route('logbooks.edit', $logbook) }}"
                                                class="px-3 py-1 bg-blue-100 hover:bg-blue-200
                    text-blue-700 rounded-lg text-xs font-medium transition">

                                                Edit

                                            </a>

                                        </div>

                                        @else

                                        <span class="text-gray-400 text-sm">
                                            Tidak tersedia
                                        </span>

                                        @endif

                                        @endif

                                    </td>

                                </tr>

                                @empty

                                <tr>

                                    <td colspan="{{ in_array(auth()->user()->role, ['admin', 'pimpinan']) ? 7 : 6 }}"
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
                                                Data logbook tidak ditemukan
                                            </h3>

                                            <p class="text-sm text-gray-500 mt-1">
                                                Tidak ada data logbook sesuai filter yang dipilih.
                                            </p>

                                            <a href="{{ route('logbooks.index') }}"
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

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $logbooks->withQueryString()->links() }}
                </div>

            </div>

        </div>

    </div>

</x-app-layout>