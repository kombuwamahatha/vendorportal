<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Vendor extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'brand_name', 'contact_person', 'email', 'password',
        'telephone', 'address_line1', 'address_line2', 'city',
        'district_id', 'province_id', 'business_reg_number',
        'product_categories', 'status', 'is_government_approved',
        'govt_approved_at', 'govt_approved_by', 'approved_at',
        'approved_by', 'rejection_reason', 'admin_notes',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password'             => 'hashed',
        'product_categories'   => 'array',
        'is_government_approved' => 'boolean',
        'govt_approved_at'     => 'datetime',
        'approved_at'          => 'datetime',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function bankDetail()
    {
        return $this->hasOne(VendorBankDetail::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(AdminUser::class, 'approved_by');
    }

    public function govtApprovedBy()
    {
        return $this->belongsTo(AdminUser::class, 'govt_approved_by');
    }
}