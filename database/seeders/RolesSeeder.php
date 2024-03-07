<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::truncate();
        
               Role::insert([
                'name' => 'SUPER_ADMIN',
                'description' => 'Role For Project Admin.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info('Roles created successfully.');
    }
}
