<?php

namespace App\Filament\Resources\DepartmentResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $title = '部门员工';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('姓名')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('邮箱')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->label('密码')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->minLength(8)
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state)),
                Forms\Components\Toggle::make('pivot.is_primary')
                    ->label('是否主部门')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('姓名')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('邮箱')
                    ->searchable(),
                Tables\Columns\IconColumn::make('pivot.is_primary')
                    ->label('主部门')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('加入时间')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label('关联员工')
                    ->preloadRecordSelect()
                    ->recordSelectOptionsQuery(fn (Builder $query) => $query->whereDoesntHave('departments', fn ($q) => $q->where('department_id', $this->getOwnerRecord()->id)))
                    ->after(function (Tables\Actions\AttachAction $action, User $record): void {
                        $record->departments()->syncWithoutDetaching([$this->getOwnerRecord()->id => ['is_primary' => false]]);
                    }),
                Tables\Actions\CreateAction::make()
                    ->label('创建员工')
                    ->using(function (array $data, string $model): User {
                        $isPrimary = $data['pivot']['is_primary'] ?? false;
                        unset($data['pivot']);
                        
                        $user = $model::create($data);
                        $user->departments()->attach($this->getOwnerRecord()->id, ['is_primary' => $isPrimary]);
                        
                        return $user;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form(fn (Form $form): Form => $this->form($form)),
                Tables\Actions\DetachAction::make()
                    ->label('移除'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()->label('批量移除'),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
