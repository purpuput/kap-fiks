{{-- resources/views/clients/index.blade.php --}}

<x-app-layout title="Data Klien">

    <div class="py-10">

        <div class="max-w-7xl mx-auto px-4">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

                <div>

                    <h2 class="text-2xl font-bold text-gray-800">
                        Data Klien
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Daftar instansi dan penugasan audit
                    </p>

                </div>

                {{-- Button tambah --}}
                @if(auth()->user()->role === 'admin')

                <a href="{{ route('clients.create') }}"
                    class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition">

                    Tambah Klien

                </a>

                @endif

            </div>

            {{-- Success --}}
            @if(session('success'))

            <div class="mb-5 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-xl">

                {{ session('success') }}

            </div>

            @endif

            {{-- Search --}}
            <div class="bg-white rounded-2xl shadow p-5 mb-6">

                <form method="GET"
                    action="{{ route('clients.index') }}">

                    <div class="relative">

                        <input type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama instansi..."
                            class="w-full border border-gray-300 rounded-xl pl-4 pr-4 py-3 focus:ring-2 focus:ring-blue-300 focus:outline-none">

                    </div>

                </form>

            </div>

            {{-- Table --}}
            <div class="bg-white rounded-2xl shadow overflow-hidden">

                <div class="overflow-x-auto">

                    <table class="w-full text-sm text-left">

                        <thead class="bg-gray-100 text-gray-700">

                            <tr>

                                <th class="px-4 py-3.5">
                                    No
                                </th>

                                <th class="px-4 py-3.5">
                                    Instansi
                                </th>

                                <th class="px-4 py-3.5">
                                    Jenis Audit
                                </th>

                                <th class="px-4 py-3.5">
                                    Tim Audit
                                </th>

                                <th class="px-4 py-3.5">
                                    Kontrak
                                </th>

                                <th class="px-4 py-3.5">
                                    Status
                                </th>

                                <th class="px-4 py-3.5 text-center">
                                    Aksi
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @forelse ($clients as $client)

                            <tr class="hover:bg-gray-50 transition">

                                {{-- No --}}
                                <td class="px-4 py-4">

                                    {{ $loop->iteration + ($clients->firstItem() - 1) }}

                                </td>

                                {{-- Instansi --}}
                                <td class="px-4 py-4">

                                    <div>

                                        <h3 class="font-semibold text-gray-800">

                                            {{ $client->institution_name }}

                                        </h3>

                                        <p class="text-xs text-gray-500 mt-1">

                                            {{ $client->leader_name }}

                                        </p>

                                    </div>

                                </td>

                                {{-- Jenis audit --}}
                                <td class="px-4 py-4">

                                    @if($client->audit_type === 'keuangan')

                                    <span class="bg-blue-100 text-blue-700 text-xs px-3 py-1 rounded-full font-medium">

                                        Keuangan

                                    </span>

                                    @else

                                    <span class="bg-purple-100 text-purple-700 text-xs px-3 py-1 rounded-full font-medium">

                                        Kepatuhan

                                    </span>

                                    @endif

                                </td>

                                {{-- Tim audit --}}
                                <td class="px-4 py-4">

                                    <div class="space-y-1">

                                        @foreach ($client->auditors as $auditor)

                                        <div class="text-xs">

                                            <span class="font-medium text-gray-700">

                                                {{ $auditor->name }}

                                            </span>

                                            @if($auditor->pivot->team_role === 'ketua')

                                            <span class="text-blue-600">

                                                (Ketua)

                                            </span>

                                            @endif

                                        </div>

                                        @endforeach

                                    </div>

                                </td>

                                {{-- Kontrak --}}
                                <td class="px-4 py-4">

                                    <div class="text-xs text-gray-600 space-y-1">

                                        <div>

                                            Mulai:
                                            {{ optional($client->contract_start)->format('d M Y') ?? '-' }}

                                        </div>

                                        <div>

                                            Selesai:
                                            {{ optional($client->contract_end)->format('d M Y') ?? '-' }}

                                        </div>

                                    </div>

                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-4">

                                    <div class="flex flex-col gap-2">

                                        {{-- Approval --}}
                                        @if($client->approval_status === 'disetujui')

                                        <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full font-medium w-fit">

                                            Disetujui

                                        </span>

                                        @elseif($client->approval_status === 'revisi')

                                        <span class="bg-red-100 text-red-700 text-xs px-3 py-1 rounded-full font-medium w-fit">

                                            Revisi

                                        </span>

                                        @else

                                        <span class="bg-yellow-100 text-yellow-700 text-xs px-3 py-1 rounded-full font-medium w-fit">

                                            Pending

                                        </span>

                                        @endif

                                        {{-- Aktif --}}
                                        @if($client->status === 'aktif')

                                        <span class="bg-blue-100 text-blue-700 text-xs px-3 py-1 rounded-full font-medium w-fit">

                                            Aktif

                                        </span>

                                        @else

                                        <span class="bg-gray-200 text-gray-700 text-xs px-3 py-1 rounded-full font-medium w-fit">

                                            Nonaktif

                                        </span>

                                        @endif

                                    </div>

                                </td>

                                {{-- Aksi --}}
                                <td class="px-4 py-4">

                                    <div class="flex items-center justify-center gap-2 flex-wrap">

                                        {{-- Detail --}}
                                        <a href="{{ route('clients.show', $client->id) }}"
                                            class="px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-xs font-medium">

                                            Detail

                                        </a>

                                        {{-- Edit --}}
                                        @if(auth()->user()->role === 'admin')

                                        <a href="{{ route('clients.edit', $client->id) }}"
                                            class="px-3 py-1.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-700 rounded-lg text-xs font-medium">

                                            Edit

                                        </a>

                                        @endif

                                        {{-- Delete --}}
                                        @if(auth()->user()->role === 'admin')

                                        <form action="{{ route('clients.destroy', $client->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Hapus data klien ini?')">

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs font-medium">

                                                Hapus

                                            </button>

                                        </form>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td colspan="7"
                                    class="px-6 py-14 text-center">

                                    <div class="flex flex-col items-center">

                                        <h3 class="text-lg font-semibold text-gray-700">
                                            Data klien belum tersedia
                                        </h3>

                                        <p class="text-sm text-gray-500 mt-1">
                                            Tambahkan data klien baru untuk memulai audit
                                        </p>

                                    </div>

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

            {{-- Pagination --}}
            <div class="mt-6">

                {{ $clients->links() }}

            </div>

        </div>

    </div>

</x-app-layout>