<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'parent_id', 'level',
        'sort_order', 'is_active', 'woo_category_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')->orderBy('sort_order');
    }

    public function vendorProducts()
    {
        return $this->hasMany(VendorProduct::class, 'category_id');
    }

    // Helper: get full path e.g. "Food & Gourmet > Tea Collection > Green Tea"
    public function getFullPathAttribute(): string
    {
        $parts = [$this->name];
        $parent = $this->parent;
        while ($parent) {
            array_unshift($parts, $parent->name);
            $parent = $parent->parent;
        }
        return implode(' > ', $parts);
    }
}