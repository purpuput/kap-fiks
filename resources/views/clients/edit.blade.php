{{-- resources/views/clients/edit.blade.php --}}

<x-app-layout title="Edit Klien">

    <div class="py-10">

        <div class="max-w-4xl mx-auto px-4">

            <div class="bg-white rounded-2xl shadow p-6">

                {{-- Header --}}
                <div class="mb-6">

                    <h2 class="text-2xl font-bold text-gray-800">
                        Edit Klien
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Perbarui data klien dan tim audit
                    </p>

                </div>

                {{-- Error --}}
                @if ($errors->any())

                <div class="mb-5 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-xl">

                    <ul class="list-disc pl-5 text-sm space-y-1">

                        @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

                @endif

                <form action="{{ route('clients.update', $client->id) }}"
                    method="POST"
                    class="space-y-5">

                    @csrf
                    @method('PUT')

                    {{-- Jenis instansi --}}
                    <div>

                        <label class="block mb-2 font-semibold">
                            Jenis Instansi
                        </label>

                        <select name="institution_type"
                            required
                            class="w-full border rounded-xl px-4 py-2">

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

                    {{-- Jenis audit --}}
                    <div>

                        <label class="block mb-2 font-semibold">
                            Jenis Audit
                        </label>

                        <select name="audit_type"
                            required
                            class="w-full border rounded-xl px-4 py-2">

                            <option value="">
                                -- Pilih --
                            </option>

                            <option value="keuangan"
                                {{ old('audit_type', $client->audit_type) == 'keuangan' ? 'selected' : '' }}>

                                Audit Keuangan

                            </option>

                            <option value="kepatuhan"
                                {{ old('audit_type', $client->audit_type) == 'kepatuhan' ? 'selected' : '' }}>

                                Audit Kepatuhan

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
                            value="{{ old('institution_name', $client->institution_name) }}"
                            required
                            class="w-full border rounded-xl px-4 py-2">

                    </div>

                    {{-- Pimpinan --}}
                    <div>

                        <label class="block mb-2 font-semibold">
                            Pimpinan Instansi
                        </label>

                        <input type="text"
                            name="leader_name"
                            value="{{ old('leader_name', $client->leader_name) }}"
                            required
                            class="w-full border rounded-xl px-4 py-2">

                    </div>

                    {{-- Telepon --}}
                    <div>

                        <label class="block mb-2 font-semibold">
                            Telepon
                        </label>

                        <input type="text"
                            name="phone"
                            value="{{ old('phone', $client->phone) }}"
                            required
                            class="w-full border rounded-xl px-4 py-2">

                    </div>

                    {{-- Email --}}
                    <div>

                        <label class="block mb-2 font-semibold">
                            Email
                        </label>

                        <input type="email"
                            name="email"
                            value="{{ old('email', $client->email) }}"
                            class="w-full border rounded-xl px-4 py-2">

                    </div>

                    {{-- NPWP --}}
                    <div>

                        <label class="block mb-2 font-semibold">
                            NPWP
                        </label>

                        <input type="text"
                            name="npwp"
                            value="{{ old('npwp', $client->npwp) }}"
                            class="w-full border rounded-xl px-4 py-2">

                    </div>

                    {{-- Alamat --}}
                    <div>

                        <label class="block mb-2 font-semibold">
                            Alamat
                        </label>

                        <textarea name="address"
                            rows="4"
                            required
                            class="w-full border rounded-xl px-4 py-2">{{ old('address', $client->address) }}</textarea>

                    </div>

                    {{-- Lokasi --}}
                    <div class="grid md:grid-cols-3 gap-4">

                        <div>

                            <label class="block mb-2 font-semibold">
                                Kota
                            </label>

                            <input type="text"
                                name="city"
                                value="{{ old('city', $client->city) }}"
                                required
                                class="w-full border rounded-xl px-4 py-2">

                        </div>

                        <div>

                            <label class="block mb-2 font-semibold">
                                Provinsi
                            </label>

                            <input type="text"
                                name="province"
                                value="{{ old('province', $client->province) }}"
                                required
                                class="w-full border rounded-xl px-4 py-2">

                        </div>

                        <div>

                            <label class="block mb-2 font-semibold">
                                Kode Pos
                            </label>

                            <input type="text"
                                name="postal_code"
                                value="{{ old('postal_code', $client->postal_code) }}"
                                required
                                class="w-full border rounded-xl px-4 py-2">

                        </div>

                    </div>

                    {{-- Kontrak --}}
                    <div class="grid md:grid-cols-2 gap-4">

                        <div>

                            <label class="block mb-2 font-semibold">
                                Kontrak Mulai
                            </label>

                            <input type="date"
                                name="contract_start"
                                value="{{ old('contract_start', $client->contract_start?->format('Y-m-d')) }}"
                                class="w-full border rounded-xl px-4 py-2">

                        </div>

                        <div>

                            <label class="block mb-2 font-semibold">
                                Kontrak Selesai
                            </label>

                            <input type="date"
                                name="contract_end"
                                value="{{ old('contract_end', $client->contract_end?->format('Y-m-d')) }}"
                                class="w-full border rounded-xl px-4 py-2">

                        </div>

                    </div>

                    {{-- Tim audit --}}
                    <div>

                        <label class="block mb-3 font-semibold">
                            Tim Audit
                        </label>

                        <div class="space-y-3">

                            @foreach ($auditors as $auditor)

                            @php
                            $selectedAuditor = $client->auditors->contains($auditor->id);

                            $pivot = $client->auditors
                            ->firstWhere('id', $auditor->id)?->pivot;
                            @endphp

                            <div class="border rounded-xl p-4">

                                <div class="flex items-center justify-between gap-4">

                                    <div class="flex items-center gap-3">

                                        <input type="checkbox"
                                            name="auditors[]"
                                            value="{{ $auditor->id }}"
                                            {{ $selectedAuditor ? 'checked' : '' }}
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

                                        <option value="anggota"
                                            {{ optional($pivot)->team_role == 'anggota' ? 'selected' : '' }}>

                                            Anggota

                                        </option>

                                        <option value="ketua"
                                            {{ optional($pivot)->team_role == 'ketua' ? 'selected' : '' }}>

                                            Ketua Tim

                                        </option>

                                    </select>

                                </div>

                            </div>

                            @endforeach

                        </div>

                    </div>

                    {{-- Status --}}
                    <div>

                        <label class="block mb-2 font-semibold">
                            Status Klien
                        </label>

                        <select name="status"
                            class="w-full border rounded-xl px-4 py-2">

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

                    {{-- Deskripsi --}}
                    <div>

                        <label class="block mb-2 font-semibold">
                            Deskripsi
                        </label>

                        <textarea name="description"
                            rows="3"
                            class="w-full border rounded-xl px-4 py-2">{{ old('description', $client->description) }}</textarea>

                    </div>

                    {{-- Button --}}
                    <div class="flex gap-3 pt-2">

                        <button
                            type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl">

                            Update

                        </button>

                        <a href="{{ route('clients.index') }}"
                            class="flex-1 bg-gray-300 hover:bg-gray-400 text-center px-4 py-2 rounded-xl">

                            Batal

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>