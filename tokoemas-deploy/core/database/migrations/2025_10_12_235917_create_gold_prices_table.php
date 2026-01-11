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
    Schema::create('gold_prices', function (Blueprint $table) {
        $table->id();
        $table->string('jenis_emas'); // Emas Tua, Emas Muda
        $table->decimal('harga_per_gram', 15, 2);
        $table->date('tanggal');
        $table->timestamps();

        // Mencegah ada data duplikat untuk jenis emas dan tanggal yang sama
        $table->unique(['jenis_emas', 'tanggal']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gold_prices');
    }
};
