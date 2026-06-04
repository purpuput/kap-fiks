{{-- resources/views/clients/show.blade.php --}}

<x-app-layout title="Detail Klien">

    <div class="py-6">

        <div class="max-w-6xl mx-auto px-4">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

                <div>

                    <h1 class="text-xl font-bold text-gray-800">
                        Detail Klien
                    </h1>

                    <p class="text-gray-500 mt-1">
                        Informasi lengkap data klien audit
                    </p>

                </div>

                <div class="flex items-center gap-2">

                    {{-- Admin --}}
                    @if(auth()->user()->role === 'admin' && $client->approval_status !== 'disetujui')

                    <a href="{{ route('clients.edit', $client) }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition shadow-sm">

                        Edit Data

                    </a>

                    @endif

                    {{-- Back --}}
                    <a href="{{ route('clients.index') }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition shadow-sm">

                        Kembali

                    </a>

                </div>

            </div>
            {{-- Status --}}
            <div class="grid grid-cols-1 {{ auth()->user()->role === 'auditor' ? 'md:grid-cols-2' : 'md:grid-cols-4' }} gap-5 mb-6">

                {{-- Status client --}}
                <div class="bg-white border border-gray-200/80 rounded-xl p-5 shadow-sm hover:shadow-md transition-all duration-300 flex items-start gap-4">
                    <div class="p-3 rounded-lg {{ $client->status === 'aktif' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500' }} shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Status Klien</p>
                        <div class="mt-1">
                            @if($client->status === 'aktif')
                            <span class="inline-flex items-center text-md font-extrabold text-emerald-700">
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center text-md font-extrabold text-rose-700">
                                Nonaktif
                            </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-1 truncate">
                            {{ $client->status === 'aktif' ? 'Klien aktif & dapat diproses' : 'Klien dinonaktifkan sementara' }}
                        </p>
                    </div>
                </div>

                {{-- Approval --}}
                @if(auth()->user()->role !== 'auditor')
                <div class="bg-white border border-gray-200/80 rounded-xl p-5 shadow-sm hover:shadow-md transition-all duration-300 flex items-start gap-4">
                    <div class="p-3 rounded-lg {{ $client->approval_status === 'disetujui' ? 'bg-emerald-50 text-emerald-600' : ($client->approval_status === 'pending' ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600') }} shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Persetujuan Pimpinan</p>
                        <div class="mt-1">
                            @if($client->approval_status === 'pending')
                            <span class="inline-flex items-center text-sm font-extrabold text-amber-700">
                                Pending
                            </span>
                            @elseif($client->approval_status === 'disetujui')
                            <span class="inline-flex items-center text-sm font-extrabold text-emerald-700">
                                Disetujui
                            </span>
                            @else
                            <span class="inline-flex items-center text-sm font-extrabold text-rose-700">
                                Revisi
                            </span>
                            @endif
                        </div>
                        <p class="text-[11px] text-gray-500 mt-1 truncate" title="{{ $client->approval_status === 'disetujui' ? 'Oleh: ' . (optional($client->approver)->name ?? '-') : ($client->approval_status === 'revisi' ? $client->revision_note : 'Menunggu review pimpinan') }}">
                            @if($client->approval_status === 'disetujui')
                            Oleh: {{ optional($client->approver)->name ?? '-' }}
                            @elseif($client->approval_status === 'revisi')
                            Catatan: {{ Str::limit($client->revision_note, 20) }}
                            @else
                            Menunggu review pimpinan
                            @endif
                        </p>
                    </div>
                </div>
                @endif

                {{-- Creator --}}
                @if(auth()->user()->role !== 'auditor')
                <div class="bg-white border border-gray-200/80 rounded-xl p-5 shadow-sm hover:shadow-md transition-all duration-300 flex items-start gap-4">
                    <div class="p-3 rounded-lg bg-blue-50 text-blue-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pembuat Data</p>
                        <div class="mt-1">
                            <span class="inline-flex items-center text-sm font-extrabold text-gray-800 truncate max-w-full" title="{{ $client->creator->name ?? '-' }}">
                                {{ $client->creator->name ?? '-' }}
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-500 mt-1 truncate">
                            Daftar: {{ $client->created_at->format('d M Y') }}
                        </p>
                    </div>
                </div>
                @endif

                {{-- Kontrak --}}
                <div class="bg-white border border-gray-200/80 rounded-xl p-5 shadow-sm hover:shadow-md transition-all duration-300 flex items-start gap-4">
                    <div class="p-3 rounded-lg {{ $client->contract_status === 'aktif' ? 'bg-emerald-50 text-emerald-600' : ($client->contract_status === 'hampir_habis' ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600') }} shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Masa Kontrak</p>
                        <div class="mt-1">
                            @if($client->contract_status === 'aktif')
                            <span class="inline-flex items-center text-md font-extrabold text-emerald-700">
                                Aktif
                            </span>
                            @elseif($client->contract_status === 'hampir_habis')
                            <span class="inline-flex items-center text-md font-extrabold text-amber-700">
                                Hampir Habis
                            </span>
                            @else
                            <span class="inline-flex items-center text-md font-extrabold text-rose-700">
                                Selesai
                            </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-1 truncate">
                            @if($client->contract_end)
                                @if(in_array($client->contract_status, ['aktif', 'hampir_habis']))
                                    @php
                                        $diffDays = now()->startOfDay()->diffInDays($client->contract_end->startOfDay(), false);
                                    @endphp
                                    {{ $diffDays >= 0 ? "Sisa: $diffDays hari" : 'Habis' }}
                                @else
                                    Selesai pada {{ $client->contract_end->format('d/M/Y') }}
                                @endif
                            @else
                                Kontrak tidak diatur
                            @endif
                        </p>
                    </div>
                </div>

            </div>

            {{-- Content --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Main --}}
                <div class="lg:col-span-2">

                    <div class="bg-white border border-gray-200/80 rounded-lg shadow-sm border border-gray-100 overflow-hidden">

                        {{-- Header --}}
                        <div class="px-6 py-5 border-b bg-gray-50">

                            <h2 class="text-xl font-bold text-gray-800">
                                Informasi Instansi
                            </h2>

                        </div>

                        <div class="p-6 space-y-6">

                            {{-- Nama --}}
                            <div>

                                <p class="text-md text-gray-500 mb-1">
                                    Nama Instansi
                                </p>

                                <h3 class="text-xl font-bold text-gray-800">

                                    {{ $client->institution_name }}

                                </h3>

                            </div>

                            {{-- Grid --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <div>

                                    <p class="text-md text-gray-500 mb-1">
                                        Jenis Instansi
                                    </p>

                                    <h3 class="font-bold text-gray-800 text-lg">

                                        {{ $client->institution_type }}

                                    </h3>

                                </div>

                                {{-- Jenis audit --}}
                                <div>

                                    <p class="text-md text-gray-500 mb-1">
                                        Jenis Audit
                                    </p>

                                    @if($client->audit_type === 'keuangan')

                                    <span class="bg-blue-100 text-blue-700 text-lg px-3 py-1 rounded-full font-medium">

                                        Audit Keuangan

                                    </span>

                                    @else

                                    <span class="bg-purple-100 text-purple-700 text-lg px-3 py-1 rounded-full font-medium">

                                        Audit Kepatuhan

                                    </span>

                                    @endif

                                </div>
                                <div>

                                    <p class="text-md text-gray-500 mb-1">
                                        Pimpinan Instansi
                                    </p>

                                    <h3 class="font-bold text-gray-800 text-lg">

                                        {{ $client->leader_name }}

                                    </h3>

                                </div>

                                <div>

                                    <p class="text-md text-gray-500 mb-1">
                                        Telepon
                                    </p>

                                    <h3 class="font-bold text-gray-800 text-lg">

                                        {{ $client->phone }}

                                    </h3>

                                </div>

                                <div>

                                    <p class="text-md text-gray-500 mb-1">
                                        Email
                                    </p>

                                    <h3 class="font-bold text-gray-800 text-lg">

                                        {{ $client->email ?? '-' }}

                                    </h3>

                                </div>

                                <div>

                                    <p class="text-md text-gray-500 mb-1">
                                        NPWP
                                    </p>

                                    <h3 class="font-bold text-gray-800 text-lg">

                                        {{ $client->npwp ?? '-' }}

                                    </h3>

                                </div>

                                <div>
                                    <p class="text-md text-gray-500 mb-1">
                                        Provinsi
                                    </p>
                                    <h3 class="font-bold text-gray-800 text-lg">
                                        {{ $client->province }}
                                    </h3>
                                </div>

                                <div>
                                    <p class="text-md text-gray-500 mb-1">
                                        Kota/Kabupaten
                                    </p>
                                    <h3 class="font-bold text-gray-800 text-lg">
                                        {{ $client->city }}
                                    </h3>
                                </div>

                                <div>
                                    <p class="text-md text-gray-500 mb-1">
                                        Kode Pos
                                    </p>
                                    <h3 class="font-bold text-gray-800 text-lg">
                                        {{ $client->postal_code }}
                                    </h3>
                                </div>

                            </div>

                            {{-- Kontrak --}}
                            <div>

                                <p class="text-md text-gray-500 mb-1">
                                    Masa Kontrak Audit
                                </p>

                                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 text-gray-700">

                                    @if($client->contract_start && $client->contract_end)

                                    {{ $client->contract_start->format('d M Y') }}

                                    -

                                    {{ $client->contract_end->format('d M Y') }}

                                    @php
                                        $totalDays = $client->contract_start->startOfDay()->diffInDays($client->contract_end->startOfDay());
                                        $totalMonths = (int) round($totalDays / 30.44);
                                    @endphp
                                    ({{ $totalMonths }} Bulan)

                                    @else

                                    Belum ada kontrak

                                    @endif

                                </div>

                            </div>

                            {{-- Alamat --}}
                            <div>

                                <p class="text-md text-gray-500 mb-1">
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
                    <div class="bg-white border border-gray-200/80 rounded-xl shadow-sm border border-gray-100 overflow-hidden">

                        <div class="px-6 py-5 border-b bg-gray-50">

                            <h2 class="text-lg font-bold text-gray-800">
                                Tim Audit
                            </h2>

                        </div>

                        <div class="p-6 space-y-3">

                            @forelse($client->auditors as $auditor)

                            <div class="flex items-center justify-between">

                                <span class="font-medium text-gray-800 text-sm">

                                    {{ $auditor->name }}

                                </span>

                                @if($auditor->pivot->team_role === 'ketua')

                                <span class="bg-blue-100 text-blue-700 text-sm px-2 py-1 rounded-full font-semibold">

                                    Ketua

                                </span>

                                @else

                                <span class="bg-gray-100 text-gray-600 text-sm px-2 py-1 rounded-full">

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

                    <div class="bg-white border border-gray-200/80 rounded-xl shadow-sm border border-gray-100 overflow-hidden">

                        <div class="px-6 py-5 border-b bg-gray-50">

                            <h2 class="text-md font-bold text-gray-800">
                                Informasi Sistem
                            </h2>

                        </div>

                        <div class="p-6 space-y-5">

                            <div>

                                <p class="text-sm text-gray-500 mb-1">
                                    Dibuat Pada
                                </p>

                                <h3 class="font-bold text-gray-800 text-sm">

                                    {{ $client->created_at->format('d M Y H:i') }}

                                </h3>

                            </div>

                            <div>

                                <p class="text-sm text-gray-500 mb-1">
                                    Update Terakhir
                                </p>

                                <h3 class="font-bold text-gray-800 text-sm">

                                    {{ $client->updated_at->format('d M Y H:i') }}

                                </h3>

                            </div>

                            <div>

                                <p class="text-sm text-gray-500 mb-1">
                                    Disetujui Oleh
                                </p>

                                <h3 class="font-bold text-gray-800 text-sm">

                                    {{ optional($client->approver)->name ?? '-' }}

                                </h3>

                            </div>

                            <div>

                                <p class="text-sm text-gray-500 mb-1">
                                    Tanggal Approval
                                </p>

                                <h3 class="font-bold text-gray-800 text-sm">

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

                    <div class="bg-white border border-gray-200/80 rounded-xl shadow-sm border border-gray-100 overflow-hidden">

                        {{-- Header --}}
                        <div class="px-6 py-5 border-b bg-gray-50">

                            <h2 class="font-bold text-gray-800 text-md">
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

                                    <p class="text-md font-semibold text-yellow-700">
                                        Menunggu Review
                                    </p>

                                    <p class="text-sm text-yellow-600 mt-1">
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

                                    <p class="text-xs text-red-500 leading-relaxed">

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