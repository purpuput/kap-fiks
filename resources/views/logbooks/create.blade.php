<x-app-layout title="Logbook">
    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold mb-6">Tambah Logbook</h2>

                <form action="{{ route('logbooks.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-bold mb-2">Tanggal</label>
                        <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required class="w-full px-4 py-2 border rounded-lg">
                        @error('date')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Klien (Opsional)</label>
                        <select name="client_id" class="w-full px-4 py-2 border rounded-lg">
                            <option value="">Pilih Klien</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Aktivitas</label>
                        <input type="text" name="activity" value="{{ old('activity') }}" required class="w-full px-4 py-2 border rounded-lg">
                        @error('activity')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Deskripsi</label>
                        <textarea name="description" required class="w-full px-4 py-2 border rounded-lg h-32">{{ old('description') }}</textarea>
                        @error('description')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Jam Kerja</label>
                        <input type="number" name="hours" step="0.5" min="0.5" max="12" value="{{ old('hours', 1) }}" required class="w-full px-4 py-2 border rounded-lg">
                        @error('hours')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Simpan</button>
                        <a href="{{ route('logbooks.index') }}" class="flex-1 bg-gray-300 text-center px-4 py-2 rounded-lg hover:bg-gray-400">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>