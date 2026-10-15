<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles for different guards
        // Admin role (uses 'admin' guard)
        Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'admin'],
            ['name' => 'admin', 'guard_name' => 'admin']
        );

        // Vendor role (uses 'admin' guard - vendors use User model)
        Role::firstOrCreate(
            ['name' => 'vendor', 'guard_name' => 'admin'],
            ['name' => 'vendor', 'guard_name' => 'admin']
        );

        // Customer role (uses 'customer' guard)
        Role::firstOrCreate(
            ['name' => 'customer', 'guard_name' => 'customer'],
            ['name' => 'customer', 'guard_name' => 'customer']
        );

        // Reseller role (uses 'admin' guard - resellers use User model)
        Role::firstOrCreate(
            ['name' => 'reseller', 'guard_name' => 'admin'],
            ['name' => 'reseller', 'guard_name' => 'admin']
        );

        $this->command->info('Roles created successfully!');
        $this->command->info('- Admin role (admin guard)');
        $this->command->info('- Vendor role (admin guard)');
        $this->command->info('- Customer role (customer guard)');
        $this->command->info('- Reseller role (admin guard)');
    }
}
