{{-- resources/views/clients/show.blade.php --}}

<x-app-layout title="Detail Klien">

    <div class="py-10">

        <div class="max-w-6xl mx-auto px-4">

            {{-- HEADER --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

                <div>

                    <h1 class="text-3xl font-bold text-gray-800">
                        Detail Klien
                    </h1>

                    <p class="text-gray-500 mt-1">
                        Informasi lengkap data klien audit.
                    </p>

                </div>

                <div class="flex items-center gap-2">

                    {{-- EDIT --}}
                    @if(in_array(auth()->user()->role, ['admin', 'auditor']))

                    <a href="{{ route('clients.edit', $client) }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition shadow-sm">

                        Edit Data

                    </a>

                    @endif

                    {{-- KEMBALI --}}
                    <a href="{{ route('clients.index') }}"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl transition shadow-sm">

                        Kembali

                    </a>

                </div>

            </div>

            {{-- STATUS CARD --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

                {{-- STATUS --}}
                <div class="bg-white rounded-2xl shadow p-5 border border-gray-100">

                    <p class="text-sm text-gray-500 mb-2">
                        Status Klien
                    </p>

                    @if($client->status === 'aktif')

                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                        Aktif
                    </span>

                    @else

                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                        Nonaktif
                    </span>

                    @endif

                </div>

                {{-- APPROVAL --}}
                <div class="bg-white rounded-xl shadow p-5 border border-gray-100">

                    <p class="text-sm text-gray-500 mb-2">
                        Approval
                    </p>

                    @if($client->approval_status === 'pending')

                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">
                        Pending
                    </span>

                    @elseif($client->approval_status === 'disetujui')

                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                        Disetujui
                    </span>

                    @else

                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                        Revisi
                    </span>

                    @endif

                </div>

                {{-- DIBUAT OLEH --}}
                <div class="bg-white rounded-2xl shadow p-5 border border-gray-100">

                    <p class="text-sm text-gray-500 mb-2">
                        Dibuat Oleh
                    </p>

                    <h3 class="text-lg font-semibold text-purple-800">
                        {{ $client->user->name ?? 'Tidak diketahui' }}
                    </h3>

                </div>

            </div>

            {{-- DETAIL --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- INFORMASI UTAMA --}}
                <div class="lg:col-span-2">

                    <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">

                        <div class="px-6 py-5 border-b bg-gray-50">

                            <h2 class="text-lg font-semibold text-gray-800">
                                Informasi Instansi
                            </h2>

                        </div>

                        <div class="p-6 space-y-6">

                            {{-- NAMA INSTANSI --}}
                            <div>

                                <p class="text-sm text-gray-500 mb-1">
                                    Nama Instansi
                                </p>

                                <h3 class="text-xl font-bold text-gray-800">
                                    {{ $client->institution_name }}
                                </h3>

                            </div>

                            {{-- GRID --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <div>

                                    <p class="text-sm text-gray-500 mb-1">
                                        Jenis Instansi
                                    </p>

                                    <h3 class="font-semibold text-gray-800">
                                        {{ $client->institution_type }}
                                    </h3>

                                </div>

                                <div>

                                    <p class="text-sm text-gray-500 mb-1">
                                        Pimpinan Instansi
                                    </p>

                                    <h3 class="font-semibold text-gray-800">
                                        {{ $client->leader_name }}
                                    </h3>

                                </div>

                                <div>

                                    <p class="text-sm text-gray-500 mb-1">
                                        Telepon
                                    </p>

                                    <h3 class="font-semibold text-gray-800">
                                        {{ $client->phone }}
                                    </h3>

                                </div>

                                <div>

                                    <p class="text-sm text-gray-500 mb-1">
                                        Email
                                    </p>

                                    <h3 class="font-semibold text-gray-800">
                                        {{ $client->email ?? '-' }}
                                    </h3>

                                </div>

                                <div>

                                    <p class="text-sm text-gray-500 mb-1">
                                        NPWP
                                    </p>

                                    <h3 class="font-semibold text-gray-800">
                                        {{ $client->npwp ?? '-' }}
                                    </h3>

                                </div>

                                <div>

                                    <p class="text-sm text-gray-500 mb-1">
                                        Kota / Provinsi
                                    </p>

                                    <h3 class="font-semibold text-gray-800">
                                        {{ $client->city }},
                                        {{ $client->province }}
                                    </h3>

                                </div>

                            </div>

                            {{-- ALAMAT --}}
                            <div>

                                <p class="text-sm text-gray-500 mb-1">
                                    Alamat
                                </p>

                                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 text-gray-700 leading-relaxed">

                                    {{ $client->address }}

                                </div>

                            </div>

                            {{-- DESKRIPSI --}}
                            <div>

                                <p class="text-sm text-gray-500 mb-1">
                                    Deskripsi
                                </p>

                                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 text-gray-700 leading-relaxed">

                                    {{ $client->description ?? '-' }}

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- SIDEBAR --}}
                <div class="space-y-6">

                    {{-- INFORMASI SYSTEM --}}
                    <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">

                        <div class="px-6 py-5 border-b bg-gray-50">

                            <h2 class="text-lg font-semibold text-gray-800">
                                Informasi Sistem
                            </h2>

                        </div>

                        <div class="p-6 space-y-5">

                            <div>

                                <p class="text-sm text-gray-500 mb-1">
                                    Dibuat Pada
                                </p>

                                <h3 class="font-semibold text-gray-800">
                                    {{ $client->created_at->format('d M Y H:i') }}
                                </h3>

                            </div>

                            <div>

                                <p class="text-sm text-gray-500 mb-1">
                                    Update Terakhir
                                </p>

                                <h3 class="font-semibold text-gray-800">
                                    {{ $client->updated_at->format('d M Y H:i') }}
                                </h3>

                            </div>

                            <div>

                                <p class="text-sm text-gray-500 mb-1">
                                    Disetujui Oleh
                                </p>

                                <h3 class="font-semibold text-gray-800">
                                    {{ optional($client->approver)->name ?? '-' }}
                                </h3>

                            </div>

                            <div>

                                <p class="text-sm text-gray-500 mb-1">
                                    Tanggal Approval
                                </p>

                                <h3 class="font-semibold text-gray-800">

                                    @if($client->approved_at)

                                    {{ \Carbon\Carbon::parse($client->approved_at)->format('d M Y H:i') }}

                                    @else

                                    -

                                    @endif

                                </h3>

                            </div>

                        </div>

                    </div>

                    {{-- CATATAN REVISI --}}
                    @if($client->revision_note)

                    <div class="bg-red-50 border border-red-200 rounded-2xl p-6">

                        <h2 class="text-lg font-semibold text-red-700 mb-3">
                            Catatan Revisi
                        </h2>

                        <p class="text-red-600 leading-relaxed">
                            {{ $client->revision_note }}
                        </p>

                    </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

</x-app-layout>