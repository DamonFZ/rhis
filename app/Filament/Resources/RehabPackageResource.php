<?php

namespace App\Filament\Resources;

use App\Models\RehabPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RehabPackageResource extends Resource
{
    protected static ?string $model = RehabPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationLabel = '康复套餐';

    protected static ?string $modelLabel = '康复套餐';

    protected static ?string $pluralModelLabel = '康复套餐列表';

    protected static ?string $navigationGroup = '基础档案';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('package_code')
                    ->label('套餐编码')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('name')
                    ->label('套餐名称')
                    ->required()
                    ->maxLength(200),
                Forms\Components\Textarea::make('description')
                    ->label('套餐描述')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('price')
                    ->label('套餐价格')
                    ->numeric()
                    ->prefix('¥')
                    ->default(0),
                Forms\Components\TextInput::make('total_sessions')
                    ->label('总次数')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('validity_days')
                    ->label('有效期（天）')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('status')
                    ->label('状态')
                    ->default(true),
                Forms\Components\Select::make('package_type')
                    ->label('套餐类型')
                    ->options([
                        '单次' => '单次',
                        '疗程卡' => '疗程卡',
                        '月卡' => '月卡',
                        '季卡' => '季卡',
                        '特惠次卡' => '特惠次卡',
                        '单项服务' => '单项服务',
                    ])
                    ->default('单次')
                    ->required(),
                Forms\Components\TextInput::make('original_price')
                    ->label('原始价格')
                    ->numeric()
                    ->prefix('¥')
                    ->default(0),
                Forms\Components\TextInput::make('average_price')
                    ->label('均价')
                    ->numeric()
                    ->prefix('¥')
                    ->default(0),
                Forms\Components\Toggle::make('is_extendable')
                    ->label('是否可延期')
                    ->default(false),
                Forms\Components\TextInput::make('extension_days')
                    ->label('可延期天数')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('is_shareable')
                    ->label('是否可共享')
                    ->default(false),
                Forms\Components\TextInput::make('commission_per_service')
                    ->label('单次服务提成金额')
                    ->numeric()
                    ->prefix('¥')
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('package_code')
                    ->label('套餐编码')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('套餐名称')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('套餐价格')
                    ->money('CNY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_sessions')
                    ->label('总次数')
                    ->sortable(),
                Tables\Columns\TextColumn::make('validity_days')
                    ->label('有效期（天）')
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('状态')
                    ->boolean(),
                Tables\Columns\TextColumn::make('package_type')
                    ->label('套餐类型')
                    ->sortable(),
                Tables\Columns\TextColumn::make('original_price')
                    ->label('原始价格')
                    ->money('CNY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('average_price')
                    ->label('均价')
                    ->money('CNY')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_extendable')
                    ->label('可延期')
                    ->boolean(),
                Tables\Columns\TextColumn::make('extension_days')
                    ->label('延期天数')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_shareable')
                    ->label('可共享')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新时间')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label('状态'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\RehabPackageResource\Pages\ListRehabPackages::route('/'),
            'create' => \App\Filament\Resources\RehabPackageResource\Pages\CreateRehabPackage::route('/create'),
            'edit' => \App\Filament\Resources\RehabPackageResource\Pages\EditRehabPackage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderBy('package_code');
    }
}