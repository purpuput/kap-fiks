<x-app-layout title="Data Klien">

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
                    <h2 class="text-2xl font-bold text-gray-800">Data Klien</h2>
                    <p class="text-gray-500 text-sm mt-1">Kelola data klien yang menggunakan jasa KAP</p>
                </div>
                @if(auth()->user()->role === 'auditor')
                <a href="{{ route('clients.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Klien
                </a>
                @endif
            </div>

            {{-- FILTER --}}
            <div class="bg-white rounded-2xl shadow p-4 mb-6">
                <form method="GET" action="{{ route('clients.index') }}" class="flex flex-wrap gap-3">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama instansi..."
                        class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:outline-none flex-1 min-w-48">

                    <select name="status"
                        class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>

                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold">Filter</button>
                    @if(request('search') || request('status'))
                    <a href="{{ route('clients.index') }}"
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
                                <th class="px-4 py-3.5 text-left">Instansi</th>
                                <th class="px-4 py-3.5 text-left">Pimpinan</th>
                                <th class="px-4 py-3.5 text-left">Telepon</th>
                                <th class="px-4 py-3.5 text-left">Status Klien</th>
                                <th class="px-4 py-3.5 text-left">Approval</th>
                                <th class="px-4 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($clients as $client)
                            <tr class="hover:bg-gray-50 transition">

                                <td class="px-4 py-4 text-gray-400">{{ $loop->iteration }}</td>

                                <td class="px-4 py-4">
                                    <p class="font-semibold text-gray-800">{{ $client->institution_name }}</p>
                                    @if($client->institution_type ?? false)
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $client->institution_type }}</p>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-gray-600">{{ $client->leader_name }}</td>

                                <td class="px-4 py-4 text-gray-600">{{ $client->phone }}</td>

                                <td class="px-4 py-4">
                                    @if(($client->status ?? 'aktif') === 'aktif')
                                    <span class="bg-green-100 text-green-700 text-xs px-2.5 py-1 rounded-full font-medium">Aktif</span>
                                    @else
                                    <span class="bg-gray-100 text-gray-500 text-xs px-2.5 py-1 rounded-full font-medium">Nonaktif</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4">
                                    @if($client->approval_status === 'pending')
                                    <span class="bg-yellow-100 text-yellow-700 text-xs px-2.5 py-1 rounded-full font-medium">Pending</span>
                                    @elseif($client->approval_status === 'disetujui')
                                    <span class="bg-green-100 text-green-700 text-xs px-2.5 py-1 rounded-full font-medium">Disetujui</span>
                                    @elseif($client->approval_status === 'revisi')
                                    <span class="bg-red-100 text-red-700 text-xs px-2.5 py-1 rounded-full font-medium">Revisi</span>
                                    @else
                                    <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-2 flex-wrap">

                                        {{-- Detail --}}
                                        <a href="{{ route('clients.show', $client) }}"
                                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                            Detail
                                        </a>

                                        {{-- Admin & Auditor: Edit + Hapus --}}
                                        @if(in_array(auth()->user()->role, ['admin', 'auditor']))
                                        <a href="{{ route('clients.edit', $client) }}"
                                            class="bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                            Edit
                                        </a>
                                        <form id="delete-form-{{ $client->id }}"
                                            action="{{ route('clients.destroy', $client) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="button" onclick="confirmDelete('{{ $client->id }}')"
                                                class="bg-red-50 hover:bg-red-100 text-red-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                                Hapus
                                            </button>
                                        </form>
                                        @endif

                                        {{-- Pimpinan: Approve + Revisi --}}
                                        @if(auth()->user()->role === 'pimpinan' && $client->approval_status !== 'disetujui')
                                        <form action="{{ route('clients.approve', $client) }}" method="POST">
                                            @csrf
                                            <button class="bg-green-50 hover:bg-green-100 text-green-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                                Approve
                                            </button>
                                        </form>
                                        <button type="button"
                                            onclick="document.getElementById('revision-{{ $client->id }}').classList.toggle('hidden')"
                                            class="bg-yellow-50 hover:bg-yellow-100 text-yellow-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                            Revisi
                                        </button>
                                        @endif

                                    </div>

                                    {{-- Form Revisi Pimpinan --}}
                                    @if(auth()->user()->role === 'pimpinan' && $client->approval_status !== 'disetujui')
                                    <div id="revision-{{ $client->id }}" class="hidden mt-2">
                                        <form action="{{ route('clients.revision', $client) }}" method="POST">
                                            @csrf
                                            <textarea name="revision_note" rows="2" required
                                                placeholder="Catatan revisi..."
                                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-yellow-200 focus:outline-none resize-none"></textarea>
                                            <button type="submit"
                                                class="mt-1 w-full bg-yellow-500 hover:bg-yellow-600 text-white text-xs py-1.5 rounded-lg font-semibold transition">
                                                Kirim Revisi
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="text-gray-400 text-sm">Belum ada data klien</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($clients->hasPages())
                <div class="px-4 py-4 border-t border-gray-100">
                    {{ $clients->withQueryString()->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>

    <script>
        function confirmDelete(id) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Yakin hapus data?',
                    text: 'Data klien akan dihapus permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            } else {
                if (confirm('Hapus data klien ini?')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        }
    </script>

</x-app-layout>