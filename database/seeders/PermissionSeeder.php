<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Customers
            ['name' => 'view-customers', 'description' => 'View customer records'],
            ['name' => 'create-customers', 'description' => 'Create new customer records'],
            ['name' => 'edit-customers', 'description' => 'Edit existing customer records'],
            ['name' => 'delete-customers', 'description' => 'Delete customer records'],
            // Leads
            ['name' => 'view-leads', 'description' => 'View leads'],
            ['name' => 'create-leads', 'description' => 'Create new leads'],
            ['name' => 'edit-leads', 'description' => 'Edit existing leads'],
            ['name' => 'delete-leads', 'description' => 'Delete leads'],
            // Roles
            ['name' => 'view-roles', 'description' => 'View user roles'],
            ['name' => 'create-roles', 'description' => 'Create new user roles'],
            ['name' => 'edit-roles', 'description' => 'Edit existing user roles'],
            ['name' => 'delete-roles', 'description' => 'Delete user roles'],
            // Permissions
            ['name' => 'view-permissions', 'description' => 'View permissions'],
            ['name' => 'create-permissions', 'description' => 'Create new permissions'],
            ['name' => 'edit-permissions', 'description' => 'Edit existing permissions'],
            ['name' => 'delete-permissions', 'description' => 'Delete permissions'],
            // Admin Section
            ['name' => 'view-admin-section', 'description' => 'Can view the Admin dropdown in navigation'],
            
            //Feedback Permissions
            ['name' => 'view-feedback', 'description' => 'View feedback'],
            ['name' => 'edit-feedback', 'description' => 'Edit existing feedback'],
            
            // Add more permissions as needed
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission['name']], $permission);
        }
    }
}