<?php

namespace App\Http\Controllers;

use App\Models\AuditReport;
use App\Models\Attendance;
use App\Models\Client;
use App\Models\AuditReportFile;
use App\Models\Logbook;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function admin()
    {
        $user = Auth::user();

        $data = [
            // Status Pribadi Admin
            'absensiSayaHariIni'  => Attendance::where('user_id', $user->id)
                                        ->whereDate('date', today())
                                        ->first(),
            'logbookSayaHariIni'  => Logbook::where('user_id', $user->id)
                                        ->whereDate('date', today())
                                        ->first(),

            // Stat utama
            'totalUsers'          => User::count(),
            'totalAuditor'        => User::where('role', 'auditor')->count(),
            'totalKlien'          => Client::count(),
            'totalLaporan'        => AuditReport::count(),
            'totalDokumen'        => AuditReportFile::where('status', 'lengkap')->count(),

            // Laporan per status
            'laporanPending'      => AuditReport::where('status', 'review')->count(),
            'laporanApprove'      => AuditReport::where('status', 'disetujui')->count(),
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

        // Laporan milik auditor ini (berdasarkan klien yang diassign ke auditor ini dan sudah disetujui)
        $myReports = AuditReport::whereHas('client.auditors', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->whereHas('client', function ($q) {
            $q->where('approval_status', 'disetujui');
        })->with('client')->latest()->get();

        // Absensi hari ini
        $absensiHariIni = Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        // Logbook hari ini
        $logbookHariIni = Logbook::where('user_id', $user->id)
            ->whereDate('date', today())
            ->first();

        // Laporan yang perlu direvisi
        $laporanRevisi = AuditReport::whereHas('client.auditors', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->whereHas('client', function ($q) {
            $q->where('approval_status', 'disetujui');
        })
            ->where('status', 'revisi')
            ->with('client')
            ->latest()
            ->get();

        // Hitung status tugas dokumen riil
        $myReportIds = AuditReport::whereHas('client.auditors', function ($q) use ($user) {
            $q->where('users.id', $user->id);
        })->whereHas('client', function ($q) {
            $q->where('approval_status', 'disetujui');
        })->pluck('id');

        $myFiles = AuditReportFile::whereIn('audit_report_id', $myReportIds)->with('review')->get();

        $tugasRevisi = 0;
        $tugasDalamReview = 0;
        $tugasDisetujui = 0;

        foreach ($myFiles as $file) {
            $review = $file->review;
            if ($review) {
                if ($review->status === 'revision') {
                    $tugasRevisi++;
                } elseif ($review->status === 'approved') {
                    $tugasDisetujui++;
                } else {
                    $tugasDalamReview++;
                }
            } else {
                $tugasDalamReview++;
            }
        }

        $data = [
            'myReports'          => $myReports,
            'totalMyReports'     => $myReports->count(),
            'myPending'          => $myReports->where('status', 'review')->count(),
            'myApprove'          => $myReports->where('status', 'disetujui')->count(),
            'myRevisi'           => $myReports->where('status', 'revisi')->count(),
            'absensiHariIni'     => $absensiHariIni,
            'logbookHariIni'     => $logbookHariIni,
            'laporanRevisi'      => $laporanRevisi,
            'laporanTerbaru'     => $myReports->take(5),
            'tugasRevisi'        => $tugasRevisi,
            'tugasDalamReview'   => $tugasDalamReview,
            'tugasDisetujui'     => $tugasDisetujui,
        ];

        return view('auditor.dashboard', $data);
    }

    public function pimpinan()
    {
        // Laporan menunggu review
        $laporanMenunggu = AuditReport::where('status', 'review')
            ->with(['client.auditors'])
            ->latest()
            ->get();

        // Semua stats
        $data = [
            'totalLaporan'        => AuditReport::count(),
            'laporanPending'      => AuditReport::where('status', 'review')->count(),
            'laporanApprove'      => AuditReport::where('status', 'disetujui')->count(),
            'laporanRevisi'       => AuditReport::where('status', 'revisi')->count(),
            'totalDokumen'        => AuditReportFile::where('status', 'lengkap')->count(),
            'totalKlien'          => Client::count(),
            'laporanKeuangan'     => AuditReport::where('audit_type', 'keuangan')->count(),
            'laporanKepatuhan'    => AuditReport::where('audit_type', 'kepatuhan')->count(),

            'laporanMenunggu'     => $laporanMenunggu,

            // Laporan baru disetujui
            'laporanDisetujui'    => AuditReport::where('status', 'disetujui')
                                        ->with(['client.auditors'])
                                        ->latest('updated_at')
                                        ->take(5)
                                        ->get(),

            // Dokumen arsip terbaru
            'dokumenTerbaru'      => AuditReportFile::where('status', 'lengkap')
                                        ->with(['auditReport.client', 'approvedBy'])
                                        ->latest('approved_at')
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
