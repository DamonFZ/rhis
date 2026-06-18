<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Form;
use Saade\FilamentFullCalendar\Actions;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class AppointmentCalendarWidget extends FullCalendarWidget
{
    protected static ?string $heading = '预约看板';

    public string|null|\Illuminate\Database\Eloquent\Model $model = Appointment::class;

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
            // 核心：允许点击和拉选空白时间块
            'selectable' => true,
            // 核心：允许后续拖拽修改预约时间
            'editable' => true,
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
                ->required(),
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

    // 控制点击空白处/右上角的「新建」动作
    protected function headerActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->mountUsing(function (Form $form, array $arguments) {
                    // 捕获鼠标在日历上点击的时间段，并回填到表单中
                    $form->fill([
                        'start_time' => isset($arguments['start']) ? \Carbon\Carbon::parse($arguments['start'])->toDateTimeString() : now(),
                        'end_time'   => isset($arguments['end']) ? \Carbon\Carbon::parse($arguments['end'])->toDateTimeString() : now()->addHour(),
                    ]);
                })
        ];
    }

    // 控制点击已有预约色块时的「编辑/删除」动作
    protected function modalActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
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
