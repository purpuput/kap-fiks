<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $status = $request->status;

        $query = Client::query();

        if ($search) {
            $query->where(function ($q) use ($search) {

                $q->where('institution_name', 'like', "%{$search}%")
                    ->orWhere('leader_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

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

    public function create()
    {
        if (Auth::guest() || !in_array(Auth::user()->role, [
            'admin',
            'auditor'
        ])) {

            abort(403);
        }

        return view('clients.create');
    }

    public function store(Request $request)
    {
        if (
            Auth::guest() ||
            !in_array(Auth::user()->role, ['admin', 'auditor'])
        ) {
            abort(403);
        }

        $validated = $request->validate([

            'institution_type' => 'required|in:Pemerintahan,Swasta',

            'institution_name' => 'required|string|max:255',

            'leader_name' => 'required|string|max:255',

            'address' => 'required|string',

            'city' => 'required|string|max:100',

            'province' => 'required|string|max:100',

            'postal_code' => 'required|string|max:10',

            'phone' => 'required|string|max:20',

            'email' => 'nullable|email',

            'npwp' => 'nullable|string|max:50',

            'description' => 'nullable|string',

        ]);

        // AUTO AMBIL USER LOGIN
        $validated['user_id'] = Auth::id();

        Client::create($validated);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Data klien berhasil ditambahkan');
    }

    public function show(Client $client)
    {
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        if (Auth::guest() || !in_array(Auth::user()->role, [
            'admin',
            'auditor'
        ])) {

            abort(403);
        }

        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        // ROLE
        if (
            Auth::guest() ||
            !in_array(Auth::user()->role, ['admin', 'auditor'])
        ) {
            abort(403);
        }

        // VALIDASI
        $validated = $request->validate([

            'institution_type' => 'required|in:Pemerintahan,Swasta',

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

            'description' => 'nullable|string',

        ]);

        // RESET APPROVAL SAAT DIEDIT
        $validated['approval_status'] = 'pending';

        $validated['approved_by'] = null;

        $validated['approved_at'] = null;

        $validated['revision_note'] = null;

        // UPDATE MANUAL
        $client->institution_type = $validated['institution_type'];

        $client->institution_name = $validated['institution_name'];

        $client->leader_name = $validated['leader_name'];

        $client->address = $validated['address'];

        $client->city = $validated['city'];

        $client->province = $validated['province'];

        $client->postal_code = $validated['postal_code'];

        $client->phone = $validated['phone'];

        $client->email = $validated['email'];

        $client->npwp = $validated['npwp'];

        $client->status = $validated['status'];

        $client->description = $validated['description'];

        $client->approval_status = 'pending';

        $client->approved_by = null;

        $client->approved_at = null;

        $client->revision_note = null;

        $client->save();

        return redirect()
            ->route('clients.index')
            ->with('success', 'Data klien berhasil diperbarui');
    }

    public function destroy(Client $client)
    {
        if (Auth::guest() || !in_array(Auth::user()->role, [
            'admin',
            'auditor'
        ])) {

            abort(403);
        }

        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', 'Data klien berhasil dihapus');
    }

    // APPROVE CLIENT
    public function approve(Client $client)
    {
        if (Auth::guest() || Auth::user()->role !== 'pimpinan') {
            abort(403);
        }

        $client->update([
            'approval_status' => 'disetujui',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'revision_note' => null,
        ]);

        return back()->with(
            'success',
            'Data klien berhasil disetujui'
        );
    }

    // REVISI CLIENT
    public function revision(Request $request, Client $client)
    {
        if (Auth::guest() || Auth::user()->role !== 'pimpinan') {
            abort(403);
        }

        $request->validate([
            'revision_note' => 'required|string'
        ]);

        $client->update([
            'approval_status' => 'revisi',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'revision_note' => $request->revision_note,
        ]);

        return back()->with(
            'success',
            'Revisi berhasil diberikan'
        );
    }
}
