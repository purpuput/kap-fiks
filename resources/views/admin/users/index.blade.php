<x-app-layout title="Manajemen User">

    <div class="space-y-6">
        {{-- SUCCESS MESSAGE --}}
        @if ($message = Session::get('success'))
        <div class="bg-emerald-50 border border-emerald-200/50 rounded-xl p-3.5 text-xs text-emerald-700 font-medium">
            {{ $message }}
        </div>
        @endif

        {{-- SEARCH & FILTER ROW --}}
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 bg-white border border-gray-200/80 rounded-xl p-3 shadow-sm">
            <form id="userFilterForm" method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-2 flex-1">
                <!-- Search -->
                <div class="relative w-full md:w-72">
                    <input type="text" id="searchInput" name="search" value="{{ $search }}"
                        placeholder="Cari nama atau email..."
                        class="w-full bg-slate-50/50 border border-gray-200 rounded-lg pl-8 pr-3 py-1.5 text-xs placeholder-gray-400 focus:bg-white focus:ring-1 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all">
                    <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Role Filter -->
                <select name="role" onchange="this.form.submit()"
                    class="bg-slate-50/50 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-gray-600 focus:bg-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="auditor" {{ $role === 'auditor' ? 'selected' : '' }}>Auditor</option>
                    <option value="pimpinan" {{ $role === 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
                </select>

                <!-- Reset -->
                @if($search || $role)
                <a href="{{ route('admin.users.index') }}"
                    class="text-xs font-bold text-gray-400 hover:text-gray-600 px-1 py-1.5 transition">
                    Reset
                </a>
                @endif
            </form>

            <!-- Action Button -->
            <a href="{{ route('admin.users.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-1.5 rounded-lg text-xs font-bold shadow-sm transition flex items-center justify-center gap-1 whitespace-nowrap">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah User
            </a>
        </div>

        {{-- TABLE SECTION --}}
        <div class="bg-white border border-gray-200/80 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto relative">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50/60 text-gray-400 font-bold uppercase tracking-wider sticky top-0 z-10 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Role</th>
                            <th class="px-4 py-3 text-left">Terdaftar</th>
                            <th class="px-4 py-3 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                        <tr class="hover:bg-slate-50/30 transition group">
                            {{-- Nama --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-7 h-7 bg-blue-50 text-blue-600 border border-blue-100 rounded-lg flex items-center justify-center text-xs font-bold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-2.5">
                                        <p class="font-bold text-gray-800 text-[13px]">{{ $user->name }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="px-4 py-3 text-gray-600 text-xs whitespace-nowrap">
                                {{ $user->email }}
                            </td>

                            {{-- Role --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="text-[11px] px-2 py-0.5 rounded font-bold border 
                                    {{ $user->role === 'admin' ? 'bg-rose-50 text-rose-700 border-rose-200/50' : 
                                       ($user->role === 'auditor' ? 'bg-blue-50 text-blue-700 border-blue-200/50' : 
                                        ($user->role === 'pimpinan' ? 'bg-emerald-50 text-emerald-700 border-emerald-200/50' : 
                                         'bg-gray-50 text-gray-600 border-gray-200/50')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>

                            {{-- Terdaftar --}}
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">
                                <span class="font-semibold text-gray-700">{{ $user->created_at->format('d/m/Y') }}</span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-3">
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="text-blue-600 hover:text-blue-700 font-bold hover:underline transition">
                                        Edit
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline"
                                        onsubmit="event.preventDefault(); confirmDelete(this, 'Data user ini akan dihapus secara permanen dari sistem!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-rose-600 hover:text-rose-700 font-bold hover:underline transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <h4 class="text-xs font-bold text-gray-800 mb-0.5">User Tidak Ditemukan</h4>
                                    <p class="text-[11px] text-gray-400 text-center">Belum ada data user yang sesuai.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if($users->hasPages())
            <div class="px-4 py-3 border-t border-gray-100 bg-slate-50/30 flex items-center justify-between">
                {{ $users->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const form = document.getElementById('userFilterForm');
            let timeout = null;

            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    form.submit();
                }, 500);
            });

            // Keep cursor at the end of search input after page load/submit
            if (searchInput.value.length > 0) {
                const len = searchInput.value.length;
                searchInput.focus();
                searchInput.setSelectionRange(len, len);
            }
        });
    </script>
</x-app-layout>