<x-app-layout title="Edit Absensi">
    <div class="py-6 animate-fade-in-up">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm">
                <h2 class="text-xl font-bold text-slate-800 mb-6">Edit Absensi</h2>

                <form action="{{ route('attendances.update', $attendance) }}" method="POST" class="space-y-5">
                    @csrf @method('PUT')

                    <div>
                        <label class="block text-sm font-semibold text-slate-650 mb-2">Tanggal</label>
                        <input type="date" value="{{ $attendance->date->toDateString() }}" disabled class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-100/70 text-slate-405 cursor-not-allowed">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-650 mb-2">Check In</label>
                            <input type="time" name="check_in" value="{{ old('check_in', $attendance->check_in) }}" class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-650 mb-2">Check Out</label>
                            <input type="time" name="check_out" value="{{ old('check_out', $attendance->check_out) }}" class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-650 mb-2">Status</label>
                        <select name="status" required class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all cursor-pointer">
                            <option value="hadir" {{ $attendance->status === 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="izin" {{ $attendance->status === 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="sakit" {{ $attendance->status === 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="alpha" {{ $attendance->status === 'alpha' ? 'selected' : '' }}>Alpha</option>
                        </select>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-2.5 px-4 rounded-xl text-sm transition shadow-sm shadow-blue-500/10 hover:shadow-md">Update</button>
                        <a href="{{ route('attendances.index') }}" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 px-4 rounded-xl text-sm transition text-center">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>