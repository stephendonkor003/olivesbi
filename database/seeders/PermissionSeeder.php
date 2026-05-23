<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'users.view'],
            ['name' => 'users.create'],
            ['name' => 'users.update'],

            ['name' => 'roles.view'],
            ['name' => 'roles.create'],
            ['name' => 'roles.update'],

            ['name' => 'permissions.view'],
            ['name' => 'permissions.create'],
            ['name' => 'permissions.update'],

            ['name' => 'dashboards.operational'],

            ['name' => 'customers.view'],
            ['name' => 'customers.create'],
            ['name' => 'customers.update'],

            ['name' => 'customer-types.view'],
            ['name' => 'customer-types.create'],
            ['name' => 'customer-types.update'],

            ['name' => 'property-types.view'],
            ['name' => 'property-types.create'],
            ['name' => 'property-types.update'],

            ['name' => 'properties.view'],
            ['name' => 'properties.create'],
            ['name' => 'properties.update'],

            ['name' => 'business-types.view'],
            ['name' => 'business-types.create'],
            ['name' => 'business-types.update'],

            ['name' => 'business-classes.view'],
            ['name' => 'business-classes.create'],
            ['name' => 'business-classes.update'],

            ['name' => 'businesses.view'],
            ['name' => 'businesses.create'],
            ['name' => 'businesses.update'],

            ['name' => 'bills.view'],
            ['name' => 'bills.create'],
            ['name' => 'bills.update'],

            ['name' => 'payments.view'],
            ['name' => 'payments.create'],
            ['name' => 'payments.update'],

            ['name' => 'assemblies.view'],
            ['name' => 'assemblies.create'],
            ['name' => 'assemblies.update'],

            ['name' => 'divisions.view'],
            ['name' => 'divisions.create'],
            ['name' => 'divisions.update'],

            ['name' => 'blocks.view'],
            ['name' => 'blocks.create'],
            ['name' => 'blocks.update'],

            ['name' => 'zones.view'],
            ['name' => 'zones.create'],
            ['name' => 'zones.update'],

            ['name' => 'property-uses.view'],
            ['name' => 'property-uses.create'],
            ['name' => 'property-uses.update'],

            ['name' => 'rates.view'],
            ['name' => 'rates.create'],
            ['name' => 'rates.update'],

            ['name' => 'reports.view'],
            ['name' => 'reports.create'],
            ['name' => 'reports.update'],

            ['name' => 'agent-assignments.view'],
            ['name' => 'agent-assignments.create'],
            ['name' => 'agent-assignments.update'],

            ['name' => 'task-assignments.view'],
            ['name' => 'task-assignments.create'],
            ['name' => 'task-assignments.update'],

            ['name' => 'analytics.view'],
            ['name' => 'nationwide.view'],
        ];

        foreach ($data as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'web'],
            );
        }
    }
}
