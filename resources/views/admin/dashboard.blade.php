<x-app-layout title="Admin Dashboard">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Admin Dashboard</h2>
                <p class="text-gray-600">Selamat datang di Admin Dashboard</p>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-600 text-sm font-medium">Total Users</p>
                                <p class="text-3xl font-bold text-blue-900 mt-1">{{ \App\Models\User::count() }}</p>
                            </div>
                            <svg class="w-12 h-12 text-blue-200" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v2a1 1 0 001 1h6a1 1 0 001-1v-2zM16 15v2a1 1 0 001 1h2a1 1 0 001-1v-2a4 4 0 00-4 0z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-600 text-sm font-medium">Karyawan</p>
                                <p class="text-3xl font-bold text-green-900 mt-1">{{ \App\Models\User::where('role', 'karyawan')->count() }}</p>
                            </div>
                            <svg class="w-12 h-12 text-green-200" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M17 11a1 1 0 01-1-1V9.413l1.409-1.409a1 1 0 10-1.414-1.414L14.586 6.87V5a1 1 0 10-2 0v2.413l-1.409-1.409a1 1 0 00-1.414 1.414L10.414 9H9a1 1 0 100 2h1.413l1.409 1.409a1 1 0 001.414-1.414L11.413 10H13a1 1 0 011 1v1a1 1 0 011 1v2a1 1 0 01-2 0v-1H6v1a1 1 0 01-2 0v-2a1 1 0 011-1h2v-1a1 1 0 00-1-1H6.413L5 8.587a1 1 0 00-1.414 1.414L5.414 11H4a1 1 0 100 2h1.413l1.409 1.409a1 1 0 101.414-1.414L6.413 12H7a1 1 0 001 1v2a1 1 0 102 0v-1h2v1a1 1 0 102 0v-2a1 1 0 00-1-1z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-600 text-sm font-medium">Admin</p>
                                <p class="text-3xl font-bold text-purple-900 mt-1">{{ \App\Models\User::where('role', 'admin')->count() }}</p>
                            </div>
                            <svg class="w-12 h-12 text-purple-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="mt-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Aksi Cepat</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('admin.users.index') }}" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-2a6 6 0 0112 0v2h12v-2a6 6 0 01-9-5.656v-.344"></path>
                            </svg>
                            Kelola User
                        </a>
                        <a href="{{ route('admin.users.create') }}" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah User Baru
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>