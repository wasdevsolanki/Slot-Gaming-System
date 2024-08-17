<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserTable extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => hash::make('superadmin@gmail.com'),
            'gender' => 'male',
            'profile' => 'super_admin.png',
            'role' => SUPERADMIN,
            'phone' => '03033633206',
            'status' => 1,
        ]);
    }
}
