<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserRole;
use App\Models\Permission;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Roles
        $adminRole = UserRole::create([
            'name' => 'Admin',
            'description' => 'Administrator with full access',
        ]);

        $salesRole = UserRole::create([
            'name' => 'Sales',
            'description' => 'Sales team member with access to customer and sales modules',
        ]);

        $supportRole = UserRole::create([
            'name' => 'Support',
            'description' => 'Support team member with access to customer and support related modules',
        ]);

        // Fetch all permissions
        $permissions = Permission::all();

        // Assign all permissions to Admin role
        if ($adminRole) {
            $adminRole->permissions()->attach($permissions->pluck('permission_id'));
        }

        // Assign specific permissions to Sales role
        if ($salesRole) {
            $salesPermissions = Permission::whereIn('name', [
                'view_customers', 'create_customers', 'edit_customers',
                'view_leads', 'create_leads', 'edit_leads',
            ])->pluck('permission_id');
            $salesRole->permissions()->attach($salesPermissions);
        }

        // Assign specific permissions to Support role
        if ($supportRole) {
            $supportPermissions = Permission::whereIn('name', [
                'view_customers', 'edit_customers', // Example: Support might view/edit but not create/delete
                'view_leads',
            ])->pluck('permission_id');
            $supportRole->permissions()->attach($supportPermissions);
        }
    }
}