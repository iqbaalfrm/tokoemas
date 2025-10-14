<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cucian_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cucian_id')->constrained()->cascadeOnDelete();
            $table->string('nama_produk');
            $table->decimal('berat', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cucian_items');
    }
};