<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update the admin user
        $admin = User::updateOrCreate(
            ['email' => 'dev@lmmu.com'],
            [
                'name' => 'Dev User',
                'password' => bcrypt('Lmmu@D3v!'),
            ]
        );

        // Create the admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);

        // Assign the admin role to the admin user if not already assigned
        if (!$admin->hasRole('Administrator')) {
            $admin->assignRole($adminRole);
        }

        // Assign all permissions to the admin role (optional)
        $adminRole->syncPermissions(Permission::all());
    }
}
