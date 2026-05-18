<x-app-layout title="Data Klien">

    <div class="py-10">

        <div class="max-w-7xl mx-auto px-4">

            {{-- Flash message --}}
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

            {{-- Header --}}
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">

                <div>

                    <h2 class="text-2xl font-bold text-gray-800">
                        Data Klien
                    </h2>

                    <p class="text-gray-500 text-sm mt-1">
                        Kelola data klien audit dan penugasan tim auditor
                    </p>

                </div>

                {{-- Admin tambah client --}}
                @if(auth()->user()->role === 'admin')

                <a href="{{ route('clients.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition flex items-center gap-2">

                    <svg class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24">

                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4" />

                    </svg>

                    Tambah Klien

                </a>

                @endif

            </div>

            {{-- Filter --}}
            <div class="bg-white rounded-2xl shadow p-4 mb-6">

                <form method="GET"
                    action="{{ route('clients.index') }}"
                    class="flex flex-wrap gap-3 items-center">

                    {{-- Search --}}
                    <input
                        id="searchInput"
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama instansi..."
                        class="border border-gray-300 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:outline-none w-full md:w-[380px]">

                    {{-- Status --}}
                    <select name="status"
                        onchange="this.form.submit()"
                        class="border border-gray-300 rounded-xl px-6 py-2 text-sm focus:ring-2 focus:ring-blue-200 focus:outline-none">

                        <option value="">
                            Semua Status
                        </option>

                        <option value="aktif"
                            {{ request('status') === 'aktif' ? 'selected' : '' }}>

                            Aktif

                        </option>

                        <option value="nonaktif"
                            {{ request('status') === 'nonaktif' ? 'selected' : '' }}>

                            Nonaktif

                        </option>

                    </select>

                    {{-- Reset --}}
                    @if(request('search') || request('status'))

                    <a href="{{ route('clients.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-xl text-sm font-semibold">

                        Reset

                    </a>

                    @endif

                </form>

            </div>

            {{-- Table --}}
            <div class="bg-white rounded-2xl shadow overflow-hidden">

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">

                            <tr>

                                <th class="px-4 py-3.5 text-left">
                                    No
                                </th>

                                <th class="px-4 py-3.5 text-left">
                                    Instansi
                                </th>

                                <th class="px-4 py-3.5 text-left">
                                    Pimpinan
                                </th>

                                <th class="px-4 py-3.5 text-left">
                                    Tim Audit
                                </th>

                                <th class="px-4 py-3.5 text-left">
                                    Kontrak
                                </th>

                                <th class="px-4 py-3.5 text-left">
                                    Telepon
                                </th>

                                <th class="px-4 py-3.5 text-left">
                                    Status Klien
                                </th>

                                {{-- Review hanya admin & pimpinan --}}
                                @if(auth()->user()->role !== 'auditor')

                                <th class="px-4 py-3.5 text-left">
                                    Status Review
                                </th>

                                @endif

                                <th class="px-4 py-3.5 text-center">
                                    Aksi
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @forelse($clients as $client)

                            <tr class="hover:bg-gray-50 transition">

                                {{-- No --}}
                                <td class="px-4 py-4 text-gray-400">

                                    {{ $loop->iteration }}

                                </td>

                                {{-- Instansi --}}
                                <td class="px-4 py-4">

                                    <p class="font-semibold text-gray-800">

                                        {{ $client->institution_name }}

                                    </p>

                                    @if($client->institution_type)

                                    <p class="text-xs text-gray-400 mt-1">

                                        {{ $client->institution_type }}

                                    </p>

                                    @endif

                                </td>

                                {{-- Pimpinan --}}
                                <td class="px-4 py-4 text-gray-700">

                                    {{ $client->leader_name }}

                                </td>

                                {{-- Tim audit --}}
                                <td class="px-4 py-4">

                                    <div class="space-y-2">

                                        @forelse($client->auditors as $auditor)

                                        <div class="flex items-center gap-2">

                                            <span class="text-sm font-medium text-gray-700">

                                                {{ $auditor->name }}

                                            </span>

                                            @if($auditor->pivot->team_role === 'ketua')

                                            <span class="bg-blue-100 text-blue-700 text-[10px] px-2 py-0.5 rounded-full font-semibold">

                                                Ketua

                                            </span>

                                            @endif

                                        </div>

                                        @empty

                                        <span class="text-sm text-gray-400">

                                            Belum ada auditor

                                        </span>

                                        @endforelse

                                    </div>

                                </td>

                                {{-- Kontrak --}}
                                <td class="px-4 py-4 text-sm text-gray-600">

                                    @if($client->contract_start && $client->contract_end)

                                    <div class="space-y-1">

                                        <div>

                                            {{ \Carbon\Carbon::parse($client->contract_start)->format('d M Y') }}

                                            -

                                            {{ \Carbon\Carbon::parse($client->contract_end)->format('d M Y') }}

                                        </div>

                                        {{-- Status kontrak --}}
                                        @if($client->contract_status === 'aktif')

                                        <span class="text-xs text-green-600 font-semibold">

                                            Kontrak Aktif

                                        </span>

                                        @elseif($client->contract_status === 'hampir_habis')

                                        <span class="text-xs text-yellow-600 font-semibold">

                                            Hampir Habis

                                        </span>

                                        @else

                                        <span class="text-xs text-red-600 font-semibold">

                                            Kontrak Selesai

                                        </span>

                                        @endif

                                    </div>

                                    @else

                                    <span class="text-gray-400">

                                        Belum ada kontrak

                                    </span>

                                    @endif

                                </td>

                                {{-- Telepon --}}
                                <td class="px-4 py-4 text-gray-700">

                                    {{ $client->phone }}

                                </td>

                                {{-- Status client --}}
                                <td class="px-4 py-4">

                                    @if($client->status === 'aktif')

                                    <span class="bg-green-100 text-green-700 text-xs px-2.5 py-1 rounded-full font-medium">

                                        Aktif

                                    </span>

                                    @else

                                    <span class="bg-gray-100 text-gray-500 text-xs px-2.5 py-1 rounded-full font-medium">

                                        Nonaktif

                                    </span>

                                    @endif

                                </td>

                                {{-- Approval --}}
                                @if(auth()->user()->role !== 'auditor')

                                <td class="px-4 py-4">

                                    @if($client->approval_status === 'pending')

                                    <span class="bg-yellow-100 text-yellow-700 text-xs px-2.5 py-1 rounded-full font-medium">

                                        Pending

                                    </span>

                                    @elseif($client->approval_status === 'disetujui')

                                    <span class="bg-green-100 text-green-700 text-xs px-2.5 py-1 rounded-full font-medium">

                                        Disetujui

                                    </span>

                                    @elseif($client->approval_status === 'revisi')

                                    <span class="bg-red-100 text-red-700 text-xs px-2.5 py-1 rounded-full font-medium">

                                        Revisi

                                    </span>

                                    @endif

                                </td>

                                @endif

                                {{-- Aksi --}}
                                <td class="px-4 py-4">

                                    <div class="flex items-center justify-center gap-2 flex-wrap">

                                        {{-- Detail --}}
                                        <a href="{{ route('clients.show', $client) }}"
                                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">

                                            Detail

                                        </a>

                                        {{-- Admin --}}
                                        @if(auth()->user()->role === 'admin')

                                        <a href="{{ route('clients.edit', $client) }}"
                                            class="bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">

                                            Edit

                                        </a>

                                        <form id="delete-form-{{ $client->id }}"
                                            action="{{ route('clients.destroy', $client) }}"
                                            method="POST">

                                            @csrf
                                            @method('DELETE')

                                            <button type="button"
                                                onclick="confirmDelete('{{ $client->id }}')"
                                                class="bg-red-50 hover:bg-red-100 text-red-700 text-xs px-3 py-1.5 rounded-lg font-semibold transition">

                                                Hapus

                                            </button>

                                        </form>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td colspan="{{ auth()->user()->role === 'auditor' ? 8 : 9 }}"
                                    class="py-16 text-center text-gray-400">

                                    Belum ada data klien

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                {{-- Pagination --}}
                @if($clients->hasPages())

                <div class="px-4 py-4 border-t border-gray-100">

                    {{ $clients->withQueryString()->links() }}

                </div>

                @endif

            </div>

        </div>

    </div>

    {{-- Delete confirm --}}
    <script>
        function confirmDelete(id) {
            if (confirm('Hapus data klien ini?')) {

                document
                    .getElementById('delete-form-' + id)
                    .submit();
            }
        }
    </script>

    {{-- Search debounce --}}
    <script>
        let timeout = null;

        document
            .getElementById('searchInput')
            .addEventListener('keyup', function() {

                clearTimeout(timeout);

                timeout = setTimeout(() => {

                    this.form.submit();

                }, 500);

            });
    </script>

</x-app-layout>