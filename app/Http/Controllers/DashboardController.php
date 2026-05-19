<?php

namespace App\Http\Controllers;

use App\Models\AuditReport;
use App\Models\Attendance;
use App\Models\Client;
use App\Models\DocumentManagement;
use App\Models\Logbook;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function admin()
    {
        $data = [
            // Stat utama
            'totalUsers'          => User::count(),
            'totalAuditor'        => User::where('role', 'auditor')->count(),
            'totalKlien'          => Client::count(),
            'totalLaporan'        => AuditReport::count(),
            'totalDokumen'        => DocumentManagement::count(),

            // Laporan per status
            'laporanPending'      => AuditReport::where('status', 'pending')->count(),
            'laporanApprove'      => AuditReport::where('status', 'approve')->count(),
            'laporanRevisi'       => AuditReport::where('status', 'revisi')->count(),

            // Laporan per jenis
            'laporanKeuangan'     => AuditReport::where('audit_type', 'keuangan')->count(),
            'laporanKepatuhan'    => AuditReport::where('audit_type', 'kepatuhan')->count(),

            // Absensi hari ini
            'absensiHariIni'      => Attendance::whereDate('date', today())->count(),

            // Logbook
            'logbookPending'      => Logbook::where('status', 'pending')->count(),
            'logbookApprove'      => Logbook::where('status', 'approved')->count(),
            'logbookRevisi'       => Logbook::where('status', 'revision')->count(),

            // Laporan terbaru
            'laporanTerbaru'      => AuditReport::with(['client.auditors'])
                                        ->latest()
                                        ->take(5)
                                        ->get(),

            // User auditor list
            'auditors'            => User::where('role', 'auditor')
                                        ->withCount(['auditReports'])
                                        ->take(5)
                                        ->get(),
        ];

        return view('admin.dashboard', $data);
    }

    public function auditor()
    {
        $user = Auth::user();

        // Laporan milik auditor ini
        $myReports = $user->auditReports()->with('client')->latest()->get();

        // Absensi hari ini
        $absensiHariIni = Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        // Logbook hari ini
        $logbookHariIni = Logbook::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        // Laporan yang perlu direvisi
        $laporanRevisi = $user->auditReports()
            ->where('status', 'revisi')
            ->with('client')
            ->latest()
            ->get();

        $data = [
            'myReports'          => $myReports,
            'totalMyReports'     => $myReports->count(),
            'myPending'          => $myReports->where('status', 'pending')->count(),
            'myApprove'          => $myReports->where('status', 'approve')->count(),
            'myRevisi'           => $myReports->where('status', 'revisi')->count(),
            'absensiHariIni'     => $absensiHariIni,
            'logbookHariIni'     => $logbookHariIni,
            'laporanRevisi'      => $laporanRevisi,
            'laporanTerbaru'     => $myReports->take(5),
        ];

        return view('auditor.dashboard', $data);
    }

    public function pimpinan()
    {
        // Laporan menunggu review
        $laporanMenunggu = AuditReport::where('status', 'pending')
            ->with(['client.auditors'])
            ->latest()
            ->get();

        // Semua stats
        $data = [
            'totalLaporan'        => AuditReport::count(),
            'laporanPending'      => AuditReport::where('status', 'pending')->count(),
            'laporanApprove'      => AuditReport::where('status', 'approve')->count(),
            'laporanRevisi'       => AuditReport::where('status', 'revisi')->count(),
            'totalDokumen'        => DocumentManagement::count(),
            'totalKlien'          => Client::count(),
            'laporanKeuangan'     => AuditReport::where('audit_type', 'keuangan')->count(),
            'laporanKepatuhan'    => AuditReport::where('audit_type', 'kepatuhan')->count(),

            'laporanMenunggu'     => $laporanMenunggu,

            // Laporan baru disetujui
            'laporanDisetujui'    => AuditReport::where('status', 'approve')
                                        ->with(['client.auditors'])
                                        ->latest('updated_at')
                                        ->take(5)
                                        ->get(),

            // Dokumen arsip terbaru
            'dokumenTerbaru'      => DocumentManagement::with(['auditReport.client', 'archivedBy'])
                                        ->latest('archived_at')
                                        ->take(5)
                                        ->get(),

            // Absensi hari ini
            'absensiHariIni'      => Attendance::whereDate('date', today())->count(),

            // Logbook
            'logbookPending'      => Logbook::where('status', 'pending')->count(),
            'logbookApprove'      => Logbook::where('status', 'approved')->count(),
            'logbookRevisi'       => Logbook::where('status', 'revision')->count(),
        ];

        return view('pimpinan.dashboard', $data);
    }
}
