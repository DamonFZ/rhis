<?php

namespace App\Filament\Pages;

use App\Models\Appointment;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class AppointmentCalendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = '预约看板';

    protected static ?string $title = '预约看板';

    protected static string $view = 'filament.pages.appointment-calendar';

    public function getAppointmentsProperty(): array
    {
        $appointments = Appointment::with(['patientProfile', 'therapist'])
            ->whereMonth('start_time', now()->month)
            ->whereYear('start_time', now()->year)
            ->where('status', '!=', 0)
            ->orderBy('start_time')
            ->get();

        return $appointments->map(function (Appointment $appointment) {
            $patientName = $appointment->patientProfile?->name ?? '未知客户';
            $therapistName = $appointment->therapist?->name ?? '未指定';

            $statusColors = [
                1 => '#0ea5e9', // 已预约 - sky-500
                2 => '#22c55e', // 已履约 - green-500
            ];

            return [
                'id' => $appointment->id,
                'start' => $appointment->start_time->toIso8601String(),
                'end' => $appointment->end_time->toIso8601String(),
                'title' => "{$patientName} · {$therapistName}",
                'description' => $appointment->remark ?: '无备注',
                'color' => $statusColors[$appointment->status] ?? '#6b7280',
                'status' => $appointment->status,
            ];
        })->toArray();
    }
}
