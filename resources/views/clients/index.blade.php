<x-app-layout title="Data Klien">

    <div class="space-y-6">
        {{-- SEARCH & ACTION ROW --}}
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 bg-white border border-gray-200/80 rounded-xl p-3 shadow-sm">
            <form id="clientFilterForm" method="GET" action="{{ route('clients.index') }}" class="flex-1">
                <!-- Search -->
                <div class="relative w-full">
                    <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama instansi..."
                        class="w-full bg-slate-50/50 border border-gray-200 rounded-lg pl-8 pr-3 py-1.5 text-md placeholder-gray-400 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </form>

            <!-- Action Button -->
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('clients.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-1.5 rounded-lg text-md font-bold shadow-sm transition flex items-center justify-center gap-1 whitespace-nowrap">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Klien
            </a>
            @endif
        </div>

        {{-- TABLE SECTION --}}
        <div class="bg-white border border-gray-200/80 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto relative">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/60 text-gray-400 font-bold uppercase tracking-wider sticky top-0 z-10 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left w-12">No</th>
                            <th class="px-4 py-3 text-left">Instansi</th>
                            <th class="px-4 py-3 text-left">Jenis Audit</th>
                            <th class="px-4 py-3 text-left">Tim Audit</th>
                            <th class="px-4 py-3 text-left">Kontrak</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($clients as $client)
                        <tr class="hover:bg-slate-50/30 transition group">
                            {{-- No --}}
                            <td class="px-4 py-3 font-mono text-sm text-gray-500 whitespace-nowrap">
                                {{ $loop->iteration + ($clients->firstItem() - 1) }}
                            </td>

                            {{-- Instansi --}}
                            <td class="px-4 py-3">
                                <div class="font-bold text-gray-800 text-md truncate max-w-xs" title="{{ $client->institution_name }}">
                                    {{ $client->institution_name }}
                                </div>
                                <div class="text-sm text-gray-400 mt-0.5">
                                    Pimpinan: {{ $client->leader_name }}
                                </div>
                            </td>

                            {{-- Jenis Audit --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($client->audit_type === 'keuangan')
                                <span class="bg-blue-50 text-blue-700 text-sm px-2 py-0.5 rounded font-bold border border-blue-100/50">
                                    Keuangan
                                </span>
                                @else
                                <span class="bg-purple-50 text-purple-700 text-sm px-2 py-0.5 rounded font-bold border border-purple-100/50">
                                    Kepatuhan
                                </span>
                                @endif
                            </td>

                            {{-- Tim Audit --}}
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1.5 max-w-[220px]">
                                    @foreach ($client->auditors as $auditor)
                                    <span class="inline-flex items-center gap-1 {{ $auditor->pivot->team_role === 'ketua' ? 'bg-blue-50 text-blue-700 border border-blue-100' : 'bg-slate-50 text-slate-600 border border-slate-100' }} text-sm px-1.5 py-0.5 rounded font-semibold truncate" title="{{ $auditor->name }}">
                                        {{ $auditor->name }}
                                        @if($auditor->pivot->team_role === 'ketua')
                                        <span class="text-sm opacity-75 font-normal">(Ketua)</span>
                                        @endif
                                    </span>
                                    @endforeach
                                </div>
                            </td>

                            {{-- Kontrak --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm text-gray-600 space-y-0.5">
                                    <div><span class="text-gray-400 font-medium">Mulai:</span> {{ optional($client->contract_start)->format('d/m/Y') ?? '-' }}</div>
                                    <div><span class="text-gray-400 font-medium">Selesai:</span> {{ optional($client->contract_end)->format('d/m/Y') ?? '-' }}</div>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    {{-- Approval --}}
                                    @if($client->approval_status === 'disetujui')
                                    <span class="bg-emerald-50 text-emerald-700 text-sm px-2 py-0.5 rounded border border-emerald-200/50 font-bold">
                                        Disetujui
                                    </span>
                                    @elseif($client->approval_status === 'revisi')
                                    <span class="bg-rose-50 text-rose-700 text-sm px-2 py-0.5 rounded border border-rose-200/50 font-bold">
                                        Revisi
                                    </span>
                                    @else
                                    <span class="bg-amber-50 text-amber-700 text-sm px-2 py-0.5 rounded border border-amber-200/50 font-bold">
                                        Pending
                                    </span>
                                    @endif

                                    {{-- Aktif/Nonaktif --}}
                                    @if($client->status === 'aktif')
                                    <span class="bg-blue-50 text-blue-700 text-sm px-2 py-0.5 rounded border border-blue-200/50 font-bold">
                                        Aktif
                                    </span>
                                    @else
                                    <span class="bg-gray-50 text-gray-600 text-sm px-2 py-0.5 rounded border border-gray-200/50 font-bold">
                                        Nonaktif
                                    </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-3">
                                    {{-- Detail --}}
                                    <a href="{{ route('clients.show', $client->id) }}"
                                        class="text-slate-500 hover:text-slate-800 font-bold hover:underline transition">
                                        Detail
                                    </a>

                                    {{-- Edit --}}
                                    @if(auth()->user()->role === 'admin' && $client->approval_status !== 'disetujui')
                                    <a href="{{ route('clients.edit', $client->id) }}"
                                        class="text-blue-600 hover:text-blue-700 font-bold hover:underline transition">
                                        Edit
                                    </a>
                                    @endif

                                    {{-- Hapus --}}
                                    @if(auth()->user()->role === 'admin')
                                    <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="inline"
                                        onsubmit="event.preventDefault(); confirmDelete(this, 'Data klien ini beserta semua dokumen laporan terkait akan dihapus secara permanen!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-rose-600 hover:text-rose-700 font-bold hover:underline transition">
                                            Hapus
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <h4 class="text-xs font-bold text-gray-800 mb-0.5">Data Klien Belum Tersedia</h4>
                                    <p class="text-[11px] text-gray-400 text-center">Tambahkan data klien baru untuk memulai audit.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if($clients->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 bg-slate-50/30 flex items-center justify-between">
                {{ $clients->links() }}
            </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const form = document.getElementById('clientFilterForm');
            let timeout = null;

            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    form.submit();
                }, 500);
            });

            // Keep cursor at the end of search input after page load/submit
            if (searchInput.value.length > 0) {
                const len = searchInput.value.length;
                searchInput.focus();
                searchInput.setSelectionRange(len, len);
            }
        });
    </script>

</x-app-layout>