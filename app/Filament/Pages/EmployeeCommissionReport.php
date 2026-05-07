<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\ConsumptionRecord;
use App\Models\PatientPackage;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeCommissionReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    
    protected static string $view = 'filament.pages.employee-commission-report';

    protected static ?string $navigationLabel = '员工提成报表';

    protected static ?string $title = '员工月度提成报表';

    protected static ?string $navigationGroup = '数据报表';

    public $selectedMonth;

    public function mount(): void
    {
        $this->selectedMonth = now()->format('Y-m');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(User::query())
            ->columns([
                TextColumn::make('name')
                    ->label('员工姓名')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('service_commission')
                    ->label('服务提成')
                    ->money('CNY')
                    ->sortable()
                    ->getStateUsing(function (User $record) {
                        $monthParts = explode('-', $this->selectedMonth);
                        $year = $monthParts[0];
                        $month = $monthParts[1];
                        
                        $records = $record->consumptionRecords()
                            ->whereYear('treatment_date', $year)
                            ->whereMonth('treatment_date', $month)
                            ->get();
                        
                        $total = 0;
                        foreach ($records as $cr) {
                            $pivot = $cr->pivot;
                            $total += $pivot ? $pivot->commission_amount : 0;
                        }
                        
                        return $total;
                    }),
                TextColumn::make('sales_commission')
                    ->label('销售提成')
                    ->money('CNY')
                    ->sortable()
                    ->getStateUsing(function (User $record) {
                        $monthParts = explode('-', $this->selectedMonth);
                        $year = $monthParts[0];
                        $month = $monthParts[1];
                        
                        return PatientPackage::where('salesperson_id', $record->id)
                            ->whereYear('purchase_date', $year)
                            ->whereMonth('purchase_date', $month)
                            ->sum('sales_commission');
                    }),
                TextColumn::make('total_commission')
                    ->label('月度总提成')
                    ->money('CNY')
                    ->sortable()
                    ->getStateUsing(function (User $record) {
                        $monthParts = explode('-', $this->selectedMonth);
                        $year = $monthParts[0];
                        $month = $monthParts[1];
                        
                        // 服务提成
                        $records = $record->consumptionRecords()
                            ->whereYear('treatment_date', $year)
                            ->whereMonth('treatment_date', $month)
                            ->get();
                        
                        $serviceTotal = 0;
                        foreach ($records as $cr) {
                            $pivot = $cr->pivot;
                            $serviceTotal += $pivot ? $pivot->commission_amount : 0;
                        }
                        
                        // 销售提成
                        $salesTotal = PatientPackage::where('salesperson_id', $record->id)
                            ->whereYear('purchase_date', $year)
                            ->whereMonth('purchase_date', $month)
                            ->sum('sales_commission');
                        
                        return $serviceTotal + $salesTotal;
                    }),
                TextColumn::make('service_count')
                    ->label('服务次数')
                    ->sortable()
                    ->getStateUsing(function (User $record) {
                        $monthParts = explode('-', $this->selectedMonth);
                        $year = $monthParts[0];
                        $month = $monthParts[1];
                        
                        return $record->consumptionRecords()
                            ->whereYear('treatment_date', $year)
                            ->whereMonth('treatment_date', $month)
                            ->count();
                    }),
                TextColumn::make('sales_count')
                    ->label('销售单数')
                    ->sortable()
                    ->getStateUsing(function (User $record) {
                        $monthParts = explode('-', $this->selectedMonth);
                        $year = $monthParts[0];
                        $month = $monthParts[1];
                        
                        return PatientPackage::where('salesperson_id', $record->id)
                            ->whereYear('purchase_date', $year)
                            ->whereMonth('purchase_date', $month)
                            ->count();
                    }),
            ])
            ->filters([
                Filter::make('selected_month')
                    ->form([
                        DatePicker::make('month')
                            ->label('选择月份')
                            ->default(now()->format('Y-m'))
                            ->format('Y-m')
                            ->displayFormat('Y年m月')
                            ->required()
                            ->helperText('选择要统计的月份'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['month'])) {
                            $this->selectedMonth = $data['month'];
                        }
                        return $query;
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!isset($data['month'])) {
                            return null;
                        }
                        $monthParts = explode('-', $data['month']);
                        return '统计月份: ' . $monthParts[0] . '年' . $monthParts[1] . '月';
                    }),
            ])
            ->actions([
                Action::make('view_details')
                    ->label('查看详情')
                    ->icon('heroicon-o-eye')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('employee_name')
                            ->label('员工姓名')
                            ->disabled(),
                        \Filament\Forms\Components\TextInput::make('month')
                            ->label('统计月份')
                            ->disabled(),
                        \Filament\Forms\Components\TextInput::make('service_commission')
                            ->label('服务提成')
                            ->prefix('¥')
                            ->disabled(),
                        \Filament\Forms\Components\TextInput::make('sales_commission')
                            ->label('销售提成')
                            ->prefix('¥')
                            ->disabled(),
                        \Filament\Forms\Components\Repeater::make('service_records')
                            ->label('服务记录明细')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('patient_name')
                                    ->label('客户姓名'),
                                \Filament\Forms\Components\TextInput::make('package_name')
                                    ->label('套餐名称'),
                                \Filament\Forms\Components\TextInput::make('treatment_date')
                                    ->label('服务日期'),
                                \Filament\Forms\Components\TextInput::make('deducted_sessions')
                                    ->label('扣减次数'),
                                \Filament\Forms\Components\TextInput::make('commission_amount')
                                    ->label('本次提成')
                                    ->prefix('¥'),
                            ])
                            ->columns(3)
                            ->reorderable(false)
                            ->addable(false)
                            ->deletable(false)
                            ->helperText('显示该员工当月的所有服务记录明细'),
                        \Filament\Forms\Components\Repeater::make('sales_records')
                            ->label('销售记录明细')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('patient_name')
                                    ->label('客户姓名'),
                                \Filament\Forms\Components\TextInput::make('package_name')
                                    ->label('套餐名称'),
                                \Filament\Forms\Components\TextInput::make('sales_type')
                                    ->label('销售类型'),
                                \Filament\Forms\Components\TextInput::make('price')
                                    ->label('套餐价格')
                                    ->prefix('¥'),
                                \Filament\Forms\Components\TextInput::make('commission_amount')
                                    ->label('本次提成')
                                    ->prefix('¥'),
                            ])
                            ->columns(3)
                            ->reorderable(false)
                            ->addable(false)
                            ->deletable(false)
                            ->helperText('显示该员工当月的所有销售记录明细'),
                    ])
                    ->fillForm(function (User $record) {
                        $monthParts = explode('-', $this->selectedMonth);
                        $year = $monthParts[0];
                        $month = $monthParts[1];
                        
                        // 服务记录
                        $consumptionRecords = $record->consumptionRecords()
                            ->whereYear('treatment_date', $year)
                            ->whereMonth('treatment_date', $month)
                            ->with(['patient', 'patientPackage'])
                            ->get();
                        
                        $serviceDetails = [];
                        $serviceTotal = 0;
                        foreach ($consumptionRecords as $consumption) {
                            $serviceTotal += $consumption->pivot->commission_amount;
                            $serviceDetails[] = [
                                'patient_name' => $consumption->patient ? $consumption->patient->name : '未知',
                                'package_name' => $consumption->patientPackage ? $consumption->patientPackage->package_name : '未知',
                                'treatment_date' => $consumption->treatment_date->format('Y-m-d'),
                                'deducted_sessions' => $consumption->deducted_sessions,
                                'commission_amount' => $consumption->pivot->commission_amount,
                            ];
                        }
                        
                        // 销售记录
                        $salesRecords = PatientPackage::where('salesperson_id', $record->id)
                            ->whereYear('purchase_date', $year)
                            ->whereMonth('purchase_date', $month)
                            ->with('patient')
                            ->get();
                        
                        $salesDetails = [];
                        $salesTotal = 0;
                        $salesTypeLabels = [
                            1 => '自主开发',
                            2 => '康复续卡',
                            3 => '协助开单',
                        ];
                        foreach ($salesRecords as $sale) {
                            $salesTotal += $sale->sales_commission;
                            $salesDetails[] = [
                                'patient_name' => $sale->patient ? $sale->patient->name : '未知',
                                'package_name' => $sale->package_name,
                                'sales_type' => isset($salesTypeLabels[$sale->sales_type]) ? $salesTypeLabels[$sale->sales_type] : '-',
                                'price' => $sale->price,
                                'commission_amount' => $sale->sales_commission,
                            ];
                        }
                        
                        return [
                            'employee_name' => $record->name,
                            'month' => $year . '年' . $month . '月',
                            'service_commission' => $serviceTotal,
                            'sales_commission' => $salesTotal,
                            'service_records' => $serviceDetails,
                            'sales_records' => $salesDetails,
                        ];
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('关闭'),
            ])
            ->defaultSort('name');
    }
}
