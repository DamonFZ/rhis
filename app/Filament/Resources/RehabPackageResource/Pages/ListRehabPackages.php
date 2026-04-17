<?php

namespace App\Filament\Resources\RehabPackageResource\Pages;

use App\Filament\Resources\RehabPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRehabPackages extends ListRecords
{
    protected static string $resource = RehabPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}