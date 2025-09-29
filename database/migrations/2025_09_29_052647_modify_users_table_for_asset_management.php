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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->unique()->after('id');
            $table->foreignId('employee_id')->unique()->constrained('employees')->after('username');
            $table->enum('role', ['admin', 'hr', 'user'])->default('user')->after('employee_id');
            $table->timestamp('last_login')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('last_login');
            
            $table->dropColumn(['name', 'email', 'email_verified_at']);
            
            $table->index('username');
            $table->index('role');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'employee_id', 'role', 'last_login', 'is_active']);
            $table->string('name')->after('id');
            $table->string('email')->unique()->after('name');
            $table->timestamp('email_verified_at')->nullable()->after('email');
        });
    }
};
