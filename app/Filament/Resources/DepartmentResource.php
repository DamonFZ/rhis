<?php

namespace App\Filament\Resources;

use App\Models\Department;
use App\Filament\Resources\DepartmentResource\RelationManagers\UsersRelationManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = '部门管理';

    protected static ?string $modelLabel = '部门';

    protected static ?string $pluralModelLabel = '部门列表';

    protected static ?string $navigationGroup = '组织架构';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('parent_id')
                    ->label('父部门')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload()
                    ->default(0)
                    ->modifyRecordSelectOptionsQuery(function (Builder $query) {
                        if ($this->getRecord()) {
                            $query->where('id', '!=', $this->getRecord()->id);
                        }
                    })
                    ->placeholder('顶级部门'),
                Forms\Components\TextInput::make('name')
                    ->label('部门名称')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('code')
                    ->label('部门编码')
                    ->maxLength(50)
                    ->nullable(),
                Forms\Components\TextInput::make('level')
                    ->label('层级')
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('sort')
                    ->label('排序')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('status')
                    ->label('状态')
                    ->default(true),
                Forms\Components\Textarea::make('description')
                    ->label('描述')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('父部门')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('部门名称')
                    ->searchable(),
                Tables\Columns\TextColumn::make('code')
                    ->label('部门编码')
                    ->searchable(),
                Tables\Columns\TextColumn::make('level')
                    ->label('层级')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort')
                    ->label('排序')
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('parent')
                    ->relationship('parent', 'name')
                    ->label('父部门'),
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
            UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\DepartmentResource\Pages\ListDepartments::route('/'),
            'create' => \App\Filament\Resources\DepartmentResource\Pages\CreateDepartment::route('/create'),
            'edit' => \App\Filament\Resources\DepartmentResource\Pages\EditDepartment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderBy('sort');
    }
}