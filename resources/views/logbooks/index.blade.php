<x-app-layout title="Logbook">
    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold">Logbook</h2>
                <a href="{{ route('logbooks.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">+ Tambah</a>
            </div>

            @if ($message = Session::get('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 rounded text-green-700">{{ $message }}</div>
            @endif

            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-bold">Tanggal</th>
                            <th class="px-4 py-2 text-left text-sm font-bold">Aktivitas</th>
                            <th class="px-4 py-2 text-left text-sm font-bold">Klien</th>
                            <th class="px-4 py-2 text-left text-sm font-bold">Jam</th>
                            <th class="px-4 py-2 text-left text-sm font-bold">Status</th>
                            <th class="px-4 py-2 text-center text-sm font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($logbooks as $logbook)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $logbook->date->format('d M Y') }}</td>
                            <td class="px-4 py-2">{{ $logbook->activity }}</td>
                            <td class="px-4 py-2">{{ $logbook->client->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $logbook->hours }} jam</td>
                            <td class="px-4 py-2"><span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">{{ ucfirst($logbook->status) }}</span></td>
                            <td class="px-4 py-2 text-center space-x-2">
                                <a href="{{ route('logbooks.edit', $logbook) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                <form action="{{ route('logbooks.destroy', $logbook) }}" method="POST" class="inline" onsubmit="return confirm('Hapus?')">
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
            <div class="mt-4">{{ $logbooks->links() }}</div>
        </div>
    </div>
</x-app-layout>