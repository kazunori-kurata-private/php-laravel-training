<?php

namespace Database\Seeders;

use App\Enums\RoleType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // パスワード: admin123
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'image' => 'users/admin.png',
            'introduction' => 'システム管理者です。',
            'role_id' => RoleType::ADMIN, // admin
        ]);

        User::create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => Hash::make('manager123'),
            'image' => 'users/manager.png',
            'introduction' => 'ねこカフェ店長です。',
            'role_id' => RoleType::MANAGER, // manager
        ]);

        User::create([
            'name' => 'Staff User',
            'email' => 'user@example.com',
            'password' => Hash::make('user123'),
            'image' => 'users/User.png',
            'introduction' => 'ねこカフェ店員です。',
            'role_id' => RoleType::STAFF, // staff
        ]);
    }
}
