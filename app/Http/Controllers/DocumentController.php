<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->input('category');
        $search = $request->input('search');

        $query = Document::with('user', 'client');

        if ($category) {
            $query->where('category', $category);
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $documents = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('documents.index', compact('documents', 'category', 'search'));
    }

    public function create()
    {
        $clients = Client::where('status', 'aktif')->get();
        return view('documents.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|max:50000',
            'category' => 'required|in:audit_evidence,client_data,report,other',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('documents/' . now()->format('Y/m'), 'public');

            $validated['file_path'] = $path;
            $validated['file_type'] = $file->getClientOriginalExtension();
            $validated['file_size'] = $file->getSize();
        }

        $validated['user_id'] = Auth::id();

        Document::create($validated);

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil diunggah');
    }

    public function download(Document $document)
    {
        return Storage::download(
            $document->file_path,
            $document->name . '.' . $document->file_type
        );
    }

    public function destroy(Document $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil dihapus');
    }
}
