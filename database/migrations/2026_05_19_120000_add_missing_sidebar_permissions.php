<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    private const PERMISSIONS = [
        'dashboard-view',
        'employee-list',
        'attendance-list',
        'leave-list',
        'salary-list',
        'bonus-list',
        'salary-payment-list',
        'newsletter-list',
        'cache-clear',
        'error-log-view',
        'delivery-boy-list',
        'delivery-withdrawal-list',
        'delivery-location-list',
    ];

    public function up(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $name) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'admin'],
                ['guard_name' => 'admin']
            );
        }

        // বিদ্যমান Admin রোলে নতুন পারমিশন যোগ (স্টাফ রোল ম্যানুয়ালি সেট করবে)
        foreach (['admin', 'Admin'] as $roleName) {
            $role = Role::where('guard_name', 'admin')->where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo(self::PERMISSIONS);
            }
        }
    }

    public function down(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::where('guard_name', 'admin')
            ->whereIn('name', self::PERMISSIONS)
            ->delete();
    }
};
