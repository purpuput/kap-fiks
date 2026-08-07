<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Evaluasi Absensi Pegawai</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }
        .header h1 {
            font-size: 16px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
            margin: 0 0 4px 0;
        }
        .header h2 {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            margin: 0;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .meta-table td {
            padding: 8px 12px;
            font-size: 11px;
        }
        .meta-label {
            font-weight: bold;
            color: #475569;
            width: 140px;
        }
        .stats-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .stats-box {
            border: 1px solid #cbd5e1;
            padding: 10px;
            text-align: center;
            background-color: #ffffff;
            width: 16%;
        }
        .stats-box .val {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 2px;
        }
        .stats-box .lbl {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #1e293b;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 8px 6px;
            border: 1px solid #1e293b;
            text-align: left;
        }
        .data-table td {
            padding: 7px 6px;
            font-size: 10px;
            border: 1px solid #e2e8f0;
        }
        .data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
            text-transform: capitalize;
        }
        .badge-hadir { background-color: #dcfce7; color: #166534; }
        .badge-sakit { background-color: #fef3c7; color: #92400e; }
        .badge-izin  { background-color: #dbeafe; color: #1e40af; }
        .badge-alpha { background-color: #ffe4e6; color: #9f1239; }
        
        .footer-sig {
            margin-top: 30px;
            width: 100%;
        }
        .footer-sig td {
            vertical-align: top;
            font-size: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>KANTOR AKUNTAN PUBLIK (KAP)</h1>
        <h2>LAPORAN EVALUASI KEHADIRAN PEGAWAI</h2>
    </div>

    <!-- METADATA INFORMATION -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">Nama Pegawai:</td>
            <td>
                <strong>
                    @if(isset($selectedUsers) && $selectedUsers->count() > 0)
                        {{ $selectedUsers->pluck('name')->implode(', ') }}
                    @else
                        Semua Pegawai
                    @endif
                </strong>
            </td>
            <td class="meta-label">Tanggal Cetak:</td>
            <td>{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
        <tr>
            <td class="meta-label">Periode Evaluasi:</td>
            <td>
                @if($startDate && $endDate)
                    {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                @elseif($startDate)
                    Mulai {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
                @elseif($endDate)
                    Sampai {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                @else
                    Semua Periode (Keseluruhan Data)
                @endif
            </td>
            <td class="meta-label">Pencetak:</td>
            <td>{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</td>
        </tr>
    </table>

    <!-- RINGKASAN EVALUASI GLOBAL -->
    <table class="stats-container">
        <tr>
            <td class="stats-box">
                <div class="lbl">Total Absensi</div>
                <div class="val">{{ $stats['total'] ?? $attendances->count() }}</div>
            </td>
            <td class="stats-box" style="background-color: #f0fdf4;">
                <div class="lbl" style="color: #166534;">Hadir</div>
                <div class="val" style="color: #166534;">{{ $stats['hadir'] ?? 0 }}</div>
            </td>
            <td class="stats-box" style="background-color: #fffbeb;">
                <div class="lbl" style="color: #92400e;">Sakit</div>
                <div class="val" style="color: #92400e;">{{ $stats['sakit'] ?? 0 }}</div>
            </td>
            <td class="stats-box" style="background-color: #eff6ff;">
                <div class="lbl" style="color: #1e40af;">Izin</div>
                <div class="val" style="color: #1e40af;">{{ $stats['izin'] ?? 0 }}</div>
            </td>
            <td class="stats-box" style="background-color: #fff1f2;">
                <div class="lbl" style="color: #9f1239;">Alpha</div>
                <div class="val" style="color: #9f1239;">{{ $stats['alpha'] ?? 0 }}</div>
            </td>
            <td class="stats-box" style="background-color: #f8fafc;">
                <div class="lbl" style="color: #0f172a;">% Kehadiran</div>
                <div class="val" style="color: #2563eb;">{{ $stats['percentage'] ?? 0 }}%</div>
            </td>
        </tr>
    </table>

    <!-- RINGKASAN EVALUASI PER PEGAWAI -->
    @if(isset($userSummary) && $userSummary->count() > 0)
    <div style="margin-top: 15px; margin-bottom: 5px; font-weight: bold; font-size: 11px; color: #1e293b; text-transform: uppercase;">
        Rekapitulasi Evaluasi Kehadiran Per Pegawai:
    </div>
    <table class="data-table" style="margin-bottom: 15px;">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Nama Pegawai</th>
                <th style="width: 60px;">Role</th>
                <th style="width: 45px;">Total</th>
                <th style="width: 45px; color: #166534;">Hadir</th>
                <th style="width: 45px; color: #92400e;">Sakit</th>
                <th style="width: 45px; color: #1e40af;">Izin</th>
                <th style="width: 45px; color: #9f1239;">Alpha</th>
                <th style="width: 65px;">% Hadir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($userSummary as $idx => $us)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td><strong>{{ $us->name }}</strong></td>
                <td class="text-center">{{ ucfirst($us->role) }}</td>
                <td class="text-center">{{ $us->total }} Hari</td>
                <td class="text-center" style="font-weight: bold; color: #166534;">{{ $us->hadir }}</td>
                <td class="text-center" style="font-weight: bold; color: #92400e;">{{ $us->sakit }}</td>
                <td class="text-center" style="font-weight: bold; color: #1e40af;">{{ $us->izin }}</td>
                <td class="text-center" style="font-weight: bold; color: #9f1239;">{{ $us->alpha }}</td>
                <td class="text-center">
                    <span class="badge" style="background-color: #dbeafe; color: #1e40af;">{{ $us->percentage }}%</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <!-- TABEL DATA ABSENSI -->
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">No</th>
                <th>Nama Pegawai</th>
                <th class="text-center" style="width: 75px;">Tanggal</th>
                <th class="text-center" style="width: 65px;">Masuk</th>
                <th class="text-center" style="width: 65px;">Pulang</th>
                <th class="text-center" style="width: 60px;">Status</th>
                <th class="text-center" style="width: 80px;">Lampiran</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $index => $attendance)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $attendance->user->name ?? '-' }}</strong></td>
                <td class="text-center">{{ $attendance->date ? \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') : '-' }}</td>
                <td class="text-center">{{ $attendance->check_in ?? '-' }}</td>
                <td class="text-center">{{ $attendance->check_out ?? '-' }}</td>
                <td class="text-center">
                    <span class="badge badge-{{ strtolower($attendance->status) }}">
                        {{ ucfirst($attendance->status) }}
                    </span>
                </td>
                <td class="text-center">
                    @if($attendance->attachment)
                        <span style="color: #2563eb; font-weight: bold;">[Ada File]</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px; color: #64748b;">
                    Tidak ada data absensi untuk kriteria evaluasi yang dipilih.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- TANDA TANGAN EVALUASI -->
    <table class="footer-sig">
        <tr>
            <td style="width: 60%;"></td>
            <td class="text-center">
                <p style="margin-bottom: 50px;">
                    Dicetak Pada: {{ date('d F Y') }}<br>
                    <strong>Pimpinan / Pengelola KAP</strong>
                </p>
                <p>___________________________</p>
            </td>
        </tr>
    </table>

</body>
</html>