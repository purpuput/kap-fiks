<x-app-layout title="Edit Laporan Audit">
    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold mb-6">Edit Laporan Audit</h2>

                <form action="{{ route('audit_reports.update', $auditReport) }}" method="POST" class="space-y-4">
                    @csrf @method('PUT')

                    <div>
                        <label class="block text-sm font-bold mb-2">No Laporan</label>
                        <input type="text" value="{{ $auditReport->report_number }}" disabled class="w-full px-4 py-2 border rounded-lg bg-gray-100">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Judul Laporan</label>
                        <input type="text" name="title" value="{{ old('title', $auditReport->title) }}" required class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Tanggal Audit</label>
                        <input type="date" name="audit_date" value="{{ old('audit_date', $auditReport->audit_date->toDateString()) }}" required class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Temuan Audit</label>
                        <textarea name="findings" required class="w-full px-4 py-2 border rounded-lg h-32">{{ old('findings', $auditReport->findings) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Rekomendasi</label>
                        <textarea name="recommendations" class="w-full px-4 py-2 border rounded-lg h-32">{{ old('recommendations', $auditReport->recommendations) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Status</label>
                        <select name="status" required class="w-full px-4 py-2 border rounded-lg">
                            <option value="draft" {{ $auditReport->status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="in_review" {{ $auditReport->status === 'in_review' ? 'selected' : '' }}>Dalam Tinjauan</option>
                            <option value="completed" {{ $auditReport->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Update</button>
                        <a href="{{ route('audit_reports.index') }}" class="flex-1 bg-gray-300 text-center px-4 py-2 rounded-lg hover:bg-gray-400">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>