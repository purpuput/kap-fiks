<x-app-layout title="Edit Logbook">

    <div class="py-12">

        <div class="max-w-2xl mx-auto px-4">

            <div class="bg-white rounded-2xl shadow p-6">

                <h2 class="text-2xl font-bold mb-6">
                    Edit Logbook
                </h2>

                @if($logbook->status === 'revision')

                <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl">

                    <h3 class="font-semibold text-red-700 mb-1">
                        Catatan Revisi
                    </h3>

                    <p class="text-sm text-red-600">
                        {{ $logbook->note }}
                    </p>

                </div>

                @endif

                <form action="{{ route('logbooks.update', $logbook) }}"
                    method="POST"
                    class="space-y-5">

                    @csrf
                    @method('PUT')

                    <!-- Tanggal -->
                    <div>

                        <label class="block text-sm font-semibold mb-2">
                            Tanggal
                        </label>

                        <input type="date"
                            name="date"
                            value="{{ old('date', $logbook->date->toDateString()) }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl">

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
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl">{{ old('activity', $logbook->activity) }}</textarea>

                    </div>

                    <!-- Tombol -->
                    <div class="flex gap-3">

                        <button type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700
                            text-white px-4 py-3 rounded-xl transition">

                            Update

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