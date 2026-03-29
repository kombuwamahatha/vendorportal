<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    public $timestamps = false;
    protected $fillable = ['province_id', 'name', 'slug'];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }
}