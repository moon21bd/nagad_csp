<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Seed permissions
        $this->call(CreatePermissionsSeeder::class);

        // Create Roles and Seed a super admin user
        $this->call(SuperAdminUserSeeder::class);
    }
}

class CreatePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'permission-list',
            'permission-create',
            'permission-edit',
            'permission-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'api']);
        }
    }
}

class SuperAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create a super admin user
        $user = User::create([
            'name' => 'Super Admin',
            'first_name' => 'Raqibul',
            'last_name' => 'Hasan',
            'email' => 'rhmoon21@gmail.com',
            'password' => Hash::make('12345678'),
        ]);

        // Create a super admin role and assign all permissions
        $superAdminRole = Role::create(['name' => 'super-admin', 'guard_name' => 'api']);

        // Assign the super admin role to the user
        $user->assignRole($superAdminRole);

        // Assign all permissions to the super admin role
        $permissions = Permission::all();
        $superAdminRole->syncPermissions($permissions);
    }
}
