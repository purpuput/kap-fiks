<x-app-layout title="Edit Laporan Audit">

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
            <div class="flex-1 bg-gray-100 overflow-hidden relative" id="previewContainerBox">
                <iframe id="previewIframe" class="w-full h-full border-none hidden" src=""></iframe>
                <img id="previewImage" class="w-full h-full object-contain hidden" src="" alt="Preview">
                <div id="previewLoading" class="absolute inset-0 flex items-center justify-center">
                    <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4">

            {{-- HEADER --}}
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Edit Laporan Audit</h2>
                <p class="text-gray-500 text-sm mt-1">
                    {{ $auditReport->report_number }} — {{ $auditReport->title }}
                </p>
            </div>

            {{-- ALERT REVISI --}}
            @if($auditReport->status === 'revisi')
            <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <p class="font-semibold text-yellow-800">Laporan Perlu Direvisi</p>
                        <p class="text-yellow-700 text-sm mt-1">{{ $auditReport->revision_note }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- ERROR VALIDATION --}}
            @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-xl">
                <p class="font-semibold mb-1">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('audit_reports.update', $auditReport) }}" method="POST"
                enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- INFORMASI LAPORAN --}}
                <div class="bg-white rounded-2xl shadow p-6 space-y-5">
                    <h3 class="text-lg font-bold text-gray-700 border-b pb-2">Informasi Laporan</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- No Laporan (readonly) --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">No. Laporan</label>
                            <input type="text" value="{{ $auditReport->report_number }}" disabled
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 bg-gray-100 text-gray-500 text-sm">
                        </div>

                        {{-- Klien --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Klien <span class="text-red-500">*</span>
                            </label>
                            <select name="client_id" required
                                {{ auth()->user()->role !== 'admin' ? 'disabled' : '' }}
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-300 focus:outline-none text-sm {{ auth()->user()->role !== 'admin' ? 'bg-gray-100 cursor-not-allowed text-gray-500' : '' }}">
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}"
                                    {{ $auditReport->client_id == $client->id ? 'selected' : '' }}>
                                    {{ $client->institution_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tanggal Audit --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Tanggal Pembuatan Laporan <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="audit_date"
                                value="{{ old('audit_date', $auditReport->audit_date->toDateString()) }}" required
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-300 focus:outline-none text-sm">
                        </div>

                    </div>
                </div>

                {{-- DOKUMEN --}}
                <div class="bg-white rounded-2xl shadow p-6">
                    <h3 class="text-lg font-bold text-gray-700 border-b pb-2 mb-5">Dokumen Laporan</h3>
                    <p class="text-sm text-gray-500 mb-4">Upload file baru hanya untuk dokumen yang ingin diganti. Dokumen yang tidak diganti akan tetap tersimpan.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($documentTypes as $type => $label)
                        @php $existingFile = $filesByType[$type] ?? null; @endphp

                        <div class="border rounded-xl p-4 {{ $existingFile ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">

                            <div class="flex items-center justify-between mb-2">
                                <label class="text-sm font-semibold text-gray-700">
                                    {{ $loop->iteration }}. {{ $label }}
                                </label>

                                @if($existingFile)
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    Sudah ada
                                </span>
                                @else
                                <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full">
                                    Belum ada
                                </span>
                                @endif
                            </div>

                            @if($existingFile)
                            <div class="flex items-center gap-2 mb-2 p-2 bg-white rounded-lg border border-green-200">
                                <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-xs text-gray-600 truncate flex-1">{{ $existingFile->document_name }}</span>

                                @php
                                $ext = strtolower(pathinfo($existingFile->file, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png']);
                                $isPdf = $ext === 'pdf';
                                @endphp

                                @if($isImage || $isPdf)
                                <button type="button"
                                    onclick="openModalPreview('{{ route('audit_reports.preview', $existingFile->id) }}', '{{ $existingFile->document_name }}', {{ $isImage ? 'true' : 'false' }})"
                                    class="text-xs text-gray-600 hover:text-gray-900 hover:underline flex-shrink-0 mr-2">
                                    Preview
                                </button>
                                @endif

                                <a href="{{ route('audit_reports.download', $existingFile->id) }}"
                                    class="text-xs text-blue-600 hover:underline flex-shrink-0">Download</a>
                            </div>
                            @endif

                            @if(array_key_exists($type, $allowedDocumentTypes))
                            <input type="file" name="files[{{ $type }}]"
                                @if($type==='data_klien' )
                                accept=".zip,.rar,.7z,.pdf,.doc,.docx,.xls,.xlsx"
                                @else
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                @endif
                                class="w-full text-xs text-gray-600 file:mr-2 file:py-1 file:px-2 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">

                            @if(!$existingFile)
                            <p class="text-xs text-red-500 mt-1">Wajib diisi</p>
                            @endif

                            @error("files.$type")
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                            @else
                            <p class="text-xs text-gray-400 mt-2 italic flex items-center gap-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Akses upload ditutup (Bukan tanggung jawab {{ ucfirst(auth()->user()->role) }})
                            </p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- TOMBOL --}}
                <div class="flex gap-3">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">
                        Update Laporan
                    </button>
                    <a href="{{ route('audit_reports.show', $auditReport) }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2.5 rounded-xl font-semibold text-sm transition">
                        Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>

    <script>
        function openModalPreview(url, title, isImage) {
            const modal = document.getElementById('modalPreview');
            const iframe = document.getElementById('previewIframe');
            const img = document.getElementById('previewImage');
            const loading = document.getElementById('previewLoading');

            document.getElementById('previewTitle').innerText = title || 'Preview Dokumen';

            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            iframe.classList.add('hidden');
            img.classList.add('hidden');

            if (isImage) {
                img.src = url;
                img.onload = function() {
                    loading.classList.add('hidden');
                    img.classList.remove('hidden');
                };
            } else {
                iframe.src = url;
                iframe.onload = function() {
                    loading.classList.add('hidden');
                    iframe.classList.remove('hidden');
                };
            }
        }

        function closeModalPreview() {
            const modal = document.getElementById('modalPreview');
            const iframe = document.getElementById('previewIframe');
            const img = document.getElementById('previewImage');

            modal.classList.add('hidden');
            iframe.src = '';
            img.src = '';
        }
    </script>
</x-app-layout>