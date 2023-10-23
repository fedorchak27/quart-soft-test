<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $item = [
            'id' => Role::ADMIN,
            'name' => 'admin',
        ];
        Role::updateOrCreate(['id' => $item['id']], $item);
        $item = [
            'id' => Role::AUTHOR,
            'name' => 'author',
        ];
        Role::updateOrCreate(['id' => $item['id']], $item);
    }
}
