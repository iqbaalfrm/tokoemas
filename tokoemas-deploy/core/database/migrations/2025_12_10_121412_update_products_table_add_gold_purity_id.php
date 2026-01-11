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
        Schema::table('products', function (Blueprint $table) {
            // Only drop the kadar column if it exists
            if (Schema::hasColumn('products', 'kadar')) {
                $table->dropColumn('kadar');
            }
            $table->foreignId('gold_purity_id')->nullable()->constrained('gold_purities')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['gold_purity_id']);
            $table->dropColumn('gold_purity_id');

            // Add back the old kadar column if it didn't exist before
            if (!Schema::hasColumn('products', 'kadar')) {
                $table->string('kadar')->nullable();
            }
        });
    }
};
