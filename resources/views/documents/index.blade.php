<x-app-layout title="Documents">
    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold">Manajemen Dokumen</h2>
                <a href="{{ route('documents.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">+ Upload</a>
            </div>

            @if ($message = Session::get('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 rounded text-green-700">{{ $message }}</div>
            @endif

            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-bold">Nama File</th>
                            <th class="px-4 py-2 text-left text-sm font-bold">Kategori</th>
                            <th class="px-4 py-2 text-left text-sm font-bold">Ukuran</th>
                            <th class="px-4 py-2 text-left text-sm font-bold">Upload Oleh</th>
                            <th class="px-4 py-2 text-left text-sm font-bold">Tgl Upload</th>
                            <th class="px-4 py-2 text-center text-sm font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($documents as $document)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2"><strong>{{ $document->name }}</strong></td>
                            <td class="px-4 py-2"><span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ str_replace('_', ' ', ucfirst($document->category)) }}</span></td>
                            <td class="px-4 py-2">{{ number_format($document->file_size / 1024, 2) }} KB</td>
                            <td class="px-4 py-2">{{ $document->user->name }}</td>
                            <td class="px-4 py-2">{{ $document->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-2 text-center space-x-2">
                                <a href="{{ route('documents.download', $document) }}" class="text-blue-600 hover:text-blue-900">Download</a>
                                <form action="{{ route('documents.destroy', $document) }}" method="POST" class="inline"
                                    onsubmit="event.preventDefault(); confirmDelete(this, 'Berkas dokumen ini akan dihapus secara permanen!')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-center text-gray-500">Tidak ada dokumen</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $documents->links() }}</div>
        </div>
    </div>
</x-app-layout>