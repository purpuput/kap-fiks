<?php

namespace App\Http\Controllers;

use App\Models\AuditReport;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditReportController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = AuditReport::with('client', 'createdBy');

        if ($search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('report_number', 'like', "%{$search}%");
        }

        if ($status) {
            $query->where('status', $status);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('audit_reports.index', compact('reports', 'search', 'status'));
    }

    public function create()
    {
        $clients = Client::where('status', 'aktif')->get();
        return view('audit_reports.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'report_number' => 'required|string|unique:audit_reports',
            'title' => 'required|string|max:255',
            'audit_date' => 'required|date',
            'findings' => 'required|string',
            'recommendations' => 'nullable|string',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'draft';

        AuditReport::create($validated);

        return redirect()->route('audit_reports.index')->with('success', 'Laporan Audit berhasil dibuat');
    }

    public function edit(AuditReport $auditReport)
    {
        $clients = Client::where('status', 'aktif')->get();
        return view('audit_reports.edit', compact('auditReport', 'clients'));
    }

    public function update(Request $request, AuditReport $auditReport)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'audit_date' => 'required|date',
            'findings' => 'required|string',
            'recommendations' => 'nullable|string',
            'status' => 'required|in:draft,in_review,completed',
        ]);

        $auditReport->update($validated);

        return redirect()->route('audit_reports.index')->with('success', 'Laporan Audit berhasil diperbarui');
    }

    public function destroy(AuditReport $auditReport)
    {
        $auditReport->delete();
        return redirect()->route('audit_reports.index')->with('success', 'Laporan Audit berhasil dihapus');
    }
}
