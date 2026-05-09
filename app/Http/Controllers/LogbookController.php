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
        $query = Logbook::with('user');

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

        return view('logbooks.create');
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
            'date' => 'required|date|before_or_equal:today',
            'activity' => 'required|string',
        ]);

        /**
         * Simpan logbook
         */
        Logbook::create([
            'user_id' => Auth::id(),
            'date' => $validated['date'],
            'activity' => $validated['activity'],
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
        if (
            Auth::user()->role === 'auditor' &&
            $logbook->status !== 'pending'
        ) {
            return redirect()
                ->route('logbooks.index')
                ->with('error', 'Logbook tidak dapat diedit');
        }

        return view('logbooks.edit', compact('logbook'));
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
            'date' => 'required|date',
            'activity' => 'required|string',
        ]);

        /**
         * Update data
         */
        $logbook->update([
            'date' => $validated['date'],
            'activity' => $validated['activity'],

            /**
             * Jika direvisi lalu diedit auditor,
             * status kembali pending
             */
            'status' => 'pending',
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
         * Proteksi:
         * - admin boleh hapus semua
         * - auditor hanya hapus miliknya
         */
        if (
            Auth::user()->role !== 'admin' &&
            $logbook->user_id !== Auth::id()
        ) {
            abort(403);
        }

        /**
         * Auditor tidak bisa hapus jika sudah approved
         */
        if (
            Auth::user()->role === 'auditor' &&
            $logbook->status !== 'pending'
        ) {
            return redirect()
                ->route('logbooks.index')
                ->with('error', 'Logbook tidak dapat dihapus');
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
