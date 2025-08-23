<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'id' => 1,
            'name' => 'admin',
            'description' => 'システム管理者',
        ]);

        Role::create([
            'id' => 2,
            'name' => 'manager',
            'description' => 'ねこカフェ店長',
        ]);

        Role::create([
            'id' => 3,
            'name' => 'staff',
            'description' => 'ねこカフェ店員',
        ]);
    }
}
