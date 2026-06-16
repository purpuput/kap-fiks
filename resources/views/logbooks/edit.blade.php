<x-app-layout title="Edit Logbook">

    <div class="py-6 animate-fade-in-up">

        <div class="max-w-2xl mx-auto px-4">

            <div class="bg-white border border-slate-100 rounded-3xl p-6 md:p-8 shadow-sm">

                <h2 class="text-xl font-bold text-slate-800 mb-6">
                    Edit Logbook
                </h2>

                <form action="{{ route('logbooks.update', $logbook->id) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Nama Pegawai -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Pegawai</label>
                        <input type="text" value="{{ $logbook->user->name }}" disabled class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-100 text-slate-400 cursor-not-allowed text-sm">
                    </div>

                    @if($logbook->user->role === 'auditor')
                    <!-- Peran Tim -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Peran dalam Tim</label>
                        <input type="text" name="team_role" id="team_role" value="{{ old('team_role', $logbook->team_role) }}" readonly required class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-100 text-slate-400 cursor-not-allowed text-sm">
                        @error('team_role')<p class="text-[10px] text-rose-500 mt-1.5 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    <!-- Klien -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Nama Klien</label>
                        <select name="client_id" id="client_id" required class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50/50 text-slate-700 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-sm">
                            <option value="">Pilih Klien</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $logbook->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->institution_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')<p class="text-[10px] text-rose-500 mt-1.5 font-semibold">{{ $message }}</p>@enderror
                    </div>
                    @endif

                    <!-- Tanggal -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tanggal Pengisian</label>
                        <input type="date" name="date" value="{{ old('date', $logbook->date->toDateString()) }}" required class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50/50 text-slate-700 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-sm">
                        @error('date')<p class="text-[10px] text-rose-500 mt-1.5 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    @if($logbook->user->role === 'auditor')
                    <!-- Tahapan Audit -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tahapan Audit</label>
                        <select name="audit_stage" required class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50/50 text-slate-700 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-sm">
                            <option value="">Pilih Tahapan</option>
                            @php
                                $stages = ['Perencanaan Audit', 'Pemahaman Bisnis Klien', 'Pengujian Pengendalian', 'Pengujian Substantif', 'Penyusunan KKA', 'Review Internal', 'Penyusunan Laporan'];
                            @endphp
                            @foreach($stages as $stage)
                                <option value="{{ $stage }}" {{ old('audit_stage', $logbook->audit_stage) == $stage ? 'selected' : '' }}>{{ $stage }}</option>
                            @endforeach
                        </select>
                        @error('audit_stage')<p class="text-[10px] text-rose-500 mt-1.5 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    <!-- Area Audit -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Area Audit</label>
                        <select name="audit_area" required class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50/50 text-slate-700 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-sm">
                            <option value="">Pilih Area</option>
                            @php
                                $areas = ['Kas & Bank', 'Piutang', 'Persediaan', 'Asset Tetap', 'Utang', 'Pendapatan', 'Beban', 'Lainnya'];
                            @endphp
                            @foreach($areas as $area)
                                <option value="{{ $area }}" {{ old('audit_area', $logbook->audit_area) == $area ? 'selected' : '' }}>{{ $area }}</option>
                            @endforeach
                        </select>
                        @error('audit_area')<p class="text-[10px] text-rose-500 mt-1.5 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    <!-- Progress Kumulatif -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Progress Kumulatif (%)</label>
                        <input type="number" name="cumulative_progress" min="1" max="100" value="{{ old('cumulative_progress', $logbook->cumulative_progress) }}" required class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50/50 text-slate-700 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-sm">
                        @error('cumulative_progress')<p class="text-[10px] text-rose-500 mt-1.5 font-semibold">{{ $message }}</p>@enderror
                    </div>
                    @endif

                    <!-- Pekerjaan Utama -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pekerjaan Utama Hari Ini</label>
                        <textarea name="main_task" rows="3" required placeholder="Masukkan pekerjaan utama..." class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50/50 text-slate-700 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-sm">{{ old('main_task', $logbook->main_task) }}</textarea>
                        @error('main_task')<p class="text-[10px] text-rose-500 mt-1.5 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    <!-- Terdapat Kendala -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Apakah Terdapat Kendala?</label>
                        <select name="has_obstacle" id="has_obstacle" required class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50/50 text-slate-700 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-sm">
                            <option value="0" {{ old('has_obstacle', $logbook->has_obstacle ? '1' : '0') == '0' ? 'selected' : '' }}>Tidak Ada</option>
                            <option value="1" {{ old('has_obstacle', $logbook->has_obstacle ? '1' : '0') == '1' ? 'selected' : '' }}>Ada</option>
                        </select>
                        @error('has_obstacle')<p class="text-[10px] text-rose-500 mt-1.5 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    <!-- Kondisional Kendala -->
                    <div id="obstacle_section" class="space-y-5" style="display: none;">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Jenis Kendala</label>
                            @php
                                $obstacles = ['Data klien belum lengkap', 'Klien sulit dihubungi', 'Sistem klien bermasalah', 'Waktu terbatas', 'Masalah teknis audit', 'Lainnya'];
                                $isLainnya = !in_array($logbook->obstacle_type, array_slice($obstacles, 0, 5)) && $logbook->obstacle_type != null;
                            @endphp
                            <select name="obstacle_type" id="obstacle_type" class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50/50 text-slate-700 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-sm">
                                <option value="">Pilih Kendala</option>
                                @foreach(array_slice($obstacles, 0, 5) as $obs)
                                    <option value="{{ $obs }}" {{ old('obstacle_type', $logbook->obstacle_type) == $obs ? 'selected' : '' }}>{{ $obs }}</option>
                                @endforeach
                                <option value="Lainnya" {{ old('obstacle_type', $isLainnya ? 'Lainnya' : '') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('obstacle_type')<p class="text-[10px] text-rose-500 mt-1.5 font-semibold">{{ $message }}</p>@enderror
                        </div>

                        <!-- Kendala Lainnya (Custom) -->
                        <div id="other_obstacle_section" style="display: none;">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Sebutkan Kendala Lainnya</label>
                            <input type="text" name="other_obstacle_type" id="other_obstacle_type" value="{{ old('other_obstacle_type', $isLainnya ? $logbook->obstacle_type : '') }}" placeholder="Tuliskan kendala..." class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50/50 text-slate-700 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-sm">
                            @error('other_obstacle_type')<p class="text-[10px] text-rose-500 mt-1.5 font-semibold">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Penjelasan Singkat Kendala</label>
                            <textarea name="obstacle_description" rows="3" placeholder="Jelaskan kendala..." class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50/50 text-slate-700 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-sm">{{ old('obstacle_description', $logbook->obstacle_description) }}</textarea>
                            @error('obstacle_description')<p class="text-[10px] text-rose-500 mt-1.5 font-semibold">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Bantuan yang Dibutuhkan</label>
                            <select name="help_needed" class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50/50 text-slate-700 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-sm">
                                <option value="">Pilih Bantuan</option>
                                @php
                                    $helps = ['Bantuan senior', 'Eskalasi ke manajer', 'Koordinasi dengan partner', 'Perpanjangan Waktu', 'Tidak perlu bantuan'];
                                @endphp
                                @foreach($helps as $help)
                                    <option value="{{ $help }}" {{ old('help_needed', $logbook->help_needed) == $help ? 'selected' : '' }}>{{ $help }}</option>
                                @endforeach
                            </select>
                            @error('help_needed')<p class="text-[10px] text-rose-500 mt-1.5 font-semibold">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <!-- Rencana Pekerjaan Besok -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Rencana Pekerjaan Besok</label>
                        <textarea name="next_plan" rows="3" required placeholder="Masukkan rencana pekerjaan besok..." class="w-full px-4 py-3 border border-slate-200 rounded-xl bg-slate-50/50 text-slate-700 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:outline-none transition-all text-sm">{{ old('next_plan', $logbook->next_plan) }}</textarea>
                        @error('next_plan')<p class="text-[10px] text-rose-500 mt-1.5 font-semibold">{{ $message }}</p>@enderror
                    </div>

                    <!-- Tombol -->
                    <div class="flex gap-3 pt-4">
                        <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-3 rounded-xl text-xs font-bold transition shadow-sm shadow-blue-500/10 hover:shadow-md">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('logbooks.index') }}" class="flex-1 bg-slate-100 hover:bg-slate-200 text-center px-4 py-3 rounded-xl text-xs font-bold transition text-slate-700">
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
                if (!clientSelect || !teamRoleInput) return;
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
            if (clientSelect) clientSelect.addEventListener('change', updateTeamRole);
            if (hasObstacleSelect) hasObstacleSelect.addEventListener('change', toggleObstacleSection);
            if (obstacleTypeSelect) obstacleTypeSelect.addEventListener('change', toggleOtherObstacle);
            
            // Cek status saat pertama kali load
            // Jika team_role kosong (karena load dari old value mungkin beda), kita update
            if (teamRoleInput && !teamRoleInput.value) {
                updateTeamRole();
            }
            toggleObstacleSection();
            toggleOtherObstacle();
        });
    </script>
    @endpush

</x-app-layout>