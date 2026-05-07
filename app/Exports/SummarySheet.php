<?php

namespace App\Exports;

use App\Models\User;
use App\Models\PatientPackage;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;

class SummarySheet implements FromCollection, WithHeadings, WithTitle, WithMapping
{
    protected string $month;

    protected int $year;

    protected int $monthNum;

    public function __construct(string $month)
    {
        $this->month = $month;
        $parts = explode('-', $month);
        $this->year = (int) $parts[0];
        $this->monthNum = (int) $parts[1];
    }

    public function collection(): Collection
    {
        return User::with(['consumptionRecords' => function ($query) {
            $query->whereYear('treatment_date', $this->year)
                ->whereMonth('treatment_date', $this->monthNum);
        }])->get()->map(function (User $user) {
            $serviceTotal = 0;
            foreach ($user->consumptionRecords as $cr) {
                $serviceTotal += $cr->pivot->commission_amount ?? 0;
            }

            $salesTotal = PatientPackage::where('salesperson_id', $user->id)
                ->whereYear('purchase_date', $this->year)
                ->whereMonth('purchase_date', $this->monthNum)
                ->sum('sales_commission');

            return [
                'user' => $user,
                'service_commission' => $serviceTotal,
                'sales_commission' => $salesTotal,
                'total_commission' => $serviceTotal + $salesTotal,
                'service_count' => $user->consumptionRecords->count(),
                'sales_count' => PatientPackage::where('salesperson_id', $user->id)
                    ->whereYear('purchase_date', $this->year)
                    ->whereMonth('purchase_date', $this->monthNum)
                    ->count(),
            ];
        })->filter(function ($item) {
            return $item['service_commission'] > 0 || $item['sales_commission'] > 0;
        });
    }

    public function headings(): array
    {
        return ['员工姓名', '服务提成', '销售提成', '月度总提成', '服务次数', '销售单数'];
    }

    public function map($row): array
    {
        return [
            $row['user']->name,
            $row['service_commission'],
            $row['sales_commission'],
            $row['total_commission'],
            $row['service_count'],
            $row['sales_count'],
        ];
    }

    public function title(): string
    {
        return '汇总';
    }
}
