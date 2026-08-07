{{-- resources/views/clients/show.blade.php --}}

<x-app-layout title="Detail Klien">

    <div class="py-6 animate-fade-in-up">

        <div class="max-w-6xl mx-auto px-4">

            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

                <div>

                    <h1 class="text-xl font-bold text-slate-800">
                        Detail Klien
                    </h1>

                    <p class="text-xs text-slate-400 mt-1 font-semibold">
                        Informasi lengkap data klien audit
                    </p>

                </div>

                <div class="flex items-center gap-2">

                    {{-- Approve (Pimpinan) --}}
                    @if(auth()->user()->role === 'pimpinan' && $client->approval_status !== 'disetujui')
                    <form action="{{ route('clients.approve', $client) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl text-sm font-bold shadow-sm transition inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            Approve Klien
                        </button>
                    </form>
                    @endif

                    {{-- Edit Data --}}
                    @if((auth()->user()->role === 'admin' && $client->approval_status !== 'disetujui') || auth()->user()->role === 'pimpinan')
                    <a href="{{ route('clients.edit', $client) }}"
                        class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-sm font-bold shadow-sm transition">
                        Edit Data / Tim Auditor
                    </a>
                    @endif

                    {{-- Back --}}
                    <a href="{{ route('clients.index') }}"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition">
                        Kembali
                    </a>

                </div>

            </div>
            {{-- Status --}}
            <div class="grid grid-cols-1 {{ auth()->user()->role === 'auditor' ? 'md:grid-cols-2' : 'md:grid-cols-4' }} gap-5 mb-6">

                {{-- Status client --}}
                <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm hover:shadow-md transition-all duration-300 flex items-start gap-4">
                    <div class="p-3 rounded-2xl {{ $client->status === 'aktif' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-50 text-slate-500' }} shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status Klien</p>
                        <div class="mt-1">
                            @if($client->status === 'aktif')
                            <span class="inline-flex items-center text-sm font-extrabold text-emerald-700">
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center text-sm font-extrabold text-rose-700">
                                Nonaktif
                            </span>
                            @endif
                        </div>
                        <p class="text-[10px] text-slate-400 font-semibold mt-1 truncate">
                            {{ $client->status === 'aktif' ? 'Klien aktif & dapat diproses' : 'Klien dinonaktifkan sementara' }}
                        </p>
                    </div>
                </div>

                {{-- Approval --}}
                @if(auth()->user()->role !== 'auditor')
                <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm hover:shadow-md transition-all duration-300 flex items-start gap-4">
                    <div class="p-3 rounded-2xl {{ $client->approval_status === 'disetujui' ? 'bg-emerald-50 text-emerald-600' : ($client->approval_status === 'pending' ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600') }} shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Persetujuan Pimpinan</p>
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
                        <p class="text-[10px] text-slate-400 font-semibold mt-1 truncate" title="{{ $client->approval_status === 'disetujui' ? 'Oleh: ' . (optional($client->approver)->name ?? '-') : ($client->approval_status === 'revisi' ? $client->revision_note : 'Menunggu review pimpinan') }}">
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
                <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm hover:shadow-md transition-all duration-300 flex items-start gap-4">
                    <div class="p-3 rounded-2xl bg-blue-50 text-blue-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pembuat Data</p>
                        <div class="mt-1">
                            <span class="inline-flex items-center text-sm font-extrabold text-slate-800 truncate max-w-full" title="{{ $client->creator->name ?? '-' }}">
                                {{ $client->creator->name ?? '-' }}
                            </span>
                        </div>
                        <p class="text-[10px] text-slate-400 font-semibold mt-1 truncate">
                            Daftar: {{ $client->created_at->format('d M Y') }}
                        </p>
                    </div>
                </div>
                @endif

                {{-- Kontrak --}}
                <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm hover:shadow-md transition-all duration-300 flex items-start gap-4">
                    <div class="p-3 rounded-2xl {{ $client->contract_status === 'aktif' ? 'bg-emerald-50 text-emerald-600' : ($client->contract_status === 'hampir_habis' ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600') }} shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Masa Kontrak</p>
                        <div class="mt-1">
                            @if($client->contract_status === 'aktif')
                            <span class="inline-flex items-center text-sm font-extrabold text-emerald-700">
                                Aktif
                            </span>
                            @elseif($client->contract_status === 'hampir_habis')
                            <span class="inline-flex items-center text-sm font-extrabold text-amber-700">
                                Hampir Habis
                            </span>
                            @else
                            <span class="inline-flex items-center text-sm font-extrabold text-rose-700">
                                Selesai
                            </span>
                            @endif
                        </div>
                        <p class="text-[10px] text-slate-400 font-semibold mt-1 truncate">
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

                    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">

                        {{-- Header --}}
                        <div class="px-6 py-5 border-b border-slate-50 bg-slate-50/50">

                            <h2 class="text-md font-bold text-slate-800">
                                Informasi Instansi
                            </h2>

                        </div>

                        <div class="p-6 space-y-6">

                            {{-- Nama --}}
                            <div>

                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                                    Nama Instansi
                                </p>

                                <h3 class="text-lg font-bold text-slate-800">
                                    {{ $client->institution_name }}
                                </h3>

                            </div>

                            {{-- Grid --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <div>

                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                                        Jenis Instansi
                                    </p>

                                    <h3 class="font-bold text-slate-700 text-sm">
                                        {{ $client->institution_type }}
                                    </h3>

                                </div>

                                {{-- Jenis audit --}}
                                <div>

                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                                        Jenis Audit
                                    </p>

                                    @if($client->audit_type === 'keuangan')

                                    <span class="bg-blue-50 text-blue-700 text-xs px-2.5 py-0.5 rounded-full font-bold border border-blue-250/20">
                                        Audit Keuangan
                                    </span>

                                    @else

                                    <span class="bg-purple-50 text-purple-700 text-xs px-2.5 py-0.5 rounded-full font-bold border border-purple-250/20">
                                        Audit Kepatuhan
                                    </span>

                                    @endif

                                </div>
                                <div>

                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                                        Pimpinan Instansi
                                    </p>

                                    <h3 class="font-bold text-slate-700 text-sm">
                                        {{ $client->leader_name }}
                                    </h3>

                                </div>

                                <div>

                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                                        Telepon
                                    </p>

                                    <h3 class="font-bold text-slate-700 text-sm">
                                        {{ $client->phone }}
                                    </h3>

                                </div>

                                <div>

                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                                        Email
                                    </p>

                                    <h3 class="font-bold text-slate-700 text-sm">
                                        {{ $client->email ?? '-' }}
                                    </h3>

                                </div>

                                <div>

                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                                        NPWP
                                    </p>

                                    <h3 class="font-bold text-slate-700 text-sm">
                                        {{ $client->npwp ?? '-' }}
                                    </h3>

                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                                        Provinsi
                                    </p>
                                    <h3 class="font-bold text-slate-700 text-sm">
                                        {{ $client->province }}
                                    </h3>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                                        Kota/Kabupaten
                                    </p>
                                    <h3 class="font-bold text-slate-700 text-sm">
                                        {{ $client->city }}
                                    </h3>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                                        Kode Pos
                                    </p>
                                    <h3 class="font-bold text-slate-700 text-sm">
                                        {{ $client->postal_code }}
                                    </h3>
                                </div>

                            </div>

                            {{-- Kontrak --}}
                            <div>

                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                                    Masa Kontrak Audit
                                </p>

                                <div class="bg-slate-50/50 border border-slate-100 rounded-2xl p-4 text-xs font-semibold text-slate-650 leading-relaxed">

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

                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                                    Alamat
                                </p>

                                <div class="bg-slate-50/50 border border-slate-100 rounded-2xl p-4 text-xs font-semibold text-slate-650 leading-relaxed">

                                    {{ $client->address }}

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">

                    {{-- Tim audit --}}
                    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">

                        <div class="px-6 py-5 border-b border-slate-50 bg-slate-50/50 flex items-center justify-between">
                            <h2 class="text-md font-bold text-slate-800">
                                Tim Audit
                            </h2>
                            @if(in_array(auth()->user()->role, ['admin', 'pimpinan']))
                            <a href="{{ route('clients.edit', $client) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-lg text-xs font-bold transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Edit Tim Auditor
                            </a>
                            @endif
                        </div>

                        <div class="p-6 space-y-3">

                            @forelse($client->auditors as $auditor)

                            <div class="flex items-center justify-between py-1 border-b border-slate-50 last:border-b-0">

                                <span class="font-bold text-slate-700 text-xs">

                                    {{ $auditor->name }}

                                </span>

                                @if($auditor->pivot->team_role === 'ketua')

                                <span class="bg-blue-50 text-blue-700 text-[10px] px-2 py-0.5 rounded-full font-bold border border-blue-250/20">

                                    Ketua

                                </span>

                                @else

                                <span class="bg-slate-50 text-slate-600 text-[10px] px-2 py-0.5 rounded-full font-bold border border-slate-100">

                                    Anggota

                                </span>

                                @endif

                            </div>

                            @empty

                            <p class="text-xs text-slate-400 font-semibold">
                                Belum ada auditor
                            </p>

                            @endforelse

                        </div>

                    </div>

                    {{-- Informasi sistem --}}
                    @if(auth()->user()->role !== 'auditor')

                    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">

                        <div class="px-6 py-5 border-b border-slate-50 bg-slate-50/50">

                            <h2 class="text-md font-bold text-slate-800">
                                Informasi Sistem
                            </h2>

                        </div>

                        <div class="p-6 space-y-5">

                            <div>

                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">
                                    Dibuat Pada
                                </p>

                                <h3 class="font-bold text-slate-700 text-xs">

                                    {{ $client->created_at->format('d M Y H:i') }}

                                </h3>

                            </div>

                            <div>

                                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">
                                    Update Terakhir
                                </p>

                                <h3 class="font-bold text-slate-700 text-xs">

                                    {{ $client->updated_at->format('d M Y H:i') }}

                                </h3>

                            </div>

                        </div>

                    </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

</x-app-layout>