<?php

namespace App\Filament\Resources\PatientProfileResource\RelationManagers;

use App\Models\PatientPackage;
use App\Models\ConsumptionRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ImagingRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'imagingRecords';
    protected static ?string $title = '康复记录';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基础信息')
                    ->schema([
                        Forms\Components\TextInput::make('record_no')
                            ->label('记录编号')
                            ->disabled()
                            ->default(fn () => 'IR' . date('YmdHis') . rand(100, 999)),
                        Forms\Components\Select::make('record_type')
                            ->label('记录类型')
                            ->options([
                                1 => '康复前',
                                2 => '康复后',
                                3 => '康复中',
                            ])
                            ->default(1)
                            ->required(),
                        Forms\Components\DatePicker::make('treatment_date')
                            ->label('康复日期')
                            ->default(now())
                            ->required(),
                    ])
                    ->columns(3),
                Forms\Components\Section::make('扣减套餐（可选）')
                    ->description('如果此次康复需要扣减套餐次数，请选择套餐并填写扣减次数')
                    ->schema([
                        Forms\Components\Select::make('patient_package_id')
                            ->label('选择套餐包')
                            ->options(function () {
                                $patientProfile = $this->getOwnerRecord();
                                return PatientPackage::where('patient_profile_id', $patientProfile->id)
                                    ->where('status', 'active')
                                    ->pluck('package_name', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if (!$state) {
                                    $set('deducted_sessions', null);
                                    $set('treatment_content', null);
                                    $set('package_name', null);
                                } else {
                                    $package = PatientPackage::find($state);
                                    if ($package) {
                                        $set('package_name', $package->package_name);
                                    }
                                }
                            }),
                        Forms\Components\TextInput::make('deducted_sessions')
                            ->label('扣减次数')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->nullable()
                            ->reactive()
                            ->hidden(fn (callable $get) => empty($get('patient_package_id')))
                            ->afterStateUpdated(function (callable $set, $state, $get) {
                                $packageId = $get('patient_package_id');
                                if ($packageId && $state) {
                                    $package = PatientPackage::find($packageId);
                                    if ($package && $package->remaining_sessions < $state) {
                                        $set('warning_message', '套餐剩余次数不足，无法扣减');
                                    } else {
                                        $set('warning_message', '');
                                    }
                                }
                            }),
                        Forms\Components\Textarea::make('treatment_content')
                            ->label('康复内容')
                            ->nullable()
                            ->hidden(fn (callable $get) => empty($get('patient_package_id'))),
                        Forms\Components\Hidden::make('package_name'),
                        Forms\Components\Placeholder::make('warning_message')
                            ->label('')
                            ->columnSpanFull()
                            ->hidden(fn (callable $get) => empty($get('warning_message')))
                            ->content(fn (callable $get) => '<span class="text-red-500">' . $get('warning_message') . '</span>'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('标准体态照片 SOP')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\FileUpload::make('photo_urls.front')
                                    ->label('正面站立')
                                    ->image()
                                    ->directory('imaging')
                                    ->required(),
                                Forms\Components\FileUpload::make('photo_urls.back')
                                    ->label('背面站立')
                                    ->image()
                                    ->directory('imaging')
                                    ->required(),
                                Forms\Components\FileUpload::make('photo_urls.left_side')
                                    ->label('左侧面')
                                    ->image()
                                    ->directory('imaging')
                                    ->required(),
                                Forms\Components\FileUpload::make('photo_urls.right_side')
                                    ->label('右侧面')
                                    ->image()
                                    ->directory('imaging')
                                    ->required(),
                                Forms\Components\FileUpload::make('photo_urls.forward_bending')
                                    ->label('正面前屈弯腰')
                                    ->image()
                                    ->directory('imaging')
                                    ->required(),
                                Forms\Components\FileUpload::make('photo_urls.back_sitting')
                                    ->label('背面坐姿')
                                    ->image()
                                    ->directory('imaging')
                                    ->required(),
                            ]),
                    ]),
                
                Forms\Components\Section::make('步态/动态视频')
                    ->schema([
                        Forms\Components\FileUpload::make('video_url')
                            ->label('动态视频')
                            ->acceptedFileTypes(['video/mp4', 'video/quicktime'])
                            ->directory('imaging_videos'),
                    ]),
                
                Forms\Components\Section::make('备注')
                    ->schema([
                        Forms\Components\Textarea::make('remark')
                            ->label('备注')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('record_no')
            ->columns([
                Tables\Columns\TextColumn::make('record_no')
                    ->label('记录编号')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('record_type')
                    ->label('类型')
                    ->formatStateUsing(fn (int $state) => match ($state) {
                        1 => '康复前',
                        2 => '康复后',
                        3 => '康复中',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (int $state) => match ($state) {
                        1 => 'warning',
                        2 => 'success',
                        3 => 'info',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('treatment_date')
                    ->label('康复日期')
                    ->date('Y-m-d')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('record_type')
                    ->label('记录类型')
                    ->options([
                        1 => '康复前',
                        2 => '康复后',
                        3 => '康复中',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function ($record, array $data) {
                        // 如果填写了套餐和扣减次数，创建消费记录
                        if (isset($data['patient_package_id']) && isset($data['deducted_sessions'])) {
                            $packageId = $data['patient_package_id'];
                            $deductedSessions = $data['deducted_sessions'];
                            
                            if ($packageId && $deductedSessions > 0) {
                                $package = PatientPackage::find($packageId);
                                if ($package && $package->isActive()) {
                                    ConsumptionRecord::create([
                                        'patient_profile_id' => $record->patient_profile_id,
                                        'patient_package_id' => $packageId,
                                        'package_name' => $data['package_name'] ?? $package->package_name,
                                        'deducted_sessions' => $deductedSessions,
                                        'treatment_date' => $record->treatment_date,
                                        'treatment_content' => $data['treatment_content'] ?? '',
                                    ]);
                                }
                            }
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('treatment_date', 'desc');
    }
}
