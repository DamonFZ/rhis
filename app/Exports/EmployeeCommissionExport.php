<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class EmployeeCommissionExport implements WithMultipleSheets
{
    protected string $month;

    public function __construct(string $month)
    {
        $this->month = $month;
    }

    public function sheets(): array
    {
        return [
            new SummarySheet($this->month),
            new ServiceDetailSheet($this->month),
            new SalesDetailSheet($this->month),
        ];
    }
}
