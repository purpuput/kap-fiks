{{-- resources/views/clients/edit.blade.php --}}

<x-app-layout title="Edit Klien">

    <div class="py-6 animate-fade-in-up">

        <div class="max-w-3xl mx-auto px-4">

            <div class="bg-white border border-slate-100 rounded-3xl p-8 shadow-sm">

                {{-- Header --}}
                <div class="mb-6">

                    <h2 class="text-xl font-bold text-slate-800">
                        Edit Klien
                    </h2>

                    <p class="text-xs text-slate-400 mt-1 font-semibold">
                        Perbarui data klien dan tim audit
                    </p>

                </div>

                {{-- Error --}}
                @if ($errors->any())

                <div class="mb-5 bg-rose-50 border border-rose-100 text-rose-700 px-5 py-4 rounded-2xl">

                    <ul class="list-disc pl-5 text-xs font-semibold space-y-1">

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

                        <label class="block mb-2 font-bold text-slate-700 text-sm">
                            Jenis Instansi
                        </label>

                        <select name="institution_type"
                            required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-slate-700">

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

                        <label class="block mb-2 font-bold text-slate-700 text-sm">
                            Jenis Audit
                        </label>

                        <select name="audit_type"
                            required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-slate-700">

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

                        <label class="block mb-2 font-bold text-slate-700 text-sm">
                            Nama Instansi
                        </label>

                        <input type="text"
                            name="institution_name"
                            value="{{ old('institution_name', $client->institution_name) }}"
                            required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-slate-700">

                    </div>

                    {{-- Pimpinan --}}
                    <div>

                        <label class="block mb-2 font-bold text-slate-700 text-sm">
                            Pimpinan Instansi
                        </label>

                        <input type="text"
                            name="leader_name"
                            value="{{ old('leader_name', $client->leader_name) }}"
                            required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-slate-700">

                    </div>

                    {{-- Telepon --}}
                    <div>
                        <label class="block mb-2 font-bold text-slate-700 text-sm">
                            Telepon
                        </label>
                        <input type="text"
                            name="phone"
                            value="{{ old('phone', $client->phone) }}"
                            required
                            placeholder="Contoh: 081234567890"
                            pattern="08[0-9]{8,11}"
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-slate-700">
                        <p class="text-xs text-slate-400 mt-1.5 font-semibold">Harus diawali 08, panjang 10-13 digit angka.</p>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block mb-2 font-bold text-slate-700 text-sm">
                            Email
                        </label>
                        <input type="email"
                            name="email"
                            value="{{ old('email', $client->email) }}"
                            required
                            placeholder="Contoh: info@klien.com"
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-slate-700">
                    </div>

                    {{-- NPWP --}}
                    <div>
                        <label class="block mb-2 font-bold text-slate-700 text-sm">
                            NPWP
                        </label>
                        <input type="text"
                            name="npwp"
                            value="{{ old('npwp', $client->npwp) }}"
                            required
                            placeholder="Contoh: 01.234.567.8-999.000"
                            pattern="(\d{15}|\d{16}|\d{2}\.\d{3}\.\d{3}\.\d{1}-\d{3}\.\d{3})"
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-slate-700">
                        <p class="text-xs text-slate-400 mt-1.5 font-semibold">Format: 15/16 digit angka, atau format NPWP standar (XX.XXX.XXX.X-XXX.XXX).</p>
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <label class="block mb-2 font-bold text-slate-700 text-sm">
                            Alamat
                        </label>
                        <textarea name="address"
                            rows="4"
                            required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-slate-700">{{ old('address', $client->address) }}</textarea>
                    </div>

                    {{-- Lokasi --}}
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block mb-2 font-bold text-slate-700 text-sm">
                                Provinsi
                            </label>
                            <select name="province"
                                id="province"
                                required
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-slate-700">
                                <option value="">-- Pilih Provinsi --</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 font-bold text-slate-700 text-sm">
                                Kota/Kabupaten
                            </label>
                            <select name="city"
                                id="city"
                                required
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-slate-700">
                                <option value="">-- Pilih Kota/Kabupaten --</option>
                            </select>
                        </div>

                        <div>
                            <label class="block mb-2 font-bold text-slate-700 text-sm">
                                Kode Pos
                            </label>
                            <input type="text"
                                name="postal_code"
                                value="{{ old('postal_code', $client->postal_code) }}"
                                required
                                pattern="[0-9]{5}"
                                placeholder="Contoh: 36111"
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-slate-700">
                            <p class="text-xs text-slate-400 mt-1.5 font-semibold">Harus 5 digit angka.</p>
                        </div>
                    </div>

                    {{-- Kontrak --}}
                    <div class="grid md:grid-cols-2 gap-4">

                        <div>

                            <label class="block mb-2 font-bold text-slate-700 text-sm">
                                Kontrak Mulai
                            </label>

                            <input type="date"
                                name="contract_start"
                                value="{{ old('contract_start', $client->contract_start?->format('Y-m-d')) }}"
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-slate-700">

                        </div>

                        <div>

                            <label class="block mb-2 font-bold text-slate-700 text-sm">
                                Kontrak Selesai
                            </label>

                            <input type="date"
                                name="contract_end"
                                value="{{ old('contract_end', $client->contract_end?->format('Y-m-d')) }}"
                                class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-slate-700">

                        </div>

                    </div>

                    {{-- Tim audit --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="font-bold text-slate-700 text-sm">
                                Tim Audit
                            </label>
                            <span class="text-xs text-slate-400 font-semibold">
                                Batas Maksimal: 3 Klien Aktif Per Auditor
                            </span>
                        </div>

                        <div class="space-y-3">
                            @foreach ($auditors as $auditor)
                            @php
                            $selectedAuditor = $client->auditors->contains($auditor->id);
                            $pivot = $client->auditors->firstWhere('id', $auditor->id)?->pivot;
                            $isFull = !$selectedAuditor && ($auditor->clients_count ?? 0) >= 3;
                            @endphp

                            <div class="border rounded-2xl p-4 transition-colors {{ $isFull ? 'border-rose-100 bg-rose-50/30 opacity-75' : 'border-slate-100 hover:bg-slate-50/50' }}">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox"
                                            name="auditors[]"
                                            value="{{ $auditor->id }}"
                                            {{ $selectedAuditor ? 'checked' : '' }}
                                            {{ $isFull ? 'disabled' : '' }}
                                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500/20 w-4 h-4 {{ $isFull ? 'cursor-not-allowed' : '' }}">

                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="font-bold text-slate-700 text-sm">
                                                    {{ $auditor->name }}
                                                </p>

                                                @if($isFull)
                                                <span class="bg-rose-100 text-rose-700 text-[10px] px-2 py-0.5 rounded-md font-bold">
                                                    Beban Penuh (3/3 Klien)
                                                </span>
                                                @else
                                                <span class="bg-slate-100 text-slate-600 text-[10px] px-2 py-0.5 rounded-md font-semibold">
                                                    {{ $auditor->clients_count ?? 0 }}/3 Klien Aktif
                                                </span>
                                                @endif
                                            </div>

                                            <p class="text-xs text-slate-400 font-semibold mt-0.5">
                                                {{ $auditor->email }}
                                            </p>
                                        </div>
                                    </div>

                                    <select
                                        name="team_role[{{ $auditor->id }}]"
                                        {{ $isFull ? 'disabled' : '' }}
                                        class="bg-slate-50/50 border border-slate-200 rounded-lg px-3 py-1.5 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all {{ $isFull ? 'cursor-not-allowed opacity-60' : '' }}">

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

                        <label class="block mb-2 font-bold text-slate-700 text-sm">
                            Status Klien
                        </label>

                        <select name="status"
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-slate-700">

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



                    {{-- Button --}}
                    <div class="flex gap-4 pt-2">

                        <button
                            type="submit"
                            class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-sm shadow-blue-500/10 hover:shadow-md transition">

                            Update

                        </button>

                        <a href="{{ route('clients.index') }}"
                            class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-center px-5 py-2.5 rounded-xl font-bold text-sm transition">

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

            const initialProvince = "{{ old('province', $client->province ?? '') }}".trim();
            const initialCity = "{{ old('city', $client->city ?? '') }}".trim();

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