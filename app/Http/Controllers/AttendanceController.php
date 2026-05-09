<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;

class AttendanceController extends Controller
{
    private const ATTENDANCE_TIMEZONE = 'Asia/Jakarta';
    private const CHECK_IN_START = '07:30';
    private const CHECK_IN_END = '08:30';
    private const CHECK_OUT_START = '16:30';
    private const CHECK_OUT_END = '17:30';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $attendanceWindow = $this->attendanceWindow();

        $date = $request->input('date');
        $user_id = $request->input('user_id');
        $user = Auth::user();

        $query = Attendance::with('user');

        // Filter role
        if (in_array($user->role, ['admin', 'pimpinan']) && $user_id) {
            $query->where('user_id', $user_id);
        } elseif ($user->role === 'auditor') {
            $query->where('user_id', Auth::id());
        }

        // Filter tanggal
        if ($date) {
            $query->whereDate('date', $date);
        }

        $attendances = $query
            ->orderBy('date', 'desc')
            ->paginate(15);

        $users = User::where('role', 'auditor')
            ->orderBy('name')
            ->get();

        return view('attendances.index', compact(
            'attendances',
            'date',
            'users',
            'user_id',
            'attendanceWindow'
        ));
    }

    /**
     * Export PDF
     */
    public function exportPdf(Request $request)
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        $query = Attendance::with('user');

        if ($request->date) {
            $query->whereDate('date', $request->date);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $attendances = $query
            ->orderBy('date', 'desc')
            ->get();

        $pdf = Pdf::loadView(
            'attendances.pdf',
            compact('attendances')
        );

        return $pdf->download('laporan-absensi.pdf');
    }

    /**
     * Export Excel
     */
    public function exportExcel(Request $request)
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        $query = Attendance::with('user');

        if ($request->date) {
            $query->whereDate('date', $request->date);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $attendances = collect(
            $query->get()->map(function ($item) {
                return [
                    'Auditor' => $item->user->name ?? '-',
                    'Tanggal' => $item->date,
                    'Check In' => $item->check_in,
                    'Check Out' => $item->check_out,
                    'Status' => ucfirst($item->status),
                    'Keterangan' => $item->notes,
                ];
            })
        );

        return Excel::download(
            new AttendanceExport($attendances),
            'laporan-absensi.xlsx'
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(
            in_array(Auth::user()->role, ['admin', 'auditor']),
            403,
            'Akses ditolak!'
        );

        $attendanceWindow = $this->attendanceWindow();
        $today = $attendanceWindow['now']->toDateString();

        $users = User::where('role', 'auditor')
            ->orderBy('name')
            ->get();

        $todayAttendance = Attendance::where('user_id', Auth::id())
            ->where('date', $today)
            ->first();

        return view('attendances.create', compact(
            'users',
            'todayAttendance',
            'attendanceWindow'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort_unless(
            in_array(Auth::user()->role, ['admin', 'auditor']),
            403,
            'Akses ditolak!'
        );

        $attendanceWindow = $this->attendanceWindow();

        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'date' => 'required|date',
            'tipe' => 'required|in:Masuk,Pulang',
            'status' => 'required|in:hadir,izin,sakit,alpha',
            'notes' => 'nullable|string',
            'photo_data' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::user()->role === 'admin'
            ? ($validated['user_id'] ?? Auth::id())
            : Auth::id();

        $validated['date'] = $attendanceWindow['now']->toDateString();

        if (!$attendanceWindow['can_attend']) {
            return back()
                ->withInput()
                ->withErrors([
                    'tipe' => $attendanceWindow['message']
                ]);
        }

        if ($validated['tipe'] !== $attendanceWindow['type']) {
            return back()
                ->withInput()
                ->withErrors([
                    'tipe' => 'Tipe absensi tidak sesuai dengan jam WIB saat ini.'
                ]);
        }

        $attendance = Attendance::where('user_id', $validated['user_id'])
            ->where('date', $validated['date'])
            ->first();

        if ($attendance && $attendance->check_in && $attendance->check_out) {
            return back()
                ->withInput()
                ->withErrors([
                    'tipe' => 'Absensi hari ini sudah lengkap.'
                ]);
        }

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

        if (
            $validated['status'] === 'hadir' &&
            !$request->filled('photo_data')
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'photo_data' => 'Foto absensi wajib diambil.'
                ]);
        }

        $photoPath = $validated['status'] === 'hadir'
            ? $this->storeCapturedPhoto(
                $validated['photo_data'],
                $validated['user_id'],
                $validated['tipe']
            )
            : null;

        $data = [
            'user_id' => $validated['user_id'],
            'date' => $validated['date'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ];

        if ($validated['status'] === 'hadir') {

            if ($validated['tipe'] === 'Masuk') {

                $data['check_in'] = $attendanceWindow['now']
                    ->format('H:i:s');

                $data['photo_check_in'] = $photoPath;
            } else {

                $data['check_out'] = $attendanceWindow['now']
                    ->format('H:i:s');

                $data['photo_check_out'] = $photoPath;
            }
        }

        if ($attendance) {
            $attendance->update($data);
        } else {
            Attendance::create($data);
        }

        return redirect()
            ->route('attendances.index')
            ->with('success', 'Absensi berhasil dicatat');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        abort_unless(Auth::user()->role === 'admin', 403);

        $attendance->delete();

        return redirect()
            ->route('attendances.index')
            ->with('success', 'Absensi berhasil dihapus');
    }

    /**
     * Store captured photo
     */
    private function storeCapturedPhoto(
        string $photoData,
        int $userId,
        string $type
    ): string {

        if (!preg_match(
            '/^data:image\/(png|jpe?g);base64,/',
            $photoData,
            $matches
        )) {
            abort(422, 'Format foto tidak valid.');
        }

        $extension = $matches[1] === 'jpeg'
            ? 'jpg'
            : $matches[1];

        $image = base64_decode(
            substr($photoData, strpos($photoData, ',') + 1),
            true
        );

        if ($image === false) {
            abort(422, 'Foto tidak bisa diproses.');
        }

        $fileName =
            'attendances/' .
            $userId . '-' .
            Str::slug($type) . '-' .
            now(self::ATTENDANCE_TIMEZONE)->format('YmdHis') .
            '.' . $extension;

        Storage::disk('public')->put($fileName, $image);

        return $fileName;
    }

    /**
     * Attendance window
     */
    private function attendanceWindow(): array
    {
        $now = now(self::ATTENDANCE_TIMEZONE);
        $time = $now->format('H:i');
        $type = null;

        if ($time >= self::CHECK_IN_START && $time <= self::CHECK_IN_END) {
            $type = 'Masuk';
        } elseif (
            $time >= self::CHECK_OUT_START &&
            $time <= self::CHECK_OUT_END
        ) {
            $type = 'Pulang';
        }

        return [
            'now' => $now,
            'type' => $type,
            'can_attend' => $type !== null,
            'timezone_label' => 'WIB',
            'check_in_start' => self::CHECK_IN_START,
            'check_in_end' => self::CHECK_IN_END,
            'check_out_start' => self::CHECK_OUT_START,
            'check_out_end' => self::CHECK_OUT_END,
            'message' =>
            'Absensi hanya bisa dilakukan pukul 07:30-08:30 WIB untuk masuk dan 16:30-17:30 WIB untuk pulang.',
        ];
    }
}
