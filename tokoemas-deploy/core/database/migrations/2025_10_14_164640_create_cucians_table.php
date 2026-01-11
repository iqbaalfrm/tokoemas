<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cucians', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('status')->default('Proses');
            $table->decimal('berat_total', 8, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cucians');
    }
};