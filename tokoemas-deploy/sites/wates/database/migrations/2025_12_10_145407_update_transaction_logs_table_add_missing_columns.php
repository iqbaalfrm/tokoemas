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
        Schema::table('transaction_logs', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('transaction_logs', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('transaction_logs', 'action')) {
                $table->string('action');
            }

            if (!Schema::hasColumn('transaction_logs', 'description')) {
                $table->text('description')->nullable();
            }

            // Add morphs columns if they don't exist
            if (!Schema::hasColumn('transaction_logs', 'model_type')) {
                $table->string('model_type');
            }

            if (!Schema::hasColumn('transaction_logs', 'model_id')) {
                $table->unsignedBigInteger('model_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'action', 'description', 'model_type', 'model_id']);
        });
    }
};
