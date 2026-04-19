<?php

namespace App\Filament\Resources\PatientProfileResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ImagingRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'imagingRecords';

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
                                1 => '治疗前',
                                2 => '治疗后',
                            ])
                            ->default(1)
                            ->required(),
                        Forms\Components\DatePicker::make('treatment_date')
                            ->label('治疗日期')
                            ->default(now())
                            ->required(),
                    ])
                    ->columns(3),
                
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
                        1 => '治疗前',
                        2 => '治疗后',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (int $state) => match ($state) {
                        1 => 'warning',
                        2 => 'success',
                        default => 'primary',
                    }),
                Tables\Columns\TextColumn::make('treatment_date')
                    ->label('治疗日期')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('record_type')
                    ->label('记录类型')
                    ->options([
                        1 => '治疗前',
                        2 => '治疗后',
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
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('treatment_date', 'desc');
    }
}
