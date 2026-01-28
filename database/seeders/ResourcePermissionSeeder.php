<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResourcePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $resources = [
            // Core/Admin
            'users',
            'companies',
            'company-categories',
            'company-users',
            'company-invitations',
            'resource-permissions',

            // Jobs
            'jobs',
            'benefits',
            'skills',
            'job-languages',
            'job-skills',
            'countries',
            'regions',
            'cities',
            'education-levels',
            'education-fields',
            'sknice-positions',

            // Billing
            'billing/products',
            'billing/product-prices',
            'billing/coupons',
            'billing/tax-rates',
            'billing/settings',
            'billing/orders',
            'billing/invoices',
            'billing/payments',
            'billing/entitlements',
            'billing/credit-ledgers',
            'billing/credit-reservations',
        ];

        foreach ($resources as $resource) {
            DB::table('resource_permissions')->updateOrInsert(
                [
                    'resource' => $resource,
                    'role_name' => 'platform.super_admin',
                ],
                [
                    'can_view' => 1,
                    'can_create' => 1,
                    'can_edit' => 1,
                    'can_delete' => 1,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
