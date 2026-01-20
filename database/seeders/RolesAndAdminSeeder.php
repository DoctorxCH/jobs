<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Platform roles
        $roles = [
            'platform.super_admin',
            'platform.admin',
            'platform.moderator',
            'platform.support',
            'platform.finance',
            'platform.viewer',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        // Ensure primary admin has super_admin (adjust email if needed)
        $admin = User::where('email', 'admin@kurka.ch')->first();

        if ($admin) {
            $admin->assignRole('platform.super_admin');
        }
    }
}
