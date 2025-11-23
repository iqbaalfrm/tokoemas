<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Create sub categories for each category
        // Using category ID 1 = Makanan Ringan
        DB::table('sub_categories')->insert([
            [
                'id' => 1,
                'category_id' => 1,
                'name' => 'Makanan Ringan',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);

        // Using category ID 2 = Minuman
        DB::table('sub_categories')->insert([
            [
                'id' => 2,
                'category_id' => 2,
                'name' => 'Minuman',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);

        // Using category ID 3 = Alat Tulis Kantor (ATK)
        DB::table('sub_categories')->insert([
            [
                'id' => 3,
                'category_id' => 3,
                'name' => 'Alat Tulis Kantor (ATK)',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);

        // Using category ID 4 = Produk Kebersihan
        DB::table('sub_categories')->insert([
            [
                'id' => 4,
                'category_id' => 4,
                'name' => 'Produk Kebersihan',
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}

