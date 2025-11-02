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
            // Kita TAMBAHKAN kolom 'cost_price'
            // Pilih salah satu dari dua baris di bawah ini:

            // Pilihan 1 (Jika cost_price boleh kosong/NULL)
            $table->decimal('cost_price', 15, 2)->nullable()->after('selling_price');

            // Pilihan 2 (Jika cost_price default-nya 0)
            // $table->decimal('cost_price', 15, 2)->default(0)->after('selling_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Ini untuk membatalkan jika perlu
            $table->dropColumn('cost_price');
        });
    }
};