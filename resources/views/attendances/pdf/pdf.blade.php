<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Absensi</title>

    <style>
        body {
            font-family: sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th,
        td {
            padding: 8px;
            font-size: 12px;
        }

        h2 {
            text-align: center;
        }
    </style>
</head>

<body>

    <h2>Laporan Absensi</h2>

    <table>
        <thead>
            <tr>
                <th>Auditor</th>
                <th>Tanggal</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Status</th>
                <th>Lampiran</th>
            </tr>
        </thead>

        <tbody>
            @foreach($attendances as $attendance)
            <tr>
                <td>{{ $attendance->user->name ?? '-' }}</td>
                <td>{{ $attendance->date ? $attendance->date->format('d/m/Y') : '-' }}</td>
                <td>{{ $attendance->check_in ?? '-' }}</td>
                <td>{{ $attendance->check_out ?? '-' }}</td>
                <td>{{ ucfirst($attendance->status) }}</td>
                <td>
                    @if($attendance->attachment)
                        <span style="color: #1d4ed8;">[Ada Lampiran]</span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>