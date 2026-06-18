<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class AppointmentCalendarWidget extends FullCalendarWidget
{
    protected static ?string $heading = '预约看板';

    public function config(): array
    {
        return [
            // 默认显示带有时间轴的周视图
            'initialView' => 'timeGridWeek',
            // 头部工具栏配置：左侧翻页，中间标题，右侧视图切换按钮
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
            ],
            // 营业时间设置（隐藏半夜不必要的空白网格，可根据门店实际情况修改）
            'slotMinTime' => '08:00:00',
            'slotMaxTime' => '22:00:00',
            // 隐藏"全天"槽位，因为门诊预约通常都是具体时段
            'allDaySlot' => false,
            // 将按钮文本汉化
            'buttonText' => [
                'today' => '今天',
                'month' => '月',
                'week'  => '周',
                'day'   => '日',
                'list'  => '议程',
            ],
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        return Appointment::with(['patientProfile', 'therapist'])
            ->where('start_time', '>=', $fetchInfo['start'])
            ->where('end_time', '<=', $fetchInfo['end'])
            ->get()
            ->map(function (Appointment $appointment) {
                $patientName = $appointment->patientProfile?->name ?? '未知客户';
                $therapistName = $appointment->therapist?->name ?? '未指定';

                $colors = [
                    0 => '#9ca3af', // 已取消 - gray
                    1 => '#3b82f6', // 已预约 - blue
                    2 => '#22c55e', // 已履约 - green
                ];

                return [
                    'id'    => $appointment->id,
                    'title' => "{$patientName} · {$therapistName}",
                    'start' => $appointment->start_time->toDateTimeString(),
                    'end'   => $appointment->end_time->toDateTimeString(),
                    'color' => $colors[$appointment->status] ?? '#3b82f6',
                    'extendedProps' => [
                        'remark'  => $appointment->remark,
                        'status'  => $appointment->status,
                    ],
                ];
            })
            ->toArray();
    }
}
