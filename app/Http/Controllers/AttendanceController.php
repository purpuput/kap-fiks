<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $user_id = $request->input('user_id');

        $query = Attendance::query();

        if ($user_id) {
            $query->where('user_id', $user_id);
        } else {
            $query->where('user_id', Auth::id());
        }

        $attendances = $query->whereYear('date', substr($month, 0, 4))
            ->whereMonth('date', substr($month, 5, 2))
            ->orderBy('date', 'desc')
            ->paginate(15);

        $users = User::all();

        return view('attendances.index', compact('attendances', 'month', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('attendances.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:hadir,izin,sakit,alpha',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();

        Attendance::create($validated);

        return redirect()->route('attendances.index')->with('success', 'Absensi berhasil dicatat');
    }

    /**
     * Check in
     */
    public function checkIn()
    {
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            $attendance = Attendance::create([
                'user_id' => Auth::id(),
                'date' => $today,
                'check_in' => now()->toTimeString(),
                'status' => 'hadir',
            ]);
        } else {
            $attendance->update(['check_in' => now()->toTimeString()]);
        }

        return back()->with('success', 'Check-in berhasil');
    }

    /**
     * Check out
     */
    public function checkOut()
    {
        $today = now()->toDateString();

        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', $today)
            ->first();

        if ($attendance) {
            $attendance->update(['check_out' => now()->toTimeString()]);
        }

        return back()->with('success', 'Check-out berhasil');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        return view('attendances.edit', compact('attendance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:hadir,izin,sakit,alpha',
            'notes' => 'nullable|string',
        ]);

        $attendance->update($validated);

        return redirect()->route('attendances.index')->with('success', 'Absensi berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('attendances.index')->with('success', 'Absensi berhasil dihapus');
    }
}
