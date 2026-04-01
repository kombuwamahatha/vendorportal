<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminUserResource\Pages;
use App\Mail\NewAdminUserMail;
use App\Models\AdminUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminUserResource extends Resource
{
    protected static ?string $model = AdminUser::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Admin Users';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 10;

    // Only Admin role can access this section
    public static function canAccess(): bool
    {
        return Auth::guard('admin')->user()?->hasRole('admin') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Admin User Details')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(150),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(191),
                    Tables\Columns\TextColumn::make('role')
                        ->badge()
                        ->color(fn(string $state): string => match($state) {
                            'admin'   => 'danger',
                            'manager' => 'warning',
                            'editor'  => 'info',
                            default   => 'gray',
                        })
                        ->required()
                        ->helperText('Admin: full access. Manager: no withdrawals or user management. Editor: product curation only.'),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->helperText('Deactivating prevents this user from logging in.')
                        ->hiddenOn('create'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('role')
                    ->colors([
                        'danger'  => 'admin',
                        'warning' => 'manager',
                        'info'    => 'editor',
                    ]),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Never')
                    ->sortable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin'   => 'Admin',
                        'manager' => 'Manager',
                        'editor'  => 'Editor',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->visible(fn(AdminUser $record) =>
                        $record->is_active &&
                        $record->id !== Auth::guard('admin')->id()
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Deactivate Admin User')
                    ->modalDescription('This user will no longer be able to log in.')
                    ->action(function (AdminUser $record) {
                        $record->update(['is_active' => false]);
                        Notification::make()
                            ->title('User deactivated')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\Action::make('activate')
                    ->label('Activate')
                    ->icon('heroicon-o-lock-open')
                    ->color('success')
                    ->visible(fn(AdminUser $record) => ! $record->is_active)
                    ->requiresConfirmation()
                    ->action(function (AdminUser $record) {
                        $record->update(['is_active' => true]);
                        Notification::make()
                            ->title('User activated')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make()
                    ->visible(fn(AdminUser $record) =>
                        $record->id !== Auth::guard('admin')->id()
                    ),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAdminUsers::route('/'),
            'create' => Pages\CreateAdminUser::route('/create'),
            'edit'   => Pages\EditAdminUser::route('/{record}/edit'),
        ];
    }
}