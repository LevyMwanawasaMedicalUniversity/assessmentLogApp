<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        Permission::create(['name' => 'Coordinator']);
        Permission::create(['name' => 'Administrator']);
        Permission::create(['name' => 'Dean']);
        Permission::create(['name' => 'Registrar']);

        // Create roles and assign permissions
        $coordinator = Role::create(['name' => 'Coordinator']);
        $administrator = Role::create(['name' => 'Administrator']);
        $dean = Role::create(['name' => 'Dean']);
        $registrar= Role::create(['name' => 'Registrar']);

        $coordinator->givePermissionTo('Coordinator');
        $administrator->givePermissionTo('Administrator');
        $administrator->givePermissionTo('Coordinator');
        $administrator->givePermissionTo('Dean');
        $administrator->givePermissionTo('Registrar');
        $dean->givePermissionTo('Dean');
        $registrar->givePermissionTo('Registrar');
    }
}
