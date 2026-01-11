<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Ubah kolom cost_price agar boleh null (nullable)
            // Sesuaikan angka 15, 2 jika di migrasi lama Anda beda
            $table->decimal('cost_price', 15, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Ini untuk membatalkan (jika perlu)
            $table->decimal('cost_price', 15, 2)->nullable(false)->change();
        });
    }
};
