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

        $query->withCount('documentManagements');

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

        $auditors = User::query()
            ->where('role', 'auditor')
            ->withCount(['clients' => function ($q) {
                $q->where('status', 'aktif');
            }])
            ->get();

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
            'postal_code' => 'required|regex:/^[0-9]{5}$/',
            'phone' => 'required|regex:/^08[0-9]{8,11}$/',
            'email' => 'required|email|max:255',
            'npwp' => ['required', 'regex:/^(\d{15}|\d{16}|\d{2}\.\d{3}\.\d{3}\.\d{1}-\d{3}\.\d{3})$/'],
            'contract_start' => 'nullable|date',
            'contract_end' => 'nullable|date',
            'auditors' => 'required|array',
            'auditors.*' => 'exists:users,id',
            'team_role' => 'required|array',
        ]);

        // Validasi Kuota Maksimal Auditor (3 Klien Aktif)
        $maxQuota = 3;
        foreach ($request->auditors as $auditorId) {
            $auditorUser = User::withCount(['clients' => function ($q) {
                $q->where('status', 'aktif');
            }])->find($auditorId);

            if ($auditorUser && $auditorUser->clients_count >= $maxQuota) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['auditors' => "Auditor {$auditorUser->name} telah mencapai batas kuota maksimal ({$maxQuota} klien aktif). Silakan pilih auditor lain."]);
            }
        }

        // Pembuat data (Admin) -> status pending untuk persetujuan Pimpinan
        $validated['created_by'] = Auth::id();
        $validated['approval_status'] = 'pending';
        $validated['approved_by'] = null;
        $validated['approved_at'] = null;

        // Create client
        $client = Client::create($validated);

        // Assign auditor
        foreach ($request->auditors as $auditorId) {
            $teamRole = $request->team_role[$auditorId] ?? 'anggota';
            $client->auditors()->attach($auditorId, [
                'team_role' => $teamRole
            ]);
        }

        // Kirim notifikasi penugasan ke auditor & notifikasi pengajuan baru ke pimpinan
        try {
            foreach ($request->auditors as $auditorId) {
                $teamRole = $request->team_role[$auditorId] ?? 'anggota';
                $auditorUser = User::find($auditorId);
                if ($auditorUser) {
                    $auditorUser->notify(new \App\Notifications\TeamAssignmentNotification(
                        $client,
                        $teamRole,
                        Auth::user()->name
                    ));
                }
            }

            // Notifikasi ke seluruh pimpinan bahwa ada penambahan data klien baru
            $pimpinanUsers = User::where('role', 'pimpinan')->get();
            foreach ($pimpinanUsers as $pimpinan) {
                if ($pimpinan->id !== Auth::id()) {
                    $pimpinan->notify(new \App\Notifications\ClientApprovalNotification(
                        $client,
                        'pending',
                        null,
                        Auth::user()->name
                    ));
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim notifikasi pembuatan klien: ' . $e->getMessage());
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
            !in_array(Auth::user()->role, ['admin', 'pimpinan'])
        ) {
            abort(403);
        }

        $auditors = User::query()
            ->where('role', 'auditor')
            ->withCount(['clients' => function ($q) {
                $q->where('status', 'aktif');
            }])
            ->get();

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
            !in_array(Auth::user()->role, ['admin', 'pimpinan'])
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
            'postal_code' => 'required|regex:/^[0-9]{5}$/',
            'phone' => 'required|regex:/^08[0-9]{8,11}$/',
            'email' => 'required|email|max:255',
            'npwp' => ['required', 'regex:/^(\d{15}|\d{16}|\d{2}\.\d{3}\.\d{3}\.\d{1}-\d{3}\.\d{3})$/'],
            'status' => 'required|in:aktif,nonaktif',
            'contract_start' => 'nullable|date',
            'contract_end' => 'nullable|date',
            'auditors' => 'required|array',
            'auditors.*' => 'exists:users,id',
            'team_role' => 'required|array',
        ]);

        // Validasi Kuota Maksimal Auditor (3 Klien Aktif, mengabaikan klien ini sendiri)
        $maxQuota = 3;
        foreach ($request->auditors as $auditorId) {
            $auditorUser = User::withCount(['clients' => function ($q) use ($client) {
                $q->where('status', 'aktif')->where('clients.id', '!=', $client->id);
            }])->find($auditorId);

            if ($auditorUser && $auditorUser->clients_count >= $maxQuota) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['auditors' => "Auditor {$auditorUser->name} telah mencapai batas kuota maksimal ({$maxQuota} klien aktif). Silakan pilih auditor lain."]);
            }
        }

        // Otomatis langsung disetujui saat di-update oleh Pimpinan/Admin
        $validated['approval_status'] = 'disetujui';
        $validated['approved_by'] = Auth::id();
        $validated['approved_at'] = now();
        $validated['revision_note'] = null;

        // Update client
        $client->update($validated);

        // Reset auditor
        $client->auditors()->detach();

        // Assign ulang auditor & kirim notifikasi
        try {
            foreach ($request->auditors as $auditorId) {
                $teamRole = $request->team_role[$auditorId] ?? 'anggota';
                $client->auditors()->attach($auditorId, [
                    'team_role' => $teamRole
                ]);

                $auditorUser = User::find($auditorId);
                if ($auditorUser) {
                    $auditorUser->notify(new \App\Notifications\TeamAssignmentNotification(
                        $client,
                        $teamRole,
                        Auth::user()->name
                    ));
                }
            }

            // Notifikasi ke Pimpinan (jika di-update Admin) atau Admin Pembuat (jika di-update Pimpinan)
            $recipients = collect();
            if (Auth::user()->role === 'admin') {
                $recipients = User::where('role', 'pimpinan')->get();
            } else {
                if ($client->creator && $client->creator->id !== Auth::id()) {
                    $recipients->push($client->creator);
                }
            }

            foreach ($recipients as $recipient) {
                if ($recipient->id !== Auth::id()) {
                    $recipient->notify(new \App\Notifications\ClientApprovalNotification(
                        $client,
                        'updated',
                        null,
                        Auth::user()->name
                    ));
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim notifikasi update klien: ' . $e->getMessage());
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
            'revision_note' => null,
        ]);

        // Kirim notifikasi ke auditor tim dan admin pembuat
        try {
            $recipients = collect();
            if ($client->creator) {
                $recipients->push($client->creator);
            }
            if ($client->auditors) {
                $recipients = $recipients->merge($client->auditors);
            }
            $recipients = $recipients->unique('id');

            foreach ($recipients as $recipient) {
                if ($recipient->id !== Auth::id()) {
                    $recipient->notify(new \App\Notifications\ClientApprovalNotification(
                        $client,
                        'disetujui',
                        null,
                        Auth::user()->name
                    ));
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim notifikasi approve klien: ' . $e->getMessage());
        }

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
            'approved_by' => null,
            'approved_at' => null,
        ]);

        // Kirim notifikasi ke admin pembuat & auditor tim
        try {
            $recipients = collect();
            if ($client->creator) {
                $recipients->push($client->creator);
            }
            if ($client->auditors) {
                $recipients = $recipients->merge($client->auditors);
            }
            $recipients = $recipients->unique('id');

            foreach ($recipients as $recipient) {
                if ($recipient->id !== Auth::id()) {
                    $recipient->notify(new \App\Notifications\ClientApprovalNotification(
                        $client,
                        'revisi',
                        $request->revision_note,
                        Auth::user()->name
                    ));
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim notifikasi revisi klien: ' . $e->getMessage());
        }

        return redirect()
            ->back()
            ->with('success', 'Revisi berhasil dikirim.');
    }
}
