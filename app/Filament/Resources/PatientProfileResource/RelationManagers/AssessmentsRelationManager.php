<?php

namespace App\Filament\Resources\PatientProfileResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Redirect;

class AssessmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'physicalAssessments';
    protected static ?string $title = '体态评估';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基础信息')
                    ->schema([
                        Forms\Components\TextInput::make('assessment_no')
                            ->label('评估编号')
                            ->disabled()
                            ->default(fn () => 'PA' . date('YmdHis') . rand(100, 999)),
                        Forms\Components\DatePicker::make('assessment_date')
                            ->label('评估日期')
                            ->default(now())
                            ->required(),
                        Forms\Components\Select::make('assessment_type')
                            ->label('评估类型')
                            ->options([
                                1 => '初评',
                                2 => '复评',
                                3 => '末评',
                            ])
                            ->default(1)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('状态')
                            ->options([
                                0 => '草稿',
                                1 => '已完成',
                            ])
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Fieldset::make('基础体测')
                    ->schema([
                        Forms\Components\TextInput::make('height')
                            ->label('身高')
                            ->numeric()
                            ->suffix('cm'),
                        Forms\Components\TextInput::make('weight')
                            ->label('体重')
                            ->numeric()
                            ->suffix('kg'),
                        Forms\Components\TextInput::make('bmi')
                            ->label('BMI')
                            ->numeric(),
                        Forms\Components\TextInput::make('body_fat_rate')
                            ->label('体脂率')
                            ->numeric()
                            ->suffix('%'),
                    ])
                    ->columns(2),

                Forms\Components\Fieldset::make('详细围度')
                    ->schema([
                        Forms\Components\TextInput::make('circumference.chest')
                            ->label('胸围')
                            ->numeric()
                            ->suffix('cm'),
                        Forms\Components\TextInput::make('circumference.waist')
                            ->label('腰围')
                            ->numeric()
                            ->suffix('cm'),
                        Forms\Components\TextInput::make('circumference.hip')
                            ->label('臀围')
                            ->numeric()
                            ->suffix('cm'),
                        Forms\Components\TextInput::make('circumference.left_arm')
                            ->label('左臂围')
                            ->numeric()
                            ->suffix('cm'),
                        Forms\Components\TextInput::make('circumference.right_arm')
                            ->label('右臂围')
                            ->numeric()
                            ->suffix('cm'),
                        Forms\Components\TextInput::make('circumference.left_thigh')
                            ->label('左大腿围')
                            ->numeric()
                            ->suffix('cm'),
                        Forms\Components\TextInput::make('circumference.right_thigh')
                            ->label('右大腿围')
                            ->numeric()
                            ->suffix('cm'),
                    ])
                    ->columns(3),

                Forms\Components\Fieldset::make('柔软度评估')
                    ->schema([
                        Forms\Components\Select::make('flexibility.trunk')
                            ->label('躯干')
                            ->options(['好' => '好', '一般' => '一般', '差' => '差'])
                            ->nullable(),
                        Forms\Components\Select::make('flexibility.hamstrings')
                            ->label('腘绳肌')
                            ->options(['好' => '好', '一般' => '一般', '差' => '差'])
                            ->nullable(),
                        Forms\Components\Select::make('flexibility.iliopsoas')
                            ->label('髂腰肌群')
                            ->options(['好' => '好', '一般' => '一般', '差' => '差'])
                            ->nullable(),
                        Forms\Components\Select::make('flexibility.quadriceps')
                            ->label('股四头肌')
                            ->options(['好' => '好', '一般' => '一般', '差' => '差'])
                            ->nullable(),
                        Forms\Components\Select::make('flexibility.calf')
                            ->label('小腿肌群')
                            ->options(['好' => '好', '一般' => '一般', '差' => '差'])
                            ->nullable(),
                        Forms\Components\Select::make('flexibility.shoulder_1')
                            ->label('肩部1')
                            ->options(['好' => '好', '一般' => '一般', '差' => '差'])
                            ->nullable(),
                        Forms\Components\Select::make('flexibility.shoulder_2')
                            ->label('肩部2')
                            ->options(['好' => '好', '一般' => '一般', '差' => '差'])
                            ->nullable(),
                    ])
                    ->columns(2),

                Forms\Components\Fieldset::make('体态评估-侧面')
                    ->schema([
                        Forms\Components\Select::make('posture_tags.side_head')
                            ->label('头部')
                            ->options(['中立位' => '中立位', '前引' => '前引', '后仰' => '后仰'])
                            ->multiple()
                            ->nullable(),
                        Forms\Components\Select::make('posture_tags.side_cervical')
                            ->label('颈椎')
                            ->options(['中心轴' => '中心轴', '过于前曲' => '过于前曲', '强直' => '强直'])
                            ->multiple()
                            ->nullable(),
                        Forms\Components\Select::make('posture_tags.side_scapula')
                            ->label('肩胛骨')
                            ->options(['中立位' => '中立位', '内旋' => '内旋'])
                            ->multiple()
                            ->nullable(),
                        Forms\Components\Select::make('posture_tags.side_thoracic')
                            ->label('胸椎')
                            ->options(['中心轴' => '中心轴', '过于后凸' => '过于后凸', '强直' => '强直'])
                            ->multiple()
                            ->nullable(),
                        Forms\Components\Select::make('posture_tags.side_lumbar')
                            ->label('腰椎')
                            ->options(['中心轴' => '中心轴', '过于前曲' => '过于前曲', '强直' => '强直'])
                            ->multiple()
                            ->nullable(),
                        Forms\Components\Select::make('posture_tags.side_pelvis')
                            ->label('骨盆')
                            ->options(['中立位' => '中立位', '前倾' => '前倾', '后倾' => '后倾'])
                            ->multiple()
                            ->nullable(),
                        Forms\Components\Select::make('posture_tags.side_knee')
                            ->label('膝关节')
                            ->options(['中立位' => '中立位', '超伸' => '超伸'])
                            ->multiple()
                            ->nullable(),
                    ])
                    ->columns(2),

                Forms\Components\Fieldset::make('体态评估-背面')
                    ->schema([
                        Forms\Components\Select::make('posture_tags.back_cervical')
                            ->label('颈椎')
                            ->options(['中立位' => '中立位', '前倾' => '前倾', '旋转' => '旋转'])
                            ->multiple()
                            ->nullable(),
                        Forms\Components\Select::make('posture_tags.back_shoulder')
                            ->label('肩部')
                            ->options(['中立位' => '中立位', '右高' => '右高', '左高' => '左高'])
                            ->multiple()
                            ->nullable(),
                        Forms\Components\Select::make('posture_tags.back_scapula')
                            ->label('肩胛骨')
                            ->options(['中立位' => '中立位', '前引' => '前引', '后缩' => '后缩'])
                            ->multiple()
                            ->nullable(),
                        Forms\Components\Select::make('posture_tags.back_thoracolumbar')
                            ->label('胸腰椎')
                            ->options(['中立位' => '中立位', 'S形' => 'S形', 'C形' => 'C形'])
                            ->multiple()
                            ->nullable(),
                        Forms\Components\Select::make('posture_tags.back_pelvis')
                            ->label('骨盆')
                            ->options(['中立位' => '中立位', '右侧倾' => '右侧倾', '左侧倾' => '左侧倾'])
                            ->multiple()
                            ->nullable(),
                        Forms\Components\Select::make('posture_tags.back_knee')
                            ->label('膝关节')
                            ->options(['中立位' => '中立位', '膝外翻' => '膝外翻', '膝内翻' => '膝内翻'])
                            ->multiple()
                            ->nullable(),
                        Forms\Components\Select::make('posture_tags.back_foot')
                            ->label('足弓')
                            ->options(['有足弓' => '有足弓', '扁平足' => '扁平足', '高足弓' => '高足弓'])
                            ->multiple()
                            ->nullable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('其他')
                    ->schema([
                        Forms\Components\Textarea::make('remark')
                            ->label('备注')
                            ->columnSpanFull(),
                    ]),
                    
                Forms\Components\Section::make('体态异常标记 (实景图谱)')
                    ->schema([
                        Forms\Components\ViewField::make('body_canvas_path')
                            ->view('filament.forms.components.body-canvas')
                            ->columnSpanFull()
                            ->dehydrateStateUsing(function ($state) {
                                if (blank($state)) return null;
                                if (str_starts_with($state, 'data:image')) {
                                    @list($type, $file_data) = explode(';', $state);
                                    @list(, $file_data) = explode(',', $file_data);
                                    $imageName = 'assessments/canvas_' . Str::random(10) . '_' . time() . '.jpg';
                                    Storage::disk('public')->put($imageName, base64_decode($file_data));
                                    return $imageName;
                                }
                                return $state;
                            })
                            ->formatStateUsing(function ($state) {
                                if (blank($state)) return null;
                                if (str_starts_with($state, 'data:image')) return $state;
                                return Storage::disk('public')->url($state);
                            }),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('assessment_no')
            ->columns([
                Tables\Columns\TextColumn::make('assessment_no')
                    ->label('评估编号')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('assessment_date')
                    ->label('评估日期')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('assessment_type')
                    ->label('类型')
                    ->formatStateUsing(fn (int $state) => match ($state) {
                        1 => '初评',
                        2 => '复评',
                        3 => '末评',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('状态')
                    ->formatStateUsing(fn (int $state) => match ($state) {
                        0 => '草稿',
                        1 => '已完成',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (int $state) => match ($state) {
                        0 => 'warning',
                        1 => 'success',
                        default => 'primary',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('assessment_type')
                    ->label('评估类型')
                    ->options([
                        1 => '初评',
                        2 => '复评',
                        3 => '末评',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('状态')
                    ->options([
                        0 => '草稿',
                        1 => '已完成',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('compare')
                        ->label('对比成效')
                        ->icon('heroicon-m-chart-bar')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->modalHeading('对比评估记录')
                        ->modalDescription('请确保已选中正好 2 条评估记录进行对比。')
                        ->action(function ($records) {
                            $records = collect($records);
                            if ($records->count() !== 2) {
                                Notification::make()
                                    ->title('请选择正好 2 条记录进行对比')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            $ids = $records->pluck('id')->sort()->values()->toArray();
                            return redirect()->route('filament.admin.pages.compare-assessments', [
                                'base_id' => $ids[0],
                                'target_id' => $ids[1],
                            ]);
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('assessment_date', 'desc');
    }
}
