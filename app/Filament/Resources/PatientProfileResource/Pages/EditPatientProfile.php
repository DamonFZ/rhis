<?php

namespace App\Filament\Resources\PatientProfileResource\Pages;

use App\Filament\Resources\PatientProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPatientProfile extends EditRecord
{
    protected static string $resource = PatientProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}