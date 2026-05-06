<?php

namespace App\Http\Controllers;

use App\Models\Logbook;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogbookController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));

        $query = Logbook::with('user', 'client');

        if (Auth::check() && Auth::user()->role !== 'admin') {
            $query->where('user_id', Auth::id());
        }

        $logbooks = $query->whereYear('date', substr($month, 0, 4))
            ->whereMonth('date', substr($month, 5, 2))
            ->orderBy('date', 'desc')
            ->paginate(15);

        return view('logbooks.index', compact('logbooks', 'month'));
    }

    public function create()
    {
        $clients = Client::where('status', 'aktif')->get();
        return view('logbooks.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'date' => 'required|date|before_or_equal:today',
            'activity' => 'required|string|max:255',
            'description' => 'required|string',
            'hours' => 'required|numeric|min:0.5|max:12',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'draft';

        Logbook::create($validated);

        return redirect()->route('logbooks.index')->with('success', 'Logbook berhasil ditambahkan');
    }

    public function edit(Logbook $logbook)
    {
        $clients = Client::where('status', 'aktif')->get();
        return view('logbooks.edit', compact('logbook', 'clients'));
    }

    public function update(Request $request, Logbook $logbook)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'activity' => 'required|string|max:255',
            'description' => 'required|string',
            'hours' => 'required|numeric|min:0.5|max:12',
        ]);

        $logbook->update($validated);

        return redirect()->route('logbooks.index')->with('success', 'Logbook berhasil diperbarui');
    }

    public function destroy(Logbook $logbook)
    {
        $logbook->delete();
        return redirect()->route('logbooks.index')->with('success', 'Logbook berhasil dihapus');
    }
}
