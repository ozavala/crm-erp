<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions
        $permissions = [
            'view_customers',
            'create_customers',
            'edit_customers',
            'delete_customers',
            'view_leads',
            'create_leads',
            'edit_leads',
            'delete_leads',
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
        ];

        // Loop through each permission and create it
        foreach ($permissions as $permission) {
            \App\Models\Permission::create(['name' => $permission]);
        }
    }
}
