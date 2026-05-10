{{-- resources/views/clients/index.blade.php --}}

<x-app-layout title="Data Klien">

    <div class="py-10">

        <div class="max-w-7xl mx-auto px-4">

            <div class="flex justify-between items-center mb-6">

                <h2 class="text-3xl font-bold">
                    Data Klien
                </h2>

                {{-- ADMIN & AUDITOR --}}
                @if(in_array(auth()->user()->role, ['auditor']))

                <a href="{{ route('clients.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">

                    + Tambah Klien

                </a>

                @endif

            </div>

            {{-- SUCCESS --}}
            @if ($message = Session::get('success'))

            <div class="mb-4 p-4 bg-green-100 border border-green-400 rounded text-green-700">

                {{ $message }}

            </div>

            @endif

            {{-- FILTER --}}
            <div class="bg-white p-4 rounded-lg shadow mb-4">

                <form method="GET"
                    class="grid md:grid-cols-4 gap-4">

                    <input type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari klien..."
                        class="border rounded-lg px-4 py-2">

                    <select name="status"
                        class="border rounded-lg px-4 py-2">

                        <option value="">
                            Semua Status
                        </option>

                        <option value="aktif"
                            {{ request('status') == 'aktif' ? 'selected' : '' }}>

                            Aktif

                        </option>

                        <option value="nonaktif"
                            {{ request('status') == 'nonaktif' ? 'selected' : '' }}>

                            Nonaktif

                        </option>

                    </select>

                    <button
                        class="bg-gray-800 hover:bg-gray-900 text-white rounded-lg px-4 py-2">

                        Filter

                    </button>

                    <a href="{{ route('clients.index') }}"
                        class="bg-gray-300 hover:bg-gray-400 text-center rounded-lg px-4 py-2">

                        Reset

                    </a>

                </form>

            </div>

            {{-- TABLE --}}
            <div class="bg-white rounded-lg shadow overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-gray-100 border-b">

                        <tr>

                            <th class="px-4 py-3 text-left">
                                No
                            </th>

                            <th class="px-4 py-3 text-left">
                                Instansi
                            </th>

                            <th class="px-4 py-3 text-left">
                                Jenis
                            </th>

                            <th class="px-4 py-3 text-left">
                                Pimpinan
                            </th>

                            <th class="px-4 py-3 text-left">
                                Telepon
                            </th>

                            <th class="px-4 py-3 text-left">
                                Approval
                            </th>

                            <th class="px-4 py-3 text-center">
                                Aksi
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y">

                        @forelse($clients as $client)

                        <tr class="hover:bg-gray-50">

                            {{-- NO --}}
                            <td class="px-4 py-3">

                                {{ $loop->iteration }}

                            </td>

                            {{-- INSTANSI --}}
                            <td class="px-4 py-3">

                                <strong>
                                    {{ $client->institution_name }}
                                </strong>

                            </td>

                            {{-- JENIS --}}
                            <td class="px-4 py-3">

                                {{ $client->institution_type }}

                            </td>

                            {{-- PIMPINAN --}}
                            <td class="px-4 py-3">

                                {{ $client->leader_name }}

                            </td>

                            {{-- TELEPON --}}
                            <td class="px-4 py-3">

                                {{ $client->phone }}

                            </td>

                            {{-- APPROVAL --}}
                            <td class="px-4 py-3">

                                @if($client->approval_status === 'pending')

                                <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs">

                                    Pending

                                </span>

                                @elseif($client->approval_status === 'disetujui')

                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">

                                    Disetujui

                                </span>

                                @elseif($client->approval_status === 'revisi')

                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs">

                                    Revisi

                                </span>

                                @endif

                            </td>

                            {{-- AKSI --}}
                            <td class="px-4 py-3 text-center">

                                <div class="flex justify-center items-center gap-2">

                                    {{-- DETAIL --}}
                                    <a href="{{ route('clients.show', $client) }}"
                                        class="text-green-600 hover:text-green-800">

                                        Detail

                                    </a>

                                    {{-- ADMIN & AUDITOR --}}
                                    @if(in_array(auth()->user()->role, ['admin', 'auditor']))

                                    {{-- EDIT --}}
                                    <a href="{{ route('clients.edit', $client) }}"
                                        class="text-blue-600 hover:text-blue-800">

                                        Edit

                                    </a>

                                    {{-- HAPUS --}}
                                    <form
                                        id="delete-form-{{ $client->id }}"
                                        action="{{ route('clients.destroy', $client) }}"
                                        method="POST">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="button"
                                            onclick="confirmDelete('{{ $client->id }}')"
                                            class="px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm transition">

                                            Hapus

                                        </button>

                                    </form>
                                    @endif

                                    {{-- PIMPINAN --}}
                                    @if(auth()->user()->role === 'pimpinan')

                                    {{-- APPROVE --}}
                                    @if($client->approval_status !== 'disetujui')

                                    <form action="{{ route('clients.approve', $client) }}"
                                        method="POST">

                                        @csrf

                                        <button
                                            class="text-green-600 hover:text-green-800">

                                            Approve

                                        </button>

                                    </form>

                                    {{-- REVISI --}}
                                    <button
                                        type="button"
                                        onclick="document.getElementById('revision-{{ $client->id }}').classList.toggle('hidden')"
                                        class="text-yellow-600 hover:text-yellow-800">

                                        Revisi

                                    </button>

                                    @endif

                                    @endif

                                </div>

                                {{-- FORM REVISI --}}
                                @if(auth()->user()->role === 'pimpinan')

                                <div id="revision-{{ $client->id }}"
                                    class="hidden mt-3">

                                    <form action="{{ route('clients.revision', $client) }}"
                                        method="POST">

                                        @csrf

                                        <textarea
                                            name="revision_note"
                                            rows="3"
                                            required
                                            placeholder="Masukkan catatan revisi..."
                                            class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>

                                        <button
                                            type="submit"
                                            class="mt-2 px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs">

                                            Kirim Revisi

                                        </button>

                                    </form>

                                </div>

                                @endif

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="6"
                                class="px-4 py-10 text-center text-gray-500">

                                Tidak ada data klien

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
            <div class="mt-4">

                {{ $clients->withQueryString()->links() }}

            </div>

        </div>

    </div>

    <script>
        function confirmDelete(id) {

            Swal.fire({

                title: 'Yakin hapus data?',

                text: 'Data klien akan dihapus permanen!',

                icon: 'warning',

                showCancelButton: true,

                confirmButtonColor: '#dc2626',

                cancelButtonColor: '#6b7280',

                confirmButtonText: 'Ya, Hapus',

                cancelButtonText: 'Batal',

                reverseButtons: true

            }).then((result) => {

                if (result.isConfirmed) {

                    document
                        .getElementById('delete-form-' + id)
                        .submit();
                }

            });

        }
    </script>
</x-app-layout>