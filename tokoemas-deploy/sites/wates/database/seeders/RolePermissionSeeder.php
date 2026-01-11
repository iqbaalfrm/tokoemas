<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles: super_admin, admin, kasir
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $kasirRole = Role::firstOrCreate([
            'name' => 'kasir',
            'guard_name' => 'web',
        ]);

        $this->command->info('Roles created: super_admin, admin, kasir');

        // Try to generate permissions using shield:generate with panel ID to avoid interactive prompt
        try {
            \Artisan::call('shield:generate', [
                '--all' => true,
                '--panel' => 'admin',
                '--minimal' => true,
            ]);
            $this->command->info('Permissions generated using Filament Shield.');
        } catch (\Exception $e) {
            $this->command->warn('Could not generate permissions automatically. Error: ' . $e->getMessage());
            $this->command->warn('Please run manually: php artisan shield:generate --all --panel=admin');
        }

        // If permissions exist, assign them all to super_admin
        $allPermissions = Permission::all();
        if ($allPermissions->count() > 0) {
            $superAdminRole->syncPermissions($allPermissions);
            $this->command->info("Assigned {$allPermissions->count()} permissions to super_admin role.");
        } else {
            $this->command->warn('No permissions found. Please run: php artisan shield:generate --all --panel=admin');
        }

        // Create users for each role
        $superAdminUser = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Owner Toko',
                'password' => bcrypt('admin123'),
            ]
        );
        $superAdminUser->assignRole('super_admin');
        $this->command->info('Super Admin user created/updated: admin@gmail.com');

        $adminUser = User::firstOrCreate(
            ['email' => 'admin2@gmail.com'],
            [
                'name' => 'Admin Toko',
                'password' => bcrypt('admin123'),
            ]
        );
        $adminUser->assignRole('admin');
        $this->command->info('Admin user created/updated: admin2@gmail.com');

        $kasirUser = User::firstOrCreate(
            ['email' => 'kasir@gmail.com'],
            [
                'name' => 'Kasir Toko',
                'password' => bcrypt('kasir123'),
            ]
        );
        $kasirUser->assignRole('kasir');
        $this->command->info('Kasir user created/updated: kasir@gmail.com');
    }
}

