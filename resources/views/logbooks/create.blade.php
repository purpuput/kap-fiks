<x-app-layout title="Tambah Logbook">

    <div class="py-12">

        <div class="max-w-2xl mx-auto px-4">

            <div class="bg-white rounded-2xl shadow p-6">

                <h2 class="text-2xl font-bold mb-6">
                    Tambah Logbook
                </h2>

                <form action="{{ route('logbooks.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Nama Auditor -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Nama Auditor</label>
                        <input type="text" value="{{ Auth::user()->name }}" disabled class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-100 text-gray-600">
                    </div>

                    <!-- Peran Tim -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Peran dalam Tim</label>
                        <input type="text" name="team_role" id="team_role" readonly required class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-100 text-gray-600" placeholder="Pilih Klien terlebih dahulu...">
                        @error('team_role')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Klien -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Nama Klien</label>
                        <select name="client_id" id="client_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                            <option value="">Pilih Klien</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                    {{ $client->institution_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Tanggal -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Tanggal Pengisian</label>
                        <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                        @error('date')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Tahapan Audit -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Tahapan Audit</label>
                        <select name="audit_stage" required class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                            <option value="">Pilih Tahapan</option>
                            @php
                                $stages = ['Perencanaan Audit', 'Pemahaman Bisnis Klien', 'Pengujian Pengendalian', 'Pengujian Substantif', 'Penyusunan KKA', 'Review Internal', 'Penyusunan Laporan'];
                            @endphp
                            @foreach($stages as $stage)
                                <option value="{{ $stage }}" {{ old('audit_stage') == $stage ? 'selected' : '' }}>{{ $stage }}</option>
                            @endforeach
                        </select>
                        @error('audit_stage')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Area Audit -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Area Audit</label>
                        <select name="audit_area" required class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                            <option value="">Pilih Area</option>
                            @php
                                $areas = ['Kas & Bank', 'Piutang', 'Persediaan', 'Asset Tetap', 'Utang', 'Pendapatan', 'Beban', 'Lainnya'];
                            @endphp
                            @foreach($areas as $area)
                                <option value="{{ $area }}" {{ old('audit_area') == $area ? 'selected' : '' }}>{{ $area }}</option>
                            @endforeach
                        </select>
                        @error('audit_area')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Progress Kumulatif -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Progress Kumulatif (%)</label>
                        <input type="number" name="cumulative_progress" min="1" max="100" value="{{ old('cumulative_progress') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-xl" placeholder="Contoh: 50">
                        @error('cumulative_progress')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Pekerjaan Utama -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Pekerjaan Utama Hari Ini</label>
                        <textarea name="main_task" rows="3" required placeholder="Masukkan pekerjaan utama..." class="w-full px-4 py-3 border border-gray-300 rounded-xl">{{ old('main_task') }}</textarea>
                        @error('main_task')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Terdapat Kendala -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Apakah Terdapat Kendala?</label>
                        <select name="has_obstacle" id="has_obstacle" required class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                            <option value="0" {{ old('has_obstacle') == '0' ? 'selected' : '' }}>Tidak Ada</option>
                            <option value="1" {{ old('has_obstacle') == '1' ? 'selected' : '' }}>Ada</option>
                        </select>
                        @error('has_obstacle')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Kondisional Kendala -->
                    <div id="obstacle_section" class="space-y-5" style="display: none;">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Jenis Kendala</label>
                            <select name="obstacle_type" id="obstacle_type" class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                                <option value="">Pilih Kendala</option>
                                @php
                                    $obstacles = ['Data klien belum lengkap', 'Klien sulit dihubungi', 'Sistem klien bermasalah', 'Waktu terbatas', 'Masalah teknis audit', 'Lainnya'];
                                @endphp
                                @foreach($obstacles as $obs)
                                    <option value="{{ $obs }}" {{ old('obstacle_type') == $obs ? 'selected' : '' }}>{{ $obs }}</option>
                                @endforeach
                            </select>
                            @error('obstacle_type')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <!-- Kendala Lainnya (Custom) -->
                        <div id="other_obstacle_section" style="display: none;">
                            <label class="block text-sm font-semibold mb-2">Sebutkan Kendala Lainnya</label>
                            <input type="text" name="other_obstacle_type" id="other_obstacle_type" value="{{ old('other_obstacle_type') }}" placeholder="Tuliskan kendala..." class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                            @error('other_obstacle_type')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Penjelasan Singkat Kendala</label>
                            <textarea name="obstacle_description" rows="3" placeholder="Jelaskan kendala..." class="w-full px-4 py-3 border border-gray-300 rounded-xl">{{ old('obstacle_description') }}</textarea>
                            @error('obstacle_description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold mb-2">Bantuan yang Dibutuhkan</label>
                            <select name="help_needed" class="w-full px-4 py-3 border border-gray-300 rounded-xl">
                                <option value="">Pilih Bantuan</option>
                                @php
                                    $helps = ['Bantuan senior', 'Eskalasi ke manajer', 'Koordinasi dengan partner', 'Perpanjangan Waktu', 'Tidak perlu bantuan'];
                                @endphp
                                @foreach($helps as $help)
                                    <option value="{{ $help }}" {{ old('help_needed') == $help ? 'selected' : '' }}>{{ $help }}</option>
                                @endforeach
                            </select>
                            @error('help_needed')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <!-- Rencana Pekerjaan Besok -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Rencana Pekerjaan Besok</label>
                        <textarea name="next_plan" rows="3" required placeholder="Masukkan rencana pekerjaan besok..." class="w-full px-4 py-3 border border-gray-300 rounded-xl">{{ old('next_plan') }}</textarea>
                        @error('next_plan')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Tombol -->
                    <div class="flex gap-3 pt-4">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-xl transition">
                            Simpan Logbook
                        </button>
                        <a href="{{ route('logbooks.index') }}" class="flex-1 bg-gray-200 hover:bg-gray-300 text-center px-4 py-3 rounded-xl transition text-gray-800">
                            Batal
                        </a>
                    </div>

                </form>

            </div>

        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data klien dari backend
            const clientRoles = @json($clientRoles);
            
            // Elemen-elemen
            const clientSelect = document.getElementById('client_id');
            const teamRoleInput = document.getElementById('team_role');
            const hasObstacleSelect = document.getElementById('has_obstacle');
            const obstacleSection = document.getElementById('obstacle_section');
            const obstacleTypeSelect = document.getElementById('obstacle_type');
            const otherObstacleSection = document.getElementById('other_obstacle_section');

            // Set otomatis team_role berdasarkan klien yang dipilih
            function updateTeamRole() {
                const selectedClientId = clientSelect.value;
                if (selectedClientId && clientRoles[selectedClientId]) {
                    teamRoleInput.value = clientRoles[selectedClientId];
                } else {
                    teamRoleInput.value = '';
                }
            }

            // Tampilkan/sembunyikan bagian kendala
            function toggleObstacleSection() {
                if (hasObstacleSelect.value === '1') {
                    obstacleSection.style.display = 'block';
                } else {
                    obstacleSection.style.display = 'none';
                }
            }

            // Tampilkan/sembunyikan isian "Lainnya" untuk kendala
            function toggleOtherObstacle() {
                if (obstacleTypeSelect.value === 'Lainnya') {
                    otherObstacleSection.style.display = 'block';
                } else {
                    otherObstacleSection.style.display = 'none';
                }
            }

            // Event Listeners
            clientSelect.addEventListener('change', updateTeamRole);
            hasObstacleSelect.addEventListener('change', toggleObstacleSection);
            obstacleTypeSelect.addEventListener('change', toggleOtherObstacle);
            
            // Cek status saat pertama kali load
            updateTeamRole();
            toggleObstacleSection();
            toggleOtherObstacle();
        });
    </script>
    @endpush

</x-app-layout>