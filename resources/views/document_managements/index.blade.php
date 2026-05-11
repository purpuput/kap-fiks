<x-app-layout title="Manajemen Dokumen">

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4">

            {{-- FLASH --}}
            @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded-xl text-sm">
                {{ session('success') }}
            </div>
            @endif

            {{-- HEADER --}}
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Manajemen Dokumen</h2>
                <p class="text-gray-500 text-sm mt-1">
                    Arsip dokumen dari laporan audit yang telah disetujui pimpinan
                </p>
            </div>

            {{-- FILTER --}}
            <div class="bg-white rounded-2xl shadow p-4 mb-6">
                <form method="GET" action="{{ route('document_managements.index') }}" class="flex flex-wrap gap-3">
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Cari nama dokumen..."
                        class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:outline-none flex-1 min-w-48">

                    <select name="client_id"
                        class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:outline-none">
                        <option value="">Semua Klien</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ $clientId == $client->id ? 'selected' : '' }}>
                            {{ $client->institution_name }}
                        </option>
                        @endforeach
                    </select>

                    <select name="document_type"
                        class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:outline-none">
                        <option value="">Semua Tipe</option>
                        @foreach($documentTypeLabels as $type => $label)
                        <option value="{{ $type }}" {{ $documentType === $type ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>

                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold">
                        Filter
                    </button>
                    @if($search || $clientId || $documentType)
                    <a href="{{ route('document_managements.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-xl text-sm font-semibold">
                        Reset
                    </a>
                    @endif
                </form>
            </div>

            {{-- INFO COUNT --}}
            <div class="mb-4 text-sm text-gray-500">
                Menampilkan <strong>{{ $documents->total() }}</strong> dokumen
            </div>

            {{-- TABLE --}}
            <div class="bg-white rounded-2xl shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3.5 text-left">Nama Dokumen</th>
                                <th class="px-4 py-3.5 text-left">Tipe Dokumen</th>
                                <th class="px-4 py-3.5 text-left">Klien</th>
                                <th class="px-4 py-3.5 text-left">No. Laporan</th>
                                <th class="px-4 py-3.5 text-left">Tanggal Arsip</th>
                                <th class="px-4 py-3.5 text-left">Diarsipkan oleh</th>
                                <th class="px-4 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($documents as $doc)
                            <tr class="hover:bg-gray-50 transition">

                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="font-medium text-gray-800 truncate max-w-xs">
                                            {{ $doc->document_name }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-4 py-4">
                                    <span class="bg-indigo-50 text-indigo-700 text-xs px-2.5 py-1 rounded-full font-medium">
                                        {{ $documentTypeLabels[$doc->document_type] ?? $doc->document_type }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 text-gray-600">
                                    {{ $doc->auditReport?->client?->institution_name ?? '-' }}
                                </td>

                                <td class="px-4 py-4 font-mono text-xs text-gray-500">
                                    @if($doc->auditReport)
                                    <a href="{{ route('audit_reports.show', $doc->auditReport) }}"
                                        class="text-blue-600 hover:underline">
                                        {{ $doc->auditReport->report_number }}
                                    </a>
                                    @else
                                    —
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-gray-500 whitespace-nowrap">
                                    {{ $doc->archived_at?->format('d M Y, H:i') ?? '-' }}
                                </td>

                                <td class="px-4 py-4 text-gray-600">
                                    {{ $doc->archivedBy?->name ?? '-' }}
                                </td>

                                <td class="px-4 py-4 text-center">
                                    <a href="{{ route('document_managements.download', $doc) }}"
                                        class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg font-semibold transition inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Download
                                    </a>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                    </svg>
                                    <p class="text-gray-400 text-sm">Belum ada dokumen yang diarsipkan</p>
                                    <p class="text-gray-300 text-xs mt-1">Dokumen akan muncul setelah laporan audit di-approve</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                @if($documents->hasPages())
                <div class="px-4 py-4 border-t border-gray-100">
                    {{ $documents->appends(request()->query())->links() }}
                </div>
                @endif
            </div>

        </div>
    </div>

</x-app-layout>
