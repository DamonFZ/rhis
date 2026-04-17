<?php

namespace App\Filament\Resources\IcdCodeResource\Pages;

use App\Filament\Resources\IcdCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIcdCodes extends ListRecords
{
    protected static string $resource = IcdCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}