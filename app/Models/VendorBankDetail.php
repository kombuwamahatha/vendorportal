<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorBankDetail extends Model
{
    protected $fillable = [
        'vendor_id',
        'bank_name',
        'bank_branch',
        'account_number',
        'account_holder_name',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}