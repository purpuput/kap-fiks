<x-app-layout title="Manajemen User">

    <div class="space-y-6 animate-fade-in-up">
        {{-- SUCCESS MESSAGE --}}
        @if ($message = Session::get('success'))
        <div class="bg-emerald-50 border border-emerald-200/50 rounded-2xl p-4 text-sm text-emerald-700 font-semibold shadow-sm">
            {{ $message }}
        </div>
        @endif

        {{-- SEARCH & FILTER ROW --}}
        <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 bg-white border border-slate-100 rounded-2xl p-4 shadow-sm">
            <form id="userFilterForm" method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
                <!-- Search -->
                <div class="relative w-full md:w-80">
                    <input type="text" id="searchInput" name="search" value="{{ $search }}"
                        placeholder="Cari nama atau email..."
                        class="w-full bg-slate-50/50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Role Filter -->
                <select name="role" onchange="this.form.submit()"
                    class="bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="auditor" {{ $role === 'auditor' ? 'selected' : '' }}>Auditor</option>
                    <option value="pimpinan" {{ $role === 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
                </select>

                <!-- Reset -->
                @if($search || $role)
                <a href="{{ route('admin.users.index') }}"
                    class="text-sm font-bold text-slate-400 hover:text-slate-650 px-2 py-2.5 transition">
                    Reset
                </a>
                @endif
            </form>

            <!-- Action Button -->
            <a href="{{ route('admin.users.create') }}"
                class="bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-sm shadow-blue-500/10 hover:shadow-md transition flex items-center justify-center gap-1.5 whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah User
            </a>
        </div>

        {{-- TABLE SECTION --}}
        <div class="bg-white border border-slate-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto relative">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50/60 text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-4 text-left">Nama</th>
                            <th class="px-5 py-4 text-left">Email</th>
                            <th class="px-5 py-4 text-left">Role</th>
                            <th class="px-5 py-4 text-left">Terdaftar</th>
                            <th class="px-5 py-4 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                        <tr class="hover:bg-slate-50/40 transition duration-150">
                            {{-- Nama --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-50 text-blue-600 border border-blue-100 rounded-xl flex items-center justify-center text-sm font-bold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="font-bold text-slate-800 text-sm">{{ $user->name }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="px-5 py-4 text-slate-600 text-sm whitespace-nowrap">
                                {{ $user->email }}
                            </td>

                            {{-- Role --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="text-xs px-2.5 py-0.5 rounded-full font-bold border 
                                    {{ $user->role === 'admin' ? 'bg-rose-50 text-rose-700 border-rose-200/50' : 
                                       ($user->role === 'auditor' ? 'bg-blue-50 text-blue-700 border-blue-200/50' : 
                                        ($user->role === 'pimpinan' ? 'bg-emerald-50 text-emerald-700 border-emerald-200/50' : 
                                         'bg-slate-50 text-slate-600 border-slate-200/50')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </td>

                            {{-- Terdaftar --}}
                            <td class="px-5 py-4 text-slate-500 whitespace-nowrap">
                                <span class="font-semibold text-slate-700">{{ $user->created_at->format('d/m/Y') }}</span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-3">
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="text-blue-600 hover:text-blue-800 font-extrabold transition text-sm">
                                        Edit
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline"
                                        onsubmit="event.preventDefault(); confirmDelete(this, 'Data user ini akan dihapus secara permanen dari sistem!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-rose-600 hover:text-rose-800 font-extrabold transition text-sm">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <h4 class="text-sm font-bold text-slate-800 mb-1">User Tidak Ditemukan</h4>
                                    <p class="text-xs text-slate-400 text-center">Belum ada data user yang sesuai.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if($users->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/30 flex items-center justify-between">
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