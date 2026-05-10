<x-app-layout title="Edit Klien">

    <div class="py-10">

        <div class="max-w-3xl mx-auto px-4">

            <div class="bg-white rounded-2xl shadow p-6">

                <h2 class="text-2xl font-bold mb-6">
                    Edit Klien
                </h2>

                {{-- ERROR VALIDATION --}}
                @if ($errors->any())

                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">

                    <ul class="list-disc pl-5">

                        @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

                @endif

                <form action="{{ route('clients.update', $client) }}"
                    method="POST"
                    class="space-y-5">

                    @csrf
                    @method('PUT')

                    {{-- JENIS INSTANSI --}}
                    <div>

                        <label class="block text-sm font-semibold mb-2">
                            Jenis Instansi
                        </label>

                        <select
                            name="institution_type"
                            required
                            class="w-full px-4 py-2 border rounded-xl">

                            <option value="">
                                -- Pilih --
                            </option>

                            <option value="Pemerintahan"
                                {{ old('institution_type', $client->institution_type) == 'Pemerintahan' ? 'selected' : '' }}>

                                Pemerintahan

                            </option>

                            <option value="Swasta"
                                {{ old('institution_type', $client->institution_type) == 'Swasta' ? 'selected' : '' }}>

                                Swasta

                            </option>

                        </select>

                    </div>

                    {{-- NAMA INSTANSI --}}
                    <div>

                        <label class="block text-sm font-semibold mb-2">
                            Nama Instansi
                        </label>

                        <input
                            type="text"
                            name="institution_name"
                            value="{{ old('institution_name', $client->institution_name) }}"
                            required
                            class="w-full px-4 py-2 border rounded-xl">

                    </div>

                    {{-- PIMPINAN --}}
                    <div>

                        <label class="block text-sm font-semibold mb-2">
                            Pimpinan Instansi
                        </label>

                        <input
                            type="text"
                            name="leader_name"
                            value="{{ old('leader_name', $client->leader_name) }}"
                            required
                            class="w-full px-4 py-2 border rounded-xl">

                    </div>

                    {{-- EMAIL --}}
                    <div>

                        <label class="block text-sm font-semibold mb-2">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email', $client->email) }}"
                            class="w-full px-4 py-2 border rounded-xl">

                    </div>

                    {{-- TELEPON --}}
                    <div>

                        <label class="block text-sm font-semibold mb-2">
                            Telepon
                        </label>

                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone', $client->phone) }}"
                            required
                            class="w-full px-4 py-2 border rounded-xl">

                    </div>

                    {{-- NPWP --}}
                    <div>

                        <label class="block text-sm font-semibold mb-2">
                            NPWP
                        </label>

                        <input
                            type="text"
                            name="npwp"
                            value="{{ old('npwp', $client->npwp) }}"
                            class="w-full px-4 py-2 border rounded-xl">

                    </div>

                    {{-- ALAMAT --}}
                    <div>

                        <label class="block text-sm font-semibold mb-2">
                            Alamat
                        </label>

                        <textarea
                            name="address"
                            rows="4"
                            required
                            class="w-full px-4 py-2 border rounded-xl">{{ old('address', $client->address) }}</textarea>

                    </div>

                    {{-- KOTA --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <div>

                            <label class="block text-sm font-semibold mb-2">
                                Kota
                            </label>

                            <input
                                type="text"
                                name="city"
                                value="{{ old('city', $client->city) }}"
                                required
                                class="w-full px-4 py-2 border rounded-xl">

                        </div>

                        <div>

                            <label class="block text-sm font-semibold mb-2">
                                Provinsi
                            </label>

                            <input
                                type="text"
                                name="province"
                                value="{{ old('province', $client->province) }}"
                                required
                                class="w-full px-4 py-2 border rounded-xl">

                        </div>

                        <div>

                            <label class="block text-sm font-semibold mb-2">
                                Kode Pos
                            </label>

                            <input
                                type="text"
                                name="postal_code"
                                value="{{ old('postal_code', $client->postal_code) }}"
                                required
                                class="w-full px-4 py-2 border rounded-xl">

                        </div>

                    </div>

                    {{-- STATUS --}}
                    <div>

                        <label class="block text-sm font-semibold mb-2">
                            Status
                        </label>

                        <select
                            name="status"
                            required
                            class="w-full px-4 py-2 border rounded-xl">

                            <option value="aktif"
                                {{ old('status', $client->status) == 'aktif' ? 'selected' : '' }}>

                                Aktif

                            </option>

                            <option value="nonaktif"
                                {{ old('status', $client->status) == 'nonaktif' ? 'selected' : '' }}>

                                Nonaktif

                            </option>

                        </select>

                    </div>

                    {{-- DESKRIPSI --}}
                    <div>

                        <label class="block text-sm font-semibold mb-2">
                            Deskripsi
                        </label>

                        <textarea
                            name="description"
                            rows="4"
                            class="w-full px-4 py-2 border rounded-xl">{{ old('description', $client->description) }}</textarea>

                    </div>

                    {{-- BUTTON --}}
                    <div class="flex gap-3 pt-2">

                        <button
                            type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl transition">

                            Update

                        </button>

                        <a href="{{ route('clients.index') }}"
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-center px-4 py-2 rounded-xl transition">

                            Batal

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>