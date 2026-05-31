<x-app-layout title="Tambah Klien">

    <div class="py-6">

        <div class="max-w-4xl mx-auto px-4">

            <div class="bg-white border border-gray-200/80 rounded-xl p-5 shadow-sm">

                <h2 class="text-lg font-bold text-gray-800 mb-6">
                    Tambah Klien
                </h2>

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
                            class="w-full border rounded-xl px-4 py-2">

                            <option value="">
                                -- Pilih --
                            </option>

                            <option value="Pemerintahan"
                                {{ old('institution_type') == 'Pemerintahan' ? 'selected' : '' }}>

                                Pemerintahan

                            </option>

                            <option value="Swasta"
                                {{ old('institution_type') == 'Swasta' ? 'selected' : '' }}>

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
                                {{ old('audit_type') == 'keuangan' ? 'selected' : '' }}>

                                Audit Keuangan

                            </option>

                            <option value="kepatuhan"
                                {{ old('audit_type') == 'kepatuhan' ? 'selected' : '' }}>

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
                            value="{{ old('institution_name') }}"
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
                            value="{{ old('leader_name') }}"
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
                            value="{{ old('phone') }}"
                            required
                            placeholder="Contoh: 081234567890"
                            pattern="08[0-9]{8,11}"
                            class="w-full border rounded-xl px-4 py-2">
                        <p class="text-xs text-gray-500 mt-1">Harus diawali 08, panjang 10-13 digit angka.</p>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block mb-2 font-semibold">
                            Email
                        </label>
                        <input type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            placeholder="Contoh: info@klien.com"
                            class="w-full border rounded-xl px-4 py-2">
                    </div>

                    {{-- NPWP --}}
                    <div>
                        <label class="block mb-2 font-semibold">
                            NPWP
                        </label>
                        <input type="text"
                            name="npwp"
                            value="{{ old('npwp') }}"
                            required
                            placeholder="Contoh: 01.234.567.8-999.000"
                            pattern="(\d{15}|\d{16}|\d{2}\.\d{3}\.\d{3}\.\d{1}-\d{3}\.\d{3})"
                            class="w-full border rounded-xl px-4 py-2">
                        <p class="text-xs text-gray-500 mt-1">Format: 15/16 digit angka, atau format NPWP standar (XX.XXX.XXX.X-XXX.XXX).</p>
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <label class="block mb-2 font-semibold">
                            Alamat
                        </label>
                        <textarea name="address"
                            rows="4"
                            required
                            class="w-full border rounded-xl px-4 py-2">{{ old('address') }}</textarea>
                    </div>

                    {{-- Lokasi --}}
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-2 font-semibold">
                                Provinsi
                            </label>
                            <select name="province"
                                id="province"
                                required
                                class="w-full border rounded-xl px-4 py-2">
                                <option value="">-- Pilih Provinsi --</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 font-semibold">
                                Kota/Kabupaten
                            </label>
                            <select name="city"
                                id="city"
                                required
                                class="w-full border rounded-xl px-4 py-2">
                                <option value="">-- Pilih Kota/Kabupaten --</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 font-semibold">
                                Kode Pos
                            </label>
                            <input type="text"
                                name="postal_code"
                                value="{{ old('postal_code') }}"
                                required
                                pattern="[0-9]{5}"
                                placeholder="Contoh: 36111"
                                class="w-full border rounded-xl px-4 py-2">
                            <p class="text-xs text-gray-500 mt-1">Harus 5 digit angka.</p>
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
                                value="{{ old('contract_start') }}"
                                class="w-full border rounded-xl px-4 py-2">
                        </div>

                        <div>
                            <label class="block mb-2 font-semibold">
                                Kontrak Selesai
                            </label>
                            <input type="date"
                                name="contract_end"
                                value="{{ old('contract_end') }}"
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
                            <div class="border rounded-xl p-4">
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

                    {{-- Button --}}
                    <div class="flex gap-3 pt-2">
                        <button
                            type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl">
                            Simpan
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const provinceSelect = document.getElementById('province');
            const citySelect = document.getElementById('city');

            // Helper to title-case names from API (e.g. "KOTA JAMBI" -> "Kota Jambi")
            function toTitleCase(str) {
                return str.replace(/\w\S*/g, function(txt){
                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                });
            }

            const initialProvince = "{{ old('province') }}".trim();
            const initialCity = "{{ old('city') }}".trim();

            // Load Provinces
            fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
                .then(response => response.json())
                .then(provinces => {
                    provinceSelect.innerHTML = '<option value="">-- Pilih Provinsi --</option>';
                    let matchedProvinceId = null;

                    provinces.forEach(p => {
                        const formattedName = toTitleCase(p.name);
                        const option = document.createElement('option');
                        option.value = formattedName;
                        option.textContent = formattedName;
                        option.dataset.id = p.id;

                        if (initialProvince && formattedName.toLowerCase() === initialProvince.toLowerCase()) {
                            option.selected = true;
                            matchedProvinceId = p.id;
                        }
                        provinceSelect.appendChild(option);
                    });

                    // Fallback if old province is not in API list
                    if (initialProvince && !matchedProvinceId) {
                        const option = document.createElement('option');
                        option.value = initialProvince;
                        option.textContent = initialProvince;
                        option.selected = true;
                        provinceSelect.appendChild(option);
                    }

                    if (matchedProvinceId) {
                        loadCities(matchedProvinceId, initialCity);
                    } else if (initialProvince && !matchedProvinceId && initialCity) {
                        citySelect.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
                        const option = document.createElement('option');
                        option.value = initialCity;
                        option.textContent = initialCity;
                        option.selected = true;
                        citySelect.appendChild(option);
                    }
                })
                .catch(error => {
                    console.error('Error fetching provinces:', error);
                    provinceSelect.innerHTML = '<option value="">-- Gagal memuat data API --</option>';
                    if (initialProvince) {
                        const opt = document.createElement('option');
                        opt.value = initialProvince;
                        opt.textContent = initialProvince;
                        opt.selected = true;
                        provinceSelect.appendChild(opt);
                    }
                    if (initialCity) {
                        citySelect.innerHTML = '<option value="">-- Gagal memuat data API --</option>';
                        const opt = document.createElement('option');
                        opt.value = initialCity;
                        opt.textContent = initialCity;
                        opt.selected = true;
                        citySelect.appendChild(opt);
                    }
                });

            // Function to load cities/regencies based on province ID
            function loadCities(provinceId, selectedCity = '') {
                citySelect.innerHTML = '<option value="">Memuat...</option>';

                fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`)
                    .then(response => response.json())
                    .then(cities => {
                        citySelect.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
                        let matchedCity = false;

                        cities.forEach(c => {
                            const formattedName = toTitleCase(c.name);
                            const option = document.createElement('option');
                            option.value = formattedName;
                            option.textContent = formattedName;

                            if (selectedCity && formattedName.toLowerCase() === selectedCity.toLowerCase()) {
                                option.selected = true;
                                matchedCity = true;
                            }
                            citySelect.appendChild(option);
                        });

                        if (selectedCity && !matchedCity) {
                            const option = document.createElement('option');
                            option.value = selectedCity;
                            option.textContent = selectedCity;
                            option.selected = true;
                            citySelect.appendChild(option);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching cities:', error);
                        citySelect.innerHTML = '<option value="">-- Gagal memuat data Kota --</option>';
                        if (selectedCity) {
                            const option = document.createElement('option');
                            option.value = selectedCity;
                            option.textContent = selectedCity;
                            option.selected = true;
                            citySelect.appendChild(option);
                        }
                    });
            }

            // Event listener for province change
            provinceSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const provinceId = selectedOption.dataset.id;

                if (provinceId) {
                    loadCities(provinceId);
                } else {
                    citySelect.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
                }
            });
        });
    </script>
</x-app-layout>