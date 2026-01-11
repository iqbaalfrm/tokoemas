<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = base_path('dummy_data.sql');
        
        if (file_exists($path)) {
            $sql = file_get_contents($path);
            \Illuminate\Support\Facades\DB::unprepared($sql);
            $this->command->info('Dummy data inserted successfully from dummy_data.sql');
        } else {
            $this->command->error('File dummy_data.sql not found!');
        }
    }
}
