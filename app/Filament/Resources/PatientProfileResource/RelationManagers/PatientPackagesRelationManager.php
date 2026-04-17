<?php

namespace App\Filament\Resources\PatientProfileResource\RelationManagers;

use App\Models\RehabPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class PatientPackagesRelationManager extends RelationManager
{
    protected static string $relationship = 'patientPackages';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('rehab_package_id')
                    ->label('选择康复套餐')
                    ->options(RehabPackage::where('status', 'active')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state) {
                            $rehabPackage = RehabPackage::find($state);
                            if ($rehabPackage) {
                                $set('package_code', $rehabPackage->code);
                                $set('package_name', $rehabPackage->name);
                                $set('package_type', $rehabPackage->package_type);
                                $set('total_sessions', $rehabPackage->total_sessions);
                                $set('remaining_sessions', $rehabPackage->total_sessions);
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
                    ])
                    ->columns(2),
            ]);
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
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('到期日期')
                    ->date()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state && Carbon::parse($state)->isPast() ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('status')
                    ->label('状态')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active' => '有效',
                        'completed' => '已完成',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active' => 'success',
                        'completed' => 'gray',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime()
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
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
