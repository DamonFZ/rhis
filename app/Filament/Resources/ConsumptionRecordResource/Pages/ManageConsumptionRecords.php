<?php

namespace App\Filament\Resources\ConsumptionRecordResource\Pages;

use App\Filament\Resources\ConsumptionRecordResource;
use Filament\Resources\Pages\ManageRecords;

class ManageConsumptionRecords extends ManageRecords
{
    protected static string $resource = ConsumptionRecordResource::class;

    protected static ?string $title = '康复记录';

    protected function getHeaderActions(): array
    {
        return [
            // 只读看板，不显示新建按钮
        ];
    }
}
