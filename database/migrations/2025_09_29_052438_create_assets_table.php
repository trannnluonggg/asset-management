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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code', 20)->unique();
            $table->string('qr_code', 50)->unique();
            $table->string('asset_name', 100);
            $table->foreignId('category_id')->constrained('asset_categories');
            $table->string('brand', 50)->nullable();
            $table->string('model', 50)->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->enum('condition_status', ['new', 'good', 'fair', 'poor', 'broken'])->default('new');
            $table->string('location', 100)->nullable();
            $table->enum('status', ['available', 'assigned', 'maintenance', 'retired'])->default('available');
            $table->timestamps();
            
            $table->index('asset_code');
            $table->index('qr_code');
            $table->index('status');
            $table->index('condition_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
