<x-app-layout title="Logbook Auditor">

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4">

            {{-- FLASH --}}
            @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded-xl text-sm">
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded-xl text-sm">
                {{ session('error') }}
            </div>
            @endif

            {{-- HEADER --}}
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Logbook Auditor</h2>
                    <p class="text-gray-500 text-sm mt-1">Monitoring aktivitas dan approval logbook auditor</p>
                </div>
                @if(auth()->user()->role === 'auditor')
                <a href="{{ route('logbooks.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Logbook
                </a>
                @endif
            </div>

            {{-- FILTER --}}
            <div class="bg-white rounded-2xl shadow p-4 mb-6">
                <form method="GET" action="{{ route('logbooks.index') }}" class="flex flex-wrap gap-3">
                    <input type="date" name="date" value="{{ request('date') }}"
                        class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:outline-none">

                    @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                    <select name="user_id"
                        class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:outline-none">
                        <option value="">Semua Auditor</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                    @endif

                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold">Filter</button>
                    @if(request('date') || request('user_id'))
                    <a href="{{ route('logbooks.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-xl text-sm font-semibold">Reset</a>
                    @endif
                </form>
            </div>

            {{-- TABLE --}}
            <div class="bg-white rounded-2xl shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3.5 text-left">No</th>
                                @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                                <th class="px-4 py-3.5 text-left">Auditor</th>
                                @endif
                                <th class="px-4 py-3.5 text-left">Tanggal</th>
                                <th class="px-4 py-3.5 text-left">Klien</th>
                                <th class="px-4 py-3.5 text-left">Pekerjaan Utama</th>
                                <th class="px-4 py-3.5 text-left">Status</th>
                                <th class="px-4 py-3.5 text-left">Catatan Revisi</th>
                                <th class="px-4 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($logbooks as $logbook)
                            <tr class="hover:bg-gray-50 transition">

                                <td class="px-4 py-4 text-gray-400">
                                    {{ $logbooks->firstItem() + $loop->index }}
                                </td>

                                @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                                <td class="px-4 py-4 font-semibold text-gray-800">
                                    {{ optional($logbook->user)->name ?? '-' }}
                                </td>
                                @endif

                                <td class="px-4 py-4 text-gray-600 whitespace-nowrap">
                                    {{ $logbook->date->format('d M Y') }}
                                </td>

                                <td class="px-4 py-4 text-gray-700">
                                    {{ optional($logbook->client)->institution_name ?? '-' }}
                                </td>

                                <td class="px-4 py-4 text-gray-700 max-w-xs">
                                    <p class="truncate">{{ $logbook->main_task }}</p>
                                </td>

                                <td class="px-4 py-4">
                                    @if($logbook->status === 'pending')
                                    <span class="bg-yellow-100 text-yellow-700 text-xs px-2.5 py-1 rounded-full font-medium">Pending</span>
                                    @elseif($logbook->status === 'approved')
                                    <span class="bg-green-100 text-green-700 text-xs px-2.5 py-1 rounded-full font-medium">Approved</span>
                                    @elseif($logbook->status === 'revision')
                                    <span class="bg-red-100 text-red-700 text-xs px-2.5 py-1 rounded-full font-medium">Revisi</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4">
                                    @if($logbook->note)
                                    <div class="bg-red-50 border border-red-100 rounded-lg p-2 text-xs text-red-700 max-w-xs">
                                        {{ $logbook->note }}
                                    </div>
                                    @else
                                    <span class="text-gray-300">—</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-center">
                                    {{-- ADMIN --}}
                                    @if(auth()->user()->role === 'admin')
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('logbooks.show', $logbook) }}"
                                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">Detail</a>
                                        <a href="{{ route('logbooks.edit', $logbook) }}"
                                            class="bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">Edit</a>
                                        <form action="{{ route('logbooks.destroy', $logbook) }}" method="POST"
                                            onsubmit="return confirm('Hapus logbook ini?')">
                                            @csrf @method('DELETE')
                                            <button class="bg-red-50 hover:bg-red-100 text-red-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">Hapus</button>
                                        </form>
                                    </div>

                                    {{-- PIMPINAN --}}
                                    @elseif(auth()->user()->role === 'pimpinan')
                                    @if($logbook->status === 'pending')
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('logbooks.show', $logbook) }}"
                                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">Detail</a>
                                        <form action="{{ route('logbooks.approve', $logbook) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button class="bg-green-50 hover:bg-green-100 text-green-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">Approve</button>
                                        </form>
                                        <button type="button"
                                            onclick="document.getElementById('revision-{{ $logbook->id }}').classList.toggle('hidden')"
                                            class="bg-yellow-50 hover:bg-yellow-100 text-yellow-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                            Revisi
                                        </button>
                                    </div>
                                    <div id="revision-{{ $logbook->id }}" class="hidden mt-2 text-left">
                                        <form action="{{ route('logbooks.revision', $logbook) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <textarea name="note" rows="2" required placeholder="Catatan revisi..."
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-yellow-200 focus:outline-none resize-none"></textarea>
                                            <button type="submit"
                                                class="mt-1 w-full bg-yellow-500 hover:bg-yellow-600 text-white text-xs py-1.5 rounded-lg font-semibold transition">
                                                Kirim Revisi
                                            </button>
                                        </form>
                                    </div>
                                    @elseif($logbook->status === 'revision')
                                    <div class="flex items-center justify-center gap-2 mt-2">
                                        <a href="{{ route('logbooks.show', $logbook) }}"
                                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">Detail</a>
                                        <button type="button"
                                            onclick="document.getElementById('edit-revision-{{ $logbook->id }}').classList.toggle('hidden')"
                                            class="bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                            Edit Revisi
                                        </button>
                                    </div>
                                    <div id="edit-revision-{{ $logbook->id }}" class="hidden mt-2 text-left">
                                        <form action="{{ route('logbooks.revision', $logbook) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <textarea name="note" rows="2" required
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-blue-200 focus:outline-none resize-none">{{ $logbook->note }}</textarea>
                                            <button type="submit"
                                                class="mt-1 w-full bg-blue-600 hover:bg-blue-700 text-white text-xs py-1.5 rounded-lg font-semibold transition">
                                                Update
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <span class="text-green-600 text-xs font-semibold">✓ Selesai</span>
                                        <a href="{{ route('logbooks.show', $logbook) }}"
                                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition mt-1">Detail</a>
                                    </div>
                                    @endif

                                    {{-- AUDITOR --}}
                                    @else
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('logbooks.show', $logbook) }}"
                                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">Detail</a>
                                        @if($logbook->status === 'pending' && !$logbook->created_at->addDay()->isPast())
                                        <a href="{{ route('logbooks.edit', $logbook) }}"
                                            class="bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">Edit</a>
                                        @endif
                                    </div>
                                    @endif
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ in_array(auth()->user()->role, ['admin', 'pimpinan']) ? 8 : 7 }}"
                                    class="py-16 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-gray-400 text-sm">Belum ada data logbook</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($logbooks->hasPages())
                <div class="px-4 py-4 border-t border-gray-100">
                    {{ $logbooks->withQueryString()->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>

</x-app-layout>