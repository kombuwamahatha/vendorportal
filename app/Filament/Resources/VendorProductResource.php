<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorProductResource\Pages;
use App\Models\ProductCategory;
use App\Models\Vendor;
use App\Models\VendorProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VendorProductResource extends Resource
{
    protected static ?string $model = VendorProduct::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Vendor Products';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Product Information')
                ->schema([
                    Forms\Components\Select::make('vendor_id')
                        ->label('Vendor')
                        ->options(Vendor::where('status', 'approved')->pluck('brand_name', 'id'))
                        ->required()
                        ->searchable(),

                    Forms\Components\Select::make('category_id')
                        ->label('Category')
                        ->options(function () {
                            return ProductCategory::with('parent.parent')
                                ->orderBy('level')
                                ->orderBy('sort_order')
                                ->get()
                                ->mapWithKeys(fn($cat) => [$cat->id => $cat->full_path]);
                        })
                        ->required()
                        ->searchable(),

                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('description')
                        ->rows(4)
                        ->columnSpanFull(),

                    Forms\Components\TextInput::make('price')
                        ->numeric()
                        ->prefix('LKR')
                        ->required(),

                    Forms\Components\TextInput::make('stock_quantity')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    Forms\Components\Textarea::make('vendor_notes')
                        ->label('Vendor Notes')
                        ->rows(2)
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Product Images')
                ->schema([
                    Forms\Components\FileUpload::make('images')
                        ->label('Vendor Product Images')
                        ->multiple()
                        ->image()
                        ->disk('s3')
                        ->directory(fn(Forms\Get $get) =>
                            'vendor-uploads/' . $get('vendor_id') . '/products/temp'
                        )
                        ->visibility('public')
                        ->reorderable()
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('WooCommerce Mapping')
                ->description('Map this vendor product to a WooCommerce product after approval.')
                ->schema([
                    Forms\Components\TextInput::make('woo_product_id')
                        ->label('WooCommerce Product ID')
                        ->numeric()
                        ->helperText('The WooCommerce product ID this maps to'),
                    Forms\Components\TextInput::make('woo_variation_id')
                        ->label('WooCommerce Variation ID')
                        ->numeric()
                        ->helperText('Leave empty for simple products — fill for variable product variations'),
                ])->columns(2),

            Forms\Components\Section::make('Curation & Publishing')
                ->schema([
                    Forms\Components\Toggle::make('is_images_done')
                        ->label('Images Done')
                        ->helperText('Admin has curated and uploaded images to WooCommerce'),
                    Forms\Components\Toggle::make('is_description_done')
                        ->label('Description Done')
                        ->helperText('Product description is complete on WooCommerce'),
                    Forms\Components\Toggle::make('is_approved')
                        ->label('Approved')
                        ->helperText('Product is approved and ready to publish'),
                    Forms\Components\Toggle::make('is_published')
                        ->label('Published to WooCommerce')
                        ->helperText('Product is live on the WooCommerce store')
                        ->disabled(),
                ])->columns(2),

            Forms\Components\Section::make('Status')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            'pending'  => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ])->required(),
                    Forms\Components\Textarea::make('rejection_reason')
                        ->visible(fn(Get $get) => $get('status') === 'rejected')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vendor.brand_name')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price (LKR)')
                    ->money('LKR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match($state) {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_images_done')
                    ->label('Images')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_description_done')
                    ->label('Description')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Approved')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('vendor_id')
                    ->label('Vendor')
                    ->options(Vendor::where('status', 'approved')->pluck('brand_name', 'id'))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(ProductCategory::where('level', 1)->pluck('name', 'id')),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Published'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(VendorProduct $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (VendorProduct $record) {
                        $record->update([
                            'status'      => 'approved',
                            'approved_by' => Auth::guard('admin')->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()->title('Product approved')->success()->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(VendorProduct $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->required()
                            ->label('Reason for rejection'),
                    ])
                    ->action(function (VendorProduct $record, array $data) {
                        $record->update([
                            'status'           => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                        Notification::make()->title('Product rejected')->danger()->send();
                    }),

                Tables\Actions\Action::make('publish')
                    ->label('Publish to WooCommerce')
                    ->icon('heroicon-o-arrow-up-circle')
                    ->color('info')
                    ->visible(fn(VendorProduct $record) =>
                        $record->is_approved &&
                        $record->woo_product_id &&
                        ! $record->is_published
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Publish to WooCommerce')
                    ->modalDescription('This will push the product live to the WooCommerce store.')
                    ->action(function (VendorProduct $record) {
                        // WooCommerce sync will be wired here in Phase 3
                        $record->update([
                            'is_published' => true,
                            'published_at' => now(),
                        ]);
                        Notification::make()
                            ->title('Product published')
                            ->body('Product is now live on WooCommerce')
                            ->success()
                            ->send();
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVendorProducts::route('/'),
            'create' => Pages\CreateVendorProduct::route('/create'),
            'view'   => Pages\ViewVendorProduct::route('/{record}'),
            'edit'   => Pages\EditVendorProduct::route('/{record}/edit'),
        ];
    }
}