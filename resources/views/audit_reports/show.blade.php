<x-app-layout title="Detail Laporan Audit">

    {{-- MODAL REVISI --}}
    <div id="modalRevisi" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4 transition-opacity">
        <div class="bg-white border border-gray-200/80 rounded-xl shadow-sm-xl w-full max-w-md transform transition-transform scale-95 opacity-0" id="modalRevisiContent">
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

    {{-- MODAL PREVIEW FILE --}}
    <div id="modalPreview" class="fixed inset-0 z-50 hidden bg-black/70 flex items-center justify-center p-4 lg:p-10 transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-full h-full flex flex-col overflow-hidden relative">
            <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800" id="previewTitle">Preview Dokumen</h3>
                <button type="button" onclick="closeModalPreview()" class="text-gray-400 hover:text-gray-700 p-2 rounded-full hover:bg-gray-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
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

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4">

            {{-- HEADER --}}
            <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">{{ $auditReport->title }}</h2>
                    <p class="text-xs text-gray-400 mt-1">{{ $auditReport->report_number }}</p>
                </div>

                {{-- STATUS BADGE --}}
                @if($auditReport->status === 'review')
                <span class="bg-yellow-100 text-yellow-700 px-4 py-1.5 rounded-full text-sm font-semibold">● Review</span>
                @elseif($auditReport->status === 'disetujui')
                <span class="bg-green-100 text-green-700 px-4 py-1.5 rounded-full text-sm font-semibold">✓ Disetujui</span>
                @elseif($auditReport->status === 'revisi')
                <span class="bg-red-100 text-red-700 px-4 py-1.5 rounded-full text-sm font-semibold">↩ Revisi</span>
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
                <div class="bg-white border border-gray-200/80 rounded-xl p-5 shadow-sm">
                    <h3 class="text-base font-bold text-gray-700 mb-4 border-b pb-2">Informasi Laporan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Klien</p>
                            <p class="font-bold text-gray-800 text-xs text-sm">{{ $auditReport->client->institution_name }}</p>
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
                            <p class="font-bold text-gray-800 text-xs text-sm">{{ $auditReport->audit_date->format('d M Y') }}</p>
                        </div>
                        <div class="md:col-span-3">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Auditor</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($auditReport->client->auditors as $auditor)
                                <span class="bg-gray-100 text-gray-700 text-xs px-3 py-1 rounded-full">{{ $auditor->name }}</span>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

                {{-- TABEL DOKUMEN --}}
                <div class="bg-white border border-gray-200/80 rounded-xl p-5 shadow-sm">
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
                                        @if($file->status === 'lengkap')
                                        <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">Lengkap</span>
                                        @elseif($file->status === 'belum lengkap')
                                        <span class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full">Belum Lengkap</span>
                                        <p class="text-[10px] text-red-500 mt-1 max-w-xs">{{ $file->revision_note }}</p>
                                        @else
                                        <span class="bg-yellow-100 text-yellow-700 text-xs px-2 py-0.5 rounded-full">Menunggu Review</span>
                                        @endif
                                        @else
                                        <span class="bg-gray-100 text-gray-500 text-xs px-2 py-0.5 rounded-full">Belum Diupload</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($file)
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <div class="flex gap-2">
                                                <button type="button"
                                                    onclick="openModalPreview(`{{ route('audit_reports.preview', $file->id) }}`, `{{ $file->document_name }}`); markFileAsViewed(`{{ $file->id }}`)"
                                                    class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100 text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                                    Lihat
                                                </button>
                                                <a href="{{ route('audit_reports.download', $file->id) }}"
                                                    onclick="markFileAsViewed('{{ $file->id }}')"
                                                    class="bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                                    Download
                                                </a>
                                            </div>
                                            @if(auth()->user()->role === 'pimpinan' && $auditReport->status !== 'disetujui')
                                            <button type="button" id="btnApproveFile_{{ $file->id }}" disabled
                                                onclick="openModalApproveFile(`{{ route('audit_reports.files.approve', $file->id) }}`)"
                                                class="w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white text-[10px] px-2 py-1 rounded-md font-semibold transition">
                                                🔒 Validasi (Lihat file dulu)
                                            </button>
                                            @endif
                                        </div>
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
                @if(auth()->user()->role === 'pimpinan' && $auditReport->status !== 'disetujui')
                <div class="bg-white border border-gray-200/80 rounded-xl p-5 shadow-sm">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Approve Laporan
                            </button>
                        </form>

                        {{-- REVISI --}}
                        <button type="button"
                            onclick="openModalRevisi(`{{ route('audit_reports.revisi', $auditReport->id) }}`)"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Kembalikan untuk Revisi
                        </button>
                    </div>
                </div>
                @endif

                {{-- SUDAH APPROVE --}}
                @if($auditReport->status === 'disetujui')
                <div class="bg-green-50 border border-green-200 rounded-2xl p-4 flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                    @if(in_array(auth()->user()->role, ['admin', 'auditor']) && $auditReport->status !== 'disetujui')
                    <a href="{{ route('audit_reports.edit', $auditReport) }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition">
                        Edit Laporan
                    </a>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL APPROVE FILE --}}
    <div id="modalApproveFile" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4 transition-opacity">
        <div class="bg-white border border-gray-200/80 rounded-xl shadow-sm-xl w-full max-w-md transform transition-transform scale-95 opacity-0" id="modalApproveFileContent">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-1">Validasi Dokumen</h3>
                <p class="text-sm text-gray-500 mb-4">Pilih status dokumen ini.</p>
                <form id="formApproveFile" method="POST" action="">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">Status Dokumen</label>
                        <select name="status" id="fileStatusSelect" onchange="toggleFileRevisionNote()" class="w-full px-4 py-3 border border-gray-300 rounded-xl" required>
                            <option value="">-- Pilih Status --</option>
                            <option value="lengkap">Lengkap / Sesuai</option>
                            <option value="belum lengkap">Belum Lengkap / Perlu Revisi</option>
                        </select>
                    </div>

                    <div id="fileRevisionNoteContainer" class="hidden mb-4">
                        <label class="block text-sm font-semibold mb-2">Catatan Revisi</label>
                        <textarea name="revision_note" id="fileRevisionNoteInput" rows="3"
                            placeholder="Jelaskan apa yang kurang..."
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-300 focus:outline-none resize-none"></textarea>
                    </div>

                    <div class="flex gap-3 justify-end">
                        <button type="button" onclick="closeModalApproveFile()"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm font-semibold">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold">
                            Simpan Status
                        </button>
                    </div>
                </form>
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

        function openModalApproveFile(url) {
            document.getElementById('formApproveFile').action = url;
            document.getElementById('modalApproveFile').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('modalApproveFileContent').classList.remove('scale-95', 'opacity-0');
            }, 10);
        }

        function closeModalApproveFile() {
            document.getElementById('modalApproveFileContent').classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                document.getElementById('modalApproveFile').classList.add('hidden');
            }, 200);
        }

        document.getElementById('modalApproveFile').addEventListener('click', function(e) {
            if (e.target === this) closeModalApproveFile();
        });

        function toggleFileRevisionNote() {
            const select = document.getElementById('fileStatusSelect').value;
            const container = document.getElementById('fileRevisionNoteContainer');
            const input = document.getElementById('fileRevisionNoteInput');

            if (select === 'belum lengkap') {
                container.classList.remove('hidden');
                input.required = true;
            } else {
                container.classList.add('hidden');
                input.required = false;
            }
        }

        function markFileAsViewed(id) {
            const btn = document.getElementById('btnApproveFile_' + id);
            if (btn) {
                btn.disabled = false;
                btn.classList.remove('disabled:bg-gray-300', 'disabled:cursor-not-allowed');
                btn.innerText = 'Validasi Dokumen';
            }
        }

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