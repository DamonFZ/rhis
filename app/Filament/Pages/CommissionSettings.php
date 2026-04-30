<?php

namespace App\Filament\Pages;

use App\Models\CommissionSetting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class CommissionSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = '设置';
    protected static ?string $title = '提成设置';
    protected static string $view = 'filament.pages.commission-settings';

    public ?array $data = [];

    public function mount(): void
    {
        // 初始化或获取配置（确保数据库有且只有 ID 为 1 的数据）
        $setting = CommissionSetting::firstOrCreate(['id' => 1], [
            'service_commission' => 15.00,
            'sales_type_1_rate' => 3.00,
            'sales_type_2_rate' => 1.00,
            'sales_type_3_rate' => 2.00,
        ]);
        $this->form->fill($setting->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('服务提成设置 (耗卡单次提成)')->schema([
                    TextInput::make('service_commission')
                        ->label('单次服务提成 (元)')
                        ->numeric()
                        ->required(),
                ]),
                Section::make('销售提成设置 (购买套餐提成)')->schema([
                    TextInput::make('sales_type_1_rate')
                        ->label('自主开发提成比例 (%)')
                        ->numeric()->step('0.1')->required(),
                    TextInput::make('sales_type_2_rate')
                        ->label('康复续卡提成比例 (%)')
                        ->numeric()->step('0.1')->required(),
                    TextInput::make('sales_type_3_rate')
                        ->label('协助开单提成比例 (%)')
                        ->numeric()->step('0.1')->required(),
                ])->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        CommissionSetting::first()->update($this->form->getState());
        Notification::make()->title('提成设置已保存')->success()->send();
    }
}
