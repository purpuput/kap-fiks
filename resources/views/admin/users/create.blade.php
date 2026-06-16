<x-app-layout title="Tambah User">
    <div class="py-6 animate-fade-in-up">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-100 rounded-2xl p-8 shadow-sm">
                <!-- Header -->
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-slate-800">Tambah User Baru</h2>
                    <p class="text-xs text-slate-400 mt-1">Isi form di bawah untuk menambahkan user baru</p>
                </div>

                <!-- Form -->
                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-650 mb-2">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all placeholder-slate-400 @error('name') border-red-500 @enderror">
                        @error('name')
                        <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-650 mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email" class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all placeholder-slate-400 @error('email') border-red-500 @enderror">
                        @error('email')
                        <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-650 mb-2">Password</label>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all placeholder-slate-400 @error('password') border-red-500 @enderror">
                        <p class="text-slate-400 text-xs mt-1.5 font-medium">Minimal 6 karakter</p>
                        @error('password')
                        <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-650 mb-2">Konfirmasi Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Masukkan konfirmasi password" class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all placeholder-slate-400 @error('password_confirmation') border-red-500 @enderror">
                        <p class="text-slate-400 text-xs mt-1.5 font-medium">Minimal 6 karakter</p>
                        @error('password_confirmation')
                        <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-sm font-semibold text-slate-650 mb-2">Role</label>
                        <select id="role" name="role" class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all cursor-pointer @error('role') border-red-500 @enderror">
                            <option value="">Pilih Role</option>
                            @foreach($roles as $role)
                            <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                            @endforeach
                        </select>
                        @error('role')
                        <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-2.5 px-4 rounded-xl text-sm transition shadow-sm shadow-blue-500/10 hover:shadow-md">
                            Simpan
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 px-4 rounded-xl text-sm transition text-center">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>