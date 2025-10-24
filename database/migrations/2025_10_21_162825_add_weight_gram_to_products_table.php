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
        Schema::table('products', function (Blueprint $table) {
            // --- TAMBAHKAN BARIS INI ---
            $table->decimal('weight_gram', 8, 
2)->default(0.00)->after('gold_type'); 
            // --------------------------
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // --- TAMBAHKAN BARIS INI (Untuk bisa rollback) ---
            $table->dropColumn('weight_gram');
            // ------------------------------------------------
        });
    }
};
