<x-app-layout title="Absensi">
    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">Absensi</h2>
                    <p class="text-gray-600 mt-1">Kelola data kehadiran</p>
                </div>
                <div class="flex gap-2">
                    <form action="{{ route('attendances.check_in') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition">
                            ✓ Check In
                        </button>
                    </form>
                    <form action="{{ route('attendances.check_out') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition">
                            ✗ Check Out
                        </button>
                    </form>
                </div>
            </div>

            <!-- Filter -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <form method="GET" class="flex gap-2">
                    <input type="month" name="month" value="{{ $month }}" class="px-4 py-2 border rounded-lg">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Filter</button>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-bold">Tanggal</th>
                            <th class="px-6 py-3 text-left text-sm font-bold">Check In</th>
                            <th class="px-6 py-3 text-left text-sm font-bold">Check Out</th>
                            <th class="px-6 py-3 text-left text-sm font-bold">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-bold">Keterangan</th>
                            <th class="px-6 py-3 text-center text-sm font-bold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($attendances as $attendance)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">{{ $attendance->date->format('d M Y') }}</td>
                            <td class="px-6 py-3">{{ $attendance->check_in ?? '-' }}</td>
                            <td class="px-6 py-3">{{ $attendance->check_out ?? '-' }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-bold
                                        {{ $attendance->status === 'hadir' ? 'bg-green-100 text-green-800' :
                                           ($attendance->status === 'sakit' ? 'bg-yellow-100 text-yellow-800' :
                                            ($attendance->status === 'izin' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm">{{ $attendance->notes }}</td>
                            <td class="px-6 py-3 text-center">
                                <a href="{{ route('attendances.edit', $attendance) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada data absensi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $attendances->links() }}
            </div>
        </div>
    </div>
</x-app-layout>