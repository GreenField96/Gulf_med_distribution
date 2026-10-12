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
        // 1. Add price column to medications table
        Schema::table('medications', function (Blueprint $table) {
            if (! Schema::hasColumn('medications', 'price')) {
                $table->decimal('price', 10, 2)->default(0.00)->after('medical_name');
            }
        });

        // 2. Ensure medications_and_dosages has required columns (medication_id, member_id, amount)
        Schema::table('medications_and_dosages', function (Blueprint $table) {
            if (! Schema::hasColumn('medications_and_dosages', 'amount')) {
                $table->integer('amount')->default(1)->after('member_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medications', function (Blueprint $table) {
            if (Schema::hasColumn('medications', 'price')) {
                $table->dropColumn('price');
            }
        });

        Schema::table('medications_and_dosages', function (Blueprint $table) {
            if (Schema::hasColumn('medications_and_dosages', 'amount')) {
                $table->dropColumn('amount');
            }
        });
    }
};
