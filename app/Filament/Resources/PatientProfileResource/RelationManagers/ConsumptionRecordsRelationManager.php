<?php

namespace App\Filament\Resources\PatientProfileResource\RelationManagers;

use App\Models\ConsumptionRecord;
use App\Models\PatientPackage;
use App\Models\RehabPackage;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConsumptionRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'consumptionRecords';

    protected static ?string $recordTitleAttribute = 'treatment_date';

    protected static ?string $title = '划扣记录';

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
                // 隐藏字段，由模型逻辑自动设置
                Forms\Components\Hidden::make('remaining_sessions')
                    ->default(0),
                Forms\Components\Hidden::make('package_name')
                    ->default(''), // 由关联自动填充
                Forms\Components\Placeholder::make('warning_message')
                    ->label('')
                    ->columnSpanFull()
                    ->hidden(fn (callable $get) => empty($get('warning_message')))
                    ->content(fn (callable $get) => '<span class="text-red-500">' . $get('warning_message') . '</span>'),
            ]);
    }

    protected function handleRecordCreation(array $data): ConsumptionRecord
    {
        return DB::transaction(function () use ($data) {
            // 1. 创建划扣记录前先移除 employee_ids
            $employeeIds = $data['employee_ids'] ?? [];
            $filteredData = collect($data)->except('employee_ids')->toArray();
            
            $record = ConsumptionRecord::create($filteredData);
            
            // 2. 计算提成
            if ($record->patient_package_id) {
                $patientPackage = $record->patientPackage;
                $deductedSessions = $record->deducted_sessions;
                
                // 通过套餐编码找到字典表获取提成
                $rehabPackage = RehabPackage::where('package_code', $patientPackage->package_code)->first();
                $baseCommission = $rehabPackage ? $rehabPackage->commission_per_service : 0;
                $totalCommission = $baseCommission * $deductedSessions;
                
                // 3. 在员工间平分
                $employeeCount = count($employeeIds);
                if ($employeeCount > 0) {
                    $splitCommission = $totalCommission / $employeeCount;
                    $syncData = [];
                    foreach ($employeeIds as $employeeId) {
                        $syncData[$employeeId] = ['commission_amount' => $splitCommission];
                    }
                    $record->employees()->sync($syncData);
                }
            }
            
            return $record;
        });
    }

    protected function handleRecordUpdate(ConsumptionRecord $record, array $data): ConsumptionRecord
    {
        return DB::transaction(function () use ($record, $data) {
            // 1. 更新记录前先移除 employee_ids
            $employeeIds = $data['employee_ids'] ?? [];
            $filteredData = collect($data)->except('employee_ids')->toArray();
            
            $record->update($filteredData);
            
            // 重新计算提成
            if ($record->patient_package_id) {
                $patientPackage = $record->patientPackage;
                $deductedSessions = $record->deducted_sessions;
                
                $rehabPackage = RehabPackage::where('package_code', $patientPackage->package_code)->first();
                $baseCommission = $rehabPackage ? $rehabPackage->commission_per_service : 0;
                $totalCommission = $baseCommission * $deductedSessions;
                
                $employeeCount = count($employeeIds);
                if ($employeeCount > 0) {
                    $splitCommission = $totalCommission / $employeeCount;
                    $syncData = [];
                    foreach ($employeeIds as $employeeId) {
                        $syncData[$employeeId] = ['commission_amount' => $splitCommission];
                    }
                    $record->employees()->sync($syncData);
                } else {
                    $record->employees()->detach();
                }
            }
            
            return $record;
        });
    }

    protected function fillFormBeforeEditing(Forms\ComponentContainer $form, ConsumptionRecord $record): void
    {
        $form->fill([
            ...$record->attributesToArray(),
            'employee_ids' => $record->employees()->pluck('user_id')->toArray(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patientPackage.package_name')
                    ->label('套餐名称')
                    ->sortable(),
                Tables\Columns\TextColumn::make('employees.name')
                    ->label('服务员工')
                    ->listWithLineBreaks(),
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
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->defaultSort('treatment_date', 'desc');
    }
}