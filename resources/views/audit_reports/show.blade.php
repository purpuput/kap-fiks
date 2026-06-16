<x-app-layout title="Detail Laporan Audit">

    {{-- MODAL PREVIEW FILE --}}
    <div id="modalPreview" class="fixed inset-0 z-50 hidden bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4 lg:p-10 transition-opacity">
        <div class="bg-white border border-slate-100 rounded-3xl shadow-2xl w-full h-full flex flex-col overflow-hidden relative">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-md font-bold text-slate-800" id="previewTitle">Preview Dokumen</h3>
                <button type="button" onclick="closeModalPreview()" class="text-slate-400 hover:text-slate-700 p-2 rounded-full hover:bg-gray-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="flex-1 bg-slate-100 overflow-hidden relative rounded-b-3xl" id="previewContainer">
                <iframe id="previewIframe" class="w-full h-full border-none hidden rounded-b-3xl" src=""></iframe>
                <div id="previewLoading" class="absolute inset-0 flex items-center justify-center">
                    <div class="w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-6 animate-fade-in-up">
        <div class="max-w-5xl mx-auto px-4">

            {{-- HEADER --}}
            <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">{{ $auditReport->title }}</h2>
                    <p class="text-xs text-slate-400 mt-1.5 font-semibold">{{ $auditReport->report_number }}</p>
                </div>

                {{-- STATUS BADGE --}}
                @if($auditReport->status === 'review')
                <span id="report-status-badge" class="bg-amber-50 text-amber-700 px-3.5 py-1.5 rounded-full text-xs font-bold border border-amber-250/20 flex items-center gap-1.5 shadow-sm">
                    <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                    Review
                </span>
                @elseif($auditReport->status === 'disetujui')
                <span id="report-status-badge" class="bg-emerald-50 text-emerald-700 px-3.5 py-1.5 rounded-full text-xs font-bold border border-emerald-250/20 flex items-center gap-1.5 shadow-sm">
                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Disetujui
                </span>
                @elseif($auditReport->status === 'revisi')
                <span id="report-status-badge" class="bg-rose-50 text-rose-700 px-3.5 py-1.5 rounded-full text-xs font-bold border border-rose-250/20 flex items-center gap-1.5 shadow-sm">
                    <span class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-pulse"></span>
                    Revisi
                </span>
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
            <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm mb-6">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <span>Timeline Progress Audit</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6 relative">
                    {{-- Step 1: Laporan Dibuat --}}
                    <div class="flex flex-col items-center text-center relative group">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 border-2 border-emerald-250/20 flex items-center justify-center font-bold text-sm shadow-sm transition-all group-hover:scale-105">
                            ✓
                        </div>
                        <p class="text-xs font-bold text-slate-800 mt-2">Laporan Dibuat</p>
                        <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Oleh Admin</p>
                    </div>

                    {{-- Step 2: Dokumen Diunggah --}}
                    <div class="flex flex-col items-center text-center relative group">
                        <div class="w-10 h-10 rounded-full {{ $allUploaded ? 'bg-emerald-50 text-emerald-600 border-2 border-emerald-250/20' : 'bg-amber-50 text-amber-600 border-2 border-amber-250/20' }} flex items-center justify-center font-bold text-sm shadow-sm transition-all group-hover:scale-105">
                            {{ $allUploaded ? '✓' : '⏳' }}
                        </div>
                        <p class="text-xs font-bold text-slate-800 mt-2">Dokumen Diunggah</p>
                        <p class="text-[10px] text-slate-400 font-semibold mt-0.5">{{ $auditReport->files->count() }}/{{ count($documentTypes) }} Dokumen</p>
                    </div>

                    {{-- Step 3: Dikirim ke Pimpinan --}}
                    @php
                        $sentToPimpinan = $allUploaded && ($auditReport->status === 'review' || $auditReport->status === 'revisi' || $auditReport->status === 'disetujui');
                    @endphp
                    <div class="flex flex-col items-center text-center relative group">
                        <div class="w-10 h-10 rounded-full {{ $sentToPimpinan ? 'bg-emerald-50 text-emerald-600 border-2 border-emerald-250/20' : 'bg-slate-50 text-slate-400 border-2 border-slate-200' }} flex items-center justify-center font-bold text-sm shadow-sm transition-all group-hover:scale-105">
                            {{ $sentToPimpinan ? '✓' : '⏳' }}
                        </div>
                        <p class="text-xs font-bold text-slate-800 mt-2">Dikirim ke Pimpinan</p>
                        <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Untuk Direview</p>
                    </div>

                    {{-- Step 4 & 5 --}}
                    @if($auditReport->status === 'disetujui')
                        <div class="flex flex-col items-center text-center relative group">
                            <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 border-2 border-emerald-250/20 flex items-center justify-center font-bold text-sm shadow-sm transition-all group-hover:scale-105">
                                ✓
                            </div>
                            <p class="text-xs font-bold text-slate-800 mt-2">Dokumen Disetujui</p>
                            <p class="text-[10px] text-emerald-600 font-bold mt-0.5">Selesai</p>
                        </div>
                        
                        <div class="flex flex-col items-center text-center relative group">
                            <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 border-2 border-emerald-250/20 flex items-center justify-center font-bold text-sm shadow-sm transition-all group-hover:scale-105">
                                ✓
                            </div>
                            <p class="text-xs font-bold text-slate-800 mt-2">Audit Selesai</p>
                            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Arsip Masuk</p>
                        </div>
                    @elseif($auditReport->status === 'revisi')
                        <div class="flex flex-col items-center text-center relative group">
                            <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 border-2 border-rose-250/20 flex items-center justify-center font-bold text-sm shadow-sm transition-all group-hover:scale-105">
                                ⚠️
                            </div>
                            <p class="text-xs font-bold text-slate-800 mt-2">Direvisi Pimpinan</p>
                            <p class="text-[10px] text-rose-500 font-bold mt-0.5">Perlu Perbaikan</p>
                        </div>
                        
                        <div class="flex flex-col items-center text-center relative group">
                            <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-600 border-2 border-amber-250/20 flex items-center justify-center font-bold text-sm shadow-sm transition-all group-hover:scale-105">
                                ⏳
                            </div>
                            <p class="text-xs font-bold text-slate-800 mt-2">Menunggu Revisi</p>
                            <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Proses Edit</p>
                        </div>
                    @else
                        @php
                            $hasApproved = $approvedFiles->count() > 0;
                        @endphp
                        @if($hasApproved)
                            <div class="flex flex-col items-center text-center relative group">
                                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 border-2 border-emerald-250/20 flex items-center justify-center font-bold text-sm shadow-sm transition-all group-hover:scale-105">
                                    ✓
                                </div>
                                <p class="text-xs font-bold text-slate-800 mt-2">Diperbaiki Auditor</p>
                                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Telah Diunggah Ulang</p>
                            </div>
                            
                            <div class="flex flex-col items-center text-center relative group">
                                <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-600 border-2 border-amber-200/50 flex items-center justify-center font-bold text-sm shadow-sm transition-all group-hover:scale-105">
                                    ⏳
                                </div>
                                <p class="text-xs font-bold text-slate-800 mt-2">Menunggu Review</p>
                                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Review Pimpinan</p>
                            </div>
                        @else
                            <div class="flex flex-col items-center text-center relative group">
                                <div class="w-10 h-10 rounded-full bg-amber-50 text-amber-600 border-2 border-amber-250/20 flex items-center justify-center font-bold text-sm shadow-sm transition-all group-hover:scale-105">
                                    ⏳
                                </div>
                                <p class="text-xs font-bold text-slate-800 mt-2">Menunggu Review</p>
                                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Pimpinan</p>
                            </div>
                            
                            <div class="flex flex-col items-center text-center relative group">
                                <div class="w-10 h-10 rounded-full bg-slate-50 text-slate-400 border-2 border-slate-200 flex items-center justify-center font-bold text-sm shadow-sm transition-all group-hover:scale-105">
                                    ⏳
                                </div>
                                <p class="text-xs font-bold text-slate-400 mt-2">Persetujuan Akhir</p>
                                <p class="text-[10px] text-slate-300 font-semibold mt-0.5">Selesai</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            {{-- PANEL KHUSUS AUDITOR / RINGKASAN REVISI --}}
            @if(auth()->user()->role === 'auditor' || auth()->user()->role === 'admin')
            <div class="mb-6 bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                @if($revisionFiles->count() > 0)
                <div class="mb-4 bg-rose-50/50 border border-rose-100 rounded-2xl p-4">
                    <h4 class="font-bold text-rose-800 text-sm flex items-center gap-1.5 mb-2">
                        <span>⚠️</span> Dokumen Yang Harus Diperbaiki
                    </h4>
                    <ol class="list-decimal pl-5 text-xs text-rose-700 space-y-2 font-semibold">
                        @foreach($revisionFiles as $revFile)
                        <li>
                            <span class="font-semibold text-rose-900">{{ $documentTypes[$revFile->document_type] }}</span>
                            <p class="mt-1 font-semibold text-rose-750 bg-rose-100/30 px-3 py-1.5 rounded-xl border border-rose-200/10">Catatan: "{{ $revFile->review->note }}"</p>
                        </li>
                        @endforeach
                    </ol>
                </div>
                @elseif($auditReport->status === 'disetujui')
                <div class="mb-4 bg-emerald-50/50 border border-emerald-100 rounded-2xl p-4">
                    <p class="font-bold text-emerald-800 text-sm flex items-center gap-1.5">
                        <span>✓</span> Semua dokumen telah disetujui pimpinan
                    </p>
                </div>
                @else
                <div class="mb-4 bg-blue-50/50 border border-blue-100 rounded-2xl p-4">
                    <p class="font-bold text-blue-800 text-sm flex items-center gap-1.5">
                        <span>⏳</span> Semua dokumen terunggah sedang menunggu review pimpinan
                    </p>
                </div>
                @endif

                <div class="flex flex-wrap gap-4 items-center justify-between mt-2">
                    <div class="flex gap-4 text-xs font-bold">
                        <span class="text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-100/50 flex items-center gap-1">
                            ✓ Dokumen Disetujui : {{ $approvedFiles->count() }}
                        </span>
                        <span class="text-rose-700 bg-rose-50 px-3 py-1.5 rounded-xl border border-rose-100/50 flex items-center gap-1">
                            ⚠️ Dokumen Revisi : {{ $revisionFiles->count() }}
                        </span>
                    </div>

                    @if($revisionFiles->count() > 0 && $auditReport->status !== 'disetujui')
                    <a href="{{ route('audit_reports.edit', $auditReport) }}"
                        class="bg-gradient-to-r from-rose-600 to-red-650 hover:from-rose-700 hover:to-red-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all shadow-sm shadow-red-500/10 hover:shadow-md flex items-center gap-1">
                        Perbaiki Dokumen
                    </a>
                    @endif
                </div>
            </div>
            @endif

            {{-- KIRIM ULANG REVIEW --}}
            @if($canResubmit && auth()->user()->role === 'auditor')
            <div class="mb-6 bg-blue-50/50 border border-blue-100 rounded-3xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <p class="font-bold text-blue-800 text-sm">Semua dokumen revisi sudah diperbaiki!</p>
                    <p class="text-blue-700 text-xs mt-1 font-semibold">Silakan kirim ulang laporan agar pimpinan dapat melakukan review kembali.</p>
                </div>
                <form action="{{ route('audit_reports.resubmit', $auditReport) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm shadow-blue-500/10 hover:shadow-md">
                        Kirim Ulang Review
                    </button>
                </form>
            </div>
            @endif

            <div class="space-y-6">

                {{-- INFORMASI --}}
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                    <p class="text-md font-bold text-slate-700 mb-4 border-b border-slate-100 pb-3">Informasi Laporan</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <p class="text-[10px] text-slate-400 uppercase tracking-wider font-bold mb-1">Klien</p>
                            <p class="font-bold text-slate-700 text-sm">{{ $auditReport->client->institution_name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 uppercase tracking-wider font-bold mb-1">Jenis Audit</p>
                            @if($auditReport->audit_type === 'keuangan')
                            <span class="bg-blue-50 text-blue-700 text-xs px-3 py-1 rounded-full font-bold border border-blue-200/30">Audit Keuangan</span>
                            @else
                            <span class="bg-purple-50 text-purple-700 text-xs px-3 py-1 rounded-full font-bold border border-purple-200/30">Audit Kepatuhan</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 uppercase tracking-wider font-bold mb-1">Tanggal Audit</p>
                            <p class="font-bold text-slate-700 text-sm">{{ $auditReport->audit_date->format('d M Y') }}</p>
                        </div>
                        <div class="md:col-span-3">
                            <p class="text-[10px] text-slate-400 uppercase tracking-wider font-bold mb-1">Auditor</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($auditReport->client->auditors as $auditor)
                                <span class="bg-slate-50 border border-slate-100 text-slate-650 text-xs px-3 py-1 rounded-full font-semibold">{{ $auditor->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TABEL DOKUMEN --}}
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">
                    <h3 class="text-md font-bold text-slate-700 mb-4 border-b border-slate-100 pb-3 flex items-center justify-between">
                        <span>Dokumen Laporan</span>
                        <span class="text-xs font-semibold text-slate-400">
                            {{ $auditReport->files->count() }}/{{ count($documentTypes) }} dokumen
                        </span>
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50/80 text-slate-600 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                                    <th class="px-4 py-3 text-left rounded-l-xl">No</th>
                                    <th class="px-4 py-3 text-left">Nama Dokumen</th>
                                    <th class="px-4 py-3 text-left">Tipe</th>
                                    <th class="px-4 py-3 text-left">Diupload oleh</th>
                                    <th class="px-4 py-3 text-left">Status Review</th>
                                    <th class="px-4 py-3 text-center rounded-r-xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($documentTypes as $type => $label)
                                @php $file = $filesByType[$type] ?? null; @endphp
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-4 py-3.5 text-slate-400 font-semibold">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3.5">
                                        <p class="font-bold text-slate-700">{{ $label }}</p>
                                        @if($file)
                                        <p class="text-xs text-slate-400 truncate max-w-xs font-semibold mt-0.5">{{ $file->document_name }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-slate-550 capitalize font-medium">
                                        {{ str_replace('_', ' ', $type) }}
                                    </td>
                                    <td class="px-4 py-3.5 text-slate-550 font-medium">
                                        {{ $file?->uploader?->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3.5" @if($file) id="status-review-{{ $file->id }}" @endif>
                                        @if($file)
                                            @php $review = $file->review; @endphp
                                            @if($review)
                                                @if($review->status === 'approved')
                                                <span class="bg-emerald-50 text-emerald-700 text-xs px-2.5 py-0.5 rounded-full font-bold border border-emerald-250/20">Disetujui</span>
                                                @elseif($review->status === 'revision')
                                                <span class="bg-rose-50 text-rose-700 text-xs px-2.5 py-0.5 rounded-full font-bold border border-rose-250/20">Perlu Revisi</span>
                                                <p class="text-[10px] text-rose-600 mt-1 max-w-xs font-semibold">Catatan: "{{ $review->note }}"</p>
                                                @else
                                                <span class="bg-amber-50 text-amber-700 text-xs px-2.5 py-0.5 rounded-full font-bold border border-amber-250/20">Menunggu Review</span>
                                                @endif
                                            @else
                                                <span class="bg-amber-50 text-amber-700 text-xs px-2.5 py-0.5 rounded-full font-bold border border-amber-250/20">Menunggu Review</span>
                                            @endif
                                        @else
                                        <span class="bg-slate-50 text-slate-400 text-xs px-2.5 py-0.5 rounded-full font-bold border border-slate-200">Belum Diupload</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-center">
                                        @if($file)
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <div class="flex gap-2">
                                                <button type="button"
                                                    onclick="openModalPreview('{{ route('audit_reports.preview', $file->id) }}', '{{ $file->document_name }}', '{{ $file->id }}')"
                                                    class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs px-3.5 py-2 rounded-xl font-bold transition">
                                                    Lihat
                                                </button>
                                                <a href="{{ route('audit_reports.download', $file->id) }}"
                                                    onclick="enableReviewButton({{ $file->id }})"
                                                    class="bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs px-3.5 py-2 rounded-xl font-bold transition">
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
                                                        class="w-full bg-emerald-650 hover:bg-emerald-700 text-white text-[11px] px-2.5 py-2 rounded-xl font-bold transition cursor-pointer shadow-sm uppercase tracking-wider">
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
                                                        class="w-full bg-slate-50 text-slate-400 text-[10px] px-2.5 py-2 rounded-xl font-bold cursor-not-allowed border border-slate-200/50">
                                                        Review (Harap Lihat)
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                        @else
                                        <span class="text-slate-300 text-xs font-bold">—</span>
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
                <div class="bg-emerald-50/50 border border-emerald-100 rounded-3xl p-5 flex items-center gap-3">
                    <svg class="w-6 h-6 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-emerald-750 text-sm font-semibold">
                        Laporan ini telah disetujui. Semua dokumen sudah tersimpan di
                        <a href="{{ route('document_managements.index') }}" class="underline hover:text-emerald-950">Manajemen Dokumen</a>.
                    </p>
                </div>
                @endif

                {{-- TOMBOL BAWAH --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('audit_reports.index') }}"
                        class="bg-white border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl text-sm font-semibold transition hover:bg-slate-50">
                        ← Kembali ke Daftar
                    </a>
                    @if(in_array(auth()->user()->role, ['admin', 'auditor']) && $auditReport->status !== 'disetujui')
                    <a href="{{ route('audit_reports.edit', $auditReport) }}"
                        class="bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition shadow-sm shadow-blue-500/10 hover:shadow-md">
                        Edit Laporan
                    </a>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL REVIEW FILE --}}
    <div id="modalReviewFile" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
        <div class="bg-white border border-slate-100 rounded-3xl shadow-xl w-full max-w-md transform transition-transform scale-95 opacity-0" id="modalReviewFileContent">
            <div class="p-6">
                <h3 class="text-md font-bold text-slate-800 mb-1">Review Dokumen</h3>
                <p class="text-xs text-slate-400 mb-5 font-semibold" id="reviewDocName">Nama Dokumen</p>
                <form id="formReviewFile" method="POST" action="">
                    @csrf
                    @method('PATCH')

                    <div class="mb-5">
                        <label class="block text-sm font-bold text-slate-700 mb-2.5">Status Review</label>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="status" value="approved" id="statusApproved" onchange="toggleReviewNoteContainer()" required class="text-emerald-600 focus:ring-emerald-500 border-slate-300 w-4 h-4">
                                <span class="text-sm font-semibold text-slate-655 group-hover:text-slate-800 transition">Disetujui</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="status" value="revision" id="statusRevision" onchange="toggleReviewNoteContainer()" required class="text-rose-600 focus:ring-rose-500 border-slate-300 w-4 h-4">
                                <span class="text-sm font-semibold text-slate-655 group-hover:text-slate-800 transition">Perlu Revisi</span>
                            </label>
                        </div>
                    </div>

                    <div id="reviewNoteContainer" class="hidden mb-5">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Catatan Revisi</label>
                        <textarea name="note" id="reviewNoteInput" rows="3"
                            placeholder="Jelaskan apa yang kurang atau perlu diperbaiki..."
                            class="w-full border border-slate-200 rounded-2xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all resize-none"></textarea>
                    </div>

                    <div class="flex gap-3 justify-end">
                        <button type="button" onclick="closeModalReviewFile()"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-sm font-semibold transition shadow-sm">
                            Simpan Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- TOAST NOTIFICATION --}}
    <div id="toastNotification" class="fixed bottom-5 right-5 z-50 transform translate-y-10 opacity-0 transition-all duration-300 pointer-events-none">
        <div class="bg-slate-850 text-white px-4 py-3 rounded-2xl shadow-lg flex items-center gap-2 text-sm font-bold border border-slate-800">
            <span class="text-emerald-400 font-bold">✓</span>
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
                btn.className = 'w-full bg-emerald-650 hover:bg-emerald-700 text-white text-[11px] px-2.5 py-2 rounded-xl font-bold transition cursor-pointer shadow-sm uppercase tracking-wider';
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
                            statusCell.innerHTML = `<span class="bg-emerald-50 text-emerald-700 text-xs px-2.5 py-0.5 rounded-full font-bold border border-emerald-250/20">Disetujui</span>`;
                        } else if (res.review_status === 'revision') {
                            statusCell.innerHTML = `
                                <span class="bg-rose-50 text-rose-700 text-xs px-2.5 py-0.5 rounded-full font-bold border border-rose-250/20">Perlu Revisi</span>
                                <p class="text-[10px] text-rose-600 mt-1 max-w-xs font-semibold">Catatan: "${res.review_note}"</p>
                            `;
                        }
                    }
                    
                    // Update Overall status badge
                    const reportStatusBadge = document.getElementById('report-status-badge');
                    if (reportStatusBadge) {
                        if (res.report_status === 'review') {
                            reportStatusBadge.className = 'bg-amber-50 text-amber-700 px-3.5 py-1.5 rounded-full text-xs font-bold border border-amber-250/20 flex items-center gap-1.5 shadow-sm';
                            reportStatusBadge.innerHTML = '<span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span> Review';
                        } else if (res.report_status === 'disetujui') {
                            reportStatusBadge.className = 'bg-emerald-50 text-emerald-700 px-3.5 py-1.5 rounded-full text-xs font-bold border border-emerald-250/20 flex items-center gap-1.5 shadow-sm';
                            reportStatusBadge.innerHTML = '<svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Disetujui';
                        } else if (res.report_status === 'revisi') {
                            reportStatusBadge.className = 'bg-rose-50 text-rose-700 px-3.5 py-1.5 rounded-full text-xs font-bold border border-rose-250/20 flex items-center gap-1.5 shadow-sm';
                            reportStatusBadge.innerHTML = '<span class="w-1.5 h-1.5 bg-rose-500 rounded-full animate-pulse"></span> Revisi';
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