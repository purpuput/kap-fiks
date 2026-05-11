<x-app-layout title="Detail Laporan Audit">

    {{-- MODAL REVISI --}}
    <div id="modalRevisi" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-1">Kembalikan untuk Revisi</h3>
                <p class="text-sm text-gray-500 mb-4">Berikan catatan yang jelas agar auditor dapat memperbaiki laporan.</p>
                <form id="formRevisi" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <textarea name="revision_note" rows="4" required
                        placeholder="Tulis catatan revisi secara detail..."
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-300 focus:outline-none resize-none mb-4"></textarea>
                    <div class="flex gap-3 justify-end">
                        <button type="button" onclick="closeModalRevisi()"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-semibold">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm font-semibold">
                            Kirim Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4">

            {{-- FLASH MESSAGE --}}
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
            <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $auditReport->title }}</h2>
                    <p class="text-gray-500 text-sm mt-1">{{ $auditReport->report_number }}</p>
                </div>

                {{-- STATUS BADGE --}}
                @if($auditReport->status === 'pending')
                <span class="bg-yellow-100 text-yellow-700 px-4 py-1.5 rounded-full text-sm font-semibold">● Pending Review</span>
                @elseif($auditReport->status === 'approve')
                <span class="bg-green-100 text-green-700 px-4 py-1.5 rounded-full text-sm font-semibold">✓ Disetujui</span>
                @else
                <span class="bg-red-100 text-red-700 px-4 py-1.5 rounded-full text-sm font-semibold">↩ Perlu Revisi</span>
                @endif
            </div>

            {{-- CATATAN REVISI --}}
            @if($auditReport->status === 'revisi')
            <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 rounded-xl p-4">
                <p class="font-semibold text-yellow-800 mb-1">Catatan Revisi dari Pimpinan:</p>
                <p class="text-yellow-700 text-sm">{{ $auditReport->revision_note }}</p>
                @if(auth()->user()->role === 'auditor')
                <a href="{{ route('audit_reports.edit', $auditReport) }}"
                    class="inline-block mt-3 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-semibold px-4 py-1.5 rounded-lg transition">
                    Perbaiki Laporan
                </a>
                @endif
            </div>
            @endif

            <div class="space-y-6">

                {{-- INFORMASI --}}
                <div class="bg-white rounded-2xl shadow p-6">
                    <h3 class="text-base font-bold text-gray-700 mb-4 border-b pb-2">Informasi Laporan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Klien</p>
                            <p class="font-semibold text-gray-800 text-sm">{{ $auditReport->client->institution_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Jenis Audit</p>
                            @if($auditReport->audit_type === 'keuangan')
                            <span class="bg-blue-100 text-blue-700 text-xs px-2.5 py-1 rounded-full font-semibold">Audit Keuangan</span>
                            @else
                            <span class="bg-purple-100 text-purple-700 text-xs px-2.5 py-1 rounded-full font-semibold">Audit Kepatuhan</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Tanggal Audit</p>
                            <p class="font-semibold text-gray-800 text-sm">{{ $auditReport->audit_date->format('d M Y') }}</p>
                        </div>
                        <div class="md:col-span-3">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Auditor</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($auditReport->auditors as $auditor)
                                <span class="bg-gray-100 text-gray-700 text-xs px-3 py-1 rounded-full">{{ $auditor->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="md:col-span-3">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Temuan Audit</p>
                            <p class="text-gray-700 text-sm leading-relaxed">{{ $auditReport->findings }}</p>
                        </div>
                        @if($auditReport->recommendations)
                        <div class="md:col-span-3">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Rekomendasi</p>
                            <p class="text-gray-700 text-sm leading-relaxed">{{ $auditReport->recommendations }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- TABEL DOKUMEN --}}
                <div class="bg-white rounded-2xl shadow p-6">
                    <h3 class="text-base font-bold text-gray-700 mb-4 border-b pb-2">
                        Dokumen Laporan
                        <span class="text-sm font-normal text-gray-400 ml-2">
                            {{ $auditReport->files->count() }}/{{ count($documentTypes) }} dokumen
                        </span>
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                                    <th class="px-4 py-3 text-left rounded-l-lg">No</th>
                                    <th class="px-4 py-3 text-left">Nama Dokumen</th>
                                    <th class="px-4 py-3 text-left">Tipe</th>
                                    <th class="px-4 py-3 text-left">Diupload oleh</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-center rounded-r-lg">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($documentTypes as $type => $label)
                                @php $file = $filesByType[$type] ?? null; @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-gray-400">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-700">{{ $label }}</p>
                                        @if($file)
                                        <p class="text-xs text-gray-400 truncate max-w-xs">{{ $file->document_name }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 capitalize">
                                        {{ str_replace('_', ' ', $type) }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">
                                        {{ $file?->uploader?->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($file)
                                        <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">Ada</span>
                                        @else
                                        <span class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full">Belum ada</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($file)
                                        <a href="{{ route('audit_reports.download', $file->id) }}"
                                            class="bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs px-3 py-1 rounded-lg font-semibold transition">
                                            Download
                                        </a>
                                        @else
                                        <span class="text-gray-300 text-xs">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- AKSI PIMPINAN --}}
                @if(auth()->user()->role === 'pimpinan' && $auditReport->status !== 'approve')
                <div class="bg-white rounded-2xl shadow p-6">
                    <h3 class="text-base font-bold text-gray-700 mb-4 border-b pb-2">Tindakan</h3>

                    @php $allUploaded = $auditReport->files->count() === count($documentTypes); @endphp

                    @if(!$allUploaded)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 mb-4 text-sm text-yellow-700">
                        ⚠ Masih ada dokumen yang belum diupload ({{ $auditReport->files->count() }}/{{ count($documentTypes) }}). Laporan belum dapat di-approve.
                    </div>
                    @endif

                    <div class="flex gap-3">
                        {{-- APPROVE --}}
                        <form action="{{ route('audit_reports.approve', $auditReport->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit"
                                {{ !$allUploaded ? 'disabled' : '' }}
                                onclick="return confirm('Approve laporan ini? Semua dokumen akan otomatis tersimpan ke Manajemen Dokumen.')"
                                class="bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Approve Laporan
                            </button>
                        </form>

                        {{-- REVISI --}}
                        <button type="button"
                            onclick="openModalRevisi('{{ route('audit_reports.revisi', $auditReport->id) }}')"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Kembalikan untuk Revisi
                        </button>
                    </div>
                </div>
                @endif

                {{-- SUDAH APPROVE --}}
                @if($auditReport->status === 'approve')
                <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-green-700 text-sm font-medium">
                        Laporan ini telah disetujui. Semua dokumen sudah tersimpan di
                        <a href="{{ route('document_managements.index') }}" class="underline hover:text-green-900">Manajemen Dokumen</a>.
                    </p>
                </div>
                @endif

                {{-- TOMBOL BAWAH --}}
                <div class="flex gap-3">
                    <a href="{{ route('audit_reports.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition">
                        ← Kembali ke Daftar
                    </a>
                    @if(auth()->user()->role === 'auditor' && $auditReport->status !== 'approve')
                    <a href="{{ route('audit_reports.edit', $auditReport) }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition">
                        Edit Laporan
                    </a>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <script>
        function openModalRevisi(url) {
            document.getElementById('formRevisi').action = url;
            document.getElementById('modalRevisi').classList.remove('hidden');
        }
        function closeModalRevisi() {
            document.getElementById('modalRevisi').classList.add('hidden');
        }
        // Tutup modal saat klik backdrop
        document.getElementById('modalRevisi').addEventListener('click', function(e) {
            if (e.target === this) closeModalRevisi();
        });
    </script>

</x-app-layout>