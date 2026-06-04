<x-app-layout title="Detail Laporan Audit">

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
                    <h2 class="text-md font-bold text-gray-800">{{ $auditReport->title }}</h2>
                    <p class="text-sm text-gray-400 mt-1">{{ $auditReport->report_number }}</p>
                </div>

                {{-- STATUS BADGE --}}
                @if($auditReport->status === 'review')
                <span id="report-status-badge" class="bg-yellow-100 text-yellow-700 px-4 py-1.5 rounded-full text-sm font-semibold">● Review</span>
                @elseif($auditReport->status === 'disetujui')
                <span id="report-status-badge" class="bg-green-100 text-green-700 px-4 py-1.5 rounded-full text-sm font-semibold">✓ Disetujui</span>
                @elseif($auditReport->status === 'revisi')
                <span id="report-status-badge" class="bg-red-100 text-red-700 px-4 py-1.5 rounded-full text-sm font-semibold">↩ Revisi</span>
                @endif
            </div>

            @php
                $revisionFiles = $auditReport->files->filter(function($f) {
                    return $f->review && $f->review->status === 'revision';
                });
                $approvedFiles = $auditReport->files->filter(function($f) {
                    return $f->review && $f->review->status === 'approved';
                });
                $pendingFiles = count($documentTypes) - $approvedFiles->count() - $revisionFiles->count();
                $allUploaded = $auditReport->files->count() === count($documentTypes);
                
                $revisionInReviewsCount = $auditReport->documentReviews()->where('status', 'revision')->count();
                $canResubmit = $auditReport->status === 'revisi' && $revisionInReviewsCount === 0;
            @endphp

            {{-- TIMELINE AUDIT --}}
            <div class="bg-white border border-gray-200/80 rounded-xl p-5 shadow-sm mb-6">
                <h3 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span>Timeline Progress Audit</span>
                </h3>
                
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    {{-- Step 1: Laporan Dibuat --}}
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-sm">✓</span>
                        <div>
                            <p class="text-xs font-bold text-gray-800">Laporan Dibuat</p>
                            <p class="text-[10px] text-gray-400">Oleh Admin</p>
                        </div>
                    </div>

                    <div class="hidden md:block flex-1 h-0.5 bg-gray-200"></div>

                    {{-- Step 2: Dokumen Diunggah --}}
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full {{ $allUploaded ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }} flex items-center justify-center font-bold text-sm">
                            {{ $allUploaded ? '✓' : '⏳' }}
                        </span>
                        <div>
                            <p class="text-xs font-bold text-gray-800">Dokumen Diunggah</p>
                            <p class="text-[10px] text-gray-400">{{ $auditReport->files->count() }}/{{ count($documentTypes) }} Dokumen</p>
                        </div>
                    </div>

                    <div class="hidden md:block flex-1 h-0.5 bg-gray-200"></div>

                    {{-- Step 3: Dikirim ke Pimpinan --}}
                    @php
                        $sentToPimpinan = $allUploaded && ($auditReport->status === 'review' || $auditReport->status === 'revisi' || $auditReport->status === 'disetujui');
                    @endphp
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-full {{ $sentToPimpinan ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' }} flex items-center justify-center font-bold text-sm">
                            {{ $sentToPimpinan ? '✓' : '⏳' }}
                        </span>
                        <div>
                            <p class="text-xs font-bold text-gray-800">Dikirim ke Pimpinan</p>
                            <p class="text-[10px] text-gray-400">Untuk Direview</p>
                        </div>
                    </div>

                    <div class="hidden md:block flex-1 h-0.5 bg-gray-200"></div>

                    {{-- Step 4 & 5 --}}
                    @if($auditReport->status === 'disetujui')
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-sm">✓</span>
                            <div>
                                <p class="text-xs font-bold text-gray-800">Seluruh Dokumen Disetujui</p>
                                <p class="text-[10px] text-green-600 font-semibold">Selesai</p>
                            </div>
                        </div>
                        
                        <div class="hidden md:block flex-1 h-0.5 bg-gray-200"></div>
                        
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-sm">✓</span>
                            <div>
                                <p class="text-xs font-bold text-gray-800">Audit Selesai</p>
                                <p class="text-[10px] text-gray-400">Arsip Masuk</p>
                            </div>
                        </div>
                    @elseif($auditReport->status === 'revisi')
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-red-100 text-red-700 flex items-center justify-center font-bold text-sm">⚠️</span>
                            <div>
                                <p class="text-xs font-bold text-gray-800">Direvisi Pimpinan</p>
                                <p class="text-[10px] text-red-500 font-semibold">Perlu Perbaikan</p>
                            </div>
                        </div>
                        
                        <div class="hidden md:block flex-1 h-0.5 bg-gray-200"></div>
                        
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full bg-yellow-100 text-yellow-700 flex items-center justify-center font-bold text-sm">⏳</span>
                            <div>
                                <p class="text-xs font-bold text-gray-800">Menunggu Perbaikan Auditor</p>
                                <p class="text-[10px] text-gray-400">Proses Edit</p>
                            </div>
                        </div>
                    @else
                        {{-- Status is review, but check if there was a prior revision --}}
                        @php
                            $hasApproved = $approvedFiles->count() > 0;
                        @endphp
                        @if($hasApproved)
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-sm">✓</span>
                                <div>
                                    <p class="text-xs font-bold text-gray-800">Diperbaiki Auditor</p>
                                    <p class="text-[10px] text-gray-400">Telah Diunggah Ulang</p>
                                </div>
                            </div>
                            
                            <div class="hidden md:block flex-1 h-0.5 bg-gray-200"></div>
                            
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full bg-yellow-100 text-yellow-700 flex items-center justify-center font-bold text-sm">⏳</span>
                                <div>
                                    <p class="text-xs font-bold text-gray-800">Menunggu Review Ulang</p>
                                    <p class="text-[10px] text-gray-400">Review Pimpinan</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full bg-yellow-100 text-yellow-700 flex items-center justify-center font-bold text-sm">⏳</span>
                                <div>
                                    <p class="text-xs font-bold text-gray-800">Menunggu Review Pertama</p>
                                    <p class="text-[10px] text-gray-400">Pimpinan</p>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- PANEL KHUSUS AUDITOR / RINGKASAN REVISI --}}
            @if(auth()->user()->role === 'auditor' || auth()->user()->role === 'admin')
            <div class="mb-6 bg-white border border-gray-200/80 rounded-xl p-5 shadow-sm">
                @if($revisionFiles->count() > 0)
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                    <h4 class="font-bold text-red-800 text-sm flex items-center gap-1.5 mb-2">
                        <span>⚠️</span> Dokumen Yang Harus Diperbaiki
                    </h4>
                    <ol class="list-decimal pl-5 text-xs text-red-700 space-y-2">
                        @foreach($revisionFiles as $revFile)
                        <li>
                            <span class="font-semibold text-red-900">{{ $documentTypes[$revFile->document_type] }}</span>
                            <p class="mt-0.5 font-normal text-red-700 bg-red-100/50 px-2.5 py-1 rounded">Catatan: "{{ $revFile->review->note }}"</p>
                        </li>
                        @endforeach
                    </ol>
                </div>
                @elseif($auditReport->status === 'disetujui')
                <div class="mb-4 bg-green-50 border-l-4 border-green-500 rounded-lg p-4">
                    <p class="font-semibold text-green-800 text-sm flex items-center gap-1.5">
                        <span>✓</span> Semua dokumen telah disetujui pimpinan
                    </p>
                </div>
                @else
                <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4">
                    <p class="font-semibold text-blue-800 text-sm flex items-center gap-1.5">
                        <span>⏳</span> Semua dokumen terunggah sedang menunggu review pimpinan
                    </p>
                </div>
                @endif

                <div class="flex flex-wrap gap-4 items-center justify-between mt-2">
                    <div class="flex gap-4 text-xs font-semibold">
                        <span class="text-green-700 bg-green-50 px-3 py-1.5 rounded-lg border border-green-100 flex items-center gap-1">
                            ✓ Dokumen Disetujui : {{ $approvedFiles->count() }}
                        </span>
                        <span class="text-red-700 bg-red-50 px-3 py-1.5 rounded-lg border border-red-100 flex items-center gap-1">
                            ⚠️ Dokumen Revisi : {{ $revisionFiles->count() }}
                        </span>
                    </div>

                    @if($revisionFiles->count() > 0 && $auditReport->status !== 'disetujui')
                    <a href="{{ route('audit_reports.edit', $auditReport) }}"
                        class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition flex items-center gap-1">
                        Perbaiki Dokumen
                    </a>
                    @endif
                </div>
            </div>
            @endif

            {{-- KIRIM ULANG REVIEW --}}
            @if($canResubmit && auth()->user()->role === 'auditor')
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <p class="font-semibold text-blue-800 text-sm">Semua dokumen revisi sudah diperbaiki!</p>
                    <p class="text-blue-700 text-xs mt-0.5">Silakan kirim ulang laporan agar pimpinan dapat melakukan review kembali.</p>
                </div>
                <form action="{{ route('audit_reports.resubmit', $auditReport) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition">
                        Kirim Ulang Review
                    </button>
                </form>
            </div>
            @endif

            <div class="space-y-6">

                {{-- INFORMASI --}}
                <div class="bg-white border border-gray-200/80 rounded-xl p-5 shadow-sm">
                    <p class="text-md font-bold text-gray-700 mb-4 border-b pb-2">Informasi Laporan</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <p class="text-sm text-gray-400 uppercase tracking-wide mb-1">Klien</p>
                            <p class="font-bold text-gray-800 text-sm">{{ $auditReport->client->institution_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400 uppercase tracking-wide mb-1">Jenis Audit</p>
                            @if($auditReport->audit_type === 'keuangan')
                            <span class="bg-blue-100 text-blue-700 text-xs px-2.5 py-1 rounded-full font-semibold">Audit Keuangan</span>
                            @else
                            <span class="bg-purple-100 text-purple-700 text-xs px-2.5 py-1 rounded-full font-semibold">Audit Kepatuhan</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-sm text-gray-400 uppercase tracking-wide mb-1">Tanggal Audit</p>
                            <p class="font-bold text-gray-800 text-sm">{{ $auditReport->audit_date->format('d M Y') }}</p>
                        </div>
                        <div class="md:col-span-3">
                            <p class="text-sm text-gray-400 uppercase tracking-wide mb-1">Auditor</p>
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
                    <h3 class="text-md font-bold text-gray-700 mb-4 border-b pb-2">
                        Dokumen Laporan
                        <span class="text-sm font-normal text-gray-400 ml-2">
                            {{ $auditReport->files->count() }}/{{ count($documentTypes) }} dokumen
                        </span>
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-md">
                            <thead>
                                <tr class="bg-gray-100 text-gray-500 text-sm uppercase tracking-wide">
                                    <th class="px-4 py-3 text-left rounded-l-lg">No</th>
                                    <th class="px-4 py-3 text-left">Nama Dokumen</th>
                                    <th class="px-4 py-3 text-left">Tipe</th>
                                    <th class="px-4 py-3 text-left">Diupload oleh</th>
                                    <th class="px-4 py-3 text-left">Status Review</th>
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
                                        <p class="text-sm text-gray-400 truncate max-w-xs">{{ $file->document_name }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-500 capitalize">
                                        {{ str_replace('_', ' ', $type) }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-500">
                                        {{ $file?->uploader?->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3" @if($file) id="status-review-{{ $file->id }}" @endif>
                                        @if($file)
                                            @php $review = $file->review; @endphp
                                            @if($review)
                                                @if($review->status === 'approved')
                                                <span class="bg-green-100 text-green-700 text-sm px-2 py-0.5 rounded-md font-semibold">Disetujui</span>
                                                @elseif($review->status === 'revision')
                                                <span class="bg-red-100 text-red-700 text-sm px-2 py-0.5 rounded-md font-semibold">Perlu Revisi</span>
                                                <p class="text-[10px] text-red-500 mt-1 max-w-xs">Catatan: "{{ $review->note }}"</p>
                                                @else
                                                <span class="bg-yellow-100 text-yellow-700 text-sm px-2 py-0.5 rounded-md font-semibold">Menunggu Review</span>
                                                @endif
                                            @else
                                                <span class="bg-yellow-100 text-yellow-700 text-sm px-2 py-0.5 rounded-md font-semibold">Menunggu Review</span>
                                            @endif
                                        @else
                                        <span class="bg-gray-100 text-gray-500 text-sm px-2 py-0.5 rounded-md">Belum Diupload</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($file)
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <div class="flex gap-2">
                                                <button type="button"
                                                    onclick="openModalPreview('{{ route('audit_reports.preview', $file->id) }}', '{{ $file->document_name }}', '{{ $file->id }}')"
                                                    class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100 text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                                    Lihat
                                                </button>
                                                <a href="{{ route('audit_reports.download', $file->id) }}"
                                                    onclick="enableReviewButton({{ $file->id }})"
                                                    class="bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                                    Download
                                                </a>
                                            </div>
                                            @if(auth()->user()->role === 'pimpinan' && $auditReport->status !== 'disetujui')
                                                @if($file->review && $file->review->is_viewed)
                                                    <button type="button"
                                                        id="btn-review-{{ $file->id }}"
                                                        data-url="{{ route('audit_reports.files.review', $file->id) }}"
                                                        data-name="{{ $file->document_name }}"
                                                        data-status="{{ $file->review?->status }}"
                                                        data-note="{{ $file->review?->note }}"
                                                        onclick="openModalReviewFile(this)"
                                                        class="w-full bg-green-600 hover:bg-green-700 text-white text-[10px] px-2 py-1 rounded-md font-semibold transition">
                                                        Review
                                                    </button>
                                                @else
                                                    <button type="button"
                                                        id="btn-review-{{ $file->id }}"
                                                        disabled
                                                        title="Harap lihat atau unduh dokumen terlebih dahulu"
                                                        data-url="{{ route('audit_reports.files.review', $file->id) }}"
                                                        data-name="{{ $file->document_name }}"
                                                        data-status="{{ $file->review?->status }}"
                                                        data-note="{{ $file->review?->note }}"
                                                        onclick="openModalReviewFile(this)"
                                                        class="w-full bg-gray-200 text-gray-400 text-[10px] px-2 py-1 rounded-md font-semibold cursor-not-allowed">
                                                        Review (Harap Lihat Dokumen)
                                                    </button>
                                                @endif
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

    {{-- MODAL REVIEW FILE --}}
    <div id="modalReviewFile" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4 transition-opacity">
        <div class="bg-white border border-gray-200/80 rounded-xl shadow-sm-xl w-full max-w-md transform transition-transform scale-95 opacity-0" id="modalReviewFileContent">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-1">Review Dokumen</h3>
                <p class="text-sm text-gray-550 mb-4" id="reviewDocName">Nama Dokumen</p>
                <form id="formReviewFile" method="POST" action="">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2 text-gray-700">Status Review</label>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="approved" id="statusApproved" onchange="toggleReviewNoteContainer()" required class="text-green-650 focus:ring-green-500">
                                <span class="text-sm font-medium text-gray-750">Disetujui</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="revision" id="statusRevision" onchange="toggleReviewNoteContainer()" required class="text-red-650 focus:ring-red-500">
                                <span class="text-sm font-medium text-gray-750">Perlu Revisi</span>
                            </label>
                        </div>
                    </div>

                    <div id="reviewNoteContainer" class="hidden mb-4">
                        <label class="block text-sm font-semibold mb-2 text-gray-750">Catatan Revisi</label>
                        <textarea name="note" id="reviewNoteInput" rows="3"
                            placeholder="Jelaskan apa yang kurang atau perlu diperbaiki..."
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-300 focus:outline-none resize-none"></textarea>
                    </div>

                    <div class="flex gap-3 justify-end">
                        <button type="button" onclick="closeModalReviewFile()"
                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-750 rounded-lg text-sm font-semibold">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold">
                            Simpan Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- TOAST NOTIFICATION --}}
    <div id="toastNotification" class="fixed bottom-5 right-5 z-50 transform translate-y-10 opacity-0 transition-all duration-300 pointer-events-none">
        <div class="bg-slate-800 text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-2 text-sm font-medium border border-gray-700">
            <span class="text-green-400 font-bold">✓</span>
            <span id="toastMessage">Pesan</span>
        </div>
    </div>

    <script>
        function openModalReviewFile(btn) {
            const url = btn.getAttribute('data-url');
            const fileName = btn.getAttribute('data-name');
            const currentStatus = btn.getAttribute('data-status');
            const currentNote = btn.getAttribute('data-note');

            document.getElementById('formReviewFile').action = url;
            document.getElementById('formReviewFile').setAttribute('data-target-btn', btn.id);
            document.getElementById('reviewDocName').innerText = fileName;
            
            const statusApproved = document.getElementById('statusApproved');
            const statusRevision = document.getElementById('statusRevision');
            const noteInput = document.getElementById('reviewNoteInput');
            
            statusApproved.checked = false;
            statusRevision.checked = false;
            noteInput.value = '';
            
            if (currentStatus === 'approved') {
                statusApproved.checked = true;
            } else if (currentStatus === 'revision') {
                statusRevision.checked = true;
                noteInput.value = currentNote;
            }
            
            toggleReviewNoteContainer();
            
            document.getElementById('modalReviewFile').classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('modalReviewFileContent').classList.remove('scale-95', 'opacity-0');
            }, 10);
        }

        function closeModalReviewFile() {
            document.getElementById('modalReviewFileContent').classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                document.getElementById('modalReviewFile').classList.add('hidden');
            }, 200);
        }

        document.getElementById('modalReviewFile').addEventListener('click', function(e) {
            if (e.target === this) closeModalReviewFile();
        });

        function toggleReviewNoteContainer() {
            const isRevision = document.getElementById('statusRevision').checked;
            const container = document.getElementById('reviewNoteContainer');
            const input = document.getElementById('reviewNoteInput');

            if (isRevision) {
                container.classList.remove('hidden');
                input.required = true;
            } else {
                container.classList.add('hidden');
                input.required = false;
            }
        }

        // JS PREVIEW MODAL
        function openModalPreview(url, title, fileId) {
            const modal = document.getElementById('modalPreview');
            const iframe = document.getElementById('previewIframe');
            const loading = document.getElementById('previewLoading');

            document.getElementById('previewTitle').innerText = title || 'Preview Dokumen';

            modal.classList.remove('hidden');
            loading.classList.remove('hidden');
            iframe.classList.add('hidden');

            // Attach onload listener BEFORE setting the src to avoid race conditions
            iframe.onload = function() {
                loading.classList.add('hidden');
                iframe.classList.remove('hidden');
            };

            // Cache buster to ensure the request is not skipped by browser cache
            const separator = url.includes('?') ? '&' : '?';
            iframe.src = url + separator + '_=' + new Date().getTime();

            if (fileId) {
                enableReviewButton(fileId);
            }
        }

        function closeModalPreview() {
            const modal = document.getElementById('modalPreview');
            const iframe = document.getElementById('previewIframe');

            modal.classList.add('hidden');
            iframe.src = '';
        }

        function enableReviewButton(fileId) {
            const btn = document.getElementById('btn-review-' + fileId);
            if (btn && btn.hasAttribute('disabled')) {
                btn.removeAttribute('disabled');
                btn.removeAttribute('title');
                btn.innerText = 'Review';
                btn.className = 'w-full bg-green-600 hover:bg-green-700 text-white text-[10px] px-2 py-1 rounded-md font-semibold transition cursor-pointer';
            }
        }

        function showToast(message) {
            const toast = document.getElementById('toastNotification');
            const msgEl = document.getElementById('toastMessage');
            msgEl.innerText = message;
            toast.classList.remove('translate-y-10', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-10', 'opacity-0');
            }, 3000);
        }

        // Intercept Form Review Submit
        document.getElementById('formReviewFile').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const action = form.action;
            const data = new FormData(form);
            const btnId = form.getAttribute('data-target-btn');
            const submitBtn = form.querySelector('button[type="submit"]');
            
            submitBtn.disabled = true;
            const originalText = submitBtn.innerText;
            submitBtn.innerText = 'Menyimpan...';

            fetch(action, {
                method: 'POST',
                body: data,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(res => {
                submitBtn.disabled = false;
                submitBtn.innerText = originalText;
                
                if (res.success) {
                    closeModalReviewFile();
                    
                    // Update Button Data Attributes
                    const btn = document.getElementById(btnId);
                    if (btn) {
                        btn.setAttribute('data-status', res.review_status);
                        btn.setAttribute('data-note', res.review_note || '');
                    }
                    
                    // Update Row Review Status
                    const fileId = btnId.replace('btn-review-', '');
                    const statusCell = document.getElementById('status-review-' + fileId);
                    if (statusCell) {
                        if (res.review_status === 'approved') {
                            statusCell.innerHTML = `<span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-semibold">Disetujui</span>`;
                        } else if (res.review_status === 'revision') {
                            statusCell.innerHTML = `
                                <span class="bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded-full font-semibold">Perlu Revisi</span>
                                <p class="text-[10px] text-red-500 mt-1 max-w-xs font-normal">Catatan: "${res.review_note}"</p>
                            `;
                        }
                    }
                    
                    // Update Overall status badge
                    const reportStatusBadge = document.getElementById('report-status-badge');
                    if (reportStatusBadge) {
                        if (res.report_status === 'review') {
                            reportStatusBadge.className = 'bg-yellow-100 text-yellow-700 px-4 py-1.5 rounded-full text-sm font-semibold';
                            reportStatusBadge.innerHTML = '● Review';
                        } else if (res.report_status === 'disetujui') {
                            reportStatusBadge.className = 'bg-green-100 text-green-700 px-4 py-1.5 rounded-full text-sm font-semibold';
                            reportStatusBadge.innerHTML = '✓ Disetujui';
                        } else if (res.report_status === 'revisi') {
                            reportStatusBadge.className = 'bg-red-100 text-red-700 px-4 py-1.5 rounded-full text-sm font-semibold';
                            reportStatusBadge.innerHTML = '↩ Revisi';
                        }
                    }
                    
                    showToast('Review berhasil disimpan.');
                } else {
                    alert('Gagal menyimpan review: ' + (res.message || 'Terjadi kesalahan.'));
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerText = originalText;
                console.error(err);
                alert('Terjadi kesalahan: ' + (err.message || 'Harap periksa input Anda.'));
            });
        });
    </script>

</x-app-layout>