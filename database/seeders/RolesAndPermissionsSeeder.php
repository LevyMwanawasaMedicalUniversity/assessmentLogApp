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
        $permissions = [
            'Coordinator',
            'Administrator',
            'Dean',
            'Registrar',
            'ViewCoordinatorsCourses',
            'ViewTheContionousAssessment'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $roles = [
            'Coordinator' => [
                'Coordinator',
                'ViewTheContionousAssessment'
            ],
            'Administrator' => [
                'Administrator',
                'Coordinator',
                'Dean',
                'Registrar',
                'ViewCoordinatorsCourses',
                'ViewTheContionousAssessment'
            ],
            'Dean' => [
                'Dean',
                'ViewCoordinatorsCourses',
                'ViewTheContionousAssessment'
            ],
            'Registrar' => [
                'Registrar',
                'ViewCoordinatorsCourses',
                'ViewTheContionousAssessment'
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}
