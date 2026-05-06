<x-app-layout title="Edit Logbook">
    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold mb-6">Edit Logbook</h2>

                <form action="{{ route('logbooks.update', $logbook) }}" method="POST" class="space-y-4">
                    @csrf @method('PUT')

                    <div>
                        <label class="block text-sm font-bold mb-2">Tanggal</label>
                        <input type="date" name="date" value="{{ old('date', $logbook->date->toDateString()) }}" disabled class="w-full px-4 py-2 border rounded-lg bg-gray-100">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Klien</label>
                        <select name="client_id" class="w-full px-4 py-2 border rounded-lg">
                            <option value="">Pilih Klien</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ $logbook->client_id == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Aktivitas</label>
                        <input type="text" name="activity" value="{{ old('activity', $logbook->activity) }}" required class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Deskripsi</label>
                        <textarea name="description" required class="w-full px-4 py-2 border rounded-lg h-32">{{ old('description', $logbook->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Jam Kerja</label>
                        <input type="number" name="hours" step="0.5" min="0.5" max="12" value="{{ old('hours', $logbook->hours) }}" required class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Update</button>
                        <a href="{{ route('logbooks.index') }}" class="flex-1 bg-gray-300 text-center px-4 py-2 rounded-lg hover:bg-gray-400">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>