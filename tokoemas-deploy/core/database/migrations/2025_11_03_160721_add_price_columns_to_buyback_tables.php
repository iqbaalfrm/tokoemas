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
            Schema::table('buybacks', function (Blueprint $table) {
                // Ganti 'member_id' menjadi 'tipe'
                $table->decimal('total_amount_paid', 15, 2)->default(0)->after('tipe');
            });
    
            Schema::table('buyback_items', function (Blueprint $table) {
                $table->decimal('item_total_price', 15, 2)->default(0)->after('berat');
            });
        }
    
        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('buybacks', function (Blueprint $table) {
                $table->dropColumn('total_amount_paid');
            });
    
            Schema::table('buyback_items', function (Blueprint $table) {
                $table->dropColumn('item_total_price');
            });
        }
    };
    
