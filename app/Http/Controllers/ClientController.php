<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', 'aktif');

        $query = Client::query();

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        if ($status) {
            $query->where('status', $status);
        }

        $clients = $query->paginate(15);

        return view('clients.index', compact('clients', 'search', 'status'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'description' => 'nullable|string',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Klien berhasil ditambahkan');
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'description' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Klien berhasil diperbarui');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Klien berhasil dihapus');
    }
}
