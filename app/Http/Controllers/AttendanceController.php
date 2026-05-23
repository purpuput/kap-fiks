<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;

class AttendanceController extends Controller
{
    /**
     * Menampilkan halaman data absensi
     */
    public function index(Request $request)
    {
        // Mengambil filter tanggal dari request
        $date = $request->input('date');

        // Mengambil filter user dari request
        $user_id = $request->input('user_id');

        // Mengambil data user yang sedang login
        $user = Auth::user();

        // Query awal attendance beserta relasi user
        $query = Attendance::with('user');

        /**
         * Filter berdasarkan role
         * - Admin dan pimpinan dapat melihat semua data
         * - Auditor hanya dapat melihat data dirinya sendiri
         */
        if (in_array($user->role, ['admin', 'pimpinan']) && $user_id) {

            $query->where('user_id', $user_id);
        } elseif ($user->role === 'auditor') {

            $query->where('user_id', Auth::id());
        }

        /**
         * Filter berdasarkan tanggal
         */
        if ($date) {
            $query->whereDate('date', $date);
        }

        // Mengambil data absensi dengan pagination
        $attendances = $query
            ->orderBy('date', 'desc')
            ->paginate(15);

        // Mengambil daftar auditor dan admin
        $users = User::whereIn('role', ['admin', 'auditor'])
            ->orderBy('name')
            ->get();

        // Menampilkan halaman index
        return view('attendances.index', compact(
            'attendances',
            'date',
            'users',
            'user_id'
        ));
    }

    /**
     * Export data absensi ke PDF
     */
    public function exportPdf(Request $request)
    {
        // Hanya admin yang boleh export PDF
        abort_unless(Auth::user()->role === 'admin', 403);

        // Query data attendance
        $query = Attendance::with('user');

        // Filter tanggal
        if ($request->date) {
            $query->whereDate('date', $request->date);
        }

        // Filter user
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Mengambil data absensi
        $attendances = $query
            ->orderBy('date', 'desc')
            ->get();

        // Load view PDF
        $pdf = Pdf::loadView(
            'attendances.pdf',
            compact('attendances')
        );

        // Download PDF
        return $pdf->download('laporan-absensi.pdf');
    }

    /**
     * Export data absensi ke Excel
     */
    public function exportExcel(Request $request)
    {
        // Hanya admin yang boleh export Excel
        abort_unless(Auth::user()->role === 'admin', 403);

        // Query data attendance
        $query = Attendance::with('user');

        // Filter tanggal
        if ($request->date) {
            $query->whereDate('date', $request->date);
        }

        // Filter user
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        /**
         * Mapping data agar format Excel lebih rapi
         */
        $attendances = collect(
            $query->get()->map(function ($item) {

                return [
                    'Auditor'     => $item->user->name ?? '-',
                    'Tanggal'     => $item->date,
                    'Check In'    => $item->check_in,
                    'Check Out'   => $item->check_out,
                    'Status'      => ucfirst($item->status),
                    'Keterangan'  => $item->notes,
                ];
            })
        );

        // Download file Excel
        return Excel::download(
            new AttendanceExport($attendances),
            'laporan-absensi.xlsx'
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

        // Tanggal hari ini
        $today = now()->toDateString();

        // Mengambil daftar auditor dan admin
        $users = User::whereIn('role', ['admin', 'auditor'])
            ->orderBy('name')
            ->get();

        /**
         * Mengecek apakah user sudah absen hari ini
         */
        $todayAttendance = Attendance::where('user_id', Auth::id())
            ->where('date', $today)
            ->first();

        // Menampilkan halaman create
        return view('attendances.create', compact(
            'users',
            'todayAttendance'
        ));
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
            'tipe' => 'required|in:Masuk,Pulang',

            // Status absensi
            'status' => 'required|in:hadir,izin,sakit,alpha',

            // Catatan tambahan
            'notes' => 'nullable|string',
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
                    'tipe' => 'Absensi hari ini sudah lengkap.'
                ]);
        }

        /**
         * Validasi agar tidak bisa absen pulang
         * sebelum melakukan absen masuk
         */
        if (
            $validated['tipe'] === 'Pulang' &&
            (!$attendance || !$attendance->check_in)
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'tipe' => 'Absensi masuk harus dilakukan terlebih dahulu.'
                ]);
        }

        /**
         * Data yang akan disimpan
         */
        $data = [
            'user_id' => $validated['user_id'],
            'date'    => $validated['date'],
            'status'  => $validated['status'],
            'notes'   => $validated['notes'] ?? null,
        ];

        /**
         * Jika status hadir maka simpan jam absensi
         */
        if ($validated['status'] === 'hadir') {

            /**
             * Absensi masuk
             */
            if ($validated['tipe'] === 'Masuk') {

                // Menyimpan jam masuk saat ini
                $data['check_in'] = now()->format('H:i:s');
            } else {

                /**
                 * Absensi pulang
                 */

                // Menyimpan jam pulang saat ini
                $data['check_out'] = now()->format('H:i:s');
            }
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
     * Menghapus data absensi
     */
    public function destroy(Attendance $attendance)
    {
        // Hanya admin yang boleh menghapus
        abort_unless(Auth::user()->role === 'admin', 403);

        // Menghapus data
        $attendance->delete();

        // Redirect kembali
        return redirect()
            ->route('attendances.index')
            ->with('success', 'Absensi berhasil dihapus');
    }
}
