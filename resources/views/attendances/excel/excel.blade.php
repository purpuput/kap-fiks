<table>
    <!-- TITLE BANNER -->
    <tr>
        <td colspan="8" style="font-size: 16px; font-weight: bold; text-align: center; background-color: #1e3a8a; color: #ffffff; height: 35px; vertical-align: middle;">
            KANTOR AKUNTAN PUBLIK (KAP)
        </td>
    </tr>
    <tr>
        <td colspan="8" style="font-size: 13px; font-weight: bold; text-align: center; background-color: #1e3a8a; color: #93c5fd; height: 25px; vertical-align: middle;">
            LAPORAN EVALUASI KEHADIRAN PEGAWAI
        </td>
    </tr>
    <tr><td colspan="8"></td></tr>

    <!-- METADATA INFORMATION -->
    <tr>
        <td style="font-weight: bold; background-color: #f1f5f9;">Pegawai / Evaluasi:</td>
        <td colspan="3" style="font-weight: bold; color: #1e293b;">
            @if(isset($selectedUsers) && $selectedUsers->count() > 0)
                {{ $selectedUsers->pluck('name')->implode(', ') }}
            @else
                Semua Pegawai
            @endif
        </td>
        <td style="font-weight: bold; background-color: #f1f5f9;">Tanggal Cetak:</td>
        <td colspan="3">{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
    </tr>
    <tr>
        <td style="font-weight: bold; background-color: #f1f5f9;">Periode Evaluasi:</td>
        <td colspan="3">
            @if($startDate && $endDate)
                {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            @elseif($startDate)
                Sejak {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}
            @elseif($endDate)
                Sampai {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
            @else
                Semua Periode
            @endif
        </td>
        <td style="font-weight: bold; background-color: #f1f5f9;">Pencetak:</td>
        <td colspan="3">{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</td>
    </tr>
    <tr><td colspan="8"></td></tr>

    <!-- SUMMARY STATS BOX -->
    <tr>
        <td colspan="8" style="font-weight: bold; background-color: #0f172a; color: #ffffff; height: 25px; vertical-align: middle;">
            RINGKASAN STATISTIK EVALUASI KEHADIRAN (GLOBAL)
        </td>
    </tr>
    <tr>
        <td colspan="2" style="background-color: #f8fafc; font-weight: bold; border: 1px solid #cbd5e1;">Total Catatan Absensi:</td>
        <td style="border: 1px solid #cbd5e1; text-align: center; font-weight: bold;">{{ $stats['total'] ?? 0 }} Hari</td>
        <td colspan="2" style="background-color: #dcfce7; font-weight: bold; color: #166534; border: 1px solid #cbd5e1;">Hadir:</td>
        <td style="border: 1px solid #cbd5e1; text-align: center; font-weight: bold; color: #166534;">{{ $stats['hadir'] ?? 0 }}</td>
        <td style="background-color: #3b82f6; font-weight: bold; color: #ffffff; border: 1px solid #cbd5e1;">Persentase Kehadiran:</td>
        <td style="background-color: #1d4ed8; font-weight: bold; color: #ffffff; border: 1px solid #cbd5e1; text-align: center;">{{ $stats['percentage'] ?? 0 }}%</td>
    </tr>
    <tr>
        <td colspan="2" style="background-color: #fef3c7; font-weight: bold; color: #92400e; border: 1px solid #cbd5e1;">Sakit:</td>
        <td style="border: 1px solid #cbd5e1; text-align: center; font-weight: bold; color: #92400e;">{{ $stats['sakit'] ?? 0 }}</td>
        <td colspan="2" style="background-color: #dbeafe; font-weight: bold; color: #1e40af; border: 1px solid #cbd5e1;">Izin:</td>
        <td style="border: 1px solid #cbd5e1; text-align: center; font-weight: bold; color: #1e40af;">{{ $stats['izin'] ?? 0 }}</td>
        <td style="background-color: #ffe4e6; font-weight: bold; color: #9f1239; border: 1px solid #cbd5e1;">Alpha:</td>
        <td style="border: 1px solid #cbd5e1; text-align: center; font-weight: bold; color: #9f1239;">{{ $stats['alpha'] ?? 0 }}</td>
    </tr>
    <tr><td colspan="8"></td></tr>

    <!-- RINGKASAN EVALUASI PER PEGAWAI -->
    @if(isset($userSummary) && $userSummary->count() > 0)
    <tr>
        <td colspan="8" style="font-weight: bold; background-color: #1e293b; color: #ffffff; height: 25px; vertical-align: middle;">
            REKAPITULASI EVALUASI KEHADIRAN PER PEGAWAI
        </td>
    </tr>
    <tr>
        <td style="background-color: #334155; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #475569;">No</td>
        <td style="background-color: #334155; color: #ffffff; font-weight: bold; text-align: left; border: 1px solid #475569;">Nama Pegawai</td>
        <td style="background-color: #334155; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #475569;">Role</td>
        <td style="background-color: #334155; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #475569;">Total Hari</td>
        <td style="background-color: #334155; color: #4ade80; font-weight: bold; text-align: center; border: 1px solid #475569;">Hadir</td>
        <td style="background-color: #334155; color: #fde047; font-weight: bold; text-align: center; border: 1px solid #475569;">Sakit</td>
        <td style="background-color: #334155; color: #60a5fa; font-weight: bold; text-align: center; border: 1px solid #475569;">Izin</td>
        <td style="background-color: #334155; color: #f87171; font-weight: bold; text-align: center; border: 1px solid #475569;">Alpha</td>
    </tr>
    @foreach($userSummary as $idx => $us)
    <tr>
        <td style="text-align: center; border: 1px solid #cbd5e1;">{{ $idx + 1 }}</td>
        <td style="border: 1px solid #cbd5e1; font-weight: bold;">{{ $us->name }}</td>
        <td style="text-align: center; border: 1px solid #cbd5e1;">{{ ucfirst($us->role) }}</td>
        <td style="text-align: center; border: 1px solid #cbd5e1;">{{ $us->total }} Hari</td>
        <td style="text-align: center; border: 1px solid #cbd5e1; font-weight: bold; color: #166534; background-color: #dcfce7;">{{ $us->hadir }}</td>
        <td style="text-align: center; border: 1px solid #cbd5e1; font-weight: bold; color: #92400e; background-color: #fef3c7;">{{ $us->sakit }}</td>
        <td style="text-align: center; border: 1px solid #cbd5e1; font-weight: bold; color: #1e40af; background-color: #dbeafe;">{{ $us->izin }}</td>
        <td style="text-align: center; border: 1px solid #cbd5e1; font-weight: bold; color: #9f1239; background-color: #ffe4e6;">{{ $us->alpha }}</td>
    </tr>
    @endforeach
    <tr><td colspan="8"></td></tr>
    @endif

    <!-- DETAIL ABSENSI BANNER -->
    <tr>
        <td colspan="8" style="font-weight: bold; background-color: #0f172a; color: #ffffff; height: 25px; vertical-align: middle;">
            RINCIAN RIWAYAT CATATAN ABSENSI HARIAN
        </td>
    </tr>

    <!-- TABLE HEADER -->
    <thead>
        <tr>
            <th style="background-color: #1e293b; color: #ffffff; font-weight: bold; text-align: center; height: 28px; vertical-align: middle; border: 1px solid #334155;">No</th>
            <th style="background-color: #1e293b; color: #ffffff; font-weight: bold; text-align: left; vertical-align: middle; border: 1px solid #334155;">Pegawai</th>
            <th style="background-color: #1e293b; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #334155;">Role</th>
            <th style="background-color: #1e293b; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #334155;">Tanggal</th>
            <th style="background-color: #1e293b; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #334155;">Jam Masuk</th>
            <th style="background-color: #1e293b; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #334155;">Jam Pulang</th>
            <th style="background-color: #1e293b; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #334155;">Status</th>
            <th style="background-color: #1e293b; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; border: 1px solid #334155;">Lampiran</th>
        </tr>
    </thead>
    <tbody>
        @forelse($attendances as $index => $att)
        <tr style="background-color: {{ $index % 2 === 0 ? '#ffffff' : '#f8fafc' }};">
            <td style="text-align: center; border: 1px solid #cbd5e1;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #cbd5e1; font-weight: bold; color: #1e293b;">{{ $att->user->name ?? '-' }}</td>
            <td style="text-align: center; border: 1px solid #cbd5e1;">{{ ucfirst($att->user->role ?? '-') }}</td>
            <td style="text-align: center; border: 1px solid #cbd5e1;">{{ $att->date ? \Carbon\Carbon::parse($att->date)->format('d/m/Y') : '-' }}</td>
            <td style="text-align: center; border: 1px solid #cbd5e1;">{{ $att->check_in ?? '-' }}</td>
            <td style="text-align: center; border: 1px solid #cbd5e1;">{{ $att->check_out ?? '-' }}</td>
            <td style="text-align: center; border: 1px solid #cbd5e1; font-weight: bold;
                @if($att->status === 'hadir') background-color: #dcfce7; color: #166534;
                @elseif($att->status === 'sakit') background-color: #fef3c7; color: #92400e;
                @elseif($att->status === 'izin') background-color: #dbeafe; color: #1e40af;
                @elseif($att->status === 'alpha') background-color: #ffe4e6; color: #9f1239;
                @endif">
                {{ ucfirst($att->status) }}
            </td>
            <td style="text-align: center; border: 1px solid #cbd5e1;">
                {{ $att->attachment ? 'Ada Lampiran' : 'Tidak Ada' }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" style="text-align: center; padding: 15px; color: #64748b; border: 1px solid #cbd5e1;">
                Tidak ada data absensi untuk kriteria yang dipilih.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
