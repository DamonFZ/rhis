<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;

class SpineNerveMappingPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static string $view = 'filament.pages.spine-nerve-mapping';

    protected static ?string $title = '神经映射沙盘';

    protected static ?string $navigationLabel = '神经映射沙盘';

    protected static ?string $navigationGroup = '康复看板';

    public function getMaxContentWidth(): MaxWidth | string | null
    {
        return MaxWidth::Full;
    }
}
