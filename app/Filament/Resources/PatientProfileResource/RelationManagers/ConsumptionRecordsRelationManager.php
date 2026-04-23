<?php

namespace App\Filament\Resources\PatientProfileResource\RelationManagers;

use App\Models\ConsumptionRecord;
use App\Models\PatientPackage;
use App\Models\RehabPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patientPackage.package_name')
                    ->label('套餐名称')
                    ->sortable(),
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