<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConsumptionRecordResource\Pages;
use App\Models\ConsumptionRecord;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ConsumptionRecordResource extends Resource
{
    protected static ?string $model = ConsumptionRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = '康复记录';

    protected static ?string $navigationGroup = '数据报表';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('treatment_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('treatment_date')
                    ->label('康复日期')
                    ->date('Y-m-d')
                    ->sortable(),

                Tables\Columns\TextColumn::make('patientProfile.name')
                    ->label('客户名称')
                    ->weight('bold')
                    ->searchable(),

                Tables\Columns\TextColumn::make('package_name')
                    ->label('关联套餐')
                    ->searchable(),

                Tables\Columns\TextColumn::make('users.name')
                    ->label('康复师')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('deducted_sessions')
                    ->label('消耗次数')
                    ->badge(),

                Tables\Columns\TextColumn::make('treatment_content')
                    ->label('康复内容/备注')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (mb_strlen($state ?? '') <= 30) {
                            return null;
                        }

                        return $state;
                    })
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('view_patient')
                    ->label('查看档案')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('primary')
                    ->url(fn (ConsumptionRecord $record): string => PatientProfileResource::getUrl('edit', ['record' => $record->patient_profile_id])),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageConsumptionRecords::route('/'),
        ];
    }
}
