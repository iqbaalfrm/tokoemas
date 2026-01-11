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
            // Cek apakah kolom category_id ada, kalau ada hapus (tanpa foreign key)
            if (Schema::hasColumn('products', 'category_id')) {
                $table->dropColumn('category_id');
            }
            
            // Tambah kolom sub_category_id kalau belum ada
            if (!Schema::hasColumn('products', 'sub_category_id')) {
                $table->foreignId('sub_category_id')->nullable()->after('id')->constrained('sub_categories')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'sub_category_id')) {
                $table->dropForeign(['sub_category_id']);
                $table->dropColumn('sub_category_id');
            }
            
            if (!Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('id')->constrained('categories')->onDelete('cascade');
            }
        });
    }
};