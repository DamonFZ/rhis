<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AppointmentCalendarWidget;
use Filament\Pages\Page;

class AppointmentCalendarPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = '预约看板';

    protected static ?string $title = '预约看板';

    protected static string $view = 'filament.pages.appointment-calendar-page';

    protected static ?int $navigationSort = 10;

    public function getHeaderWidgets(): array
    {
        return [
            AppointmentCalendarWidget::class,
        ];
    }
}
