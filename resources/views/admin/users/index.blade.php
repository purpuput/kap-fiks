<x-app-layout title="Manajemen User">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">Manajemen User</h2>
                    <p class="text-gray-600 mt-1">Kelola data user sistem</p>
                </div>
                <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                    + Tambah User
                </a>
            </div>

            <!-- Success Message -->
            @if ($message = Session::get('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <p class="text-green-700">{{ $message }}</p>
            </div>
            @endif

            <!-- Search & Filter -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cari Nama/Email</label>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nama atau email..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter Role</label>
                            <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Semua Role</option>
                                <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="kepala_kantor" {{ $role === 'kepala_kantor' ? 'selected' : '' }}>Kepala Kantor</option>
                                <option value="auditor" {{ $role === 'auditor' ? 'selected' : '' }}>Auditor</option>
                                <option value="karyawan" {{ $role === 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                                <option value="pimpinan" {{ $role === 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                                Cari
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition duration-200 text-center">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Terdaftar</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($users as $user)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $user->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : 
                                               ($user->role === 'kepala_kantor' ? 'bg-purple-100 text-purple-800' : 
                                                ($user->role === 'auditor' ? 'bg-orange-100 text-orange-800' : 
                                                 ($user->role === 'pimpinan' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'))) }}">
                                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-900 transition duration-200">Edit</a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 transition duration-200">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data user</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-white px-6 py-4 border-t border-gray-200">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>