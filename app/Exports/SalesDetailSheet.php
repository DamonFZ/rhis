<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesDetailSheet implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected string $month;

    protected int $year;

    protected int $monthNum;

    protected Collection $flatData;

    protected array $salesTypeLabels = [
        1 => '自主开发',
        2 => '康复续卡',
        3 => '协助开单',
    ];

    public function __construct(string $month)
    {
        $this->month = $month;
        $parts = explode('-', $month);
        $this->year = (int) $parts[0];
        $this->monthNum = (int) $parts[1];
        $this->flatData = $this->buildFlatData();
    }

    protected function buildFlatData(): Collection
    {
        // 从 patient_package_sales 中间表读取，支持多人平分
        $employees = User::whereHas('salesPackages', function ($q) {
            $q->whereYear('purchase_date', $this->year)
                ->whereMonth('purchase_date', $this->monthNum);
        })->with(['salesPackages' => function ($q) {
            $q->whereYear('purchase_date', $this->year)
                ->whereMonth('purchase_date', $this->monthNum)
                ->with('patient');
        }])->get();

        $flat = [];
        foreach ($employees as $employee) {
            foreach ($employee->salesPackages as $package) {
                $flat[] = [
                    'employee_name' => $employee->name,
                    'patient_name' => $package->patient ? $package->patient->name : '未知',
                    'package_name' => $package->package_name,
                    'sales_type' => isset($this->salesTypeLabels[$package->sales_type]) ? $this->salesTypeLabels[$package->sales_type] : '-',
                    'price' => $package->price,
                    'commission_amount' => $package->pivot->commission_amount,
                ];
            }
        }

        return collect($flat);
    }

    public function collection(): Collection
    {
        return $this->flatData;
    }

    public function headings(): array
    {
        return ['员工姓名', '客户姓名', '套餐名称', '销售类型', '套餐价格', '本次提成'];
    }

    public function map($row): array
    {
        return [
            $row['employee_name'],
            $row['patient_name'],
            $row['package_name'],
            $row['sales_type'],
            $row['price'],
            $row['commission_amount'],
        ];
    }

    public function title(): string
    {
        return '销售明细';
    }
}
