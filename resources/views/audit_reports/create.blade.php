{{-- resources/views/audit_reports/create.blade.php --}}

<x-app-layout title="Tambah Laporan Audit">

    <div class="py-6 animate-fade-in-up">

        <div class="max-w-5xl mx-auto px-4">

            {{-- Header --}}
            <div class="mb-6">

                <h2 class="text-xl font-bold text-slate-800">
                    Tambah Laporan Audit
                </h2>

                <p class="text-sm text-slate-400 mt-1.5">
                    Pilih klien dan upload seluruh dokumen audit
                </p>

            </div>

            {{-- Error --}}
            @if ($errors->any())

            <div class="mb-6 bg-rose-50 border border-rose-200/50 text-rose-700 px-4 py-3 rounded-2xl">

                <p class="font-semibold mb-1 text-sm">
                    Terdapat kesalahan:
                </p>

                <ul class="list-disc list-inside text-xs space-y-1">

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
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6">

                    <h3 class="text-md font-bold text-slate-700 border-b border-slate-100 pb-3">
                        Informasi Laporan
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- Client --}}
                        <div class="md:col-span-2">

                            <label class="block text-sm font-semibold text-slate-650 mb-2">

                                Klien
                                <span class="text-rose-500">*</span>

                            </label>

                            <select name="client_id"
                                id="clientSelect"
                                required
                                class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all cursor-pointer">

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

                            @error('client_id')
                            <p class="text-[11px] text-rose-600 mt-1.5 font-semibold">
                                {{ $message }}
                            </p>
                            @enderror

                        </div>

                        {{-- Informasi client --}}
                        <div id="clientInfo"
                            class="hidden md:col-span-2 bg-blue-50/40 border border-blue-100/50 rounded-2xl p-5">

                            <h4 class="text-sm font-bold text-blue-800 mb-4">
                                Informasi Klien
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-semibold">

                                <div>

                                    <p class="text-slate-400 mb-0.5">
                                        Nama Instansi
                                    </p>

                                    <p id="clientName" class="text-slate-700 font-medium">
                                        -
                                    </p>

                                </div>

                                <div>

                                    <p class="text-slate-400 mb-0.5">
                                        Pimpinan
                                    </p>

                                    <p id="clientPic" class="text-slate-700 font-medium">
                                        -
                                    </p>

                                </div>

                                <div>

                                    <p class="text-slate-400 mb-0.5">
                                        Email
                                    </p>

                                    <p id="clientEmail" class="text-slate-700 font-medium">
                                        -
                                    </p>

                                </div>

                                <div>

                                    <p class="text-slate-400 mb-0.5">
                                        No Telepon
                                    </p>

                                    <p id="clientPhone" class="text-slate-700 font-medium">
                                        -
                                    </p>

                                </div>

                                <div class="md:col-span-2">

                                    <p class="text-slate-400 mb-0.5">
                                        Alamat
                                    </p>

                                    <p id="clientAddress" class="text-slate-700 font-medium">
                                        -
                                    </p>

                                </div>

                            </div>

                        </div>

                        {{-- Tim audit --}}
                        <div id="clientTeam"
                            class="hidden md:col-span-2 bg-slate-50 border border-slate-100 rounded-2xl p-5">

                            <h4 class="text-sm font-bold text-slate-800 mb-4">
                                Tim Audit
                            </h4>

                            <div id="teamMembers"
                                class="space-y-2 text-sm text-slate-650 font-medium leading-relaxed">

                            </div>

                        </div>

                        {{-- No laporan --}}
                        <div>

                            <label class="block text-sm font-semibold text-slate-650 mb-2">

                                No. Laporan

                            </label>

                            <input type="text"
                                value="Otomatis dibuat sistem"
                                readonly
                                class="w-full bg-slate-100/70 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-400 cursor-not-allowed">

                        </div>

                        {{-- Auditor --}}
                        <div>

                            <label class="block text-sm font-semibold text-slate-650 mb-2">

                                Pembuat Laporan

                            </label>

                            <input type="text"
                                value="{{ auth()->user()->name }}"
                                readonly
                                class="w-full bg-slate-100/70 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-400 cursor-not-allowed">

                        </div>

                        {{-- Tanggal audit --}}
                        <div class="md:col-span-2">

                            <label class="block text-sm font-semibold text-slate-650 mb-2">

                                Tanggal Pembuatan Laporan
                                <span class="text-rose-500">*</span>

                            </label>

                            <input type="date"
                                name="audit_date"
                                value="{{ old('audit_date', now()->toDateString()) }}"
                                required
                                class="w-full px-4 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all cursor-pointer">

                            @error('audit_date')
                            <p class="text-[11px] text-rose-600 mt-1.5 font-semibold">
                                {{ $message }}
                            </p>
                            @enderror

                        </div>

                    </div>

                </div>

                {{-- Upload dokumen --}}
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm">

                    <h3 class="text-md font-bold text-slate-700 border-b border-slate-100 pb-3 mb-5 flex items-center justify-between">
                        <span>Upload Dokumen Laporan</span>
                        <span class="text-xs font-semibold text-rose-500">
                            * Semua dokumen wajib diisi
                        </span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        @foreach($documentTypes as $type => $label)

                        <div class="border border-slate-100 rounded-2xl p-4 hover:border-blue-300 transition-all hover:shadow-sm duration-150">

                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                {{ $loop->iteration }}.
                                {{ $label }}
                                <span class="text-rose-500">*</span>
                            </label>

                            <input type="file"
                                name="files[{{ $type }}]"

                                @if($type==='data_klien' )
                                accept=".zip,.rar,.tar"
                                @else
                                accept=".pdf,.doc,.docx,.xls,.xlsx"
                                @endif

                                class="w-full text-sm text-slate-650
                                    file:mr-3
                                    file:py-2
                                    file:px-4
                                    file:rounded-xl
                                    file:border-0
                                    file:text-xs
                                    file:font-bold
                                    file:bg-blue-50
                                    file:text-blue-700
                                    hover:file:bg-blue-100
                                    cursor-pointer">

                            <p class="text-[10px] text-slate-400 mt-1.5 font-medium">

                                @if($type === 'data_klien')
                                Format: ZIP, RAR, TAR — maks. 100MB
                                @else
                                Format: PDF, DOC, DOCX, XLS, XLSX — maks. 50MB
                                @endif

                            </p>

                            @error("files.$type")
                            <p class="text-[11px] text-rose-600 mt-1.5 font-semibold">
                                {{ $message }}
                            </p>
                            @enderror

                        </div>

                        @endforeach

                    </div>

                </div>

                {{-- Button --}}
                <div class="flex items-center gap-3">

                    <button type="submit"
                        class="bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition shadow-sm shadow-blue-500/10 hover:shadow-md">
                        Simpan & Kirim Laporan
                    </button>

                    <a href="{{ route('audit_reports.index') }}"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-5 py-2.5 rounded-xl font-bold text-sm transition">
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