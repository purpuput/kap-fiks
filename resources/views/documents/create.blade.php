<x-app-layout title="Upload Dokumen">
    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold mb-6">Upload Dokumen</h2>

                <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-bold mb-2">Nama Dokumen</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Klien (Opsional)</label>
                        <select name="client_id" class="w-full px-4 py-2 border rounded-lg">
                            <option value="">Tidak Ada</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Deskripsi</label>
                        <textarea name="description" class="w-full px-4 py-2 border rounded-lg">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Kategori</label>
                        <select name="category" required class="w-full px-4 py-2 border rounded-lg">
                            <option value="">Pilih Kategori</option>
                            <option value="audit_evidence">Bukti Audit</option>
                            <option value="client_data">Data Klien</option>
                            <option value="report">Laporan</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">File (Max 50MB)</label>
                        <input type="file" name="file" required class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Upload</button>
                        <a href="{{ route('documents.index') }}" class="flex-1 bg-gray-300 text-center px-4 py-2 rounded-lg hover:bg-gray-400">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>