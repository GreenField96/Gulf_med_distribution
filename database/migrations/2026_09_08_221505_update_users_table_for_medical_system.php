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
            if (! Schema::hasColumn('users', 'phone_num')) {
                $table->string('phone_num')->nullable()->after('password');
            }

            if (! Schema::hasColumn('users', 'company_id')) {
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('companies_members');
            }
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
