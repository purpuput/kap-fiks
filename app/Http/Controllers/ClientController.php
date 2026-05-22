<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    // List client
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->status;

        // Query berdasarkan role
        if (Auth::check() && Auth::user()->role === 'auditor') {

            /** @var \App\Models\User $user */
            $user = Auth::user();

            $query = $user->clients()
                ->where('approval_status', 'disetujui');
        } else {

            $query = Client::query();
        }

        // Search client
        if ($search) {

            $query->where(function ($q) use ($search) {

                $q->where('institution_name', 'like', "%{$search}%")
                    ->orWhere('leader_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($status) {

            $query->where('status', $status);
        }

        $clients = $query
            ->latest()
            ->paginate(10);

        return view('clients.index', compact(
            'clients',
            'search',
            'status'
        ));
    }

    // Form tambah client
    public function create()
    {
        if (
            Auth::guest() ||
            Auth::user()->role !== 'admin'
        ) {
            abort(403);
        }

        $auditors = User::where('role', 'auditor')->get();

        return view('clients.create', compact('auditors'));
    }

    // Simpan client
    public function store(Request $request)
    {
        if (
            Auth::guest() ||
            Auth::user()->role !== 'admin'
        ) {
            abort(403);
        }

        $validated = $request->validate([

            'institution_type' => 'required|in:Pemerintahan,Swasta',

            'audit_type' => 'required|in:keuangan,kepatuhan',

            'institution_name' => 'required|string|max:255',

            'leader_name' => 'required|string|max:255',

            'address' => 'required|string',

            'city' => 'required|string|max:100',

            'province' => 'required|string|max:100',

            'postal_code' => 'required|string|max:10',

            'phone' => 'required|string|max:20',

            'email' => 'nullable|email',

            'npwp' => 'nullable|string|max:50',

            'contract_start' => 'nullable|date',

            'contract_end' => 'nullable|date',

            'auditors' => 'required|array',

            'auditors.*' => 'exists:users,id',

            'team_role' => 'required|array',
        ]);

        // Admin pembuat
        $validated['created_by'] = Auth::id();

        // Create client
        $client = Client::create($validated);

        // Assign auditor
        foreach ($request->auditors as $auditorId) {

            $client->auditors()->attach($auditorId, [

                'team_role' =>
                $request->team_role[$auditorId] ?? 'anggota'

            ]);
        }

        return redirect()
            ->route('clients.index')
            ->with('success', 'Data klien berhasil ditambahkan');
    }

    // Detail client
    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    // Form edit client
    public function edit(Client $client)
    {
        if (
            Auth::guest() ||
            Auth::user()->role !== 'admin'
        ) {
            abort(403);
        }

        $auditors = User::where('role', 'auditor')->get();

        return view('clients.edit', compact(
            'client',
            'auditors'
        ));
    }

    // Update client
    public function update(Request $request, Client $client)
    {
        if (
            Auth::guest() ||
            Auth::user()->role !== 'admin'
        ) {
            abort(403);
        }

        $validated = $request->validate([

            'institution_type' => 'required|in:Pemerintahan,Swasta',

            'audit_type' => 'required|in:keuangan,kepatuhan',

            'institution_name' => 'required|string|max:255',

            'leader_name' => 'required|string|max:255',

            'address' => 'required|string',

            'city' => 'required|string|max:100',

            'province' => 'required|string|max:100',

            'postal_code' => 'required|string|max:10',

            'phone' => 'required|string|max:20',

            'email' => 'nullable|email',

            'npwp' => 'nullable|string|max:50',

            'status' => 'required|in:aktif,nonaktif',

            'contract_start' => 'nullable|date',

            'contract_end' => 'nullable|date',

            'auditors' => 'required|array',

            'auditors.*' => 'exists:users,id',

            'team_role' => 'required|array',
        ]);

        // Reset approval
        $validated['approval_status'] = 'pending';

        $validated['approved_by'] = null;

        $validated['approved_at'] = null;

        $validated['revision_note'] = null;

        // Update client
        $client->update($validated);

        // Reset auditor
        $client->auditors()->detach();

        // Assign ulang auditor
        foreach ($request->auditors as $auditorId) {

            $client->auditors()->attach($auditorId, [

                'team_role' =>
                $request->team_role[$auditorId] ?? 'anggota'

            ]);
        }

        return redirect()
            ->route('clients.index')
            ->with('success', 'Data klien berhasil diperbarui');
    }

    // Hapus client
    public function destroy(Client $client)
    {
        if (
            Auth::guest() ||
            Auth::user()->role !== 'admin'
        ) {
            abort(403);
        }

        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', 'Data klien berhasil dihapus');
    }

    // Approve client
    public function approve(Client $client)
    {
        $client->update([

            'approval_status' => 'disetujui',

            'approved_by' => Auth::user()->id,

            'approved_at' => now(),

            // hapus revisi lama
            'revision_note' => null,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Client berhasil disetujui.');
    }

    // Revisi client
    public function revision(Request $request, Client $client)
    {
        // Validasi
        $request->validate([
            'revision_note' => 'required|string'
        ]);

        // Update revisi
        $client->update([

            'approval_status' => 'revisi',

            'revision_note' => $request->revision_note,

            // reset approval
            'approved_by' => null,

            'approved_at' => null,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Revisi berhasil dikirim.');
    }
}
