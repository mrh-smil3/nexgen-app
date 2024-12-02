<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use App\Models\User;
use App\Models\Package;

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

        // Create permissions
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'delete users']);
        Permission::create(['name' => 'create users']);

        // Assign Permissions to Roles
        $adminRole->givePermissionTo([
            'view users',
            'create users',
            'edit users',
            'delete users'
        ]);

        // $managerRole->givePermissionTo([
        //     'view packages',
        //     'edit packages'
        // ]);

        // Create a super-admin user and assign role
        $superAdmin = User::create([
            'name' => 'Super Admin', // Ganti dengan nama pengguna
            'email' => 'admin@filamentphp.com', // Ganti dengan email pengguna
            'password' => bcrypt('admin'), // Ganti dengan password yang aman
        ]);

        $superAdmin->assignRole($adminRole);

        // Create Packages
        $superAdmin = Package::create([
            'name' => 'Basic',
            'description' => 'Basic Paket',
            'price' => '1000000',
            'duration' => '12 Bulan',
        ]);
    }
}
