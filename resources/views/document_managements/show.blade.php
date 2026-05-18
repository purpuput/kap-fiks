<x-app-layout title="Dokumen Klien">

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4">

            {{-- HEADER --}}
            <div class="flex items-center justify-between mb-8">

                <div>
                    <h2 class="text-3xl font-bold text-gray-800">
                        {{ $client->institution_name }}
                    </h2>

                    <p class="text-gray-500 mt-2">
                        Arsip dokumen audit klien
                    </p>
                </div>

                <a href="{{ route('document_managements.index') }}"
                    class="bg-blue-200 hover:bg-blue-300 text-blue-700 px-4 py-2 rounded-xl text-sm font-semibold">

                    Kembali

                </a>

            </div>

            {{-- FILTER --}}
            <div class="bg-white rounded-2xl shadow p-4 mb-6">

                <form method="GET">

                    <div class="flex flex-wrap gap-3">

                        <select
                            name="document_type"
                            class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:outline-none">

                            <option value="">Semua Tipe</option>

                            @foreach($documentTypeLabels as $type => $label)

                            <option
                                value="{{ $type }}"
                                {{ $documentType === $type ? 'selected' : '' }}>

                                {{ $label }}

                            </option>

                            @endforeach

                        </select>

                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold">

                            Filter

                        </button>

                    </div>

                </form>

            </div>

            {{-- TABLE --}}
            <div class="bg-white rounded-2xl shadow overflow-hidden">

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">

                            <tr>
                                <th class="px-4 py-4 text-left">Dokumen</th>
                                <th class="px-4 py-4 text-left">Tipe</th>
                                <th class="px-4 py-4 text-left">No. Laporan</th>
                                <th class="px-4 py-4 text-left">Tanggal Arsip</th>
                                <th class="px-4 py-4 text-left">Diarsipkan Oleh</th>
                                <th class="px-4 py-4 text-center">Aksi</th>
                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @forelse($documents as $doc)

                            <tr class="hover:bg-gray-50 transition">

                                <td class="px-4 py-4 font-medium text-gray-800">
                                    {{ $doc->document_name }}
                                </td>

                                <td class="px-4 py-4">

                                    <span class="bg-indigo-50 text-indigo-700 text-xs px-3 py-1 rounded-full">

                                        {{ $documentTypeLabels[$doc->document_type] ?? $doc->document_type }}

                                    </span>

                                </td>

                                <td class="px-4 py-4 text-gray-500">
                                    {{ $doc->auditReport?->report_number }}
                                </td>

                                <td class="px-4 py-4 text-gray-500">
                                    {{ $doc->archived_at?->format('d M Y H:i') }}
                                </td>

                                <td class="px-4 py-4 text-gray-500">
                                    {{ $doc->archivedBy?->name }}
                                </td>

                                <td class="px-4 py-4 text-center">

                                    <div class="flex items-center justify-center gap-2">

                                        {{-- PREVIEW --}}
                                        <a href="{{ route('document_managements.preview', $doc) }}"
                                            target="_blank"
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-semibold">

                                            Lihat

                                        </a>

                                        {{-- DOWNLOAD --}}
                                        <a href="{{ route('document_managements.download', $doc) }}"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-xs font-semibold">

                                            Download

                                        </a>

                                    </div>

                                </td>
                            </tr>

                            @empty

                            <tr>

                                <td colspan="6" class="py-14 text-center text-gray-400">

                                    Belum ada dokumen

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                {{-- PAGINATION --}}
                <div class="p-4 border-t border-gray-100">
                    {{ $documents->links() }}
                </div>

            </div>

        </div>
    </div>

</x-app-layout>