<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $adminRole = Role::create(['name' => 'super-admin']);
        $managerRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'customer']);

        // Create Permissions
        Permission::create(['name' => 'view packages']);
        Permission::create(['name' => 'create packages']);
        Permission::create(['name' => 'edit packages']);
        Permission::create(['name' => 'delete packages']);

        // Assign Permissions to Roles
        $adminRole->givePermissionTo([
            'view packages', 
            'create packages', 
            'edit packages', 
            'delete packages'
        ]);

        $managerRole->givePermissionTo([
            'view packages', 
            'edit packages'
        ]);
    }
}
