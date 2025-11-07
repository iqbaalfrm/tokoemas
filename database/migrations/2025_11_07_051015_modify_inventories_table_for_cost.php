<?php
    
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    
    return new class extends Migration
    {
        public function up(): void
        {
            Schema::table('inventories', function (Blueprint $table) {
                // Tambah kolom baru untuk total biaya modal
                $table->decimal('total_cost', 15, 2)->default(0)->after('source');
                
                // Perbaiki kolom 'total' yang error (beri default value 0)
                // Pastikan tipe data decimalnya sesuai dengan migrasi awal kamu
                $table->decimal('total', 15, 2)->default(0)->change(); 
            });
        }
    
        public function down(): void
        {
            Schema::table('inventories', function (Blueprint $table) {
                $table->dropColumn('total_cost');
                $table->decimal('total', 15, 2)->nullable(false)->change(); // Kembalikan seperti semula
            });
        }
    };
