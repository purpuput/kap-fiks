<x-app-layout title="Dokumen Klien">

    <div class="py-6 animate-fade-in-up">

        <div class="max-w-7xl mx-auto px-4">

            {{-- HEADER --}}
            <div class="flex items-center justify-between mb-8">

                <div>

                    <h2 class="text-xl font-bold text-slate-800">
                        {{ $client->institution_name }}
                    </h2>

                    <p class="text-xs text-slate-400 mt-1.5 font-semibold">
                        Arsip dokumen audit klien
                    </p>

                </div>

                <a href="{{ route('document_managements.index') }}"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl text-sm font-bold transition">
                    Kembali
                </a>

            </div>

            {{-- FILTER --}}
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-4 mb-6">

                <form method="GET">

                    <div class="flex flex-wrap gap-3">

                        <select
                            name="document_type"
                            onchange="this.form.submit()"
                            class="bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all">

                            <option value="">Semua Tipe</option>

                            @foreach($documentTypeLabels as $type => $label)

                            <option
                                value="{{ $type }}"
                                {{ $documentType === $type ? 'selected' : '' }}>

                                {{ $label }}

                            </option>

                            @endforeach

                        </select>

                    </div>

                </form>

            </div>

            {{-- TABLE --}}
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-slate-50/70 text-slate-400 uppercase text-[10px] tracking-wider font-bold">

                            <tr>
                                <th class="px-6 py-4 text-left">Dokumen</th>
                                <th class="px-6 py-4 text-left">Tipe</th>
                                <th class="px-6 py-4 text-left">No. Laporan</th>
                                <th class="px-6 py-4 text-left">Tanggal Arsip</th>
                                <th class="px-6 py-4 text-left">Diarsipkan Oleh</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>

                        </thead>

                        <tbody class="divide-y divide-slate-100">

                            @forelse($documents as $doc)

                            <tr class="hover:bg-slate-50/50 transition-colors">

                                <td class="px-6 py-4 font-bold text-slate-700 text-sm">
                                    {{ $doc->document_name }}
                                </td>

                                <td class="px-6 py-4">

                                    <span class="text-xs text-slate-500 font-medium">
                                        {{ $documentTypeLabels[$doc->document_type] ?? $doc->document_type }}
                                    </span>

                                </td>

                                <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                                    {{ $doc->auditReport?->report_number }}
                                </td>

                                <td class="px-6 py-4 text-xs text-slate-450 font-medium">
                                    {{ $doc->approved_at?->format('d M Y H:i') }}
                                </td>

                                <td class="px-6 py-4 text-xs text-slate-500 font-bold">
                                    {{ $doc->approvedBy?->name }}
                                </td>

                                <td class="px-6 py-4 text-center">

                                    <div class="flex items-center justify-center gap-2">

                                        {{-- PREVIEW --}}
                                        <button type="button"
                                            class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition shadow-sm shadow-purple-500/10 hover:shadow-md"
                                            data-url="{{ route('document_managements.preview', $doc->id) }}"
                                            data-name="{{ $doc->document_name }}"
                                            onclick="openModalPreview(this.dataset.url, this.dataset.name)">
                                            Lihat
                                        </button>

                                        {{-- DOWNLOAD --}}
                                        <a href="{{ route('document_managements.download', $doc) }}"
                                            class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl text-xs font-bold transition">
                                            Download
                                        </a>

                                    </div>

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td colspan="6" class="py-14 text-center text-slate-400 text-xs font-semibold">
                                    Belum ada dokumen
                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                {{-- PAGINATION --}}
                <div class="p-4 border-t border-slate-50">
                    {{ $documents->links() }}
                </div>

            </div>

        </div>

    </div>

    {{-- MODAL PREVIEW FILE --}}
    <div id="modalPreview" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 lg:p-10 transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl w-full h-full flex flex-col overflow-hidden relative border border-slate-100">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-50 bg-slate-50/50">
                <h3 class="text-sm font-bold text-slate-800" id="previewTitle">Preview Dokumen</h3>
                <button type="button" onclick="closeModalPreview()" class="text-slate-400 hover:text-slate-650 p-2 rounded-full hover:bg-slate-100 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="flex-1 bg-slate-50 overflow-hidden relative" id="previewContainer">
                <iframe id="previewIframe" class="w-full h-full border-none hidden" src=""></iframe>
                <div id="previewLoading" class="absolute inset-0 flex items-center justify-center">
                    <div class="w-10 h-10 border-4 border-blue-100 border-t-blue-600 rounded-full animate-spin"></div>
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