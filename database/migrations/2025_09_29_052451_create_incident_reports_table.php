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
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets');
            $table->foreignId('reported_by')->constrained('employees');
            $table->enum('incident_type', ['damage', 'lost', 'theft', 'malfunction']);
            $table->date('incident_date');
            $table->text('description');
            $table->text('resolution')->nullable();
            $table->enum('status', ['pending', 'investigating', 'resolved', 'closed'])->default('pending');
            $table->foreignId('resolved_by')->nullable()->constrained('employees');
            $table->date('resolved_date')->nullable();
            $table->timestamps();
            
            $table->index('incident_type');
            $table->index('status');
            $table->index('incident_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_reports');
    }
};
