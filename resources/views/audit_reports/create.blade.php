<x-app-layout title="Tambah Laporan Audit">
    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold mb-6">Buat Laporan Audit</h2>

                <form action="{{ route('audit_reports.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-bold mb-2">Klien</label>
                        <select name="client_id" required class="w-full px-4 py-2 border rounded-lg">
                            <option value="">Pilih Klien</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">No Laporan</label>
                        <input type="text" name="report_number" value="{{ old('report_number') }}" required placeholder="e.g. AR/2026/001" class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Judul Laporan</label>
                        <input type="text" name="title" value="{{ old('title') }}" required class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Tanggal Audit</label>
                        <input type="date" name="audit_date" value="{{ old('audit_date') }}" required class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Temuan Audit</label>
                        <textarea name="findings" required class="w-full px-4 py-2 border rounded-lg h-32">{{ old('findings') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Rekomendasi</label>
                        <textarea name="recommendations" class="w-full px-4 py-2 border rounded-lg h-32">{{ old('recommendations') }}</textarea>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Buat Laporan</button>
                        <a href="{{ route('audit_reports.index') }}" class="flex-1 bg-gray-300 text-center px-4 py-2 rounded-lg hover:bg-gray-400">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>