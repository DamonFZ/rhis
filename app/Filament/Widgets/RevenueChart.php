<?php

namespace App\Filament\Widgets;

use App\Models\PatientPackage;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RevenueChart extends ChartWidget
{
    protected static ?string $heading = '月度销售额趋势';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $startDate = now()->subMonths(11)->startOfMonth();
        $endDate = now()->endOfMonth();
        
        // 获取过去12个月的销售额数据
        $monthlyData = PatientPackage::select(
            DB::raw('DATE_FORMAT(purchase_date, "%Y-%m") as month'),
            DB::raw('COALESCE(SUM(price), 0) as total')
        )
        ->whereBetween('purchase_date', [$startDate, $endDate])
        ->groupBy('month')
        ->orderBy('month')
        ->pluck('total', 'month');
        
        // 生成完整的12个月标签和数据（填充缺失月份）
        $labels = [];
        $data = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $monthKey = $currentDate->format('Y-m');
            $labels[] = $monthKey;
            $data[] = $monthlyData->get($monthKey, 0);
            $currentDate->addMonth();
        }

        return [
            'datasets' => [
                [
                    'label' => '月度销售额',
                    'data' => $data,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
