<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['stock', 'is_active'], 'idx_products_stock_active');
            $table->index(['name', 'sku'], 'idx_products_name_sku');
            $table->index('barcode', 'idx_products_barcode');
            $table->index('sub_category_id', 'idx_products_subcategory_id');
        });

        Schema::table('transaction_items', function (Blueprint $table) {

            $table->index('transaction_id', 'idx_transaction_items_transaction_id'); 
        });

        Schema::table('members', function (Blueprint $table) {
            $table->index('no_hp', 'idx_members_no_hp');
        });

        Schema::table('cash_flows', function (Blueprint $table) {
            $table->index('type', 'idx_cash_flows_type');
            $table->index(['source', 'type'], 'idx_cash_flows_source_type');
            $table->index('created_at', 'idx_cash_flows_date');
        });

  
        DB::statement('CREATE INDEX idx_gold_price_type_date ON gold_prices(jenis_emas, tanggal DESC)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_stock_active');
            $table->dropIndex('idx_products_name_sku');
            $table->dropIndex('idx_products_barcode');
            $table->dropIndex('idx_products_subcategory_id');
        });

        Schema::table('transaction_items', function (Blueprint $table) {
            $table->dropIndex('idx_transaction_items_transaction_id');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex('idx_members_no_hp');
        });

        Schema::table('cash_flows', function (Blueprint $table) {
            $table->dropIndex('idx_cash_flows_type');
            $table->dropIndex('idx_cash_flows_source_type');
            $table->dropIndex('idx_cash_flows_date');
        });
        
        Schema::table('gold_prices', function (Blueprint $table) {
            $table->dropIndex('idx_gold_price_type_date');
        });
    }
};