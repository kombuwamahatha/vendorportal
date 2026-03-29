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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('brand_name', 200);
            $table->string('contact_person', 150);
            $table->string('email', 191)->unique();
            $table->string('password', 255);
            $table->string('telephone', 20);
            $table->string('address_line1', 255);
            $table->string('address_line2', 255)->nullable();
            $table->string('city', 100);
            $table->unsignedTinyInteger('district_id');
            $table->unsignedTinyInteger('province_id');
            $table->string('business_reg_number', 100)->nullable();
            $table->json('product_categories')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->boolean('is_government_approved')->default(false);
            $table->timestamp('govt_approved_at')->nullable();
            $table->foreignId('govt_approved_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('district_id')->references('id')->on('districts');
            $table->foreign('province_id')->references('id')->on('provinces');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
