<?php

namespace App\Filament\Widgets;

use App\Models\PatientProfile;
use App\Models\PatientPackage;
use App\Models\ConsumptionRecord;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        // 计算总销售额：统计所有客户套餐包的总价
        $totalRevenue = PatientPackage::sum('price');
        
        // 本月销售额
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $monthlyRevenue = PatientPackage::whereMonth('purchase_date', $currentMonth)
            ->whereYear('purchase_date', $currentYear)
            ->sum('price');
        
        // 上月销售额
        $lastMonth = now()->subMonth()->month;
        $lastYear = now()->subMonth()->year;
        $lastMonthRevenue = PatientPackage::whereMonth('purchase_date', $lastMonth)
            ->whereYear('purchase_date', $lastYear)
            ->sum('price');
        
        // 计算本月服务产值：SUM(每次划扣的单次均价 * 扣减次数)
        $monthlyServiceOutput = ConsumptionRecord::whereMonth('treatment_date', $currentMonth)
            ->whereYear('treatment_date', $currentYear)
            ->whereHas('patientPackage')
            ->get()
            ->sum(function ($record) {
                return $record->patientPackage ? $record->patientPackage->average_price * $record->deducted_sessions : 0;
            });
        
        // 本月服务人次：去重客户数
        $monthlyActiveClients = ConsumptionRecord::whereMonth('treatment_date', $currentMonth)
            ->whereYear('treatment_date', $currentYear)
            ->distinct('patient_profile_id')
            ->count('patient_profile_id');
        
        // 累计服务总次数
        $totalServices = ConsumptionRecord::sum('deducted_sessions');
        
        // 待处理预警：余额不足3次 或 超过30天未到店
        $actionRequired = 0;
        
        // 1. 余额不足3次的活跃套餐
        $lowBalancePackages = PatientPackage::where('status', 'active')
            ->where('remaining_sessions', '<=', 3)
            ->count();
        
        // 2. 超过30天未到店的活跃套餐
        $thirtyDaysAgo = now()->subDays(30);
        $noShowPackages = PatientPackage::where('status', 'active')
            ->whereDoesntHave('consumptionRecords', function ($query) use ($thirtyDaysAgo) {
                $query->where('treatment_date', '>=', $thirtyDaysAgo);
            })
            ->count();
        
        // 合并两个条件的数量（注意可能有重叠，需要去重）
        $actionRequired = PatientPackage::where('status', 'active')
            ->where(function ($query) use ($thirtyDaysAgo) {
                $query->where('remaining_sessions', '<=', 3)
                    ->orWhereDoesntHave('consumptionRecords', function ($subQuery) use ($thirtyDaysAgo) {
                        $subQuery->where('treatment_date', '>=', $thirtyDaysAgo);
                    });
            })
            ->count();
        
        // 计算上月对比
        $revenueChange = $lastMonthRevenue > 0 
            ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) 
            : 0;
        $revenueChangeText = $revenueChange > 0 
            ? "较上月增长 {$revenueChange}%" 
            : ($revenueChange < 0 
                ? "较上月下降 " . abs($revenueChange) . "%" 
                : "与上月持平");
        
        // 1. 耗卡进度预警：待唤醒客户（30天未到店）
        // 逻辑：有效(status=active)、有余额(remaining_sessions > 0)，且最近30天无划扣
        $toAwakenClients = PatientPackage::where('status', 'active')
            ->where('remaining_sessions', '>', 0)
            ->whereDoesntHave('consumptionRecords', function ($query) use ($thirtyDaysAgo) {
                $query->where('treatment_date', '>=', $thirtyDaysAgo);
            })
            ->count();
        
        // 2. 客户复购率
        // 逻辑：(购买过2个及以上套餐的客户数 / 总成交客户数) * 100%
        $totalCustomerCount = PatientProfile::has('patientPackages')->count();
        $repeatCustomerCount = PatientProfile::has('patientPackages', '>=', 2)->count();
        
        $retentionRate = $totalCustomerCount > 0 
            ? round(($repeatCustomerCount / $totalCustomerCount) * 100, 1) 
            : 0;
        $retentionColor = $retentionRate > 20 ? 'success' : 'gray';
        
        // 3. 热门康复项目
        $topPackage = PatientPackage::select('package_name', DB::raw('count(*) as total'))
            ->groupBy('package_name')
            ->orderBy('total', 'desc')
            ->first();
        $topPackageName = $topPackage ? $topPackage->package_name : '暂无数据';
        $topPackageCount = $topPackage ? $topPackage->total : 0;

        return [
            // 总销售额
            Stat::make('总销售额', '¥' . number_format($totalRevenue, 2))
                ->description('累计所有套餐订单金额')
                ->color('success')
                ->icon('heroicon-o-banknotes'),
            
            // 本月销售额
            Stat::make('本月销售额', '¥' . number_format($monthlyRevenue, 2))
                ->description($revenueChangeText)
                ->descriptionIcon($revenueChange > 0 ? 'heroicon-o-arrow-trending-up' : ($revenueChange < 0 ? 'heroicon-o-arrow-trending-down' : ''))
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-currency-dollar'),
            
            // 本月服务产值
            Stat::make('本月服务产值', '¥' . number_format($monthlyServiceOutput, 2))
                ->description('实际服务产生的价值')
                ->color('primary')
                ->icon('heroicon-o-sparkles'),
            
            // 本月服务人次
            Stat::make('本月服务人次', $monthlyActiveClients)
                ->description('产生划扣的去重客户')
                ->color('info')
                ->icon('heroicon-o-users'),
            
            // 累计服务总次数
            Stat::make('累计服务总次数', $totalServices)
                ->description('系统所有划扣记录')
                ->color('warning')
                ->icon('heroicon-o-clipboard-document-check'),
            
            // 待处理预警
            Stat::make('待处理预警', $actionRequired)
                ->description("余额不足: {$lowBalancePackages} | 超30天未到店: {$noShowPackages}")
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle'),
            
            // 待唤醒客户
            Stat::make('待唤醒客户（30天未到店）', $toAwakenClients)
                ->description('有余额但30天未到店')
                ->color('danger')
                ->icon('heroicon-o-exclamation-triangle'),
            
            // 客户复购率
            Stat::make('客户复购率', $retentionRate . '%')
                ->description("复购客户: {$repeatCustomerCount} / 总成交: {$totalCustomerCount}")
                ->color($retentionColor)
                ->icon('heroicon-o-arrow-path'),
            
            // 最受欢迎项目
            Stat::make('最受欢迎项目', $topPackageName)
                ->description("累计成交 {$topPackageCount} 笔")
                ->color('warning')
                ->icon('heroicon-o-fire'),
        ];
    }
}
