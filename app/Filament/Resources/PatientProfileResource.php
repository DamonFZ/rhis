<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientProfileResource\Pages;
use App\Filament\Resources\PatientProfileResource\RelationManagers\AssessmentsRelationManager;
use App\Filament\Resources\PatientProfileResource\RelationManagers\ConsumptionRecordsRelationManager;
use App\Filament\Resources\PatientProfileResource\RelationManagers\ImagingRecordsRelationManager;
use App\Filament\Resources\PatientProfileResource\RelationManagers\PatientPackagesRelationManager;
use App\Models\PatientProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;

class PatientProfileResource extends Resource
{
    protected static ?string $model = PatientProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = '客户档案';

    protected static ?string $modelLabel = '客户档案';

    protected static ?string $pluralModelLabel = '客户档案列表';

    protected static ?string $navigationGroup = '数字化档案';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('patient_id')
                    ->label('客户编号')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('name')
                    ->label('姓名')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('phone')
                    ->label('联系电话')
                    ->tel()
                    ->maxLength(20),
                Forms\Components\DatePicker::make('join_date')
                    ->label('建档日期')
                    ->default(now()),
                Forms\Components\Textarea::make('initial_symptoms')
                    ->label('初始症状')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient_id')
                    ->label('客户编号')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('姓名')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('联系电话')
                    ->searchable(),
                Tables\Columns\TextColumn::make('latestPackage.package_name')
                    ->label('套餐名称')
                    ->default('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('latestPackage.remaining_sessions')
                    ->label('剩余次数')
                    ->default('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('latestImagingRecord.treatment_date')
                    ->label('最近康复日期')
                    ->formatStateUsing(fn ($state) => $state ? $state->format('Y-m-d') : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('join_date')
                    ->label('建档日期')
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('创建时间')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('bindWechat')
                    ->label('微信绑定')
                    ->icon('heroicon-o-qr-code')
                    ->color('success')
                    ->hidden(fn ($record) => filled($record->wechat_openid))
                    ->modalHeading('绑定微信档案')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalContent(function ($record) {
                        if (blank($record->bind_token)) {
                            $record->update(['bind_token' => Str::random(32)]);
                        }
                        $url = route('mobile.bind', [
                            'patient_id' => $record->id,
                            'token' => $record->bind_token,
                        ]);

                        $qrCode = QrCode::size(250)->margin(1)->generate($url);

                        return view('filament.components.qrcode-modal', [
                            'qrCode' => $qrCode,
                            'patientName' => $record->name,
                        ]);
                    })
                    ->extraModalActions([
                        Action::make('resetToken')
                            ->label('重新生成二维码')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->action(function ($record) {
                                $record->update(['bind_token' => Str::random(32)]);
                                Notification::make()->success()->title('已重新生成并作废旧码')->send();
                            }),
                    ]),
                Action::make('unbindWechat')
                    ->label('解绑')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => filled($record->wechat_openid))
                    ->requiresConfirmation()
                    ->modalHeading('确认解绑微信')
                    ->modalDescription('确定要解绑该客户的微信吗？解绑后需要重新生成二维码让客户扫码绑定。')
                    ->action(function ($record) {
                        $record->update([
                            'wechat_openid' => null,
                            'bind_token' => null
                        ]);
                        Notification::make()->success()->title('已成功解绑微信')->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // 暂时屏蔽批量删除功能，防止员工误操作
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->defaultSort('join_date', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Tabs::make('客户档案详情')
                    ->tabs([
                        Infolists\Components\Tabs\Tab::make('基础信息')
                            ->schema([
                                Infolists\Components\TextEntry::make('patient_id')
                                    ->label('客户编号'),
                                Infolists\Components\TextEntry::make('name')
                                    ->label('姓名'),
                                Infolists\Components\TextEntry::make('phone')
                                    ->label('联系电话'),
                                Infolists\Components\TextEntry::make('join_date')
                                    ->label('建档日期')
                                    ->date('Y-m-d'),
                                Infolists\Components\TextEntry::make('initial_symptoms')
                                    ->label('初始症状')
                                    ->columnSpanFull(),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->label('创建时间')
                                    ->dateTime('Y-m-d H:i:s'),
                                Infolists\Components\TextEntry::make('updated_at')
                                    ->label('更新时间')
                                    ->dateTime('Y-m-d H:i:s'),
                            ])->columns(2),
                        Infolists\Components\Tabs\Tab::make('医疗与影像记录')
                            ->schema([
                                Infolists\Components\TextEntry::make('')
                                    ->label('')
                                    ->hiddenLabel()
                                    ->default('请在下方的关联管理器中管理影像记录'),
                            ]),
                        Infolists\Components\Tabs\Tab::make('康复体态评估')
                            ->schema([
                                Infolists\Components\TextEntry::make('')
                                    ->label('')
                                    ->hiddenLabel()
                                    ->default('请在下方的关联管理器中管理评估记录'),
                            ]),
                        Infolists\Components\Tabs\Tab::make('财务与资产')
                            ->schema([
                                Infolists\Components\TextEntry::make('')
                                    ->label('')
                                    ->hiddenLabel()
                                    ->default('请在下方的关联管理器中管理套餐包和消费记录'),
                            ]),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ImagingRecordsRelationManager::class,
            AssessmentsRelationManager::class,
            PatientPackagesRelationManager::class,
            ConsumptionRecordsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatientProfiles::route('/'),
            'create' => Pages\CreatePatientProfile::route('/create'),
            'view' => Pages\ViewPatientProfile::route('/{record}'),
            'edit' => Pages\EditPatientProfile::route('/{record}/edit'),
        ];
    }
}
