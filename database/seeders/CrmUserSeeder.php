<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CrmUser; // Adjust the namespace according to your application structure
use App\Models\UserRole;

class CrmUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // You can seed the crm_users table with some initial data here.
        // For example, you might want to create a few users with different roles.

        $adminUser = CrmUser::create([
            'username' => 'Admin User',
            'full_name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'), // Use a secure password in production
        ]);

        $salesUser = CrmUser::create([
            'username' => 'Sales User',
            'full_name' => 'Sales User',
            'email' => 'sales@example.com',
            'password' => bcrypt('password'),
        ]);

        $supportUser = CrmUser::create([
            'username' => 'Support User',
            'full_name' => 'Support User',
            'email' => 'support@example.com',
            'password' => bcrypt('password'),
        ]);

        $marketingUser = CrmUser::create([
            'username' => 'Marketing User',
            'full_name' => 'Marketing User',
            'email' => 'marketing@example.com',
            'password' => bcrypt('password'),
        ]);

        // Assign Roles
        $adminRole = UserRole::where('name', 'Admin')->first();
        $salesRole = UserRole::where('name', 'Sales')->first();
        $supportRole = UserRole::where('name', 'Support')->first();

        if ($adminUser && $adminRole) {
            $adminUser->roles()->attach($adminRole->role_id);
        }
        if ($salesUser && $salesRole) {
            $salesUser->roles()->attach($salesRole->role_id);
        }
        if ($supportUser && $supportRole) {
            $supportUser->roles()->attach($supportRole->role_id);
        }
        // Marketing user might have sales role or a dedicated marketing role if you create one
        if ($marketingUser && $salesRole) {
            $marketingUser->roles()->attach($salesRole->role_id);
        }
    }
}
