<x-app-layout title="Tambah Klien">
    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold mb-6">Tambah Klien</h2>

                <form action="{{ route('clients.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-bold mb-2">Nama</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Nama Perusahaan</label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required class="w-full px-4 py-2 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2">Alamat</label>
                        <textarea name="address" required class="w-full px-4 py-2 border rounded-lg">{{ old('address') }}</textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-bold mb-2">Kota</label>
                            <input type="text" name="city" value="{{ old('city') }}" required class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2">Provinsi</label>
                            <input type="text" name="province" value="{{ old('province') }}" required class="w-full px-4 py-2 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-2">Kode Pos</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code') }}" required class="w-full px-4 py-2 border rounded-lg">
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Simpan</button>
                        <a href="{{ route('clients.index') }}" class="flex-1 bg-gray-300 text-center px-4 py-2 rounded-lg hover:bg-gray-400">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>