<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class AppointmentCalendarWidget extends FullCalendarWidget
{
    protected static ?string $heading = '预约看板';

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
