<?php

namespace App\Filament\Resources\AdminUserResource\Pages;

use App\Filament\Resources\AdminUserResource;
use App\Mail\NewAdminUserMail;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CreateAdminUser extends CreateRecord
{
    protected static string $resource = AdminUserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->plainPassword = Str::random(12);

        $data['password']   = $this->plainPassword;
        $data['created_by'] = Auth::guard('admin')->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        Mail::to($this->record->email)->send(
            new NewAdminUserMail($this->record, $this->plainPassword, $this->record->role)
        );
    }

    private string $plainPassword;
}