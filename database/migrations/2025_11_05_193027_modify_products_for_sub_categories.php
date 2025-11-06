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
                // 1. Hapus foreign key 'category_id' yang lama (asumsi nama default)
                // Kita cek dulu apakah constraint-nya ada sebelum dihapus
                try {
                    // Coba hapus constraint dengan nama default
                    $table->dropForeign('products_category_id_foreign');
                } catch (\Exception $e) {
                    // Jika error (misal nama constraint beda atau tidak ada),
                    // Coba hapus pakai array (cara lain)
                    try {
                        $table->dropForeign(['category_id']);
                    } catch (\Exception $ex) {
                        // Jika masih error, abaikan (mungkin constraint tidak ada)
                        // \Log::warning('Could not drop foreign key for category_id: ' . $ex->getMessage());
                    }
                }
                
                // 2. Hapus kolom lama
                $table->dropColumn('category_id');
                
                // 3. Tambah kolom baru
                $table->foreignId('sub_category_id')
                      ->nullable() 
                      ->after('name') 
                      ->constrained('sub_categories') 
                      ->nullOnDelete(); 
            });
        }
    
        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::table('products', function (Blueprint $table) {
                // Balikin
                $table->dropForeign(['sub_category_id']);
                $table->dropColumn('sub_category_id');
                
                // Tambah lagi kolom category_id (sesuaikan dengan definisi awal)
                $table->foreignId('category_id')->nullable()->constrained('categories');
            });
        }
    };