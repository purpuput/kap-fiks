{{-- Form tambah client --}}

<x-app-layout title="Tambah Klien">

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4">

            <div class="bg-white rounded-xl shadow p-6">

                <h2 class="text-2xl font-bold mb-6">
                    Tambah Klien
                </h2>

                <form action="{{ route('clients.store') }}"
                    method="POST"
                    class="space-y-5">

                    @csrf

                    {{-- Jenis instansi --}}
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

                    {{-- Nama instansi --}}
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

                    {{-- Pimpinan --}}
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

                    {{-- Telepon --}}
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

                    {{-- Email --}}
                    <div>
                        <label class="block mb-2 font-semibold">
                            Email
                        </label>

                        <input type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="w-full border rounded-lg px-4 py-2">
                    </div>

                    {{-- NPWP --}}
                    <div>
                        <label class="block mb-2 font-semibold">
                            NPWP
                        </label>

                        <input type="text"
                            name="npwp"
                            value="{{ old('npwp') }}"
                            class="w-full border rounded-lg px-4 py-2">
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <label class="block mb-2 font-semibold">
                            Alamat
                        </label>

                        <textarea name="address"
                            rows="4"
                            required
                            class="w-full border rounded-lg px-4 py-2">{{ old('address') }}</textarea>
                    </div>

                    {{-- Lokasi --}}
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

                    {{-- Kontrak audit --}}
                    <div class="grid md:grid-cols-2 gap-4">

                        <div>
                            <label class="block mb-2 font-semibold">
                                Kontrak Mulai
                            </label>

                            <input type="date"
                                name="contract_start"
                                value="{{ old('contract_start') }}"
                                class="w-full border rounded-lg px-4 py-2">
                        </div>

                        <div>
                            <label class="block mb-2 font-semibold">
                                Kontrak Selesai
                            </label>

                            <input type="date"
                                name="contract_end"
                                value="{{ old('contract_end') }}"
                                class="w-full border rounded-lg px-4 py-2">
                        </div>

                    </div>

                    {{-- Tim audit --}}
                    <div>

                        <label class="block mb-3 font-semibold">
                            Tim Audit
                        </label>

                        <div class="space-y-3">

                            @foreach ($auditors as $auditor)

                            <div class="border rounded-lg p-4">

                                <div class="flex items-center justify-between gap-4">

                                    <div class="flex items-center gap-3">

                                        <input type="checkbox"
                                            name="auditors[]"
                                            value="{{ $auditor->id }}"
                                            class="rounded">

                                        <div>

                                            <p class="font-medium">
                                                {{ $auditor->name }}
                                            </p>

                                            <p class="text-sm text-gray-500">
                                                {{ $auditor->email }}
                                            </p>

                                        </div>

                                    </div>

                                    <select
                                        name="team_role[{{ $auditor->id }}]"
                                        class="border rounded-lg px-3 py-2">

                                        <option value="anggota">
                                            Anggota
                                        </option>

                                        <option value="ketua">
                                            Ketua Tim
                                        </option>

                                    </select>

                                </div>

                            </div>

                            @endforeach

                        </div>

                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="block mb-2 font-semibold">
                            Deskripsi
                        </label>

                        <textarea name="description"
                            rows="3"
                            class="w-full border rounded-lg px-4 py-2">{{ old('description') }}</textarea>
                    </div>

                    {{-- Button --}}
                    <div class="flex gap-2 pt-2">

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