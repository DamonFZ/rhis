<?php

namespace App\Filament\Resources\PatientProfileResource\RelationManagers;

use App\Models\CommissionSetting;
use App\Models\ConsumptionRecord;
use App\Models\PatientPackage;
use App\Models\RehabPackage;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ConsumptionRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'consumptionRecords';

    protected static ?string $recordTitleAttribute = 'treatment_date';

    protected static ?string $title = '划扣记录';

    public array $tempEmployeeIds = [];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('patient_package_id')
                    ->label('选择套餐包')
                    ->options(function () {
                        $patientProfile = $this->getOwnerRecord();
                        return PatientPackage::where('patient_profile_id', $patientProfile->id)
                            ->where('status', 'active')
                            ->pluck('package_name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->required()
                    ->reactive(),
                Forms\Components\Select::make('employee_ids')
                    ->label('服务员工（可多选）')
                    ->multiple()
                    ->options(User::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->helperText('提成将在选中的员工间平分'),
//                    ->dehydrated(false), // 核心 1：绝对不能少！告诉 Filament 别把它当主表字段保存
                Forms\Components\TextInput::make('deducted_sessions')
                    ->label('扣减次数')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (callable $set, $state, $get) {
                        $packageId = $get('patient_package_id');
                        if ($packageId && $state) {
                            $package = PatientPackage::find($packageId);
                            if ($package && $package->remaining_sessions < $state) {
                                $set('warning_message', '套餐剩余次数不足，无法扣减');
                            } else {
                                $set('warning_message', '');
                            }
                        }
                    }),
                Forms\Components\DatePicker::make('treatment_date')
                    ->label('康复日期')
                    ->default(now())
                    ->required(),
                Forms\Components\Textarea::make('treatment_content')
                    ->label('康复内容')
                    ->columnSpanFull(),
                Forms\Components\Hidden::make('remaining_sessions')
                    ->default(0),
                Forms\Components\Hidden::make('package_name')
                    ->default(''),
                Forms\Components\Placeholder::make('warning_message')
                    ->label('')
                    ->columnSpanFull()
                    ->hidden(fn (callable $get) => empty($get('warning_message')))
                    ->content(fn (callable $get) => '<span class="text-red-500">' . $get('warning_message') . '</span>'),
            ]);
    }

    // 核心 2：必须改为 public 方法，让闭包可以无障碍调用
    public function syncEmployeesLogic(ConsumptionRecord $record, array $employeeIds): void
    {
        if (empty($employeeIds) || !$record->patient_package_id) {
            $record->employees()->detach();
            return;
        }

        $patientPackage = PatientPackage::find($record->patient_package_id);
        if (!$patientPackage) {
            return;
        }

        $deductedSessions = $record->deducted_sessions;

        // 从全局提成设置中读取单次服务提成
        $commissionSetting = CommissionSetting::first();
        $baseCommission = $commissionSetting ? ($commissionSetting->service_commission ?? 15.00) : 15.00;
        $totalCommission = $baseCommission * $deductedSessions;

        $employeeCount = count($employeeIds);
        if ($employeeCount > 0) {
            $splitCommission = $totalCommission / $employeeCount;
            $syncData = [];
            foreach ($employeeIds as $employeeId) {
                // 将计算好的金额压入同步数据
                $syncData[$employeeId] = ['commission_amount' => $splitCommission];
            }
            $record->employees()->sync($syncData);
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('employees'))
            ->columns([
                Tables\Columns\TextColumn::make('patientPackage.package_name')
                    ->label('套餐名称')
                    ->sortable(),
                Tables\Columns\TextColumn::make('employees')
                    ->label('服务员工')
                    ->formatStateUsing(function ($state, $record) {
                        $employees = $record->employees;
                        if ($employees->isEmpty()) {
                            return '-';
                        }
                        return $employees->pluck('name')->join(', ');
                    })
                    ->default('-'),
                Tables\Columns\TextColumn::make('treatment_date')
                    ->label('康复日期')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deducted_sessions')
                    ->label('扣减次数')
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining_sessions')
                    ->label('剩余次数')
                    ->sortable(),
                Tables\Columns\TextColumn::make('treatment_content')
                    ->label('康复内容')
                    ->limit(50)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    // 1. 保存前：把员工ID截获到暂存区，并从保存数据中剔除（防止报错）
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                        $livewire->tempEmployeeIds = $data['employee_ids'] ?? [];
                        unset($data['employee_ids']);
                        return $data;
                    })
                    // 2. 保存后：从中转站拿出ID执行提成计算
                    ->after(function (ConsumptionRecord $record, RelationManager $livewire) {
                        $livewire->syncEmployeesLogic($record, $livewire->tempEmployeeIds);
                        $livewire->tempEmployeeIds = []; // 用完清空
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    // 1. 加载编辑表单时：回填之前选中的员工
                    ->mutateRecordDataUsing(function (ConsumptionRecord $record, array $data): array {
                        $data['employee_ids'] = $record->employees()->pluck('users.id')->toArray();
                        return $data;
                    })
                    // 2. 保存修改前：截获并剔除
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                        $livewire->tempEmployeeIds = $data['employee_ids'] ?? [];
                        unset($data['employee_ids']);
                        return $data;
                    })
                    // 3. 保存修改后：重新计算并同步
                    ->after(function (ConsumptionRecord $record, RelationManager $livewire) {
                        $livewire->syncEmployeesLogic($record, $livewire->tempEmployeeIds);
                        $livewire->tempEmployeeIds = [];
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
//            ->headerActions([
//                Tables\Actions\CreateAction::make()
//                    // 核心 3：利用依赖注入直接抓取表单原生数据
//                    ->after(function (ConsumptionRecord $record, Forms\Form $form, RelationManager $livewire) {
//                        $employeeIds = $form->getRawState()['employee_ids'] ?? [];
//                        $livewire->syncEmployeesLogic($record, $employeeIds);
//                    }),
//            ])
//            ->actions([
//                Tables\Actions\EditAction::make()
//                    ->mutateRecordDataUsing(function (ConsumptionRecord $record, array $data): array {
//                        // 核心 4：编辑页面数据回填
//                        $data['employee_ids'] = $record->employees()->pluck('users.id')->toArray();
//                        return $data;
//                    })
//                    ->after(function (ConsumptionRecord $record, Forms\Form $form, RelationManager $livewire) {
//                        $employeeIds = $form->getRawState()['employee_ids'] ?? [];
//                        $livewire->syncEmployeesLogic($record, $employeeIds);
//                    }),
//                Tables\Actions\DeleteAction::make(),
//            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('treatment_date', 'desc');
    }
}
