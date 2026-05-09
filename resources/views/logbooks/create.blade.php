<x-app-layout title="Tambah Logbook">

    <div class="py-12">

        <div class="max-w-2xl mx-auto px-4">

            <div class="bg-white rounded-2xl shadow p-6">

                <h2 class="text-2xl font-bold mb-6">
                    Tambah Logbook
                </h2>

                <form action="{{ route('logbooks.store') }}"
                    method="POST"
                    class="space-y-5">

                    @csrf

                    <!-- Tanggal -->
                    <div>

                        <label class="block text-sm font-semibold mb-2">
                            Tanggal
                        </label>

                        <input type="date"
                            name="date"
                            value="{{ old('date', now()->toDateString()) }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl">

                        @error('date')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>

                    <!-- Aktivitas -->
                    <div>

                        <label class="block text-sm font-semibold mb-2">
                            Aktivitas
                        </label>

                        <textarea
                            name="activity"
                            rows="5"
                            required
                            placeholder="Masukkan aktivitas logbook..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl">{{ old('activity') }}</textarea>

                        @error('activity')
                        <p class="text-red-600 text-sm mt-1">
                            {{ $message }}
                        </p>
                        @enderror

                    </div>

                    <!-- Tombol -->
                    <div class="flex gap-3">

                        <button type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700
                            text-white px-4 py-3 rounded-xl transition">

                            Simpan

                        </button>

                        <a href="{{ route('logbooks.index') }}"
                            class="flex-1 bg-gray-200 hover:bg-gray-300
                            text-center px-4 py-3 rounded-xl transition">

                            Batal

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>