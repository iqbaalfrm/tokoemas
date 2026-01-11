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
        // Indexes for transaction table (commonly searched and filtered)
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['created_at'], 'idx_transactions_created_at');
            $table->index(['payment_method_id'], 'idx_transactions_payment_method_id');
            $table->index(['transaction_number'], 'idx_transactions_transaction_number');
            $table->index(['exclusion_approved_at'], 'idx_transactions_exclusion_approved_at');
        });

        // Indexes for buybacks table (for performance)
        Schema::table('buybacks', function (Blueprint $table) {
            $table->index(['tanggal'], 'idx_buybacks_tanggal');
            $table->index(['tipe'], 'idx_buybacks_tipe');
        });

        // Indexes for buyback_items table
        Schema::table('buyback_items', function (Blueprint $table) {
            $table->index(['buyback_id'], 'idx_buyback_items_buyback_id');
            $table->index(['item_total_price'], 'idx_buyback_items_item_total_price');
        });

        // Indexes for inventory table
        Schema::table('inventories', function (Blueprint $table) {
            $table->index(['type'], 'idx_inventories_type');
            $table->index(['created_at'], 'idx_inventories_created_at');
        });

        // Indexes for inventory_items table
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->index(['inventory_id'], 'idx_inventory_items_inventory_id');
            $table->index(['product_id'], 'idx_inventory_items_product_id');
        });

        // Indexes for reports table
        Schema::table('reports', function (Blueprint $table) {
            $table->index(['report_type'], 'idx_reports_report_type');
            $table->index(['start_date', 'end_date'], 'idx_reports_date_range');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_transactions_created_at');
            $table->dropIndex('idx_transactions_payment_method_id');
            $table->dropIndex('idx_transactions_transaction_number');
            $table->dropIndex('idx_transactions_exclusion_approved_at');
        });

        Schema::table('buybacks', function (Blueprint $table) {
            $table->dropIndex('idx_buybacks_tanggal');
            $table->dropIndex('idx_buybacks_tipe');
        });

        Schema::table('buyback_items', function (Blueprint $table) {
            $table->dropIndex('idx_buyback_items_buyback_id');
            $table->dropIndex('idx_buyback_items_item_total_price');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex('idx_inventories_type');
            $table->dropIndex('idx_inventories_created_at');
        });

        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropIndex('idx_inventory_items_inventory_id');
            $table->dropIndex('idx_inventory_items_product_id');
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex('idx_reports_report_type');
            $table->dropIndex('idx_reports_date_range');
        });
    }
};
