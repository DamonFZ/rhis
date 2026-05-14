<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientProfileResource\Pages;
use App\Filament\Resources\PatientProfileResource\RelationManagers\AssessmentsRelationManager;
use App\Filament\Resources\PatientProfileResource\RelationManagers\ConsumptionRecordsRelationManager;
use App\Filament\Resources\PatientProfileResource\RelationManagers\ImagingRecordsRelationManager;
use App\Filament\Resources\PatientProfileResource\RelationManagers\PatientPackagesRelationManager;
use App\Models\CommissionSetting;
use App\Models\PatientPackage;
use App\Models\PatientProfile;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\DB;
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
                Tables\Columns\TextColumn::make('latestConsumptionRecord.treatment_date')
                    ->label('最近康复日期')
                    ->formatStateUsing(fn ($state) => $state ? $state->format('Y-m-d') : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('join_date')
                    ->label('建档日期')
                    ->date('Y-m-d')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('quick_deduct_trigger')
                    ->label('快速划扣')
                    ->state(fn () => '⚡ 快速划扣')
                    ->color('warning')
                    ->weight('bold')
                    ->extraAttributes(['class' => 'cursor-pointer hover:underline'])
                    ->action(
                        Action::make('quick_deduct_modal')
                            ->modalHeading('快速划扣')
                            ->modalWidth('md')
                            ->form([
                                Forms\Components\Select::make('patient_package_id')
                                    ->label('选择有效套餐')
                                    ->options(fn (PatientProfile $record) => $record->patientPackages()->where('status', 'active')->where('remaining_sessions', '>', 0)->get()->mapWithKeys(fn ($p) => [$p->id => "{$p->package_name} (剩:{$p->remaining_sessions}次)"]))
                                    ->required()
                                    ->reactive(),
                                Forms\Components\TextInput::make('deducted_sessions')
                                    ->label('扣减次数')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->rule(fn ($get) => function ($attr, $val, $fail) use ($get) {
                                        $p = PatientPackage::find($get('patient_package_id'));
                                        if ($p && $val > $p->remaining_sessions) $fail("最多只能扣 {$p->remaining_sessions} 次");
                                    }),
                                Forms\Components\Select::make('therapist_ids')
                                    ->label('服务康复师')
                                    ->options(User::pluck('name', 'id'))
                                    ->multiple()
                                    ->required(),
                                Forms\Components\DatePicker::make('treatment_date')
                                    ->label('日期')
                                    ->default(now())
                                    ->required(),
                                Forms\Components\Textarea::make('treatment_content')
                                    ->label('康复内容')
                                    ->rows(2),
                            ])
                            ->action(function (array $data, PatientProfile $record) {
                                DB::transaction(function () use ($data, $record) {
                                    // 检查套餐次数是否足够
                                    $p = PatientPackage::lockForUpdate()->findOrFail($data['patient_package_id']);
                                    if ($p->remaining_sessions < $data['deducted_sessions']) {
                                        throw new \Exception('次数不足');
                                    }

                                    // 创建消费记录，模型中的 creating 钩子会自动扣减套餐剩余次数
                                    $c = \App\Models\ConsumptionRecord::create([
                                        'patient_profile_id' => $record->id,
                                        'patient_package_id' => $p->id,
                                        'deducted_sessions' => $data['deducted_sessions'],
                                        'treatment_date' => $data['treatment_date'],
                                        'treatment_content' => $data['treatment_content'],
                                    ]);

                                    // 计算员工提成
                                    $amt = CommissionSetting::first()->service_commission ?? 15.00;
                                    $c->users()->sync(array_fill_keys($data['therapist_ids'], ['commission_amount' => $amt]));
                                });
                                Notification::make()->title('划扣成功')->success()->send();
                            })
                    ),
                \Filament\Tables\Columns\TextColumn::make('buy_package_trigger' )
                    ->label('购买套餐' )
                    ->state(fn ( ) => '🛒 购买套餐')
                    ->color('success' )
                    ->weight('bold' )
                    ->extraAttributes(['class' => 'cursor-pointer hover:underline'] )
                    ->action(
                        \Filament\Tables\Actions\Action::make('buy_package_modal' )
                            ->modalHeading('为客户办理新套餐' )
                            ->modalWidth('md' )
                            ->form( [
                                \Filament\Forms\Components\Select::make('rehab_package_id' )
                                    ->label('选择康复套餐' )
                                    ->options(\App\Models\RehabPackage::where('status', 1)->pluck('name', 'id') )
                                    ->required( )
                                    ->searchable( ),
                                \Filament\Forms\Components\Select::make('salesperson_id' )
                                    ->label('开单员工（销售）' )
                                    ->options(\App\Models\User::pluck('name', 'id') )
                                    ->required( )
                                    ->searchable( ),
                                \Filament\Forms\Components\Select::make('sales_type' )
                                    ->label('提成类型' )
                                    ->options(function ( ) {
                                        $setting = \App\Models\CommissionSetting::first( );
                                        return  [
                                            1 => '自主开发 (' . ($setting->sales_type_1_rate ?? 3) . '%)' ,
                                            2 => '康复续卡 (' . ($setting->sales_type_2_rate ?? 1) . '%)' ,
                                            3 => '协助开单 (' . ($setting->sales_type_3_rate ?? 2) . '%)' ,
                                        ];
                                    } )
                                    ->required( ),
                            ] )
                            ->action(function (array $data, \App\Models\PatientProfile $record ) {
                                \Illuminate\Support\Facades\DB::transaction(function () use ($data, $record ) {
                                    $rehabPkg = \App\Models\RehabPackage::findOrFail($data['rehab_package_id'] );
                                    $setting = \App\Models\CommissionSetting::first( );
                                    $rates  = [
                                        1 => ($setting->sales_type_1_rate ?? 3) / 100 ,
                                        2 => ($setting->sales_type_2_rate ?? 1) / 100 ,
                                        3 => ($setting->sales_type_3_rate ?? 2) / 100 ,
                                    ];

                                    \App\Models\PatientPackage::create( [
                                        'patient_profile_id' => $record ->id,
                                        'package_code'       => $rehabPkg ->package_code,
                                        'package_name'       => $rehabPkg ->name,
                                        'package_type'       => $rehabPkg ->package_type,
                                        'total_sessions'     => $rehabPkg ->total_sessions,
                                        'remaining_sessions' => $rehabPkg ->total_sessions,
                                        'price'              => $rehabPkg ->price,
                                        'original_price'     => $rehabPkg ->original_price,
                                        'average_price'      => $rehabPkg ->average_price,
                                        'status'             => 'active' ,
                                        'is_extendable'      => $rehabPkg ->is_extendable,
                                        'extension_days'     => $rehabPkg ->extension_days,
                                        'is_shareable'       => $rehabPkg ->is_shareable,
                                        'purchase_date'      => now( ),
                                        'expiry_date'        => now()->addDays($rehabPkg->validity_days + $rehabPkg->extension_days ),
                                        'salesperson_id'     => $data['salesperson_id' ],
                                        'sales_type'         => $data['sales_type' ],
                                        'sales_commission'   => $rehabPkg->price * ($rates[$data['sales_type']] ?? 0.03 ),
                                    ] );
                                } );
                                \Filament\Notifications\Notification::make()->title('套餐购买成功')->success()->send( );
                            } )
                    ),
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
