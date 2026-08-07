<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceExport;
use App\Models\Attendance;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    /**
     * Menampilkan halaman data absensi
     */
    public function index(Request $request)
    {
        // Mengambil filter dari request
        $date = $request->input('date');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        
        // Memproses multiple user_ids
        $user_ids = $request->input('user_ids');
        if (is_string($user_ids)) {
            $user_ids = array_filter(explode(',', $user_ids));
        }
        if (empty($user_ids) && $request->filled('user_id')) {
            $user_ids = [$request->input('user_id')];
        }
        $user_ids = is_array($user_ids) ? array_values(array_filter($user_ids)) : [];

        // Mengambil data user yang sedang login
        $user = Auth::user();

        // Query awal attendance beserta relasi user
        $query = Attendance::with('user');

        /**
         * Filter berdasarkan role
         * - Admin dan pimpinan dapat melihat semua data atau memfilter 1 / lebih user
         * - Auditor hanya dapat melihat data dirinya sendiri
         */
        if (in_array($user->role, ['admin', 'pimpinan']) && !empty($user_ids)) {
            $query->whereIn('user_id', $user_ids);
        } elseif ($user->role === 'auditor') {
            $query->where('user_id', Auth::id());
        }

        /**
         * Filter berdasarkan tanggal / rentang periode
         */
        if ($start_date && $end_date) {
            $query->whereBetween('date', [$start_date, $end_date]);
        } elseif ($start_date) {
            $query->whereDate('date', '>=', $start_date);
        } elseif ($end_date) {
            $query->whereDate('date', '<=', $end_date);
        } elseif ($date) {
            $query->whereDate('date', $date);
        }

        // Unpaginated query untuk rekapitulasi statistik evaluasi per pegawai
        $allAttendancesForSummary = (clone $query)->get();
        $userSummary = $allAttendancesForSummary->groupBy('user_id')->map(function ($items) {
            $userItem = $items->first()->user;
            $totalDays = $items->count();
            $hadir = $items->where('status', 'hadir')->count();
            $sakit = $items->where('status', 'sakit')->count();
            $izin  = $items->where('status', 'izin')->count();
            $alpha = $items->where('status', 'alpha')->count();
            $percentage = $totalDays > 0 ? round(($hadir / $totalDays) * 100, 1) : 0;

            return (object) [
                'user_id' => $items->first()->user_id,
                'name' => $userItem->name ?? 'Pegawai',
                'role' => $userItem->role ?? '-',
                'total' => $totalDays,
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpha' => $alpha,
                'percentage' => $percentage,
            ];
        })->values();

        // Mengambil data absensi dengan pagination
        $attendances = $query
            ->orderBy('date', 'desc')
            ->paginate(15);

        // Mengambil daftar auditor dan admin untuk dropdown filter
        $users = User::whereIn('role', ['admin', 'auditor'])
            ->orderBy('name')
            ->get();

        // Menampilkan halaman index
        return view('attendances.index', compact(
            'attendances',
            'date',
            'start_date',
            'end_date',
            'users',
            'user_ids',
            'userSummary'
        ));
    }

    /**
     * Export data absensi ke PDF
     */
    public function exportPdf(Request $request)
    {
        $currentUser = Auth::user();

        // Query data attendance
        $query = Attendance::with('user');

        // Filter user berdasarkan role
        if ($currentUser->role === 'auditor') {
            $userIds = [$currentUser->id];
        } else {
            $userIds = $request->input('user_ids');
            if (is_string($userIds)) {
                $userIds = array_filter(explode(',', $userIds));
            }
            if (empty($userIds) && $request->filled('user_id')) {
                $userIds = [$request->input('user_id')];
            }
            $userIds = is_array($userIds) ? array_values(array_filter($userIds)) : [];
        }

        if (!empty($userIds)) {
            $query->whereIn('user_id', $userIds);
        }

        // Filter rentang tanggal / periode
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $date = $request->input('date');

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        } elseif ($date) {
            $query->whereDate('date', $date);
            $startDate = $date;
            $endDate = $date;
        }

        // Mengambil data absensi
        $attendances = $query
            ->orderBy('date', 'desc')
            ->get();

        // Pengguna yang difilter
        $selectedUsers = !empty($userIds) ? User::whereIn('id', $userIds)->get() : collect();

        // Rekapitulasi Per Pegawai untuk PDF
        $userSummary = $attendances->groupBy('user_id')->map(function ($items) {
            $userItem = $items->first()->user;
            $totalDays = $items->count();
            $hadir = $items->where('status', 'hadir')->count();
            $sakit = $items->where('status', 'sakit')->count();
            $izin  = $items->where('status', 'izin')->count();
            $alpha = $items->where('status', 'alpha')->count();
            $percentage = $totalDays > 0 ? round(($hadir / $totalDays) * 100, 1) : 0;

            return (object) [
                'user_id' => $items->first()->user_id,
                'name' => $userItem->name ?? 'Pegawai',
                'role' => $userItem->role ?? '-',
                'total' => $totalDays,
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpha' => $alpha,
                'percentage' => $percentage,
            ];
        })->values();

        // Statistik Evaluasi Total
        $totalDays = $attendances->count();
        $totalHadir = $attendances->where('status', 'hadir')->count();
        $totalSakit = $attendances->where('status', 'sakit')->count();
        $totalIzin  = $attendances->where('status', 'izin')->count();
        $totalAlpha = $attendances->where('status', 'alpha')->count();
        $attendancePercentage = $totalDays > 0 ? round(($totalHadir / $totalDays) * 100, 1) : 0;

        $stats = [
            'total' => $totalDays,
            'hadir' => $totalHadir,
            'sakit' => $totalSakit,
            'izin' => $totalIzin,
            'alpha' => $totalAlpha,
            'percentage' => $attendancePercentage,
        ];

        // Load view PDF Laporan Evaluasi
        $pdf = Pdf::loadView(
            'attendances.pdf.pdf',
            compact('attendances', 'selectedUsers', 'startDate', 'endDate', 'stats', 'userSummary')
        );

        $nameSuffix = $selectedUsers->count() === 1 
            ? str_replace(' ', '_', $selectedUsers->first()->name) 
            : ($selectedUsers->count() > 1 ? $selectedUsers->count() . '_Pegawai' : 'Semua_Pegawai');

        $fileName = 'Laporan_Evaluasi_Absensi_' . $nameSuffix . '_' . date('Ymd_His') . '.pdf';

        // Download PDF
        return $pdf->download($fileName);
    }

    /**
     * Export data absensi ke Excel
     */
    public function exportExcel(Request $request)
    {
        $currentUser = Auth::user();

        // Query data attendance
        $query = Attendance::with('user');

        // Filter user berdasarkan role
        if ($currentUser->role === 'auditor') {
            $userIds = [$currentUser->id];
        } else {
            $userIds = $request->input('user_ids');
            if (is_string($userIds)) {
                $userIds = array_filter(explode(',', $userIds));
            }
            if (empty($userIds) && $request->filled('user_id')) {
                $userIds = [$request->input('user_id')];
            }
            $userIds = is_array($userIds) ? array_values(array_filter($userIds)) : [];
        }

        if (!empty($userIds)) {
            $query->whereIn('user_id', $userIds);
        }

        // Filter rentang tanggal / periode
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $date = $request->input('date');

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->whereDate('date', '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('date', '<=', $endDate);
        } elseif ($date) {
            $query->whereDate('date', $date);
            $startDate = $date;
            $endDate = $date;
        }

        $selectedUsers = !empty($userIds) ? User::whereIn('id', $userIds)->get() : collect();
        $attendances = $query->orderBy('date', 'desc')->get();

        // Rekapitulasi Per Pegawai untuk Excel
        $userSummary = $attendances->groupBy('user_id')->map(function ($items) {
            $userItem = $items->first()->user;
            $totalDays = $items->count();
            $hadir = $items->where('status', 'hadir')->count();
            $sakit = $items->where('status', 'sakit')->count();
            $izin  = $items->where('status', 'izin')->count();
            $alpha = $items->where('status', 'alpha')->count();
            $percentage = $totalDays > 0 ? round(($hadir / $totalDays) * 100, 1) : 0;

            return (object) [
                'user_id' => $items->first()->user_id,
                'name' => $userItem->name ?? 'Pegawai',
                'role' => $userItem->role ?? '-',
                'total' => $totalDays,
                'hadir' => $hadir,
                'sakit' => $sakit,
                'izin' => $izin,
                'alpha' => $alpha,
                'percentage' => $percentage,
            ];
        })->values();

        // Statistik Evaluasi untuk Excel
        $totalDays = $attendances->count();
        $totalHadir = $attendances->where('status', 'hadir')->count();
        $totalSakit = $attendances->where('status', 'sakit')->count();
        $totalIzin  = $attendances->where('status', 'izin')->count();
        $totalAlpha = $attendances->where('status', 'alpha')->count();
        $attendancePercentage = $totalDays > 0 ? round(($totalHadir / $totalDays) * 100, 1) : 0;

        $stats = [
            'total' => $totalDays,
            'hadir' => $totalHadir,
            'sakit' => $totalSakit,
            'izin' => $totalIzin,
            'alpha' => $totalAlpha,
            'percentage' => $attendancePercentage,
        ];

        $nameSuffix = $selectedUsers->count() === 1 
            ? str_replace(' ', '_', $selectedUsers->first()->name) 
            : ($selectedUsers->count() > 1 ? $selectedUsers->count() . '_Pegawai' : 'Semua_Pegawai');

        // Download file Excel
        $fileName = 'Laporan_Evaluasi_Absensi_' . $nameSuffix . '_' . date('Ymd_His') . '.xlsx';

        return Excel::download(
            new AttendanceExport($attendances, $selectedUsers, $startDate, $endDate, $stats, $userSummary),
            $fileName
        );
    }

    /**
     * Menampilkan form tambah absensi
     */
    public function create()
    {
        // Hanya admin dan auditor yang boleh akses
        abort_unless(
            in_array(Auth::user()->role, ['admin', 'auditor']),
            403,
            'Akses ditolak!'
        );

        return redirect()->route('attendances.index', ['show_modal' => 1]);
    }

    /**
     * Menyimpan data absensi
     */
    public function store(Request $request)
    {
        // Validasi role
        abort_unless(
            in_array(Auth::user()->role, ['admin', 'auditor']),
            403,
            'Akses ditolak!'
        );

        /**
         * Validasi input form
         */
        $validated = $request->validate([

            // User boleh kosong jika auditor login sendiri
            'user_id' => ['nullable', 'exists:users,id'],

            // Tipe absensi
            'tipe' => 'required_if:status,hadir,alpha|nullable|in:Masuk,Pulang',

            // Status absensi
            'status' => 'required|in:hadir,izin,sakit,alpha',

            // File lampiran (wajib jika sakit atau izin)
            'attachment' => 'required_if:status,izin,sakit|nullable|file|mimes:pdf|max:2048',
        ]);

        /**
         * Jika admin bisa memilih user
         * Jika auditor otomatis menggunakan ID login
         */
        $validated['user_id'] = Auth::user()->role === 'admin'
            ? ($validated['user_id'] ?? Auth::id())
            : Auth::id();

        // Menggunakan tanggal hari ini otomatis
        $validated['date'] = now()->toDateString();

        /**
         * Mengecek apakah user sudah memiliki data absensi hari ini
         */
        $attendance = Attendance::where('user_id', $validated['user_id'])
            ->where('date', $validated['date'])
            ->first();

        /**
         * Jika absensi masuk dan pulang sudah lengkap
         */
        if ($attendance && $attendance->check_in && $attendance->check_out) {

            return back()
                ->withInput()
                ->withErrors([
                    'tipe' => 'Absensi hari ini sudah lengkap.',
                ]);
        }

        /**
         * Validasi agar tidak bisa absen pulang
         * sebelum melakukan absen masuk
         */
        if (
            $validated['status'] === 'hadir' &&
            $validated['tipe'] === 'Pulang' &&
            (! $attendance || ! $attendance->check_in)
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'tipe' => 'Absensi masuk harus dilakukan terlebih dahulu.',
                ]);
        }

        /**
         * Data yang akan disimpan
         */
        $data = [
            'user_id' => $validated['user_id'],
            'date' => $validated['date'],
            'status' => $validated['status'],
        ];

        /**
         * Jika status hadir maka simpan jam absensi
         */
        if ($validated['status'] === 'hadir') {

            /**
             * Absensi masuk
             */
            if (($validated['tipe'] ?? 'Masuk') === 'Masuk') {

                // Menyimpan jam masuk saat ini
                $data['check_in'] = now()->format('H:i:s');
            } else {

                /**
                 * Absensi pulang
                 */

                // Menyimpan jam pulang saat ini
                $data['check_out'] = now()->format('H:i:s');
            }
        } else {
            // Jika sakit/izin, jam masuk dan pulang kosong
            $data['check_in'] = null;
            $data['check_out'] = null;
        }

        // Simpan file attachment jika ada
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs('attachments', $filename, 'local');
            $data['attachment'] = $path;
        }

        /**
         * Jika data attendance sudah ada
         * maka update
         */
        if ($attendance) {

            $attendance->update($data);
        } else {

            /**
             * Jika belum ada maka create baru
             */
            Attendance::create($data);
        }

        // Redirect kembali ke halaman index
        return redirect()
            ->route('attendances.index')
            ->with('success', 'Absensi berhasil dicatat');
    }

    /**
     * Menampilkan form edit absensi
     */
    public function edit(Attendance $attendance)
    {
        // Hanya admin yang boleh mengedit
        abort_unless(Auth::user()->role === 'admin', 403);

        return view('attendances.edit', compact('attendance'));
    }

    /**
     * Memperbarui data absensi
     */
    public function update(Request $request, Attendance $attendance)
    {
        // Hanya admin yang boleh mengupdate
        abort_unless(Auth::user()->role === 'admin', 403);

        $validated = $request->validate([
            'status' => 'required|in:hadir,izin,sakit,alpha',
            'check_in' => 'nullable',
            'check_out' => 'nullable',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:2048',
        ]);

        $data = [
            'status' => $validated['status'],
        ];

        if ($validated['status'] === 'hadir') {
            $data['check_in'] = $validated['check_in'];
            $data['check_out'] = $validated['check_out'];
        } else {
            $data['check_in'] = null;
            $data['check_out'] = null;
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time().'_'.$file->getClientOriginalName();
            $path = $file->storeAs('attachments', $filename, 'local');
            $data['attachment'] = $path;
        }

        $attendance->update($data);

        return redirect()
            ->route('attendances.index')
            ->with('success', 'Absensi berhasil diperbarui');
    }

    /**
     * Menghapus data absensi
     */
    public function destroy(Attendance $attendance)
    {
        // Hanya admin yang boleh menghapus
        abort_unless(Auth::user()->role === 'admin', 403);

        // Hapus file attachment dari local storage jika ada
        if ($attendance->attachment) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($attendance->attachment);
        }

        // Menghapus data
        $attendance->delete();

        // Redirect kembali
        return redirect()
            ->route('attendances.index')
            ->with('success', 'Absensi berhasil dihapus');
    }

    /**
     * Download lampiran absensi secara aman
     */
    public function downloadAttachment(Attendance $attendance)
    {
        // Otorisasi: Hanya admin, pimpinan, atau pemilik absensi yang boleh mengunduh
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'pimpinan' && Auth::id() !== $attendance->user_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunduh lampiran ini.');
        }

        if (empty($attendance->attachment) || !\Illuminate\Support\Facades\Storage::disk('local')->exists($attendance->attachment)) {
            return back()->with('error', 'File lampiran tidak ditemukan di server.');
        }

        // Mengambil extension file untuk memberikan nama file unduhan yang bersih
        $extension = pathinfo($attendance->attachment, PATHINFO_EXTENSION);
        $cleanName = 'Lampiran_Absensi_' . str_replace(' ', '_', $attendance->user->name) . '_' . $attendance->date . '.' . $extension;

        return \Illuminate\Support\Facades\Storage::disk('local')->download($attendance->attachment, $cleanName);
    }

    /**
     * Preview lampiran absensi secara aman
     */
    public function previewAttachment(Attendance $attendance)
    {
        // Otorisasi: Hanya admin, pimpinan, atau pemilik absensi yang boleh melihat
        if (Auth::user()->role !== 'admin' && Auth::user()->role !== 'pimpinan' && Auth::id() !== $attendance->user_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat lampiran ini.');
        }

        if (empty($attendance->attachment) || !\Illuminate\Support\Facades\Storage::disk('local')->exists($attendance->attachment)) {
            return response('<div style="text-align:center; padding: 50px; font-family: sans-serif; color: #666;"><h3>File Tidak Ditemukan</h3><p>File fisik tidak ada di server.</p></div>', 404);
        }

        $path = \Illuminate\Support\Facades\Storage::disk('local')->path($attendance->attachment);
        return response()->file($path);
    }
}
