<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role', 50);
            $table->string('permission', 100);
            $table->boolean('allowed')->default(false);
            $table->timestamps();
            $table->unique(['role', 'permission']);
        });

        $defaults = [
            'dashboard' => ['admin', 'manager'],
            'reports' => ['admin', 'manager'],
            'products.view' => ['admin', 'manager', 'warehouse_staff'],
            'products.create_delete' => ['admin'],
            'products.edit' => ['admin', 'manager'],
            'inventory.edit' => ['admin', 'warehouse_staff'],
            'inventory.logs' => ['admin', 'manager', 'warehouse_staff'],
            'orders.manage' => ['admin', 'manager', 'customer_service'],
            'refunds.manage' => ['admin', 'manager'],
            'chat' => ['admin', 'customer_service'],
            'members.manage' => ['admin', 'manager'],
            'marketing.manage' => ['admin', 'manager'],
            'staff.manage' => ['admin'],
        ];
        $roles = ['admin', 'manager', 'warehouse_staff', 'customer_service'];
        $now = now();
        $rows = [];

        foreach ($defaults as $permission => $allowedRoles) {
            foreach ($roles as $role) {
                $rows[] = [
                    'role' => $role,
                    'permission' => $permission,
                    'allowed' => in_array($role, $allowedRoles, true),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('role_permissions')->insert($rows);
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
