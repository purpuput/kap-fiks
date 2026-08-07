<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogbookController extends Controller
{
    /**
     * Menampilkan daftar logbook
     */
    public function index(Request $request)
    {
        $query = Logbook::with(['user', 'client']);

        /**
         * Auditor hanya melihat logbook miliknya
         */
        if (Auth::user()->role === 'auditor') {
            $query->where('user_id', Auth::id());
        }

        /**
         * Filter tanggal
         */
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        /**
         * Filter auditor
         */
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        /**
         * Data logbook
         */
        $logbooks = $query
            ->latest('date')
            ->paginate(10)
            ->withQueryString();

        /**
         * Data pegawai untuk filter
         */
        $users = User::whereIn('role', ['auditor', 'admin'])
            ->orderBy('name')
            ->get();

        return view('logbooks.index', compact(
            'logbooks',
            'users'
        ));
    }

    /**
     * Detail logbook
     */
    public function show(Logbook $logbook)
    {
        /**
         * Proteksi:
         * - admin dan pimpinan boleh melihat semua
         * - auditor hanya bisa melihat miliknya
         */
        if (
            Auth::user()->role === 'auditor' &&
            $logbook->user_id !== Auth::id()
        ) {
            abort(403);
        }

        $logbook->load(['user', 'client']);

        return view('logbooks.show', compact('logbook'));
    }

    /**
     * Form tambah logbook
     */
    public function create()
    {
        /**
         * Admin dan auditor bisa tambah logbook
         */
        if (!in_array(Auth::user()->role, ['admin', 'auditor'])) {
            abort(403);
        }

        if (Auth::user()->role === 'admin') {
            $clients = collect(); // Admin tidak butuh klien
            $clientRoles = collect();
        } else {
            /**
             * Klien yang di-handle oleh auditor beserta perannya
             */
            $clients = Auth::user()
                ->clients()
                ->where('approval_status', 'disetujui')
                ->where('status', 'aktif')
                ->get();

            $clientRoles = $clients->mapWithKeys(function ($client) {
                $role = $client->pivot->team_role ?? 'anggota';
                return [$client->id => $role === 'ketua' ? 'ketua tim' : 'anggota tim'];
            });
        }

        return view('logbooks.create', compact('clients', 'clientRoles'));
    }

    /**
     * Simpan logbook
     */
    public function store(Request $request)
    {
        /**
         * Validasi
         */
        $isAdmin = Auth::user()->role === 'admin';

        $rules = [
            'date' => 'required|date|before_or_equal:today',
            'main_task' => 'required|string',
            'has_obstacle' => 'required|in:0,1',
            'obstacle_type' => 'nullable|required_if:has_obstacle,1|string',
            'other_obstacle_type' => 'nullable|required_if:obstacle_type,Lainnya|string',
            'obstacle_description' => 'nullable|string',
            'help_needed' => 'nullable|required_if:has_obstacle,1|string',
            'next_plan' => 'required|string',
        ];

        if (!$isAdmin) {
            $rules['client_id'] = 'required|exists:clients,id';
            $rules['team_role'] = 'required|in:ketua tim,anggota tim';
            $rules['audit_stage'] = 'required|string';
            $rules['audit_area'] = 'required|string';
            $rules['cumulative_progress'] = 'required|integer|min:1|max:100';
        }

        $validated = $request->validate($rules);

        /**
         * Pastikan klien memang di-handle oleh user (untuk auditor)
         */
        if (!$isAdmin) {
            $isAssigned = Auth::user()->clients()->where('clients.id', $request->client_id)->exists();
            if (!$isAssigned) {
                return back()->withInput()->with('error', 'Anda tidak ditugaskan pada klien ini.');
            }
        }

        $finalObstacleType = null;
        if ($validated['has_obstacle'] == 1) {
            if ($validated['obstacle_type'] === 'Lainnya') {
                $finalObstacleType = $validated['other_obstacle_type'];
            } else {
                $finalObstacleType = $validated['obstacle_type'];
            }
        }

        /**
         * Simpan logbook
         */
        try {
            Logbook::create([
                'user_id' => Auth::id(),
                'client_id' => $isAdmin ? null : $validated['client_id'],
                'team_role' => $isAdmin ? null : $validated['team_role'],
                'date' => $validated['date'],
                'audit_stage' => $isAdmin ? null : $validated['audit_stage'],
                'audit_area' => $isAdmin ? null : $validated['audit_area'],
                'cumulative_progress' => $isAdmin ? null : $validated['cumulative_progress'],
                'main_task' => $validated['main_task'],
                'has_obstacle' => $validated['has_obstacle'] == 1,
                'obstacle_type' => $finalObstacleType,
                'obstacle_description' => $validated['has_obstacle'] == 1 ? $validated['obstacle_description'] : null,
                'help_needed' => $validated['has_obstacle'] == 1 ? $validated['help_needed'] : null,
                'next_plan' => $validated['next_plan'],
                'status' => 'pending',
            ]);

            return redirect()
                ->route('logbooks.index')
                ->with('success', 'Logbook berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan logbook: Terjadi kesalahan sistem.');
        }
    }

    /**
     * Form edit logbook
     */
    public function edit(Logbook $logbook)
    {
        /**
         * Proteksi:
         * - admin boleh edit semua
         * - auditor hanya edit miliknya
         */
        if (
            Auth::user()->role !== 'admin' &&
            $logbook->user_id !== Auth::id()
        ) {
            abort(403);
        }

        /**
         * Auditor tidak bisa edit jika sudah approved
         */
        if (Auth::user()->role === 'auditor') {

            // Tidak bisa edit jika sudah approved/revision
            if ($logbook->status !== 'pending') {

                return redirect()
                    ->route('logbooks.index')
                    ->with('error', 'Logbook tidak dapat diedit');
            }

            // Batas 24 jam
            if ($logbook->created_at->addDay()->isPast()) {

                return redirect()
                    ->route('logbooks.index')
                    ->with('error', 'Batas edit logbook hanya 24 jam');
            }
        }

        /**
         * Klien yang di-handle oleh auditor
         */
        if ($logbook->user->role === 'admin') {
            $clients = collect();
            $clientRoles = collect();
        } else {
            $clients = User::find($logbook->user_id)
                ->clients()
                ->where('approval_status', 'disetujui')
                ->where('status', 'aktif')
                ->get();

            $clientRoles = $clients->mapWithKeys(function ($client) {
                $role = $client->pivot->team_role ?? 'anggota';
                return [$client->id => $role === 'ketua' ? 'ketua tim' : 'anggota tim'];
            });
        }

        return view('logbooks.edit', compact('logbook', 'clients', 'clientRoles'));
    }

    /**
     * Update logbook
     */
    public function update(Request $request, Logbook $logbook)
    {
        /**
         * Proteksi
         */
        if (
            Auth::user()->role !== 'admin' &&
            $logbook->user_id !== Auth::id()
        ) {
            abort(403);
        }

        /**
         * Validasi
         */
        $isAdminLogbook = $logbook->user->role === 'admin';

        $rules = [
            'date' => 'required|date',
            'main_task' => 'required|string',
            'has_obstacle' => 'required|in:0,1',
            'obstacle_type' => 'nullable|required_if:has_obstacle,1|string',
            'other_obstacle_type' => 'nullable|required_if:obstacle_type,Lainnya|string',
            'obstacle_description' => 'nullable|string',
            'help_needed' => 'nullable|required_if:has_obstacle,1|string',
            'next_plan' => 'required|string',
        ];

        if (!$isAdminLogbook) {
            $rules['client_id'] = 'required|exists:clients,id';
            $rules['team_role'] = 'required|in:ketua tim,anggota tim';
            $rules['audit_stage'] = 'required|string';
            $rules['audit_area'] = 'required|string';
            $rules['cumulative_progress'] = 'required|integer|min:1|max:100';
        }

        $validated = $request->validate($rules);

        $finalObstacleType = null;
        if ($validated['has_obstacle'] == 1) {
            if ($validated['obstacle_type'] === 'Lainnya') {
                $finalObstacleType = $validated['other_obstacle_type'];
            } else {
                $finalObstacleType = $validated['obstacle_type'];
            }
        }

        /**
         * Update data
         */
        try {
            $logbook->update([
                'client_id' => $isAdminLogbook ? null : $validated['client_id'],
                'team_role' => $isAdminLogbook ? null : $validated['team_role'],
                'date' => $validated['date'],
                'audit_stage' => $isAdminLogbook ? null : $validated['audit_stage'],
                'audit_area' => $isAdminLogbook ? null : $validated['audit_area'],
                'cumulative_progress' => $isAdminLogbook ? null : $validated['cumulative_progress'],
                'main_task' => $validated['main_task'],
                'has_obstacle' => $validated['has_obstacle'] == 1,
                'obstacle_type' => $finalObstacleType,
                'obstacle_description' => $validated['has_obstacle'] == 1 ? $validated['obstacle_description'] : null,
                'help_needed' => $validated['has_obstacle'] == 1 ? $validated['help_needed'] : null,
                'next_plan' => $validated['next_plan'],

                /**
                 * Jika direvisi lalu diedit auditor,
                 * status kembali pending
                 */
                'status' => 'pending',
                'note' => null,
            ]);

            return redirect()
                ->route('logbooks.index')
                ->with('success', 'Logbook berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui logbook: Terjadi kesalahan sistem.');
        }
    }

    /**
     * Hapus logbook
     */
    public function destroy(Logbook $logbook)
    {
        /**
         * Hanya admin yang bisa hapus
         */
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        try {
            $logbook->delete();

            return redirect()
                ->route('logbooks.index')
                ->with('success', 'Logbook berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus logbook karena data sedang digunakan.');
        }
    }

    /**
     * Approve logbook
     */
    public function approve(Logbook $logbook)
    {
        /**
         * Hanya admin & pimpinan
         */
        if (
            !in_array(Auth::user()->role, ['admin', 'pimpinan'])
        ) {
            abort(403);
        }

        $logbook->update([
            'status' => 'approved',
            'note' => null,
        ]);

        /**
         * Kirim notifikasi ke auditor pemilik logbook
         */
        try {
            if ($logbook->user) {
                $logbook->user->notify(new \App\Notifications\LogbookReviewedNotification(
                    $logbook->fresh(),
                    'approved',
                    Auth::user()->name
                ));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim notifikasi logbook approve: ' . $e->getMessage());
        }

        return back()->with(
            'success',
            'Logbook berhasil disetujui'
        );
    }

    /**
     * Revisi logbook
     */
    public function revision(Request $request, Logbook $logbook)
    {
        /**
         * Hanya admin & pimpinan
         */
        if (
            !in_array(Auth::user()->role, ['admin', 'pimpinan'])
        ) {
            abort(403);
        }

        /**
         * Validasi
         */
        $request->validate([
            'note' => 'required|string',
        ]);

        /**
         * Update revisi
         */
        $logbook->update([
            'status' => 'revision',
            'note' => $request->note,
        ]);

        /**
         * Kirim notifikasi ke auditor pemilik logbook
         */
        try {
            if ($logbook->user) {
                $logbook->user->notify(new \App\Notifications\LogbookReviewedNotification(
                    $logbook->fresh(),
                    'revision',
                    Auth::user()->name
                ));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim notifikasi logbook revision: ' . $e->getMessage());
        }

        return back()->with(
            'success',
            'Revisi berhasil dikirim'
        );
    }
}
