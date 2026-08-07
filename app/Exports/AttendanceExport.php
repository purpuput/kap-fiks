<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class AttendanceExport implements FromView, ShouldAutoSize, WithTitle
{
    protected $attendances;
    protected $selectedUsers;
    protected $startDate;
    protected $endDate;
    protected $stats;
    protected $userSummary;

    public function __construct($attendances, $selectedUsers = null, $startDate = null, $endDate = null, $stats = [], $userSummary = null)
    {
        $this->attendances   = $attendances;
        $this->selectedUsers = $selectedUsers;
        $this->startDate     = $startDate;
        $this->endDate       = $endDate;
        $this->stats         = $stats;
        $this->userSummary   = $userSummary;
    }

    public function view(): View
    {
        return view('attendances.excel.excel', [
            'attendances'   => $this->attendances,
            'selectedUsers' => $this->selectedUsers,
            'startDate'     => $this->startDate,
            'endDate'       => $this->endDate,
            'stats'         => $this->stats,
            'userSummary'   => $this->userSummary,
        ]);
    }

    public function title(): string
    {
        return 'Evaluasi Kehadiran';
    }
}
