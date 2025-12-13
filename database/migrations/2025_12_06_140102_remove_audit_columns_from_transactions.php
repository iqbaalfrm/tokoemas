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
            // Drop foreign key constraints first
            $table->dropForeign(['exclusion_requested_by']);
            $table->dropForeign(['exclusion_approved_by']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            // Then drop the columns
            $table->dropColumn(['exclusion_requested_by', 'exclusion_approved_by', 'exclusion_reason', 'exclusion_approved_at', 'report_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('report_status', 50)->nullable()->default('published');
            $table->text('exclusion_reason')->nullable();
            $table->unsignedBigInteger('exclusion_requested_by')->nullable();
            $table->unsignedBigInteger('exclusion_approved_by')->nullable();
            $table->timestamp('exclusion_approved_at')->nullable();
        });

        // Create foreign key constraints separately to avoid issues
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('exclusion_requested_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('exclusion_approved_by')->references('id')->on('users')->nullOnDelete();
        });
    }
};
