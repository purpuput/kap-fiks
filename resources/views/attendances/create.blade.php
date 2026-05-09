{{-- resources/views/attendances/create.blade.php --}}

<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Form Absensi
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-2xl p-6">

                {{-- Error Validation --}}
                @if ($errors->any())
                <div class="mb-5 p-4 bg-red-100 border border-red-300 rounded-xl">

                    <ul class="list-disc list-inside text-red-700 text-sm">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>
                @endif

                <form action="{{ route('attendances.store') }}"
                    method="POST">

                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- Auditor --}}
                        @if(auth()->user()->role === 'admin')
                        <div>

                            <label class="block text-sm font-semibold mb-2">
                                Auditor
                            </label>

                            <select
                                name="user_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                                @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }}
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
                                name="date"
                                value="{{ now()->format('Y-m-d') }}"
                                readonly
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl bg-gray-100">

                        </div>

                        {{-- Tipe Absensi --}}
                        <div>

                            <label class="block text-sm font-semibold mb-2">
                                Tipe Absensi
                            </label>

                            <input
                                type="text"
                                value="{{ $attendanceWindow['type'] ?? 'Di Luar Jam Absensi' }}"
                                readonly
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl bg-gray-100">

                            @if($attendanceWindow['type'])
                            <input
                                type="hidden"
                                name="tipe"
                                value="{{ $attendanceWindow['type'] }}">
                            @endif

                        </div>

                        {{-- Status --}}
                        <div>

                            <label class="block text-sm font-semibold mb-2">
                                Status
                            </label>

                            <select
                                name="status"
                                id="status"
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl
                                       focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                                <option value="hadir">Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="alpha">Alpha</option>

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
                            class="w-full px-4 py-2 border border-gray-300 rounded-xl
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>

                    </div>

                    {{-- Camera --}}
                    <div id="camera-section" class="mt-6">

                        <label class="block text-sm font-semibold mb-2">
                            Foto Absensi
                        </label>

                        <video
                            id="camera"
                            autoplay
                            playsinline
                            class="w-full rounded-2xl border border-gray-300 shadow">
                        </video>

                        <canvas id="canvas" class="hidden"></canvas>

                        <input
                            type="hidden"
                            name="photo_data"
                            id="photo_data">

                        <div class="mt-4 flex gap-3">

                            <button
                                type="button"
                                id="capture"
                                class="px-5 py-2 bg-gray-800 hover:bg-gray-900
                                       text-white rounded-xl transition">

                                Ambil Foto

                            </button>

                            <span
                                id="photo-status"
                                class="text-sm text-green-600 hidden">

                                ✓ Foto berhasil diambil

                            </span>

                        </div>

                    </div>

                    {{-- Action --}}
                    <div class="mt-8 flex flex-wrap gap-3">

                        <button
                            type="submit"
                            {{ !$attendanceWindow['can_attend'] ? 'disabled' : '' }}
                            class="px-5 py-2 rounded-xl transition shadow text-white
                            {{ $attendanceWindow['can_attend']
                                ? 'bg-blue-600 hover:bg-blue-700'
                                : 'bg-gray-400 cursor-not-allowed' }}">

                            Simpan Absensi

                        </button>

                        <a href="{{ route('attendances.index') }}"
                            class="px-5 py-2 bg-gray-200 hover:bg-gray-300
                            text-gray-800 rounded-xl transition">

                            Kembali

                        </a>

                    </div>

                    @if(!$attendanceWindow['can_attend'])

                    <div class="mt-5 p-4 rounded-xl bg-yellow-100 border border-yellow-300 text-yellow-800">

                        <strong>Perhatian:</strong><br>

                        Absensi hanya dapat dilakukan pada:
                        <ul class="list-disc list-inside mt-2">
                            <li>Masuk : {{ $attendanceWindow['check_in_start'] }} - {{ $attendanceWindow['check_in_end'] }} WIB</li>
                            <li>Pulang : {{ $attendanceWindow['check_out_start'] }} - {{ $attendanceWindow['check_out_end'] }} WIB</li>
                        </ul>

                    </div>

                    @endif

                </form>

            </div>

        </div>

    </div>

    {{-- Camera Script --}}
    <script>
        const camera = document.getElementById('camera');
        const canvas = document.getElementById('canvas');
        const capture = document.getElementById('capture');
        const photoData = document.getElementById('photo_data');
        const photoStatus = document.getElementById('photo-status');
        const statusSelect = document.getElementById('status');
        const cameraSection = document.getElementById('camera-section');

        let stream = null;

        // Start kamera
        async function startCamera() {

            try {

                stream = await navigator.mediaDevices.getUserMedia({
                    video: true
                });

                camera.srcObject = stream;

            } catch (error) {

                console.log(error);

                alert('Kamera tidak dapat diakses');
            }
        }

        // Stop kamera
        function stopCamera() {

            if (stream) {

                stream.getTracks().forEach(track => track.stop());

                camera.srcObject = null;
            }
        }

        // Default
        if (statusSelect.value === 'hadir') {
            startCamera();
        } else {
            cameraSection.style.display = 'none';
        }

        // Capture
        capture.addEventListener('click', () => {

            canvas.width = camera.videoWidth;
            canvas.height = camera.videoHeight;

            const ctx = canvas.getContext('2d');

            ctx.drawImage(camera, 0, 0);

            photoData.value = canvas.toDataURL('image/png');

            photoStatus.classList.remove('hidden');

        });

        // Ganti status
        statusSelect.addEventListener('change', function() {

            if (this.value === 'hadir') {

                cameraSection.style.display = 'block';

                startCamera();

            } else {

                cameraSection.style.display = 'none';

                photoData.value = '';

                photoStatus.classList.add('hidden');

                stopCamera();
            }

        });
    </script>

</x-app-layout>