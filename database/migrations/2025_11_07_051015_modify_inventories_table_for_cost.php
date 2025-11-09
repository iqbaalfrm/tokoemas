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
        if (!Schema::hasColumn('inventories', 'total_cost')) {
            Schema::table('inventories', function (Blueprint $table) {
                $table->decimal('total_cost', 15, 2)->notNull()->default('0')->after('source');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropColumn('total_cost');
            $table->decimal('total', 15, 2)->nullable(false)->change();
        });
    }
};