<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
           // Check if an admin user with id 1 exists
           $adminUser = User::where('id', 1)->first();

           if (!$adminUser) {
               // If no admin user with id 1 exists, truncate the table
               User::truncate();

               // Add a new admin user
              User::insert([
                   'id' => 1,
                   'first_name' => 'Admin',
                   'last_name' => 'User',
                   'email' => 'admin.mastercatalog@yopmail.com',
                   'phone' => 9087654321,
                   'role_id' => 1,
                   'password' => Hash::make('password'),
                   'status' => 'active',
                   'email_verified_at' => now(),
                   'created_at' => now(),
                   'updated_at' => now(),
               ]);

               $this->command->info('Admin user created successfully.');
           } else {
               $this->command->info('Admin user already exists.');
           }
    }
}
