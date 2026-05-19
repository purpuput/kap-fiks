{{-- resources/views/audit_reports/create.blade.php --}}

<x-app-layout title="Tambah Laporan Audit">

    <div class="py-10">

        <div class="max-w-5xl mx-auto px-4">

            {{-- Header --}}
            <div class="mb-6">

                <h2 class="text-2xl font-bold text-gray-800">
                    Tambah Laporan Audit
                </h2>

                <p class="text-gray-500 text-sm mt-1">
                    Pilih klien dan upload seluruh dokumen audit
                </p>

            </div>

            {{-- Error --}}
            @if ($errors->any())

            <div class="mb-6 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-xl">

                <p class="font-semibold mb-1">
                    Terdapat kesalahan:
                </p>

                <ul class="list-disc list-inside text-sm space-y-1">

                    @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

            @endif

            <form action="{{ route('audit_reports.store') }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-6">

                @csrf

                {{-- Informasi laporan --}}
                <div class="bg-white rounded-2xl shadow p-6 space-y-5">

                    <h3 class="text-lg font-bold text-gray-700 border-b pb-2">
                        Informasi Laporan
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- Client --}}
                        <div class="md:col-span-2">

                            <label class="block text-sm font-semibold text-gray-700 mb-1">

                                Klien
                                <span class="text-red-500">*</span>

                            </label>

                            <select name="client_id"
                                id="clientSelect"
                                required
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-300 focus:outline-none text-sm">

                                <option value="">
                                    -- Pilih Klien --
                                </option>

                                @foreach($clients as $client)

                                <option value="{{ $client->id }}"
                                    data-name="{{ $client->institution_name }}"
                                    data-address="{{ $client->address }}"
                                    data-pic="{{ $client->leader_name }}"
                                    data-email="{{ $client->email }}"
                                    data-phone="{{ $client->phone }}"
                                    data-team="
                                            @foreach($client->auditors as $auditor)

                                                • {{ $auditor->name }}

                                                @if($auditor->pivot->team_role === 'ketua')
                                                    (Ketua)
                                                @endif

                                                <br>

                                            @endforeach
                                        "
                                    {{ old('client_id') == $client->id ? 'selected' : '' }}>

                                    {{ $client->institution_name }}

                                </option>

                                @endforeach

                            </select>

                        </div>

                        {{-- Informasi client --}}
                        <div id="clientInfo"
                            class="hidden md:col-span-2 bg-blue-50 border border-blue-200 rounded-xl p-5">

                            <h4 class="text-sm font-bold text-blue-800 mb-4">
                                Informasi Klien
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

                                <div>

                                    <p class="font-semibold text-gray-700">
                                        Nama Instansi
                                    </p>

                                    <p id="clientName" class="text-gray-600">
                                        -
                                    </p>

                                </div>

                                <div>

                                    <p class="font-semibold text-gray-700">
                                        Pimpinan
                                    </p>

                                    <p id="clientPic" class="text-gray-600">
                                        -
                                    </p>

                                </div>

                                <div>

                                    <p class="font-semibold text-gray-700">
                                        Email
                                    </p>

                                    <p id="clientEmail" class="text-gray-600">
                                        -
                                    </p>

                                </div>

                                <div>

                                    <p class="font-semibold text-gray-700">
                                        No Telepon
                                    </p>

                                    <p id="clientPhone" class="text-gray-600">
                                        -
                                    </p>

                                </div>

                                <div class="md:col-span-2">

                                    <p class="font-semibold text-gray-700">
                                        Alamat
                                    </p>

                                    <p id="clientAddress" class="text-gray-600">
                                        -
                                    </p>

                                </div>

                            </div>

                        </div>

                        {{-- Tim audit --}}
                        <div id="clientTeam"
                            class="hidden md:col-span-2 bg-gray-50 border border-gray-200 rounded-xl p-5">

                            <h4 class="text-sm font-bold text-gray-800 mb-4">
                                Tim Audit
                            </h4>

                            <div id="teamMembers"
                                class="space-y-2 text-sm text-gray-700">

                            </div>

                        </div>

                        {{-- No laporan --}}
                        <div>

                            <label class="block text-sm font-semibold text-gray-700 mb-1">

                                No. Laporan

                            </label>

                            <input type="text"
                                value="Otomatis dibuat sistem"
                                readonly
                                class="w-full bg-gray-100 border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-500">

                        </div>

                        {{-- Auditor --}}
                        <div>

                            <label class="block text-sm font-semibold text-gray-700 mb-1">

                                Auditor

                            </label>

                            <input type="text"
                                value="{{ auth()->user()->name }}"
                                readonly
                                class="w-full bg-gray-100 border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-500">

                        </div>



                        {{-- Tanggal audit --}}
                        <div class="md:col-span-2">

                            <label class="block text-sm font-semibold text-gray-700 mb-1">

                                Tanggal Pembuatan Laporan
                                <span class="text-red-500">*</span>

                            </label>

                            <input type="date"
                                name="audit_date"
                                value="{{ old('audit_date', now()->toDateString()) }}"
                                required
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-300 focus:outline-none text-sm">

                        </div>

                    </div>

                </div>

                {{-- Upload dokumen --}}
                <div class="bg-white rounded-2xl shadow p-6">

                    <h3 class="text-lg font-bold text-gray-700 border-b pb-2 mb-5">

                        Upload Dokumen Laporan

                        <span class="text-sm font-normal text-red-500 ml-2">
                            * Semua dokumen wajib diisi
                        </span>

                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        @foreach($documentTypes as $type => $label)

                        <div class="border border-gray-200 rounded-xl p-4 hover:border-blue-300 transition">

                            <label class="block text-sm font-semibold text-gray-700 mb-2">

                                {{ $loop->iteration }}.
                                {{ $label }}

                                <span class="text-red-500">*</span>

                            </label>

                            <input type="file"
                                name="files[{{ $type }}]"

                                @if($type==='data_klien' )
                                accept=".zip,.rar,.7z,.pdf,.doc,.docx,.xls,.xlsx"
                                @else
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                @endif

                                class="w-full text-sm text-gray-600
                                    file:mr-3
                                    file:py-1.5
                                    file:px-3
                                    file:rounded-lg
                                    file:border-0
                                    file:text-xs
                                    file:font-semibold
                                    file:bg-blue-50
                                    file:text-blue-700
                                    hover:file:bg-blue-100
                                    cursor-pointer">

                            <p class="text-xs text-gray-400 mt-1">

                                @if($type === 'data_klien')

                                Format:
                                ZIP, RAR, 7Z, PDF, DOC, XLS
                                — maks. 100MB

                                @else

                                Format:
                                PDF, DOC, DOCX, XLS, XLSX, JPG, PNG
                                — maks. 50MB

                                @endif

                            </p>

                            @error("files.$type")

                            <p class="text-xs text-red-500 mt-1">
                                {{ $message }}
                            </p>

                            @enderror

                        </div>

                        @endforeach

                    </div>

                </div>

                {{-- Button --}}
                <div class="flex gap-3">

                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">

                        Simpan & Kirim Laporan

                    </button>

                    <a href="{{ route('audit_reports.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2.5 rounded-xl font-semibold text-sm transition">

                        Kembali

                    </a>

                </div>

            </form>

        </div>

    </div>

    {{-- Auto info client --}}
    <script>
        const clientSelect = document.getElementById('clientSelect');

        const clientInfo = document.getElementById('clientInfo');

        const clientTeam = document.getElementById('clientTeam');

        const teamMembers = document.getElementById('teamMembers');

        const clientName = document.getElementById('clientName');

        const clientPic = document.getElementById('clientPic');

        const clientEmail = document.getElementById('clientEmail');

        const clientPhone = document.getElementById('clientPhone');

        const clientAddress = document.getElementById('clientAddress');

        clientSelect.addEventListener('change', function() {

            const selected = this.options[this.selectedIndex];

            if (this.value !== '') {

                clientInfo.classList.remove('hidden');

                clientTeam.classList.remove('hidden');

                // Informasi client
                clientName.textContent =
                    selected.dataset.name || '-';

                clientPic.textContent =
                    selected.dataset.pic || '-';

                clientEmail.textContent =
                    selected.dataset.email || '-';

                clientPhone.textContent =
                    selected.dataset.phone || '-';

                clientAddress.textContent =
                    selected.dataset.address || '-';

                // Tim audit
                teamMembers.innerHTML =
                    selected.dataset.team || '-';

            } else {

                clientInfo.classList.add('hidden');

                clientTeam.classList.add('hidden');

                teamMembers.innerHTML = '';
            }
        });
    </script>

</x-app-layout>