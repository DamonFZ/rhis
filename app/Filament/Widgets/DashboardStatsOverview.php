<?php

namespace App\Filament\Widgets;

use App\Models\PatientProfile;
use App\Models\PatientPackage;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('总客户数', PatientProfile::count())
                ->description('持续增长中')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->icon('heroicon-m-user-group'),
            Stat::make('进行中的套餐', PatientPackage::where('status', 'active')->count())
                ->description('当前活跃套餐')
                ->color('primary')
                ->icon('heroicon-m-clipboard-document-list'),
            Stat::make('本月新增客户', PatientProfile::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count())
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('info')
                ->icon('heroicon-m-chart-bar'),
        ];
    }
}
