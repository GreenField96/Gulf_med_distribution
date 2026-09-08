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
            $table->string('phone_num')->nullable()->after('password');
            $table->enum('role_permission', ['admin_user', 'companies_members', 'medical_distro_operator_user'])
                  ->default('companies_members')
                  ->after('phone_num');
            $table->foreignId('company_id')
                  ->nullable()
                  ->after('role_permission')
                  ->constrained('companies')
                  ->nullOnDelete(); // null = all access across all companies
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn(['phone_num', 'role_permission', 'company_id']);
        });
    }
};
