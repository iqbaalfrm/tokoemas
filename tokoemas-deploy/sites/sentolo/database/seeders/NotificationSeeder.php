<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Approval;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::role('super_admin')->first();
        $admin = User::role('admin')->first();
        $kasir = User::role('kasir')->first();

        if (!$superAdmin) {
            $this->command->warn('Super Admin user not found. Please run RolePermissionSeeder first.');
            return;
        }

        $now = Carbon::now();

        // Create sample approvals for notifications
        $approval1 = Approval::create([
            'user_id' => $admin ? $admin->id : $superAdmin->id,
            'approvable_type' => Transaction::class,
            'approvable_id' => null,
            'action_type' => 'create',
            'changes' => ['total' => 500000, 'name' => 'Pelanggan Test'],
            'status' => 'pending',
            'created_at' => $now->subDays(2),
            'updated_at' => $now->subDays(2),
        ]);

        $approval2 = Approval::create([
            'user_id' => $kasir ? $kasir->id : $superAdmin->id,
            'approvable_type' => 'App\\Models\\Product',
            'approvable_id' => 1,
            'action_type' => 'update',
            'changes' => ['stock' => 100, 'name' => 'Produk Test'],
            'status' => 'pending',
            'created_at' => $now->subDays(1),
            'updated_at' => $now->subDays(1),
        ]);

        $approval3 = Approval::create([
            'user_id' => $admin ? $admin->id : $superAdmin->id,
            'approvable_type' => 'App\\Models\\CashFlow',
            'approvable_id' => null,
            'action_type' => 'create',
            'changes' => ['amount' => 1000000, 'type' => 'income'],
            'status' => 'pending',
            'created_at' => $now->subHours(5),
            'updated_at' => $now->subHours(5),
        ]);

        // Create sample notifications
        $notifications = [
            // Notification 1: Approval request for Transaction (unread)
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\\Notifications\\ApprovalDiminta',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $superAdmin->id,
                'data' => json_encode([
                    'title' => 'TIKET BARU: Transaction',
                    'body' => ($admin ? $admin->name : 'Admin') . ' meminta persetujuan untuk create data.',
                    'message' => ($admin ? $admin->name : 'Admin') . ' meminta persetujuan untuk create data.',
                    'approval_id' => $approval1->id,
                    'url' => '/admin/daftar-approval',
                    'id' => \Illuminate\Support\Str::uuid()->toString(),
                    'duration' => 'persistent',
                    'format' => 'database',
                ]),
                'read_at' => null,
                'created_at' => $now->subDays(2),
                'updated_at' => $now->subDays(2),
            ],

            // Notification 2: Approval request for Product (unread)
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\\Notifications\\ApprovalDiminta',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $superAdmin->id,
                'data' => json_encode([
                    'title' => 'TIKET BARU: Product',
                    'body' => ($kasir ? $kasir->name : 'Kasir') . ' meminta persetujuan untuk update data.',
                    'message' => ($kasir ? $kasir->name : 'Kasir') . ' meminta persetujuan untuk update data.',
                    'approval_id' => $approval2->id,
                    'url' => '/admin/daftar-approval',
                    'id' => \Illuminate\Support\Str::uuid()->toString(),
                    'duration' => 'persistent',
                    'format' => 'database',
                ]),
                'read_at' => null,
                'created_at' => $now->subDays(1),
                'updated_at' => $now->subDays(1),
            ],

            // Notification 3: Approval request for CashFlow (unread - recent)
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\\Notifications\\ApprovalDiminta',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $superAdmin->id,
                'data' => json_encode([
                    'title' => 'TIKET BARU: CashFlow',
                    'body' => ($admin ? $admin->name : 'Admin') . ' meminta persetujuan untuk create data.',
                    'message' => ($admin ? $admin->name : 'Admin') . ' meminta persetujuan untuk create data.',
                    'approval_id' => $approval3->id,
                    'url' => '/admin/daftar-approval',
                    'id' => \Illuminate\Support\Str::uuid()->toString(),
                    'duration' => 'persistent',
                    'format' => 'database',
                ]),
                'read_at' => null,
                'created_at' => $now->subHours(5),
                'updated_at' => $now->subHours(5),
            ],

            // Notification 4: Read notification (old)
            [
                'id' => \Illuminate\Support\Str::uuid(),
                'type' => 'App\\Notifications\\ApprovalDiminta',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => $superAdmin->id,
                'data' => json_encode([
                    'title' => 'TIKET BARU: Transaction',
                    'body' => 'Transaksi lama yang sudah dibaca.',
                    'message' => 'Transaksi lama yang sudah dibaca.',
                    'approval_id' => null,
                    'url' => '/admin/daftar-approval',
                    'id' => \Illuminate\Support\Str::uuid()->toString(),
                    'duration' => 'persistent',
                    'format' => 'database',
                ]),
                'read_at' => $now->subDays(3),
                'created_at' => $now->subDays(3),
                'updated_at' => $now->subDays(3),
            ],
        ];

        DB::table('notifications')->insert($notifications);

        $this->command->info('Created ' . count($notifications) . ' sample notifications.');
        $this->command->info('3 unread notifications and 1 read notification created for Super Admin.');
    }
}

