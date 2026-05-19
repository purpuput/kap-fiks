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
         * Data auditor untuk filter
         */
        $users = User::where('role', 'auditor')
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
         * Hanya auditor yang bisa tambah logbook
         */
        if (Auth::user()->role !== 'auditor') {
            abort(403);
        }

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
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'team_role' => 'required|in:ketua tim,anggota tim',
            'date' => 'required|date|before_or_equal:today',
            'audit_stage' => 'required|string',
            'audit_area' => 'required|string',
            'cumulative_progress' => 'required|integer|min:1|max:100',
            'main_task' => 'required|string',
            'has_obstacle' => 'required|in:0,1',
            'obstacle_type' => 'nullable|required_if:has_obstacle,1|string',
            'other_obstacle_type' => 'nullable|required_if:obstacle_type,Lainnya|string',
            'obstacle_description' => 'nullable|string',
            'help_needed' => 'nullable|required_if:has_obstacle,1|string',
            'next_plan' => 'required|string',
        ]);

        /**
         * Pastikan klien memang di-handle oleh user
         */
        $isAssigned = Auth::user()->clients()->where('clients.id', $request->client_id)->exists();
        if (!$isAssigned) {
            return back()->withInput()->with('error', 'Anda tidak ditugaskan pada klien ini.');
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
        Logbook::create([
            'user_id' => Auth::id(),
            'client_id' => $validated['client_id'],
            'team_role' => $validated['team_role'],
            'date' => $validated['date'],
            'audit_stage' => $validated['audit_stage'],
            'audit_area' => $validated['audit_area'],
            'cumulative_progress' => $validated['cumulative_progress'],
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
        $clients = User::find($logbook->user_id)
            ->clients()
            ->where('approval_status', 'disetujui')
            ->where('status', 'aktif')
            ->get();

        $clientRoles = $clients->mapWithKeys(function ($client) {
            $role = $client->pivot->team_role ?? 'anggota';
            return [$client->id => $role === 'ketua' ? 'ketua tim' : 'anggota tim'];
        });

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
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'team_role' => 'required|in:ketua tim,anggota tim',
            'date' => 'required|date',
            'audit_stage' => 'required|string',
            'audit_area' => 'required|string',
            'cumulative_progress' => 'required|integer|min:1|max:100',
            'main_task' => 'required|string',
            'has_obstacle' => 'required|in:0,1',
            'obstacle_type' => 'nullable|required_if:has_obstacle,1|string',
            'other_obstacle_type' => 'nullable|required_if:obstacle_type,Lainnya|string',
            'obstacle_description' => 'nullable|string',
            'help_needed' => 'nullable|required_if:has_obstacle,1|string',
            'next_plan' => 'required|string',
        ]);

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
        $logbook->update([
            'client_id' => $validated['client_id'],
            'team_role' => $validated['team_role'],
            'date' => $validated['date'],
            'audit_stage' => $validated['audit_stage'],
            'audit_area' => $validated['audit_area'],
            'cumulative_progress' => $validated['cumulative_progress'],
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

        $logbook->delete();

        return redirect()
            ->route('logbooks.index')
            ->with('success', 'Logbook berhasil dihapus');
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

        return back()->with(
            'success',
            'Revisi berhasil dikirim'
        );
    }
}
