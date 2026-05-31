<x-app-layout title="Edit User">
    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200/80 rounded-xl p-6 shadow-sm">
                <!-- Header -->
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Edit User</h2>
                    <p class="text-xs text-gray-500 mt-1">Ubah data user: <strong>{{ $user->name }}</strong></p>
                </div>

                <!-- Form -->
                <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-xs font-semibold text-gray-600 mb-2">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                        <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-600 mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Masukkan email" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                        <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password (Optional) -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-600 mb-2">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" id="password" name="password" placeholder="Masukkan password baru" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                        <p class="text-gray-400 text-[10px] mt-0.5">Minimal 6 karakter</p>
                        @error('password')
                        <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role" class="block text-xs font-semibold text-gray-600 mb-2">Role</label>
                        <select id="role" name="role" class="w-full px-3 py-1.5 border border-gray-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('role') border-red-500 @enderror">
                            <option value="">Pilih Role</option>
                            @foreach($roles as $role)
                            <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role)) }}</option>
                            @endforeach
                        </select>
                        @error('role')
                        <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Info -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <p class="text-xs text-gray-700">
                            <strong>Terdaftar:</strong> {{ $user->created_at->format('d M Y H:i') }}<br>
                            <strong>Terakhir Update:</strong> {{ $user->updated_at->format('d M Y H:i') }}
                        </p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-3.5 rounded-lg text-xs transition duration-200">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-1.5 px-3.5 rounded-lg text-xs transition duration-200 text-center">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>