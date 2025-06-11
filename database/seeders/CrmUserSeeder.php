<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CrmUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // You can seed the crm_users table with some initial data here.
        // For example, you might want to create a few users with different roles.

        \App\Models\CrmUser::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'), // Use a secure password in production
            'role' => 'admin', // Assuming you have a role field
       
        ]);

        \App\Models\CrmUser::create([
            'username' => 'Sales User',
            'full_name' => 'Sales User',
            'email' => 'sales@example.com',
            'password' => bcrypt('password'),
           
        ]);
    }
}


