<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class AdminUser extends Authenticatable implements FilamentUser
{
    use HasRoles, Notifiable;

    protected $table = 'admin_users';
    protected $guard_name = 'admin';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'created_by',
        'last_login_at',
    ];
    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password'      => 'hashed',
        'is_active'     => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active === true;
    }

    protected static function booted(): void
    {
        static::saved(function (AdminUser $adminUser) {
            if ($adminUser->wasChanged('role') || $adminUser->wasRecentlyCreated) {
                $adminUser->syncRoles([$adminUser->role]);
            }
        });
    }
}