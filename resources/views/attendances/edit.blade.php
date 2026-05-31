<x-app-layout title="Edit Absensi">
    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white border border-gray-200/80 rounded-xl p-5 shadow-sm">
                <h2 class="text-lg font-bold mb-6">Edit Absensi</h2>

                <form action="{{ route('attendances.update', $attendance) }}" method="POST" class="space-y-4">
                    @csrf @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Tanggal</label>
                        <input type="date" value="{{ $attendance->date->toDateString() }}" disabled class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs bg-gray-100">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Check In</label>
                            <input type="time" name="check_in" value="{{ old('check_in', $attendance->check_in) }}" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Check Out</label>
                            <input type="time" name="check_out" value="{{ old('check_out', $attendance->check_out) }}" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1">Status</label>
                        <select name="status" required class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs">
                            <option value="hadir" {{ $attendance->status === 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="izin" {{ $attendance->status === 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="sakit" {{ $attendance->status === 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="alpha" {{ $attendance->status === 'alpha' ? 'selected' : '' }}>Alpha</option>
                        </select>
                    </div>



                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Update</button>
                        <a href="{{ route('attendances.index') }}" class="flex-1 bg-gray-300 text-center px-4 py-2 rounded-lg hover:bg-gray-400">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>