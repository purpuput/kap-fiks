<x-app-layout title="Laporan Audit">

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
        <div class="max-w-7xl mx-auto px-4">

            {{-- FLASH --}}
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
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Laporan Audit</h2>
                    <p class="text-gray-500 text-sm mt-1">Kelola dan pantau laporan audit</p>
                </div>
                @if(auth()->user()->role === 'auditor')
                <a href="{{ route('audit_reports.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Laporan
                </a>
                @endif
            </div>

            {{-- FILTER --}}
            <div class="bg-white rounded-2xl shadow p-4 mb-6">
                <form method="GET" action="{{ route('audit_reports.index') }}" class="flex flex-wrap gap-3">
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Cari judul / no. laporan..."
                        class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:outline-none flex-1 min-w-48">
                    <select name="status"
                        class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approve" {{ $status === 'approve' ? 'selected' : '' }}>Approve</option>
                        <option value="revisi" {{ $status === 'revisi' ? 'selected' : '' }}>Revisi</option>
                    </select>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold">Filter</button>
                    @if($search || $status)
                    <a href="{{ route('audit_reports.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-xl text-sm font-semibold">Reset</a>
                    @endif
                </form>
            </div>

            {{-- TABLE --}}
            <div class="bg-white rounded-2xl shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3.5 text-left">No. Laporan</th>
                                <th class="px-4 py-3.5 text-left">Judul</th>
                                <th class="px-4 py-3.5 text-left">Klien</th>
                                <th class="px-4 py-3.5 text-left">Jenis</th>
                                <th class="px-4 py-3.5 text-left">Auditor</th>
                                <th class="px-4 py-3.5 text-center">Dokumen</th>
                                <th class="px-4 py-3.5 text-left">Status</th>
                                <th class="px-4 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($reports as $report)
                            <tr class="hover:bg-gray-50 transition">

                                <td class="px-4 py-4 font-mono text-xs text-gray-600 whitespace-nowrap">
                                    {{ $report->report_number }}
                                </td>

                                <td class="px-4 py-4 max-w-xs">
                                    <p class="font-semibold text-gray-800 truncate">{{ $report->title }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $report->audit_date->format('d M Y') }}</p>
                                </td>

                                <td class="px-4 py-4 text-gray-600 whitespace-nowrap">
                                    {{ $report->client->institution_name }}
                                </td>

                                <td class="px-4 py-4">
                                    @if($report->audit_type === 'keuangan')
                                    <span class="bg-blue-100 text-blue-700 text-xs px-2.5 py-1 rounded-full font-medium">Keuangan</span>
                                    @else
                                    <span class="bg-purple-100 text-purple-700 text-xs px-2.5 py-1 rounded-full font-medium">Kepatuhan</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($report->auditors as $auditor)
                                        <span class="bg-gray-100 text-gray-600 text-xs px-2 py-0.5 rounded-full">{{ $auditor->name }}</span>
                                        @endforeach
                                    </div>
                                </td>

                                <td class="px-4 py-4 text-center">
                                    @php $fileCount = $report->files->count(); @endphp
                                    <span class="text-xs font-semibold {{ $fileCount === 10 ? 'text-green-600' : 'text-orange-500' }}">
                                        {{ $fileCount }}/10
                                    </span>
                                </td>

                                <td class="px-4 py-4">
                                    @if($report->status === 'pending')
                                    <span class="bg-yellow-100 text-yellow-700 text-xs px-2.5 py-1 rounded-full font-medium">Pending</span>
                                    @elseif($report->status === 'approve')
                                    <span class="bg-green-100 text-green-700 text-xs px-2.5 py-1 rounded-full font-medium">Approve</span>
                                    @else
                                    <span class="bg-red-100 text-red-700 text-xs px-2.5 py-1 rounded-full font-medium">Revisi</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-2 flex-wrap">

                                        {{-- Detail --}}
                                        <a href="{{ route('audit_reports.show', $report) }}"
                                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                            Detail
                                        </a>

                                        {{-- Auditor: Edit --}}
                                        @if(auth()->user()->role === 'auditor' && $report->status !== 'approve')
                                        <a href="{{ route('audit_reports.edit', $report) }}"
                                            class="bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                            Edit
                                        </a>
                                        @endif

                                        {{-- Admin: Hapus --}}
                                        @if(auth()->user()->role === 'admin' && $report->status !== 'approve')
                                        <form action="{{ route('audit_reports.destroy', $report) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Hapus laporan ini? Semua file akan ikut terhapus.')"
                                                class="bg-red-50 hover:bg-red-100 text-red-600 text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                                Hapus
                                            </button>
                                        </form>
                                        @endif

                                        {{-- Pimpinan: Approve / Revisi --}}
                                        @if(auth()->user()->role === 'pimpinan' && $report->status !== 'approve')
                                        <form action="{{ route('audit_reports.approve', $report->id) }}" method="POST" class="inline">
                                            @csrf @method('PUT')
                                            <button type="submit"
                                                onclick="return confirm('Approve laporan ini?')"
                                                class="bg-green-50 hover:bg-green-100 text-green-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                                Approve
                                            </button>
                                        </form>
                                        <button type="button"
                                            data-url="{{ route('audit_reports.revisi', $report->id) }}"
                                            onclick="openModalRevisi(this.dataset.url)"
                                            class="bg-yellow-50 hover:bg-yellow-100 text-yellow-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                            Revisi
                                        </button>
                                        @endif

                                        @if(auth()->user()->role === 'pimpinan' && $report->status === 'approve')
                                        <span class="text-green-600 text-xs font-semibold">✓ Selesai</span>
                                        @endif

                                    </div>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-12 text-gray-400 text-sm">
                                    Belum ada laporan audit
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                @if($reports->hasPages())
                <div class="px-4 py-4 border-t border-gray-100">
                    {{ $reports->links() }}
                </div>
                @endif
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
        document.getElementById('modalRevisi').addEventListener('click', function(e) {
            if (e.target === this) closeModalRevisi();
        });
    </script>

</x-app-layout>