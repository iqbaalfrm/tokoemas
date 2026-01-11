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

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kalau di-rollback, baru kita balikin kolomnya (buat jaga-jaga)
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('report_status', 50)->nullable()->default('published');
            $table->text('exclusion_reason')->nullable();
            $table->unsignedBigInteger('exclusion_requested_by')->nullable();
            $table->unsignedBigInteger('exclusion_approved_by')->nullable();
            $table->timestamp('exclusion_approved_at')->nullable();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->foreign('exclusion_requested_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('exclusion_approved_by')->references('id')->on('users')->nullOnDelete();
        });
    }
};