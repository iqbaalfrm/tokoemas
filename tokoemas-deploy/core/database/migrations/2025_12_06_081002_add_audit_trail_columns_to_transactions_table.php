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
        Schema::table('transactions', function (Blueprint $table) {
            $table->text('exclusion_reason')->nullable();
            $table->unsignedBigInteger('exclusion_requested_by')->nullable();
            $table->unsignedBigInteger('exclusion_approved_by')->nullable();
            $table->timestamp('exclusion_approved_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'exclusion_reason',
                'exclusion_requested_by',
                'exclusion_approved_by',
                'exclusion_approved_at'
            ]);
        });
    }
};
