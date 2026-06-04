<x-app-layout title="Laporan Audit">

    {{-- MODAL REVISI --}}
    <div id="modalRevisi" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center p-4 transition-all duration-300">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md transform scale-95 opacity-0 transition-all duration-300" id="modalRevisiContent">
            <div class="p-5">
                <h3 class="text-lg font-bold text-gray-800 mb-1">Kembalikan untuk Revisi</h3>
                <p class="text-sm text-gray-400 mb-3">Berikan catatan perbaikan laporan audit ini secara detail.</p>
                <form id="formRevisi" method="POST" action="" onsubmit="showSkeleton()">
                    @csrf
                    @method('PUT')
                    <textarea name="revision_note" rows="3" required
                        placeholder="Tulis catatan revisi..."
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-1 focus:ring-rose-500 focus:border-rose-500 focus:outline-none resize-none mb-3 transition-all"></textarea>
                    <div class="flex gap-2 justify-end">
                        <button type="button" onclick="closeModalRevisi()"
                            class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-gray-600 rounded-lg text-xs font-semibold transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-semibold transition">
                            Kirim Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        {{-- SUMMARY STATISTIC ROW --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-white border border-gray-200/80 rounded-xl p-4 shadow-sm">
            <!-- Total Laporan -->
            <div class="px-2 flex items-center gap-3 border-r border-gray-100 last:border-none text-md">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <span class=" font-bold text-gray-400 uppercase tracking-wider block leading-none">Total Laporan</span>
                    <h3 class=" font-extrabold text-blue-800 mt-1 leading-none">{{ $totalReports }}</h3>
                </div>
            </div>

            <!-- Disetujui -->
            <div class="px-2 flex items-center gap-3 border-r border-gray-100 last:border-none text-md">
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class=" font-bold text-gray-400 uppercase tracking-wider block leading-none">Disetujui</span>
                    <h3 class=" font-extrabold text-emerald-600 mt-1 leading-none">{{ $disetujuiCount }}</h3>
                </div>
            </div>

            <!-- Dalam Review -->
            <div class="px-2 flex items-center gap-3 border-r border-gray-100 last:border-none text-md">
                <div class="p-2 bg-amber-50 text-amber-500 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <span class=" font-bold text-gray-400 uppercase tracking-wider block leading-none">Dalam Review</span>
                    <h3 class=" font-extrabold text-amber-500 mt-1 leading-none">{{ $reviewCount }}</h3>
                </div>
            </div>

            <!-- Revisi -->
            <div class="px-2 flex items-center gap-3 border-r border-gray-100 last:border-none text-md">
                <div class="p-2 bg-rose-50 text-rose-500 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <span class=" font-bold text-gray-400 uppercase tracking-wider block leading-none">Revisi</span>
                    <h3 class=" font-extrabold text-rose-500 mt-1 leading-none">{{ $revisiCount }}</h3>
                </div>
            </div>
        </div>

        {{-- SEARCH, FILTER & ACTION ROW --}}
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 bg-white border border-gray-200/80 rounded-xl p-3 shadow-sm">
            <form id="reportFilterForm" method="GET" action="{{ route('audit_reports.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
                <!-- Search -->
                <div class="relative w-full md:w-80">
                    <input type="text" id="searchInput" name="search" value="{{ $search }}"
                        placeholder="Cari nomor, judul, klien, atau auditor..."
                        class="w-full bg-slate-50/50 border border-gray-200 rounded-lg pl-8 pr-3 py-1.5 text-md placeholder-gray-400 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Jenis Audit -->
                <select name="audit_type" onchange="showSkeleton(); this.form.submit()"
                    class="bg-slate-50/50 border border-gray-200 rounded-lg px-8 py-1.5 text-md font-semibold text-gray-600 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">Semua Jenis</option>
                    <option value="keuangan" {{ $audit_type === 'keuangan' ? 'selected' : '' }}>Keuangan</option>
                    <option value="kepatuhan" {{ $audit_type === 'kepatuhan' ? 'selected' : '' }}>Kepatuhan</option>
                </select>

                <!-- Status -->
                <select name="status" onchange="showSkeleton(); this.form.submit()"
                    class="bg-slate-50/50 border border-gray-200 rounded-lg px-8 py-1.5 text-md font-semibold text-gray-600 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="review" {{ $status === 'review' ? 'selected' : '' }}>Review</option>
                    <option value="disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="revisi" {{ $status === 'revisi' ? 'selected' : '' }}>Revisi</option>
                </select>

                <!-- Reset -->
                @if($search || $status || $audit_type)
                <a href="{{ route('audit_reports.index') }}" onclick="showSkeleton()"
                    class="text-md font-bold text-gray-400 hover:text-gray-600 px-1 py-1.5 transition">
                    Reset
                </a>
                @endif
            </form>

            <!-- Action Button -->
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('audit_reports.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-1.5 rounded-lg text-md font-bold shadow-sm transition flex items-center justify-center gap-1 whitespace-nowrap">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Laporan
            </a>
            @endif
        </div>

        {{-- TABLE SECTION --}}
        <div class="bg-white border border-gray-200/80 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto relative">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/60 text-gray-400 font-bold uppercase tracking-wider sticky top-0 z-10 border-b border-gray-100">
                        <tr>
                            <th class="px-2 py-3 text-left">No. Laporan</th>
                            <th class="px-2 py-3 text-left">Judul</th>
                            <th class="px-2 py-3 text-left">Klien</th>
                            <th class="px-2 py-3 text-left">Jenis</th>
                            <th class="px-2 py-3 text-left">Auditor</th>
                            <th class="px-2 py-3 text-left">Dokumen</th>
                            <th class="px-2 py-3 text-left">Status</th>
                            <th class="px-2 py-3 text-left">Terakhir</th>
                            <th class="px-2 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">

                        {{-- LOADING SKELETON --}}
                        <tr id="loadingSkeleton" class="hidden">
                            <td colspan="9" class="p-4">
                                <div class="space-y-3 animate-pulse">
                                    @for ($i = 0; $i < 3; $i++)
                                    <div class="flex items-center space-x-3 py-2">
                                        <div class="h-3.5 bg-gray-100 rounded w-1/12"></div>
                                        <div class="space-y-1.5 flex-1">
                                            <div class="h-3.5 bg-gray-100 rounded w-1/2"></div>
                                            <div class="h-2.5 bg-gray-100 rounded w-1/4"></div>
                                        </div>
                                        <div class="h-3.5 bg-gray-100 rounded w-2/12"></div>
                                        <div class="h-3.5 bg-gray-100 rounded w-1/12"></div>
                                        <div class="h-3.5 bg-gray-100 rounded w-1/12"></div>
                                        <div class="h-3.5 bg-gray-100 rounded w-2/12"></div>
                                        <div class="h-3.5 bg-gray-100 rounded w-1/12"></div>
                                    </div>
                                    @endfor
                                </div>
                            </td>
                        </tr>

                        @forelse($reports as $report)
                        <tr class="hover:bg-slate-50/30 transition group">
                            {{-- No Laporan --}}
                            <td class="px-2 py-3 font-mono text-sm text-gray-500 whitespace-nowrap">
                                {{ $report->report_number }}
                            </td>

                            {{-- Judul --}}
                            <td class="px-2 py-3 max-w-[130px]">
                                <div class="font-bold text-gray-800 text-md truncate" title="{{ $report->title }}">
                                    {{ $report->title }}
                                </div>
                                <div class="text-sm text-gray-400 mt-0.5">
                                    {{ $report->audit_date->format('d M Y') }}
                                </div>
                            </td>

                            {{-- Klien --}}
                            <td class="px-2 py-3 text-md text-gray-700 font-bold max-w-[120px] truncate" title="{{ $report->client->institution_name }}">
                                {{ $report->client->institution_name }}
                            </td>

                            {{-- Jenis Audit --}}
                            <td class="px-2 py-3 whitespace-nowrap">
                                @if($report->audit_type === 'keuangan')
                                <span class="bg-blue-50 text-blue-700 text-sm px-2 py-0.5 rounded font-bold border border-blue-100/50">
                                    Keuangan
                                </span>
                                @else
                                <span class="bg-purple-50 text-purple-700 text-sm px-2 py-0.5 rounded font-bold border border-purple-100/50">
                                    Kepatuhan
                                </span>
                                @endif
                            </td>

                            {{-- Auditor --}}
                            <td class="px-2 py-3">
                                <div class="flex flex-wrap gap-1 max-w-[110px]">
                                    @foreach($report->client->auditors as $auditor)
                                    <span class="bg-slate-50 text-slate-500 border border-slate-100 text-sm px-1.5 py-0.5 rounded font-medium truncate" title="{{ $auditor->name }}">
                                        {{ $auditor->name }}
                                    </span>
                                    @endforeach
                                </div>
                            </td>

                            {{-- Progress Dokumen --}}
                            <td class="px-2 py-3">
                                @php $fileCount = $report->files->count(); @endphp
                                <div class="flex flex-col gap-0.5 w-20">
                                    <div class="flex items-center justify-between text-sm font-bold text-gray-400">
                                        <span>{{ $fileCount }}/10</span>
                                        <span class="{{ $fileCount === 10 ? 'text-emerald-600' : 'text-blue-600' }}">{{ $fileCount * 10 }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-1 overflow-hidden">
                                        <div class="h-1 rounded-full transition-all duration-500 {{ $fileCount === 10 ? 'bg-emerald-500' : 'bg-blue-500' }}" style="width: {{ $fileCount * 10 }}%"></div>
                                    </div>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-2 py-3 whitespace-nowrap">
                                @if($report->status === 'review')
                                <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 text-sm px-2 py-0.5 rounded border border-amber-200/50 font-bold">
                                    Review
                                </span>
                                @elseif($report->status === 'revisi')
                                <span class="inline-flex items-center gap-1 bg-rose-50 text-rose-700 text-sm px-2 py-0.5 rounded border border-rose-200/50 font-bold">
                                    Revisi
                                </span>
                                @elseif($report->status === 'disetujui')
                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-sm px-2 py-0.5 rounded border border-emerald-200/50 font-bold">
                                    Disetujui
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 bg-gray-50 text-gray-600 text-sm px-2 py-0.5 rounded border border-gray-200/50 font-bold">
                                    Draft
                                </span>
                                @endif
                            </td>

                            {{-- Terakhir Diperbarui --}}
                            <td class="px-2 py-3 text-gray-500 whitespace-nowrap">
                                <span class="font-bold text-gray-700">{{ $report->updated_at->format('d/m/Y') }}</span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-2 py-3 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-2">

                                    {{-- Detail --}}
                                    <a href="{{ route('audit_reports.show', $report) }}"
                                        class="text-slate-500 hover:text-slate-800 font-bold hover:underline transition text-sm">
                                        Detail
                                    </a>

                                    @if($report->status !== 'disetujui')
                                        {{-- Edit --}}
                                        @if(in_array(auth()->user()->role, ['admin', 'auditor']))
                                            <a href="{{ route('audit_reports.edit', $report) }}"
                                                class="text-blue-600 hover:text-blue-700 font-bold hover:underline transition">
                                                Edit
                                            </a>
                                        @endif

                                        {{-- Hapus --}}
                                        @if(auth()->user()->role === 'admin')
                                            <form action="{{ route('audit_reports.destroy', $report) }}" method="POST" class="inline"
                                                onsubmit="event.preventDefault(); confirmDelete(this, 'Laporan audit ini beserta semua file dokumen terkait akan dihapus secara permanen!')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="text-rose-600 hover:text-rose-700 font-bold hover:underline transition">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Pimpinan: Approve / Revisi --}}
                                        
                                    @endif

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <h4 class="text-xs font-bold text-gray-800 mb-0.5">Laporan Tidak Ditemukan</h4>
                                    <p class="text-[11px] text-gray-400 text-center mb-4">Belum ada data laporan audit yang sesuai.</p>
                                    @if($search || $status || $audit_type)
                                    <a href="{{ route('audit_reports.index') }}" onclick="showSkeleton()"
                                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all">
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
            <div class="px-4 py-3 border-t border-gray-100 bg-slate-50/30 flex items-center justify-between">
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