<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignResource\Pages;
use App\Models\Campaign;
use App\Models\RehabPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = '营销活动';

    protected static ?string $modelLabel = '营销活动';

    protected static ?string $pluralModelLabel = '营销活动';

    protected static ?string $navigationGroup = '营销管理';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本信息')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('活动名称')
                            ->required()
                            ->maxLength(200),
                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('开始时间')
                            ->required()
                            ->native(false),
                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('结束时间')
                            ->required()
                            ->native(false)
                            ->after('start_date'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('启用状态')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('活动规则（参与套餐与赠送次数）')
                    ->schema([
                        Forms\Components\Repeater::make('package_rules')
                            ->label('套餐赠送规则')
                            ->schema([
                                Forms\Components\Select::make('rehab_package_id')
                                    ->label('康复套餐')
                                    ->options(fn () => RehabPackage::where('status', 1)->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),
                                Forms\Components\TextInput::make('extra_count')
                                    ->label('赠送次数')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required()
                                    ->default(0),
                            ])
                            ->columns(2)
                            ->columnSpanFull()
                            ->addActionLabel('添加套餐规则')
                            ->defaultItems(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('活动名称')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('开始时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('结束时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('状态')
                    ->badge()
                    ->getStateUsing(function (Campaign $record): string {
                        if (! $record->is_active) {
                            return '已停用';
                        }

                        $now = Carbon::now();
                        if ($now < $record->start_date) {
                            return '未开始';
                        }
                        if ($now > $record->end_date) {
                            return '已结束';
                        }

                        return '进行中';
                    })
                    ->color(function (Campaign $record): string {
                        if (! $record->is_active) {
                            return 'gray';
                        }

                        $now = Carbon::now();
                        if ($now < $record->start_date) {
                            return 'info';
                        }
                        if ($now > $record->end_date) {
                            return 'danger';
                        }

                        return 'success';
                    }),
                Tables\Columns\TextColumn::make('rehabPackages_count')
                    ->label('参与套餐数')
                    ->counts('rehabPackages')
                    ->badge(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('启停'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('启用状态'),
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
            ->defaultSort('start_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('rehabPackages');
    }
}
