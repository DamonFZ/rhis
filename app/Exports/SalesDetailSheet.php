<?php

namespace App\Exports;

use App\Models\PatientPackage;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesDetailSheet implements FromCollection, WithHeadings, WithTitle, WithMapping
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
        $records = PatientPackage::with(['salesperson', 'patient'])
            ->whereNotNull('salesperson_id')
            ->whereYear('purchase_date', $this->year)
            ->whereMonth('purchase_date', $this->monthNum)
            ->get();

        $flat = [];
        foreach ($records as $record) {
            $flat[] = [
                'employee_name' => $record->salesperson ? $record->salesperson->name : '未知',
                'patient_name' => $record->patient ? $record->patient->name : '未知',
                'package_name' => $record->package_name,
                'sales_type' => isset($this->salesTypeLabels[$record->sales_type]) ? $this->salesTypeLabels[$record->sales_type] : '-',
                'price' => $record->price,
                'commission_amount' => $record->sales_commission,
            ];
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
