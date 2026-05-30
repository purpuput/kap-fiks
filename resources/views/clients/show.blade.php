{{-- resources/views/clients/show.blade.php --}}

<x-app-layout title="Detail Klien">

    <div class="py-10">

        <div class="max-w-6xl mx-auto px-4">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

                <div>

                    <h1 class="text-3xl font-bold text-gray-800">
                        Detail Klien
                    </h1>

                    <p class="text-gray-500 mt-1">
                        Informasi lengkap data klien audit
                    </p>

                </div>

                <div class="flex items-center gap-2">

                    {{-- Admin --}}
                    @if(auth()->user()->role === 'admin')

                    <a href="{{ route('clients.edit', $client) }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition shadow-sm">

                        Edit Data

                    </a>

                    @endif

                    {{-- Back --}}
                    <a href="{{ route('clients.index') }}"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-xl transition shadow-sm">

                        Kembali

                    </a>

                </div>

            </div>

            {{-- Status --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

                {{-- Status client --}}
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

                {{-- Approval --}}
                @if(auth()->user()->role !== 'auditor')

                <div class="bg-white rounded-2xl shadow p-5 border border-gray-100">

                    <p class="text-sm text-gray-500 mb-2">
                        Approval
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        Review pimpinan kantor
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

                @endif

                {{-- Creator --}}
                @if(auth()->user()->role !== 'auditor')

                <div class="bg-white rounded-2xl shadow p-5 border border-gray-100">

                    <p class="text-sm text-gray-500 mb-2">
                        Dibuat Oleh
                    </p>

                    <h3 class="text-lg font-semibold text-gray-800">

                        {{ $client->creator->name ?? '-' }}

                    </h3>

                </div>

                @endif

                {{-- Kontrak --}}
                <div class="bg-white rounded-2xl shadow p-5 border border-gray-100">

                    <p class="text-sm text-gray-500 mb-2">
                        Status Kontrak
                    </p>

                    <p class="text-xs text-gray-400 mt-1">
                        Masa kerja sama audit
                    </p>

                    @if($client->contract_status === 'aktif')

                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-700">

                        Aktif

                    </span>

                    @elseif($client->contract_status === 'hampir_habis')

                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700">

                        Hampir Habis

                    </span>

                    @else

                    <span class="px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700">

                        Selesai

                    </span>

                    @endif

                </div>

            </div>

            {{-- Content --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Main --}}
                <div class="lg:col-span-2">

                    <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">

                        {{-- Header --}}
                        <div class="px-6 py-5 border-b bg-gray-50">

                            <h2 class="text-lg font-semibold text-gray-800">
                                Informasi Instansi
                            </h2>

                        </div>

                        <div class="p-6 space-y-6">

                            {{-- Nama --}}
                            <div>

                                <p class="text-sm text-gray-500 mb-1">
                                    Nama Instansi
                                </p>

                                <h3 class="text-xl font-bold text-gray-800">

                                    {{ $client->institution_name }}

                                </h3>

                            </div>

                            {{-- Grid --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <div>

                                    <p class="text-sm text-gray-500 mb-1">
                                        Jenis Instansi
                                    </p>

                                    <h3 class="font-semibold text-gray-800">

                                        {{ $client->institution_type }}

                                    </h3>

                                </div>

                                {{-- Jenis audit --}}
<div>

    <p class="text-sm text-gray-500 mb-1">
        Jenis Audit
    </p>

    @if($client->audit_type === 'keuangan')

    <span class="bg-blue-100 text-blue-700 text-xs px-3 py-1 rounded-full font-medium">

        Audit Keuangan

    </span>

    @else

    <span class="bg-purple-100 text-purple-700 text-xs px-3 py-1 rounded-full font-medium">

        Audit Kepatuhan

    </span>

    @endif

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
                                        Provinsi
                                    </p>
                                    <h3 class="font-semibold text-gray-800">
                                        {{ $client->province }}
                                    </h3>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500 mb-1">
                                        Kota/Kabupaten
                                    </p>
                                    <h3 class="font-semibold text-gray-800">
                                        {{ $client->city }}
                                    </h3>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500 mb-1">
                                        Kode Pos
                                    </p>
                                    <h3 class="font-semibold text-gray-800">
                                        {{ $client->postal_code }}
                                    </h3>
                                </div>

                            </div>

                            {{-- Kontrak --}}
                            <div>

                                <p class="text-sm text-gray-500 mb-1">
                                    Masa Kontrak Audit
                                </p>

                                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 text-gray-700">

                                    @if($client->contract_start && $client->contract_end)

                                    {{ $client->contract_start->format('d M Y') }}

                                    -

                                    {{ $client->contract_end->format('d M Y') }}

                                    @else

                                    Belum ada kontrak

                                    @endif

                                </div>

                            </div>

                            {{-- Alamat --}}
                            <div>

                                <p class="text-sm text-gray-500 mb-1">
                                    Alamat
                                </p>

                                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 text-gray-700 leading-relaxed">

                                    {{ $client->address }}

                                </div>

                            </div>



                        </div>

                    </div>

                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">

                    {{-- Tim audit --}}
                    <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">

                        <div class="px-6 py-5 border-b bg-gray-50">

                            <h2 class="text-lg font-semibold text-gray-800">
                                Tim Audit
                            </h2>

                        </div>

                        <div class="p-6 space-y-3">

                            @forelse($client->auditors as $auditor)

                            <div class="flex items-center justify-between">

                                <span class="font-medium text-gray-800">

                                    {{ $auditor->name }}

                                </span>

                                @if($auditor->pivot->team_role === 'ketua')

                                <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full font-semibold">

                                    Ketua

                                </span>

                                @else

                                <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">

                                    Anggota

                                </span>

                                @endif

                            </div>

                            @empty

                            <p class="text-sm text-gray-400">
                                Belum ada auditor
                            </p>

                            @endforelse

                        </div>

                    </div>

                    {{-- Informasi sistem --}}
                    @if(auth()->user()->role !== 'auditor')

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

                    @endif

                    {{-- Review pimpinan --}}
                    @if(auth()->user()->role === 'pimpinan')

                    <div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">

                        {{-- Header --}}
                        <div class="px-6 py-5 border-b bg-gray-50">

                            <h2 class="text-lg font-semibold text-gray-800">
                                Review Pimpinan
                            </h2>

                            <p class="text-sm text-gray-500 mt-1">
                                Persetujuan data klien audit
                            </p>

                        </div>

                        <div class="p-6">

                            {{-- Pending --}}
                            @if($client->approval_status === 'pending')

                            <div class="space-y-4">

                                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">

                                    <p class="text-sm font-semibold text-yellow-700">
                                        Menunggu Review
                                    </p>

                                    <p class="text-xs text-yellow-600 mt-1">
                                        Silakan tinjau data klien sebelum approval.
                                    </p>

                                </div>

                                {{-- Action --}}
                                <div class="flex items-center gap-3">

                                    {{-- Approve --}}
                                    <form action="{{ route('clients.approve', $client) }}"
                                        method="POST">

                                        @csrf

                                        <button
                                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold transition">

                                            Approve

                                        </button>

                                    </form>

                                    {{-- Revisi --}}
                                    <button type="button"
                                        onclick="toggleRevision('{{ $client->id }}')"
                                        class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl text-sm font-semibold transition">

                                        Revisi

                                    </button>

                                </div>

                                {{-- Form revisi --}}
                                <div id="revision-form-{{ $client->id }}"
                                    class="hidden">

                                    <form action="{{ route('clients.revision', $client) }}"
                                        method="POST"
                                        class="space-y-3">

                                        @csrf

                                        <textarea
                                            name="revision_note"
                                            rows="3"
                                            required
                                            placeholder="Masukkan catatan revisi..."
                                            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm resize-none focus:ring-2 focus:ring-yellow-200 focus:outline-none"></textarea>

                                        <button type="submit"
                                            class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-2.5 rounded-xl text-sm font-semibold transition">

                                            Kirim Revisi

                                        </button>

                                    </form>

                                </div>

                            </div>

                            {{-- Revisi --}}
                            @elseif($client->approval_status === 'revisi')

                            <div class="space-y-4">

                                {{-- Status --}}
                                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">

                                    <p class="text-sm font-semibold text-yellow-700">
                                        Menunggu Perbaikan Admin
                                    </p>

                                    <p class="text-xs text-yellow-600 mt-1">
                                        Data klien telah dikembalikan untuk diperbaiki.
                                    </p>

                                </div>

                                {{-- Catatan revisi --}}
                                <div class="bg-red-50 border border-red-200 rounded-xl p-4">

                                    <p class="text-sm font-semibold text-red-700 mb-2">
                                        Catatan Revisi
                                    </p>

                                    <p class="text-sm text-red-600 leading-relaxed">

                                        {{ $client->revision_note }}

                                    </p>

                                </div>

                                {{-- Button edit --}}
                                <button type="button"
                                    onclick="toggleRevision('{{ $client->id }}')"
                                    class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition">

                                    Edit Catatan Revisi

                                </button>

                                {{-- Form edit --}}
                                <div id="revision-form-{{ $client->id }}"
                                    class="hidden">

                                    <form action="{{ route('clients.revision', $client) }}"
                                        method="POST"
                                        class="space-y-3">

                                        @csrf

                                        <textarea
                                            name="revision_note"
                                            rows="3"
                                            required
                                            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm resize-none focus:ring-2 focus:ring-blue-200 focus:outline-none">{{ $client->revision_note }}</textarea>

                                        <button type="submit"
                                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl text-sm font-semibold transition">

                                            Update Revisi

                                        </button>

                                    </form>

                                </div>

                            </div>

                            {{-- Approved --}}
                            @elseif($client->approval_status === 'disetujui')

                            <div class="bg-green-50 border border-green-200 rounded-xl p-5">

                                <div class="flex items-center gap-2">

                                    <span class="text-green-600 text-lg">
                                        ✓
                                    </span>

                                    <div>

                                        <p class="font-semibold text-green-700">
                                            Data Klien Disetujui
                                        </p>

                                        <p class="text-sm text-green-600 mt-1">
                                            Klien sudah dapat diakses auditor.
                                        </p>

                                    </div>

                                </div>

                            </div>

                            @endif

                        </div>

                    </div>

                    @endif

                    {{-- Catatan revisi admin --}}
                    @if(
                    auth()->user()->role === 'admin'
                    && $client->revision_note
                    )

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

    {{-- Toggle revisi --}}
    <script>
        function toggleRevision(id) {
            document
                .getElementById('revision-form-' + id)
                .classList
                .toggle('hidden');
        }
    </script>

</x-app-layout>