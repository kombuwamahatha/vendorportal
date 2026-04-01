<?php

namespace App\Filament\Resources\VendorProductResource\Pages;

use App\Filament\Resources\VendorProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVendorProduct extends ViewRecord
{
    protected static string $resource = VendorProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}