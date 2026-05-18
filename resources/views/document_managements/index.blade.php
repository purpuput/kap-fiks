<x-app-layout title="Manajemen Dokumen">

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4">

            {{-- HEADER --}}
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-800">
                    Manajemen Dokumen
                </h2>

                <p class="text-gray-500 mt-2">
                    Arsip dokumen audit berdasarkan klien
                </p>
            </div>

            {{-- SEARCH --}}
            <div class="bg-white rounded-2xl shadow p-4 mb-8">
                <form method="GET">

                    <div class="flex gap-3">
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari nama klien..."
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-200 focus:outline-none">

                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 rounded-xl text-sm font-semibold">
                            Cari
                        </button>
                    </div>

                </form>
            </div>

            {{-- CARD CLIENT --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                @forelse($clients as $client)

                <div class="bg-white rounded-2xl shadow hover:shadow-lg transition p-6">

                    <div class="flex items-start justify-between mb-5">

                        <div>
                            <h3 class="font-bold text-lg text-gray-800">
                                {{ $client->institution_name }}
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                {{ $client->document_managements_count }} Dokumen
                            </p>
                        </div>

                        <div class="bg-blue-100 text-blue-600 p-3 rounded-2xl">
                            <svg class="w-6 h-6"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 7a2 2 0 012-2h4l2 2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V7z" />
                            </svg>
                        </div>

                    </div>

                    <a href="{{ route('document_managements.show', $client) }}"
                        class="w-full inline-flex justify-center items-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-xl text-sm font-semibold transition">

                        Lihat Dokumen

                    </a>

                </div>

                @empty

                <div class="col-span-full">

                    <div class="bg-white rounded-2xl shadow p-12 text-center">

                        <p class="text-gray-400">
                            Belum ada data klien
                        </p>

                    </div>

                </div>

                @endforelse

            </div>

            {{-- PAGINATION --}}
            <div class="mt-8">
                {{ $clients->links() }}
            </div>

        </div>
    </div>

</x-app-layout>