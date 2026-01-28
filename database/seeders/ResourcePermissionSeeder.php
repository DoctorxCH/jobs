<?php

namespace Database\Seeders;

use App\Models\ResourcePermission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class ResourcePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $resources = [
            'companies',
            'company_users',
            'platform_users',
            'jobs',
            'company_categories',
            'benefits',
            'skills',
            'countries',
            'regions',
            'cities',
            'education_levels',
            'education_fields',
            'sknice_positions',
        ];

        $roles = [
            'platform.super_admin',
            'platform.admin',
            'platform.moderator',
            'platform.finance',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        foreach ($resources as $resource) {
            ResourcePermission::updateOrCreate(
                ['resource' => $resource, 'role_name' => 'platform.super_admin'],
                ['can_view' => true, 'can_create' => true, 'can_edit' => true, 'can_delete' => true],
            );
        }

        foreach (['companies', 'company_users', 'platform_users'] as $resource) {
            ResourcePermission::updateOrCreate(
                ['resource' => $resource, 'role_name' => 'platform.admin'],
                ['can_view' => true, 'can_create' => true, 'can_edit' => true, 'can_delete' => true],
            );
        }

        ResourcePermission::updateOrCreate(
            ['resource' => 'jobs', 'role_name' => 'platform.moderator'],
            ['can_view' => true, 'can_create' => false, 'can_edit' => true, 'can_delete' => false],
        );

        ResourcePermission::updateOrCreate(
            ['resource' => 'companies', 'role_name' => 'platform.moderator'],
            ['can_view' => true, 'can_create' => false, 'can_edit' => false, 'can_delete' => false],
        );

        ResourcePermission::updateOrCreate(
            ['resource' => 'companies', 'role_name' => 'platform.finance'],
            ['can_view' => true, 'can_create' => false, 'can_edit' => false, 'can_delete' => false],
        );

        ResourcePermission::updateOrCreate(
            ['resource' => 'platform_users', 'role_name' => 'platform.finance'],
            ['can_view' => false, 'can_create' => false, 'can_edit' => false, 'can_delete' => false],
        );
    }
}
