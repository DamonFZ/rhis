<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Form;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class AppointmentCalendarWidget extends FullCalendarWidget
{
    protected static ?string $heading = '预约看板';

    public ?string $model = Appointment::class;

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
            // 拖动时显示投影色块
            'selectMirror' => true,
            // 核心：允许拖拽已有事件改变时间
            'editable' => true,
            // 允许跨天选择
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
            \Filament\Actions\CreateAction::make()
                ->label('新增预约')
                ->model(Appointment::class)
                ->form($this->getFormSchema())
                ->mountUsing(function (Form $form, array $arguments) {
                    $form->fill([
                        'start_time' => $arguments['start'] ?? now()->toDateTimeString(),
                        'end_time'   => $arguments['end'] ?? now()->addHour()->toDateTimeString(),
                        'status'     => 1,
                    ]);
                })
        ];
    }

    protected function modalActions(): array
    {
        return [
            \Filament\Actions\EditAction::make()
                ->label('编辑')
                ->model(Appointment::class)
                ->form($this->getFormSchema()),
            \Filament\Actions\DeleteAction::make()
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

                return [
                    'id'    => $appointment->id,
                    'title' => ($appointment->patientProfile?->name ?? '未知客户') . $remarkText,
                    'start' => $appointment->start_time->toDateTimeString(),
                    'end'   => $appointment->end_time->toDateTimeString(),
                    'color' => $appointment->status === 1 ? '#3b82f6' : '#22c55e',
                ];
            })
            ->toArray();
    }
}
