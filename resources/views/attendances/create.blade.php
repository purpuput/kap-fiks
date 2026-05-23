{{-- resources/views/attendances/create.blade.php --}}

<x-app-layout>

    {{-- Header --}}
    <x-slot name="header">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Form Absensi
        </h2>

    </x-slot>

    <div class="py-12">

        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Card utama --}}
            <div class="bg-white shadow rounded-2xl p-6">

                {{-- Error validasi --}}
                @if ($errors->any())

                <div class="mb-5 p-4 bg-red-100 border border-red-300 rounded-xl">

                    <ul class="list-disc list-inside text-red-700 text-sm">

                        @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

                @endif

                @php

                /**
                * Menentukan tipe absensi otomatis
                */

                $attendanceType = 'Masuk';

                // Jika sudah check in maka otomatis pulang
                if ($todayAttendance && $todayAttendance->check_in) {
                $attendanceType = 'Pulang';
                }

                // Jika absensi sudah lengkap
                $isCompleted =
                $todayAttendance &&
                $todayAttendance->check_in &&
                $todayAttendance->check_out;

                @endphp

                {{-- Jika absensi selesai --}}
                @if($isCompleted)

                <div class="p-5 rounded-2xl bg-green-100 border border-green-300">

                    <h3 class="text-lg font-semibold text-green-700">
                        Absensi Hari Ini Sudah Lengkap
                    </h3>

                    <p class="text-sm text-green-600 mt-1">
                        Anda sudah melakukan absensi masuk dan pulang hari ini.
                    </p>

                    <div class="mt-4">

                        <a href="{{ route('attendances.index') }}"
                            class="inline-flex px-4 py-2 bg-green-600 hover:bg-green-700
                                   text-white rounded-xl transition">

                            Kembali ke Riwayat

                        </a>

                    </div>

                </div>

                @else

                {{-- Form absensi --}}
                <form
                    action="{{ route('attendances.store') }}"
                    method="POST">

                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- Pilihan auditor --}}
                        @if(auth()->user()->role === 'admin')

                        <div>

                            <label class="block text-sm font-semibold mb-2">
                                Pilih Akun
                            </label>

                            <select
                                name="user_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                                @foreach($users as $user)

                                <option value="{{ $user->id }}" {{ $user->id === auth()->id() ? 'selected' : '' }}>

                                    {{ $user->name }} {{ $user->id === auth()->id() ? '(Saya)' : '' }}

                                </option>

                                @endforeach

                            </select>

                        </div>

                        @endif

                        {{-- Tanggal --}}
                        <div>

                            <label class="block text-sm font-semibold mb-2">
                                Tanggal
                            </label>

                            <input
                                type="date"
                                value="{{ now()->format('Y-m-d') }}"
                                readonly
                                class="w-full px-4 py-2 border border-gray-300
                                       rounded-xl bg-gray-100">

                        </div>

                        {{-- Tipe absensi otomatis --}}
                        <div>

                            <label class="block text-sm font-semibold mb-2">
                                Tipe Absensi
                            </label>

                            <input
                                type="text"
                                value="Absen {{ $attendanceType }}"
                                readonly
                                class="w-full px-4 py-2 border border-gray-300
                                       rounded-xl bg-gray-100 font-semibold">

                            {{-- Hidden input --}}
                            <input
                                type="hidden"
                                name="tipe"
                                value="{{ $attendanceType }}">

                        </div>

                        {{-- Status --}}
                        <div>

                            <label class="block text-sm font-semibold mb-2">
                                Status
                            </label>

                            <select
                                name="status"
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                                <option value="hadir">
                                    Hadir
                                </option>

                                <option value="izin">
                                    Izin
                                </option>

                                <option value="sakit">
                                    Sakit
                                </option>

                                <option value="alpha">
                                    Alpha
                                </option>

                            </select>

                        </div>

                    </div>

                    {{-- Keterangan --}}
                    <div class="mt-5">

                        <label class="block text-sm font-semibold mb-2">
                            Keterangan
                        </label>

                        <textarea
                            name="notes"
                            rows="4"
                            placeholder="Tambahkan keterangan jika diperlukan..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>

                    </div>

                    {{-- Tombol aksi --}}
                    <div class="mt-8 flex flex-wrap gap-3">

                        {{-- Tombol simpan --}}
                        <button
                            type="submit"
                            class="px-5 py-2 rounded-xl transition shadow
                                   text-white bg-blue-600 hover:bg-blue-700">

                            Simpan {{ $attendanceType }}

                        </button>

                        {{-- Tombol kembali --}}
                        <a href="{{ route('attendances.index') }}"
                            class="px-5 py-2 bg-gray-200 hover:bg-gray-300
                                   text-gray-800 rounded-xl transition">

                            Kembali

                        </a>

                    </div>

                </form>

                @endif

            </div>

        </div>

    </div>

</x-app-layout>