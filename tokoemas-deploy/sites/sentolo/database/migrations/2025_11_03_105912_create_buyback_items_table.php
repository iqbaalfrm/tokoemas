<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buyback_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyback_id')->constrained('buybacks')->cascadeOnDelete();
            $table->string('nama_produk');
            $table->decimal('berat', 10, 3)->default(0);
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buyback_items');
    }
};