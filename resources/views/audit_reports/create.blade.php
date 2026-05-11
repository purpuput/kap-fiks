<x-app-layout title="Tambah Laporan Audit">

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4">

            {{-- HEADER --}}
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Tambah Laporan Audit</h2>
                <p class="text-gray-500 text-sm mt-1">Isi data laporan dan upload seluruh 10 dokumen yang diperlukan</p>
            </div>

            {{-- ERROR --}}
            @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-xl">
                <p class="font-semibold mb-1">Terdapat kesalahan:</p>
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('audit_reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- INFORMASI LAPORAN --}}
                <div class="bg-white rounded-2xl shadow p-6 space-y-5">
                    <h3 class="text-lg font-bold text-gray-700 border-b pb-2">Informasi Laporan</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- Klien --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Klien <span class="text-red-500">*</span>
                            </label>
                            <select name="client_id" required
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-300 focus:outline-none text-sm">
                                <option value="">-- Pilih Klien --</option>
                                @foreach($clients as $client)
                                <option value="{{ $client->id }}"
                                    {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->institution_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- No Laporan --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                No. Laporan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="report_number" value="{{ old('report_number') }}"
                                placeholder="Contoh: LA/2026/001" required
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-300 focus:outline-none text-sm">
                        </div>

                        {{-- Judul --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Judul Laporan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" value="{{ old('title') }}"
                                placeholder="Masukkan judul laporan audit" required
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-300 focus:outline-none text-sm">
                        </div>

                        {{-- Jenis Audit --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Jenis Audit <span class="text-red-500">*</span>
                            </label>
                            <select name="audit_type" required
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-300 focus:outline-none text-sm">
                                <option value="">-- Pilih Jenis Audit --</option>
                                <option value="keuangan" {{ old('audit_type') == 'keuangan' ? 'selected' : '' }}>
                                    Audit Keuangan
                                </option>
                                <option value="kepatuhan" {{ old('audit_type') == 'kepatuhan' ? 'selected' : '' }}>
                                    Audit Kepatuhan
                                </option>
                            </select>
                        </div>

                        {{-- Tanggal Audit --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Tanggal Audit <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="audit_date" value="{{ old('audit_date') }}" required
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-300 focus:outline-none text-sm">
                        </div>

                        {{-- Auditor --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Auditor <span class="text-red-500">*</span>
                            </label>
                            <select name="auditors[]" multiple required
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-300 focus:outline-none text-sm h-28">
                                @foreach($auditors as $auditor)
                                <option value="{{ $auditor->id }}"
                                    {{ in_array($auditor->id, old('auditors', [])) ? 'selected' : '' }}>
                                    {{ $auditor->name }}
                                </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Tahan Ctrl/Cmd untuk memilih lebih dari satu auditor</p>
                        </div>

                        {{-- Temuan Audit --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Temuan Audit <span class="text-red-500">*</span>
                            </label>
                            <textarea name="findings" rows="4" required
                                placeholder="Deskripsikan temuan-temuan dalam audit..."
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-300 focus:outline-none text-sm resize-none">{{ old('findings') }}</textarea>
                        </div>

                        {{-- Rekomendasi --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Rekomendasi</label>
                            <textarea name="recommendations" rows="3"
                                placeholder="Rekomendasi perbaikan (opsional)..."
                                class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-blue-300 focus:outline-none text-sm resize-none">{{ old('recommendations') }}</textarea>
                        </div>

                    </div>
                </div>

                {{-- UPLOAD 10 DOKUMEN --}}
                <div class="bg-white rounded-2xl shadow p-6">
                    <h3 class="text-lg font-bold text-gray-700 border-b pb-2 mb-5">
                        Upload Dokumen Laporan
                        <span class="text-sm font-normal text-red-500 ml-2">* Semua dokumen wajib diisi</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($documentTypes as $type => $label)
                        <div class="border border-gray-200 rounded-xl p-4 hover:border-blue-300 transition">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ $loop->iteration }}. {{ $label }}
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="file" name="files[{{ $type }}]" id="file_{{ $type }}"
                                @if($type === 'data_klien')
                                    accept=".zip,.rar,.7z,.pdf,.doc,.docx,.xls,.xlsx"
                                @else
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                @endif
                                class="w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">

                            <p class="text-xs text-gray-400 mt-1">
                                @if($type === 'data_klien')
                                    Format: ZIP, RAR, 7Z, PDF, DOC, XLS — maks. 100MB
                                @else
                                    Format: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG — maks. 50MB
                                @endif
                            </p>

                            @error("files.$type")
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- TOMBOL --}}
                <div class="flex gap-3">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-semibold text-sm transition">
                        Simpan Laporan
                    </button>
                    <a href="{{ route('audit_reports.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2.5 rounded-xl font-semibold text-sm transition">
                        Kembali
                    </a>
                </div>

            </form>
        </div>
    </div>

</x-app-layout>