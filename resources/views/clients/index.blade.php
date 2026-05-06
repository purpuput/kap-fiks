<x-app-layout title="Clients">
    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold">Data Klien</h2>
                <a href="{{ route('clients.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">+ Tambah</a>
            </div>

            @if ($message = Session::get('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 rounded text-green-700">{{ $message }}</div>
            @endif

            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-bold">Nama</th>
                            <th class="px-4 py-2 text-left text-sm font-bold">Email</th>
                            <th class="px-4 py-2 text-left text-sm font-bold">Telp</th>
                            <th class="px-4 py-2 text-left text-sm font-bold">Kota</th>
                            <th class="px-4 py-2 text-left text-sm font-bold">Status</th>
                            <th class="px-4 py-2 text-center text-sm font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($clients as $client)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2"><strong>{{ $client->name }}</strong></td>
                            <td class="px-4 py-2">{{ $client->email }}</td>
                            <td class="px-4 py-2">{{ $client->phone }}</td>
                            <td class="px-4 py-2">{{ $client->city }}</td>
                            <td class="px-4 py-2"><span class="px-2 py-1 text-xs rounded {{ $client->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ ucfirst($client->status) }}</span></td>
                            <td class="px-4 py-2 text-center space-x-2">
                                <a href="{{ route('clients.edit', $client) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline" onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $clients->links() }}</div>
        </div>
    </div>
</x-app-layout>