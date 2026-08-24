<?php

namespace App\Filament\Resources\ConsumptionRecordResource\Pages;

use App\Filament\Resources\ConsumptionRecordResource;
use App\Models\CommissionSetting;
use App\Models\ConsumptionRecord;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRecords;

class ManageConsumptionRecords extends ManageRecords
{
    protected static string $resource = ConsumptionRecordResource::class;

    protected static ?string $title = '康复记录';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('anonymous_checkout')
                ->label('散客/体验开单')
                ->color('warning')
                ->icon('heroicon-o-bolt')
                ->modalHeading('散客/体验开单')
                ->modalSubmitActionLabel('确认开单')
                ->form([
                    TextInput::make('amount')
                        ->label('金额')
                        ->numeric()
                        ->prefix('¥')
                        ->default(0)
                        ->required(),
                    Select::make('therapists')
                        ->label('康复师')
                        ->multiple()
                        ->options(fn () => User::pluck('name', 'id')->toArray())
                        ->searchable()
                        ->required()
                        ->helperText('提成将在选中的康复师间平分'),
                    DatePicker::make('treatment_date')
                        ->label('康复日期')
                        ->default(now())
                        ->required(),
                    Textarea::make('treatment_content')
                        ->label('康复内容')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $therapistIds = $data['therapists'] ?? [];
                    $amount = $data['amount'] ?? 0;

                    $record = ConsumptionRecord::create([
                        'is_anonymous' => true,
                        'patient_profile_id' => null,
                        'patient_package_id' => null,
                        'package_name' => '散客消费',
                        'source_type' => ConsumptionRecord::SOURCE_TRIAL,
                        'amount' => $amount,
                        'deducted_sessions' => 1,
                        'remaining_sessions' => 0,
                        'treatment_date' => $data['treatment_date'],
                        'treatment_content' => $data['treatment_content'] ?? null,
                    ]);

                    if (! empty($therapistIds)) {
                        $commissionSetting = CommissionSetting::first();
                        $baseCommission = $commissionSetting ? ($commissionSetting->service_commission ?? 15.00) : 15.00;
                        $therapistCount = count($therapistIds);
                        $splitCommission = $baseCommission / $therapistCount;

                        $syncData = [];
                        foreach ($therapistIds as $therapistId) {
                            $syncData[$therapistId] = ['commission_amount' => $splitCommission];
                        }
                        $record->employees()->sync($syncData);
                    }
                }),
        ];
    }
}
