<?php

namespace App\Filament\Resources\PatientProfileResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PatientPackagesRelationManager extends RelationManager
{
    protected static string $relationship = 'patientPackages';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('package_name')
                    ->label('套餐名称')
                    ->required()
                    ->maxLength(200),
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
                Forms\Components\TextInput::make('price')
                    ->label('套餐价格')
                    ->numeric()
                    ->prefix('¥')
                    ->default(0),
                Forms\Components\Select::make('status')
                    ->label('状态')
                    ->options([
                        'active' => '有效',
                        'completed' => '已完成',
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('package_name')
            ->columns([
                Tables\Columns\TextColumn::make('package_name')
                    ->label('套餐名称')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_sessions')
                    ->label('总次数')
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining_sessions')
                    ->label('剩余次数')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('套餐价格')
                    ->money('CNY')
                    ->sortable(),
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
