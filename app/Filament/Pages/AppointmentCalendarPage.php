<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AppointmentCalendarWidget;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;

class AppointmentCalendarPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = '预约看板';

    protected static ?string $title = '预约看板';

    protected static string $view = 'filament.pages.appointment-calendar-page';

    protected static ?int $navigationSort = 10;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('toggle_fullscreen')
                ->label('大屏模式')
                ->icon('heroicon-o-arrows-pointing-out')
                ->color('gray')
                ->extraAttributes([
                    'x-on:click' => 'document.fullscreenElement ? document.exitFullscreen() : document.documentElement.requestFullscreen()',
                ]),
        ];
    }

    public function getMaxContentWidth(): MaxWidth|string|null
    {
        return 'full';
    }

    public function getHeaderWidgets(): array
    {
        return [
            AppointmentCalendarWidget::class,
        ];
    }
}
