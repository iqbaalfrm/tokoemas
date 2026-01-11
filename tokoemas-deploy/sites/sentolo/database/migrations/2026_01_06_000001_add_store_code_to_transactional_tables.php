<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migration ini menambahkan kolom untuk identifikasi store/tenant
     * pada tabel-tabel transaksional.
     */
    public function up(): void
    {
        // Tambah store_code ke transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('store_code', 20)->nullable()->after('id')->index();
        });

        // Tambah store_code ke buybacks
        Schema::table('buybacks', function (Blueprint $table) {
            $table->string('store_code', 20)->nullable()->after('id')->index();
        });

        // Tambah store_code ke inventories
        Schema::table('inventories', function (Blueprint $table) {
            $table->string('store_code', 20)->nullable()->after('id')->index();
        });

        // Tambah store_code ke cash_flows
        Schema::table('cash_flows', function (Blueprint $table) {
            $table->string('store_code', 20)->nullable()->after('id')->index();
        });

        // Tambah store_code ke products
        Schema::table('products', function (Blueprint $table) {
            $table->string('store_code', 20)->nullable()->after('id')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('store_code');
        });

        Schema::table('buybacks', function (Blueprint $table) {
            $table->dropColumn('store_code');
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn('store_code');
        });

        Schema::table('cash_flows', function (Blueprint $table) {
            $table->dropColumn('store_code');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('store_code');
        });
    }
};
