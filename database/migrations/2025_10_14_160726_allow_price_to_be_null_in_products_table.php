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
        // Pilih salah satu dari dua opsi di bawah ini

        // OPSI A: Izinkan kolom 'price' untuk kosong (NULL)
        $table->decimal('price', 15, 2)->nullable()->change();

        // OPSI B: Beri nilai default 0 jika tidak diisi
        // $table->decimal('price', 15, 2)->default(0)->change();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
