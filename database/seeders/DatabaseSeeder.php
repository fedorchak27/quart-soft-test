<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SubscriptionSeeder::class,
        ]);
        // \App\Models\User::factory(10)->create();

         \App\Models\User::factory()->create([
             'name' => 'Admin',
             'email' => 'admin@admin.com',
             'password' => 'admin',
             'role_id' => Role::ADMIN,
         ]);
    }
}
