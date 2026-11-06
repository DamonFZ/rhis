<?php

namespace App\Filament\Resources\PatientProfileResource\RelationManagers;

use App\Models\RehabPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class PatientPackagesRelationManager extends RelationManager
{
    protected static string $relationship = 'patientPackages';

    protected static ?string $title = '康复套餐';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('rehab_package_id')
                    ->label('选择康复套餐')
                    ->options(RehabPackage::where('status', 1)->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state) {
                            $rehabPackage = RehabPackage::find($state);
                            if ($rehabPackage) {
                                // 营销活动：检查是否有进行中的活动赠送次数
                                $campaignInfo = \App\Models\Campaign::getActiveExtraCountForPackage($rehabPackage->id);
                                $extraCount = $campaignInfo ? $campaignInfo['extra_count'] : 0;
                                $campaignName = $campaignInfo ? $campaignInfo['campaign']->name : null;

                                $totalSessions = $rehabPackage->total_sessions + $extraCount;
                                $packageName = $campaignName
                                    ? $rehabPackage->name." [{$campaignName}赠{$extraCount}次]"
                                    : $rehabPackage->name;

                                $set('package_code', $rehabPackage->package_code);
                                $set('package_name', $packageName);
                                $set('package_type', $rehabPackage->package_type);
                                $set('total_sessions', $totalSessions);
                                $set('remaining_sessions', $totalSessions);
                                $set('price', $rehabPackage->price);
                                $set('original_price', $rehabPackage->original_price);
                                $set('average_price', $rehabPackage->average_price);
                                $set('description', $rehabPackage->description);
                                $set('is_extendable', $rehabPackage->is_extendable);
                                $set('extension_days', $rehabPackage->extension_days);
                                $set('is_shareable', $rehabPackage->is_shareable);

                                $validityDays = $rehabPackage->validity_days ?? 0;
                                $extensionDays = $rehabPackage->is_extendable ? ($rehabPackage->extension_days ?? 0) : 0;
                                $totalDays = $validityDays + $extensionDays;

                                $purchaseDate = now()->toDateString();
                                $set('purchase_date', $purchaseDate);
                                if ($totalDays > 0) {
                                    $expiryDate = Carbon::parse($purchaseDate)->addDays($totalDays)->toDateString();
                                    $set('expiry_date', $expiryDate);
                                }
                            }
                        }
                    })
                    ->hint('选择套餐后会自动填充相关信息')
                    ->columnSpanFull(),

                Forms\Components\Section::make('套餐基本信息')
                    ->schema([
                        Forms\Components\TextInput::make('package_code')
                            ->label('套餐编码')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('package_name')
                            ->label('套餐名称')
                            ->required()
                            ->maxLength(200),
                        Forms\Components\TextInput::make('package_type')
                            ->label('套餐类型')
                            ->maxLength(50),
                        Forms\Components\Textarea::make('description')
                            ->label('套餐描述')
                            ->maxLength(1000),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('次数与价格')
                    ->schema([
                        Forms\Components\TextInput::make('total_sessions')
                            ->label('总次数')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Forms\Components\TextInput::make('remaining_sessions')
                            ->label('剩余次数')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Forms\Components\TextInput::make('original_price')
                            ->label('原价')
                            ->numeric()
                            ->prefix('¥')
                            ->default(0),
                        Forms\Components\TextInput::make('price')
                            ->label('套餐价格')
                            ->numeric()
                            ->prefix('¥')
                            ->default(0),
                        Forms\Components\TextInput::make('average_price')
                            ->label('均价')
                            ->numeric()
                            ->prefix('¥')
                            ->default(0),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('其他设置')
                    ->schema([
                        Forms\Components\Toggle::make('is_extendable')
                            ->label('是否可延期'),
                        Forms\Components\TextInput::make('extension_days')
                            ->label('可延期天数')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_shareable')
                            ->label('是否可共享'),
                        Forms\Components\DatePicker::make('purchase_date')
                            ->label('购买日期')
                            ->default(now()->toDateString())
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $set, $state, $get) {
                                $validityDays = $get('validity_days') ?? 0;
                                $extensionDays = $get('is_extendable') ? ($get('extension_days') ?? 0) : 0;
                                $totalDays = $validityDays + $extensionDays;
                                if ($state && $totalDays > 0) {
                                    $expiryDate = Carbon::parse($state)->addDays($totalDays)->toDateString();
                                    $set('expiry_date', $expiryDate);
                                }
                            }),
                        Forms\Components\DatePicker::make('expiry_date')
                            ->label('到期日期'),
                        Forms\Components\Select::make('status')
                            ->label('状态')
                            ->options([
                                'active' => '有效',
                                'completed' => '已完成',
                            ])
                            ->default('active')
                            ->required(),
                        Forms\Components\Select::make('salesperson_ids')
                            ->label('开单员工（销售）')
                            ->options(\App\Models\User::pluck('name', 'id'))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Select::make('sales_type')
                            ->label('开单提成类型')
                            ->options(function () {
                                $setting = \App\Models\CommissionSetting::first();

                                return [
                                    1 => '自主开发 (提成 '.($setting->sales_type_1_rate ?? 3).'%)',
                                    2 => '康复续卡 (提成 '.($setting->sales_type_2_rate ?? 1).'%)',
                                    3 => '协助开单 (提成 '.($setting->sales_type_3_rate ?? 2).'%)',
                                ];
                            })
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }

    /**
     * 创建前：将多选员工数组转换为首个员工ID写入 salesperson_id，保留数组用于 afterCreate 平分
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $employeeIds = array_values(array_filter((array) ($data['salesperson_ids'] ?? []), 'is_numeric'));
        $data['salesperson_id'] = $employeeIds[0] ?? null;
        unset($data['salesperson_ids']);

        // 暂存员工ID数组到实例属性，供 afterCreate 使用
        $this->pendingSalespersonIds = $employeeIds;

        return $data;
    }

    /**
     * 创建后：平分销售提成并写入 patient_package_sales
     */
    protected function afterCreate(): void
    {
        $this->syncSalespersons($this->record, $this->pendingSalespersonIds ?? []);
    }

    /**
     * 编辑回显：将 pivot 中的员工ID回填到多选框
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['salesperson_ids'] = $this->record->salespersons()->pluck('user_id')->toArray();

        return $data;
    }

    /**
     * 编辑保存前：同样转换为首个员工ID
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $employeeIds = array_values(array_filter((array) ($data['salesperson_ids'] ?? []), 'is_numeric'));
        $data['salesperson_id'] = $employeeIds[0] ?? null;
        unset($data['salesperson_ids']);

        $this->pendingSalespersonIds = $employeeIds;

        return $data;
    }

    /**
     * 编辑保存后：重新同步销售分成
     */
    protected function afterSave(): void
    {
        $this->syncSalespersons($this->record, $this->pendingSalespersonIds ?? []);
    }

    /**
     * 通用：根据总提成平分并同步到中间表
     */
    protected function syncSalespersons(\App\Models\PatientPackage $package, array $employeeIds): void
    {
        $employeeCount = count($employeeIds);
        if ($employeeCount === 0) {
            $package->salespersons()->detach();

            return;
        }

        $totalCommission = (float) $package->sales_commission;
        $splitCommission = round($totalCommission / $employeeCount, 2);

        $syncData = [];
        foreach ($employeeIds as $employeeId) {
            $syncData[$employeeId] = ['commission_amount' => $splitCommission];
        }

        $package->salespersons()->sync($syncData);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('package_name')
            ->columns([
                Tables\Columns\TextColumn::make('package_code')
                    ->label('套餐编码')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('package_name')
                    ->label('套餐名称')
                    ->sortable(),
                Tables\Columns\TextColumn::make('package_type')
                    ->label('套餐类型')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_sessions')
                    ->label('总次数')
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining_sessions')
                    ->label('剩余次数')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state, $record) => $state == $record->total_sessions ? 'success' : ($state > 0 ? 'warning' : 'danger')),
                Tables\Columns\TextColumn::make('price')
                    ->label('套餐价格')
                    ->money('CNY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchase_date')
                    ->label('购买日期')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('到期日期')
                    ->date('Y-m-d')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state && Carbon::parse($state)->isPast() ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('status')
                    ->label('状态')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active' => '有效',
                        'completed' => '已用完',
                        'upgraded' => '已升级',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active' => 'success',
                        'completed' => 'gray',
                        'upgraded' => 'warning',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('salespersons.name')
                    ->label('开单员工')
                    ->badge()
                    ->separator(',')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sales_commission')
                    ->label('销售提成(元)')
                    ->money('CNY')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('状态')
                    ->options([
                        'active' => '有效',
                        'completed' => '已完成',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                \Filament\Tables\Actions\Action::make('upgrade')
                    ->label('升级套餐')
                    ->icon('heroicon-o-arrow-trending-up')
                    ->color('success')
                    ->visible(fn (\App\Models\PatientPackage $record) => $record->status === 'active')
                    ->modalWidth('md')
                    ->form([
                        \Filament\Forms\Components\Select::make('new_id')
                            ->label('新套餐')
                            ->options(\App\Models\RehabPackage::where('status', 1)->pluck('name', 'id'))
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (callable $set, $state, $livewire) {
                                if ($state && method_exists($livewire, 'getMountedTableActionRecord')) {
                                    $record = $livewire->getMountedTableActionRecord();
                                    if ($record) {
                                        $newR = \App\Models\RehabPackage::find($state);
                                        if ($newR) {
                                            $diff = $newR->price - $record->price;
                                            $set('diff', max(0, round($diff, 2)));
                                        }
                                    }
                                }
                            }),
                        \Filament\Forms\Components\TextInput::make('diff')
                            ->label('补差价金额')
                            ->numeric()
                            ->prefix('¥')
                            ->required()
                            ->hint('自动计算：新套餐价 - 原套餐价，可手动修改')
                            ->default(function (callable $get, $livewire) {
                                if (method_exists($livewire, 'getMountedTableActionRecord')) {
                                    $record = $livewire->getMountedTableActionRecord();
                                    if ($record) {
                                        $newId = $get('new_id');
                                        if ($newId) {
                                            $newR = \App\Models\RehabPackage::find($newId);
                                            if ($newR) {
                                                return max(0, round($newR->price - $record->price, 2));
                                            }
                                        }
                                    }
                                }

                                return null;
                            }),
                        \Filament\Forms\Components\TextInput::make('used_sessions')
                            ->label('结转已用次数')
                            ->numeric()
                            ->required()
                            ->hint(function ($livewire) {
                                if (method_exists($livewire, 'getMountedTableActionRecord')) {
                                    $record = $livewire->getMountedTableActionRecord();
                                    if ($record) {
                                        $used = $record->total_sessions - $record->remaining_sessions;

                                        return "原套餐已使用 {$used} 次";
                                    }
                                }

                                return '';
                            }),
                        \Filament\Forms\Components\Select::make('sales_ids')
                            ->label('升单员工')
                            ->options(\App\Models\User::pluck('name', 'id'))
                            ->multiple()
                            ->required(),
                        \Filament\Forms\Components\Select::make('sales_type')
                            ->label('提成类型')
                            ->options([1 => '自主开发', 2 => '康复续卡', 3 => '协助开单'])
                            ->required(),
                    ])
                    ->action(function (array $data, \App\Models\PatientPackage $record) {
                        \Illuminate\Support\Facades\DB::transaction(function () use ($data, $record) {
                            $usedSessions = (int) $data['used_sessions'];
                            // 升级前保留原套餐的剩余次数与总次数，用于累加
                            $oldRemaining = (int) $record->remaining_sessions;
                            $oldTotal = (int) $record->total_sessions;
                            $oldExpiry = $record->expiry_date ? Carbon::parse($record->expiry_date) : null;

                            $record->update(['status' => 'upgraded', 'remaining_sessions' => 0]);

                            $newR = \App\Models\RehabPackage::findOrFail($data['new_id']);
                            $set = \App\Models\CommissionSetting::first();
                            $rates = [1 => $set->sales_type_1_rate / 100, 2 => $set->sales_type_2_rate / 100, 3 => $set->sales_type_3_rate / 100];

                            // 营销活动：检查新套餐是否有进行中的活动赠送次数
                            $campaignInfo = \App\Models\Campaign::getActiveExtraCountForPackage($newR->id);
                            $extraCount = $campaignInfo ? $campaignInfo['extra_count'] : 0;
                            $campaignName = $campaignInfo ? $campaignInfo['campaign']->name : null;

                            $newPackageTotal = $newR->total_sessions + $extraCount;
                            $packageName = $campaignName
                                ? $newR->name." [{$campaignName}赠{$extraCount}次]"
                                : $newR->name;

                            // 次数累加：总次数 = 原总次数 + 新套餐次数(含赠送)；剩余 = 原剩余 + 新套餐次数(含赠送) - 结转已用
                            $newTotal = $oldTotal + $newPackageTotal;
                            $newRemaining = max(0, $oldRemaining + $newPackageTotal - $usedSessions);

                            // 到期日顺延：固定到期日优先，否则在原到期日（未过期）基础上顺延，否则从今日起算
                            $totalDays = ($newR->validity_days ?? 0) + ($newR->extension_days ?? 0);
                            if ($newR->valid_end_date) {
                                $expiryDate = $newR->valid_end_date;
                            } elseif ($oldExpiry && $oldExpiry->isFuture()) {
                                $expiryDate = $oldExpiry->copy()->addDays($totalDays);
                            } else {
                                $expiryDate = now()->addDays($totalDays);
                            }

                            // 销售提成平分逻辑
                            $employeeIds = array_values((array) $data['sales_ids']);
                            $employeeCount = count($employeeIds);
                            $totalCommission = $data['diff'] * ($rates[$data['sales_type']] ?? 0.03);
                            $splitCommission = $employeeCount > 0 ? round($totalCommission / $employeeCount, 2) : 0;

                            $newP = \App\Models\PatientPackage::create([
                                'patient_profile_id' => $record->patient_profile_id, 'package_code' => $newR->package_code,
                                'package_name' => $packageName, 'package_type' => $newR->package_type,
                                'total_sessions' => $newTotal, 'remaining_sessions' => $newRemaining,
                                'price' => $newR->price, 'original_price' => $newR->original_price, 'average_price' => $newR->average_price,
                                'status' => 'active', 'is_extendable' => $newR->is_extendable, 'extension_days' => $newR->extension_days,
                                'is_shareable' => $newR->is_shareable, 'purchase_date' => now(),
                                'expiry_date' => $expiryDate,
                                'salesperson_id' => $employeeIds[0] ?? null, 'sales_type' => $data['sales_type'],
                                'sales_commission' => $totalCommission,
                            ]);

                            // 为每位升单员工写入平分提成记录
                            $syncData = [];
                            foreach ($employeeIds as $employeeId) {
                                $syncData[$employeeId] = ['commission_amount' => $splitCommission];
                            }
                            $newP->salespersons()->sync($syncData);

                            if ($usedSessions > 0) {
                                \App\Models\ConsumptionRecord::create([
                                    'patient_profile_id' => $record->patient_profile_id, 'patient_package_id' => $newP->id,
                                    'package_name' => $newP->package_name, 'deducted_sessions' => 0,
                                    'remaining_sessions' => $newP->remaining_sessions,
                                    'treatment_date' => now(),
                                    'treatment_content' => "系统结转：套餐升级，扣除原套餐已用 {$usedSessions} 次",
                                ]);

                                $newP->refresh();
                            }
                        });
                        \Filament\Notifications\Notification::make()->title('套餐升级完成')->success()->send();
                    }),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
