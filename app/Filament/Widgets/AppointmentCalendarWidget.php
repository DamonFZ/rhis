<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Form;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class AppointmentCalendarWidget extends FullCalendarWidget
{
    protected static ?string $heading = '预约看板';

    public string|null|\Illuminate\Database\Eloquent\Model $model = Appointment::class;

    public function config(): array
    {
        return [
            // 核心优化：定义一个滚动的 7 天视图，它会自动以当天为起始日
            'views' => [
                'rollingWeek' => [
                    'type' => 'timeGrid',
                    'duration' => ['days' => 7],
                    'buttonText' => '周',
                ],
            ],
            // 默认加载自定义的滚动周视图
            'initialView' => 'rollingWeek',
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,rollingWeek,timeGridDay,listWeek',
            ],
            'slotMinTime' => '08:00:00',
            'slotMaxTime' => '22:00:00',
            'allDaySlot' => false,
            'buttonText' => [
                'today' => '今天',
                'month' => '月',
                'week'  => '周',
                'day'   => '日',
                'list'  => '议程',
            ],
            'selectable' => true,
            'selectMirror' => true,
            'editable' => true,
            'selectOverlap' => true,
        ];
    }

    public function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('patient_profile_id')
                ->label('预约客户')
                ->options(\App\Models\PatientProfile::pluck('name', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\Select::make('therapist_id')
                ->label('康复师')
                ->options(\App\Models\User::pluck('name', 'id'))
                ->searchable()
                ->nullable(),
            Forms\Components\DateTimePicker::make('start_time')->label('开始时间')->required(),
            Forms\Components\DateTimePicker::make('end_time')->label('结束时间')->required(),
            Forms\Components\Select::make('status')
                ->label('状态')
                ->options([0 => '已取消', 1 => '已预约', 2 => '已履约'])
                ->default(1)
                ->required(),
            Forms\Components\Textarea::make('remark')->label('备注')->rows(2),
        ];
    }

    protected function headerActions(): array
    {
        return [
            \Saade\FilamentFullCalendar\Actions\CreateAction::make()
                ->label('新增预约')
                ->model(Appointment::class)
                ->mountUsing(function (Form $form, array $arguments) {
                    $form->fill([
                        'start_time' => isset($arguments['start']) ? \Carbon\Carbon::parse($arguments['start'])->toDateTimeString() : now()->toDateTimeString(),
                        'end_time'   => isset($arguments['end']) ? \Carbon\Carbon::parse($arguments['end'])->toDateTimeString() : now()->addHour()->toDateTimeString(),
                        'status'     => 1,
                    ]);
                })
        ];
    }

    protected function modalActions(): array
    {
        return [
            \Saade\FilamentFullCalendar\Actions\EditAction::make()
                ->label('编辑')
                ->model(Appointment::class),
            \Saade\FilamentFullCalendar\Actions\DeleteAction::make()
                ->label('删除')
                ->model(Appointment::class),
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        return Appointment::with('patientProfile')
            ->where('start_time', '>=', $fetchInfo['start'])
            ->where('end_time', '<=', $fetchInfo['end'])
            ->get()
            ->map(function (Appointment $appointment) {
                $remarkText = $appointment->remark ? ' - ' . $appointment->remark : '';

                $color = match ((int) $appointment->status) {
                    0 => '#9ca3af', // 灰色 (已取消)
                    1 => '#3b82f6', // 蓝色 (已预约)
                    2 => '#22c55e', // 绿色 (已履约)
                    default => '#3b82f6',
                };

                return [
                    'id'    => $appointment->id,
                    'title' => ($appointment->patientProfile?->name ?? '未知客户') . $remarkText,
                    'start' => $appointment->start_time->toDateTimeString(),
                    'end'   => $appointment->end_time->toDateTimeString(),
                    'color' => $color,
                ];
            })
            ->toArray();
    }
}
