<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua user auditor
        $users = User::where('role', 'auditor')->get();

        // Generate 30 hari terakhir
        foreach ($users as $user) {

            for ($i = 1; $i <= 10; $i++) {

                $date = Carbon::now()->subDays($i);

                // Random status
                $statuses = ['hadir', 'izin', 'sakit'];
                $status = $statuses[array_rand($statuses)];

                Attendance::create([
                    'user_id' => $user->id,
                    'date' => $date->format('Y-m-d'),

                    'check_in' => $status === 'hadir'
                        ? Carbon::createFromTime(7, rand(30, 59))->format('H:i:s')
                        : null,

                    'check_out' => $status === 'hadir'
                        ? Carbon::createFromTime(16, rand(30, 59))->format('H:i:s')
                        : null,

                    'status' => $status,

                    'photo_check_in' => null,
                    'photo_check_out' => null,
                    'attachment' => in_array($status, ['izin', 'sakit']) ? 'attachments/dummy_surat.pdf' : null,
                ]);
            }
        }
    }
}
