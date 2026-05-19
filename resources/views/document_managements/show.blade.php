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
                                        <button type="button" 
                                            onclick="openModalPreview('{{ route('document_managements.preview', $doc->id) }}', '{{ $doc->document_name }}')"
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-xs font-semibold">

                                            Lihat

                                        </button>

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

    {{-- MODAL PREVIEW FILE --}}
    <div id="modalPreview" class="fixed inset-0 z-50 hidden bg-black/70 flex items-center justify-center p-4 lg:p-10 transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full h-full flex flex-col overflow-hidden relative">
            <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800" id="previewTitle">Preview Dokumen</h3>
                <button type="button" onclick="closeModalPreview()" class="text-gray-400 hover:text-gray-700 p-2 rounded-full hover:bg-gray-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="flex-1 bg-gray-100 overflow-hidden relative" id="previewContainer">
                <iframe id="previewIframe" class="w-full h-full border-none hidden" src=""></iframe>
                <div id="previewLoading" class="absolute inset-0 flex items-center justify-center">
                    <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JS PREVIEW MODAL
        function openModalPreview(url, title) {
            const modal = document.getElementById('modalPreview');
            const iframe = document.getElementById('previewIframe');
            const loading = document.getElementById('previewLoading');
            
            document.getElementById('previewTitle').innerText = title || 'Preview Dokumen';
            
            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            iframe.classList.add('hidden');
            
            iframe.src = url;
            
            iframe.onload = function() {
                loading.classList.add('hidden');
                iframe.classList.remove('hidden');
            };
        }

        function closeModalPreview() {
            const modal = document.getElementById('modalPreview');
            const iframe = document.getElementById('previewIframe');
            
            modal.classList.add('hidden');
            iframe.src = '';
        }
    </script>
</x-app-layout>