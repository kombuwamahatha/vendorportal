<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorProduct extends Model
{
    protected $fillable = [
        'vendor_id', 'category_id', 'name', 'description',
        'price', 'stock_quantity', 'images', 'vendor_notes',
        'status', 'rejection_reason',
        'woo_product_id', 'woo_variation_id',
        'is_images_done', 'is_description_done',
        'is_approved', 'is_published',
        'approved_by', 'approved_at', 'published_at',
    ];

    protected $casts = [
        'images'              => 'array',
        'price'               => 'decimal:2',
        'is_images_done'      => 'boolean',
        'is_description_done' => 'boolean',
        'is_approved'         => 'boolean',
        'is_published'        => 'boolean',
        'approved_at'         => 'datetime',
        'published_at'        => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(AdminUser::class, 'approved_by');
    }

    // S3 path for vendor uploaded images
    public static function s3UploadPath(int $vendorId, int $productId, string $filename): string
    {
        return "vendor-uploads/{$vendorId}/products/{$productId}/{$filename}";
    }

    // S3 path for curated images
    public static function s3CuratedPath(int $wooProductId, string $filename): string
    {
        return "curated/products/{$wooProductId}/{$filename}";
    }
}