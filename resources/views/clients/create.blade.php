{{-- resources/views/clients/create.blade.php --}}

<x-app-layout title="Tambah Klien">

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4">

            <div class="bg-white rounded-lg shadow p-6">

                <h2 class="text-2xl font-bold mb-6">
                    Tambah Klien
                </h2>

                <form action="{{ route('clients.store') }}"
                    method="POST"
                    class="space-y-4">

                    @csrf

                    <div>
                        <label class="block mb-2 font-semibold">
                            Jenis Instansi
                        </label>

                        <select name="institution_type"
                            required
                            class="w-full border rounded-lg px-4 py-2">

                            <option value="">-- Pilih --</option>

                            <option value="Pemerintahan">
                                Pemerintahan
                            </option>

                            <option value="Swasta">
                                Swasta
                            </option>

                        </select>
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">
                            Nama Instansi
                        </label>

                        <input type="text"
                            name="institution_name"
                            value="{{ old('institution_name') }}"
                            required
                            class="w-full border rounded-lg px-4 py-2">
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">
                            Pimpinan Instansi
                        </label>

                        <input type="text"
                            name="leader_name"
                            value="{{ old('leader_name') }}"
                            required
                            class="w-full border rounded-lg px-4 py-2">
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">
                            Telepon
                        </label>

                        <input type="text"
                            name="phone"
                            value="{{ old('phone') }}"
                            required
                            class="w-full border rounded-lg px-4 py-2">
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">
                            Email
                        </label>

                        <input type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="w-full border rounded-lg px-4 py-2">
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">
                            NPWP
                        </label>

                        <input type="text"
                            name="npwp"
                            value="{{ old('npwp') }}"
                            class="w-full border rounded-lg px-4 py-2">
                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">
                            Alamat
                        </label>

                        <textarea name="address"
                            rows="4"
                            required
                            class="w-full border rounded-lg px-4 py-2">{{ old('address') }}</textarea>
                    </div>

                    <div class="grid md:grid-cols-3 gap-4">

                        <div>
                            <label class="block mb-2 font-semibold">
                                Kota
                            </label>

                            <input type="text"
                                name="city"
                                value="{{ old('city') }}"
                                required
                                class="w-full border rounded-lg px-4 py-2">
                        </div>

                        <div>
                            <label class="block mb-2 font-semibold">
                                Provinsi
                            </label>

                            <input type="text"
                                name="province"
                                value="{{ old('province') }}"
                                required
                                class="w-full border rounded-lg px-4 py-2">
                        </div>

                        <div>
                            <label class="block mb-2 font-semibold">
                                Kode Pos
                            </label>

                            <input type="text"
                                name="postal_code"
                                value="{{ old('postal_code') }}"
                                required
                                class="w-full border rounded-lg px-4 py-2">
                        </div>

                    </div>

                    <div>
                        <label class="block mb-2 font-semibold">
                            Deskripsi
                        </label>

                        <textarea name="description"
                            rows="3"
                            class="w-full border rounded-lg px-4 py-2">{{ old('description') }}</textarea>
                    </div>

                    <div class="flex gap-2">

                        <button
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            Simpan
                        </button>

                        <a href="{{ route('clients.index') }}"
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-center px-4 py-2 rounded-lg">
                            Batal
                        </a>

                    </div>

                </form>

            </div>

        </div>
    </div>

</x-app-layout>