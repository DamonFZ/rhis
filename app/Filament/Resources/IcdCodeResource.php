<?php

namespace App\Filament\Resources;

use App\Models\IcdCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IcdCodeResource extends Resource
{
    protected static ?string $model = IcdCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'ICD编码';

    protected static ?string $modelLabel = 'ICD编码';

    protected static ?string $pluralModelLabel = 'ICD编码列表';

    protected static ?string $navigationGroup = '基础档案';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('ICD编码')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('name')
                    ->label('疾病名称')
                    ->required()
                    ->maxLength(200),
                Forms\Components\TextInput::make('category')
                    ->label('分类')
                    ->maxLength(100),
                Forms\Components\Textarea::make('description')
                    ->label('描述')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('status')
                    ->label('状态')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('ICD编码')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('疾病名称')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('分类')
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('状态')
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
                Tables\Filters\SelectFilter::make('category')
                    ->label('分类')
                    ->options(fn () => IcdCode::distinct()->pluck('category', 'category')),
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
            'index' => \App\Filament\Resources\IcdCodeResource\Pages\ListIcdCodes::route('/'),
            'create' => \App\Filament\Resources\IcdCodeResource\Pages\CreateIcdCode::route('/create'),
            'edit' => \App\Filament\Resources\IcdCodeResource\Pages\EditIcdCode::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderBy('code');
    }
}