<x-app-layout title="Edit Laporan Audit">

    {{-- MODAL PREVIEW FILE --}}
    <div id="modalPreview" class="fixed inset-0 z-50 hidden bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4 lg:p-10 transition-opacity">
        <div class="bg-white border border-slate-100 rounded-3xl shadow-2xl w-full h-full flex flex-col overflow-hidden relative">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-md font-bold text-slate-800" id="previewTitle">Preview Dokumen</h3>
                <button type="button" onclick="closeModalPreview()" class="text-slate-400 hover:text-slate-700 p-2 rounded-full hover:bg-slate-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="flex-1 bg-slate-100 overflow-hidden relative" id="previewContainerBox">
                <iframe id="previewIframe" class="w-full h-full border-none hidden rounded-b-3xl" src=""></iframe>
                <img id="previewImage" class="w-full h-full object-contain hidden" src="" alt="Preview">
                <div id="previewLoading" class="absolute inset-0 flex items-center justify-center">
                    <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-6 animate-fade-in-up">
        <div class="max-w-5xl mx-auto px-4">

            {{-- HEADER --}}
            <div class="mb-6">
                <h2 class="text-xl font-bold text-slate-800">Edit Laporan Audit</h2>
                <p class="text-xs text-slate-400 mt-1.5 font-semibold">
                    {{ $auditReport->report_number }} — {{ $auditReport->title }}
                </p>
            </div>

            {{-- ERROR VALIDATION --}}
            @if ($errors->any())
            <div class="mb-6 bg-rose-50 border border-rose-200/50 text-rose-700 px-4 py-3 rounded-2xl">
                <p class="font-semibold mb-1 text-sm">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside text-xs space-y-1">
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
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6">
                    <h3 class="text-md font-bold text-slate-700 border-b border-slate-100 pb-3">Informasi Laporan</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- No Laporan (readonly) --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-650 mb-2">No. Laporan</label>
                            <input type="text" value="{{ $auditReport->report_number }}" disabled
                                class="w-full bg-slate-100/70 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-400 cursor-not-allowed">
                        </div>

                        {{-- Klien --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-650 mb-2">
                                Klien <span class="text-rose-500">*</span>
                            </label>
                            <select name="client_id" required
                                {{ auth()->user()->role !== 'admin' ? 'disabled' : '' }}
                                class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all cursor-pointer {{ auth()->user()->role !== 'admin' ? 'bg-slate-100/70 cursor-not-allowed text-slate-450' : '' }}">
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}"
                                    {{ $auditReport->client_id == $client->id ? 'selected' : '' }}>
                                    {{ $client->institution_name }}
                                </option>
                                @endforeach
                            </select>
                            @error('client_id')
                            <p class="text-[11px] text-rose-600 mt-1.5 font-semibold">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Tanggal Audit --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-650 mb-2">
                                Tanggal Pembuatan Laporan <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="audit_date"
                                value="{{ old('audit_date', $auditReport->audit_date->toDateString()) }}" required
                                class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all cursor-pointer">
                            @error('audit_date')
                            <p class="text-[11px] text-rose-600 mt-1.5 font-semibold">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                    </div>
                </div>                {{-- DOKUMEN --}}
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                    <h3 class="text-md font-bold text-slate-700 border-b border-slate-100 pb-3 mb-4">Dokumen Laporan</h3>
                    <p class="text-xs text-slate-400 mb-6 font-semibold">Pengunggahan dokumen baru hanya diperbolehkan jika belum diunggah atau statusnya perlu revisi.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($documentTypes as $type => $label)
                        @php
                            $existingFile = $filesByType[$type] ?? null;
                            $canUpload = true;
                            $statusBadge = '';
                            $bgColor = 'border-slate-100 bg-white';
                            $revisionNote = null;

                            if ($existingFile) {
                                $review = $existingFile->review;
                                if (auth()->user()->role === 'auditor') {
                                    if ($review) {
                                        if ($review->status === 'approved') {
                                            $canUpload = false;
                                            $statusBadge = '<span class="text-[10px] bg-emerald-50 text-emerald-700 px-2.5 py-0.5 rounded-full font-bold border border-emerald-250/20">✓ Disetujui (Terkunci)</span>';
                                            $bgColor = 'border-emerald-100 bg-emerald-50/10';
                                        } elseif ($review->status === 'revision') {
                                            $canUpload = true;
                                            $statusBadge = '<span class="text-[10px] bg-rose-50 text-rose-700 px-2.5 py-0.5 rounded-full font-bold border border-rose-250/20">⚠️ Perlu Revisi</span>';
                                            $bgColor = 'border-rose-100 bg-rose-50/15';
                                            $revisionNote = $review->note;
                                        } else {
                                            $canUpload = false;
                                            $statusBadge = '<span class="text-[10px] bg-amber-50 text-amber-700 px-2.5 py-0.5 rounded-full font-bold border border-amber-250/20">🔒 Menunggu Review</span>';
                                            $bgColor = 'border-amber-100 bg-amber-50/10';
                                        }
                                    } else {
                                        $canUpload = false;
                                        $statusBadge = '<span class="text-[10px] bg-amber-50 text-amber-700 px-2.5 py-0.5 rounded-full font-bold border border-amber-250/20">🔒 Menunggu Review</span>';
                                        $bgColor = 'border-amber-100 bg-amber-50/10';
                                    }
                                } else {
                                    $statusBadge = '<span class="text-[10px] bg-emerald-50 text-emerald-700 px-2.5 py-0.5 rounded-full font-bold border border-emerald-250/20">Sudah ada</span>';
                                    $bgColor = 'border-emerald-100 bg-emerald-50/10';
                                }
                            } else {
                                $statusBadge = '<span class="text-[10px] bg-slate-50 text-slate-500 px-2.5 py-0.5 rounded-full font-bold border border-slate-200">Belum ada</span>';
                                $bgColor = 'border-slate-100 bg-white';
                            }
                        @endphp

                        <div class="border rounded-2xl p-4 transition-all hover:shadow-sm duration-150 {{ $bgColor }}">

                            <div class="flex items-center justify-between mb-3">
                                <label class="text-sm font-semibold text-slate-700">
                                    {{ $loop->iteration }}. {{ $label }}
                                </label>
                                {!! $statusBadge !!}
                            </div>

                            @if($existingFile)
                            <div class="flex items-center gap-2 mb-3 p-2 bg-slate-50 rounded-xl border border-slate-100">
                                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="text-xs text-slate-600 truncate flex-1 font-medium">{{ $existingFile->document_name }}</span>

                                @php
                                $ext = strtolower(pathinfo($existingFile->file, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg', 'jpeg', 'png']);
                                $isPdf = $ext === 'pdf';
                                @endphp

                                @if($isImage || $isPdf)
                                <button type="button"
                                    onclick="openModalPreview('{{ route('audit_reports.preview', $existingFile->id) }}', '{{ $existingFile->document_name }}', {{ $isImage ? 'true' : 'false' }})"
                                    class="text-xs text-slate-500 hover:text-slate-800 hover:underline flex-shrink-0 mr-2 font-bold">
                                    Preview
                                </button>
                                @endif

                                <a href="{{ route('audit_reports.download', $existingFile->id) }}"
                                    class="text-xs text-blue-600 hover:text-blue-800 hover:underline flex-shrink-0 font-bold">Download</a>
                            </div>
                            @endif

                            @if($revisionNote)
                            <div class="mb-3 bg-rose-50 border-l-4 border-rose-500 p-3 rounded-r-xl text-xs text-rose-800 font-semibold leading-relaxed">
                                <span class="font-bold text-rose-900 block mb-0.5">Catatan Revisi:</span> "{{ $revisionNote }}"
                            </div>
                            @endif

                            @if(array_key_exists($type, $allowedDocumentTypes) && $canUpload)
                            <input type="file" name="files[{{ $type }}]"
                                @if($type==='data_klien' )
                                accept=".zip,.rar,.7z,.pdf,.doc,.docx,.xls,.xlsx"
                                @else
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                @endif
                                class="w-full text-xs text-slate-650 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">

                            @if(!$existingFile)
                            <p class="text-[10px] text-rose-600 mt-2 font-bold">Wajib diisi</p>
                            @endif

                            @error("files.$type")
                            <p class="text-[10px] text-rose-600 mt-2 font-bold">{{ $message }}</p>
                            @enderror
                            @elseif(array_key_exists($type, $allowedDocumentTypes))
                            <p class="text-xs text-slate-400 italic flex items-center gap-1.5 mt-2 bg-slate-50 p-2.5 rounded-xl border border-slate-100 font-medium">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>Pengunggahan dikunci.</span>
                            </p>
                            @else
                            <p class="text-xs text-slate-400 mt-2 italic flex items-center gap-1.5 bg-slate-50 p-2.5 rounded-xl border border-slate-100 font-medium">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>Akses upload ditutup (Bukan tanggung jawab {{ ucfirst(auth()->user()->role) }})</span>
                            </p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- TOMBOL --}}
                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition shadow-sm shadow-blue-500/10 hover:shadow-md">
                        Update Laporan
                    </button>
                    <a href="{{ route('audit_reports.show', $auditReport) }}"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-bold text-sm transition">
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