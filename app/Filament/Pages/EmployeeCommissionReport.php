<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\ConsumptionRecord;
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
                TextColumn::make('total_commission')
                    ->label('月度总提成')
                    ->money('CNY')
                    ->sortable()
                    ->getStateUsing(function (User $record) {
                        $monthParts = explode('-', $this->selectedMonth);
                        $year = $monthParts[0];
                        $month = $monthParts[1];
                        
                        // 使用 pivot 表直接计算提成
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
                        \Filament\Forms\Components\Repeater::make('records')
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
                    ])
                    ->fillForm(function (User $record) {
                        $monthParts = explode('-', $this->selectedMonth);
                        $year = $monthParts[0];
                        $month = $monthParts[1];
                        
                        $records = $record->consumptionRecords()
                            ->whereYear('treatment_date', $year)
                            ->whereMonth('treatment_date', $month)
                            ->with(['patient', 'patientPackage'])
                            ->get();
                        
                        $details = [];
                        foreach ($records as $consumption) {
                            $details[] = [
                                'patient_name' => $consumption->patient ? $consumption->patient->name : '未知',
                                'package_name' => $consumption->patientPackage ? $consumption->patientPackage->package_name : '未知',
                                'treatment_date' => $consumption->treatment_date->format('Y-m-d'),
                                'deducted_sessions' => $consumption->deducted_sessions,
                                'commission_amount' => $consumption->pivot->commission_amount,
                            ];
                        }
                        
                        return [
                            'employee_name' => $record->name,
                            'month' => $year . '年' . $month . '月',
                            'records' => $details,
                        ];
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('关闭'),
            ])
            ->defaultSort('name');
    }
}
