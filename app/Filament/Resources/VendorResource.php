<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorResource\Pages;
use App\Filament\Resources\VendorResource\RelationManagers;
use App\Models\Vendor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('brand_name')
                    ->required()
                    ->maxLength(200),
                Forms\Components\TextInput::make('contact_person')
                    ->required()
                    ->maxLength(150),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('telephone')
                    ->tel()
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('address_line1')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address_line2')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('city')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('district_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('province_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('business_reg_number')
                    ->maxLength(100)
                    ->default(null),
                Forms\Components\Textarea::make('product_categories')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Toggle::make('is_government_approved')
                    ->required(),
                Forms\Components\DateTimePicker::make('govt_approved_at'),
                Forms\Components\TextInput::make('govt_approved_by')
                    ->numeric()
                    ->default(null),
                Forms\Components\DateTimePicker::make('approved_at'),
                Forms\Components\TextInput::make('approved_by')
                    ->numeric()
                    ->default(null),
                Forms\Components\Textarea::make('rejection_reason')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('admin_notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('brand_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact_person')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telephone')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address_line1')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address_line2')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('district_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('province_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('business_reg_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\IconColumn::make('is_government_approved')
                    ->boolean(),
                Tables\Columns\TextColumn::make('govt_approved_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('govt_approved_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'edit' => Pages\EditVendor::route('/{record}/edit'),
        ];
    }
}
