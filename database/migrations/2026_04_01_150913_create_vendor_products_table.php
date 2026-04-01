<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vendor_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('product_categories');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->json('images')->nullable(); // S3 paths array
            $table->text('vendor_notes')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();

            // WooCommerce mapping
            $table->unsignedBigInteger('woo_product_id')->nullable();
            $table->unsignedBigInteger('woo_variation_id')->nullable();

            // Curation flags
            $table->boolean('is_images_done')->default(false);
            $table->boolean('is_description_done')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_published')->default(false);

            // Tracking
            $table->foreignId('approved_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('published_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_products');
    }
};
