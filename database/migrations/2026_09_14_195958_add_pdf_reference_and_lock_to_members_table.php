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
        Schema::table('members', function (Blueprint $table) {
            // $table->string('reference_to_pdf')->nullable()->default(null)->after('national_ID');
            $table->boolean('is_locked')->default(false)->after('reference_to_pdf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['reference_to_pdf', 'is_locked']);
        });
    }
};
