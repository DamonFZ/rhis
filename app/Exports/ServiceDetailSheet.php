<?php

namespace App\Exports;

use App\Models\ConsumptionRecord;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;

class ServiceDetailSheet implements FromCollection, WithHeadings, WithTitle, WithMapping
{
    protected string $month;

    protected int $year;

    protected int $monthNum;

    protected Collection $flatData;

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
        $records = ConsumptionRecord::with(['patient', 'patientPackage', 'employees'])
            ->whereYear('treatment_date', $this->year)
            ->whereMonth('treatment_date', $this->monthNum)
            ->get();

        $flat = [];
        foreach ($records as $record) {
            $patientName = $record->patient ? $record->patient->name : '未知';
            $packageName = $record->patientPackage ? $record->patientPackage->package_name : '未知';
            $treatmentDate = $record->treatment_date->format('Y-m-d');
            $deductedSessions = $record->deducted_sessions;

            foreach ($record->employees as $employee) {
                $pivot = $employee->pivot;
                $commission = $pivot ? $pivot->commission_amount : 0;
                $flat[] = [
                    'employee_name' => $employee->name,
                    'patient_name' => $patientName,
                    'package_name' => $packageName,
                    'treatment_date' => $treatmentDate,
                    'deducted_sessions' => $deductedSessions,
                    'commission_amount' => $commission,
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
        return ['员工姓名', '客户姓名', '套餐名称', '服务日期', '扣减次数', '本次提成'];
    }

    public function map($row): array
    {
        return [
            $row['employee_name'],
            $row['patient_name'],
            $row['package_name'],
            $row['treatment_date'],
            $row['deducted_sessions'],
            $row['commission_amount'],
        ];
    }

    public function title(): string
    {
        return '服务明细';
    }
}
