<x-app-layout title="Manajemen Dokumen">

    <div class="py-6 animate-fade-in-up">

        <div class="max-w-6xl mx-auto px-4 space-y-6">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <div>

                    <h1 class="text-xl font-bold text-slate-800">
                        Manajemen Dokumen
                    </h1>

                    <p class="text-xs text-slate-400 mt-1 font-semibold">
                        Kelola dan tinjau berkas/dokumen kerja milik klien audit
                    </p>

                </div>

            </div>

            {{-- SEARCH ROW --}}
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 bg-white border border-slate-100 rounded-3xl p-3 shadow-sm">

                <form id="docFilterForm" method="GET" class="flex-1">

                    <!-- Search -->
                    <div class="relative w-full">

                        <input type="text" id="searchInput" name="search" value="{{ $search }}"
                            placeholder="Cari nama klien..."
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-slate-700">

                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">

                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />

                            </svg>

                        </div>

                    </div>

                </form>

            </div>

            {{-- GRID CLIENT FOLDERS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                @forelse($clients as $client)

                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between group">

                    <div>

                        <div class="flex items-center gap-4">

                            <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl group-hover:bg-gradient-to-r group-hover:from-blue-600 group-hover:to-indigo-650 group-hover:text-white group-hover:shadow-lg group-hover:shadow-blue-500/20 transition-all duration-300">

                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>

                                </svg>

                            </div>

                            <div class="overflow-hidden">

                                <h3 class="font-bold text-sm text-slate-800 truncate" title="{{ $client->institution_name }}">
                                    {{ $client->institution_name }}
                                </h3>

                                <p class="text-xs text-slate-400 font-semibold mt-1">
                                    {{ $client->document_managements_count }} Dokumen
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="border-t border-slate-50 pt-4 mt-5 flex justify-end">

                        <a href="{{ route('document_managements.show', $client) }}"
                            class="text-xs font-bold text-blue-600 hover:text-indigo-650 inline-flex items-center gap-1 transition-all duration-200">
                            Lihat Dokumen
                            <svg class="w-3.5 h-3.5 transform group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>

                    </div>

                </div>

                @empty

                <div class="col-span-full bg-white border border-slate-100 rounded-3xl p-12 text-center shadow-sm">

                    <p class="text-slate-400 text-xs font-semibold">
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