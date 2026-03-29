<?php
namespace App\Filament\Resources;

use App\Filament\Resources\VendorResource\Pages;
use App\Models\District;
use App\Models\Province;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Vendors';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Business Information')
                ->schema([
                    Forms\Components\TextInput::make('brand_name')
                        ->required()->maxLength(200),
                    Forms\Components\TextInput::make('contact_person')
                        ->required()->maxLength(150),
                    Forms\Components\TextInput::make('email')
                        ->email()->required()->maxLength(191),
                    Forms\Components\TextInput::make('telephone')
                        ->required()->maxLength(20),
                    Forms\Components\TextInput::make('business_reg_number')
                        ->label('Business Registration No. (Optional)')
                        ->maxLength(100),
                ])->columns(2),

            Forms\Components\Section::make('Address')
                ->schema([
                    Forms\Components\TextInput::make('address_line1')
                        ->required()->maxLength(255)->columnSpanFull(),
                    Forms\Components\TextInput::make('address_line2')
                        ->maxLength(255)->columnSpanFull(),
                    Forms\Components\TextInput::make('city')
                        ->required()->maxLength(100),
                    Forms\Components\Select::make('province_id')
                        ->label('Province')
                        ->options(Province::pluck('name', 'id'))
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn($set) => $set('district_id', null)),
                    Forms\Components\Select::make('district_id')
                        ->label('District')
                        ->options(fn(Get $get) => District::where('province_id', $get('province_id'))
                            ->pluck('name', 'id'))
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('Bank Details')
                ->relationship('bankDetail')
                ->schema([
                    Forms\Components\TextInput::make('bank_name')->required()->maxLength(100),
                    Forms\Components\TextInput::make('bank_branch')->required()->maxLength(100),
                    Forms\Components\TextInput::make('account_number')->required()->maxLength(50),
                    Forms\Components\TextInput::make('account_holder_name')->required()->maxLength(150),
                ])->columns(2),

            Forms\Components\Section::make('Admin')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            'pending'   => 'Pending',
                            'approved'  => 'Approved',
                            'rejected'  => 'Rejected',
                            'suspended' => 'Suspended',
                        ])->required(),
                    Forms\Components\Toggle::make('is_government_approved')
                        ->label('Government Approved')
                        ->helperText('Only set after manual government verification'),
                    Forms\Components\Textarea::make('rejection_reason')
                        ->visible(fn(Get $get) => $get('status') === 'rejected')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('admin_notes')
                        ->label('Internal Admin Notes (not visible to vendor)')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('brand_name')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('contact_person')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('province.name')
                    ->label('Province')->sortable(),
                Tables\Columns\TextColumn::make('district.name')
                    ->label('District')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                        'gray'    => 'suspended',
                    ]),
                Tables\Columns\IconColumn::make('is_government_approved')
                    ->label('Govt. Approved')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered')->dateTime('d M Y')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'approved'  => 'Approved',
                        'rejected'  => 'Rejected',
                        'suspended' => 'Suspended',
                    ]),
                Tables\Filters\SelectFilter::make('province_id')
                    ->label('Province')
                    ->options(Province::pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_government_approved')
                    ->label('Government Approved'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(Vendor $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Vendor $record) {
                        $record->update([
                            'status'      => 'approved',
                            'approved_at' => now(),
                            'approved_by' => Auth::guard('admin')->id(),
                        ]);
                        Notification::make()->title('Vendor approved')->success()->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(Vendor $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->required()->label('Reason for rejection'),
                    ])
                    ->action(function (Vendor $record, array $data) {
                        $record->update([
                            'status'           => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                        Notification::make()->title('Vendor rejected')->danger()->send();
                    }),

                Tables\Actions\Action::make('suspend')
                    ->label('Suspend')
                    ->icon('heroicon-o-pause-circle')
                    ->color('warning')
                    ->visible(fn(Vendor $record) => $record->status === 'approved')
                    ->requiresConfirmation()
                    ->action(function (Vendor $record) {
                        $record->update(['status' => 'suspended']);
                        Notification::make()->title('Vendor suspended')->warning()->send();
                    }),

                Tables\Actions\Action::make('toggle_govt')
                    ->label(fn(Vendor $record) => $record->is_government_approved
                        ? 'Revoke Govt Approval'
                        : 'Mark Govt Approved')
                    ->icon('heroicon-o-shield-check')
                    ->color(fn(Vendor $record) => $record->is_government_approved ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->action(function (Vendor $record) {
                        $record->update([
                            'is_government_approved' => ! $record->is_government_approved,
                            'govt_approved_at'       => ! $record->is_government_approved ? now() : null,
                            'govt_approved_by'       => ! $record->is_government_approved
                                ? Auth::guard('admin')->id() : null,
                        ]);
                        Notification::make()->title('Government approval updated')->success()->send();
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelationManagers(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'view'   => Pages\ViewVendor::route('/{record}'),
            'edit'   => Pages\EditVendor::route('/{record}/edit'),
        ];
    }
}