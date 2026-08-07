<x-app-layout title="Laporan Audit">

    {{-- MODAL REVISI --}}
    <div id="modalRevisi" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-300">
        <div class="bg-white border border-slate-100 rounded-3xl shadow-xl w-full max-w-md transform scale-95 opacity-0 transition-all duration-300" id="modalRevisiContent">
            <div class="p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-1">Kembalikan untuk Revisi</h3>
                <p class="text-xs text-slate-405 mb-4">Berikan catatan perbaikan laporan audit ini secara detail.</p>
                <form id="formRevisi" method="POST" action="" onsubmit="showSkeleton()">
                    @csrf
                    @method('PUT')
                    <textarea name="revision_note" rows="3" required
                        placeholder="Tulis catatan revisi..."
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 focus:outline-none resize-none mb-4 transition-all placeholder-slate-400"></textarea>
                    <div class="flex gap-3 justify-end">
                        <button type="button" onclick="closeModalRevisi()"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-rose-500 to-red-650 hover:from-rose-600 hover:to-red-700 text-white rounded-xl text-sm font-bold transition shadow-sm">
                            Kirim Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="space-y-6 animate-fade-in-up">
        {{-- SUMMARY STATISTIC ROW --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
            <!-- Total Laporan -->
            <div class="px-2 flex items-center gap-3 border-r border-slate-100 last:border-none text-sm">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block leading-none">Total Laporan</span>
                    <h3 class="text-2xl font-black text-slate-800 mt-1.5 leading-none">{{ $totalReports }}</h3>
                </div>
            </div>

            <!-- Disetujui -->
            <div class="px-2 flex items-center gap-3 border-r border-slate-100 last:border-none text-sm">
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block leading-none">Disetujui</span>
                    <h3 class="text-2xl font-black text-emerald-600 mt-1 leading-none">{{ $disetujuiCount }}</h3>
                    <p class="text-[10px] text-slate-400 mt-1">Lolos review & final</p>
                </div>
            </div>

            <!-- Dalam Review -->
            <div class="px-2 flex items-center gap-3 border-r border-slate-100 last:border-none text-sm">
                <div class="p-3 bg-amber-50 text-amber-500 rounded-2xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block leading-none">Dalam Review</span>
                    <h3 class="text-2xl font-black text-amber-500 mt-1 leading-none">{{ $reviewCount }}</h3>
                    <p class="text-[10px] text-slate-400 mt-1">Sedang diperiksa</p>
                </div>
            </div>

            <!-- Perlu Revisi -->
            <div class="px-2 flex items-center gap-3 border-r border-slate-100 last:border-none text-sm">
                <div class="p-3 bg-rose-50 text-rose-500 rounded-2xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block leading-none">Perlu Revisi</span>
                    <h3 class="text-2xl font-black text-rose-500 mt-1 leading-none">{{ $revisiCount }}</h3>
                    <p class="text-[10px] text-slate-400 mt-1">Ada catatan perbaikan</p>
                </div>
            </div>
        </div>

        {{-- SEARCH, FILTER & ACTION ROW --}}
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
            <form id="reportFilterForm" method="GET" action="{{ route('audit_reports.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
                <!-- Search -->
                <div class="relative w-full md:w-80">
                    <input type="text" id="searchInput" name="search" value="{{ $search }}"
                        placeholder="Cari nomor, judul, klien, atau auditor..."
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Jenis Audit -->
                <select name="audit_type" onchange="showSkeleton(); this.form.submit()"
                    class="bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-650 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer">
                    <option value="">Semua Jenis Audit</option>
                    <option value="keuangan" {{ $audit_type === 'keuangan' ? 'selected' : '' }}>Keuangan</option>
                    <option value="kepatuhan" {{ $audit_type === 'kepatuhan' ? 'selected' : '' }}>Kepatuhan</option>
                </select>

                <!-- Status -->
                <select name="status" onchange="showSkeleton(); this.form.submit()"
                    class="bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-650 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft (Belum diajukan)</option>
                    <option value="review" {{ $status === 'review' ? 'selected' : '' }}>Dalam Review (Sedang diperiksa)</option>
                    <option value="revisi" {{ $status === 'revisi' ? 'selected' : '' }}>Perlu Revisi (Ada catatan)</option>
                    <option value="disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui (Lolos review & final)</option>
                </select>

                <!-- Reset -->
                @if($search || $status || $audit_type)
                <a href="{{ route('audit_reports.index') }}" onclick="showSkeleton()"
                    class="text-sm font-bold text-slate-400 hover:text-slate-650 px-2 py-2.5 transition">
                    Reset
                </a>
                @endif
            </form>

            <!-- Action Button -->
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('audit_reports.create') }}"
                class="bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm shadow-blue-500/10 hover:shadow-md transition flex items-center justify-center gap-1.5 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Laporan
            </a>
            @endif
        </div>

        {{-- TABLE SECTION --}}
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto relative">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/60 text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-4 text-left">No. Laporan</th>
                            <th class="px-5 py-4 text-left">Judul</th>
                            <th class="px-5 py-4 text-left">Klien</th>
                            <th class="px-5 py-4 text-left">Jenis</th>
                            <th class="px-5 py-4 text-left">Auditor</th>
                            <th class="px-5 py-4 text-left">Dokumen</th>
                            <th class="px-5 py-4 text-left">Status</th>
                            <th class="px-5 py-4 text-left">Terakhir</th>
                            <th class="px-5 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">

                        {{-- LOADING SKELETON --}}
                        <tr id="loadingSkeleton" class="hidden">
                            <td colspan="9" class="p-6">
                                <div class="space-y-4 animate-pulse">
                                    @for ($i = 0; $i < 3; $i++)
                                    <div class="flex items-center space-x-4 py-2">
                                        <div class="h-3.5 bg-slate-100 rounded-lg w-1/12"></div>
                                        <div class="space-y-2 flex-1">
                                            <div class="h-3.5 bg-slate-100 rounded-lg w-1/2"></div>
                                            <div class="h-2.5 bg-slate-100 rounded-lg w-1/4"></div>
                                        </div>
                                        <div class="h-3.5 bg-slate-100 rounded-lg w-2/12"></div>
                                        <div class="h-3.5 bg-slate-100 rounded-lg w-1/12"></div>
                                        <div class="h-3.5 bg-slate-100 rounded-lg w-1/12"></div>
                                        <div class="h-3.5 bg-slate-100 rounded-lg w-2/12"></div>
                                        <div class="h-3.5 bg-slate-100 rounded-lg w-1/12"></div>
                                    </div>
                                    @endfor
                                </div>
                            </td>
                        </tr>

                        @forelse($reports as $report)
                        <tr class="hover:bg-slate-50/40 transition duration-150">
                            {{-- No Laporan --}}
                            <td class="px-5 py-4 font-mono text-xs text-slate-400 whitespace-nowrap">
                                {{ $report->report_number }}
                            </td>

                            {{-- Judul --}}
                            <td class="px-5 py-4 max-w-[150px]">
                                <div class="font-bold text-slate-800 text-sm truncate" title="{{ $report->title }}">
                                    {{ $report->title }}
                                </div>
                                <div class="text-xs text-slate-400 mt-1">
                                    {{ $report->audit_date->format('d M Y') }}
                                </div>
                            </td>

                            {{-- Klien --}}
                            <td class="px-5 py-4 text-sm text-slate-800 font-bold max-w-[140px] truncate" title="{{ $report->client->institution_name }}">
                                {{ $report->client->institution_name }}
                            </td>

                            {{-- Jenis Audit --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($report->audit_type === 'keuangan')
                                <span class="bg-blue-50 text-blue-750 text-xs px-2.5 py-0.5 rounded-full font-bold border border-blue-200/50">
                                    Keuangan
                                </span>
                                @else
                                <span class="bg-purple-50 text-purple-750 text-xs px-2.5 py-0.5 rounded-full font-bold border border-purple-200/50">
                                    Kepatuhan
                                </span>
                                @endif
                            </td>

                            {{-- Auditor --}}
                            <td class="px-5 py-4">
                                <div class="flex flex-wrap gap-1 max-w-[130px]">
                                    @foreach($report->client->auditors as $auditor)
                                    <span class="bg-slate-50 text-slate-500 border border-slate-100 text-[10px] px-2 py-0.5 rounded-full font-semibold truncate" title="{{ $auditor->name }}">
                                        {{ $auditor->name }}
                                    </span>
                                    @endforeach
                                </div>
                            </td>

                            {{-- Progress Dokumen --}}
                            <td class="px-5 py-4">
                                @php $fileCount = $report->files->count(); @endphp
                                <div class="flex flex-col gap-1 w-24">
                                    <div class="flex items-center justify-between text-xs font-bold text-slate-400">
                                        <span>{{ $fileCount }}/10</span>
                                        <span class="{{ $fileCount === 10 ? 'text-emerald-600' : 'text-blue-600' }}">{{ $fileCount * 10 }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-1 overflow-hidden">
                                        <div class="h-1 rounded-full transition-all duration-500 {{ $fileCount === 10 ? 'bg-emerald-500' : 'bg-blue-500' }}" style="width: {{ $fileCount * 10 }}%"></div>
                                    </div>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($report->status === 'review')
                                <span class="inline-flex flex-col items-start bg-amber-50 text-amber-800 text-xs px-2.5 py-1 rounded-lg border border-amber-200/60 font-bold" title="Sedang diperiksa auditor/reviewer">
                                    <span>Dalam Review</span>
                                    <span class="text-[9px] font-normal text-amber-700/80">Sedang diperiksa</span>
                                </span>
                                @elseif($report->status === 'revisi')
                                <span class="inline-flex flex-col items-start bg-rose-50 text-rose-800 text-xs px-2.5 py-1 rounded-lg border border-rose-200/60 font-bold" title="Ada temuan/catatan yang harus diperbaiki">
                                    <span>Perlu Revisi</span>
                                    <span class="text-[9px] font-normal text-rose-700/80">Ada temuan perbaikan</span>
                                </span>
                                @elseif($report->status === 'disetujui')
                                <span class="inline-flex flex-col items-start bg-emerald-50 text-emerald-800 text-xs px-2.5 py-1 rounded-lg border border-emerald-200/60 font-bold" title="Sudah lolos review dan final">
                                    <span>Disetujui</span>
                                    <span class="text-[9px] font-normal text-emerald-700/80">Lolos review & final</span>
                                </span>
                                @else
                                <span class="inline-flex flex-col items-start bg-slate-100 text-slate-700 text-xs px-2.5 py-1 rounded-lg border border-slate-200 font-bold" title="Belum diajukan">
                                    <span>Draft</span>
                                    <span class="text-[9px] font-normal text-slate-500">Belum diajukan</span>
                                </span>
                                @endif
                            </td>

                            {{-- Terakhir Diperbarui --}}
                            <td class="px-5 py-4 text-slate-500 whitespace-nowrap">
                                <span class="font-semibold text-slate-700">{{ $report->updated_at->format('d/m/Y') }}</span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-4 whitespace-nowrap text-xs">
                                <div class="flex items-center justify-center gap-3">

                                    {{-- Detail --}}
                                    <a href="{{ route('audit_reports.show', $report) }}"
                                        class="text-slate-600 hover:text-slate-800 font-bold hover:underline transition">
                                        Detail
                                    </a>

                                    @if($report->status !== 'disetujui')
                                        {{-- Edit --}}
                                        @if(in_array(auth()->user()->role, ['admin', 'auditor']))
                                            <a href="{{ route('audit_reports.edit', $report) }}"
                                                class="text-blue-600 hover:text-blue-800 font-extrabold transition">
                                                Edit
                                            </a>
                                        @endif

                                        {{-- Hapus --}}
                                        @if(auth()->user()->role === 'admin')
                                            <form action="{{ route('audit_reports.destroy', $report) }}" method="POST" class="inline"
                                                onsubmit="event.preventDefault(); confirmDelete(this, 'Laporan audit ini beserta semua file dokumen terkait akan dihapus secara permanen!')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="text-rose-600 hover:text-rose-800 font-extrabold transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    @endif

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <h4 class="text-sm font-bold text-slate-800 mb-1">Laporan Tidak Ditemukan</h4>
                                    <p class="text-xs text-slate-400 text-center mb-4">Belum ada data laporan audit yang sesuai.</p>
                                    @if($search || $status || $audit_type)
                                    <a href="{{ route('audit_reports.index') }}" onclick="showSkeleton()"
                                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl text-xs font-bold transition-all">
                                        Reset Filter
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if($reports->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/30 flex items-center justify-between">
                {{ $reports->links() }}
            </div>
            @endif
        </div>

    </div>

    <script>
        function openModalRevisi(url) {
            document.getElementById('formRevisi').action = url;
            const modal = document.getElementById('modalRevisi');
            const content = document.getElementById('modalRevisiContent');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
            }, 10);
        }

        function closeModalRevisi() {
            const modal = document.getElementById('modalRevisi');
            const content = document.getElementById('modalRevisiContent');
            
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        document.getElementById('modalRevisi').addEventListener('click', function(e) {
            if (e.target === this) closeModalRevisi();
        });

        // Skeleton Loading Handler
        function showSkeleton() {
            const rows = document.querySelectorAll('tbody tr:not(#loadingSkeleton)');
            rows.forEach(r => r.classList.add('hidden'));
            
            const skeleton = document.getElementById('loadingSkeleton');
            if (skeleton) skeleton.classList.remove('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const form = document.getElementById('reportFilterForm');
            let timeout = null;

            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    showSkeleton();
                    form.submit();
                }, 500);
            });

            // Keep cursor at the end of search input after page load/submit
            if (searchInput.value.length > 0) {
                const len = searchInput.value.length;
                searchInput.focus();
                searchInput.setSelectionRange(len, len);
            }
        });
    </script>

</x-app-layout>