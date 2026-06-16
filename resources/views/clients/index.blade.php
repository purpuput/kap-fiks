<x-app-layout title="Data Klien">

    <div class="space-y-6 animate-fade-in-up">
        {{-- SEARCH & ACTION ROW --}}
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 bg-white border border-slate-100 rounded-3xl p-4 shadow-sm">
            <form id="clientFilterForm" method="GET" action="{{ route('clients.index') }}" class="flex-1">
                <!-- Search -->
                <div class="relative w-full">
                    <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama instansi..."
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl pl-9 pr-4 py-2.5 text-sm placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </form>

            <!-- Action Button -->
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('clients.create') }}"
                class="bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm shadow-blue-500/10 hover:shadow-md transition flex items-center justify-center gap-1.5 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Klien
            </a>
            @endif
        </div>

        {{-- TABLE SECTION --}}
        <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto relative">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/80 text-slate-650 font-bold uppercase tracking-wider sticky top-0 z-10 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4 text-left w-12">No</th>
                            <th class="px-6 py-4 text-left">Instansi</th>
                            <th class="px-6 py-4 text-left">Jenis Audit</th>
                            <th class="px-6 py-4 text-left">Tim Audit</th>
                            <th class="px-6 py-4 text-left">Kontrak</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse ($clients as $client)
                        <tr class="hover:bg-slate-50/50 transition group">
                            {{-- No --}}
                            <td class="px-6 py-4 font-mono text-sm text-slate-400 font-semibold whitespace-nowrap">
                                {{ $loop->iteration + ($clients->firstItem() - 1) }}
                            </td>

                            {{-- Instansi --}}
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-700 text-md truncate max-w-xs" title="{{ $client->institution_name }}">
                                    {{ $client->institution_name }}
                                </div>
                                <div class="text-xs text-slate-400 mt-1 font-semibold">
                                    Pimpinan: {{ $client->leader_name }}
                                </div>
                            </td>

                            {{-- Jenis Audit --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($client->audit_type === 'keuangan')
                                <span class="bg-blue-50 text-blue-700 text-xs px-2.5 py-0.5 rounded-full font-bold border border-blue-250/20">
                                    Keuangan
                                </span>
                                @else
                                <span class="bg-purple-50 text-purple-700 text-xs px-2.5 py-0.5 rounded-full font-bold border border-purple-250/20">
                                    Kepatuhan
                                </span>
                                @endif
                            </td>

                            {{-- Tim Audit --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1.5 max-w-[220px]">
                                    @foreach ($client->auditors as $auditor)
                                    <span class="inline-flex items-center gap-1 {{ $auditor->pivot->team_role === 'ketua' ? 'bg-blue-50 text-blue-700 border border-blue-250/20' : 'bg-slate-50 text-slate-600 border border-slate-100' }} text-xs px-2 py-0.5 rounded-full font-bold truncate" title="{{ $auditor->name }}">
                                        {{ $auditor->name }}
                                        @if($auditor->pivot->team_role === 'ketua')
                                        <span class="text-[10px] opacity-75 font-normal">(Ketua)</span>
                                        @endif
                                    </span>
                                    @endforeach
                                </div>
                            </td>

                            {{-- Kontrak --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-slate-650 space-y-1 font-semibold">
                                    <div><span class="text-slate-400 font-bold uppercase tracking-wider text-[9px] mr-1">Mulai:</span> {{ optional($client->contract_start)->format('d/m/Y') ?? '-' }}</div>
                                    <div><span class="text-slate-400 font-bold uppercase tracking-wider text-[9px] mr-1">Selesai:</span> {{ optional($client->contract_end)->format('d/m/Y') ?? '-' }}</div>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    {{-- Approval --}}
                                    @if($client->approval_status === 'disetujui')
                                    <span class="bg-emerald-50 text-emerald-700 text-xs px-2.5 py-0.5 rounded-full border border-emerald-250/20 font-bold">
                                        Disetujui
                                    </span>
                                    @elseif($client->approval_status === 'revisi')
                                    <span class="bg-rose-50 text-rose-700 text-xs px-2.5 py-0.5 rounded-full border border-rose-250/20 font-bold">
                                        Revisi
                                    </span>
                                    @else
                                    <span class="bg-amber-50 text-amber-700 text-xs px-2.5 py-0.5 rounded-full border border-amber-250/20 font-bold">
                                        Pending
                                    </span>
                                    @endif

                                    {{-- Aktif/Nonaktif --}}
                                    @if($client->status === 'aktif')
                                    <span class="bg-blue-50 text-blue-700 text-xs px-2.5 py-0.5 rounded-full border border-blue-250/20 font-bold">
                                        Aktif
                                    </span>
                                    @else
                                    <span class="bg-slate-50 text-slate-600 text-xs px-2.5 py-0.5 rounded-full border border-slate-100 font-bold">
                                        Nonaktif
                                    </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-3">
                                    {{-- Detail --}}
                                    <a href="{{ route('clients.show', $client->id) }}"
                                        class="text-slate-500 hover:text-slate-800 font-bold hover:underline transition text-sm">
                                        Detail
                                    </a>

                                    {{-- Edit --}}
                                    @if(auth()->user()->role === 'admin' && $client->approval_status !== 'disetujui')
                                    <a href="{{ route('clients.edit', $client->id) }}"
                                        class="text-blue-600 hover:text-blue-700 font-bold hover:underline transition text-sm">
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
                                            class="text-rose-600 hover:text-rose-700 font-bold hover:underline transition text-sm">
                                            Hapus
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <h4 class="text-sm font-bold text-slate-800 mb-1">Data Klien Belum Tersedia</h4>
                                    <p class="text-xs text-slate-400 text-center font-semibold">Tambahkan data klien baru untuk memulai audit.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if($clients->hasPages())
            <div class="px-6 py-4 border-t border-slate-50 bg-slate-50/30 flex items-center justify-between">
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