<x-app-layout title="Manajemen Dokumen">

    <div class="space-y-6">
        {{-- SEARCH ROW --}}
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 bg-white border border-gray-200/80 rounded-xl p-3 shadow-sm">
            <form id="docFilterForm" method="GET" class="flex-1">
                <!-- Search -->
                <div class="relative w-full md:w-72">
                    <input type="text" id="searchInput" name="search" value="{{ $search }}"
                        placeholder="Cari nama klien..."
                        class="w-full bg-slate-50/50 border border-gray-200 rounded-lg pl-8 pr-3 py-1.5 text-xs placeholder-gray-400 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </form>
        </div>

        {{-- GRID CLIENT FOLDERS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($clients as $client)
            <div class="bg-white border border-gray-200/80 rounded-xl p-4 shadow-sm hover:shadow transition-all duration-150 flex flex-col justify-between group">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-blue-50 text-blue-600 rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-all duration-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="overflow-hidden">
                            <h3 class="font-bold text-[13px] text-gray-800 truncate" title="{{ $client->institution_name }}">
                                {{ $client->institution_name }}
                            </h3>
                            <p class="text-[11px] text-gray-400 font-semibold mt-0.5">
                                {{ $client->document_managements_count }} Dokumen
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="border-t border-gray-100 pt-2.5 mt-3 flex justify-end">
                    <a href="{{ route('document_managements.show', $client) }}"
                        class="text-xs font-bold text-blue-600 hover:text-blue-700 inline-flex items-center gap-0.5 hover:underline transition">
                        Lihat Dokumen
                        <svg class="w-3 h-3 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full bg-white border border-gray-200/80 rounded-xl p-12 text-center shadow-sm">
                <p class="text-gray-400 text-xs font-semibold">
                    Belum ada data klien
                </p>
            </div>
            @endforelse
        </div>

        {{-- PAGINATION --}}
        @if($clients->hasPages())
        <div class="mt-4">
            {{ $clients->links() }}
        </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const form = document.getElementById('docFilterForm');
            let timeout = null;

            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    form.submit();
                }, 500);
            });

            if (searchInput.value.length > 0) {
                const len = searchInput.value.length;
                searchInput.focus();
                searchInput.setSelectionRange(len, len);
            }
        });
    </script>
</x-app-layout>